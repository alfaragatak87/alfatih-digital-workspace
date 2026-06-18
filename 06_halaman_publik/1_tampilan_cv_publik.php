<?php if (!defined('SITE_URL')) exit; // Proteksi akses langsung 
// +------------------------------------------------------------------------------+
//   FILE: tampilan/halaman/halaman_pendaratan.php                               
//   DESKRIPSI: Landing Page Premium dengan Glassmorphism & Animasi Fluid.
// +------------------------------------------------------------------------------+

$talent_users = [];
if (isset($mysqli)) {
    $res = $mysqli->query("SELECT username, nama_lengkap, foto_profil, profile_data FROM users WHERE role IN ('superadmin','admin','user') ORDER BY nama_lengkap ASC");
    if ($res) {
        while ($tu = $res->fetch_assoc()) {
            $tpd = json_decode($tu['profile_data'] ?? '{}', true) ?? [];
            if (!empty($tpd['identitas']['tampil_publik'])) { 
                $tu['_pd'] = $tpd; 
                $talent_users[] = $tu; 
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pub_page==='login' ? 'Login — Alfatih Workspace' : 'Alfatih Digital Workspace' ?></title>
    <meta name="theme-color" content="#09090b">
    <meta name="application-name" content="Alfatih Workspace">
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="07_aset_visual/images/LOGO_GAWE.svg">
    <link rel="icon" type="image/svg+xml" href="07_aset_visual/images/LOGO_GAWE.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --bg-color: #fafafa;
            --text-main: #111111;
            --text-muted: #666666;
            --accent-primary: #111111; 
            --accent-secondary: #333333; 
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(0, 0, 0, 0.08);
            --border: rgba(0,0,0,0.06);
            --border-md: rgba(0,0,0,0.1);
            --border-dark: rgba(0,0,0,0.15);
            --surface: #ffffff;
            --surface-2: #f8fafc;
        }
        
        html { scroll-behavior: smooth; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        a { color: inherit; text-decoration: none; }
        ::selection { background: var(--accent-primary); color: var(--text-main); }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-color); }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
        
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background-color: #fafafa;
            background-image: 
              radial-gradient(at 0% 0%, rgba(0,0,0,0.03) 0px, transparent 50%),
              radial-gradient(at 100% 100%, rgba(253,224,71,0.05) 0px, transparent 50%);
            background-attachment: fixed;
            color: var(--text-main);
            overflow-x: hidden;
            position: relative;
            -webkit-font-smoothing: antialiased;
        }
        
        /* Keyframe Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes pulseGlow {
            0% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.05); }
            100% { opacity: 0.3; transform: scale(1); }
        }
        @keyframes floatUpDown {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        @keyframes shakeError {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        .animate-fade-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }

        /* Glowing Orbs */
        .glow-orb-1, .glow-orb-2 {
            position: fixed; border-radius: 50%; filter: blur(120px);
            z-index: -1; pointer-events: none; animation: pulseGlow 10s ease-in-out infinite;
        }
        .glow-orb-1 {
            width: 50vw; height: 50vw; max-width: 600px; max-height: 600px;
            background: rgba(0,0,0,0.05); top: -20vh; left: -10vw;
        }
        .glow-orb-2 {
            width: 40vw; height: 40vw; max-width: 500px; max-height: 500px;
            background: rgba(253,224,71,0.1); bottom: -10vh; right: -10vw;
            animation-delay: -5s;
        }

        /* Navigation */
        .pub-nav {
            position: absolute; top: 0; left: 0; right: 0; height: 100px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 8%; z-index: 100;
            background: transparent;
            border-bottom: none;
            animation: fadeIn 1s ease-out forwards;
        }
        .pub-nav-back {
            display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px;
            border-radius: 12px; background: var(--border); color: var(--text-muted);
            border: 1px solid var(--glass-border); transition: all 0.3s;
        }
        .pub-nav-back:hover { color: var(--text-main); background: var(--accent-primary); transform: translateX(-3px); }
        .pub-nav-logo { display: flex; align-items: center; gap: 12px; transition: transform 0.3s; }
        .pub-nav-logo:hover { transform: scale(1.05); }
        .pub-nav-logo img { height: 36px; object-fit: contain; }
        .pub-nav-logo span {
            font-family: 'Outfit', sans-serif; font-size: 1.3rem; font-weight: 800;
            color: var(--text-main);
        }
        .pub-nav-links { display: flex; align-items: center; gap: 12px; }
        .pub-nav-links a.nav-item {
            padding: 10px 18px; font-size: 0.9rem; font-weight: 600;
            color: var(--text-muted); transition: all 0.3s; border-radius: 10px;
        }
        .pub-nav-links a.nav-item:hover { color: var(--text-main); background: var(--glass-bg); transform: translateY(-2px); }
        .pub-nav-links a.nav-cta {
            background: #fde047; /* Yellow */
            color: #111; padding: 12px 32px; font-size: 0.9rem; font-weight: 800;
            border-radius: 30px; 
            transition: all 0.3s;
        }
        .pub-nav-links a.nav-ghost {
            background: transparent;
            color: #fff; padding: 12px 24px; font-size: 0.9rem; font-weight: 600;
            border: 1px solid rgba(255,255,255,0.3); border-radius: 30px;
            transition: all 0.3s; margin-right: 12px;
        }
        .pub-nav-links a.nav-ghost:hover { background: rgba(255,255,255,0.1); }
        .pub-nav-links a.nav-item.active { color: #111; font-weight: 800; position: relative; }
        .pub-nav-links a.nav-item.active::after { content: ''; position: absolute; top: -5px; left: 18px; width: 12px; height: 3px; background: #111; border-radius: 2px; transform: rotate(-30deg); }
        .pub-nav-links a.nav-cta:hover { transform: scale(1.05); box-shadow: 0 8px 25px rgba(139, 92, 246, 0.5); }

        /* Typography & Utilities */
        .text-gradient {
            background: linear-gradient(to right, var(--accent-secondary), var(--accent-primary));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .text-accent {
            background: linear-gradient(to right, var(--accent-secondary), var(--accent-primary));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        /* Split Hero Section */
        .ed-hero {
            min-height: 100vh; position: relative; overflow: hidden;
            display: flex; align-items: center; padding: 0 8%;
            background: #ffffff;
        }
        .ed-hero-bg-shape {
            position: absolute;
            width: 65vw;
            height: 180vh;
            right: -10vw;
            top: -40vh;
            background: #1a1a1a;
            transform: rotate(-25deg);
            border-radius: 120px;
            z-index: 1;
        }
        .ed-hero-left {
            flex: 1; max-width: 45%; padding-right: 40px; z-index: 10;
        }
        .ed-hero-right {
            position: absolute; right: 0; top: 0; bottom: 0; width: 55%;
            z-index: 2;
            display: flex; align-items: center; justify-content: center;
            overflow: visible;
        }
        
        .ed-hero-title {
            font-family: 'Outfit', sans-serif; line-height: 1.1; margin-bottom: 16px; color: #111;
        }
        .ed-hero-title span { font-weight: 300; font-size: clamp(2.5rem, 4vw, 3.5rem); display: block; color: #333; }
        .ed-hero-title strong { font-weight: 900; font-size: clamp(3.5rem, 5vw, 4.5rem); display: block; }
        
        .ed-hero-sub {
            font-size: 1.05rem; color: #888; max-width: 400px;
            margin-bottom: 40px; line-height: 1.6; font-weight: 400;
        }
        .ed-hero-action {
            display: flex; align-items: center; gap: 32px; margin-bottom: 80px;
        }
        .ed-price {
            font-size: 2.2rem; font-weight: 800; color: #111; font-family: 'Outfit', sans-serif;
            display: flex; flex-direction: column; line-height: 1.2;
        }
        .ed-price span { font-size: 0.9rem; color: #aaa; font-weight: 600; text-decoration: line-through; }
        
        .btn-ed-primary {
            padding: 10px 32px 10px 10px; border-radius: 40px;
            background: #111; color: #ffffff; font-weight: 600; font-size: 1rem;
            transition: all 0.3s; display: inline-flex; align-items: center; gap: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: none; cursor: pointer; text-decoration: none;
        }
        .btn-ed-primary:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(0,0,0,0.3); color: #fff; }
        .btn-ed-primary i { background: #fde047; color: #111; width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; }
        
        /* Recommend Card */
        .ed-hero-recommend { display: flex; align-items: center; gap: 20px; }
        .recommend-avatar-wrap {
            position: relative; padding: 6px;
            border: 2px dashed #ddd; border-radius: 50%;
        }
        .recommend-avatar-wrap img { width: 50px; height: 50px; border-radius: 50%; display: block; }
        .ed-hero-recommend-text span { font-size: 0.9rem; color: #666; display: block; margin-bottom: 4px; }
        .ed-hero-recommend-text h4 { font-size: 1.1rem; color: #111; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px; cursor: pointer; }
        .ed-hero-recommend-text h4:hover { color: #fde047; }
        .ed-hero-recommend-text h4 i { transform: rotate(-45deg); font-size: 0.9rem; }

        /* Right Side Content */
        .hero-floating-logo {
            width: 55%; max-width: 500px;
            filter: drop-shadow(0 30px 50px rgba(0,0,0,0.6));
            animation: floatUpDown 6s ease-in-out infinite;
            z-index: 5; position: relative;
        }
        .hero-floating-item { position: absolute; animation: floatUpDown 8s ease-in-out infinite alternate; z-index: 4; opacity: 0.9; }
        
        /* Stats Coupon Box */
        .hero-stats-box {
            position: absolute; bottom: 12%; right: 12%; z-index: 10;
            background: rgba(30, 30, 30, 0.6); backdrop-filter: blur(15px);
            border: 2px dashed rgba(255,255,255,0.3); border-radius: 16px;
            padding: 24px; color: #fff; width: 400px;
        }
        .hero-stats-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; font-size: 0.85rem; color: #ccc; }
        .hero-stats-content { display: flex; align-items: center; gap: 20px; }
        .hero-stats-img { width: 60px; height: 60px; background: url('https://upload.wikimedia.org/wikipedia/commons/1/12/Google_Drive_icon_%282020%29.svg') center/cover; border-radius: 50%; box-shadow: 0 10px 20px rgba(0,0,0,0.5); }
        .hero-stats-text h3 { font-size: 1.2rem; font-weight: 700; margin-bottom: 4px; color: #fff; }
        .hero-stats-text p { font-size: 0.8rem; color: #aaa; margin: 0; display: flex; align-items: center; gap: 6px; }
        .hero-stats-price { display: flex; flex-direction: column; margin-left: auto; }
        .hero-stats-price span:first-child { font-size: 0.75rem; color: #888; text-decoration: line-through; margin-bottom: 2px; }
        .hero-stats-price span:last-child { font-size: 1.4rem; font-weight: 800; color: #fff; }
        .hero-stats-btn { background: #fde047; color: #111; font-weight: 700; font-size: 0.8rem; padding: 6px 12px; border-radius: 20px; display: flex; align-items: center; gap: 6px; margin-left: 12px; }

        /* Features Strip */
        .preview-strip { display: grid; grid-template-columns: repeat(3, 1fr); border-top: 1px solid var(--border-md); background: #ffffff; }
        .preview-panel { padding: 80px 5%; border-right: 1px solid var(--border-md); border-bottom: 1px solid var(--border-md); transition: all 0.4s ease; position: relative; overflow: hidden; }
        .preview-panel:nth-child(3n) { border-right: none; }
        .preview-panel:hover { background: #fafafa; transform: translateY(-5px); }
        .preview-panel::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: #fde047; opacity: 0; transition: opacity 0.4s; }
        .preview-panel:hover::before { opacity: 1; }
        .preview-panel-icon {
            font-size: 2.5rem; margin-bottom: 24px; color: #111; display: inline-block;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .preview-panel:hover .preview-panel-icon { transform: scale(1.1) rotate(-5deg); color: #eab308; }
        .preview-panel-title { font-family: 'Outfit', sans-serif; font-size: 1.5rem; font-weight: 800; margin-bottom: 16px; color: var(--text-main); }
        .preview-panel-desc { font-size: 0.95rem; color: var(--text-muted); line-height: 1.7; }

        /* Talent Directory */
        .ed-section { padding: 120px 5%; position: relative; }
        .ed-section-head { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 80px; flex-wrap: wrap; gap: 24px; }
        .ed-section-label { font-size: 0.85rem; font-weight: 800; letter-spacing: 4px; text-transform: uppercase; color: var(--accent-secondary); display: block; margin-bottom: 12px; }
        .ed-section-title { font-family: 'Outfit', sans-serif; font-size: 3.5rem; font-weight: 900; line-height: 1.1; }
        .ed-section-sub { font-size: 1rem; color: var(--text-main); font-weight: 600; background: rgba(0,0,0,0.1); padding: 12px 24px; border-radius: 30px; border: 1px solid var(--glass-border); backdrop-filter: blur(10px); }
        
        .talent-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 32px; }
        .talent-card {
            background: #ffffff; border: 1px solid var(--border-md);
            border-radius: 20px; padding: 40px;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); position: relative; overflow: hidden;
            text-decoration: none; display: flex; flex-direction: column; align-items: flex-start;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }
        .talent-card::before {
            content: ''; position: absolute; inset: 0; border-radius: 20px;
            border: 2px solid transparent; transition: all 0.4s; pointer-events: none;
        }
        .talent-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
        .talent-card:hover::before { border-color: #111; }
        .talent-card-num { position: absolute; top: 30px; right: 30px; font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 900; color: rgba(0,0,0,0.05); transition: color 0.4s; }
        .talent-card:hover .talent-card-num { color: #eab308; opacity: 1; }
        
        .talent-card-avatar { width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 3px solid #f8fafc; margin-bottom: 24px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); transition: transform 0.4s; }
        .talent-card:hover .talent-card-avatar { transform: scale(1.1) rotate(5deg); border-color: #fde047; }
        
        .talent-card-name { font-family: 'Outfit', sans-serif; font-size: 1.4rem; font-weight: 800; margin-bottom: 8px; color: var(--text-main); }
        .talent-card-profesi { font-size: 0.8rem; font-weight: 700; letter-spacing: 1px; color: #666; margin-bottom: 20px; text-transform: uppercase; }
        .talent-card-summary { font-size: 0.9rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 30px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .talent-card-tags { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 30px; }
        .talent-card-tag { font-size: 0.7rem; font-weight: 600; padding: 6px 12px; background: #f1f5f9; border-radius: 30px; color: #475569; transition: all 0.3s; }
        .talent-card:hover .talent-card-tag { background: #fde047; color: #111; }
        .talent-card-link { display: inline-flex; align-items: center; gap: 10px; font-size: 0.9rem; font-weight: 700; color: var(--text-main); transition: all 0.3s; margin-top: auto; }
        .talent-card:hover .talent-card-link { gap: 16px; color: #eab308; }

        .talent-empty { padding: 120px 5%; text-align: center; color: var(--text-muted); background: rgba(255,255,255,0.02); border-radius: 24px; border: 2px dashed var(--glass-border); }

        /* Login Page */
        .login-gate { position: fixed; top: 0; bottom: 0; width: 50vw; background: #0a0a0f; z-index: 9999; animation: gateOpen 1.2s cubic-bezier(0.8, 0, 0.2, 1) forwards; animation-delay: 0.8s; pointer-events: none; display: flex; align-items: center; justify-content: flex-end; }
        .login-gate.right { right: 0; left: auto; justify-content: flex-start; animation-name: gateOpenRight; }
        .login-gate::before { content: ''; position: absolute; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, transparent, var(--accent-primary), transparent); box-shadow: 0 0 20px var(--accent-primary); right: 0; }
        .login-gate.right::before { right: auto; left: 0; background: linear-gradient(to bottom, transparent, var(--accent-secondary), transparent); box-shadow: 0 0 20px var(--accent-secondary); }
        .login-gate-logo { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10000; color: var(--text-main); font-family: 'Outfit', sans-serif; font-size: 2.5rem; font-weight: 900; letter-spacing: 10px; animation: logoFadeOut 0.5s ease forwards; animation-delay: 0.6s; pointer-events: none; text-shadow: 0 0 20px rgba(139, 92, 246, 0.5); }
        
        @keyframes gateOpen { to { transform: translateX(-100%); visibility: hidden; } }
        @keyframes gateOpenRight { to { transform: translateX(100%); visibility: hidden; } }
        @keyframes logoFadeOut { to { opacity: 0; transform: translate(-50%, -50%) scale(1.5); visibility: hidden; } }
        @keyframes floatBlob { from { transform: translate(0, 0); } to { transform: translate(30px, 50px) scale(1.1); } }
        @keyframes pulseDecor { from { opacity: 0.01; transform: scale(1); } to { opacity: 0.04; transform: scale(1.05); } }

        .login-page { min-height: 100vh; display: grid; grid-template-columns: 1.2fr 1fr; }
        .login-page .animate-fade-up { opacity: 0; animation: fadeInUp 0.8s forwards; animation-delay: 1s; }
        .login-page .delay-100 { animation-delay: 1.2s; }
        .login-page .delay-200 { animation-delay: 1.4s; }

        .login-left {
            position: relative; overflow: hidden; display: flex; flex-direction: column; justify-content: center; padding: 10%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.4), rgba(248, 250, 252, 0.6));
        }
        .login-left::after {
            content: ''; position: absolute; inset: 0; background: url('https://www.transparenttextures.com/patterns/cubes.png') repeat; opacity: 0.15; pointer-events: none;
        }
        .login-left-decor { font-family: 'Outfit', sans-serif; font-size: 20rem; font-weight: 900; color: rgba(0,0,0,0.03); position: absolute; top: -100px; left: -80px; line-height: 1; user-select: none; animation: pulseDecor 4s infinite alternate; }
        .login-left-title { font-family: 'Outfit', sans-serif; font-size: 4rem; font-weight: 900; line-height: 1.1; margin-bottom: 24px; position: relative; z-index: 1; text-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .login-left-sub { font-size: 1.2rem; color: var(--text-muted); line-height: 1.8; position: relative; z-index: 1; max-width: 480px; }
        
        .login-right { display: flex; flex-direction: column; justify-content: center; padding: 8% 10%; position: relative; z-index: 2; background: rgba(255,255,255,0.6); backdrop-filter: blur(30px); overflow: hidden; }
        .login-right::before { content: ''; position: absolute; width: 400px; height: 400px; background: radial-gradient(circle, var(--accent-primary), transparent 70%); top: -100px; right: -100px; opacity: 0.15; filter: blur(60px); z-index: -1; animation: floatBlob 8s infinite alternate ease-in-out; }
        .login-right::after { content: ''; position: absolute; width: 300px; height: 300px; background: radial-gradient(circle, var(--accent-secondary), transparent 70%); bottom: -50px; left: -50px; opacity: 0.15; filter: blur(50px); z-index: -1; animation: floatBlob 6s infinite alternate-reverse ease-in-out; }
        .login-right-back {
            position: absolute; top: 40px; right: 40px; font-size: 0.9rem; font-weight: 700;
            color: var(--text-muted); display: inline-flex; align-items: center; gap: 10px; transition: all 0.3s;
            background: var(--border); padding: 12px 24px; border-radius: 12px; border: 1px solid var(--glass-border); z-index: 10;
        }
        .login-right-back:hover { color: var(--text-main); background: rgba(0,0,0,0.05); transform: translateX(-5px); }
        .login-box { background: rgba(255,255,255,0.03); padding: 50px; border-radius: 24px; border: 1px solid rgba(0,0,0,0.1); box-shadow: 0 30px 60px -12px rgba(0,0,0,0.6); position: relative; z-index: 10; backdrop-filter: blur(10px); }
        .login-right-title { font-family: 'Outfit', sans-serif; font-size: 3rem; font-weight: 900; margin-bottom: 16px; }
        .login-right-sub { font-size: 1.05rem; color: var(--text-muted); margin-bottom: 48px; }
        
        .login-err { 
            background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; 
            padding: 16px 20px; border-radius: 12px; font-size: 0.9rem; font-weight: 600; margin-bottom: 30px; 
            display: flex; align-items: center; gap: 12px; animation: shakeError 0.5s ease-in-out; 
        }
        
        .ed-form-group { margin-bottom: 28px; position: relative; }
        .ed-form-label { display: block; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); margin-bottom: 10px; transition: color 0.3s; }
        .ed-form-group:focus-within .ed-form-label { color: var(--accent-primary); }
        .ed-form-input {
            width: 100%; padding: 16px 20px; background: var(--surface); border: 2px solid var(--border-md);
            border-radius: 12px; color: var(--text-main); font-size: 1.05rem; font-family: 'Inter', sans-serif; outline: none; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .ed-form-input::placeholder { color: #64748b; font-weight: 400; }
        .ed-form-input:focus { border-color: var(--accent-primary); box-shadow: 0 0 0 6px rgba(139, 92, 246, 0.15); background: rgba(255,255,255,0.6); transform: scale(1.02); }
        .ed-form-input.err { border-color: #ef4444; }
        .ed-form-input.err:focus { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0.15); }
        
        .btn-ed-submit {
            width: 100%; padding: 18px; background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: #ffffff; font-size: 1.05rem; font-weight: 800; border-radius: 12px; border: none; cursor: pointer;
            font-family: 'Inter', sans-serif; margin-top: 20px; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); 
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4); display: flex; align-items: center; justify-content: center; gap: 12px;
        }
        .btn-ed-submit:hover { transform: translateY(-4px) scale(1.02); box-shadow: 0 12px 35px rgba(139, 92, 246, 0.6); }

        /* Footer */
        .pub-footer { padding: 60px 5%; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--glass-border); background: rgba(255,255,255,0.6); }
        .pub-footer-copy { font-size: 0.95rem; color: var(--text-muted); font-weight: 500; }
        .pub-footer-link { font-size: 0.95rem; font-weight: 700; color: var(--text-main); display: inline-flex; align-items: center; gap: 10px; transition: all 0.3s; padding: 12px 24px; background: var(--border); border-radius: 12px; }
        .pub-footer-link:hover { color: var(--text-main); background: var(--accent-primary); transform: translateY(-3px); box-shadow: 0 10px 20px rgba(139, 92, 246, 0.3); }

        @media(max-width:1200px) {
            .login-page { grid-template-columns: 1fr 1fr; }
            .login-right { padding: 6% 8%; }
        }
        @media(max-width:992px) {
            .preview-strip { grid-template-columns: 1fr; }
            .preview-panel { border-right: none; border-bottom: 1px solid var(--glass-border); padding: 60px 5%; }
            .login-page { grid-template-columns: 1fr; }
            .login-left { display: none; }
            .login-right { padding: 40px 5%; justify-content: center; }
            .login-right-back { top: 20px; right: 20px; }
        }
        @media(max-width:768px) {
            .pub-nav { padding: 0 20px; height: 70px; }
            .pub-nav-logo span { font-size: 1.1rem; }
            .pub-nav-links a.nav-item, .pub-nav-links a.nav-ghost { display: none; }
            .pub-nav-links a.nav-cta { padding: 8px 16px; font-size: 0.8rem; }
            .ed-hero { flex-direction: column; padding: 120px 5% 40px; text-align: center; }
            .ed-hero-bg-shape { width: 200vw; height: 100%; right: auto; left: -50vw; top: 50%; transform: rotate(-8deg); border-radius: 60px; }
            .ed-hero-left { max-width: 100%; padding-right: 0; margin-bottom: 30px; display: flex; flex-direction: column; align-items: center; }
            .ed-hero-title span { font-size: 2rem; }
            .ed-hero-title strong { font-size: 2.8rem; }
            .ed-hero-sub { font-size: 0.95rem; margin-bottom: 24px; }
            .ed-hero-action { flex-direction: column; justify-content: center; gap: 16px; margin-bottom: 30px; width: 100%; }
            .ed-price { font-size: 1.8rem; }
            .btn-ed-primary { width: 100%; justify-content: center; }
            .ed-hero-right { position: relative; width: 100%; min-height: 350px; display: flex; flex-direction: column; align-items: center; }
            .hero-floating-logo { width: 70%; max-width: 250px; }
            .hero-stats-box { position: relative; bottom: auto; right: auto; margin-top: 20px; width: 100%; max-width: 100%; padding: 16px; }
            .hero-stats-content { flex-direction: column; gap: 12px; }
            .hero-stats-price { margin-left: 0; align-items: center; }
            .hero-stats-btn { margin-left: 0; margin-top: 8px; width: 100%; justify-content: center; }
            .ed-section { padding: 60px 20px; }
            .ed-section-head { flex-direction: column; align-items: flex-start; gap: 16px; margin-bottom: 30px; }
            .ed-section-title { font-size: 2.2rem; }
            .pub-footer { flex-direction: column; gap: 24px; text-align: center; }
            .login-box { padding: 30px 20px; }
            .login-right-title { font-size: 2.2rem; }
        }
    </style>
</head>
<body>
<!-- Glowing Background -->
<div class="glow-orb-1"></div>
<div class="glow-orb-2"></div>

<nav class="pub-nav">
    <div style="display: flex; align-items: center; gap: 16px;">
        <?php if ($pub_page !== 'hub') { ?>
        <a href="javascript:history.back()" class="pub-nav-back" title="Kembali"><i class="fa-solid fa-arrow-left"></i></a>
        <?php } ?>
        <a href="index.php" class="pub-nav-logo">
            <img src="07_aset_visual/images/LOGO_GAWE.svg?v=<?= time() ?>" alt="Logo" onerror="this.style.display='none'">
            <span>GAWE.MY.ID</span>
        </a>
    </div>
    <div class="pub-nav-links">
        <?php if ($pub_page !== 'login') { ?>
        <a href="#features" class="nav-item">Why Workspace?</a>
        <a href="#talent" class="nav-item active">Talents</a>
        <div style="width: 20px;"></div>
        <a href="index.php?page=login" class="nav-ghost">Sign In</a>
        <a href="index.php?page=login" class="nav-cta">Login</a>
        <?php } else { ?>
        <a href="index.php" class="nav-cta" style="background: var(--border); border: 1px solid var(--glass-border); color: var(--text-muted); box-shadow: none;"><i class="fa-solid fa-house"></i> Beranda</a>
        <?php } ?>
    </div>
</nav>

<?php if ($pub_page === 'login') { ?>
<!-- Entrance Gate Animation -->
<div class="login-gate left"></div>
<div class="login-gate right"></div>
<div class="login-gate-logo"><img src="07_aset_visual/images/LOGO_GAWE.svg" alt="Logo" style="height:60px; filter: drop-shadow(0 0 20px rgba(139, 92, 246, 0.8));"></div>

<div class="login-page">
    <div class="login-left animate-fade-up">
        <div class="login-left-decor">AW</div>
        <h2 class="login-left-title">Selamat<br>Datang<br><span class="text-gradient">Kembali.</span></h2>
        <p class="login-left-sub">Kelola file, bangun portfolio, dan tampilkan karya terbaik Anda kepada dunia melalui ruang kerja digital modern.</p>
    </div>
    <div class="login-right">
        <div class="login-box animate-fade-up delay-200">
            <h1 class="login-right-title">Akses <span class="text-accent">Workspace</span></h1>
            <p class="login-right-sub">Masukkan kredensial Anda untuk melanjutkan ke dasbor.</p>
            <?php if ($error_msg) { ?><div class="login-err"><i class="fa-solid fa-circle-exclamation" style="font-size:1.2rem;"></i><?= h($error_msg) ?></div><?php } ?>
            <div class="auth-tabs" style="display:flex; margin-bottom: 24px; border-bottom: 1px solid var(--glass-border);">
                <button onclick="toggleAuth('login')" id="tab_login" style="flex:1; padding: 12px; background:transparent; border:none; border-bottom: 2px solid var(--accent-primary); color: var(--text-main); font-weight:600; cursor:pointer;">Masuk</button>
                <button onclick="toggleAuth('register')" id="tab_register" style="flex:1; padding: 12px; background:transparent; border:none; border-bottom: 2px solid transparent; color: var(--text-muted); font-weight:600; cursor:pointer;">Daftar Baru</button>
            </div>

            <!-- FORM LOGIN -->
            <form id="formLogin" method="POST" action="index.php">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="csrf_token" value="<?= generateCSRF() ?>">
                <div class="ed-form-group">
                    <label class="ed-form-label">Username / Email</label>
                    <input type="text" name="username" class="ed-form-input <?= $error_msg ? 'err' : '' ?>" placeholder="Ketik username / email" required autocomplete="username">
                </div>
                <div class="ed-form-group">
                    <label class="ed-form-label">Kata Sandi</label>
                    <div style="position: relative;">
                        <input type="password" id="login_password" name="password" class="ed-form-input <?= $error_msg ? 'err' : '' ?>" placeholder="••••••••" required autocomplete="current-password" style="padding-right: 50px;">
                        <button type="button" onclick="const pwd = document.getElementById('login_password'); const icon = document.getElementById('toggle_pwd_icon'); if (pwd.type === 'password') { pwd.type = 'text'; icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); } else { pwd.type = 'password'; icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 1.1rem; padding: 4px; z-index: 5;">
                            <i class="fa-solid fa-eye" id="toggle_pwd_icon"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-ed-submit">Masuk Sekarang <i class="fa-solid fa-arrow-right"></i></button>
            </form>

            <!-- FORM REGISTER -->
            <form id="formRegister" method="POST" action="index.php" style="display:none;">
                <input type="hidden" name="action" value="register">
                <input type="hidden" name="csrf_token" value="<?= generateCSRF() ?>">
                
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div class="ed-form-group">
                        <label class="ed-form-label">Nama Panggilan</label>
                        <input type="text" name="nama_panggilan" class="ed-form-input" placeholder="Cth: Budi" required>
                    </div>
                    <div class="ed-form-group">
                        <label class="ed-form-label">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="ed-form-input" placeholder="Cth: Budi Santoso" required>
                    </div>
                </div>
                
                <div class="ed-form-group">
                    <label class="ed-form-label">Email Aktif (Gmail)</label>
                    <input type="email" name="email" class="ed-form-input" placeholder="budi@gmail.com" required>
                </div>

                <div class="ed-form-group">
                    <label class="ed-form-label">Profesi / Pekerjaan Anda</label>
                    <select name="profesi" class="ed-form-input" required style="background: rgba(255,255,255,0.8); color: var(--text-main);">
                        <option value="">-- Pilih Profesi --</option>
                        <option value="Pegawai Balai Desa">Pegawai Balai Desa</option>
                        <option value="Guru / Dosen">Guru / Dosen</option>
                        <option value="Pekerja Lepas / Kreatif">Pekerja Lepas / Programmer</option>
                        <option value="Mahasiswa">Mahasiswa</option>
                        <option value="Pelajar">Pelajar</option>
                        <option value="Lainnya">Lainnya / Umum</option>
                    </select>
                </div>

                <button type="submit" class="btn-ed-submit" style="background: linear-gradient(135deg, var(--accent-secondary), var(--accent-primary));">Buat Akun <i class="fa-solid fa-user-plus"></i></button>
                <div style="font-size:0.8rem; color:var(--text-muted); margin-top:12px; text-align:center;">
                    * Username & Kata Sandi otomatis akan dikirim ke Email Anda.
                </div>
            </form>

            <script>
            function toggleAuth(type) {
                if (type === 'login') {
                    document.getElementById('formLogin').style.display = 'block';
                    document.getElementById('formRegister').style.display = 'none';
                    document.getElementById('tab_login').style.borderBottomColor = 'var(--accent-primary)';
                    document.getElementById('tab_login').style.color = 'var(--text-main)';
                    document.getElementById('tab_register').style.borderBottomColor = 'transparent';
                    document.getElementById('tab_register').style.color = 'var(--text-muted)';
                } else {
                    document.getElementById('formLogin').style.display = 'none';
                    document.getElementById('formRegister').style.display = 'block';
                    document.getElementById('tab_register').style.borderBottomColor = 'var(--accent-primary)';
                    document.getElementById('tab_register').style.color = 'var(--text-main)';
                    document.getElementById('tab_login').style.borderBottomColor = 'transparent';
                    document.getElementById('tab_login').style.color = 'var(--text-muted)';
                }
            }
            </script>
            
            <div style="margin: 24px 0; display: flex; align-items: center; text-align: center; color: var(--text-muted); font-size: 0.85rem;">
                <div style="flex: 1; height: 1px; background: rgba(0,0,0,0.1);"></div>
                <span style="margin: 0 16px;">ATAU GUNAKAN GOOGLE</span>
                <div style="flex: 1; height: 1px; background: rgba(0,0,0,0.1);"></div>
            </div>

            <div id="g_id_onload"
                 data-client_id="465641813024-kog6osfr6od306d3bfbd3pcsppon1f3c.apps.googleusercontent.com"
                 data-context="signin"
                 data-ux_mode="popup"
                 data-callback="handleGoogleLogin"
                 data-auto_prompt="false">
            </div>
            <div class="g_id_signin"
                 data-type="standard"
                 data-shape="rectangular"
                 data-theme="outline"
                 data-text="continue_with"
                 data-size="large"
                 data-logo_alignment="left"
                 style="display: flex; justify-content: center;">
            </div>

            <form id="googleLoginForm" method="POST" action="index.php?page=login" style="display: none;">
                <input type="hidden" name="action" value="google_login">
                <input type="hidden" name="credential" id="googleCredential">
            </form>
        </div>
    </div>
</div>
<?php } else { ?>
<section class="ed-hero">
    <div class="ed-hero-bg-shape"></div>
    <div class="ed-hero-left">
        <h1 class="ed-hero-title animate-fade-up">
            <span>Digital Workspace</span>
            <strong>Organized.</strong>
        </h1>
        <p class="ed-hero-sub animate-fade-up delay-100">Ruang kerja digital untuk mengelola file secara cerdas, mendesain CV profesional, dan mempublikasikan Portfolio Anda ke online.</p>
        
        <div class="ed-hero-action animate-fade-up delay-200">

            <a href="index.php?page=login" class="btn-ed-primary">
                <i class="fa-solid fa-lock"></i> Masuk ke Dasbor
            </a>
        </div>
        
        <div class="ed-hero-recommend animate-fade-up delay-300">
            <div class="recommend-avatar-wrap">
                <img src="https://ui-avatars.com/api/?name=Admin&background=fde047&color=000&bold=true" alt="Admin">
            </div>
            <div class="ed-hero-recommend-text">
                <span>Direktori Eksklusif</span>
                <h4 onclick="document.getElementById('talent').scrollIntoView({behavior:'smooth'})">Jelajahi Talent <i class="fa-solid fa-arrow-right"></i></h4>
            </div>
        </div>
    </div>
    
    <div class="ed-hero-right">
        <!-- Background decorative pattern on dark -->
        <div style="position:absolute; inset:0; background: url('https://www.transparenttextures.com/patterns/food.png') repeat; opacity: 0.05; pointer-events: none; border-bottom-left-radius: 30vw;"></div>
        
        <!-- Floating Logo replacing food -->
        <img src="07_aset_visual/images/LOGO_GAWE.svg" alt="Gawe Logo" class="hero-floating-logo">
        
        <!-- Floating decors -->
        <i class="fa-solid fa-star hero-floating-item" style="color:#fde047; font-size:1.5rem; top:20%; left:20%; animation-delay:-2s;"></i>
        <i class="fa-solid fa-cloud hero-floating-item" style="color:var(--accent-secondary); font-size:2rem; bottom:30%; right:15%; animation-delay:-4s;"></i>
        <i class="fa-solid fa-folder-open hero-floating-item" style="color:var(--accent-primary); font-size:1.2rem; top:35%; right:20%; animation-delay:-1s;"></i>
        <div class="hero-floating-item" style="width:12px; height:12px; background: #ef4444; border-radius:50%; top:60%; left:10%; animation-delay:-3s;"></div>
        
        <!-- Stats Coupon Box -->
        <div class="hero-stats-box animate-fade-up delay-400">
            <div class="hero-stats-header">
                <span>Statistik Server</span>
                <div style="font-size:0.7rem; color:#888; cursor:pointer;"><i class="fa-solid fa-arrow-left"></i> Prev &nbsp;&nbsp; Next <i class="fa-solid fa-arrow-right" style="color:#fff;"></i></div>
            </div>
            <div class="hero-stats-content">
                <div class="hero-stats-img"></div>
                <div class="hero-stats-text">
                    <h3>Cloud Storage</h3>
                    <p><i class="fa-solid fa-fire" style="color:#ef4444;"></i> 99.9% Uptime</p>
                </div>
                <div class="hero-stats-price">
                    <span>1 TB</span>
                    <span>10 GB</span>
                </div>
                <div class="hero-stats-btn">
                    <i class="fa-solid fa-clock"></i> 24/7
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ed-section" id="talent">
    <div class="ed-section-head animate-fade-up">
        <div>
            <span class="ed-section-label">Direktori Eksklusif</span>
            <h2 class="ed-section-title">Talent <span class="text-accent">Showcase</span></h2>
        </div>
        <span class="ed-section-sub"><?= count($talent_users ?? []) ?> Profil Aktif</span>
    </div>
    <?php if (empty($talent_users)) { ?>
    <div class="talent-empty animate-fade-up delay-100">
        <i class="fa-solid fa-user-astronaut" style="font-size:4rem;display:block;margin-bottom:24px;color:var(--glass-border);"></i>
        <strong style="font-size:1.4rem;color:var(--text-main);display:block;margin-bottom:12px;">Ruang Kosong Menunggu Bintang.</strong>
        <p style="font-size:1.05rem;">Login sekarang dan aktifkan "Tampilkan di Direktori Publik" melalui CV Builder.</p>
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
            $delay = ($idx % 3) * 100;
        ?>
        <a href="<?= h($t_url) ?>" target="_blank" class="talent-card animate-fade-up" style="animation-delay: <?= $delay ?>ms;">
            <div class="talent-card-num"><?= str_pad($idx+1, 2, '0', STR_PAD_LEFT) ?></div>
            <img src="<?= h($t_foto) ?>" alt="<?= h($t_name) ?>" class="talent-card-avatar" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($t_name) ?>&background=1a1a1a&color=ffffff&bold=true'">
            <h3 class="talent-card-name"><?= h($t_name) ?></h3>
            <?php if ($t_profesi) { ?><div class="talent-card-profesi"><?= h($t_profesi) ?><?php if($t_sebutan){?> <span style="text-transform:none;font-weight:500;color:var(--text-muted);">"<?= h($t_sebutan) ?>"</span><?php }?></div><?php } ?>
            <?php if ($t_summary) { ?><p class="talent-card-summary"><?= h($t_summary) ?></p><?php } ?>
            <?php if (!empty($t_skills)) { ?><div class="talent-card-tags"><?php foreach ($t_skills as $tsk) { ?><span class="talent-card-tag"><?= h($tsk['nama']??'') ?></span><?php } ?></div><?php } ?>
            <div class="talent-card-link">Kunjungi Portfolio <i class="fa-solid fa-arrow-right"></i></div>
        </a>
        <?php } ?>
    </div>
    <?php } ?>
</section>

<div class="preview-strip" id="features">
    <div class="preview-panel animate-fade-up"><div class="preview-panel-icon"><i class="fa-solid fa-folder-tree"></i></div><h3 class="preview-panel-title">Smart File Manager</h3><p class="preview-panel-desc">Sistem folder bersarang, upload multi-file dengan drag & drop, fitur recycle bin, dan kemampuan membagikan file via tautan atau kode QR dengan super cepat.</p></div>
    <div class="preview-panel animate-fade-up delay-100"><div class="preview-panel-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></div><h3 class="preview-panel-title">CV & Portfolio Builder</h3><p class="preview-panel-desc">Bangun profil komprehensif mulai dari riwayat pendidikan, pengalaman kerja, hingga galeri portfolio. Tampil otomatis sebagai halaman web publik yang elegan.</p></div>
    <div class="preview-panel animate-fade-up delay-200"><div class="preview-panel-icon"><i class="fa-solid fa-shield-halved"></i></div><h3 class="preview-panel-title">RBAC God Mode</h3><p class="preview-panel-desc">Keamanan berlapis dengan sistem peran (Role-Based Access Control). SuperAdmin dapat memantau seluruh aktivitas, storage, dan memanajemen akses.</p></div>
    <div class="preview-panel animate-fade-up delay-300"><div class="preview-panel-icon"><i class="fa-solid fa-users"></i></div><h3 class="preview-panel-title">Manajemen Penduduk</h3><p class="preview-panel-desc">Sistem basis data canggih untuk mengelola informasi kependudukan, statistik demografi, hingga pembuatan laporan kependudukan dengan presisi tinggi.</p></div>
    <div class="preview-panel animate-fade-up delay-400"><div class="preview-panel-icon"><i class="fa-solid fa-chart-line"></i></div><h3 class="preview-panel-title">Proyek & Klien Bisnis</h3><p class="preview-panel-desc">Pantau perkembangan proyek klien secara <em>real-time</em>, kelola jadwal penagihan, status pembayaran, dan timeline penyelesaian kerja di satu tempat.</p></div>
    <div class="preview-panel animate-fade-up delay-500"><div class="preview-panel-icon"><i class="fa-solid fa-robot"></i></div><h3 class="preview-panel-title">Integrasi Asisten AI</h3><p class="preview-panel-desc">Dapatkan bantuan dari Chatbot pintar bertenaga AI untuk mempermudah analisis data, menemukan file, hingga mengotomatisasi tugas-tugas administratif harian.</p></div>
</div>

<footer class="pub-footer animate-fade-up">
    <span class="pub-footer-copy">&copy; <?= date('Y') ?> Alfatih Digital Workspace. Crafted with <i class="fa-solid fa-heart" style="color:#ef4444;margin:0 6px;"></i></span>
    <a href="index.php?page=login" class="pub-footer-link">Akses Panel Admin <i class="fa-solid fa-shield-halved"></i></a>
</footer>
<?php } ?>
<script src="https://accounts.google.com/gsi/client" async defer></script>
<script>
    function handleGoogleLogin(response) {
        if (response.credential) {
            document.getElementById('googleCredential').value = response.credential;
            document.getElementById('googleLoginForm').submit();
        }
    }
</script>
<script>
    if('serviceWorker' in navigator){navigator.serviceWorker.register('sw.js').catch(()=>{});}
    // Intersection Observer for scroll animations
    document.addEventListener("DOMContentLoaded", function() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.animate-fade-up').forEach(el => {
            el.style.animationPlayState = 'paused';
            observer.observe(el);
        });
    });
</script>
</body>
</html>