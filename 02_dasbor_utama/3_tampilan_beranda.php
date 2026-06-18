<?php
// Diekstrak dari 2_tampilan_dasbor.php
// ══════════════ PAGE: BERANDA ══════════════
    date_default_timezone_set('Asia/Jakarta');
    $hour=(int)date('G');
    if($hour < 4) { $greeting = 'Selamat Malam'; }
    elseif($hour < 11) { $greeting = 'Selamat Pagi'; }
    elseif($hour < 15) { $greeting = 'Selamat Siang'; }
    elseif($hour < 18) { $greeting = 'Selamat Sore'; }
    else { $greeting = 'Selamat Malam'; }
?>
<style>
.home-top-grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
}
.greeting-strip-new {
    background: linear-gradient(120deg, var(--accent) 0%, var(--accent-2) 100%); color: #ffffff;
    border-radius: var(--radius-lg);
    border: 1px solid rgba(255,255,255,0.06);
    padding: 36px 40px;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
<?php if(isSuperAdmin()){ ?>
.greeting-strip-new {
    background: linear-gradient(120deg, #8b5cf6 0%, #d946ef 100%); color: #ffffff;
    border-color: rgba(217, 70, 239, 0.2);
}
.greeting-strip-new::before {
    background: radial-gradient(circle, rgba(217, 70, 239, 0.15) 0%, transparent 60%);
}
<?php } ?>
.greeting-strip-new::before {
    content:''; position:absolute; top:-50%; left:-10%; width:600px; height:600px;
    background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 60%);
    pointer-events:none;
}
.bento-grid-new {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}
@media(max-width: 992px) {
    .home-top-grid {
        grid-template-columns: 1fr;
    }
}
@media(max-width: 768px) {
    .dash-inner { padding-top: 16px !important; }
    .greeting-strip-new { padding: 24px 20px; }
    .greeting-strip-new .greeting-name { font-size: 1.6rem !important; line-height: 1.2; }
    .bento-grid-new { gap: 12px; }
    .bento-card { padding: 16px !important; }
    .stat-value { font-size: 1.3rem !important; }
}
@media(max-width: 480px) {
    .bento-grid-new { 
        display: grid;
        grid-template-columns: 1fr 1fr; 
        gap: 8px; 
    }
    .bento-card {
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        text-align: left;
        padding: 10px 14px !important;
        gap: 12px;
        min-height: 56px;
    }
    .bento-card-icon {
        width: 32px;
        height: 32px;
        font-size: 0.9rem;
        margin: 0;
    }
    .stat-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: auto;
    }
    .stat-label { 
        font-size: 0.55rem; 
        margin-top: 0; 
        margin-bottom: 4px;
        letter-spacing: 0.5px;
        opacity: 0.7;
    }
    .stat-value { 
        font-size: 1.15rem !important; 
        margin-bottom: 0; 
        font-family: var(--f-body);
        font-weight: 700;
        letter-spacing: 0;
        background: none;
        
        color: var(--text-main);
    }
    .stat-sub { display: none; } 
}
    .greeting-actions .gqa-btn { color: #ffffff !important; background: rgba(255,255,255,0.15) !important; border-color: rgba(255,255,255,0.3) !important; }
    .greeting-actions .gqa-btn:hover { background: rgba(255,255,255,0.25) !important; transform: translateY(-2px); }
</style>

<div class="dash-inner" style="padding-top: 32px;">
    <div class="home-top-grid">
        <!-- Kolom Kiri: Sapaan -->
        <div class="greeting-strip-new">
            <div class="greeting-content">
                <div class="greeting-label"><span class="pulse-dot"></span> Semua Sistem Normal</div>
                <h1 class="greeting-name"><?= $greeting ?>, <br><?= h($display_name) ?>.</h1>
                <p class="greeting-sub" style="margin-bottom: 32px;"><?= date('l, d F Y') ?> &mdash; Selamat datang kembali di Alfatih Workspace.</p>
                
                <div class="greeting-actions">
                    <a href="index.php?page=workspace" class="gqa-btn dark-inv"><i class="fa-solid fa-folder-open"></i> Buka Workspace</a>
                    <a href="index.php?page=profile" class="gqa-btn"><i class="fa-solid fa-pen-nib"></i> Edit CV</a>
                    <a href="<?= h($portfolio_url) ?>" target="_blank" class="gqa-btn"><i class="fa-solid fa-globe"></i> Lihat Web</a>
                    <?php if(isSuperAdmin()){?><a href="index.php?page=manajemen-pengguna" class="gqa-btn" style="border-color:rgba(217,70,239,0.3);color:#d946ef;background:rgba(217,70,239,0.1);"><i class="fa-solid fa-bolt"></i> God Mode</a><?php }?>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Stats Grid -->
        <div class="bento-grid-new">
            <div class="bento-card stat-block" style="margin-bottom: 0;">
                <div class="bento-card-icon dark"><i class="fa-solid fa-file-lines"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Total File</div>
                    <div class="stat-value"><?= $stat_files ?></div>
                    <div class="stat-sub">File Tersimpan</div>
                </div>
            </div>
            <div class="bento-card stat-block" style="margin-bottom: 0;">
                <div class="bento-card-icon light"><i class="fa-solid fa-folder-tree"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Direktori</div>
                    <div class="stat-value"><?= $stat_folders ?></div>
                    <div class="stat-sub">Folder Aktif</div>
                </div>
            </div>
            <div class="bento-card stat-block storage-bento" style="margin-bottom: 0;">
                <div class="bento-card-icon dark"><i class="fa-solid fa-hard-drive"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Penyimpanan</div>
                    <div class="stat-value" style="font-size:1.6rem;"><?= $size_used ?></div>
                    <div class="stat-sub">Kapasitas <?= $storage_pct ?>%</div>
                </div>
            </div>
            <div class="bento-card stat-block" style="margin-bottom: 0;">
                <div class="bento-card-icon light"><i class="fa-solid fa-link"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Tautan</div>
                    <div class="stat-value"><?= $stat_links ?></div>
                    <div class="stat-sub">URL Disimpan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Bawah: Profil -->
    <?php
    $ident_filled=!empty($profile_data['identitas']['nama_lengkap']);
    $edu_filled=!empty($profile_data['pendidikan']);
    $exp_filled=!empty($profile_data['pengalaman']);
    $skill_filled=!empty($profile_data['keahlian']);
    $pct_cv=(($ident_filled?25:0)+($edu_filled?25:0)+($exp_filled?25:0)+($skill_filled?25:0));
    ?>
    <div class="ed-card" style="margin-bottom: 24px;">
        <div class="ed-card-head">
            <h3><i class="fa-solid fa-id-badge"></i> Kelengkapan Profile</h3>
            <span class="pct-badge"><?= $pct_cv ?>%</span>
        </div>
        <div class="ed-card-body">
            <div class="progress-wrap"><div class="progress-bar" style="width:<?= $pct_cv ?>%;"></div></div>
            
            <?php if($pct_cv == 100){ ?>
            <div style="text-align:center;padding:32px 24px;">
                <i class="fa-solid fa-circle-check" style="font-size:3.5rem;color:#10b981;margin-bottom:16px;"></i>
                <div style="font-size:1.2rem;font-weight:700;color:var(--text-main);">Profil Anda Sudah Lengkap & Profesional!</div>
                <div style="font-size:0.9rem;color:#94a3b8;margin-top:6px;">Data identitas, pendidikan, pengalaman, dan keahlian sudah terisi dengan baik. CV Anda kini siap digunakan.</div>
            </div>
            <?php } else { ?>
            <div style="padding: 20px 24px; border-bottom: 1px dashed rgba(255,255,255,0.08); cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="const b=document.getElementById('portfolio-optional-body'); const i=document.getElementById('portfolio-optional-icon'); if(b.style.display==='none'){b.style.display='block';i.style.transform='rotate(180deg)';}else{b.style.display='none';i.style.transform='rotate(0)';}">
                <div style="font-size: 1.05rem; font-weight: 700; color: var(--text-main);">Tertarik Buat Web Portfolio? <span style="font-size: 0.85rem; font-weight: 500; color: #94a3b8;">(Opsional)</span></div>
                <i id="portfolio-optional-icon" class="fa-solid fa-chevron-down" style="color: #94a3b8; transition: transform 0.3s;"></i>
            </div>
            <div id="portfolio-optional-body" style="display: none;">
                <div style="padding: 16px 24px 0 24px; font-size: 0.85rem; color: #94a3b8; line-height: 1.5;">Kelengkapan profil ini bersifat opsional. Jika Anda ingin mempublikasikan Web Portfolio secara publik, silakan lengkapi informasi di bawah ini sesuai kebutuhan Anda:</div>
                <div class="profile-check-list" style="padding: 0 24px 12px 24px;">
                    <?php foreach([['Identitas Dasar', $ident_filled, 'fa-user', 'Isi data diri, kontak, dan alamat Anda.'], ['Riwayat Pendidikan', $edu_filled, 'fa-graduation-cap', 'Tambahkan informasi sekolah atau kampus tempat Anda belajar.'], ['Pengalaman Kerja', $exp_filled, 'fa-briefcase', 'Cantumkan riwayat pekerjaan atau magang Anda.'], ['Keahlian / Skill', $skill_filled, 'fa-code-branch', 'Tambahkan keahlian teknis maupun soft-skill Anda.']] as [$lbl, $done, $ico, $desc]){?>
                    <div class="profile-check-row <?= $done?'is-done':'' ?>" style="padding: 16px 0;">
                        <div class="pcr-icon" style="width: 44px; height: 44px; font-size: 1.2rem;"><i class="fa-solid <?= $ico ?>"></i></div>
                        <div class="pcr-info">
                            <div class="pcr-title" style="font-size: 0.9rem;"><?= $lbl ?></div>
                            <div class="pcr-desc" style="font-size: 0.75rem; margin-top: 4px;"><?= $done ? 'Tersimpan dengan aman ✓' : $desc ?></div>
                        </div>
                        <div class="pcr-action">
                            <?php if(!$done){?><a href="index.php?page=profile" class="btn-fill" style="background: linear-gradient(135deg, var(--accent), var(--accent-2)); padding: 8px 16px; font-size: 0.7rem; box-shadow: 0 4px 12px rgba(99,102,241,0.3);">Edit (Opsional)</a><?php }else{?><i class="fa-solid fa-check-circle text-success" style="font-size: 1.4rem;"></i><?php }?>
                        </div>
                    </div>
                    <?php }?>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
<?php



