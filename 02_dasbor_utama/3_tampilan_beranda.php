<?php
// Diekstrak dari 2_tampilan_dasbor.php
?>
// ââââââââââââââ PAGE: BERANDA ââââââââââââââ
if($current_page==='beranda'){
    $hour=(int)date('G');
    $greeting=$hour<12?'Selamat Pagi':($hour<17?'Selamat Siang':($hour<20?'Selamat Sore':'Selamat Malam'));
?>
<div class="greeting-strip">
    <div class="greeting-content">
        <div class="greeting-label"><span class="pulse-dot"></span> Semua Sistem Normal</div>
        <h1 class="greeting-name"><?= $greeting ?>, <br><?= h($display_name) ?>.</h1>
        <p class="greeting-sub"><?= date('l, d F Y') ?> &mdash; Selamat datang kembali di Alfatih Workspace.</p>
        <div class="greeting-actions">
            <a href="index.php?page=workspace" class="gqa-btn dark-inv"><i class="fa-solid fa-folder-open"></i> Buka Workspace</a>
            <a href="index.php?page=profile" class="gqa-btn"><i class="fa-solid fa-pen-nib"></i> Edit CV</a>
            <a href="<?= h($portfolio_url) ?>" target="_blank" class="gqa-btn"><i class="fa-solid fa-globe"></i> Lihat Web</a>
            <?php if(isSuperAdmin()){?><a href="index.php?page=manajemen-pengguna" class="gqa-btn" style="border-color:rgba(245,158,11,0.3);color:var(--superadmin);"><i class="fa-solid fa-users-gear"></i> Manage</a><?php }?>
        </div>
    </div>
</div>
<div class="dash-inner" style="padding:0;">
<div class="bento-grid" style="padding:32px 32px 0;">
    <div class="bento-card stat-block">
        <div class="bento-card-icon dark"><i class="fa-solid fa-file-lines"></i></div>
        <div class="stat-info">
            <div class="stat-label">Total File</div>
            <div class="stat-value"><?= $stat_files ?></div>
            <div class="stat-sub">File Tersimpan</div>
        </div>
    </div>
    <div class="bento-card stat-block">
        <div class="bento-card-icon light"><i class="fa-solid fa-folder-tree"></i></div>
        <div class="stat-info">
            <div class="stat-label">Direktori</div>
            <div class="stat-value"><?= $stat_folders ?></div>
            <div class="stat-sub">Folder Aktif</div>
        </div>
    </div>
    <div class="bento-card stat-block storage-bento">
        <div class="bento-card-icon dark"><i class="fa-solid fa-hard-drive"></i></div>
        <div class="stat-info">
            <div class="stat-label">Penyimpanan</div>
            <div class="stat-value" style="font-size:1.6rem;"><?= $size_used ?></div>
            <div class="stat-sub">Kapasitas <?= $storage_pct ?>%</div>
        </div>
    </div>
    <div class="bento-card stat-block">
        <div class="bento-card-icon light"><i class="fa-solid fa-link"></i></div>
        <div class="stat-info">
            <div class="stat-label">Tautan</div>
            <div class="stat-value"><?= $stat_links ?></div>
            <div class="stat-sub">URL Disimpan</div>
        </div>
    </div>
</div>
</div>
<div class="dash-inner grid-1" style="display:grid;grid-template-columns:1fr;gap:24px;padding-top:24px;">
<?php
$ident_filled=!empty($profile_data['identitas']['nama_lengkap']);
$edu_filled=!empty($profile_data['pendidikan']);
$exp_filled=!empty($profile_data['pengalaman']);
$skill_filled=!empty($profile_data['keahlian']);
$pct_cv=(($ident_filled?25:0)+($edu_filled?25:0)+($exp_filled?25:0)+($skill_filled?25:0));
?>
<div class="ed-card">
    <div class="ed-card-head"><h3><i class="fa-solid fa-id-badge"></i> Kelengkapan Profile</h3><span class="pct-badge"><?= $pct_cv ?>%</span></div>
    <div class="ed-card-body">
        <div class="progress-wrap"><div class="progress-bar" style="width:<?= $pct_cv ?>%;"></div></div>
        <?php if($pct_cv == 100){ ?>
        <div style="text-align:center;padding:24px 0;">
            <i class="fa-solid fa-circle-check" style="font-size:3rem;color:#10b981;margin-bottom:12px;"></i>
            <div style="font-size:1.1rem;font-weight:600;color:#fff;">Profil Anda Sudah Lengkap!</div>
            <div style="font-size:0.9rem;color:#94a3b8;margin-top:4px;">Terima kasih telah melengkapi data profil Anda.</div>
        </div>
        <?php } else { ?>
        <div class="profile-check-list">
            <?php foreach([['Identitas Dasar',$ident_filled,'fa-user'],['Riwayat Pendidikan',$edu_filled,'fa-graduation-cap'],['Pengalaman Kerja',$exp_filled,'fa-briefcase'],['Keahlian / Skill',$skill_filled,'fa-code-branch']] as [$lbl,$done,$ico]){?>
            <div class="profile-check-row <?= $done?'is-done':'' ?>">
                <div class="pcr-icon"><i class="fa-solid <?= $ico ?>"></i></div>
                <div class="pcr-info">
                    <div class="pcr-title"><?= $lbl ?></div>
                    <div class="pcr-desc"><?= $done?'Sudah diisi dengan baik':'Belum dilengkapi' ?></div>
                </div>
                <div class="pcr-action">
                    <?php if(!$done){?><a href="index.php?page=profile" class="btn-fill">Isi Sekarang</a><?php }else{?><i class="fa-solid fa-check-circle text-success"></i><?php }?>
                </div>
            </div>
            <?php }?>
        </div>
        <?php } ?>
    </div>
</div>
</div>

<?php
