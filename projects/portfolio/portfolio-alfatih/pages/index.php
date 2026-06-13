<?php
$pageTitle = "Beranda";
// Pastikan path ke file konfigurasi dan template sudah benar
// Sesuaikan '../' jika file ini ada di direktori yang berbeda
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../templates/header.php';

// Fallback data jika koneksi database gagal
$profile = ['profile_image' => null, 'summary' => 'Saya adalah seorang mahasiswa Informatika di ITB Widyagama Lumajang yang passionate dalam pengembangan web dan teknologi. Saya senang belajar hal baru dan membangun solusi digital yang bermanfaat.'];
$projects = [];
$testimonials = [];

// Get data dari database dengan aman
if (isset($pdo)) {
    try {
        // Get profile data
        $stmt_profile = $pdo->query("SELECT profile_image, summary FROM profil WHERE id = 1");
        $db_profile = $stmt_profile->fetch(PDO::FETCH_ASSOC);
        if ($db_profile) {
            $profile = $db_profile;
        }

        // Get latest projects
        $stmt_projects = $pdo->query("SELECT * FROM projects WHERE status = 'active' ORDER BY created_at DESC LIMIT 6");
        $projects = $stmt_projects->fetchAll(PDO::FETCH_ASSOC);

        // Get testimonials
        $stmt_testimonials = $pdo->query("SELECT * FROM testimonials WHERE aktif = 1 ORDER BY id DESC LIMIT 8");
        $testimonials = $stmt_testimonials->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Biarkan data fallback yang digunakan jika ada error
        // error_log("Database query failed: " . $e->getMessage()); // Opsional: untuk debugging
    }
}
?>

<style>
/* 🎨 ADVANCED PARTICLE SYSTEM */
.particle-system {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    overflow: hidden;
    pointer-events: none;
}

.particle-canvas {
    position: absolute;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at 20% 80%, rgba(0, 229, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(124, 77, 255, 0.1) 0%, transparent 50%);
}

.floating-particle {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    animation: floatParticle 15s infinite ease-in-out;
}

.floating-particle:nth-child(1) {
    width: 4px; height: 4px;
    background: rgba(0, 229, 255, 0.8);
    left: 10%; top: 20%;
    animation-delay: 0s;
    animation-duration: 12s;
}

.floating-particle:nth-child(2) {
    width: 8px; height: 8px;
    background: rgba(124, 77, 255, 0.6);
    left: 80%; top: 10%;
    animation-delay: 2s;
    animation-duration: 16s;
}

.floating-particle:nth-child(3) {
    width: 6px; height: 6px;
    background: rgba(6, 182, 212, 0.7);
    left: 70%; top: 80%;
    animation-delay: 4s;
    animation-duration: 14s;
}

.floating-particle:nth-child(4) {
    width: 10px; height: 10px;
    background: rgba(0, 229, 255, 0.4);
    left: 30%; top: 70%;
    animation-delay: 6s;
    animation-duration: 18s;
}

.floating-particle:nth-child(5) {
    width: 5px; height: 5px;
    background: rgba(124, 77, 255, 0.8);
    left: 50%; top: 30%;
    animation-delay: 8s;
    animation-duration: 13s;
}

.floating-particle:nth-child(6) {
    width: 12px; height: 12px;
    background: rgba(6, 182, 212, 0.3);
    left: 90%; top: 50%;
    animation-delay: 10s;
    animation-duration: 20s;
}

@keyframes floatParticle {
    0%, 100% {
        transform: translateY(0px) translateX(0px) rotate(0deg) scale(1);
        opacity: 0;
    }
    10% { opacity: 1; }
    20% { transform: translateY(-20px) translateX(10px) rotate(90deg) scale(1.1); }
    40% { transform: translateY(-40px) translateX(-15px) rotate(180deg) scale(0.9); }
    60% { transform: translateY(-30px) translateX(20px) rotate(270deg) scale(1.2); }
    80% { transform: translateY(-60px) translateX(-10px) rotate(360deg) scale(0.8); }
    90% { opacity: 1; }
}

