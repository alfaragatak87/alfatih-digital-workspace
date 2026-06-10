<?php if (!defined('SITE_URL')) exit; // Proteksi akses langsung ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($name) ?><?= $profesi?' — '.h($profesi):'' ?></title>
    <meta name="theme-color" content="#fafafa">
    <meta name="description" content="<?= h(mb_strimwidth($summary?:"Portfolio $name",0,160,'...')) ?>">
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/svg+xml" href="assets/images/LOGO_GAWE.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <link rel="stylesheet" href="assets/css/portfolio.css">
</head>
<body>
<div class="pf-progress"><div class="pf-progress-bar"></div></div>
<nav class="pf-nav">
    <div class="pf-nav-name"><?= h($name) ?></div>
    <div class="pf-nav-actions">
        <button class="pf-nav-btn" onclick="copyPortfolioLink()"><i class="fa-solid fa-copy"></i> Salin Link</button>
        <a href="index.php" class="pf-nav-btn dark"><i class="fa-solid fa-arrow-left"></i> Beranda</a>
    </div>
</nav>
<section class="pf-hero">
    <div class="pf-hero-left">
        <?php if($profesi){?><div class="pf-hero-eyebrow"><?= h($profesi) ?></div><?php }?>
        <h1 class="pf-hero-name"><?= h($name) ?></h1>
        <?php if($tagline){?><div class="pf-hero-profesi"><?= h($tagline) ?></div><?php }?>
        <?php if($summary){?><p class="pf-hero-summary"><?= h($summary) ?></p><?php }?>
        <?php if($github||$linkedin||$insta||($ident['website']??'')){?>
        <div class="pf-hero-socials">
            <?php if($github){?><a href="<?= h($github) ?>" target="_blank" class="pf-social-btn"><i class="fa-brands fa-github"></i> GitHub</a><?php }?>
            <?php if($linkedin){?><a href="<?= h($linkedin) ?>" target="_blank" class="pf-social-btn"><i class="fa-brands fa-linkedin"></i> LinkedIn</a><?php }?>
            <?php if($insta){?><a href="<?= h($insta) ?>" target="_blank" class="pf-social-btn"><i class="fa-brands fa-instagram"></i> Instagram</a><?php }?>
            <?php if(!empty($ident['website'])){?><a href="<?= h($ident['website']) ?>" target="_blank" class="pf-social-btn"><i class="fa-solid fa-globe"></i> Website</a><?php }?>
        </div>
        <?php }?>
        <div class="pf-hero-stats">
            <?php if(!empty($edu)){?><div class="pf-stat"><div class="pf-stat-num"><?= count($edu) ?></div><div class="pf-stat-lbl">Pendidikan</div></div><?php }?>
            <?php if(!empty($exp)){?><div class="pf-stat"><div class="pf-stat-num"><?= count($exp) ?></div><div class="pf-stat-lbl">Pengalaman</div></div><?php }?>
            <?php if(!empty($skills)){?><div class="pf-stat"><div class="pf-stat-num"><?= count($skills) ?></div><div class="pf-stat-lbl">Keahlian</div></div><?php }?>
            <?php if(!empty($porto)){?><div class="pf-stat"><div class="pf-stat-num"><?= count($porto) ?></div><div class="pf-stat-lbl">Proyek</div></div><?php }?>
        </div>
    </div>
    <div class="pf-hero-right">
        <img src="<?= h($pFoto) ?>" alt="<?= h($name) ?>" class="pf-hero-img" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($name) ?>&background=1a1a1a&color=ffffff&bold=true&size=400'">
        <div class="pf-hero-img-overlay"></div>
    </div>
