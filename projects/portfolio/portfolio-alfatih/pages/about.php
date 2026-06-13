<?php
$pageTitle = "About Me";

// =======================================================================
// LANGKAH 1: Panggil file-file penting di paling atas
// Ini akan memastikan variabel koneksi database $pdo sudah ada sebelum digunakan.
// =======================================================================
require_once '../config/koneksi.php';
require_once '../config/constants.php';
require_once '../includes/helpers.php';

// =======================================================================
// LANGKAH 2: Ambil semua data yang diperlukan dari database sekarang
// =======================================================================

// --- Ambil Data Profil ---
try {
    $stmt_profile = $pdo->query("SELECT * FROM profil WHERE id = 1");
    $profile = $stmt_profile->fetch(PDO::FETCH_ASSOC);
    // Jika data profil tidak ditemukan, buat jadi array kosong agar tidak error saat dipanggil
    if (!$profile) {
        $profile = [];
    }
} catch (PDOException $e) {
    $profile = []; // Jika terjadi error koneksi, buat jadi array kosong
    // error_log("Gagal mengambil data profil: " . $e->getMessage()); // Aktifkan untuk debug
}

// --- Ambil Data Skills ---
try {
    $stmt_skills = $pdo->query("SELECT * FROM skills WHERE is_active = 1 ORDER BY category, display_order, name");
    $skills_by_category = [];
    while ($skill = $stmt_skills->fetch(PDO::FETCH_ASSOC)) {
        // Kelompokkan skill berdasarkan kategori
        $skills_by_category[$skill['category']][] = $skill;
    }
} catch (PDOException $e) {
    $skills_by_category = [];
    // error_log("Gagal mengambil data skills: " . $e->getMessage()); // Aktifkan untuk debug
}

// --- Ambil Data Pendidikan ---
try {
    $stmt_education = $pdo->query("SELECT * FROM pendidikan ORDER BY tahun_mulai DESC");
    $education_data = $stmt_education->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $education_data = [];
}

// --- Ambil Data Testimonial untuk Quote ---
try {
    $stmt_testimonial = $pdo->query("SELECT testimonial, nama FROM testimonials WHERE aktif = 1 ORDER BY RAND() LIMIT 1");
    $random_testimonial = $stmt_testimonial->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $random_testimonial = null;
}

// --- Ambil Data Proyek Terbaru ---
try {
    $stmt_projects = $pdo->query("SELECT COUNT(*) as total_projects FROM proyek");
    $project_count = $stmt_projects->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $project_count = ['total_projects' => 0];
}

// =======================================================================
// LANGKAH 3: Panggil template header setelah semua data siap
// =======================================================================
require_once '../templates/header.php';
?>

<style>
/* 🎨 ENHANCED PARTICLE SYSTEM */
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

.floating-particle {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    animation: floatUp 15s linear infinite;
}

.floating-particle:nth-child(1) { width: 4px; height: 4px; background: rgba(0, 229, 255, 0.8); left: 10%; animation-delay: 0s; }
.floating-particle:nth-child(2) { width: 8px; height: 8px; background: rgba(124, 77, 255, 0.6); left: 20%; animation-delay: 2s; }
.floating-particle:nth-child(3) { width: 6px; height: 6px; background: rgba(6, 182, 212, 0.7); left: 30%; animation-delay: 4s; }
.floating-particle:nth-child(4) { width: 10px; height: 10px; background: rgba(0, 229, 255, 0.4); left: 40%; animation-delay: 6s; }
.floating-particle:nth-child(5) { width: 5px; height: 5px; background: rgba(124, 77, 255, 0.8); left: 50%; animation-delay: 8s; }
.floating-particle:nth-child(6) { width: 12px; height: 12px; background: rgba(6, 182, 212, 0.3); left: 60%; animation-delay: 10s; }
.floating-particle:nth-child(7) { width: 7px; height: 7px; background: rgba(0, 229, 255, 0.5); left: 70%; animation-delay: 12s; }
.floating-particle:nth-child(8) { width: 9px; height: 9px; background: rgba(124, 77, 255, 0.4); left: 80%; animation-delay: 14s; }