/* 🚀 ULTRA-MODERN HERO SECTION */
.hero-section {
    min-height: 100vh;
    display: flex;
    align-items: center;
    background: none (135deg, 
        rgba(0, 10, 26, 0.95) 0%, 
        rgba(18, 18, 37, 0.9) 50%, 
        rgba(0, 20, 40, 0.85) 100%);
    position: relative;
    overflow: hidden;
    padding-top: 80px; /* Jarak untuk header */
}

.hero-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
    z-index: 10;
}

.hero-text {
    position: relative;
}

.hero-text h1 {
    font-size: 4.5rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, #00e5ff 0%, #7c4dff 50%, #00e5ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    color: transparent; /* Fallback */
    line-height: 1.1;
    position: relative;
    animation: textGlow 3s ease-in-out infinite alternate;
}

@keyframes textGlow {
    0% {
        text-shadow: 0 0 20px rgba(0, 229, 255, 0.3);
    }
    100% {
        text-shadow: 0 0 30px rgba(0, 229, 255, 0.6), 0 0 40px rgba(124, 77, 255, 0.4);
    }
}

.hero-text .subtitle {
    font-size: 1.6rem;
    color: #b0bec5;
    margin-bottom: 1.5rem;
    font-weight: 500;
    opacity: 0;
    animation: fadeInUp 1s ease-out 0.5s forwards;
}

.hero-text .description {
    font-size: 1.2rem;
    color: #90a4ae;
    margin-bottom: 3rem;
    line-height: 1.7;
    opacity: 0;
    animation: fadeInUp 1s ease-out 1s forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hero-cta {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
    opacity: 0;
    animation: fadeInUp 1s ease-out 1.5s forwards;
}

.btn-hero {
    padding: 1.2rem 2.5rem;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: none;
    cursor: pointer;
    font-size: 1.1rem;
    display: inline-flex;
    align-items: center;
    gap: 0.8rem;
    position: relative;
    overflow: hidden;
}

.btn-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.6s;
}

.btn-hero:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, #00e5ff, #7c4dff);
    color: #0a0a1a;
    box-shadow: 0 8px 32px rgba(0, 229, 255, 0.3);
}

.btn-primary:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 15px 40px rgba(0, 229, 255, 0.4);
}

.btn-outline {
    background: rgba(255, 255, 255, 0.1);
    color: #00e5ff;
    border: 2px solid #00e5ff;
    backdrop-filter: blur(10px);
}

.btn-outline:hover {
    background: rgba(0, 229, 255, 0.1);
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 15px 40px rgba(0, 229, 255, 0.2);
}

/* 💎 FOTO PROFIL DINAMIS SEMPURNA */
.hero-visual {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
}

.profile-showcase {
    position: relative;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(30px);
    border-radius: 30px;
    padding: 3rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    text-align: center;
    box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    transition: transform 0.3s ease;
}

.profile-showcase:hover {
    transform: translateY(-10px);
}

.profile-avatar-container {
    position: relative;
    width: 250px;
    height: 250px;
    margin: 0 auto 2rem;
}

.profile-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: linear-gradient(135deg, #00e5ff, #7c4dff);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 6px;
    position: relative;
    box-shadow: 0 0 50px rgba(0, 229, 255, 0.3);
    animation: profilePulse 4s ease-in-out infinite;
}

@keyframes profilePulse {
    0%, 100% {
        box-shadow: 0 0 50px rgba(0, 229, 255, 0.3);
    }
    50% {
        box-shadow: 0 0 80px rgba(0, 229, 255, 0.6), 0 0 100px rgba(124, 77, 255, 0.4);
    }
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    background: #0a0a1a;
    transition: transform 0.3s ease;
}

