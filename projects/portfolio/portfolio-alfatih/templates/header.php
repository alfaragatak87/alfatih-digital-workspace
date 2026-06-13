<?php
// Pastikan session sudah dimulai, biasanya di file konfigurasi utama
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/helpers.php';

// Default values jika koneksi DB gagal atau pengaturan tidak ada
$themeColor = '#00e5ff';
$enableParticles = true;
$settings = [];

// Ambil pengaturan dari database dengan aman
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT kunci, nilai FROM pengaturan WHERE kunci IN ('theme_color', 'enable_particles')");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $themeColor = $settings['theme_color'] ?? '#00e5ff';
        $enableParticles = isset($settings['enable_particles']) ? (bool)$settings['enable_particles'] : true;
    } catch (Exception $e) {
        // Abaikan error, gunakan nilai default. Bisa ditambahkan logging jika perlu.
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?? 'id' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' . OWNER_NAME : OWNER_NAME . ' - Portfolio' ?></title>
    <meta name="description" content="Portfolio profesional <?= OWNER_NAME ?>, seorang Web Developer.">

    <link rel="icon" href="<?= BASE_URL ?>/assets/img/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css"> <style>
        :root {
            --primary-color: <?= htmlspecialchars($themeColor) ?>;
            --primary-dark: <?= htmlspecialchars(adjustBrightness($themeColor, -20)) ?>;
            --header-height: 70px;
            
            /* Light Theme */
            --bg-color-light: rgba(245, 247, 250, 0.8);
            --text-color-light: #2c3e50;
            --shadow-light: 0 4px 20px rgba(0, 0, 0, 0.05);

            /* Dark Theme */
            --bg-color-dark: rgba(10, 25, 47, 0.75);
            --text-color-dark: #e0e6f1;
            --shadow-dark: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        /* Terapkan warna berdasarkan tema */
        body {
            --bg-color: var(--bg-color-light);
            --text-color: var(--text-color-light);
            --header-shadow: var(--shadow-light);
            background-color: #f0f2f5;
            color: var(--text-color);
        }

        body.dark-mode {
            --bg-color: var(--bg-color-dark);
            --text-color: var(--text-color-dark);
            --header-shadow: var(--shadow-dark);
            background-color: #0a192f;
            color: var(--text-color);
        }

        /* Main Header Styling */
        .main-header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: var(--header-height);
            z-index: 1000;
            background-color: var(--bg-color);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: var(--header-shadow);
            transition: top 0.3s ease-in-out, background-color 0.3s, box-shadow 0.3s;
        }

        .main-header.header-hidden {
            top: -100px;
        }

        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            height: 100%;
        }

        .logo a {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
            transition: transform 0.3s ease;
        }
        .logo a:hover {
            transform: scale(1.05);
        }

        .nav-menu-container {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        .nav-menu {
            display: flex;
            align-items: center;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 1.5rem;
        }

        .nav-menu .nav-link, .dropdown-toggle {
            color: var(--text-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 1rem;
            padding: 0.5rem 0;
            position: relative;
            transition: color 0.3s;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0%;
            height: 2px;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--primary-color);
            transition: width 0.3s ease-in-out;
        }

        .nav-link:hover, .nav-link.active, .dropdown:hover .dropdown-toggle {
            color: var(--primary-color);
        }

        .nav-link:hover::after, .nav-link.active::after {
            width: 100%;
        }

        /* Dropdown Styling */
        .dropdown {
            position: relative;
        }
        .dropdown-toggle {
            cursor: pointer;
        }
        .dropdown-toggle i {
            margin-left: 0.3rem;
            font-size: 0.8rem;
            transition: transform 0.3s;
        }
        .dropdown:hover .dropdown-toggle i {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            position: absolute;
            top: 150%;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--bg-color);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            list-style: none;
            padding: 0.5rem 0;
            margin: 0;
            border-radius: 8px;
            box-shadow: var(--header-shadow);
            min-width: 180px;
            opacity: 0;
            visibility: hidden;
            transition: top 0.3s ease, opacity 0.3s ease, visibility 0.3s;
            pointer-events: none;
        }
        .dropdown:hover .dropdown-menu {
            top: 100%;
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .dropdown-menu a {
            display: block;
            padding: 0.75rem 1.5rem;
            color: var(--text-color);
            text-decoration: none;
            transition: background-color 0.2s, color 0.2s;
        }
        .dropdown-menu a:hover, .dropdown-menu a.active {
            background-color: rgba(124, 77, 255, 0.1);
            color: var(--primary-color);
        }

        /* Theme Toggle & Mobile Menu Toggle */
        .theme-toggle, .mobile-menu-toggle {
            background: none;
            border: none;
            color: var(--text-color);
            font-size: 1.2rem;
            cursor: pointer;
            transition: color 0.3s, transform 0.3s;
        }
        .theme-toggle:hover, .mobile-menu-toggle:hover {
            color: var(--primary-color);
            transform: scale(1.1);
        }
        .mobile-menu-toggle {
            display: none; /* Sembunyikan di desktop */
        }

        /* Mobile Responsive */
        @media (max-width: 992px) {
            .mobile-menu-toggle {
                display: block;
            }
            .nav-menu {
                position: fixed;
                top: var(--header-height);
                right: -100%;
                width: 70%;
                max-width: 320px;
                height: calc(100vh - var(--header-height));
                background-color: var(--bg-color);
                flex-direction: column;
                align-items: flex-start;
                padding: 2rem;
                gap: 0;
                box-shadow: -5px 0 15px rgba(0,0,0,0.1);
                transition: right 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }
            .nav-menu.active {
                right: 0;
            }
            .nav-menu li {
                width: 100%;
            }
            .nav-menu .nav-link, .dropdown-toggle {
                display: block;
                padding: 1rem 0;
                font-size: 1.1rem;
            }
            .nav-link::after {
                left: 0;
                transform: translateX(0);
            }
            .dropdown-menu {
                position: static;
                transform: none;
                box-shadow: none;
                opacity: 1;
                visibility: visible;
                background: transparent;
                padding: 0 0 0 1rem;
                border-radius: 0;
                display: none; /* Sembunyikan by default */
                min-width: unset;
                transition: none;
            }
            .dropdown.active .dropdown-menu {
                display: block; /* Tampilkan saat dropdown di-tap */
            }
        }
    </style>
</head>
<body class="theme-default">
    <?php if ($enableParticles): ?>
        <div id="particles-js"></div>
    <?php endif; ?>

    <button id="backToTop" title="Back to Top"><i class="fas fa-arrow-up"></i></button>

    <header class="main-header">
        <nav class="navbar-container">
            <div class="logo">
                <a href="<?= BASE_URL ?>/pages/index.php"><?= OWNER_NAME ?></a>
            </div>

            <div class="nav-menu-container">
                <ul class="nav-menu" id="nav-menu">
                    <li><a href="<?= BASE_URL ?>/pages/index.php" class="nav-link <?= isCurrentPage('index.php') ?>">Home</a></li>
                    <li><a href="<?= BASE_URL ?>/pages/about.php" class="nav-link <?= isCurrentPage('about.php') ?>">About</a></li>
                    
                    <li class="dropdown">
                        <a class="dropdown-toggle">Karya <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= BASE_URL ?>/pages/projects.php" class="nav-link <?= isCurrentPage('projects.php') ?>">Projects</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/cv.php" class="nav-link <?= isCurrentPage('cv.php') ?>">CV</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/semester.php" class="nav-link <?= isCurrentPage('semester.php') ?>">Semester</a></li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a class="dropdown-toggle">Lainnya <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                             <li><a href="<?= BASE_URL ?>/pages/blog.php" class="nav-link <?= isCurrentPage('blog.php') ?>">Blog</a></li>
                            <li><a href="<?= BASE_URL ?>/pages/testimonials.php" class="nav-link <?= isCurrentPage('testimonials.php') ?>">Testimonials</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="<?= BASE_URL ?>/pages/contact.php" class="nav-link <?= isCurrentPage('contact.php') ?>">Contact</a></li>
                    
                    <?php if (isset($_SESSION['admin_id'])): ?>
                    <li><a href="<?= BASE_URL ?>/admin/dashboard.php" class="nav-link admin-btn">Admin Panel</a></li>
                    <?php else: ?>
                    <li><a href="<?= BASE_URL ?>/admin/login.php" class="nav-link login-btn">Login</a></li>
                    <?php endif; ?>
                </ul>

                <button id="theme-toggle" class="theme-toggle" title="Ganti Tema">
                    <i class="fas fa-moon"></i>
                </button>
                <button class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </nav>
    </header>

    <?php displayAlert(); ?>

    <main style="padding-top: var(--header-height);"> <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;
            const header = document.querySelector('.main-header');
            const themeToggleButton = document.getElementById('theme-toggle');
            const themeIcon = themeToggleButton.querySelector('i');
            const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
            const navMenu = document.getElementById('nav-menu');
            const mobileMenuIcon = mobileMenuToggle.querySelector('i');

            // 1. FUNGSI DARK/LIGHT MODE
            const currentTheme = localStorage.getItem('theme');
            if (currentTheme === 'dark') {
                body.classList.add('dark-mode');
                themeIcon.classList.replace('fa-moon', 'fa-sun');
            }

            themeToggleButton.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                let theme = 'light';
                if (body.classList.contains('dark-mode')) {
                    theme = 'dark';
                    themeIcon.classList.replace('fa-moon', 'fa-sun');
                } else {
                    themeIcon.classList.replace('fa-sun', 'fa-moon');
                }
                localStorage.setItem('theme', theme);
            });

            // 2. FUNGSI HIDE/SHOW HEADER ON SCROLL
            let lastScrollTop = 0;
            window.addEventListener('scroll', function() {
                let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                if (scrollTop > lastScrollTop && scrollTop > header.offsetHeight) {
                    // Scroll Down
                    header.classList.add('header-hidden');
                } else {
                    // Scroll Up
                    header.classList.remove('header-hidden');
                }
                lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; 
            }, false);

            // 3. FUNGSI MOBILE MENU
            mobileMenuToggle.addEventListener('click', () => {
                navMenu.classList.toggle('active');
                if (navMenu.classList.contains('active')) {
                    mobileMenuIcon.classList.replace('fa-bars', 'fa-times');
                } else {
                    mobileMenuIcon.classList.replace('fa-times', 'fa-bars');
                }
            });

            // Tambahkan fungsionalitas dropdown untuk mobile
            const dropdowns = document.querySelectorAll('.nav-menu .dropdown');
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                toggle.addEventListener('click', (e) => {
                    // Cek jika dalam mode mobile
                    if (window.innerWidth <= 992) {
                        e.preventDefault(); // Mencegah link default
                        const parentLi = dropdown;
                        parentLi.classList.toggle('active'); // toggle class active di <li>
                    }
                });
            });

        });
    </script>