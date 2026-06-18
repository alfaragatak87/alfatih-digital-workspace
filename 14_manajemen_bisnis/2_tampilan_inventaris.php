<?php
if (!defined('SITE_URL') || empty($_SESSION['uid'])) exit;

$owner = $_SESSION['username'];

// Proses aksi form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_inventaris'])) {
    $nama_barang = $mysqli->real_escape_string($_POST['nama_barang']);
    $jumlah_stok = (int)$_POST['jumlah_stok'];
    $tipe_pergerakan = $mysqli->real_escape_string($_POST['tipe_pergerakan']);
    $keterangan = $mysqli->real_escape_string($_POST['keterangan']);
    
    if (!empty($nama_barang) && $jumlah_stok > 0) {
        $mysqli->query("INSERT INTO inventaris (owner_username, nama_barang, jumlah_stok, tipe_pergerakan, keterangan) VALUES ('$owner', '$nama_barang', $jumlah_stok, '$tipe_pergerakan', '$keterangan')");
        header("Location: index.php?page=inventaris");
        exit;
    }
}

// Ambil data (Grouping by nama barang to show current stock)
$res = $mysqli->query("SELECT nama_barang, SUM(CASE WHEN tipe_pergerakan IN ('Masuk', 'Stok Awal') THEN jumlah_stok ELSE -jumlah_stok END) as stok_saat_ini FROM inventaris WHERE owner_username='$owner' GROUP BY nama_barang");
$stok_barang = [];
if ($res) {
    while($r = $res->fetch_assoc()) $stok_barang[] = $r;
}

// Riwayat Pergerakan
$resHistory = $mysqli->query("SELECT * FROM inventaris WHERE owner_username='$owner' ORDER BY created_at DESC LIMIT 20");
$riwayat = [];
if ($resHistory) {
    while($r = $resHistory->fetch_assoc()) $riwayat[] = $r;
}
?>

<div style="padding: 24px; max-width: 1000px; margin: 0 auto; animation: fadeUp 0.5s ease;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px;">
        <div>
            <h1 style="font-family: var(--f-display); margin: 0 0 8px 0; font-size: 2.5rem;"><i class="fa-solid fa-boxes-stacked" style="color:var(--blue);"></i> Inventaris Barang</h1>
            <p style="color: var(--text-muted); margin: 0;">Pantau stok barang masuk dan keluar.</p>
        </div>
    </div>

    <!-- Layout 2 Kolom -->
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 24px;">
        
        <!-- Kolom Kiri: Form & Daftar Stok -->
        <div>
            <!-- Form Tambah -->
            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm);">
                <h3 style="margin: 0 0 16px 0; font-size: 1.1rem;"><i class="fa-solid fa-plus-circle"></i> Catat Pergerakan Barang</h3>
                <form method="POST" style="display: flex; flex-direction: column; gap: 12px;">
                    <input type="text" name="nama_barang" required placeholder="Nama Barang" style="padding: 12px; border-radius: 8px; background: var(--surface); color: var(--text-main); border: 1px solid var(--border);">
                    <div style="display: flex; gap: 12px;">
                        <input type="number" name="jumlah_stok" required min="1" placeholder="Jumlah" style="width:50%; padding: 12px; border-radius: 8px; background: var(--surface); color: var(--text-main); border: 1px solid var(--border);">
                        <select name="tipe_pergerakan" required style="width:50%; padding: 12px; border-radius: 8px; background: var(--surface); color: var(--text-main); border: 1px solid var(--border);">
                            <option value="Stok Awal">Stok Awal</option>
                            <option value="Masuk">Masuk</option>
                            <option value="Keluar">Keluar</option>
                        </select>
                    </div>
                    <input type="text" name="keterangan" placeholder="Keterangan (Opsional)" style="padding: 12px; border-radius: 8px; background: var(--surface); color: var(--text-main); border: 1px solid var(--border);">
                    <button type="submit" name="tambah_inventaris" style="background: var(--blue); border: none; padding: 12px; border-radius: 8px; color: #fff; font-weight: bold; cursor: pointer; margin-top: 8px;">Simpan Data</button>
                </form>
            </div>

            <!-- Daftar Stok Saat Ini -->
            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 16px; padding: 24px;">
                <h3 style="margin: 0 0 16px 0; font-size: 1.1rem;"><i class="fa-solid fa-box-open"></i> Sisa Stok Barang</h3>
                <?php if(empty($stok_barang)): ?>
                    <p style="color:var(--text-muted); font-size:0.9rem;">Belum ada data barang.</p>
                <?php else: ?>
                    <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:12px;">
                        <?php foreach($stok_barang as $s): ?>
                        <li style="display:flex; justify-content:space-between; padding-bottom:8px; border-bottom:1px solid rgba(255,255,255,0.05);">
                            <span style="font-weight:500;"><?= htmlspecialchars($s['nama_barang']) ?></span>
                            <span style="background:rgba(255,255,255,0.1); padding:2px 8px; border-radius:12px; font-size:0.85rem; font-family:monospace;"><?= $s['stok_saat_ini'] ?> unit</span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Kolom Kanan: Riwayat -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 16px; overflow: hidden;">
            <h3 style="margin: 0; padding: 24px; font-size: 1.1rem; border-bottom: 1px solid rgba(255,255,255,0.05);"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Pergerakan (20 Terakhir)</h3>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--border);">
                        <th style="padding: 16px; color: var(--text-muted); font-size: 0.85rem;">Tanggal</th>
                        <th style="padding: 16px; color: var(--text-muted); font-size: 0.85rem;">Barang</th>
                        <th style="padding: 16px; color: var(--text-muted); font-size: 0.85rem;">Tipe</th>
                        <th style="padding: 16px; color: var(--text-muted); font-size: 0.85rem;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($riwayat)): ?>
                    <tr><td colspan="4" style="text-align:center; padding: 32px; color: var(--text-muted);">Belum ada riwayat pergerakan barang.</td></tr>
                    <?php else: ?>
                        <?php foreach($riwayat as $r): 
                            $c = '';
                            if($r['tipe_pergerakan']=='Masuk') $c = 'color:var(--success);';
                            elseif($r['tipe_pergerakan']=='Keluar') $c = 'color:var(--danger);';
                            else $c = 'color:var(--text-muted);';
                        ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                            <td style="padding: 16px; color: var(--text-muted); font-size: 0.85rem;"><?= date('d M Y H:i', strtotime($r['created_at'])) ?></td>
                            <td style="padding: 16px; font-weight: 500;"><?= htmlspecialchars($r['nama_barang']) ?></td>
                            <td style="padding: 16px; font-size: 0.85rem; <?= $c ?>">
                                <?= $r['tipe_pergerakan'] ?>
                            </td>
                            <td style="padding: 16px; font-family: monospace;">
                                <?= $r['tipe_pergerakan']=='Keluar'?'-':'+' ?><?= $r['jumlah_stok'] ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
