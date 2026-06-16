<?php if (!defined('SITE_URL')) exit; // Proteksi akses langsung ?>

// +------------------------------------------------------------------------------+
// ¦  FILE: tampilan/dasbor/beranda.php                                           ¦
// ¦                                                                              ¦
// ¦  DESKRIPSI:                                                                  ¦
// ¦  Halaman Dasbor Utama (Overview) untuk pengguna yang berhasil masuk (Login). ¦
// ¦  Menampilkan sapaan dinamis berdasarkan waktu, widget ringkasan jumlah       ¦
// ¦  file/folder/penyimpanan, serta tabel riwayat aktivitas terbaru.             ¦
// ¦                                                                              ¦
// ¦  KONEKSI & RELASI:                                                           ¦
// ¦  - Di-render (di-include) oleh index.php saat variabel page = 'beranda'. ¦
// ¦  - Variabel statistik diambil langsung dari logika di index.php.           ¦
// ¦                                                                              ¦
// ¦  BARIS KODE PENTING:                                                         ¦
// ¦  - $greeting : Logika PHP penentuan sapaan Pagi/Siang/Sore/Malam.          ¦
// ¦  - oreach() : Melakukan iterasi untuk mencetak tabel      ¦
// ¦    aktivitas (login, upload, hapus) lengkap dengan log alamat IP.            ¦
// +------------------------------------------------------------------------------+

<?php
$hour = (int)date('G');
$greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 17 ? 'Selamat Siang' : ($hour < 20 ? 'Selamat Sore' : 'Selamat Malam'));
?>

<div class="greeting-strip">
    <div class="greeting-label"><?= $greeting ?>, <?= date('l, d F Y') ?></div>
    <h1 class="greeting-name"><?= h($display_name) ?>.</h1>
    <p class="greeting-sub">Selamat datang kembali di Alfatih Workspace. Semua sistem berjalan normal.</p>
    <div class="greeting-actions">
        <a href="index.php?page=workspace" class="gqa-btn dark-inv"><i class="fa-solid fa-folder-open"></i> Workspace</a>
        <a href="index.php?page=profile" class="gqa-btn"><i class="fa-solid fa-id-card"></i> CV Builder</a>
        <a href="<?= h($portfolio_url) ?>" target="_blank" class="gqa-btn"><i class="fa-solid fa-globe"></i> Portfolio</a>
        <?php if(isSuperAdmin()){?>
        <a href="index.php?page=manajemen-pengguna" class="gqa-btn" style="border-color:rgba(180,93,9,.5);color:#fed7aa;"><i class="fa-solid fa-users-gear"></i> Kelola User</a>
        <?php }?>
    </div>
</div>

<div class="dash-inner" style="padding:0;">
    <div class="bento-grid" style="padding:28px 32px 0;">
        <div class="bento-card stat-block"><div class="bento-card-icon dark"><i class="fa-solid fa-file"></i></div><div class="stat-label">Total File</div><div class="stat-value"><?= $stat_files ?? 0 ?></div><div class="stat-sub">File tersimpan</div></div>
        <div class="bento-card stat-block"><div class="bento-card-icon light"><i class="fa-solid fa-folder"></i></div><div class="stat-label">Folder</div><div class="stat-value"><?= $stat_folders ?? 0 ?></div><div class="stat-sub">Direktori aktif</div></div>
        <div class="bento-card stat-block"><div class="bento-card-icon dark"><i class="fa-solid fa-hard-drive"></i></div><div class="stat-label">Penyimpanan</div><div class="stat-value" style="font-size:1.6rem;"><?= $size_used ?? '0 B' ?></div><div class="stat-sub"><?= $storage_pct ?? 0 ?>% dari 1 GB</div></div>
        <div class="bento-card stat-block"><div class="bento-card-icon light"><i class="fa-solid fa-link"></i></div><div class="stat-label">Tautan</div><div class="stat-value"><?= $stat_links ?? 0 ?></div><div class="stat-sub">URL tersimpan</div></div>
    </div>
</div>

<div class="dash-inner">
    <div class="ed-card">
        <div class="ed-card-head"><h3><i class="fa-solid fa-clock-rotate-left"></i> Aktivitas Terbaru</h3><a href="index.php?page=workspace">Lihat semua &rarr;</a></div>
        <div class="ed-card-body">
            <?php if(empty($recent_activity)){?>
            <div class="empty-activity">Belum ada aktivitas tercatat.</div>
            <?php }else{?>
            <table class="activity-table">
                <thead><tr><th>Aksi</th><th>Detail</th><th>IP</th><th>Waktu</th></tr></thead>
                <tbody>
                <?php foreach($recent_activity as $ra){
                    $al=strtolower($ra['action']??'');
                    $bc=str_contains($al,'login')?'login':(str_contains($al,'upload')?'upload':(str_contains($al,'delete')?'delete':'other'));
                    echo "<tr><td><span class='act-badge $bc'>".h($ra['action'])."</span></td><td>".h($ra['details']??'-')."</td><td style='font-family:monospace;font-size:.75rem;'>".h($ra['ip_address']??'-')."</td><td style='font-size:.75rem;color:var(--text-muted);'>".date('d M Y H:i',strtotime($ra['created_at']))."</td></tr>";
                }?>
                </tbody>
            </table>
            <?php }?>
        </div>
    </div>

    <?php
    $ident_filled = !empty($profile_data['identitas']['nama_lengkap']);
    $edu_filled   = !empty($profile_data['pendidikan']);
    $exp_filled   = !empty($profile_data['pengalaman']);
    $skill_filled = !empty($profile_data['keahlian']);
    $pct_cv       = (($ident_filled?25:0)+($edu_filled?25:0)+($exp_filled?25:0)+($skill_filled?25:0));
    ?>
    <div class="ed-card" style="margin-top:24px;">
        <div class="ed-card-head"><h3><i class="fa-solid fa-id-card"></i> Kelengkapan CV</h3><span style="font-size:.82rem;font-weight:700;"><?= $pct_cv ?>%</span></div>
        <div class="ed-card-body">
            <div style="height:3px;background:var(--border);"><div style="height:100%;width:<?= $pct_cv ?>%;background:var(--ink);transition:width .6s;"></div></div>
            <div class="profile-check-grid">
                <?php foreach([['Identitas',$ident_filled,'fa-user'],['Pendidikan',$edu_filled,'fa-graduation-cap'],['Pengalaman',$exp_filled,'fa-briefcase'],['Keahlian',$skill_filled,'fa-code']] as [$lbl,$done,$ico]){?>
                <div class="profile-check-item">
                    <i class="fa-solid <?= $ico ?> <?= $done?'done':'todo' ?>" style="width:16px;"></i>
                    <span><?= $lbl ?></span>
                    <?php if(!$done){?><a href="index.php?page=profile" style="margin-left:auto;font-size:.68rem;font-weight:700;text-transform:uppercase;color:var(--text-muted);border-bottom:1px solid var(--border);">Isi &rarr;</a><?php }else{?><i class="fa-solid fa-check" style="margin-left:auto;font-size:.75rem;color:var(--success);"></i><?php }?>
                </div>
                <?php }?>
            </div>
        </div>
    </div>
</div>