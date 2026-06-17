<?php
if (!defined('SITE_URL') || empty($_SESSION['uid'])) exit;

if (($_SESSION['profesi'] ?? '') !== 'Pekerja Lepas / Kreatif' && !isSuperAdmin()) {
    echo "<div class='alert-bar error'><i class='fa-solid fa-circle-exclamation'></i> Akses Ditolak! Anda bukan Pekerja Lepas / Kreatif.</div>";
    exit;
}

// Data dummy untuk demonstrasi karena belum ada tabel database proyek_klien.
$proyek = [
    [
        'id' => 1,
        'nama_klien' => 'PT Makmur Jaya',
        'nama_proyek' => 'Redesain Website Company Profile',
        'status' => 'Sedang Berjalan',
        'tenggat_waktu' => '2026-07-01',
        'nilai_proyek' => 'Rp 5.000.000'
    ],
    [
        'id' => 2,
        'nama_klien' => 'Toko Buku Kita',
        'nama_proyek' => 'Pembuatan Aplikasi Kasir',
        'status' => 'Selesai',
        'tenggat_waktu' => '2026-05-15',
        'nilai_proyek' => 'Rp 12.500.000'
    ]
];
?>

<div style="padding: 24px; max-width: 1200px; margin: 0 auto; animation: fadeUp 0.5s ease;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div>
            <h1 style="font-family: var(--f-display); margin: 0 0 8px 0; font-size: 2rem;">Manajemen Proyek Klien</h1>
            <p style="color: var(--text-muted); margin: 0;">Lacak kemajuan proyek dan tagihan (Invoice)</p>
        </div>
        <button style="background: linear-gradient(135deg, var(--accent-secondary), var(--accent-primary)); border: none; padding: 12px 24px; border-radius: 8px; color: #fff; font-weight: 600; cursor: pointer; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 8px; transition: transform 0.2s;">
            <i class="fa-solid fa-folder-plus"></i> Buat Proyek Baru
        </button>
    </div>

    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-sm);">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: rgba(255,255,255,0.04); border-bottom: 1px solid var(--border);">
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">ID</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Klien</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Nama Proyek</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Status</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Tenggat Waktu</th>
                    <th style="padding: 16px; font-weight: 600; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($proyek as $p): ?>
                <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 16px; font-family: monospace; font-size: 0.95rem;">PRJ-<?= str_pad($p['id'], 4, '0', STR_PAD_LEFT) ?></td>
                    <td style="padding: 16px; font-weight: 500;"><?= htmlspecialchars($p['nama_klien']) ?></td>
                    <td style="padding: 16px; color: var(--text-muted); font-size: 0.9rem;"><?= htmlspecialchars($p['nama_proyek']) ?></td>
                    <td style="padding: 16px;">
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; background: <?= $p['status'] === 'Selesai' ? 'var(--success-bg)' : 'var(--warning-bg)' ?>; color: <?= $p['status'] === 'Selesai' ? 'var(--success)' : 'var(--warning)' ?>;">
                            <?= htmlspecialchars($p['status']) ?>
                        </span>
                    </td>
                    <td style="padding: 16px; color: var(--text-muted); font-size: 0.9rem;"><?= htmlspecialchars($p['tenggat_waktu']) ?></td>
                    <td style="padding: 16px; font-weight: 500; color: var(--text-main);"><?= htmlspecialchars($p['nilai_proyek']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
