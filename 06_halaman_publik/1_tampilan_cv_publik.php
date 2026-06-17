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
            --bg-color: #09090b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent-primary: #8b5cf6; 
            --accent-secondary: #0ea5e9; 
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.08);
        }
        
        html { scroll-behavior: smooth; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        a { color: inherit; text-decoration: none; }
        ::selection { background: var(--accent-primary); color: #fff; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-color); }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
        
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg-color);
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
            background: var(--accent-primary); top: -20vh; left: -10vw;
        }
        .glow-orb-2 {
            width: 40vw; height: 40vw; max-width: 500px; max-height: 500px;
            background: var(--accent-secondary); bottom: -10vh; right: -10vw;
            animation-delay: -5s;
        }

        /* Navigation */
        .pub-nav {
            position: fixed; top: 0; left: 0; right: 0; height: 80px;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 5%; z-index: 100;
            background: rgba(9, 9, 11, 0.7);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            animation: fadeIn 1s ease-out forwards;
        }
        .pub-nav-back {
            display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px;
            border-radius: 12px; background: rgba(255,255,255,0.05); color: var(--text-muted);
            border: 1px solid var(--glass-border); transition: all 0.3s;
        }
        .pub-nav-back:hover { color: #fff; background: var(--accent-primary); transform: translateX(-3px); }
        .pub-nav-logo { display: flex; align-items: center; gap: 12px; transition: transform 0.3s; }
        .pub-nav-logo:hover { transform: scale(1.05); }
        .pub-nav-logo img { height: 36px; object-fit: contain; }
        .pub-nav-logo span {
            font-family: 'Outfit', sans-serif; font-size: 1.3rem; font-weight: 800;
            background: linear-gradient(to right, #fff, #cbd5e1);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .pub-nav-links { display: flex; align-items: center; gap: 12px; }
        .pub-nav-links a.nav-item {
            padding: 10px 18px; font-size: 0.9rem; font-weight: 600;
            color: var(--text-muted); transition: all 0.3s; border-radius: 10px;
        }
        .pub-nav-links a.nav-item:hover { color: #fff; background: var(--glass-bg); transform: translateY(-2px); }
        .pub-nav-links a.nav-cta {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: #fff; padding: 12px 28px; font-size: 0.9rem; font-weight: 700;
            border-radius: 12px; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .pub-nav-links a.nav-cta:hover { transform: scale(1.05); box-shadow: 0 8px 25px rgba(139, 92, 246, 0.5); }

        /* Typography & Utilities */
        .text-gradient {
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .text-accent {
            background: linear-gradient(to right, var(--accent-secondary), var(--accent-primary));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }

        /* Hero Section */
        .ed-hero {
            min-height: 100vh; display: flex; flex-direction: column; justify-content: center;
            padding: 140px 5% 80px; position: relative; text-align: center;
        }
        .ed-hero-eyebrow {
            display: inline-block; padding: 8px 20px; background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border); border-radius: 30px;
            font-size: 0.8rem; font-weight: 700; letter-spacing: 2px; color: var(--text-muted);
            margin: 0 auto 30px; text-transform: uppercase;
            backdrop-filter: blur(10px);
        }
        .ed-hero-title {
            font-family: 'Outfit', sans-serif; font-size: clamp(3.5rem, 8vw, 7rem);
            font-weight: 900; line-height: 1.05; margin-bottom: 28px;
            text-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .ed-hero-sub {
            font-size: 1.25rem; color: var(--text-muted); max-width: 650px;
            margin: 0 auto 48px; line-height: 1.7;
        }
        .ed-hero-ctas { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; }
        
        .btn-ed-primary {
            padding: 18px 40px; border-radius: 14px;
            background: linear-gradient(135deg, var(--accent-primary), #6d28d9);
            color: #fff; font-size: 1rem; font-weight: 700;
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4); transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            display: inline-flex; align-items: center; gap: 12px;
        }
        .btn-ed-primary:hover { transform: translateY(-4px) scale(1.02); box-shadow: 0 15px 35px rgba(139, 92, 246, 0.6); }
        
        .btn-ed-ghost {
            padding: 18px 40px; border-radius: 14px;
            background: rgba(255,255,255,0.02); color: var(--text-main); font-size: 1rem; font-weight: 700;
            border: 1px solid var(--glass-border); transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            display: inline-flex; align-items: center; gap: 12px; backdrop-filter: blur(10px);
        }
        .btn-ed-ghost:hover { background: rgba(255,255,255,0.1); transform: translateY(-4px); border-color: rgba(255,255,255,0.2); }

        /* Marquee */
        .ed-hero-marquee-wrap {
            position: absolute; bottom: 0; left: 0; right: 0; overflow: hidden;
            border-top: 1px solid var(--glass-border); padding: 20px 0; background: rgba(0,0,0,0.4);
            backdrop-filter: blur(12px);
        }
        .ed-marquee { display: flex; gap: 50px; white-space: nowrap; animation: marquee 30s linear infinite; }
        .ed-marquee span { font-family: 'Outfit', sans-serif; font-size: 0.9rem; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: rgba(255,255,255,0.5); }
        @keyframes marquee { from { transform: translateX(0) } to { transform: translateX(-50%) } }

        /* Features Strip */
        .preview-strip { display: grid; grid-template-columns: repeat(3, 1fr); border-bottom: 1px solid var(--glass-border); background: rgba(0,0,0,0.2); }
        .preview-panel { padding: 100px 5%; border-right: 1px solid var(--glass-border); transition: all 0.4s ease; position: relative; overflow: hidden; }
        .preview-panel:last-child { border-right: none; }
        .preview-panel:hover { background: rgba(255,255,255,0.03); transform: translateY(-5px); }
        .preview-panel::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(to right, var(--accent-secondary), var(--accent-primary)); opacity: 0; transition: opacity 0.4s; }
        .preview-panel:hover::before { opacity: 1; }
        .preview-panel-icon {
            font-size: 3rem; margin-bottom: 30px;
            background: linear-gradient(135deg, var(--accent-secondary), var(--accent-primary));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: inline-block;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .preview-panel:hover .preview-panel-icon { transform: scale(1.1) rotate(-5deg); }
        .preview-panel-title { font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 800; margin-bottom: 16px; }
        .preview-panel-desc { font-size: 1.05rem; color: var(--text-muted); line-height: 1.8; }

        /* Talent Directory */
        .ed-section { padding: 120px 5%; position: relative; }
        .ed-section-head { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 80px; flex-wrap: wrap; gap: 24px; }
        .ed-section-label { font-size: 0.85rem; font-weight: 800; letter-spacing: 4px; text-transform: uppercase; color: var(--accent-secondary); display: block; margin-bottom: 12px; }
        .ed-section-title { font-family: 'Outfit', sans-serif; font-size: 3.5rem; font-weight: 900; line-height: 1.1; }
        .ed-section-sub { font-size: 1rem; color: var(--text-main); font-weight: 600; background: rgba(255,255,255,0.1); padding: 12px 24px; border-radius: 30px; border: 1px solid var(--glass-border); backdrop-filter: blur(10px); }
        
        .talent-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 32px; }
        .talent-card {
            background: rgba(255, 255, 255, 0.03); border: 1px solid var(--glass-border);
            border-radius: 20px; padding: 40px; backdrop-filter: blur(16px);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); position: relative; overflow: hidden;
            text-decoration: none; display: block;
        }
        .talent-card::before {
            content: ''; position: absolute; inset: 0; border-radius: 20px;
            padding: 2px; background: linear-gradient(135deg, var(--accent-secondary), var(--accent-primary));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor; mask-composite: exclude; opacity: 0; transition: opacity 0.4s;
        }
        .talent-card:hover { transform: translateY(-10px) scale(1.02); background: rgba(255,255,255,0.06); box-shadow: 0 30px 60px rgba(0,0,0,0.5); }
        .talent-card:hover::before { opacity: 1; }
        .talent-card-num { position: absolute; top: 30px; right: 30px; font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 900; color: rgba(255,255,255,0.06); transition: color 0.4s; }
        .talent-card:hover .talent-card-num { color: var(--accent-primary); opacity: 0.5; }
        
        .talent-card-avatar { width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 3px solid rgba(255,255,255,0.2); margin-bottom: 24px; box-shadow: 0 10px 20px rgba(0,0,0,0.3); transition: transform 0.4s; }
        .talent-card:hover .talent-card-avatar { transform: scale(1.1) rotate(5deg); border-color: var(--accent-secondary); }
        
        .talent-card-name { font-family: 'Outfit', sans-serif; font-size: 1.6rem; font-weight: 800; margin-bottom: 8px; color: #fff; }
        .talent-card-profesi { font-size: 0.85rem; font-weight: 700; letter-spacing: 1px; color: var(--accent-secondary); margin-bottom: 20px; text-transform: uppercase; }
        .talent-card-summary { font-size: 0.95rem; color: var(--text-muted); line-height: 1.7; margin-bottom: 30px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .talent-card-tags { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 30px; }
        .talent-card-tag { font-size: 0.75rem; font-weight: 600; padding: 8px 14px; background: rgba(255,255,255,0.08); border-radius: 8px; color: #f1f5f9; transition: background 0.3s; }
        .talent-card:hover .talent-card-tag { background: rgba(255,255,255,0.15); }
        .talent-card-link { display: inline-flex; align-items: center; gap: 10px; font-size: 0.95rem; font-weight: 700; color: var(--accent-primary); transition: all 0.3s; }
        .talent-card:hover .talent-card-link { gap: 16px; color: var(--accent-secondary); }

        .talent-empty { padding: 120px 5%; text-align: center; color: var(--text-muted); background: rgba(255,255,255,0.02); border-radius: 24px; border: 2px dashed var(--glass-border); }

        /* Login Page */
        .login-gate { position: fixed; top: 0; bottom: 0; width: 50vw; background: #0a0a0f; z-index: 9999; animation: gateOpen 1.2s cubic-bezier(0.8, 0, 0.2, 1) forwards; animation-delay: 0.8s; pointer-events: none; display: flex; align-items: center; justify-content: flex-end; }
        .login-gate.right { right: 0; left: auto; justify-content: flex-start; animation-name: gateOpenRight; }
        .login-gate::before { content: ''; position: absolute; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, transparent, var(--accent-primary), transparent); box-shadow: 0 0 20px var(--accent-primary); right: 0; }
        .login-gate.right::before { right: auto; left: 0; background: linear-gradient(to bottom, transparent, var(--accent-secondary), transparent); box-shadow: 0 0 20px var(--accent-secondary); }
        .login-gate-logo { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10000; color: #fff; font-family: 'Outfit', sans-serif; font-size: 2.5rem; font-weight: 900; letter-spacing: 10px; animation: logoFadeOut 0.5s ease forwards; animation-delay: 0.6s; pointer-events: none; text-shadow: 0 0 20px rgba(139, 92, 246, 0.5); }
        
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
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(9, 9, 11, 0.98));
        }
        .login-left::after {
            content: ''; position: absolute; inset: 0; background: url('https://www.transparenttextures.com/patterns/cubes.png') repeat; opacity: 0.15; pointer-events: none;
        }
        .login-left-decor { font-family: 'Outfit', sans-serif; font-size: 20rem; font-weight: 900; color: rgba(255,255,255,0.02); position: absolute; top: -100px; left: -80px; line-height: 1; user-select: none; animation: pulseDecor 4s infinite alternate; }
        .login-left-title { font-family: 'Outfit', sans-serif; font-size: 4rem; font-weight: 900; line-height: 1.1; margin-bottom: 24px; position: relative; z-index: 1; text-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .login-left-sub { font-size: 1.2rem; color: var(--text-muted); line-height: 1.8; position: relative; z-index: 1; max-width: 480px; }
        
        .login-right { display: flex; flex-direction: column; justify-content: center; padding: 8% 10%; position: relative; z-index: 2; background: rgba(0,0,0,0.6); backdrop-filter: blur(30px); overflow: hidden; }
        .login-right::before { content: ''; position: absolute; width: 400px; height: 400px; background: radial-gradient(circle, var(--accent-primary), transparent 70%); top: -100px; right: -100px; opacity: 0.15; filter: blur(60px); z-index: -1; animation: floatBlob 8s infinite alternate ease-in-out; }
        .login-right::after { content: ''; position: absolute; width: 300px; height: 300px; background: radial-gradient(circle, var(--accent-secondary), transparent 70%); bottom: -50px; left: -50px; opacity: 0.15; filter: blur(50px); z-index: -1; animation: floatBlob 6s infinite alternate-reverse ease-in-out; }
        .login-right-back {
            position: absolute; top: 40px; right: 40px; font-size: 0.9rem; font-weight: 700;
            color: var(--text-muted); display: inline-flex; align-items: center; gap: 10px; transition: all 0.3s;
            background: rgba(255,255,255,0.05); padding: 12px 24px; border-radius: 12px; border: 1px solid var(--glass-border); z-index: 10;
        }
        .login-right-back:hover { color: #fff; background: rgba(255,255,255,0.15); transform: translateX(-5px); }
        .login-box { background: rgba(255,255,255,0.03); padding: 50px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 30px 60px -12px rgba(0,0,0,0.6); position: relative; z-index: 10; backdrop-filter: blur(10px); }
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
            width: 100%; padding: 16px 20px; background: rgba(0,0,0,0.4); border: 2px solid rgba(255,255,255,0.1);
            border-radius: 12px; color: #fff; font-size: 1.05rem; font-family: 'Inter', sans-serif; outline: none; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .ed-form-input::placeholder { color: #64748b; font-weight: 400; }
        .ed-form-input:focus { border-color: var(--accent-primary); box-shadow: 0 0 0 6px rgba(139, 92, 246, 0.15); background: rgba(0,0,0,0.6); transform: scale(1.02); }
        .ed-form-input.err { border-color: #ef4444; }
        .ed-form-input.err:focus { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0.15); }
        
        .btn-ed-submit {
            width: 100%; padding: 18px; background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: #fff; font-size: 1.05rem; font-weight: 800; border-radius: 12px; border: none; cursor: pointer;
            font-family: 'Inter', sans-serif; margin-top: 20px; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); 
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4); display: flex; align-items: center; justify-content: center; gap: 12px;
        }
        .btn-ed-submit:hover { transform: translateY(-4px) scale(1.02); box-shadow: 0 12px 35px rgba(139, 92, 246, 0.6); }

        /* Footer */
        .pub-footer { padding: 60px 5%; display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--glass-border); background: rgba(0,0,0,0.6); }
        .pub-footer-copy { font-size: 0.95rem; color: var(--text-muted); font-weight: 500; }
        .pub-footer-link { font-size: 0.95rem; font-weight: 700; color: var(--text-main); display: inline-flex; align-items: center; gap: 10px; transition: all 0.3s; padding: 12px 24px; background: rgba(255,255,255,0.05); border-radius: 12px; }
        .pub-footer-link:hover { color: #fff; background: var(--accent-primary); transform: translateY(-3px); box-shadow: 0 10px 20px rgba(139, 92, 246, 0.3); }

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
            .pub-nav-links a.nav-item { display: none; }
            .pub-nav-links a.nav-cta { padding: 10px 18px; font-size: 0.85rem; }
            .ed-hero { padding: 120px 20px 60px; }
            .ed-hero-title { font-size: 2.8rem; }
            .ed-hero-sub { font-size: 1.05rem; margin-bottom: 32px; }
            .ed-hero-ctas { flex-direction: column; width: 100%; gap: 16px; }
            .btn-ed-primary, .btn-ed-ghost { width: 100%; justify-content: center; padding: 16px; font-size: 1rem; }
            .ed-section { padding: 80px 20px; }
            .ed-section-head { flex-direction: column; align-items: flex-start; gap: 16px; margin-bottom: 40px; }
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
        <a href="#features" class="nav-item">Features</a>
        <a href="#talent" class="nav-item">Talents</a>
        <a href="index.php?page=login" class="nav-cta">Login Workspace</a>
        <?php } else { ?>
        <a href="index.php" class="nav-cta" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: var(--text-muted); box-shadow: none;"><i class="fa-solid fa-house"></i> Beranda</a>
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
                <button onclick="toggleAuth('login')" id="tab_login" style="flex:1; padding: 12px; background:transparent; border:none; border-bottom: 2px solid var(--accent-primary); color: #fff; font-weight:600; cursor:pointer;">Masuk</button>
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
                    <select name="profesi" class="ed-form-input" required style="background: rgba(0,0,0,0.5); color: white;">
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
                    document.getElementById('tab_login').style.color = '#fff';
                    document.getElementById('tab_register').style.borderBottomColor = 'transparent';
                    document.getElementById('tab_register').style.color = 'var(--text-muted)';
                } else {
                    document.getElementById('formLogin').style.display = 'none';
                    document.getElementById('formRegister').style.display = 'block';
                    document.getElementById('tab_register').style.borderBottomColor = 'var(--accent-primary)';
                    document.getElementById('tab_register').style.color = '#fff';
                    document.getElementById('tab_login').style.borderBottomColor = 'transparent';
                    document.getElementById('tab_login').style.color = 'var(--text-muted)';
                }
            }
            </script>
            
            <div style="margin: 24px 0; display: flex; align-items: center; text-align: center; color: var(--text-muted); font-size: 0.85rem;">
                <div style="flex: 1; height: 1px; background: rgba(255,255,255,0.1);"></div>
                <span style="margin: 0 16px;">ATAU GUNAKAN GOOGLE</span>
                <div style="flex: 1; height: 1px; background: rgba(255,255,255,0.1);"></div>
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
                 data-theme="filled_black"
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
    <div class="ed-hero-eyebrow animate-fade-up">Est. 2026 &mdash; Next Gen Platform</div>
    <h1 class="ed-hero-title animate-fade-up delay-100">DIGITAL WORKSPACE.<br><em class="text-gradient">Organized.</em></h1>
    <p class="ed-hero-sub animate-fade-up delay-200">Ruang kerja digital premium untuk mengelola file secara cerdas, mendesain CV profesional, dan mempublikasikan Portfolio Anda ke dunia.</p>
    <div class="ed-hero-ctas animate-fade-up delay-300">
        <a href="index.php?page=login" class="btn-ed-primary">Masuk ke Dasbor <i class="fa-solid fa-arrow-right"></i></a>
        <a href="#talent" class="btn-ed-ghost">Jelajahi Talent <i class="fa-solid fa-arrow-down"></i></a>
    </div>
    <div class="ed-hero-marquee-wrap animate-fade-up delay-400">
        <div class="ed-marquee">
            <?php for($i=0;$i<6;$i++){ ?><span>Smart File Manager</span><span>&bull;</span><span>Interactive CV Builder</span><span>&bull;</span><span>Global Portfolio</span><span>&bull;</span><span>Talent Directory</span><span>&bull;</span><span>RBAC Security</span><span>&bull;</span><?php } ?>
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
        <strong style="font-size:1.4rem;color:#fff;display:block;margin-bottom:12px;">Ruang Kosong Menunggu Bintang.</strong>
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