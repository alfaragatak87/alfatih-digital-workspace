<?php
if (!defined('SITE_URL') || empty($_SESSION['uid'])) exit;

$owner = $_SESSION['username'];

// Proses aksi form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_keuangan'])) {
    $tipe = $mysqli->real_escape_string($_POST['tipe']);
    $nominal = (float)$_POST['nominal'];
    $keterangan = $mysqli->real_escape_string($_POST['keterangan']);
    $tanggal = $mysqli->real_escape_string($_POST['tanggal']);
    
    if ($nominal > 0 && !empty($keterangan) && !empty($tanggal)) {
        $mysqli->query("INSERT INTO keuangan (owner_username, tipe, nominal, keterangan, tanggal) VALUES ('$owner', '$tipe', $nominal, '$keterangan', '$tanggal')");
        header("Location: index.php?page=keuangan");
        exit;
    }
}

// Ambil data
$res = $mysqli->query("SELECT * FROM keuangan WHERE owner_username='$owner' ORDER BY tanggal DESC, id DESC");
$keuangan = [];
$total_pemasukan = 0;
$total_pengeluaran = 0;

if ($res) {
    while($r = $res->fetch_assoc()) {
        $keuangan[] = $r;
        if ($r['tipe'] === 'Pemasukan') $total_pemasukan += $r['nominal'];
        else $total_pengeluaran += $r['nominal'];
    }
}
$laba_rugi = $total_pemasukan - $total_pengeluaran;
?>

<div style="padding: 24px; max-width: 1000px; margin: 0 auto; animation: fadeUp 0.5s ease;">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px;">
        <div>
            <h1 style="font-family: var(--f-display); margin: 0 0 8px 0; font-size: 2.5rem;"><i class="fa-solid fa-wallet" style="color:var(--success);"></i> Keuangan Bisnis</h1>
            <p style="color: var(--text-muted); margin: 0;">Catat dan pantau arus kas Anda.</p>
        </div>
    </div>

    <!-- Ringkasan Card -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 32px;">
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 16px; padding: 24px; text-align: center;">
            <p style="color: var(--text-muted); margin: 0 0 8px 0; font-size: 0.9rem; text-transform: uppercase;">Total Pemasukan</p>
            <h2 style="margin: 0; color: var(--success); font-family: monospace;">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></h2>
        </div>
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 16px; padding: 24px; text-align: center;">
            <p style="color: var(--text-muted); margin: 0 0 8px 0; font-size: 0.9rem; text-transform: uppercase;">Total Pengeluaran</p>
            <h2 style="margin: 0; color: var(--danger); font-family: monospace;">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></h2>
        </div>
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 16px; padding: 24px; text-align: center;">
            <p style="color: var(--text-muted); margin: 0 0 8px 0; font-size: 0.9rem; text-transform: uppercase;">Laba / Rugi Bersih</p>
            <h2 style="margin: 0; color: <?= $laba_rugi >= 0 ? '#fff' : 'var(--danger)' ?>; font-family: monospace;">Rp <?= number_format($laba_rugi, 0, ',', '.') ?></h2>
        </div>
    </div>

    <!-- Form Tambah -->
    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 16px; padding: 24px; margin-bottom: 32px; box-shadow: var(--shadow-sm);">
        <h3 style="margin: 0 0 16px 0; font-size: 1.1rem;"><i class="fa-solid fa-plus-circle"></i> Catat Transaksi Baru</h3>
        <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr 2fr 1fr auto; gap: 12px; align-items: end;">
            <div>
                <label style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 8px;">Tipe</label>
                <select name="tipe" required style="width:100%; padding: 12px; border-radius: 8px; background: rgba(0,0,0,0.2); color: #fff; border: 1px solid var(--border);">
                    <option value="Pemasukan">Pemasukan</option>
                    <option value="Pengeluaran">Pengeluaran</option>
                </select>
            </div>
            <div>
                <label style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 8px;">Nominal (Rp)</label>
                <input type="number" name="nominal" required min="1" style="width:100%; padding: 12px; border-radius: 8px; background: rgba(0,0,0,0.2); color: #fff; border: 1px solid var(--border);">
            </div>
            <div>
                <label style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 8px;">Keterangan</label>
                <input type="text" name="keterangan" required placeholder="Contoh: Beli Bahan Baku" style="width:100%; padding: 12px; border-radius: 8px; background: rgba(0,0,0,0.2); color: #fff; border: 1px solid var(--border);">
            </div>
            <div>
                <label style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 8px;">Tanggal</label>
                <input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>" style="width:100%; padding: 12px; border-radius: 8px; background: rgba(0,0,0,0.2); color: #fff; border: 1px solid var(--border);">
            </div>
            <button type="submit" name="tambah_keuangan" style="background: var(--accent); border: none; padding: 12px 24px; border-radius: 8px; color: #fff; font-weight: bold; cursor: pointer; height: 43px;">Simpan</button>
        </form>
    </div>

    <!-- Riwayat Transaksi -->
    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 16px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: rgba(255,255,255,0.04); border-bottom: 1px solid var(--border);">
                    <th style="padding: 16px; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Tanggal</th>
                    <th style="padding: 16px; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Keterangan</th>
                    <th style="padding: 16px; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; text-align: right;">Pemasukan</th>
                    <th style="padding: 16px; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; text-align: right;">Pengeluaran</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($keuangan)): ?>
                <tr><td colspan="4" style="text-align:center; padding: 32px; color: var(--text-muted);">Belum ada riwayat transaksi.</td></tr>
                <?php else: ?>
                    <?php foreach($keuangan as $k): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                        <td style="padding: 16px; color: var(--text-muted); font-size: 0.9rem;"><?= htmlspecialchars($k['tanggal']) ?></td>
                        <td style="padding: 16px; font-weight: 500;"><?= htmlspecialchars($k['keterangan']) ?></td>
                        <td style="padding: 16px; text-align: right; color: var(--success); font-family: monospace;">
                            <?= $k['tipe']==='Pemasukan' ? '+ Rp '.number_format($k['nominal'],0,',','.') : '-' ?>
                        </td>
                        <td style="padding: 16px; text-align: right; color: var(--danger); font-family: monospace;">
                            <?= $k['tipe']==='Pengeluaran' ? '- Rp '.number_format($k['nominal'],0,',','.') : '-' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