</section>
<?php if(!empty($exp)){?>
<section class="pf-section"><div class="pf-section-inner">
    <div class="pf-section-head"><span class="pf-section-label">Karir</span><h2 class="pf-section-title">Pengalaman</h2></div>
    <div class="timeline"><?php foreach($exp as $e){?>
    <div class="tl-item"><div class="tl-period"><?= h($e['periode']??'') ?></div><div class="tl-content"><div class="tl-title"><?= h($e['jabatan']??'') ?></div><div class="tl-sub"><?= h($e['perusahaan']??'') ?></div><?php if(!empty($e['deskripsi'])){?><p class="tl-desc"><?= h($e['deskripsi']) ?></p><?php }?></div></div>
    <?php }?></div>
</div></section>
<?php }?>
<?php if(!empty($edu)){?>
<section class="pf-section"><div class="pf-section-inner">
    <div class="pf-section-head"><span class="pf-section-label">Riwayat</span><h2 class="pf-section-title">Pendidikan</h2></div>
    <div class="timeline"><?php foreach($edu as $e){?>
    <div class="tl-item"><div class="tl-period"><?= h(($e['tahun_mulai']??'').' — '.($e['tahun_selesai']??'Sekarang')) ?></div><div class="tl-content"><div class="tl-title"><?= h($e['institusi']??'') ?></div><div class="tl-sub"><?= h($e['gelar']??'') ?><?= !empty($e['bidang'])?' · '.h($e['bidang']):'' ?></div><?php if(!empty($e['deskripsi'])){?><p class="tl-desc"><?= h($e['deskripsi']) ?></p><?php }?></div></div>
    <?php }?></div>
</div></section>
<?php }?>
<?php if(!empty($skills)){?>
<section class="pf-section"><div class="pf-section-inner">
    <div class="pf-section-head"><span class="pf-section-label">Kompetensi</span><h2 class="pf-section-title">Keahlian</h2></div>
    <table class="skills-table"><?php foreach($skills as $sk){?>
    <tr><td class="sk-name"><?= h($sk['nama']??'') ?></td><td class="sk-cat"><?= h($sk['kategori']??'') ?></td><td class="sk-bar-wrap"><div class="sk-bar"><div class="sk-bar-fill" style="--w:<?= (int)($sk['level']??70) ?>%;"></div></div></td><td class="sk-pct"><?= (int)($sk['level']??70) ?>%</td></tr>
    <?php }?></table>
</div></section>
<?php }?>
<?php if(!empty($porto)){?>
<section class="pf-section"><div class="pf-section-inner">
    <div class="pf-section-head"><span class="pf-section-label">Karya</span><h2 class="pf-section-title">Portfolio</h2></div>
    <div class="porto-grid"><?php foreach($porto as $i=>$p){?>
    <div class="porto-item"><div class="porto-num"><?= str_pad($i+1,2,'0',STR_PAD_LEFT) ?></div><h3 class="porto-name"><?= h($p['nama']??'') ?></h3><?php if(!empty($p['deskripsi'])){?><p class="porto-desc"><?= h($p['deskripsi']) ?></p><?php }?><?php if(!empty($p['tech'])){?><div class="porto-tags"><?php foreach(explode(',',$p['tech']) as $t){?><span class="porto-tag"><?= h(trim($t)) ?></span><?php }?></div><?php }?><?php if(!empty($p['url'])){?><a href="<?= h($p['url']) ?>" target="_blank" class="porto-link">Lihat Proyek <i class="fa-solid fa-arrow-right" style="font-size:.6em;"></i></a><?php }?></div>
    <?php }?></div>
</div></section>
<?php }?>
<?php if($email||$phone||$loc||$github){?>
<section class="pf-section"><div class="pf-section-inner">
    <div class="pf-section-head"><span class="pf-section-label">Hubungi</span><h2 class="pf-section-title">Kontak</h2></div>
    <table class="contact-table">
        <?php if($email){?><tr><td class="contact-label">Email</td><td><a href="mailto:<?= h($email) ?>"><?= h($email) ?></a></td></tr><?php }?>
        <?php if($phone){?><tr><td class="contact-label">Telepon</td><td><a href="tel:<?= h($phone) ?>"><?= h($phone) ?></a></td></tr><?php }?>
        <?php if($loc){?><tr><td class="contact-label">Lokasi</td><td><?= h($loc) ?></td></tr><?php }?>
        <?php if($github){?><tr><td class="contact-label">GitHub</td><td><a href="<?= h($github) ?>" target="_blank"><?= h($github) ?></a></td></tr><?php }?>
    </table>
</div></section>
<?php }?>
<footer class="pf-footer">
    <span class="pf-footer-copy">Portfolio <?= h($name) ?> &mdash; Alfatih Digital Workspace</span>
    <a href="index.php" class="pf-footer-back">Kembali ke Beranda &rarr;</a>
</footer>
<div id="pf-toast">&checkmark; Link disalin!</div>

<script>
// Scroll Reveal & Skill Bar Animation
const pfProgressBar = document.querySelector('.pf-progress-bar');
if (pfProgressBar) {
  document.addEventListener('scroll', () => {
    const scrolled = window.scrollY;
    const total    = document.documentElement.scrollHeight - window.innerHeight;
    pfProgressBar.style.width = Math.min(100, (scrolled / total) * 100) + '%';
  }, { passive: true });
}

const pfReveal = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('revealed');
      pfReveal.unobserve(e.target);
    }
  });
}, { threshold: 0.08, rootMargin: '0px 0px -48px 0px' });

document.querySelectorAll(
  '.tl-item, .porto-item, .pf-section-head, .pf-stat, ' +
  '.skills-table tr, .contact-table tr, .pf-hero-eyebrow'
).forEach((el, i) => {
  el.classList.add('pf-reveal');
  const siblings = Array.from(el.parentNode.children).filter(c => c.classList.contains('pf-reveal'));
  const idx = siblings.indexOf(el);
  el.style.transitionDelay = Math.min(idx * 0.06, 0.36) + 's';
  pfReveal.observe(el);
});

const skillObs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      const fill = e.target;
      const w    = getComputedStyle(fill).getPropertyValue('--w').trim();
      fill.style.width = '0';
      requestAnimationFrame(() => {
        setTimeout(() => {
          fill.style.transition = 'width 1.1s cubic-bezier(.16,1,.3,1)';
          fill.style.width = w;
        }, 50);
      });
      skillObs.unobserve(fill);
    }
  });
}, { threshold: 0.4 });

document.querySelectorAll('.sk-bar-fill').forEach(el => skillObs.observe(el));

function copyPortfolioLink() {
  navigator.clipboard.writeText(document.querySelector('.pf-nav-name')?.dataset?.url || window.location.href)
    .then(() => {
      const t = document.getElementById('pf-toast');
      t.classList.add('show');
      setTimeout(() => t.classList.remove('show'), 2800);
    });
}
document.querySelectorAll('[onclick="copyPortfolioLink()"]').forEach(btn => {
  const nav = document.querySelector('.pf-nav-name');
  if (nav) nav.dataset.url = window.location.href;
});

const heroImg = document.querySelector('.pf-hero-img');
if (heroImg) {
  heroImg.addEventListener('load', () => { heroImg.style.animationPlayState = 'running'; });
}
const heroName = document.querySelector('.pf-hero-name');
if (heroName) heroName.style.borderRight = 'none';
</script>
</body>
</html>