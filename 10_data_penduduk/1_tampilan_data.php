<?php
if (!defined('SITE_URL') || empty($_SESSION['uid'])) exit;

if (($_SESSION['profesi'] ?? '') !== 'Pegawai Balai Desa' && !isSuperAdmin()) {
    echo "<div class='alert-bar error'><i class='fa-solid fa-circle-exclamation'></i> Akses Ditolak! Anda bukan Pegawai Balai Desa.</div>";
    exit;
}

$owner = $_SESSION['username'];
$query = "SELECT * FROM penduduk ";
if (!isSuperAdmin()) {
    $query .= "WHERE owner_username='$owner' ";
}
$query .= "ORDER BY id DESC";

$res = $mysqli->query($query);
$penduduk = [];
if ($res) {
    while($row = $res->fetch_assoc()) {
        $penduduk[] = $row;
    }
}
?>

<div style="padding: 24px; max-width: 1200px; margin: 0 auto; animation: fadeUp 0.5s ease;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-family: var(--f-display); margin: 0 0 8px 0; font-size: 2rem;">Data Warga</h1>
            <p style="color: var(--text-muted); margin: 0;">Sistem Informasi Kependudukan Desa Cerdas Berbasis AI</p>
        </div>
        <button onclick="document.querySelector('.ai-widget-btn').click();" style="background: linear-gradient(135deg, var(--accent-secondary), var(--accent-primary)); border: none; padding: 12px 24px; border-radius: 8px; color: #fff; font-weight: 600; cursor: pointer; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 8px; transition: transform 0.2s;">
            <i class="fa-solid fa-camera"></i> Scan KTP dengan AI
        </button>
    </div>

    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-sm);">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: rgba(255,255,255,0.04); border-bottom: 1px solid var(--border);">
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">NIK</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Nama Lengkap</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">TTL</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Jenis Kelamin</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Alamat</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Pekerjaan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($penduduk) === 0): ?>
                <tr>
                    <td colspan="6" style="padding: 32px; text-align: center; color: var(--text-muted);">
                        <i class="fa-solid fa-users" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i><br>
                        Belum ada data warga.<br>Gunakan fitur <strong>Scan KTP dengan AI</strong> untuk menambahkan data secara otomatis!
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($penduduk as $p): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 16px; font-family: monospace; font-size: 0.95rem;"><?= htmlspecialchars($p['nik']) ?></td>
                        <td style="padding: 16px; font-weight: 500;"><?= htmlspecialchars($p['nama']) ?></td>
                        <td style="padding: 16px; color: var(--text-muted); font-size: 0.9rem;"><?= htmlspecialchars($p['tempat_lahir'] . ', ' . $p['tanggal_lahir']) ?></td>
                        <td style="padding: 16px; color: var(--text-muted); font-size: 0.9rem;"><?= htmlspecialchars($p['jenis_kelamin']) ?></td>
                        <td style="padding: 16px; color: var(--text-muted); font-size: 0.9rem;"><?= htmlspecialchars($p['alamat']) ?></td>
                        <td style="padding: 16px; color: var(--text-muted); font-size: 0.9rem;"><?= htmlspecialchars($p['pekerjaan']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
