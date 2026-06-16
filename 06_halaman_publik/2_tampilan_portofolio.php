<?php
// +------------------------------------------------------------------------------+
// |  FILE: 06_halaman_publik/2_tampilan_portofolio.php                           |
// |                                                                              |
// |  DESKRIPSI:                                                                  |
// |  Menampilkan halaman portofolio publik pengguna.                             |
// |                                                                              |
// +------------------------------------------------------------------------------+
if (!defined('SITE_URL')) exit; // Proteksi akses langsung

// +------------------------------------------------------------------------------+
// ¦  FILE: tampilan/halaman/halaman_portofolio.php                               ¦
// ¦                                                                              ¦
// ¦  DESKRIPSI:                                                                  ¦
// ¦  Halaman Portofolio Publik yang mempresentasikan data profil user ke dalam   ¦
// ¦  bentuk Resume online siap-bagikan. Tersedia tombol Cetak/Unduh PDF.         ¦
// ¦                                                                              ¦
// ¦  KONEKSI & RELASI:                                                           ¦
// ¦  - Di-trigger lewat URL index.php?portfolio=username.                      ¦
// ¦  - Mengurai (Decode) string JSON dari kolom profile_data database.         ¦
// ¦                                                                              ¦
// ¦  BARIS KODE PENTING:                                                         ¦
// ¦  - $pd = json_decode(['profile_data'], true) : Logika krusial untuk  ¦
// ¦    mengubah JSON string mentah menjadi struktur array multi-dimensi.         ¦
// +------------------------------------------------------------------------------+

$ident  = $pd['identitas'] ?? [];
$edu    = $pd['pendidikan'] ?? [];
$exp    = $pd['pengalaman'] ?? [];
$skills = $pd['keahlian'] ?? [];
$porto  = $pd['portfolio'] ?? [];

$name    = !empty($ident['nama_lengkap']) ? $ident['nama_lengkap'] : ($puser['nama_lengkap'] ?? $puser['username']);
$profesi = $ident['profesi'] ?? '';
$tagline = $ident['tagline'] ?? '';

// Handle hierarki alamat
$provinsi  = $ident['provinsi'] ?? '';
$kabupaten = $ident['kabupaten'] ?? '';
$kecamatan = $ident['kecamatan'] ?? '';
$desa      = $ident['desa'] ?? '';
$loc_parts = array_filter([$desa, $kecamatan, $kabupaten, $provinsi]);
$loc       = !empty($loc_parts) ? implode(', ', $loc_parts) : '';

$email    = $ident['email'] ?? '';
$phone    = $ident['phone'] ?? '';
$github   = $ident['github'] ?? '';
$linkedin = $ident['linkedin'] ?? '';
$insta    = $ident['instagram'] ?? '';
$website  = $ident['website'] ?? '';
$summary  = $ident['summary'] ?? '';
$portfolioLink = SITE_URL . '/index.php?portfolio=' . urlencode($puser['username']);