.profile-avatar:hover img {
    transform: scale(1.05);
}

.profile-floating-elements {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.floating-skill {
    position: absolute;
    background: rgba(0, 229, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    color: #00e5ff;
    border: 1px solid rgba(0, 229, 255, 0.3);
    animation: skillFloat 6s ease-in-out infinite;
}

.floating-skill:nth-child(1) { top: 10%; left: -20%; animation-delay: 0s; }
.floating-skill:nth-child(2) { top: 20%; right: -20%; animation-delay: 2s; }
.floating-skill:nth-child(3) { bottom: 20%; left: -20%; animation-delay: 4s; }
.floating-skill:nth-child(4) { bottom: 10%; right: -20%; animation-delay: 6s; }

@keyframes skillFloat {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
        opacity: 0.7;
    }
    50% {
        transform: translateY(-20px) rotate(5deg);
        opacity: 1;
    }
}

/* 🎯 DYNAMIC SKILL SHOWCASE */
.skills-section, .section {
    padding: 6rem 0;
    background: none (135deg, rgba(0, 10, 26, 0.98), rgba(18, 18, 37, 0.95));
    position: relative;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.section-title {
    text-align: center;
    font-size: 3rem;
    margin-bottom: 4rem;
    color: #fff;
    font-weight: 700;
    background: linear-gradient(135deg, #00e5ff, #7c4dff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    color: transparent;
}

.skills-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.skill-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: transform 0.3s ease;
}

.skill-card:hover {
    transform: translateY(-10px);
}

.skill-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.skill-item:last-child {
    margin-bottom: 0;
}

.skill-icon {
    width: 50px;
    height: 50px;
    flex-shrink: 0;
    border-radius: 10px;
    background: linear-gradient(135deg, #00e5ff, #7c4dff);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #0a0a1a;
}

.skill-info {
    width: 100%;
}

.skill-info h4 {
    color: #fff;
    margin-bottom: 0.5rem;
}

.skill-progress {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50px;
    height: 8px;
    overflow: hidden;
    position: relative;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #00e5ff, #7c4dff);
    border-radius: 50px;
    transition: width 2s ease-out;
    width: 0%;
}

.progress-bar.visible {
    width: var(--progress);
}

/* 📱 RESPONSIVE DESIGN */
@media (max-width: 992px) {
    .hero-content {
        grid-template-columns: 1fr;
        gap: 4rem;
        text-align: center;
    }
    .hero-text {
        order: 2;
    }
    .hero-visual {
        order: 1;
    }
    .hero-cta {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .hero-text h1 {
        font-size: 3rem;
    }
    .hero-text .subtitle {
        font-size: 1.3rem;
    }
    .hero-text .description {
        font-size: 1rem;
    }
    .profile-avatar-container {
        width: 200px;
        height: 200px;
    }
    .skills-grid {
        grid-template-columns: 1fr;
    }
    .section-title {
        font-size: 2.5rem;
    }
}

/* 🎨 GLASSMORPHISM EFFECTS */
.glass-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

/* ⚡ INTERACTIVE HOVER STATES */
.interactive-element {
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.interactive-element:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}
</style>

<div class="particle-system">
    <div class="particle-canvas">
        <div class="floating-particle"></div>
        <div class="floating-particle"></div>
        <div class="floating-particle"></div>
        <div class="floating-particle"></div>
        <div class="floating-particle"></div>
        <div class="floating-particle"></div>
    </div>
</div>

<section class="hero-section">
    <div class="hero-content">
        <div class="hero-text">
            
            <h1>Hi there, saya<br>Muhammad Alfatih</h1>

            <p class="subtitle">Mahasiswa Informatika di ITB Widyagama Lumajang</p>
            <p class="description">
            Lagi ngulik banyak hal seputar web biar bisa bikin solusi digital yang keren dan gampang dipakai. Fokusku sekarang di pengembangan web modern sama desain antarmuka yang enak dilihat dan dipakai.
            </p>

            <div class="hero-cta">
                <a href="<?= htmlspecialchars(BASE_URL . '/pages/about.php') ?>" class="btn-hero btn-primary">
                    <i class="fas fa-user"></i> About Me
                </a>
                <a href="<?= htmlspecialchars(BASE_URL . '/pages/contact.php') ?>" class="btn-hero btn-outline">
                    <i class="fas fa-envelope"></i> Contact
                </a>
            </div>
        </div>

        <div class="hero-visual">
            <div class="profile-showcase glass-card interactive-element">
                <div class="profile-avatar-container">
                    <div class="profile-avatar">
                        <?php if (!empty($profile['profile_image'])): ?>
                            <img src="<?= htmlspecialchars(BASE_URL . '/uploads/profile/' . $profile['profile_image']) ?>" alt="Foto Profil Muhammad Alfatih">
                        <?php else: ?>
                            <img src="<?= htmlspecialchars(BASE_URL . '/assets/img/default-profile.jpg') ?>" alt="Foto Profil Default">
                        <?php endif; ?>
                    </div>
                    <div class="profile-floating-elements">
                        <div class="floating-skill">PHP</div>
                        <div class="floating-skill">JavaScript</div>
                        <div class="floating-skill">React</div>
                        <div class="floating-skill">MySQL</div>
                    </div>
                </div>
                <h3 style="color: #fff; margin-bottom: 0.5rem; font-size: 1.4rem;">Muhammd Alfatih</h3>
                <p style="color: #b0bec5; font-size: 1rem;">kenalin nggeh kulo Alfa</p>
            </div>
        </div>
    </div>
</section>

<section class="skills-section">
    <div class="container">
        <h2 class="section-title">My Skills</h2>
        <div class="skills-grid">
            <div class="skill-card glass-card">
                <h3 style="color: #00e5ff; margin-bottom: 1.5rem; font-size: 1.3rem;"><i class="fas fa-code"></i> Frontend Development</h3>
                <div class="skill-item">
                    <div class="skill-icon"><i class="fab fa-html5"></i></div>
                    <div class="skill-info"><h4>HTML5</h4><div class="skill-progress"><div class="progress-bar" style="--progress: 30%;"></div></div></div>
                </div>
                <div class="skill-item">
                    <div class="skill-icon"><i class="fab fa-css3-alt"></i></div>
                    <div class="skill-info"><h4>CSS3</h4><div class="skill-progress"><div class="progress-bar" style="--progress: 37%;"></div></div></div>
                </div>
                <div class="skill-item">
                    <div class="skill-icon"><i class="fab fa-js"></i></div>
                    <div class="skill-info"><h4>JavaScript</h4><div class="skill-progress"><div class="progress-bar" style="--progress: 20%;"></div></div></div>
                </div>
            </div>
            
            <div class="skill-card glass-card">
                <h3 style="color: #7c4dff; margin-bottom: 1.5rem; font-size: 1.3rem;"><i class="fas fa-server"></i> Backend Development</h3>
                <div class="skill-item">
                    <div class="skill-icon"><i class="fab fa-php"></i></div>
                    <div class="skill-info"><h4>PHP</h4><div class="skill-progress"><div class="progress-bar" style="--progress: 41%;"></div></div></div>
                </div>
                <div class="skill-item">
                    <div class="skill-icon"><i class="fas fa-database"></i></div>
                    <div class="skill-info"><h4>MySQL</h4><div class="skill-progress"><div class="progress-bar" style="--progress: 85%;"></div></div></div>
                </div>
                <div class="skill-item">
                    <div class="skill-icon"><i class="fab fa-laravel"></i></div>
                    <div class="skill-info"><h4>Laravel</h4><div class="skill-progress"><div class="progress-bar" style="--progress: 21%;"></div></div></div>
                </div>
            </div>
            
            <div class="skill-card glass-card">
                 <h3 style="color: #06b6d4; margin-bottom: 1.5rem; font-size: 1.3rem;"><i class="fas fa-paint-brush"></i> Design & Tools</h3>
                <div class="skill-item">
                    <div class="skill-icon"><i class="fab fa-figma"></i></div>
                    <div class="skill-info"><h4>Figma</h4><div class="skill-progress"><div class="progress-bar" style="--progress: 23%;"></div></div></div>
                </div>
                <div class="skill-item">
                    <div class="skill-icon"><i class="fab fa-adobe"></i></div>
                    <div class="skill-info"><h4>Photoshop</h4><div class="skill-progress"><div class="progress-bar" style="--progress: 27%;"></div></div></div>
                </div>
                <div class="skill-item">
                    <div class="skill-icon"><i class="fab fa-git-alt"></i></div>
                    <div class="skill-info"><h4>Git</h4><div class="skill-progress"><div class="progress-bar" style="--progress: 41%;"></div></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="glass-card interactive-element" style="padding: 4rem 2rem; text-align: center;">
            <h2 class="section-title" style="margin-bottom: 2rem;">Tentang Saya</h2>
            <p style="color: #94a3b8; font-size: 1.2rem; line-height: 1.6; max-width: 800px; margin: 0 auto 2rem;">
                <?= htmlspecialchars($profile['summary']) ?>
            </p>
            <a href="<?= htmlspecialchars(BASE_URL . '/pages/about.php') ?>" class="btn-hero btn-primary">
                <i class="fas fa-arrow-right"></i> Selengkapnya
            </a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="glass-card interactive-element" style="padding: 4rem 2rem; text-align: center;">
            <h2 style="color: #fff; font-size: 2.5rem; margin-bottom: 1rem;">Kasih saran dong bangg?</h2>
            <p style="color: #b0bec5; font-size: 1.2rem; margin-bottom: 2rem;">
                soalnya masih pemula.
            </p>
            <div style="display: flex; gap: 1.5rem; justify-content: center; flex-wrap: wrap;">
                <a href="<?= htmlspecialchars(BASE_URL . '/pages/contact.php') ?>" class="btn-hero btn-primary">
                    <i class="fas fa-paper-plane"></i> hubungi saya
                </a>
                <a href="<?= htmlspecialchars(BASE_URL . '/pages/testimonials.php') ?>" class="btn-hero btn-outline">
                    <i class="fas fa-star"></i> Lihat Testimoni
                </a>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 🎯 INTERSECTION OBSERVER untuk animasi saat elemen terlihat
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Animate fade-in
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                
                // Animate progress bars
                if (entry.target.classList.contains('skill-card')) {
                    const progressBars = entry.target.querySelectorAll('.progress-bar');
                    progressBars.forEach(bar => {
                        bar.classList.add('visible');
                    });
                }
                observer.unobserve(entry.target); // Stop observing after animation
            }
        });
    }, observerOptions);

    // Terapkan observer ke elemen yang ingin dianimasikan
    document.querySelectorAll('.skill-card, .glass-card.interactive-element').forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(40px)';
        element.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
        observer.observe(element);
    });

    // 🎨 BUTTON RIPPLE EFFECT
    document.querySelectorAll('.btn-hero').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            ripple.classList.add('ripple');
            
            // Hapus ripple lama jika ada
            const oldRipple = this.querySelector('.ripple');
            if(oldRipple) {
                oldRipple.remove();
            }
            
            this.appendChild(ripple);
        });
    });

    // Tambahkan style untuk ripple effect
    const rippleStyle = document.createElement('style');
    rippleStyle.textContent = `
        .btn-hero .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.4);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(rippleStyle);

});
</script>

<?php 
// Pastikan path ke footer.php sudah benar
require_once __DIR__ . '/../templates/footer.php'; 
?>