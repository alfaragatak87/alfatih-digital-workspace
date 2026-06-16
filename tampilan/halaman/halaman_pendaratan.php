<?php if (!defined('SITE_URL')) exit; // Proteksi akses langsung ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pub_page==='login' ? 'Login — Alfatih Workspace' : 'Alfatih Digital Workspace' ?></title>
    <meta name="theme-color" content="#0a0a0a">
    <meta name="application-name" content="Alfatih Workspace">
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="aset/images/LOGO_GAWE.svg">
    <link rel="icon" type="image/svg+xml" href="aset/images/LOGO_GAWE.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}a{color:inherit;text-decoration:none;}::selection{background:#000;color:#fff;}
        ::-webkit-scrollbar{width:4px;}::-webkit-scrollbar-track{background:#f5f5f5;}::-webkit-scrollbar-thumb{background:#ccc;}
        body{font-family:'Inter',system-ui,sans-serif;background:#fafafa;color:#0a0a0a;overflow-x:hidden;}
        .pub-nav{position:fixed;top:0;left:0;right:0;height:60px;display:flex;align-items:center;justify-content:space-between;padding:0 40px;z-index:100;background:#fafafa;border-bottom:1px solid #0a0a0a;}
        .pub-nav-logo{display:flex;align-items:center;gap:10px;}
        .pub-nav-logo img{height:28px;object-fit:contain;}
        .pub-nav-logo span{font-family:'Playfair Display',serif;font-size:1.05rem;font-weight:700;letter-spacing:-.5px;color:#0a0a0a;}
        .pub-nav-links{display:flex;align-items:center;gap:0;}
        .pub-nav-links a{padding:8px 18px;font-size:.78rem;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:#555;transition:color .2s;border-left:1px solid #e5e5e5;}
        .pub-nav-links a:hover{color:#0a0a0a;}
        .pub-nav-links a.nav-cta{background:#0a0a0a;color:#fff;border-color:#0a0a0a;}
        .pub-nav-links a.nav-cta:hover{background:#333;}
        .ed-hero{min-height:100vh;display:flex;flex-direction:column;justify-content:flex-end;padding:120px 40px 60px;position:relative;border-bottom:1px solid #0a0a0a;}
        .ed-hero-eyebrow{font-size:.72rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#888;margin-bottom:20px;}
        .ed-hero-title{font-family:'Playfair Display',serif;font-size:clamp(3.5rem,8vw,7rem);font-weight:900;line-height:1;letter-spacing:-3px;margin-bottom:24px;color:#0a0a0a;}
        .ed-hero-title em{font-style:italic;color:#555;}
        .ed-hero-sub{font-size:1rem;color:#666;max-width:480px;line-height:1.7;margin-bottom:40px;}
        .ed-hero-ctas{display:flex;gap:0;flex-wrap:wrap;}
        .btn-ed-primary{padding:16px 36px;background:#0a0a0a;color:#fff;font-size:.82rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;border:1px solid #0a0a0a;transition:all .2s;display:inline-flex;align-items:center;gap:8px;}
        .btn-ed-primary:hover{background:#333;}
        .btn-ed-ghost{padding:16px 36px;background:transparent;color:#0a0a0a;font-size:.82rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;border:1px solid #ccc;transition:all .2s;display:inline-flex;align-items:center;gap:8px;}
        .btn-ed-ghost:hover{border-color:#0a0a0a;}
        .ed-hero-marquee-wrap{position:absolute;bottom:0;left:0;right:0;overflow:hidden;border-top:1px solid #e5e5e5;padding:12px 0;background:#f0f0f0;}
        .ed-marquee{display:flex;gap:40px;white-space:nowrap;animation:marquee 20s linear infinite;}
        .ed-marquee span{font-size:.7rem;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:#999;}
        @keyframes marquee{from{transform:translateX(0)}to{transform:translateX(-50%)}}
        .ed-features{display:grid;grid-template-columns:repeat(4,1fr);border-bottom:1px solid #0a0a0a;}
        .ed-feat{padding:40px 32px;border-right:1px solid #0a0a0a;}
        .ed-feat:last-child{border-right:none;}
        .ed-feat-num{font-family:'Playfair Display',serif;font-size:2.5rem;font-weight:900;margin-bottom:12px;}
        .ed-feat-label{font-size:.78rem;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:#555;margin-bottom:8px;}
        .ed-feat-desc{font-size:.85rem;color:#888;line-height:1.6;}
        .ed-section{padding:80px 40px;border-bottom:1px solid #0a0a0a;}
        .ed-section-head{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:48px;padding-bottom:20px;border-bottom:1px solid #0a0a0a;}
        .ed-section-head-left{display:flex;align-items:baseline;gap:16px;}
        .ed-section-label{font-size:.68rem;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:#888;}
        .ed-section-title{font-family:'Playfair Display',serif;font-size:2.5rem;font-weight:900;letter-spacing:-1px;}
        .ed-section-sub{font-size:.82rem;color:#888;}
        .talent-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:0;border:1px solid #0a0a0a;}
        .talent-card{padding:32px;border-right:1px solid #0a0a0a;border-bottom:1px solid #0a0a0a;transition:background .2s;}
        .talent-card:hover{background:#f5f5f5;}
        .talent-card-num{font-size:.7rem;font-weight:700;letter-spacing:2px;color:#ccc;margin-bottom:20px;}
        .talent-card-avatar{width:64px;height:64px;object-fit:cover;filter:grayscale(100%);border:1px solid #0a0a0a;margin-bottom:16px;display:block;}
        .talent-card-name{font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:700;margin-bottom:4px;line-height:1.2;}
        .talent-card-profesi{font-size:.75rem;font-weight:600;letter-spacing:.5px;text-transform:uppercase;color:#888;margin-bottom:12px;}
        .talent-card-summary{font-size:.83rem;color:#555;line-height:1.6;margin-bottom:20px;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;}
        .talent-card-tags{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:20px;}
        .talent-card-tag{font-size:.68rem;font-weight:600;letter-spacing:.5px;text-transform:uppercase;padding:4px 10px;border:1px solid #0a0a0a;}
        .talent-card-link{font-size:.72rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;display:inline-flex;align-items:center;gap:6px;border-bottom:1px solid #0a0a0a;padding-bottom:2px;}
        .talent-card-link:hover{opacity:.6;}
        .talent-empty{padding:80px 40px;text-align:center;color:#888;}
        .preview-strip{display:grid;grid-template-columns:repeat(3,1fr);border-bottom:1px solid #0a0a0a;}
        .preview-panel{padding:60px 40px;border-right:1px solid #0a0a0a;}
        .preview-panel:last-child{border-right:none;}
        .preview-panel-icon{font-size:2rem;margin-bottom:20px;}
        .preview-panel-title{font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;margin-bottom:12px;}
        .preview-panel-desc{font-size:.85rem;color:#666;line-height:1.7;}
        .pub-footer{padding:40px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid #0a0a0a;}
        .pub-footer-copy{font-size:.75rem;color:#888;letter-spacing:.5px;}
        .pub-footer-link{font-size:.75rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;border-bottom:1px solid #0a0a0a;}
        .login-page{min-height:100vh;display:grid;grid-template-columns:1fr 1fr;}
        .login-left{background:#0a0a0a;display:flex;flex-direction:column;justify-content:flex-end;padding:60px;position:relative;overflow:hidden;}
        .login-left-decor{font-family:'Playfair Display',serif;font-size:clamp(4rem,10vw,9rem);font-weight:900;color:rgba(255,255,255,.05);position:absolute;top:40px;left:40px;line-height:1;user-select:none;}
        .login-left-title{font-family:'Playfair Display',serif;font-size:2.5rem;font-weight:900;color:#fff;line-height:1.1;margin-bottom:12px;}
        .login-left-sub{font-size:.85rem;color:rgba(255,255,255,.5);line-height:1.7;}
        .login-right{display:flex;flex-direction:column;justify-content:center;padding:60px;}
        .login-right-back{font-size:.75rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:#888;display:inline-flex;align-items:center;gap:6px;margin-bottom:48px;}
        .login-right-back:hover{color:#0a0a0a;}
        .login-right-title{font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;margin-bottom:8px;}
        .login-right-sub{font-size:.85rem;color:#888;margin-bottom:36px;}
        .login-err{background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;padding:12px 16px;font-size:.85rem;margin-bottom:20px;}
        .ed-form-group{margin-bottom:20px;}
        .ed-form-label{display:block;font-size:.72rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#555;margin-bottom:8px;}
        .ed-form-input{width:100%;padding:12px 0;background:transparent;border:none;border-bottom:2px solid #0a0a0a;color:#0a0a0a;font-size:.95rem;font-family:'Inter',sans-serif;outline:none;transition:border-color .2s;}
        .ed-form-input::placeholder{color:#ccc;}.ed-form-input:focus{border-color:#555;}.ed-form-input.err{border-color:#dc2626;}
        .btn-ed-submit{width:100%;padding:16px;background:#0a0a0a;color:#fff;font-size:.82rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;border:none;cursor:pointer;font-family:'Inter',sans-serif;margin-top:8px;transition:background .2s;}
        .btn-ed-submit:hover{background:#333;}
        @media(max-width:768px){
            .login-page{grid-template-columns:1fr;}.login-left{display:none;}
            .ed-features{grid-template-columns:repeat(2,1fr);}.talent-grid{grid-template-columns:1fr;}
            .preview-strip{grid-template-columns:1fr;}.preview-panel{border-right:none;}
            .pub-nav{padding:0 20px;}.ed-hero{padding:100px 20px 50px;}.ed-section{padding:48px 20px;}
            .pub-footer{padding:24px 20px;flex-direction:column;gap:12px;}
        }
    </style>
</head>
<body>
<nav class="pub-nav">
    <div class="pub-nav-logo">
        <img src="aset/images/LOGO_GAWE.svg" alt="Logo" onerror="this.style.display='none'">
        <span>GAWE.MY.ID</span>
    </div>
    <div class="pub-nav-links">
        <?php if ($pub_page !== 'login') { ?>
        <a href="#talent">Talents</a><a href="#features">Features</a>
        <?php } ?>
        <a href="index.php?page=login" class="nav-cta">Login</a>
    </div>
</nav>

<?php if ($pub_page === 'login') { ?>
<div class="login-page" style="padding-top:60px;">
    <div class="login-left">
        <div class="login-left-decor">AW</div>
        <h2 class="login-left-title">Selamat<br>Datang<br>Kembali.</h2>
        <p class="login-left-sub">Kelola file, bangun portfolio, dan tampilkan karya terbaik Anda kepada dunia.</p>
    </div>
    <div class="login-right">
        <a href="index.php" class="login-right-back"><i class="fa-solid fa-arrow-left"></i> Beranda</a>
        <h1 class="login-right-title">Masuk</h1>
        <p class="login-right-sub">Gunakan kredensial akun Anda untuk melanjutkan.</p>
        <?php if ($error_msg) { ?><div class="login-err"><i class="fa-solid fa-circle-exclamation" style="margin-right:6px;"></i><?= h($error_msg) ?></div><?php } ?>
        <form method="POST" action="index.php">
            <input type="hidden" name="action" value="login">
            <input type="hidden" name="csrf_token" value="<?= generateCSRF() ?>">
            <div class="ed-form-group">
                <label class="ed-form-label">Username</label>
                <input type="text" name="username" class="ed-form-input <?= $error_msg ? 'err' : '' ?>" placeholder="Username Anda" required autocomplete="username">
            </div>
            <div class="ed-form-group">
                <label class="ed-form-label">Password</label>
                <input type="password" name="password" class="ed-form-input <?= $error_msg ? 'err' : '' ?>" placeholder="••••••••" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn-ed-submit">Masuk ke Workspace &rarr;</button>
        </form>
    </div>
</div>
<?php } else { ?>
<section class="ed-hero" style="padding-top:80px;">
    <div class="ed-hero-eyebrow">Est. 2026 &mdash; Digital Workspace</div>
    <h1 class="ed-hero-title">SELAMAT DATANG.<br><em>DIWORKSPACE</em><br>Organized.</h1>
    <p class="ed-hero-sub">Ruang kerja digital untuk menyimpan, mengelola, dan Membuat Cv builder Anda.</p>
    <div class="ed-hero-ctas">
        <a href="index.php?page=login" class="btn-ed-primary">Masuk ke Workspace <i class="fa-solid fa-arrow-right"></i></a>
        <a href="#talent" class="btn-ed-ghost">Lihat Talent <i class="fa-solid fa-arrow-down"></i></a>
    </div>
    <div class="ed-hero-marquee-wrap">
        <div class="ed-marquee">
            <?php for($i=0;$i<4;$i++){ ?><span>File Manager</span><span>&mdash;</span><span>CV Builder</span><span>&mdash;</span><span>Portfolio</span><span>&mdash;</span><span>Talent Directory</span><span>&mdash;</span><span>RBAC Security</span><span>&mdash;</span><?php } ?>
        </div>
    </div>
</section>

<section class="ed-section" id="talent">
    <div class="ed-section-head">
        <div class="ed-section-head-left"><span class="ed-section-label">Direktori</span><h2 class="ed-section-title">Talent</h2></div>
        <span class="ed-section-sub"><?= count($talent_users ?? []) ?> profil aktif</span>
    </div>
    <?php if (empty($talent_users)) { ?>
    <div class="talent-empty">
        <i class="fa-solid fa-users" style="font-size:2.5rem;display:block;margin-bottom:12px;color:#ccc;"></i>
        <strong>Belum ada profil publik.</strong><br>Login dan aktifkan "Tampilkan di Direktori Publik" di CV Builder.
    </div>
    <?php } else { ?>
    <div class="talent-grid">
        <?php foreach ($talent_users as $idx => $tu) {
            $tpd = $tu['_pd']; $tid2 = $tpd['identitas'] ?? [];
            $t_name = !empty($tid2['nama_lengkap']) ? $tid2['nama_lengkap'] : ($tu['nama_lengkap'] ?? $tu['username']);
            $t_sebutan = $tid2['nama_sebutan'] ?? ''; $t_profesi = $tid2['profesi'] ?? '';
            $t_summary = $tid2['summary'] ?? ''; $t_skills = array_slice($tpd['keahlian'] ?? [], 0, 4);
            $t_foto_raw = $tu['foto_profil'] ?? '';
            $t_foto = ($t_foto_raw && $t_foto_raw !== 'default.png' && file_exists(PROFILE_IMG_DIR . $t_foto_raw))
                ? PROFILE_IMG_DIR . $t_foto_raw
                : 'https://ui-avatars.com/api/?name=' . urlencode($t_name) . '&background=1a1a1a&color=ffffff&bold=true&size=128';
            $t_url = SITE_URL . '/index.php?portfolio=' . urlencode($tu['username']);
        ?>
        <div class="talent-card">
            <div class="talent-card-num"><?= str_pad($idx+1, 2, '0', STR_PAD_LEFT) ?></div>
            <img src="<?= h($t_foto) ?>" alt="<?= h($t_name) ?>" class="talent-card-avatar" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($t_name) ?>&background=1a1a1a&color=ffffff&bold=true'">
            <h3 class="talent-card-name"><?= h($t_name) ?><?php if($t_sebutan){?> <small style="font-size:.7em;font-weight:400;color:#888;">"<?= h($t_sebutan) ?>"</small><?php }?></h3>
            <?php if ($t_profesi) { ?><div class="talent-card-profesi"><?= h($t_profesi) ?></div><?php } ?>
            <?php if ($t_summary) { ?><p class="talent-card-summary"><?= h($t_summary) ?></p><?php } ?>
            <?php if (!empty($t_skills)) { ?><div class="talent-card-tags"><?php foreach ($t_skills as $tsk) { ?><span class="talent-card-tag"><?= h($tsk['nama']??'') ?></span><?php } ?></div><?php } ?>
            <a href="<?= h($t_url) ?>" target="_blank" class="talent-card-link">Lihat Portfolio <i class="fa-solid fa-arrow-right" style="font-size:.6em;"></i></a>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
</section>
<div class="preview-strip" id="features">
    <div class="preview-panel"><div class="preview-panel-icon"><i class="fa-solid fa-folder-open"></i></div><h3 class="preview-panel-title">Smart File Manager</h3><p class="preview-panel-desc">Folder bersarang, upload multi-file, drag & drop, bulk operasi, filter per user, dan share link publik dengan QR code.</p></div>
    <div class="preview-panel"><div class="preview-panel-icon"><i class="fa-solid fa-id-card"></i></div><h3 class="preview-panel-title">CV & Portfolio Builder</h3><p class="preview-panel-desc">Bangun profil lengkap dengan Identitas, Pendidikan, Pengalaman, dan Portfolio. Tampil otomatis sebagai halaman web publik premium.</p></div>
    <div class="preview-panel"><div class="preview-panel-icon"><i class="fa-solid fa-crown"></i></div><h3 class="preview-panel-title">RBAC God Mode</h3><p class="preview-panel-desc">SuperAdmin dapat mengelola semua user, melihat data workspace, dan mengontrol akses dengan sistem peran yang ketat.</p></div>
</div>
<footer class="pub-footer">
    <span class="pub-footer-copy">&copy; <?= date('Y') ?> Alfatih Digital Workspace. All rights reserved.</span>
    <a href="index.php?page=login" class="pub-footer-link">Login &rarr;</a>
</footer>
<?php } ?>
<script>if('serviceWorker' in navigator){navigator.serviceWorker.register('sw.js').catch(()=>{});}</script>
</body>
</html>