// Fallback Photo
$pFoto = !empty($puser['foto_profil']) && $puser['foto_profil'] !== 'default.png' 
    ? PROFILE_IMG_DIR . $puser['foto_profil'] 
    : 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=1e293b&color=8b5cf6&bold=true&size=400';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($name) ?><?= $profesi ? ' â€” ' . h($profesi) : '' ?></title>
    <meta name="theme-color" content="#0B0C10">
    <meta name="description" content="<?= h(mb_strimwidth($summary ?: "Portfolio $name", 0, 160, '...')) ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        darkBase: '#0B0C10',
                        darkCard: 'rgba(30, 41, 59, 0.3)',
                        primary: '#8b5cf6',
                        secondary: '#10b981',
                        accent: '#3b82f6'
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        heading: ['"Outfit"', 'sans-serif'],
                    },
                    backgroundImage: {
                        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                    }
                }
            }
        }
    </script>

    <style>
        /* Custom Utilities & Glassmorphism */
        body {
            background-color: #0B0C10;
            color: #f8fafc;
            overflow-x: hidden;
        }

        .glass-panel {
            background: var(--darkCard, rgba(30, 41, 59, 0.4));
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .glass-panel:hover {
            border-color: rgba(139, 92, 246, 0.3);
            box-shadow: 0 10px 40px rgba(139, 92, 246, 0.15);
        }

        .glass-nav {
            background: rgba(11, 12, 16, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Gradient Text */
        .text-gradient {
            background: linear-gradient(to right, #a855f7, #3b82f6, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .text-gradient-primary {
            background: linear-gradient(to right, #fff, #c4b5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Animated Blobs (Glows) */
        .blob {
            position: absolute;
            filter: blur(90px);
            z-index: -1;
            opacity: 0.5;
            animation: float 10s infinite alternate;
        }
        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(20px, -30px) scale(1.1); }
            100% { transform: translate(-20px, 20px) scale(0.9); }
        }

        /* Timeline Styles */
        .timeline-line {
            position: absolute;
            left: 1rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, #8b5cf6, transparent);
            z-index: 0;
        }
        @media (min-width: 768px) {
            .timeline-line { left: 50%; transform: translateX(-50%); }
            .timeline-item:nth-child(odd) { justify-content: flex-start; text-align: right; }
            .timeline-item:nth-child(even) { justify-content: flex-end; text-align: left; }
            .timeline-item:nth-child(odd) .timeline-content { margin-right: 3rem; }
            .timeline-item:nth-child(even) .timeline-content { margin-left: 3rem; }
            .timeline-dot { left: 50% !important; transform: translate(-50%, 0) !important; }
        }

        /* Skill Bar Animation */
        .skill-bar-fill {
            transition: width 1.5s cubic-bezier(0.22, 1, 0.36, 1);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0B0C10; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #8b5cf6; }

        /* Progress Bar */
        #progress-bar {
            position: fixed; top: 0; left: 0; height: 3px;
            background: linear-gradient(to right, #8b5cf6, #10b981);
            z-index: 9999; transition: width 0.1s;
        }
    </style>
</head>
<body class="antialiased font-sans relative">

    <!-- Scroll Progress -->
    <div id="progress-bar" style="width: 0%;"></div>

    <!-- tsParticles Background -->
    <div id="tsparticles" class="fixed inset-0 z-[-1] pointer-events-none opacity-50 mix-blend-screen"></div>

    <!-- Background Animated Blobs -->
    <div class="fixed inset-0 z-[-2] overflow-hidden pointer-events-none">
        <div class="blob bg-primary w-96 h-96 rounded-full top-[-10%] left-[-10%] mix-blend-screen"></div>
        <div class="blob bg-accent w-80 h-80 rounded-full top-[30%] right-[-5%] mix-blend-screen" style="animation-delay: 2s;"></div>
        <div class="blob bg-secondary w-96 h-96 rounded-full bottom-[-10%] left-[20%] mix-blend-screen" style="animation-delay: 4s;"></div>
    </div>

    <!-- Navbar -->
    <nav class="glass-nav fixed w-full top-0 z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0">
                    <span class="font-heading font-bold text-2xl text-gradient-primary tracking-wide">
                        <?= h($name) ?>
                    </span>
                </div>
                <div class="flex gap-3">
                    <button onclick="copyPortfolioLink()" class="px-4 py-2 rounded-full border border-slate-700 bg-slate-800/50 hover:bg-slate-700 hover:border-slate-500 transition-all text-sm font-medium flex items-center gap-2 group">
                        <i class="fa-solid fa-link text-slate-400 group-hover:text-white transition-colors"></i>
                        <span class="hidden sm:inline">Salin Link</span>
                    </button>
                    <a href="index.php" class="px-5 py-2 rounded-full bg-primary hover:bg-violet-600 shadow-[0_0_15px_rgba(139,92,246,0.4)] transition-all text-sm font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span class="hidden sm:inline">Beranda</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative w-full">
            <div class="flex flex-col-reverse lg:flex-row items-center gap-12 lg:gap-20">
                
                <!-- Hero Text -->
                <div class="flex-1 text-center lg:text-left" data-aos="fade-up" data-aos-duration="1000">
                    <?php if($profesi){ ?>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-violet-500/30 bg-violet-500/10 text-violet-400 text-sm font-semibold mb-6">
                            <span class="w-2 h-2 rounded-full bg-violet-500 animate-pulse"></span>
                            <?= h($profesi) ?>
                        </div>
                    <?php } ?>
                    
                    <h1 class="font-heading text-5xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight mb-6 leading-tight">
                        Hello, I'm <br>
                        <span class="text-gradient"><?= h($name) ?></span>
                    </h1>
                    
                    <?php if($tagline){ ?>
                        <p class="text-xl sm:text-2xl text-slate-300 font-medium mb-4"><?= h($tagline) ?></p>
                    <?php } ?>
                    
                    <?php if($summary){ ?>
                        <p class="text-slate-400 text-lg max-w-2xl mx-auto lg:mx-0 mb-8 leading-relaxed">
                            <?= h($summary) ?>
                        </p>
                    <?php } ?>

                    <!-- Contact Info -->
                    <div class="flex flex-wrap justify-center lg:justify-start gap-3 mb-6">
                        <?php 
                        $contacts = [
                            ['url' => $email ? "mailto:$email" : '', 'icon' => 'fa-solid fa-envelope', 'color' => 'hover:border-red-500 hover:text-red-400', 'text' => $email],
                            ['url' => $phone ? "tel:$phone" : '', 'icon' => 'fa-brands fa-whatsapp', 'color' => 'hover:border-[#25D366] hover:text-[#25D366]', 'text' => $phone],
                            ['url' => $loc ? 'javascript:void(0)' : '', 'icon' => 'fa-solid fa-location-dot', 'color' => 'hover:border-primary hover:text-primary', 'text' => $loc, 'no_blank' => true],
                        ];
                        foreach($contacts as $c) { 
                            if($c['url']) { 
                                $is_link = ($c['url'] !== 'javascript:void(0)');
                                $target = (empty($c['no_blank']) && $is_link && strpos($c['url'], 'mailto:') === false && strpos($c['url'], 'tel:') === false) ? 'target="_blank"' : '';
                        ?>
                                <a href="<?= h($c['url']) ?>" <?= $target ?> class="px-4 py-2 rounded-full glass-panel flex items-center gap-2 text-slate-300 transition-all duration-300 <?= $c['color'] ?> text-sm font-medium hover:-translate-y-1 hover:bg-white/5 border border-transparent">
                                    <i class="<?= $c['icon'] ?> text-lg"></i>
                                    <span><?= h($c['text']) ?></span>
                                </a>
                        <?php } } ?>
                    </div>

                    <!-- Social Links -->
                    <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                        <?php 
                        $socials = [
                            ['url' => $github, 'icon' => 'fa-brands fa-github', 'color' => 'hover:bg-[#333] hover:border-[#333]'],
                            ['url' => $linkedin, 'icon' => 'fa-brands fa-linkedin-in', 'color' => 'hover:bg-[#0077b5] hover:border-[#0077b5]'],
                            ['url' => $insta, 'icon' => 'fa-brands fa-instagram', 'color' => 'hover:bg-[#e1306c] hover:border-[#e1306c]'],
                            ['url' => $website, 'icon' => 'fa-solid fa-globe', 'color' => 'hover:bg-primary hover:border-primary'],
                        ];
                        foreach($socials as $s) { 
                            if($s['url']) { 
                        ?>
                                <a href="<?= h($s['url']) ?>" target="_blank" class="w-12 h-12 rounded-full glass-panel flex items-center justify-center text-slate-400 transition-all duration-300 hover:text-white <?= $s['color'] ?> hover:shadow-[0_0_20px_rgba(255,255,255,0.2)] hover:-translate-y-1">
                                    <i class="<?= $s['icon'] ?> text-xl"></i>
                                </a>
                        <?php } } ?>
                    </div>
                </div>

                <!-- Hero Image -->
                <div class="flex-1 flex justify-center lg:justify-end w-full max-w-sm lg:max-w-none" data-aos="zoom-in" data-aos-duration="1200">
                    <div class="relative group">
                        <!-- Glow effect behind image -->
                        <div class="absolute -inset-2 bg-gradient-to-r from-primary to-accent rounded-3xl blur-2xl opacity-40 group-hover:opacity-70 transition duration-700 animate-pulse"></div>
                        <img src="<?= h($pFoto) ?>" alt="<?= h($name) ?>" class="relative w-64 h-64 sm:w-80 sm:h-80 lg:w-[26rem] lg:h-[26rem] object-cover rounded-3xl border border-white/10 shadow-2xl transition-transform duration-700 group-hover:scale-[1.02] group-hover:-rotate-2">
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Education Section (Moved Above Experience) -->
    <?php if(!empty($edu)){ ?>
    <section class="py-24 relative z-10 border-t border-white/5 bg-slate-900/20">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-accent text-sm font-bold uppercase tracking-wider mb-2 block">Riwayat Akademis</span>
                <h2 class="font-heading text-4xl font-bold text-white">Pendidikan</h2>
            </div>

            <div class="relative">
                <div class="timeline-line hidden md:block" style="background: linear-gradient(to bottom, #3b82f6, transparent);"></div>
                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-accent to-transparent md:hidden z-0"></div>

                <div class="space-y-12">
                    <?php foreach($edu as $i => $e){ 
                        $t_mulai = h($e['tahun_mulai'] ?? '');
                        $t_selesai = (!empty($e['is_current']) && $e['is_current'] == 1) ? 'Saat Ini' : h($e['tahun_selesai'] ?? '');
                    ?>
                    <div class="timeline-item relative flex w-full" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                        <div class="timeline-dot absolute left-6 md:left-1/2 -translate-x-1/2 w-4 h-4 rounded-full bg-darkBase border-2 border-accent shadow-[0_0_10px_rgba(59,130,246,0.6)] z-10 transition-transform duration-300 hover:scale-150 hover:bg-accent"></div>
                        
                        <div class="timeline-content w-full md:w-[45%] pl-14 md:pl-0">
                            <div class="glass-panel p-6 sm:p-8 rounded-2xl transition-all duration-300 group hover:border-accent/40 hover:shadow-[0_10px_40px_rgba(59,130,246,0.1)]">
                                <div class="inline-block px-3 py-1 rounded-full bg-accent/10 text-accent text-xs font-bold mb-4 border border-accent/20">
                                    <?= $t_mulai . ' â€” ' . $t_selesai ?>
                                </div>
                                <h3 class="text-2xl font-bold text-white mb-1 group-hover:text-accent transition-colors"><?= h($e['institusi'] ?? '') ?></h3>
                                <h4 class="text-slate-400 font-medium mb-3">
                                    <?= h($e['gelar'] ?? '') ?><?= !empty($e['bidang']) ? ' <span class="mx-2 text-slate-600">â€¢</span> ' . h($e['bidang']) : '' ?>
                                </h4>
                                <?php if(!empty($e['ipk_nilai'])){ ?>
                                    <div class="text-sm font-semibold text-accent mb-3 flex items-center gap-2">
                                        <i class="fa-solid fa-star"></i> Nilai/IPK: <?= h($e['ipk_nilai']) ?>
                                    </div>
                                <?php } ?>
                                <?php if(!empty($e['deskripsi'])){ ?>
                                    <p class="text-slate-300 text-sm leading-relaxed"><?= nl2br(h($e['deskripsi'])) ?></p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
    <?php } ?>

    <!-- Experience Section (Moved Below Education) -->
    <?php if(!empty($exp)){ ?>
    <section class="py-24 relative z-10 border-t border-white/5">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-primary text-sm font-bold uppercase tracking-wider mb-2 block">Karir</span>
                <h2 class="font-heading text-4xl font-bold text-white">Pengalaman Kerja</h2>
            </div>

            <div class="relative">
                <div class="timeline-line hidden md:block"></div>
                <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-primary to-transparent md:hidden z-0"></div>

                <div class="space-y-12">
                    <?php foreach($exp as $i => $e){ 
                        $t_mulai = h($e['tahun_mulai'] ?? '');
                        $t_selesai = (!empty($e['is_current']) && $e['is_current'] == 1) ? 'Saat Ini' : h($e['tahun_selesai'] ?? '');
                    ?>
                    <div class="timeline-item relative flex w-full" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                        <!-- Dot -->
                        <div class="timeline-dot absolute left-6 md:left-1/2 -translate-x-1/2 w-4 h-4 rounded-full bg-darkBase border-2 border-primary shadow-[0_0_10px_rgba(139,92,246,0.6)] z-10 transition-transform duration-300 hover:scale-150 hover:bg-primary"></div>
                        
                        <!-- Content Card -->
                        <div class="timeline-content w-full md:w-[45%] pl-14 md:pl-0">
                            <div class="glass-panel p-6 sm:p-8 rounded-2xl transition-all duration-300 group">
                                <div class="inline-block px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold mb-4 border border-primary/20">
                                    <?= $t_mulai . ' â€” ' . $t_selesai ?>
                                </div>
                                <h3 class="text-2xl font-bold text-white mb-1 group-hover:text-primary transition-colors"><?= h($e['jabatan'] ?? '') ?></h3>
                                <h4 class="text-slate-400 font-medium mb-4 flex items-center gap-2">
                                    <i class="fa-solid fa-building text-sm"></i> <?= h($e['perusahaan'] ?? '') ?>
                                </h4>
                                <?php if(!empty($e['deskripsi'])){ ?>
                                    <p class="text-slate-300 text-sm leading-relaxed"><?= nl2br(h($e['deskripsi'])) ?></p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
    <?php } ?>

    <!-- Skills Section -->
    <?php if(!empty($skills)){ ?>
    <section class="py-24 relative z-10 border-t border-white/5 bg-slate-900/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-secondary text-sm font-bold uppercase tracking-wider mb-2 block">Kompetensi</span>
                <h2 class="font-heading text-4xl font-bold text-white">Keahlian & Kemampuan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($skills as $i => $sk){ 
                    $level = (int)($sk['level'] ?? 70);
                    $icon = !empty($sk['logo_icon']) ? h($sk['logo_icon']) : 'fa-solid fa-code';
                ?>
                <div class="glass-panel p-6 rounded-2xl group hover:border-secondary/40 hover:shadow-[0_10px_30px_rgba(16,185,129,0.1)] transition-all" data-aos="zoom-in" data-aos-delay="<?= $i * 50 ?>">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-2xl text-secondary group-hover:scale-110 group-hover:bg-secondary/20 transition-all">
                            <i class="<?= $icon ?>"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-1">
                                <h3 class="font-semibold text-white"><?= h($sk['nama'] ?? '') ?></h3>
                                <span class="text-sm font-medium text-slate-400"><?= $level ?>%</span>
                            </div>
                            <div class="h-2 w-full bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-secondary to-teal-400 rounded-full skill-bar-fill w-0" data-width="<?= $level ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <?php } ?>

    <!-- Portfolio Section -->
    <?php if(!empty($porto)){ ?>
    <section class="py-24 relative z-10 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-primary text-sm font-bold uppercase tracking-wider mb-2 block">Karya</span>
                <h2 class="font-heading text-4xl font-bold text-white">Proyek & Portfolio</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach($porto as $i => $p){ ?>
                <div class="glass-panel p-8 rounded-3xl relative overflow-hidden group hover:-translate-y-2 transition-all duration-300 flex flex-col" data-aos="fade-up" data-aos-delay="<?= $i * 100 ?>">
                    <!-- Large Background Number -->
                    <div class="absolute -top-4 -right-2 text-8xl font-heading font-extrabold text-white/5 group-hover:text-primary/10 transition-colors pointer-events-none z-0">
                        <?= str_pad($i+1, 2, '0', STR_PAD_LEFT) ?>
                    </div>
                    
                    <div class="relative z-10 flex flex-col h-full">
                        <h3 class="text-2xl font-bold text-white mb-3 group-hover:text-primary transition-colors"><?= h($p['nama'] ?? '') ?></h3>
                        <?php if(!empty($p['deskripsi'])){ ?>
                            <p class="text-slate-400 text-sm leading-relaxed mb-6 flex-grow"><?= h($p['deskripsi']) ?></p>
                        <?php } ?>
                        
                        <?php if(!empty($p['tech'])){ ?>
                        <div class="flex flex-wrap gap-2 mb-6">
                            <?php foreach(explode(',', $p['tech']) as $t){ ?>
                                <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs text-slate-300"><?= h(trim($t)) ?></span>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        
                        <?php if(!empty($p['url'])){ ?>
                            <div class="mt-auto">
                                <a href="<?= h($p['url']) ?>" target="_blank" class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:text-white transition-colors group/link">
                                    Lihat Proyek 
                                    <i class="fa-solid fa-arrow-right transform group-hover/link:translate-x-1 transition-transform"></i>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>
    <?php } ?>

    <!-- CTA & Footer -->
    <section class="pt-20 pb-10 relative z-10 border-t border-white/5 bg-slate-900/40 backdrop-blur-md overflow-hidden">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center" data-aos="fade-up">
            <h2 class="text-3xl md:text-5xl font-heading font-extrabold text-white mb-6">Tertarik Bekerja Sama?</h2>
            <p class="text-slate-400 text-lg mb-10 max-w-2xl mx-auto">Saya selalu terbuka untuk mendiskusikan peluang baru, proyek menantang, atau sekadar berbagi ide kreatif. Mari ciptakan sesuatu yang luar biasa bersama!</p>
            
            <div class="flex flex-wrap justify-center gap-4">
                <?php if($email) { ?>
                    <a href="mailto:<?= h($email) ?>" class="inline-flex items-center gap-3 px-8 py-4 rounded-full bg-primary hover:bg-violet-600 text-white font-bold transition-all hover:-translate-y-1 shadow-[0_10px_30px_rgba(139,92,246,0.3)]">
                        <i class="fa-solid fa-paper-plane text-xl"></i> Hubungi via Email
                    </a>
                <?php } ?>
                <?php if($phone) { ?>
                    <a href="tel:<?= h($phone) ?>" class="inline-flex items-center gap-3 px-8 py-4 rounded-full glass-panel hover:border-secondary hover:text-white text-slate-300 font-bold transition-all hover:-translate-y-1 group">
                        <i class="fa-brands fa-whatsapp text-xl text-secondary group-hover:text-white transition-colors"></i> Chat WhatsApp
                    </a>
                <?php } ?>
            </div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 pt-8 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-6">
            <!-- Brand -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-primary to-accent flex items-center justify-center text-white font-heading font-bold text-lg shadow-lg">
                    <?= strtoupper(substr(h($name), 0, 1)) ?>
                </div>
                <span class="text-white font-heading font-bold tracking-wider text-xl"><?= h($name) ?></span>
            </div>

            <!-- Copyright -->
            <p class="text-slate-500 font-medium text-sm text-center">
                &copy; <?= date('Y') ?> Hak Cipta Dilindungi.<br class="md:hidden"> 
                <span class="hidden md:inline mx-2">|</span>
                Didukung oleh <span class="text-primary/80 font-bold">Alfatih Digital Workspace</span>
            </p>
            
            <!-- Back to Top -->
            <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="w-12 h-12 rounded-full glass-panel flex items-center justify-center text-slate-400 hover:text-white hover:bg-primary hover:border-primary transition-all group shadow-lg" aria-label="Kembali ke atas">
                <i class="fa-solid fa-arrow-up group-hover:-translate-y-1 transition-transform"></i>
            </button>
        </div>
    </section>

    <!-- Toast Notification -->
    <div id="pf-toast" class="fixed bottom-8 left-1/2 -translate-x-1/2 translate-y-24 opacity-0 bg-secondary text-white px-6 py-3 rounded-full font-bold shadow-[0_10px_30px_rgba(16,185,129,0.4)] flex items-center gap-3 transition-all duration-500 z-[9999]">
        <i class="fa-solid fa-check-circle text-xl"></i> Link berhasil disalin!
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tsparticles-engine@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/tsparticles-basic@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/tsparticles-interaction-particles-links@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>
    <script>
        // Background tsParticles Configuration
        tsParticles.load("tsparticles", {
            fpsLimit: 60,
            particles: {
                number: { value: 60, density: { enable: true, value_area: 800 } },
                color: { value: ["#8b5cf6", "#3b82f6", "#10b981"] },
                shape: { type: "circle" },
                opacity: { value: 0.6, random: true, anim: { enable: true, speed: 1, opacity_min: 0.1, sync: false } },
                size: { value: 4, random: true, anim: { enable: true, speed: 2, size_min: 0.1, sync: false } },
                links: { enable: true, distance: 150, color: "#8b5cf6", opacity: 0.3, width: 1 },
                move: { enable: true, speed: 1.5, direction: "none", random: true, straight: false, outModes: "out" }
            },
            interactivity: {
                events: { onHover: { enable: true, mode: "grab" }, onClick: { enable: true, mode: "push" } },
                modes: { grab: { distance: 200, links: { opacity: 0.8 } }, push: { particles_nb: 4 } }
            },
            detectRetina: true
        });

        // Initialize AOS Animation Library
        AOS.init({
            once: true,
            offset: 50,
            duration: 800,
            easing: 'ease-out-cubic',
        });

        // Skill Bar Animation Trigger on Scroll
        document.addEventListener('aos:in', ({ detail }) => {
            const bars = detail.querySelectorAll('.skill-bar-fill');
            bars.forEach(bar => {
                setTimeout(() => {
                    bar.style.width = bar.getAttribute('data-width');
                }, 300);
            });
        });

        // For skill bars that are already in view on load
        window.addEventListener('load', () => {
            document.querySelectorAll('.skill-card.aos-animate').forEach(card => {
                const bar = card.querySelector('.skill-bar-fill');
                if(bar) bar.style.width = bar.getAttribute('data-width');
            });
        });

        // Top Progress Bar & Navbar Scroll Effect
        const pfProgressBar = document.getElementById('progress-bar');
        const nav = document.getElementById('navbar');
        
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;
            const total = document.documentElement.scrollHeight - window.innerHeight;
            pfProgressBar.style.width = Math.min(100, (scrolled / total) * 100) + '%';
            
            if(scrolled > 50) {
                nav.classList.add('bg-slate-900/90', 'shadow-lg');
                nav.classList.remove('bg-transparent');
            } else {
                nav.classList.remove('bg-slate-900/90', 'shadow-lg');
                nav.classList.add('bg-transparent');
            }
        });

        // Copy Link function
        function copyPortfolioLink() {
            navigator.clipboard.writeText('<?= h($portfolioLink) ?>').then(() => {
                const toast = document.getElementById('pf-toast');
                toast.classList.remove('translate-y-24', 'opacity-0');
                
                setTimeout(() => {
                    toast.classList.add('translate-y-24', 'opacity-0');
                }, 3000);
            });
        }
    </script>
</body>
</html>