@keyframes floatUp {
    0% { transform: translateY(100vh) translateX(0px) rotate(0deg); opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { transform: translateY(-100px) translateX(50px) rotate(360deg); opacity: 0; }
}

/* 🚀 MODERN ABOUT HERO */
.about-hero { min-height: 70vh; display: flex; align-items: center; position: relative; overflow: hidden; padding-top: 80px; }
.about-hero::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: radial-gradient(circle at 30% 70%, rgba(0, 229, 255, 0.1) 0%, transparent 50%), radial-gradient(circle at 70% 30%, rgba(124, 77, 255, 0.1) 0%, transparent 50%); animation: gradientShift 8s ease-in-out infinite; }
@keyframes gradientShift { 0%, 100% { opacity: 0.3; } 50% { opacity: 0.6; } }
.about-hero-content { max-width: 1200px; margin: 0 auto; padding: 3rem 2rem; text-align: center; position: relative; z-index: 2; }
.about-hero h1 { font-size: 4rem; font-weight: 800; margin-bottom: 1.5rem; background: linear-gradient(135deg, #00e5ff, #7c4dff, #00e5ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; animation: textGlow 3s ease-in-out infinite alternate; }
@keyframes textGlow { 0% { text-shadow: 0 0 20px rgba(0, 229, 255, 0.3); } 100% { text-shadow: 0 0 30px rgba(0, 229, 255, 0.6), 0 0 40px rgba(124, 77, 255, 0.4); } }
.about-hero .subtitle { font-size: 1.4rem; color: #b0bec5; margin-bottom: 2rem; font-weight: 500; }
.about-hero-quote { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); border-radius: 25px; padding: 2.5rem; border: 1px solid rgba(255, 255, 255, 0.1); max-width: 700px; margin: 0 auto; transition: transform 0.3s ease, box-shadow 0.3s ease; position: relative; overflow: hidden; }
.about-hero-quote::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent); transition: left 0.6s; }
.about-hero-quote:hover::before { left: 100%; }
.about-hero-quote:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0, 229, 255, 0.2); }
.about-hero-quote p { font-size: 1.3rem; color: #e0e7ff; font-style: italic; margin: 1rem 0; line-height: 1.6; }
.about-hero-quote i { color: #00e5ff; font-size: 2rem; opacity: 0.7; }
.quote-author { color: #7c4dff; font-size: 1rem; font-weight: 600; margin-top: 1rem; }

/* 💎 ENHANCED ABOUT CONTENT */
.about-content { padding: 8rem 0; position: relative; }
.about-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 5rem; max-width: 1400px; margin: 0 auto; padding: 0 2rem; align-items: center; }
.about-image-column { text-align: center; position: relative; }
.about-image-wrapper { position: relative; display: inline-block; margin-bottom: 2rem; }
.about-image { position: relative; width: 350px; height: 350px; border-radius: 50%; overflow: hidden; background: linear-gradient(135deg, #00e5ff, #7c4dff); padding: 8px; box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3); transition: transform 0.3s ease, box-shadow 0.3s ease; }
.about-image:hover { transform: scale(1.05) rotate(5deg); box-shadow: 0 30px 60px rgba(0, 229, 255, 0.4); }
.about-image img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; background: #0a0a1a; transition: transform 0.3s ease; }
.profile-stats { display: flex; justify-content: center; gap: 2rem; margin: 2rem 0; }
.stat-item { text-align: center; background: rgba(255, 255, 255, 0.05); padding: 1rem; border-radius: 15px; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); transition: transform 0.3s ease; }
.stat-item:hover { transform: translateY(-5px); }
.stat-number { font-size: 2rem; font-weight: 700; color: #00e5ff; display: block; }
.stat-label { font-size: 0.9rem; color: #b0bec5; margin-top: 0.5rem; }
.about-social { display: flex; justify-content: center; gap: 1.5rem; margin-top: 2rem; }
.social-icon { width: 60px; height: 60px; border-radius: 50%; background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(15px); border: 2px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center; justify-content: center; color: #00e5ff; font-size: 1.5rem; text-decoration: none; transition: all 0.3s ease; position: relative; overflow: hidden; }
.social-icon::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, #00e5ff, #7c4dff); opacity: 0; transition: opacity 0.3s ease; }
.social-icon:hover::before { opacity: 1; }
.social-icon:hover { transform: translateY(-5px) scale(1.1); box-shadow: 0 15px 30px rgba(0, 229, 255, 0.3); color: #0a0a1a; }
.social-icon i { position: relative; z-index: 1; }

/* 📝 ENHANCED TEXT CONTENT */
.about-text-column { position: relative; }
.about-text-card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); border-radius: 25px; padding: 4rem; border: 1px solid rgba(255, 255, 255, 0.1); box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2); transition: transform 0.3s ease; position: relative; overflow: hidden; }
.about-text-card::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(0, 229, 255, 0.1) 0%, transparent 70%); animation: cardGlow 6s ease-in-out infinite; }
@keyframes cardGlow { 0%, 100% { opacity: 0.3; } 50% { opacity: 0.6; } }
.about-text-card:hover { transform: translateY(-10px); }
.about-text-card h2 { font-size: 2.8rem; color: #fff; margin-bottom: 2rem; font-weight: 800; position: relative; z-index: 1; }
.about-text-intro { font-size: 1.3rem; color: #e0e7ff; line-height: 1.8; margin-bottom: 2rem; position: relative; z-index: 1; }
.about-text-card p { color: #b0bec5; line-height: 1.7; margin-bottom: 2rem; position: relative; z-index: 1; }
.about-info-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; margin: 3rem 0; position: relative; z-index: 1; }
.about-info-item { background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 15px; border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; position: relative; overflow: hidden; }
.about-info-item::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(0, 229, 255, 0.1), transparent); transition: left 0.6s; }
.about-info-item:hover::before { left: 100%; }
.about-info-item:hover { transform: translateY(-5px); background: rgba(255, 255, 255, 0.08); box-shadow: 0 10px 20px rgba(0, 229, 255, 0.1); }
.info-label { font-size: 1rem; color: #7c4dff; margin-bottom: 0.5rem; font-weight: 600; }
.info-value { color: #fff; font-weight: 700; font-size: 1.1rem; }
.about-cta { display: flex; gap: 2rem; margin-top: 3rem; position: relative; z-index: 1; }
.btn { padding: 1.2rem 2.5rem; border-radius: 50px; font-weight: 600; text-decoration: none; transition: all 0.3s ease; border: none; cursor: pointer; font-size: 1.1rem; display: inline-flex; align-items: center; gap: 0.8rem; position: relative; overflow: hidden; }
.btn::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent); transition: left 0.6s; }
.btn:hover::before { left: 100%; }
.btn-primary { background: linear-gradient(135deg, #00e5ff, #7c4dff); color: #0a0a1a; box-shadow: 0 10px 30px rgba(0, 229, 255, 0.3); }
.btn-primary:hover { transform: translateY(-5px) scale(1.05); box-shadow: 0 15px 40px rgba(0, 229, 255, 0.4); }
.btn-outline { background: rgba(255, 255, 255, 0.1); color: #00e5ff; border: 2px solid #00e5ff; backdrop-filter: blur(10px); }
.btn-outline:hover { background: rgba(0, 229, 255, 0.1); transform: translateY(-5px) scale(1.05); box-shadow: 0 15px 40px rgba(0, 229, 255, 0.2); }

/* 🎯 ENHANCED SKILLS SECTION */
.skills-section { padding: 8rem 0; position: relative; }
.skills-section::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: radial-gradient(circle at 80% 20%, rgba(124, 77, 255, 0.1) 0%, transparent 50%); animation: skillsGlow 10s ease-in-out infinite; }
@keyframes skillsGlow { 0%, 100% { opacity: 0.3; } 50% { opacity: 0.6; } }
.skills-container { max-width: 1400px; margin: 0 auto; padding: 0 2rem; position: relative; z-index: 1; }
.section-header { text-align: center; margin-bottom: 5rem; }
.section-header h2 { font-size: 3rem; color: #fff; margin-bottom: 1rem; font-weight: 800; background: linear-gradient(135deg, #00e5ff, #7c4dff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
.section-header p { color: #b0bec5; font-size: 1.2rem; }
.skills-category { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); border-radius: 25px; padding: 3rem; border: 1px solid rgba(255, 255, 255, 0.1); margin-bottom: 3rem; transition: all 0.3s ease; position: relative; overflow: hidden; }
.skills-category::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, #00e5ff, #7c4dff); transition: height 0.3s ease; }
.skills-category:hover::before { height: 100%; opacity: 0.1; }
.skills-category:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0, 229, 255, 0.2); }
.skills-category h3 { color: #00e5ff; margin-bottom: 2rem; font-size: 1.5rem; font-weight: 700; position: relative; z-index: 1; }
.skills-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; position: relative; z-index: 1; }
.skill-item { display: flex; align-items: center; gap: 1.5rem; padding: 1.5rem; background: rgba(255, 255, 255, 0.05); border-radius: 15px; border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; position: relative; overflow: hidden; }
.skill-item::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(0, 229, 255, 0.1), transparent); transition: left 0.6s; }
.skill-item:hover::before { left: 100%; }
.skill-item:hover { transform: translateY(-5px); background: rgba(255, 255, 255, 0.08); box-shadow: 0 10px 20px rgba(0, 229, 255, 0.1); }
.skill-icon { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px; background: linear-gradient(135deg, rgba(0, 229, 255, 0.2), rgba(124, 77, 255, 0.2)); transition: transform 0.3s ease; }
.skill-item:hover .skill-icon { transform: scale(1.1) rotate(10deg); }
.skill-icon img { width: 35px; height: 35px; object-fit: contain; }
.skill-info { flex: 1; }
.skill-info h4 { color: #fff; margin-bottom: 0.8rem; font-size: 1.1rem; font-weight: 600; }
.skill-progress { background: rgba(255, 255, 255, 0.1); border-radius: 50px; height: 8px; overflow: hidden; position: relative; }
.progress { height: 100%; background: linear-gradient(90deg, #00e5ff, #7c4dff); border-radius: 50px; transition: width 2s ease; position: relative; }
.progress::after { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent); animation: progressShine 2s ease-in-out infinite; }
@keyframes progressShine { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }

/* ✅ CUSTOM CURSOR & RIPPLE */
.custom-cursor { width: 25px; height: 25px; position: fixed; top: 0; left: 0; background: rgba(0,229,255,0.6); border: 2px solid #7c4dff; border-radius: 50%; transform: translate3d(0, 0, 0); pointer-events: none; z-index: 9999; transition: transform 0.15s cubic-bezier(0.25, 0.46, 0.45, 0.94); will-change: transform; backface-visibility: hidden; }
.custom-cursor.hover { transform: scale(1.5); background: rgba(124, 77, 255, 0.8); }
.btn .ripple { position: absolute; background: rgba(255,255,255,0.5); border-radius: 50%; transform: scale(0); animation: ripple-effect 0.6s linear; pointer-events: none; }
@keyframes ripple-effect { to { transform: scale(4); opacity: 0; } }

/* ✅ GENERAL ANIMATION STYLES */
.skill-item, .about-info-item, .skills-category, .stat-item { opacity: 0; transform: translateY(30px); transition: all 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94); will-change: transform, opacity; }
.in-view { opacity: 1; transform: translateY(0); }

/*
=======================================================================
✅ [KODE TAMBAHAN] CSS TIMELINE SUPER INTERAKTIF DARI FILE SEBELUMNYA
=======================================================================
*/
.education-timeline-section {
    padding: 6rem 0;
    position: relative;
    z-index: 1;
}
.education-timeline {
    background: rgba(0, 10, 26, 0.5);
    border-radius: 30px;
    padding: 4rem 2rem;
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.timeline { position: relative; max-width: 900px; margin: 0 auto; padding: 3rem 0; }
.timeline::before { content: ''; position: absolute; top: 0; left: 35px; width: 6px; height: 100%; background: linear-gradient(to bottom, rgba(0, 229, 255, 0.9) 0%, rgba(124, 77, 255, 0.9) 30%, rgba(6, 182, 212, 0.9) 60%, rgba(255, 106, 193, 0.9) 100%); border-radius: 3px; box-shadow: 0 0 20px rgba(0, 229, 255, 0.6), 0 0 40px rgba(124, 77, 255, 0.4), 0 0 60px rgba(6, 182, 212, 0.3); animation: timelineGlowPulse 4s ease-in-out infinite; z-index: 1; }
.timeline::after { content: ''; position: absolute; top: 0; left: 31px; width: 14px; height: 100%; background: linear-gradient(to bottom, rgba(0, 229, 255, 0.3) 0%, rgba(124, 77, 255, 0.3) 30%, rgba(6, 182, 212, 0.3) 60%, rgba(255, 106, 193, 0.3) 100%); border-radius: 7px; animation: timelineGlowWave 3s ease-in-out infinite; z-index: 0; }
@keyframes timelineGlowPulse { 0%, 100% { box-shadow: 0 0 20px rgba(0, 229, 255, 0.6), 0 0 40px rgba(124, 77, 255, 0.4), 0 0 60px rgba(6, 182, 212, 0.3); opacity: 0.8; } 50% { box-shadow: 0 0 30px rgba(0, 229, 255, 0.8), 0 0 60px rgba(124, 77, 255, 0.6), 0 0 80px rgba(6, 182, 212, 0.5); opacity: 1; } }
@keyframes timelineGlowWave { 0% { opacity: 0.2; transform: scaleX(1); } 50% { opacity: 0.6; transform: scaleX(1.2); } 100% { opacity: 0.2; transform: scaleX(1); } }
.timeline-item { position: relative; padding-left: 100px; margin-bottom: 5rem; opacity: 0; transform: translateX(-80px); transition: all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94); }
.timeline-item.animate-in { opacity: 1; transform: translateX(0); }
.timeline-dot { position: absolute; top: 30px; left: 8px; width: 35px; height: 35px; border-radius: 50%; background: linear-gradient(135deg, #00e5ff, #7c4dff); box-shadow: 0 0 40px rgba(0, 229, 255, 0.8), 0 0 60px rgba(124, 77, 255, 0.6), inset 0 2px 6px rgba(255, 255, 255, 0.4); z-index: 3; border: 4px solid rgba(255, 255, 255, 0.3); transition: all 0.5s ease; animation: dotMegaPulse 3s ease-in-out infinite; cursor: pointer; }
@keyframes dotMegaPulse { 0%, 100% { transform: scale(1); box-shadow: 0 0 40px rgba(0, 229, 255, 0.8), 0 0 60px rgba(124, 77, 255, 0.6), inset 0 2px 6px rgba(255, 255, 255, 0.4); } 50% { transform: scale(1.3); box-shadow: 0 0 60px rgba(0, 229, 255, 1), 0 0 80px rgba(124, 77, 255, 0.8), 0 0 100px rgba(6, 182, 212, 0.6), inset 0 2px 6px rgba(255, 255, 255, 0.6); } }
.timeline-dot:hover { transform: scale(1.4) !important; box-shadow: 0 0 80px rgba(0, 229, 255, 1), 0 0 120px rgba(124, 77, 255, 0.8), 0 0 140px rgba(6, 182, 212, 0.6) !important; }
.timeline-content { background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(30px); padding: 3rem; border-radius: 25px; border: 1px solid rgba(255, 255, 255, 0.15); transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94); position: relative; overflow: hidden; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3); }
.timeline-content:hover { transform: translateY(-12px) scale(1.03); box-shadow: 0 30px 60px rgba(0, 229, 255, 0.25), 0 20px 40px rgba(0, 0, 0, 0.4); background: rgba(255, 255, 255, 0.12); border-color: rgba(0, 229, 255, 0.4); }
.timeline-date { color: #00e5ff; font-weight: 700; font-size: 1.1rem; margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 1.5px; }
.timeline-content h3 { color: #fff; margin-bottom: 1rem; font-size: 1.7rem; font-weight: 700; }
.timeline-content h4 { color: #7c4dff; margin-bottom: 1.5rem; font-size: 1.3rem; font-weight: 600; }
.timeline-content p { color: #b0bec5; line-height: 1.8; margin-bottom: 1.5rem; font-size: 1.1rem; }
.achievement-badges { display: flex; gap: 1rem; margin-top: 1.5rem; flex-wrap: wrap; }
.achievement-badge { background: rgba(0, 229, 255, 0.15); color: #00e5ff; padding: 0.6rem 1.2rem; border-radius: 20px; font-size: 0.9rem; font-weight: 600; border: 2px solid rgba(0, 229, 255, 0.3); transition: all 0.4s ease; }
.achievement-badge:hover { background: rgba(0, 229, 255, 0.25); transform: translateY(-3px) scale(1.05); box-shadow: 0 10px 20px rgba(0, 229, 255, 0.3); }

/* 📱 RESPONSIVE DESIGN */
@media (max-width: 992px) { .about-hero h1 { font-size: 3rem; } .about-grid { grid-template-columns: 1fr; gap: 3rem; } .about-image { width: 280px; height: 280px; } .profile-stats { flex-direction: column; gap: 1rem; } .about-text-card { padding: 2.5rem; } .about-info-grid { grid-template-columns: 1fr; } .about-cta { flex-direction: column; align-items: center; } .skills-grid { grid-template-columns: 1fr; } }
@media (max-width: 768px) { .about-hero h1 { font-size: 2.5rem; } .about-image { width: 220px; height: 220px; } .about-text-card { padding: 2rem; } .about-text-card h2 { font-size: 2.2rem; } .about-hero-quote { padding: 2rem; } .section-header h2 { font-size: 2.5rem; } .skills-category { padding: 2rem; } .education-timeline { padding: 2rem 1rem; } .timeline::before { left: 25px; width: 4px; } .timeline::after { left: 22px; width: 10px; } .timeline-item { padding-left: 70px; } .timeline-dot { left: 8px; width: 25px; height: 25px; top: 25px; } .timeline-content { padding: 2rem; } .timeline-content h3 { font-size: 1.4rem; } .timeline-content h4 { font-size: 1.2rem; } }
</style>

<section class="about-hero">
    <div class="particle-system">
        <?php for ($i = 0; $i < 8; $i++) echo '<div class="floating-particle"></div>'; ?>
    </div>
    <div class="about-hero-content">
        <h1>About Me</h1>
        <p class="subtitle">Kenali aku lebih dekat</p>
        <div class="about-hero-quote">
            <i class="fas fa-quote-left"></i>
            <p>
                <?php if ($random_testimonial): ?>
                    "<?= htmlspecialchars($random_testimonial['testimonial']) ?>"
                <?php else: ?>
                    "Passionate about creating digital solutions that make a difference. Every line of code is an opportunity to build something meaningful."
                <?php endif; ?>
            </p>
            <i class="fas fa-quote-right" style="display: block; text-align: right;"></i>
            <?php if ($random_testimonial): ?>
                <div class="quote-author">- <?= htmlspecialchars($random_testimonial['nama']) ?></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="about-content">
    <div class="about-grid">
        <div class="about-image-column">
            <div class="about-image-wrapper">
                <div class="about-image">
                    <?php
                    $profile_image = $profile['profile_image'] ?? '';
                    $nama = $profile['nama'] ?? 'Muhammad Alfatih';
                    ?>
                    <img src="<?= !empty($profile_image) ? BASE_URL . '/uploads/profile/' . htmlspecialchars($profile_image) : BASE_URL . '/assets/img/default-profile.jpg' ?>"
                         alt="<?= htmlspecialchars($nama) ?>"
                         onerror="this.src='<?= BASE_URL ?>/assets/img/default-profile.jpg'">
                </div>
            </div>
            <div class="profile-stats">
                <div class="stat-item"><span class="stat-number"><?= $project_count['total_projects'] ?></span><span class="stat-label">Projects</span></div>
                <div class="stat-item"><span class="stat-number">3</span><span class="stat-label">Years Learning</span></div>
                <div class="stat-item"><span class="stat-number">∞</span><span class="stat-label">Passion</span></div>
            </div>
            <div class="about-social">
                <a href="<?= htmlspecialchars($profile['github'] ?? '#') ?>" target="_blank" class="social-icon" title="GitHub"><i class="fab fa-github"></i></a>
                <a href="mailto:<?= htmlspecialchars($profile['email'] ?? '#') ?>" class="social-icon" title="Email"><i class="fas fa-envelope"></i></a>
                <a href="https://wa.me/<?= str_replace(['+', ' ', '-'], '', htmlspecialchars($profile['whatsapp'] ?? '')) ?>" target="_blank" class="social-icon" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                <a href="<?= htmlspecialchars($profile['instagram'] ?? '#') ?>" target="_blank" class="social-icon" title="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <div class="about-text-column">
            <div class="about-text-card">
                <h2>Hello! I'm <?= htmlspecialchars($nama) ?></h2>
                <p class="about-text-intro">
                    Saat ini saya seorang mahasiswa semester 3 jurusan Informatika di ITB Widyagama Lumajang. Saya sangat menikmati proses belajar di dunia teknologi dan coding.
                </p>
                <p>
                    Di sela-sela kuliah, saya aktif mengerjakan berbagai proyek pribadi yang membantu saya mengasah skill dalam pengembangan web, problem-solving, dan manajemen proyek. Pengalaman ini mengajarkan saya pentingnya ketekunan dan kontribusi nyata.
                </p>
                <p>
                    Saya sangat bersyukur atas dukungan dari orang-orang terdekat, terutama seseorang yang spesial, <strong style="color: #00e5ff;">Niawatul Hasanah</strong>, yang selalu menjadi penyemangat. Bagi saya, hidup adalah perjalanan untuk terus belajar dan menjadi versi terbaik dari diri sendiri.
                </p>
                <div class="about-info-grid">
                    <div class="about-info-item"><div class="info-label">Full Name</div><div class="info-value"><?= htmlspecialchars($nama) ?></div></div>
                    <div class="about-info-item"><div class="info-label">Email</div><div class="info-value"><?= htmlspecialchars($profile['email'] ?? '-') ?></div></div>
                    <div class="about-info-item"><div class="info-label">Phone</div><div class="info-value"><?= htmlspecialchars($profile['whatsapp'] ?? '-') ?></div></div>
                    <div class="about-info-item"><div class="info-label">Location</div><div class="info-value"><?= htmlspecialchars($profile['location'] ?? 'Lumajang, Jawa Timur') ?></div></div>
                    <div class="about-info-item"><div class="info-label">University</div><div class="info-value">ITB Widyagama Lumajang</div></div>
                    <div class="about-info-item"><div class="info-label">Status</div><div class="info-value"><?= htmlspecialchars($profile['current_status'] ?? 'Mahasiswa') ?></div></div>
                </div>
                <div class="about-cta">
                    <a href="<?= BASE_URL ?>/pages/contact.php" class="btn btn-primary"><i class="fas fa-envelope"></i> Contact Me</a>
                    <a href="<?= BASE_URL ?>/pages/projects.php" class="btn btn-outline"><i class="fas fa-briefcase"></i> View Projects</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($education_data)): ?>
<section class="education-timeline-section">
    <div class="container">
        <div class="section-header">
            <h2>Pendidikan Saya</h2>
            <p>Jejak akademis dan pengalaman belajar saya.</p>
        </div>
        <div class="education-timeline">
            <div class="timeline">
                <?php foreach ($education_data as $education): ?>
                    <div class="timeline-item">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <div class="timeline-date"><?= $education['tahun_mulai'] ?> - <?= $education['tahun_selesai'] ?: 'Sekarang' ?></div>
                            <h3><?= htmlspecialchars($education['gelar']) ?></h3>
                            <h4><?= htmlspecialchars($education['institusi']) ?></h4>
                            <?php if (!empty($education['deskripsi'])): ?>
                                <p><?= htmlspecialchars($education['deskripsi']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($education['prestasi'])): ?>
                                <div class="achievement-badges">
                                    <?php 
                                    $prestasi_list = explode(',', $education['prestasi']);
                                    foreach ($prestasi_list as $prestasi): ?>
                                        <span class="achievement-badge"><?= htmlspecialchars(trim($prestasi)) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>


<section class="skills-section">
    <div class="skills-container">
        <div class="section-header">
            <h2>My Skills</h2>
            <p>Teknologi dan tools yang saya kuasai.</p>
        </div>
        <?php if (!empty($skills_by_category)): ?>
            <?php foreach ($skills_by_category as $category => $skills): ?>
                <div class="skills-category">
                    <h3><i class="fas fa-code"></i> <?= htmlspecialchars($category) ?></h3>
                    <div class="skills-grid">
                        <?php foreach ($skills as $skill): ?>
                            <div class="skill-item">
                                <div class="skill-icon">
                                    <img src="<?= BASE_URL ?>/uploads/skills/<?= htmlspecialchars($skill['icon']) ?>" alt="<?= htmlspecialchars($skill['name']) ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <i class="fas fa-star" style="display:none; color: #00e5ff; font-size: 1.8rem;"></i>
                                </div>
                                <div class="skill-info">
                                    <h4><?= htmlspecialchars($skill['name']) ?></h4>
                                    <div class="skill-progress">
                                        <div class="progress" style="width: <?= $skill['level'] ?>%;"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center; color: #b0bec5;">Data skill belum tersedia.</p>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // ✅ ENHANCED CUSTOM CURSOR
    const cursor = document.createElement("div");
    cursor.classList.add("custom-cursor");
    document.body.appendChild(cursor);
    let mouseX = 0, mouseY = 0;
    let cursorX = 0, cursorY = 0;
    function animateCursor() {
        cursorX += (mouseX - cursorX) * 0.15;
        cursorY += (mouseY - cursorY) * 0.15;
        cursor.style.transform = `translate3d(${cursorX}px, ${cursorY}px, 0)`;
        requestAnimationFrame(animateCursor);
    }
    animateCursor();
    document.addEventListener("mousemove", (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
    });
    const interactiveElements = document.querySelectorAll("a, button, .social-icon, .btn, .skill-item, .about-info-item, .timeline-content, .stat-item, .timeline-dot");
    interactiveElements.forEach(el => {
        el.addEventListener("mouseenter", () => cursor.classList.add("hover"));
        el.addEventListener("mouseleave", () => cursor.classList.remove("hover"));
    });

    // ✅ ENHANCED RIPPLE EFFECT
    document.querySelectorAll(".btn").forEach(btn => {
        btn.addEventListener("click", function(e) {
            const ripple = document.createElement("span");
            ripple.classList.add("ripple");
            this.appendChild(ripple);
            const maxDim = Math.max(this.offsetWidth, this.offsetHeight);
            ripple.style.width = ripple.style.height = `${maxDim}px`;
            ripple.style.left = `${e.clientX - this.offsetLeft - maxDim / 2}px`;
            ripple.style.top = `${e.clientY - this.offsetTop - maxDim / 2}px`;
            setTimeout(() => ripple.remove(), 600);
        });
    });
    
    // ✅ COUNTER ANIMATION FOR STATS
    const counters = document.querySelectorAll('.stat-number');
    counters.forEach(counter => {
        const target = counter.textContent;
        if (!isNaN(target)) {
            const counterObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        counter.textContent = '0';
                        const increment = Math.max(1, Math.ceil(target / 100));
                        const updateCount = () => {
                            const current = parseInt(counter.textContent);
                            if (current < target) {
                                counter.textContent = Math.min(target, current + increment);
                                setTimeout(updateCount, 20);
                            } else {
                                counter.textContent = target;
                            }
                        };
                        updateCount();
                        counterObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            counterObserver.observe(counter);
        }
    });

    /*
    =======================================================================
    ✅ [KODE TAMBAHAN] JAVASCRIPT ANIMASI INTERAKTIF DARI FILE SEBELUMNYA
    =======================================================================
    */
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("in-view");
                
                if (entry.target.classList.contains('skill-item')) {
                    const progressBar = entry.target.querySelector('.progress');
                    if (progressBar) {
                        const width = progressBar.style.width;
                        progressBar.style.width = '0%';
                        setTimeout(() => { progressBar.style.width = width; }, 300);
                    }
                }
                
                // Animasi khusus untuk timeline
                if (entry.target.classList.contains('timeline-item')) {
                    entry.target.classList.add('animate-in');
                    const content = entry.target.querySelector('.timeline-content');
                    const badges = entry.target.querySelectorAll('.achievement-badge');
                    
                    if (content) {
                        setTimeout(() => {
                            content.style.transform = 'translateY(0) scale(1)';
                            content.style.opacity = '1';
                        }, 400);
                    }
                    
                    badges.forEach((badge, index) => {
                        setTimeout(() => {
                            badge.style.transform = 'translateY(0) scale(1)';
                            badge.style.opacity = '1';
                        }, 600 + (index * 150));
                    });
                }
                
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe semua elemen yang butuh animasi
    document.querySelectorAll(".skill-item, .about-info-item, .skills-category, .timeline-item, .stat-item").forEach(item => {
        observer.observe(item);
    });

    // Inisialisasi state awal untuk animasi timeline
    document.querySelectorAll('.timeline-item').forEach(item => {
        const content = item.querySelector('.timeline-content');
        const badges = item.querySelectorAll('.achievement-badge');
        
        if (content) {
            content.style.transform = 'translateY(50px) scale(0.9)';
            content.style.opacity = '0';
            content.style.transition = 'all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        }
        
        badges.forEach(badge => {
            badge.style.transform = 'translateY(30px) scale(0.7)';
            badge.style.opacity = '0';
            badge.style.transition = 'all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        });
    });
});
</script>

<?php require_once '../templates/footer.php'; ?>