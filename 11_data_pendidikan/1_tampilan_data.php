<?php
if (!defined('SITE_URL') || empty($_SESSION['uid'])) exit;

if (($_SESSION['profesi'] ?? '') !== 'Guru / Dosen' && !isSuperAdmin()) {
    echo "<div class='alert-bar error'><i class='fa-solid fa-circle-exclamation'></i> Akses Ditolak! Anda bukan Guru / Dosen.</div>";
    exit;
}

$owner = $_SESSION['username'];
$query = "SELECT * FROM data_siswa ";
if (!isSuperAdmin()) {
    $query .= "WHERE owner_username='$owner' ";
}
$query .= "ORDER BY id DESC";

$res = $mysqli->query($query);
$siswa = [];
if ($res) {
    while($row = $res->fetch_assoc()) {
        $siswa[] = $row;
    }
}
?>

<div style="padding: 24px; max-width: 1200px; margin: 0 auto; animation: fadeUp 0.5s ease;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-family: var(--f-display); margin: 0 0 8px 0; font-size: 2rem;">Data Tugas & Nilai Siswa</h1>
            <p style="color: var(--text-muted); margin: 0;">Sistem Manajemen Nilai Cerdas</p>
        </div>
        <button style="background: linear-gradient(135deg, var(--accent-secondary), var(--accent-primary)); border: none; padding: 12px 24px; border-radius: 8px; color: #fff; font-weight: 600; cursor: pointer; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 8px; transition: transform 0.2s;">
            <i class="fa-solid fa-plus"></i> Tambah Nilai Baru
        </button>
    </div>

    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-sm);">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: rgba(255,255,255,0.04); border-bottom: 1px solid var(--border);">
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">NIS</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Nama Siswa</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Kelas</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Nilai</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($siswa) === 0): ?>
                <tr>
                    <td colspan="5" style="padding: 32px; text-align: center; color: var(--text-muted);">
                        <i class="fa-solid fa-graduation-cap" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i><br>
                        Belum ada data nilai siswa.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($siswa as $s): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 16px; font-family: monospace; font-size: 0.95rem;"><?= htmlspecialchars($s['nis']) ?></td>
                        <td style="padding: 16px; font-weight: 500;"><?= htmlspecialchars($s['nama_siswa']) ?></td>
                        <td style="padding: 16px; color: var(--text-muted); font-size: 0.9rem;"><?= htmlspecialchars($s['kelas']) ?></td>
                        <td style="padding: 16px; font-weight: bold; color: <?= $s['nilai'] >= 75 ? 'var(--success)' : 'var(--danger)' ?>; font-size: 1.1rem;"><?= htmlspecialchars($s['nilai']) ?></td>
                        <td style="padding: 16px; color: var(--text-muted); font-size: 0.9rem;"><?= htmlspecialchars($s['catatan']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
