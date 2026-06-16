<?php
// +------------------------------------------------------------------------------+
// |  FILE: 2_tampilan_dasbor.php                                                 |
// |                                                                              |
// |  DESKRIPSI:                                                                  |
// |  File ini berisi kerangka dasar HTML untuk seluruh halaman Dasbor (Workspace).|
// |  Memuat tag <head>, gaya CSS utama, dan struktur layout (Navbar/Sidebar).    |
// +------------------------------------------------------------------------------+
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Workspace &mdash; <?= h($display_name) ?></title>
    <meta name="theme-color" content="#080b14">
    <meta name="application-name" content="Alfatih Workspace">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Workspace">
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="07_aset_visual/images/LOGO_GAWE.svg">
    <link rel="icon" type="image/svg+xml" href="07_aset_visual/images/LOGO_GAWE.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <style>
/* âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
   WORKSPACE DASHBOARD â Dark SaaS Premium Edition v3
   Deep Indigo + Violet + Cyan accents, Bento Grid, Glass panels
   âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ */

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
::selection{background:#6366f1;color:#fff;}

:root {
  /* Core dark palette */
  --bg:          #080b14;
  --bg-2:        #0d1117;
  --surface:     #111827;
  --surface-2:   #1a2235;
  --surface-3:   #1f2d44;
  --glass:       rgba(17,24,39,0.8);
  --glass-border:rgba(255,255,255,0.08);
  --ink:         #f0f4ff;
  --ink-2:       #a8b3cf;
  --text-main:   #f0f4ff;
  --text-secondary:#c8d3ef;
  --text-muted:  #5a6888;
  --border:      rgba(255,255,255,0.07);
  --border-md:   rgba(255,255,255,0.13);
  --border-dark: rgba(255,255,255,0.2);

  /* Accent system */
  --accent:      #6366f1;
  --accent-2:    #8b5cf6;
  --accent-soft: rgba(99,102,241,0.12);
  --success:     #10b981;
  --success-bg:  rgba(16,185,129,0.1);
  --warning:     #f59e0b;
  --warning-bg:  rgba(245,158,11,0.1);
  --danger:      #f43f5e;
  --danger-bg:   rgba(244,63,94,0.1);
  --superadmin:  #f59e0b;
  --blue:        #06b6d4;
  --blue-bg:     rgba(6,182,212,0.1);

  /* Glow effects */
  --glow-sm:    0 0 0 3px rgba(99,102,241,0.15);
  --glow-md:    0 0 0 4px rgba(99,102,241,0.25);
  --glow-accent:0 0 30px rgba(99,102,241,0.2);

  /* Shadows */
  --shadow-xs:  0 1px 2px rgba(0,0,0,.3);
  --shadow-sm:  0 1px 4px rgba(0,0,0,.4),0 2px 8px rgba(0,0,0,.3);
  --shadow-md:  0 4px 12px rgba(0,0,0,.5),0 2px 4px rgba(0,0,0,.3);
  --shadow-lg:  0 8px 32px rgba(0,0,0,.5),0 2px 8px rgba(0,0,0,.3);
  --shadow-xl:  0 20px 60px rgba(0,0,0,.6),0 4px 16px rgba(0,0,0,.4);
  --shadow-inset: inset 0 1px 2px rgba(0,0,0,.3);

  /* Layout */
  --nav-h:     60px;
  --sidebar-w: 260px;
  --radius-sm: 10px;
  --radius-md: 14px;
  --radius-lg: 20px;
  --radius-xl: 28px;

  /* Typography */
  --f-display: 'Syne',system-ui,sans-serif;
  --f-body:    'Inter',system-ui,sans-serif;

  /* Animation */
  --tr:            0.2s ease;
  --tr-spring:     0.45s cubic-bezier(.16,1,.3,1);
  --tr-bounce:     0.5s cubic-bezier(.34,1.56,.64,1);
  --ease-out-expo: cubic-bezier(.16,1,.3,1);
}

/* ââ BASE ââ */
body{
  background:var(--bg);
  color:var(--text-main);
  font-family:var(--f-body);
  -webkit-font-smoothing:antialiased;
  -moz-osx-font-smoothing:grayscale;
  overflow-x:hidden;
}
body::before{
  content:'';
  position:fixed;inset:0;
  background:radial-gradient(ellipse 80% 60% at 20% 10%,rgba(99,102,241,.08) 0%,transparent 60%),
             radial-gradient(ellipse 60% 50% at 80% 80%,rgba(139,92,246,.06) 0%,transparent 60%);
  pointer-events:none;z-index:0;
}
a{color:inherit;text-decoration:none;}
::-webkit-scrollbar{width:5px;height:5px;}
::-webkit-scrollbar-track{background:var(--bg-2);}
::-webkit-scrollbar-thumb{background:rgba(99,102,241,.3);border-radius:8px;}
::-webkit-scrollbar-thumb:hover{background:rgba(99,102,241,.5);}

/* ââ KEYFRAMES ââ */
@keyframes fadeIn      {from{opacity:0}to{opacity:1}}
@keyframes fadeUp      {from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}
@keyframes slideDown   {from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:none}}
@keyframes scaleIn     {from{opacity:0;transform:scale(.92)}to{opacity:1;transform:none}}
@keyframes modalIn     {from{opacity:0;transform:scale(.96) translateY(14px)}to{opacity:1;transform:none}}
@keyframes toastIn     {from{opacity:0;transform:translate(-50%,14px)}to{opacity:1;transform:translate(-50%,0)}}
@keyframes toastOut    {to{opacity:0;transform:translate(-50%,14px)}}
@keyframes stagger-in  {from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
@keyframes glow-pulse  {0%,100%{box-shadow:0 0 0 0 rgba(99,102,241,0)}50%{box-shadow:0 0 0 8px rgba(99,102,241,.15)}}
@keyframes spin        {to{transform:rotate(360deg)}}
@keyframes skeleton    {0%{background-position:200% 0}to{background-position:-200% 0}}
@keyframes indigo-glow {0%,100%{box-shadow:0 0 20px rgba(99,102,241,.2);}50%{box-shadow:0 0 35px rgba(99,102,241,.4);}}

/* ââ STAGGER ANIMATION ââ */
.stagger-child{
  opacity:0;
  animation:stagger-in .45s var(--ease-out-expo) both;
}
.stagger-child:nth-child(1){animation-delay:.04s}
.stagger-child:nth-child(2){animation-delay:.08s}
.stagger-child:nth-child(3){animation-delay:.12s}
.stagger-child:nth-child(4){animation-delay:.16s}
.stagger-child:nth-child(5){animation-delay:.20s}
.stagger-child:nth-child(6){animation-delay:.24s}
.stagger-child:nth-child(7){animation-delay:.28s}
.stagger-child:nth-child(8){animation-delay:.32s}
.stagger-child:nth-child(9){animation-delay:.36s}
.stagger-child:nth-child(n+10){animation-delay:.40s}

/* ââ NAVBAR ââ */
.top-navbar{
  display:flex;align-items:center;justify-content:space-between;
  padding:0 24px;
  height:var(--nav-h);
  background:rgba(8,11,20,0.85);
  border-bottom:1px solid var(--border);
  backdrop-filter:blur(24px);
  -webkit-backdrop-filter:blur(24px);
  position:sticky;top:0;z-index:200;
  box-shadow:0 1px 0 var(--border),var(--shadow-xs);
  animation:fadeIn .4s ease both;
}
.header-left{display:flex;align-items:center;gap:12px;min-width:200px;}
.logo-mark{
  display:flex;align-items:center;gap:8px;
  cursor:pointer;
  transition:opacity .2s;
}
.logo-mark img{height:22px;}
.logo-mark span{
  font-family:var(--f-display);
  font-size:.82rem;font-weight:800;
  letter-spacing:1px;text-transform:uppercase;
  background:linear-gradient(90deg,var(--text-main),var(--ink-2));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.logo-mark:hover{opacity:.8;}
.header-center{flex:1;display:flex;justify-content:center;padding:0 20px;}
.search-bar{width:100%;max-width:520px;position:relative;}
.search-bar form{display:flex;}
.search-bar input{
  width:100%;
  padding:9px 16px 9px 38px;
  background:var(--surface-3);
  border:1.5px solid transparent;
  border-radius:var(--radius-sm);
  color:var(--text-main);
  font-size:.875rem;
  font-family:var(--f-body);
  outline:none;
  transition:border-color .2s,background .2s,box-shadow .2s;
}
.search-bar input:focus{
  background:var(--surface);
  border-color:var(--border-md);
  box-shadow:var(--shadow-sm),var(--glow-sm);
}
.search-bar input::placeholder{color:var(--text-muted);}
.search-bar i{
  position:absolute;left:12px;top:50%;
  transform:translateY(-50%);
  color:var(--text-muted);font-size:.82rem;
}
.header-right{display:flex;align-items:center;gap:4px;min-width:200px;justify-content:flex-end;}
.stats-badge{
  font-size:.7rem;
  color:var(--accent);
  font-weight:600;
  padding:5px 12px;
  background:var(--accent-soft);
  border:1px solid rgba(99,102,241,.2);
  border-radius:var(--radius-sm);
  letter-spacing:.3px;
  margin-right:4px;
}
.btn-icon{
  background:transparent;
  border:none;
  color:var(--text-muted);
  width:38px;height:38px;
  border-radius:var(--radius-sm);
  cursor:pointer;
  display:flex;align-items:center;justify-content:center;
  transition:all var(--tr);
  font-size:.9rem;
  position:relative;
}
.btn-icon:hover{
  background:var(--surface-3);
  color:var(--text-main);
}
.btn-icon:active{transform:scale(.92);}
.btn-menu{border:none;}
.sa-badge{
  font-size:.58rem;
  font-weight:800;
  letter-spacing:.8px;
  text-transform:uppercase;
  padding:3px 8px;
  background:linear-gradient(135deg,var(--superadmin),#d97706);
  color:#080b14;
  border-radius:var(--radius-sm);
  box-shadow:0 2px 8px rgba(245,158,11,.4);
  animation:glow-pulse 3s ease infinite;
}
[data-tooltip]{position:relative;}
[data-tooltip]:hover::after{
  content:attr(data-tooltip);
  position:absolute;bottom:calc(100% + 8px);left:50%;
  transform:translateX(-50%);
  background:var(--ink);color:#fff;
  padding:5px 10px;
  font-size:.68rem;white-space:nowrap;
  z-index:1000;pointer-events:none;
  border-radius:6px;
  animation:fadeIn .15s ease;
}
.profile-container{position:relative;}
.avatar{
  width:34px;height:34px;
  object-fit:cover;
  cursor:pointer;
  border:2px solid transparent;
  border-radius:var(--radius-sm);
  transition:all .25s var(--ease-out-expo);
}
.avatar:hover{
  border-color:var(--accent);
  transform:scale(1.05);
  box-shadow:0 0 12px rgba(99,102,241,.4);
}
.profile-menu{
  display:none;
  position:absolute;right:0;top:calc(100% + 8px);
  background:var(--surface);
  min-width:240px;
  border:1px solid var(--border-md);
  border-radius:var(--radius-md);
  z-index:201;
  box-shadow:var(--shadow-xl),0 0 0 1px rgba(99,102,241,.1);
  overflow:hidden;
}
.profile-menu.show{display:block;animation:scaleIn .2s var(--ease-out-expo);}
.profile-header-info{
  padding:16px 18px;
  display:flex;align-items:center;gap:12px;
  border-bottom:1px solid var(--border);
  background:linear-gradient(135deg,var(--surface-2),var(--surface));
}
.profile-header-info img{
  width:40px;height:40px;
  object-fit:cover;
  border-radius:var(--radius-sm);
  border:1px solid var(--border-md);
}
.profile-header-info strong{font-size:.88rem;font-weight:700;display:block;color:var(--text-main);}
.profile-header-info span{font-size:.72rem;color:var(--accent);text-transform:capitalize;}
.profile-menu-links a,.profile-menu-links button{
  display:flex;align-items:center;gap:10px;
  padding:10px 18px;
  color:var(--text-main);
  font-size:.83rem;
  transition:background var(--tr),color var(--tr);
  width:100%;background:none;border:none;
  font-family:var(--f-body);cursor:pointer;text-align:left;
}
.profile-menu-links a:hover,.profile-menu-links button:hover{
  background:var(--accent-soft);
  color:var(--accent);
}
.profile-menu-links a i,.profile-menu-links button i{
  width:18px;text-align:center;
  color:var(--text-muted);font-size:.85rem;
}
.profile-menu-links a:hover i,.profile-menu-links button:hover i{color:var(--accent);}
.menu-divider{border:none;border-top:1px solid var(--border);}

/* ââ SIDEBAR ââ */
.sidebar{
  position:fixed;
  left:calc(-1 * var(--sidebar-w) - 20px);
  top:0;width:var(--sidebar-w);height:100vh;
  background:var(--surface);
  border-right:1px solid var(--border-md);
  z-index:300;
  transition:left var(--tr-spring),box-shadow .3s;
  padding-top:var(--nav-h);
  display:flex;flex-direction:column;
  overflow-y:auto;
  box-shadow:none;
}
.sidebar.active{
  left:0;
  box-shadow:var(--shadow-xl),4px 0 40px rgba(99,102,241,.1);
}
.sidebar-overlay{
  display:none;
  position:fixed;inset:0;
  background:rgba(0,0,0,.6);
  backdrop-filter:blur(6px);
  z-index:299;
}
.sidebar-overlay.active{display:block;animation:fadeIn .25s ease;}
.sidebar-section{padding:8px 0;}
.sidebar-section-label{
  font-size:.6rem;
  font-weight:800;
  letter-spacing:2px;
  text-transform:uppercase;
  color:var(--text-muted);
  padding:14px 20px 6px;
}
.nav-item{
  display:flex;align-items:center;gap:11px;
  padding:9px 16px 9px 20px;
  color:var(--text-muted);
  font-size:.83rem;font-weight:500;
  transition:all var(--tr);
  margin:1px 8px;
  border-radius:var(--radius-sm);
  position:relative;
}
.nav-item i{width:18px;text-align:center;font-size:.9rem;}
.nav-item:hover{
  background:var(--accent-soft);
  color:var(--accent);
  transform:translateX(3px);
}
.nav-item:hover i{color:var(--accent);}
.nav-item.active{
  background:linear-gradient(135deg,rgba(99,102,241,.2),rgba(139,92,246,.15));
  color:var(--accent);
  font-weight:700;
  box-shadow:var(--shadow-md),inset 0 0 0 1px rgba(99,102,241,.2);
  border:1px solid rgba(99,102,241,.2);
}
.nav-item.active i{color:var(--accent);}
.nav-item.superadmin-item i{color:var(--superadmin);}
.nav-item.superadmin-item:hover{
  background:var(--warning-bg);
  color:var(--superadmin);
}
.nav-item.superadmin-item.active{
  background:linear-gradient(135deg,rgba(245,158,11,.2),rgba(245,158,11,.1));
  color:var(--superadmin);
  border-color:rgba(245,158,11,.3);
}
.sidebar-storage{
  padding:16px;margin-top:auto;
  border-top:1px solid var(--border);
  background:linear-gradient(135deg,var(--surface-2),var(--surface));
}
.storage-label{
  font-size:.62rem;font-weight:700;
  letter-spacing:1px;text-transform:uppercase;
  color:var(--text-muted);margin-bottom:8px;
}
.storage-bar{
  height:4px;
  background:var(--surface-3);
  border-radius:4px;
  margin-bottom:6px;
  overflow:hidden;
}
.storage-bar-fill{
  height:100%;
  background:linear-gradient(90deg,var(--accent),var(--accent-2));
  border-radius:4px;
  transition:width .8s var(--ease-out-expo);
  box-shadow:0 0 8px rgba(99,102,241,.5);
}
.storage-text{font-size:.75rem;color:var(--text-muted);font-weight:500;}

/* ââ MAIN LAYOUT ââ */
.main-wrapper{
  display:flex;
  height:calc(100vh - var(--nav-h));
  overflow:hidden;
}
.content-area{
  flex:1;padding:0;
  overflow-y:auto;
  background:var(--bg);
}

/* ââ ALERT BAR ââ */
.alert-bar{
  padding:12px 28px;
  display:flex;align-items:center;gap:10px;
  font-size:.84rem;font-weight:600;
  border-bottom:1px solid transparent;
  animation:slideDown .3s var(--ease-out-expo);
}
.alert-bar.success{background:var(--success-bg);color:var(--success);border-color:#bbf7d0;}
.alert-bar.error{background:var(--danger-bg);color:var(--danger);border-color:#fecaca;}

/* ââ PAGE HEADER ââ */
.page-header{
  padding:28px 32px 20px;
  border-bottom:1px solid var(--border);
  display:flex;align-items:flex-end;justify-content:space-between;
  flex-wrap:wrap;gap:14px;
  background:var(--surface);
}
.page-eyebrow{
  font-size:.6rem;font-weight:800;
  letter-spacing:2px;text-transform:uppercase;
  color:var(--text-muted);margin-bottom:4px;
}
.page-title{
  font-family:var(--f-display);
  font-size:1.8rem;font-weight:900;letter-spacing:-.8px;
}
.page-sub{font-size:.84rem;color:var(--text-muted);margin-top:3px;}
.page-actions{display:flex;gap:8px;}
.btn-primary{
  padding:9px 20px;
  background:var(--ink);color:#fff;
  font-size:.76rem;font-weight:700;letter-spacing:.3px;text-transform:uppercase;
  border:none;
  border-radius:var(--radius-sm);
  cursor:pointer;font-family:var(--f-body);
  transition:background var(--tr),box-shadow var(--tr),transform .15s;
  display:inline-flex;align-items:center;gap:7px;
  box-shadow:var(--shadow-sm);
}
.btn-primary:hover{background:#222;box-shadow:var(--shadow-md);}
.btn-primary:active{transform:scale(.97);}
.btn-ghost{
  padding:9px 20px;
  background:var(--surface);color:var(--text-main);
  font-size:.76rem;font-weight:700;letter-spacing:.3px;text-transform:uppercase;
  border:1.5px solid var(--border-md);
  border-radius:var(--radius-sm);
  cursor:pointer;font-family:var(--f-body);
  transition:all var(--tr);
  display:inline-flex;align-items:center;gap:7px;
}
.btn-ghost:hover{border-color:var(--border-dark);background:var(--surface-2);}
.btn-ghost:active{transform:scale(.97);}

/* ââ BENTO DASHBOARD ââ */
.dash-inner{padding:28px 32px;}
.bento-grid{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:16px;
  margin-bottom:24px;
}
.bento-card{
  background:linear-gradient(145deg, rgba(30,41,59,0.5), rgba(15,23,42,0.8));
  backdrop-filter:blur(16px);
  border-radius:var(--radius-lg);
  border:1px solid rgba(255,255,255,0.06);
  padding:24px;
  transition:transform .35s var(--ease-out-expo),box-shadow .35s,border-color .35s;
  position:relative;
  overflow:hidden;
  display:flex;
  align-items:center;
  gap:18px;
}
.bento-card::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(circle at top right,rgba(99,102,241,.15),transparent 60%);
  opacity:0;transition:opacity .4s;pointer-events:none;
}
.bento-card:hover{
  transform:translateY(-4px);
  box-shadow:0 12px 30px rgba(0,0,0,0.5), inset 0 0 0 1px rgba(99,102,241,0.2);
  border-color:rgba(99,102,241,0.3);
}
.bento-card:hover::before{opacity:1;}
.bento-card-icon{
  width:48px;height:48px;
  border-radius:12px;
  display:flex;align-items:center;justify-content:center;
  font-size:1.2rem;flex-shrink:0;
}
.bento-card-icon.dark{
  background:linear-gradient(135deg,var(--accent),var(--accent-2));
  color:#fff;
  box-shadow:0 6px 16px rgba(99,102,241,.4);
}
.bento-card-icon.light{
  background:rgba(99,102,241,0.1);
  color:var(--accent);
  border:1px solid rgba(99,102,241,.2);
}
.stat-info{flex:1;min-width:0;}
.stat-label{
  font-size:.65rem;font-weight:800;
  letter-spacing:1.5px;text-transform:uppercase;
  color:var(--text-muted);margin-bottom:4px;
}
.stat-value{
  font-family:var(--f-display);
  font-size:1.85rem;font-weight:900;
  letter-spacing:-1px;line-height:1;
  background:linear-gradient(135deg,#fff,#a8b3cf);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  margin-bottom:4px;
}
.stat-sub{font-size:.7rem;color:rgba(255,255,255,0.4);}

/* Greeting bento block */
.greeting-strip{
  background:linear-gradient(120deg, #0f172a 0%, #020617 100%);
  position:relative;
  overflow:hidden;
  padding:36px 40px;
  border-bottom:1px solid rgba(255,255,255,0.05);
}
.greeting-strip::before{
  content:'';position:absolute;top:-50%;left:-10%;width:600px;height:600px;
  background:radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 60%);
  pointer-events:none;
}
.greeting-strip::after{
  content:'';position:absolute;bottom:-40%;right:-10%;width:500px;height:500px;
  background:radial-gradient(circle, rgba(139,92,246,0.1) 0%, transparent 60%);
  pointer-events:none;
}
.greeting-content{position:relative;z-index:2;}
.greeting-label{
  font-size:.65rem;font-weight:800;
  letter-spacing:2px;text-transform:uppercase;
  color:var(--success);margin-bottom:12px;
  display:inline-flex;align-items:center;gap:8px;
  background:rgba(16,185,129,0.1);
  padding:4px 12px;border-radius:20px;
  border:1px solid rgba(16,185,129,0.2);
}
.pulse-dot{
  width:6px;height:6px;border-radius:50%;background:var(--success);
  box-shadow:0 0 0 0 rgba(16,185,129,0.4);
  animation:pulse-dot 2s infinite;
}
@keyframes pulse-dot{0%{transform:scale(0.95);box-shadow:0 0 0 0 rgba(16,185,129,0.7)}70%{transform:scale(1);box-shadow:0 0 0 6px rgba(16,185,129,0)}100%{transform:scale(0.95);box-shadow:0 0 0 0 rgba(16,185,129,0)}}
.greeting-name{
  font-family:var(--f-display);
  font-size:2.4rem;font-weight:900;
  letter-spacing:-1px;margin-bottom:8px;
  color:#fff;text-shadow:0 2px 10px rgba(0,0,0,0.5);
}
.greeting-sub{font-size:.88rem;color:rgba(255,255,255,.5);margin-bottom:24px;}
.greeting-actions{display:flex;gap:10px;flex-wrap:wrap;}
.gqa-btn{
  padding:10px 20px;
  font-size:.75rem;font-weight:700;
  letter-spacing:.3px;
  border:1px solid rgba(255,255,255,0.1);
  color:rgba(255,255,255,.8);cursor:pointer;
  background:rgba(255,255,255,0.03);
  font-family:var(--f-body);
  transition:all .25s var(--ease-out-expo);
  display:inline-flex;align-items:center;gap:8px;
  border-radius:30px;
  backdrop-filter:blur(10px);
}
.gqa-btn:hover{background:rgba(255,255,255,0.08);color:#fff;border-color:rgba(255,255,255,0.2);transform:translateY(-2px);}
.gqa-btn:active{transform:scale(.96);}
.gqa-btn.dark-inv{
  background:linear-gradient(135deg,var(--accent),var(--accent-2));
  color:#fff;border-color:transparent;
  box-shadow:0 4px 16px rgba(99,102,241,.3);
}
.gqa-btn.dark-inv:hover{box-shadow:0 8px 24px rgba(99,102,241,.5);transform:translateY(-2px);}

/* ââ EDITORIAL CARDS ââ */
.ed-card{
  background:linear-gradient(145deg, rgba(30,41,59,0.3), rgba(15,23,42,0.5));
  backdrop-filter:blur(16px);
  border-radius:var(--radius-lg);
  border:1px solid rgba(255,255,255,0.06);
  overflow:hidden;
  box-shadow:var(--shadow-sm);
  display:flex;flex-direction:column;
  transition:box-shadow .2s, border-color .2s;
}
.ed-card:hover{border-color:rgba(255,255,255,0.15);box-shadow:var(--shadow-md);}
.ed-card-head{
  display:flex;align-items:center;justify-content:space-between;
  padding:20px 24px;
  border-bottom:1px solid rgba(255,255,255,0.04);
  background:rgba(0,0,0,0.15);
}
.ed-card-head h3{
  font-size:.85rem;font-weight:800;
  letter-spacing:.5px;text-transform:uppercase;
  display:flex;align-items:center;gap:10px;color:#fff;
}
.ed-card-head h3 i{color:var(--accent);}
.ed-card-head a{
  font-size:.7rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  color:var(--accent);
  transition:color .2s;
}
.ed-card-head a:hover{color:#fff;}
.pct-badge{
  background:rgba(99,102,241,0.15);
  color:var(--accent);
  padding:4px 10px;border-radius:20px;
  font-size:.75rem;font-weight:800;
  border:1px solid rgba(99,102,241,0.3);
}
.ed-card-body{padding:0;flex:1;}

/* Timeline List */
.timeline-list{padding:10px 24px;}
.timeline-item{
  display:flex;gap:16px;padding:14px 0;
  border-bottom:1px dashed rgba(255,255,255,0.08);
}
.timeline-item:last-child{border-bottom:none;}
.tl-icon{
  width:36px;height:36px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:.85rem;flex-shrink:0;
}
.tl-icon.login{background:rgba(16,185,129,0.15);color:var(--success);}
.tl-icon.upload{background:rgba(6,182,212,0.15);color:var(--blue);}
.tl-icon.delete{background:rgba(244,63,94,0.15);color:var(--danger);}
.tl-icon.other{background:rgba(255,255,255,0.08);color:var(--text-muted);}
.tl-content{flex:1;min-width:0;}
.tl-title{font-size:.82rem;font-weight:700;color:#fff;margin-bottom:2px;text-transform:capitalize;}
.tl-desc{font-size:.75rem;color:var(--text-secondary);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:4px;}
.tl-meta{font-size:.65rem;color:var(--text-muted);font-family:monospace;}

/* Check List */
.progress-wrap{
  height:4px;background:rgba(0,0,0,0.3);
  width:100%;
}
.progress-bar{
  height:100%;background:linear-gradient(90deg,var(--accent),var(--blue));
  box-shadow:0 0 10px rgba(99,102,241,0.5);
  transition:width .8s var(--ease-out-expo);
}
.profile-check-list{padding:12px 24px;}
.profile-check-row{
  display:flex;align-items:center;gap:14px;
  padding:14px 0;
  border-bottom:1px solid rgba(255,255,255,0.04);
}
.profile-check-row:last-child{border-bottom:none;}
.pcr-icon{
  width:38px;height:38px;border-radius:10px;
  background:rgba(255,255,255,0.05);color:var(--text-muted);
  display:flex;align-items:center;justify-content:center;
  font-size:1rem;flex-shrink:0;transition:all .3s;
}
.profile-check-row.is-done .pcr-icon{
  background:var(--success-bg);color:var(--success);
}
.pcr-info{flex:1;}
.pcr-title{font-size:.82rem;font-weight:700;color:#fff;}
.pcr-desc{font-size:.7rem;color:var(--text-muted);margin-top:2px;}
.pcr-action .btn-fill{
  font-size:.65rem;font-weight:800;letter-spacing:.5px;text-transform:uppercase;
  background:rgba(255,255,255,0.08);color:#fff;
  padding:6px 12px;border-radius:20px;
  transition:all .2s;
}
.pcr-action .btn-fill:hover{background:var(--accent);color:#fff;}
.text-success{color:var(--success);font-size:1.1rem;}

.empty-activity{
  padding:40px 20px;text-align:center;
  color:var(--text-muted);font-size:.85rem;line-height:1.6;
}
.empty-activity i{font-size:2rem;margin-bottom:12px;opacity:0.5;}

/* ââ WORKSPACE TOOLBAR ââ */
.toolbar-main{
  display:flex;align-items:center;justify-content:space-between;
  padding:10px 24px;
  border-bottom:1px solid var(--border);
  background:var(--surface);
}
.toolbar-left,.toolbar-right{display:flex;align-items:center;gap:6px;}
.btn-new{
  padding:9px 18px;
  background:var(--ink);color:#fff;
  font-size:.74rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  border:none;border-radius:var(--radius-sm);
  cursor:pointer;font-family:var(--f-body);
  transition:all var(--tr);
  display:flex;align-items:center;gap:8px;
  box-shadow:var(--shadow-sm);
}
.btn-new:hover{background:#222;box-shadow:var(--shadow-md);}
.btn-new:active{transform:scale(.97);}
.dropdown{position:relative;}
.dropdown-content{
  display:none;
  position:absolute;top:calc(100% + 8px);left:0;
  background:var(--surface);
  min-width:260px;
  border:1px solid var(--border-md);
  border-radius:var(--radius-md);
  z-index:150;
  box-shadow:var(--shadow-xl);
  overflow:hidden;
}
.dropdown:hover .dropdown-content{display:block;animation:scaleIn .18s var(--ease-out-expo);}
.dropdown-content button{
  display:flex;align-items:center;gap:14px;
  width:100%;padding:12px 16px;
  background:none;border:none;
  color:var(--text-main);font-size:.84rem;
  cursor:pointer;text-align:left;
  font-family:var(--f-body);
  border-bottom:1px solid var(--border);
  transition:background var(--tr);
}
.dropdown-content button:last-child{border-bottom:none;}
.dropdown-content button:hover{background:var(--surface-3);}
.dropdown-content button i{width:18px;text-align:center;color:var(--text-muted);}
.dd-desc{font-size:.73rem;color:var(--text-muted);}
.view-toggle{display:flex;border:1px solid var(--border-md);border-radius:var(--radius-sm);overflow:hidden;}
.view-toggle button{
  width:38px;height:38px;
  background:transparent;border:none;
  color:var(--text-muted);cursor:pointer;
  font-size:.9rem;transition:all var(--tr);
}
.view-toggle button:hover{background:var(--surface-3);color:var(--text-main);}
.view-toggle button.active{background:var(--ink);color:#fff;}
.breadcrumbs{
  padding:8px 24px;
  border-bottom:1px solid var(--border);
  font-size:.76rem;
  color:var(--text-muted);
  display:flex;align-items:center;gap:6px;
  flex-wrap:wrap;
  background:var(--surface);
}
.breadcrumbs a{color:var(--text-main);font-weight:600;}
.breadcrumbs a:hover{text-decoration:underline;}
.bulk-toolbar{
  display:none;align-items:center;gap:12px;
  padding:9px 24px;
  background:var(--ink);color:#fff;
  border-bottom:none;
}
.bulk-toolbar.active{display:flex;animation:slideDown .2s var(--ease-out-expo);}
.bulk-count{font-size:.78rem;font-weight:700;letter-spacing:.3px;}
.bulk-actions{display:flex;gap:6px;margin-left:auto;}
.bulk-btn{
  padding:6px 14px;
  background:rgba(255,255,255,.12);color:#fff;
  border:1px solid rgba(255,255,255,.25);
  border-radius:var(--radius-sm);
  font-size:.7rem;font-weight:700;letter-spacing:.3px;text-transform:uppercase;
  cursor:pointer;font-family:var(--f-body);
  transition:background .2s;
}
.bulk-btn:hover{background:rgba(255,255,255,.22);}
.bulk-btn.danger{color:#fca5a5;}
.filter-chips{
  display:flex;flex-wrap:wrap;gap:6px;
  padding:10px 24px;
  border-bottom:1px solid var(--border);
  background:var(--surface);
}
.chip{
  padding:5px 14px;
  font-size:.7rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  color:var(--text-muted);
  border:1px solid var(--border-md);
  border-radius:20px;
  transition:all var(--tr);
  display:flex;align-items:center;gap:6px;
}
.chip:hover{background:var(--surface-3);color:var(--text-main);}
.chip.active{
  background:var(--ink);color:#fff;
  border-color:var(--ink);
  box-shadow:var(--shadow-sm);
}

/* ââ FILE LISTING ââ */
#workspaceContainer{padding:0;}
.list-header{
  display:grid;
  grid-template-columns:28px 1fr 140px 120px 90px 44px;
  align-items:center;
  padding:8px 24px;
  border-bottom:1px solid var(--border);
  background:var(--surface-2);
  font-size:.62rem;font-weight:700;
  letter-spacing:.5px;text-transform:uppercase;
  color:var(--text-muted);
}
.list-header a{color:var(--text-muted);}
.list-header a:hover{color:var(--text-main);}
.item-card{
  display:grid;
  grid-template-columns:28px 1fr 140px 120px 90px 44px;
  align-items:center;
  padding:11px 24px;
  border-bottom:1px solid var(--border);
  cursor:pointer;
  transition:background .15s;
  position:relative;
  background:var(--surface);
}
.item-card:hover,.item-card.selected{background:var(--surface-3);}
.item-card.selected{
  background:var(--surface-2);
  box-shadow:inset 3px 0 0 var(--ink);
}
.item-card.dragging{opacity:.45;transform:scale(.98);}
.item-card.drag-over{
  background:#f0f0f3;
  box-shadow:inset 0 0 0 2px var(--ink);
}
.item-checkbox{
  width:16px;height:16px;
  accent-color:var(--ink);
  cursor:pointer;flex-shrink:0;
  transition:opacity .2s;
}
.item-card:not(:hover) .item-checkbox:not(:checked){opacity:.35;}
.item-card:hover .item-checkbox{opacity:1;}
.item-info-wrap{display:flex;align-items:center;gap:12px;min-width:0;}
.item-icon-lg{
  font-size:1.1rem;
  width:34px;height:34px;
  display:flex;align-items:center;justify-content:center;
  flex-shrink:0;
  background:var(--surface-2);
  border-radius:var(--radius-sm);
  transition:transform .2s var(--ease-out-expo);
}
.item-card:hover .item-icon-lg{transform:scale(1.08);}
.item-name{
  font-size:.86rem;font-weight:600;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
}
.item-details{min-width:0;}
.tag-badge{
  font-size:.6rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  padding:2px 7px;
  border:1px solid var(--border-md);
  color:var(--text-muted);
  display:inline-block;margin-left:8px;
  border-radius:20px;
}
.col-owner{
  font-size:.76rem;color:var(--text-muted);
  display:flex;align-items:center;gap:6px;
}
.col-owner img{
  width:20px;height:20px;
  object-fit:cover;
  border-radius:var(--radius-sm);
  filter:grayscale(60%);
}
.col-date,.col-size{font-size:.76rem;color:var(--text-muted);}
.action-wrapper{position:relative;display:flex;justify-content:center;}
.btn-dots{
  background:none;border:none;
  color:var(--text-muted);font-size:.88rem;
  cursor:pointer;
  width:30px;height:30px;
  display:flex;align-items:center;justify-content:center;
  transition:all var(--tr);
  border-radius:var(--radius-sm);
}
.btn-dots:hover{background:var(--surface-3);color:var(--text-main);}
.action-dropdown{
  display:none;
  position:absolute;right:0;top:calc(100% + 4px);
  background:var(--surface);
  min-width:186px;
  border:1px solid var(--border-md);
  border-radius:var(--radius-md);
  z-index:150;
  box-shadow:var(--shadow-xl);
  overflow:hidden;
}
.action-dropdown.show{display:block;animation:scaleIn .16s var(--ease-out-expo);}
.action-dropdown a,.action-dropdown button{
  display:flex;align-items:center;gap:9px;
  padding:9px 14px;
  font-size:.82rem;color:var(--text-main);
  border:none;background:none;
  width:100%;text-align:left;cursor:pointer;
  font-family:var(--f-body);
  border-bottom:1px solid var(--border);
  transition:background var(--tr);
}
.action-dropdown a:last-child,.action-dropdown button:last-child{border-bottom:none;}
.action-dropdown a:hover,.action-dropdown button:hover{background:var(--surface-3);}
.action-dropdown a i,.action-dropdown button i{
  width:16px;text-align:center;color:var(--text-muted);
}
.select-all-wrap{display:flex;align-items:center;}
.rename-inline{
  background:var(--surface);
  border:2px solid var(--ink);
  color:var(--text-main);
  font-size:.86rem;
  padding:2px 8px;
  font-family:var(--f-body);
  width:200px;outline:none;
  border-radius:4px;
  box-shadow:var(--glow-md);
}

/* GRID VIEW */
.view-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(156px,1fr));
  gap:0;
  border-top:1px solid var(--border);
}
.view-grid .item-card{
  display:flex;flex-direction:column;align-items:center;
  text-align:center;padding:22px 14px;
  border-right:1px solid var(--border);
  grid-template-columns:unset;
  border-radius:0;
}
.view-grid .item-info-wrap{flex-direction:column;gap:9px;width:100%;}
.view-grid .item-icon-lg{
  font-size:1.9rem;width:auto;height:auto;
  background:none;margin:0 auto;
}
.view-grid .item-name{font-size:.8rem;}
.view-grid .item-checkbox{position:absolute;top:8px;left:8px;}
.view-grid .action-wrapper{position:absolute;top:5px;right:5px;}
.view-grid .col-owner,.view-grid .col-date,.view-grid .col-size{display:none;}
.view-grid .tag-badge{display:none;}

/* EMPTY STATE */
.empty-state{
  display:flex;flex-direction:column;
  align-items:center;justify-content:center;
  padding:72px 40px;
  text-align:center;cursor:pointer;
  border-bottom:1px solid var(--border);
  transition:background .2s;
}
.empty-state:hover{background:var(--surface-2);}
.empty-state i{
  font-size:2.5rem;
  color:var(--border-md);
  margin-bottom:16px;
  transition:transform .3s var(--ease-out-expo);
}
.empty-state:hover i{transform:scale(1.15);}
.empty-state h3{font-size:.95rem;font-weight:700;margin-bottom:6px;}
.empty-state p{font-size:.83rem;color:var(--text-muted);}
.empty-activity{
  padding:32px;text-align:center;
  color:var(--text-muted);font-size:.83rem;
}

/* ââ RIGHT SIDEBAR ââ */
.right-sidebar{
  width:292px;
  border-left:1px solid var(--border);
  background:var(--surface);
  overflow-y:auto;
  display:none;
  flex-direction:column;
}
.right-sidebar.active{display:flex;animation:slideDown .2s var(--ease-out-expo);}
.rs-header{
  display:flex;align-items:center;justify-content:space-between;
  padding:12px 16px;
  border-bottom:1px solid var(--border);
  background:var(--surface-2);
}
.rs-header h3{font-size:.75rem;font-weight:700;letter-spacing:.3px;text-transform:uppercase;}
.rs-content{padding:16px;flex:1;}
.rs-preview{
  text-align:center;padding:20px;
  background:var(--surface-2);
  border:1px solid var(--border);
  border-radius:var(--radius-sm);
  margin-bottom:14px;
}
.rs-group{margin-bottom:11px;}
.rs-group label{
  font-size:.6rem;font-weight:700;
  letter-spacing:.8px;text-transform:uppercase;
  color:var(--text-muted);display:block;margin-bottom:3px;
}
.rs-val{font-size:.82rem;color:var(--text-main);word-break:break-all;}
.rs-action-buttons{
  display:flex;flex-wrap:wrap;gap:0;
  margin-bottom:14px;
  border:1px solid var(--border-md);
  border-radius:var(--radius-sm);
  overflow:hidden;
}
.btn-rs-action{
  display:inline-flex;align-items:center;gap:6px;
  padding:8px 10px;
  font-size:.7rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  cursor:pointer;border:none;
  font-family:var(--f-body);
  transition:all var(--tr);
  border-right:1px solid var(--border);
  border-bottom:1px solid var(--border);
  flex:1;min-width:50%;justify-content:center;
}
.btn-rs-primary{background:var(--ink);color:#fff;}
.btn-rs-primary:hover{background:#222;}
.btn-rs-secondary{background:var(--surface-3);color:var(--text-main);}
.btn-rs-secondary:hover{background:var(--surface-2);}
.btn-rs-danger{background:var(--danger-bg);color:var(--danger);}
.btn-rs-danger:hover{background:var(--danger);color:#fff;}
.btn-rs-whatsapp{background:var(--success-bg);color:var(--success);}
.btn-rs-whatsapp:hover{background:var(--success);color:#fff;}
.rs-qr-box{
  text-align:center;padding:12px;
  border:1px solid var(--border);
  border-radius:var(--radius-sm);
  margin-bottom:14px;display:none;
}
.rs-qr-box img{width:112px;height:112px;}
.rs-qr-box p{font-size:.68rem;color:var(--text-muted);margin-top:8px;}

/* ââ CV BUILDER ââ */
.profile-inner{padding:32px;}
.tab-nav{
  display:flex;
  border-bottom:1px solid var(--border);
  margin-bottom:28px;
  overflow-x:auto;
  -webkit-overflow-scrolling:touch;
  scrollbar-width:none;
  gap:0;
}
.tab-nav::-webkit-scrollbar{display:none;}
.tab-btn{
  padding:11px 22px;
  font-size:.73rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  background:transparent;border:none;
  border-bottom:2.5px solid transparent;
  color:var(--text-muted);cursor:pointer;
  font-family:var(--f-body);
  transition:all var(--tr);
  display:flex;align-items:center;gap:7px;
  white-space:nowrap;margin-bottom:-1px;
}
.tab-btn:hover{color:var(--text-main);}
.tab-btn.active{
  color:var(--text-main);
  border-bottom-color:var(--ink);
  font-weight:700;
}
.tab-panel{display:none;animation:fadeUp .3s var(--ease-out-expo);}
.tab-panel.active{display:block;}
.portfolio-link-box{
  display:flex;align-items:center;gap:0;
  border:1.5px solid var(--border-md);
  border-radius:var(--radius-sm);
  margin-bottom:28px;
  overflow:hidden;
  transition:border-color .2s,box-shadow .2s;
}
.portfolio-link-box:focus-within{
  border-color:var(--border-dark);
  box-shadow:var(--glow-sm);
}
.portfolio-link-box input{
  flex:1;background:transparent;border:none;
  color:var(--text-muted);font-size:.82rem;
  font-family:monospace;outline:none;
  min-width:0;padding:11px 14px;
}
.portfolio-link-box .copy-btn{
  padding:11px 18px;
  background:var(--ink);color:#fff;border:none;
  font-size:.7rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  cursor:pointer;font-family:var(--f-body);
  display:inline-flex;align-items:center;gap:6px;
  white-space:nowrap;
  transition:background var(--tr);
}
.portfolio-link-box .copy-btn:hover{background:#222;}
.portfolio-link-box a.copy-btn{
  background:var(--surface-3);color:var(--text-main);
}
.portfolio-link-box a.copy-btn:hover{background:var(--surface-2);}

/* Profile form */
.ident-section-title{
  font-size:.62rem;font-weight:700;
  letter-spacing:1.5px;text-transform:uppercase;
  color:var(--text-muted);
  padding-bottom:10px;
  border-bottom:1px solid var(--border);
  margin:24px 0 16px;
  display:flex;align-items:center;gap:8px;
}
.profile-form-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
  gap:18px;margin-bottom:8px;
}
.profile-form-field{display:flex;flex-direction:column;gap:5px;}
.profile-form-field label{
  font-size:.65rem;font-weight:700;
  letter-spacing:.5px;text-transform:uppercase;
  color:var(--text-muted);
}
.profile-form-field input,
.profile-form-field textarea,
.profile-form-field select{
  width:100%;padding:10px 12px;
  background:var(--surface-2);
  border:1.5px solid var(--border);
  border-radius:var(--radius-sm);
  color:var(--text-main);
  font-family:var(--f-body);font-size:.88rem;
  outline:none;
  transition:border-color .2s,box-shadow .2s,background .2s;
}
.profile-form-field input:focus,
.profile-form-field textarea:focus,
.profile-form-field select:focus{
  border-color:var(--border-dark);
  background:var(--surface);
  box-shadow:var(--glow-sm);
}
.profile-form-field textarea{resize:vertical;min-height:96px;}
.profile-form-field.full-width{grid-column:1/-1;}
.profesi-badge{
  display:inline-flex;align-items:center;gap:6px;
  font-size:.7rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  padding:4px 12px;
  border:1px solid var(--border-md);
  color:var(--text-main);
  border-radius:20px;
}
.tampil-toggle{
  display:flex;align-items:center;gap:12px;
  padding:14px 0;
  border-bottom:1px solid var(--border);
  margin-top:18px;flex-wrap:wrap;
}
.tampil-toggle input[type=checkbox]{
  width:18px;height:18px;
  accent-color:var(--ink);cursor:pointer;flex-shrink:0;
}
.tampil-toggle label{font-size:.83rem;font-weight:600;cursor:pointer;}
.tampil-toggle .tampil-desc{font-size:.76rem;color:var(--text-muted);margin-left:auto;}

/* ââ ACCORDION (CV sections) ââ */
.dyn-list{
  display:flex;flex-direction:column;
  gap:8px;
  margin-bottom:16px;
}
.dyn-item{
  border:1.5px solid var(--border-md);
  border-radius:var(--radius-md);
  overflow:hidden;
  background:var(--surface);
  box-shadow:var(--shadow-xs);
  transition:box-shadow .2s,border-color .2s;
}
.dyn-item:hover{box-shadow:var(--shadow-sm);}
.dyn-item.is-open{
  border-color:var(--ink);
  box-shadow:var(--shadow-md),var(--glow-sm);
}
.dyn-item-header{
  display:flex;justify-content:space-between;align-items:center;
  padding:14px 18px;
  cursor:pointer;user-select:none;
  min-height:52px;
  transition:background .2s;
}
.dyn-item-header:hover{background:var(--surface-2);}
.dyn-item.is-open .dyn-item-header{
  background:var(--ink);
  color:#fff;
}
.dyn-item-header h4{
  font-size:.83rem;font-weight:700;
  letter-spacing:.2px;
  display:flex;align-items:center;gap:9px;
  flex:1;min-width:0;
}
.dyn-item.is-open .dyn-item-header h4{color:#fff;}
.dyn-preview{
  font-size:.7rem;font-weight:400;
  color:var(--text-muted);margin-left:8px;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
  max-width:200px;
}
.dyn-item.is-open .dyn-preview{color:rgba(255,255,255,.55);}
.dyn-item-header-btns{display:flex;align-items:center;gap:8px;flex-shrink:0;}
.dyn-chevron{
  font-size:.78rem;
  color:var(--text-muted);
  transition:transform .32s var(--ease-out-expo);
}
.dyn-item.is-open .dyn-chevron{
  transform:rotate(180deg);
  color:rgba(255,255,255,.65);
}
/* Smooth body open/close */
.dyn-body{
  display:grid;
  grid-template-rows:0fr;
  transition:grid-template-rows .32s var(--ease-out-expo);
}
.dyn-body-inner{
  overflow:hidden;
  padding:0;
}
.dyn-item.is-open .dyn-body{
  grid-template-rows:1fr;
}
.dyn-item.is-open .dyn-body-inner{
  padding:20px 18px;
}
.dyn-body-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(200px,1fr));
  gap:14px;
}
.dyn-field{display:flex;flex-direction:column;gap:5px;}
.dyn-field label{
  font-size:.62rem;font-weight:700;
  letter-spacing:.5px;text-transform:uppercase;
  color:var(--text-muted);
}
.dyn-field input,.dyn-field textarea,.dyn-field select{
  width:100%;padding:9px 11px;
  background:var(--surface-2);
  border:1.5px solid var(--border);
  border-radius:var(--radius-sm);
  color:var(--text-main);
  font-family:var(--f-body);font-size:.86rem;
  outline:none;
  transition:border-color .2s,box-shadow .2s;
  min-height:40px;
}
.dyn-field input:focus,.dyn-field textarea:focus{
  border-color:var(--border-dark);
  box-shadow:var(--glow-sm);
}
.dyn-field textarea{resize:vertical;min-height:76px;}
.dyn-field.full-width{grid-column:1/-1;}
.skill-slider-wrap{display:flex;align-items:center;gap:12px;}
.skill-slider-wrap input[type=range]{
  flex:1;
  accent-color:var(--ink);
  height:4px;cursor:pointer;
  border-radius:4px;
}
.btn-remove-dyn{
  padding:5px 11px;
  font-size:.62rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  background:rgba(255,255,255,.12);
  border:1px solid rgba(255,255,255,.3);
  color:rgba(255,255,255,.8);
  cursor:pointer;font-family:var(--f-body);
  transition:all .2s;
  display:inline-flex;align-items:center;gap:4px;
  border-radius:var(--radius-sm);
}
.btn-remove-dyn:hover{background:rgba(255,255,255,.22);}
.btn-add-dyn{
  padding:13px 20px;
  background:var(--surface-2);
  border:2px dashed var(--border-md);
  color:var(--text-muted);
  font-size:.76rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  cursor:pointer;font-family:var(--f-body);
  transition:all .22s;
  display:flex;align-items:center;gap:8px;
  width:100%;justify-content:center;
  border-radius:var(--radius-md);
}
.btn-add-dyn:hover{
  background:var(--ink);
  color:#fff;
  border-style:solid;
  border-color:var(--ink);
  box-shadow:var(--shadow-sm);
}
.btn-add-dyn:active{transform:scale(.98);}
.btn-submit{
  padding:11px 26px;
  background:var(--ink);color:#fff;
  font-size:.76rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  border:none;cursor:pointer;font-family:var(--f-body);
  transition:all var(--tr);
  display:inline-flex;align-items:center;gap:8px;
  margin-top:18px;
  border-radius:var(--radius-sm);
  box-shadow:var(--shadow-sm);
}
.btn-submit:hover{background:#222;box-shadow:var(--shadow-md);}
.btn-submit:active{transform:scale(.97);}

/* ââ USER MANAGEMENT TABLE ââ */
.section-card{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius-md);
  overflow:hidden;
  margin-bottom:-1px;
  box-shadow:var(--shadow-xs);
}
.section-card-header{
  display:flex;align-items:center;justify-content:space-between;
  padding:14px 20px;
  border-bottom:1px solid var(--border);
  background:var(--surface-2);
}
.section-card-header h3{
  font-size:.78rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  display:flex;align-items:center;gap:8px;
}
.section-card-body{padding:20px;}
.user-table{width:100%;border-collapse:collapse;}
.user-table th{
  padding:9px 14px;font-size:.62rem;
  font-weight:700;letter-spacing:.5px;text-transform:uppercase;
  color:var(--text-muted);
  border-bottom:1px solid var(--border);
  background:var(--surface-2);text-align:left;
}
.user-table td{
  padding:11px 14px;font-size:.82rem;
  border-bottom:1px solid var(--border);
  vertical-align:middle;
  transition:background .15s;
}
.user-table tr:last-child td{border-bottom:none;}
.user-table tr:hover td{background:var(--surface-3);}
.user-avatar-sm{
  width:32px;height:32px;
  object-fit:cover;
  border-radius:var(--radius-sm);
  filter:grayscale(40%);
}
.role-badge{
  display:inline-block;
  padding:3px 9px;
  font-size:.62rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  border-radius:20px;
}
.role-badge.superadmin{background:var(--warning-bg);color:var(--superadmin);}
.role-badge.admin{background:var(--blue-bg);color:var(--blue);}
.role-badge.user{background:var(--surface-3);color:var(--text-muted);}
.action-btn-sm{
  padding:5px 11px;
  font-size:.66rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  cursor:pointer;border:1px solid var(--border-md);
  font-family:var(--f-body);
  transition:all var(--tr);
  background:transparent;
  border-radius:var(--radius-sm);
}
.edit-btn:hover{background:var(--ink);color:#fff;border-color:var(--ink);}
.del-btn:hover{background:var(--danger);color:#fff;border-color:var(--danger);}
.view-workspace-btn:hover{background:var(--surface-2);}

/* ââ MODALS ââ */
.modal{
  display:none;
  position:fixed;z-index:500;inset:0;
  background:rgba(0,0,0,.45);
  backdrop-filter:blur(6px);
}
.modal-content{
  background:var(--surface);
  margin:5vh auto;padding:0;
  width:92%;max-width:480px;
  border:1px solid var(--border-md);
  border-radius:var(--radius-lg);
  box-shadow:var(--shadow-xl);
  animation:modalIn .28s var(--ease-out-expo);
  max-height:90vh;overflow-y:auto;
}
.modal-content.wide{max-width:620px;}
.modal-title{
  padding:18px 22px;
  font-size:.82rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  display:flex;align-items:center;justify-content:space-between;
  border-bottom:1px solid var(--border);
  background:var(--surface-2);
}
.modal-title span{display:flex;align-items:center;gap:8px;}
.close-btn{
  cursor:pointer;font-size:1.1rem;
  color:var(--text-muted);
  width:28px;height:28px;
  display:flex;align-items:center;justify-content:center;
  transition:all .2s;
  background:none;border:none;
  border-radius:var(--radius-sm);
}
.close-btn:hover{background:var(--surface-3);color:var(--text-main);}
.modal form{padding:22px;}
.modal label{
  display:block;font-size:.63rem;font-weight:700;
  letter-spacing:.5px;text-transform:uppercase;
  color:var(--text-muted);margin-bottom:6px;margin-top:16px;
}
.modal label:first-child{margin-top:0;}
.modal input[type="text"],
.modal input[type="password"],
.modal input[type="url"],
.modal input[type="email"],
.modal select,
.modal textarea{
  width:100%;padding:10px 12px;
  background:var(--surface-2);
  border:1.5px solid var(--border);
  border-radius:var(--radius-sm);
  color:var(--text-main);font-family:var(--f-body);
  font-size:.88rem;outline:none;
  transition:border-color .2s,box-shadow .2s;
}
.modal input:focus,.modal select:focus,.modal textarea:focus{
  border-color:var(--border-dark);
  box-shadow:var(--glow-sm);
}
.modal input[type="color"]{
  width:100%;height:40px;
  border:1.5px solid var(--border);
  background:none;cursor:pointer;padding:2px;
  border-radius:var(--radius-sm);
}
.modal input[type="file"]{
  border:none;padding:0;
  color:var(--text-muted);font-size:.84rem;cursor:pointer;
}
.btn-submit-modal{
  width:100%;padding:12px;
  background:var(--ink);color:#fff;border:none;
  font-size:.78rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  cursor:pointer;font-family:var(--f-body);
  margin-top:18px;
  border-radius:var(--radius-sm);
  transition:background var(--tr),box-shadow var(--tr);
  box-shadow:var(--shadow-sm);
}
.btn-submit-modal:hover{background:#222;box-shadow:var(--shadow-md);}
.btn-submit-modal:active{transform:scale(.98);}
.upload-zone{
  border:2px dashed var(--border-md);
  border-radius:var(--radius-md);
  padding:28px 20px;text-align:center;
  cursor:pointer;transition:all .22s;margin-top:8px;
}
.upload-zone:hover,.upload-zone.dragover{
  border-color:var(--ink);
  background:var(--surface-2);
  box-shadow:var(--glow-sm);
}
.upload-zone i{
  font-size:1.9rem;color:var(--border-md);
  margin-bottom:10px;display:block;
  transition:transform .3s var(--ease-out-expo),color .2s;
}
.upload-zone:hover i{transform:scale(1.15) translateY(-3px);color:var(--text-muted);}
.upload-zone p{color:var(--text-muted);font-size:.82rem;}
.upload-zone input[type="file"]{display:none;}

/* ââ PREVIEW OVERLAY ââ */
.preview-overlay{
  display:none;position:fixed;inset:0;
  background:rgba(5,5,10,.94);
  z-index:600;flex-direction:column;
}
.preview-overlay.active{display:flex;animation:fadeIn .22s ease;}
.preview-header{
  display:flex;align-items:center;justify-content:space-between;
  padding:12px 22px;
  border-bottom:1px solid rgba(255,255,255,.1);flex-shrink:0;
}
.preview-filename{color:#fff;font-size:.84rem;font-weight:600;display:flex;align-items:center;gap:8px;}
.preview-actions{display:flex;gap:0;}
.preview-actions a,.preview-actions button{
  padding:8px 16px;
  background:rgba(255,255,255,.08);color:#fff;
  border:none;border-left:1px solid rgba(255,255,255,.1);
  font-size:.72rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  cursor:pointer;font-family:var(--f-body);
  display:inline-flex;align-items:center;gap:6px;
  transition:background .2s;
}
.preview-actions a:hover,.preview-actions button:hover{background:rgba(255,255,255,.16);}
.preview-body{
  flex:1;display:flex;align-items:center;
  justify-content:center;overflow:auto;padding:24px;
}
.preview-body img{
  max-width:100%;max-height:80vh;object-fit:contain;
  border-radius:var(--radius-sm);
}
.preview-body iframe{width:100%;height:100%;border:none;}
.preview-unsupported{text-align:center;color:rgba(255,255,255,.5);}
.preview-unsupported i{font-size:4rem;margin-bottom:16px;display:block;}

/* ââ CONFIRM DIALOG ââ */
.confirm-overlay{
  display:none;position:fixed;inset:0;z-index:700;
  background:rgba(0,0,0,.5);
  backdrop-filter:blur(8px);
  justify-content:center;align-items:center;
}
.confirm-overlay.active{display:flex;}
.confirm-box{
  background:var(--surface);
  border:1px solid var(--border-md);
  border-radius:var(--radius-lg);
  padding:40px;max-width:360px;width:92%;
  text-align:center;animation:modalIn .28s var(--ease-out-expo);
  box-shadow:var(--shadow-xl);
}
.confirm-icon{font-size:3rem;margin-bottom:16px;}
.confirm-box h3{
  font-family:var(--f-display);
  font-size:1.25rem;font-weight:700;margin-bottom:8px;
}
.confirm-box p{color:var(--text-muted);font-size:.84rem;margin-bottom:24px;line-height:1.6;}
.confirm-btns{display:flex;gap:8px;justify-content:center;}
.confirm-cancel{
  padding:10px 24px;
  background:var(--surface-3);color:var(--text-main);
  border:1.5px solid var(--border-md);
  font-size:.76rem;font-weight:700;letter-spacing:.3px;text-transform:uppercase;
  cursor:pointer;font-family:var(--f-body);
  border-radius:var(--radius-sm);transition:all var(--tr);
}
.confirm-cancel:hover{background:var(--surface-2);}
.confirm-danger{
  padding:10px 24px;
  background:var(--danger);color:#fff;
  border:none;
  font-size:.76rem;font-weight:700;letter-spacing:.3px;text-transform:uppercase;
  cursor:pointer;font-family:var(--f-body);
  border-radius:var(--radius-sm);
  transition:background var(--tr),box-shadow var(--tr);
  box-shadow:0 2px 8px rgba(220,38,38,.3);
}
.confirm-danger:hover{background:#b91c1c;}

/* ââ TOAST ââ */
#toast{
  min-width:220px;
  background:var(--ink);color:#fff;
  text-align:center;padding:11px 22px;
  position:fixed;z-index:800;
  left:50%;bottom:72px;
  transform:translateX(-50%);
  font-size:.8rem;font-weight:600;letter-spacing:.3px;
  visibility:hidden;
  border-radius:var(--radius-sm);
  box-shadow:var(--shadow-lg);
}
#toast.show{
  visibility:visible;
  animation:toastIn .3s var(--ease-out-expo),toastOut .4s ease 3.3s forwards;
}

/* ââ GLOBAL DROP OVERLAY ââ */
.global-drop-overlay{
  position:fixed;inset:0;
  background:rgba(250,250,250,.95);
  backdrop-filter:blur(16px);
  z-index:900;
  display:flex;flex-direction:column;
  justify-content:center;align-items:center;
  opacity:0;visibility:hidden;
  transition:all .25s var(--ease-out-expo);
  border:3px dashed var(--border-md);
}
.global-drop-overlay.active{opacity:1;visibility:visible;}
.drop-pill{
  background:var(--ink);color:#fff;
  padding:14px 32px;
  font-size:.95rem;font-weight:700;letter-spacing:.3px;text-transform:uppercase;
  border-radius:var(--radius-md);
  box-shadow:var(--shadow-lg);
}

/* ââ SweetAlert2 OVERRIDES ââ */
.swal2-popup{
  font-family:var(--f-body)!important;
  border-radius:var(--radius-lg)!important;
  border:1px solid var(--border-md)!important;
  box-shadow:var(--shadow-xl)!important;
}
.swal2-title{
  font-family:var(--f-display)!important;
  font-size:1.45rem!important;letter-spacing:-.3px!important;
}
.swal2-confirm{
  background:var(--ink)!important;
  border-radius:var(--radius-sm)!important;
  font-weight:700!important;
  letter-spacing:.3px!important;
  text-transform:uppercase!important;
  font-size:.78rem!important;
  box-shadow:var(--shadow-sm)!important;
}

/* ââ MOBILE ââ */
.bottom-nav{display:none;}
.fab-container{display:none;}
.mobile-panel-overlay{display:none;}

@media(max-width:768px){
  .top-navbar{padding:0 14px;}
  .header-center{display:none;}
  .header-right .stats-badge{display:none;}
  .content-area{padding-bottom:72px!important;}
  .right-sidebar{display:none!important;}
  .list-header{display:none;}
  .item-card{grid-template-columns:28px 1fr 38px;padding:11px 14px;}
  .col-owner,.col-date,.col-size{display:none;}
  .view-grid{grid-template-columns:repeat(auto-fill,minmax(128px,1fr));}
  .bento-grid{grid-template-columns:1fr;gap:12px;padding:16px 16px 0 !important;}
  .dash-inner.grid-2{grid-template-columns:1fr !important; gap:16px; padding:16px !important;}
  .greeting-strip{padding:24px 20px;}
  .greeting-name{font-size:1.8rem;}
  .tab-btn{font-size:.7rem;padding:9px 13px;}
  .profile-inner,.dash-inner{padding:16px;}
  .page-header{padding:16px;}
  .breadcrumbs,.toolbar-main{padding:9px 14px;}
  .filter-chips{padding:8px 14px;}
  .profile-form-grid{grid-template-columns:1fr;}
  .dyn-body-grid{grid-template-columns:1fr;}
  .dyn-field input,.dyn-field textarea{font-size:16px!important;}
  .profile-form-field input,.profile-form-field textarea{font-size:16px!important;}
  .btn-submit{width:100%;justify-content:center;}
  .portfolio-link-box{flex-wrap:wrap;}
  .portfolio-link-box input{width:100%;}
  .bottom-nav{
    display:flex;justify-content:space-around;align-items:center;
    position:fixed;bottom:0;left:0;width:100%;
    background:var(--glass);
    border-top:1px solid var(--border);
    padding:6px 0 max(10px,env(safe-area-inset-bottom));
    z-index:200;
    backdrop-filter:blur(16px);
  }
  .bottom-nav-item{
    display:flex;flex-direction:column;align-items:center;
    color:var(--text-muted);font-size:.58rem;
    gap:2px;width:20%;padding:4px 0;
    font-weight:700;letter-spacing:.3px;text-transform:uppercase;
  }
  .bottom-nav-item.active{color:var(--text-main);}
  .bottom-nav-item i{
    font-size:1.05rem;padding:5px 14px;
    border-radius:var(--radius-sm);
  }
  .bottom-nav-item.active i{background:var(--surface-3);}
  .fab-container{
    display:block;position:fixed;
    bottom:72px;right:16px;z-index:210;
  }
  .fab{
    width:50px;height:50px;
    background:var(--ink);color:#fff;
    font-size:1.3rem;
    display:flex;align-items:center;justify-content:center;
    border:none;cursor:pointer;
    transition:all .22s var(--ease-out-expo);
    border-radius:var(--radius-md);
    box-shadow:var(--shadow-lg);
  }
  .fab:active{transform:scale(.92);}
  .fab-menu{
    position:absolute;bottom:62px;right:0;
    display:flex;flex-direction:column;gap:8px;align-items:flex-end;
    opacity:0;visibility:hidden;
    transition:all .25s var(--ease-out-expo);
    transform:translateY(10px);
  }
  .fab-menu.active{opacity:1;visibility:visible;transform:translateY(0);}
  .fab-item{
    display:flex;align-items:center;gap:10px;
    background:var(--surface);
    padding:9px 16px;color:var(--text-main);
    font-size:.8rem;font-weight:600;
    border:1px solid var(--border-md);
    white-space:nowrap;cursor:pointer;
    font-family:var(--f-body);
    border-radius:var(--radius-sm);
    box-shadow:var(--shadow-md);
  }
  .fab-item i{color:var(--text-muted);}
  .mobile-panel-overlay{
    display:none;position:fixed;inset:0;
    background:rgba(0,0,0,.35);
    backdrop-filter:blur(4px);z-index:400;
  }
  .mobile-panel-overlay.active{display:block;}
  .mobile-detail-panel{
    position:fixed;bottom:0;left:0;width:100%;
    max-height:80vh;
    background:var(--surface);
    border-top:1px solid var(--border-md);
    border-radius:var(--radius-xl) var(--radius-xl) 0 0;
    z-index:401;
    transform:translateY(100%);
    transition:transform .36s var(--ease-out-expo);
    overflow-y:auto;
  }
  .mobile-detail-panel.active{transform:translateY(0);}
  .mobile-panel-handle{
    width:36px;height:4px;
    background:var(--border-md);
    border-radius:4px;
    margin:10px auto 0;
  }
}
<?php if($current_page === 'workspace'): ?>
/* ââ OVERRIDE ROOT VARIABLES FOR LIGHT THEME ââ */
:root, body, html, .main-wrapper, .drive-layout {
  --bg: #f8fafd !important;
  --bg-2: #e9eef6 !important;
  --surface: #ffffff !important;
  --surface-2: #f0f4f9 !important;
  --surface-3: #e8eaed !important;
  --glass: rgba(255, 255, 255, 0.9) !important;
  --glass-border: rgba(0, 0, 0, 0.08) !important;
  --ink: #1f1f1f !important;
  --ink-2: #444746 !important;
  --text-main: #1f1f1f !important;
  --text-secondary: #444746 !important;
  --text-muted: #5f6368 !important;
  --border: #e0e0e0 !important;
  --border-md: #dadce0 !important;
  --border-dark: #bdc1c6 !important;
  --accent: #0b57d0 !important;
  --accent-2: #1a73e8 !important;
  --accent-soft: #d3e3fd !important;
}
/*  ? ? GOOGLE DRIVE LAYOUT (DARK SAAS PREMIUM)  ? ? */
.drive-layout { padding: 16px; background: transparent; min-height: calc(100vh - 80px); display: block !important; margin: 0 16px 16px 0; }
body { background: var(--bg); color: var(--text-main); }
.top-navbar { background: var(--bg); border-bottom: 1px solid var(--border); box-shadow: none; height: 64px; padding: 8px 16px; }
.sidebar { background: var(--bg); border-right: 1px solid var(--border); width: 256px; padding-top: 12px; }
.main-wrapper { margin-left: 256px; padding-top: 64px; }
.nav-item { margin: 2px 12px; border-radius: 12px; padding: 8px 16px; color: var(--text-secondary); font-size: 0.875rem; font-weight: 500; font-family: 'Inter', sans-serif; transition: all 0.2s ease; }
.nav-item:hover { background: var(--surface-2); color: var(--text-main); }
.nav-item.active { background: var(--accent-soft); color: var(--accent); font-weight: 600; }
.nav-item i { width: 24px; font-size: 1.1rem; color: var(--text-secondary); transition: all 0.2s ease; }
.nav-item:hover i { color: var(--text-main); }
.nav-item.active i { color: var(--accent); }
.btn-drive-new { display: flex; align-items: center; gap: 12px; background: var(--surface-2); border: 1px solid var(--border); border-radius: 16px; padding: 12px 20px; font-size: 0.875rem; font-weight: 600; font-family: 'Inter', sans-serif; color: var(--text-main); cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: all 0.2s ease; margin-bottom: 8px; }
.btn-drive-new:hover { background: var(--surface-3); border-color: var(--border-md); transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,0,0,0.15); }

.drive-section-title { font-size: 0.875rem; font-weight: 600; font-family: 'Inter', sans-serif; color: var(--text-secondary); margin-bottom: 16px; margin-top: 24px; letter-spacing: 0.5px; text-transform: uppercase; }
.drive-grid-folders { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; margin-bottom: 24px; }
.drive-folder-card { display: flex; align-items: center; background: var(--surface-2); border: 1px solid var(--border); border-radius: 16px; padding: 12px 16px; cursor: pointer; transition: all 0.2s ease; position: relative; gap: 12px; height: 56px; backdrop-filter: blur(10px); }
.drive-folder-card:hover { background: var(--surface-3); border-color: var(--border-md); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
.drive-folder-card.selected { background: var(--accent-soft); border-color: var(--accent); }
.drive-folder-card .item-info-wrap { flex: 1; display: flex; align-items: center; gap: 12px; overflow: hidden; }
.drive-folder-card .item-icon-lg { font-size: 1.4rem; color: var(--text-secondary); }
.drive-folder-card .item-name { font-size: 0.9rem; font-weight: 500; font-family: 'Inter', sans-serif; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.drive-folder-card .action-wrapper, .drive-folder-card .item-checkbox, .drive-folder-card .col-owner, .drive-folder-card .col-date, .drive-folder-card .col-size { display: none; }
.drive-folder-card:hover .action-wrapper { display: flex; position: absolute; right: 8px; top: 8px; }

.drive-grid-files { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }
.drive-file-card { display: flex; flex-direction: column; background: var(--surface-2); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; cursor: pointer; transition: all 0.2s ease; position: relative; height: 200px; backdrop-filter: blur(10px); }
.drive-file-card:hover { background: var(--surface-3); border-color: var(--border-md); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
.drive-file-card.selected { background: var(--accent-soft); border-color: var(--accent); }
.drive-file-preview { height: 140px; background: var(--surface); display: flex; align-items: center; justify-content: center; border-bottom: 1px solid var(--border); overflow: hidden; }
.drive-file-preview-img { width: 100%; height: 100%; object-fit: cover; opacity: 0.9; transition: transform 0.3s ease; }
.drive-file-card:hover .drive-file-preview-img { transform: scale(1.05); }
.drive-file-icon-placeholder i { font-size: 4rem; opacity: 0.7; }
.drive-file-card .item-info-wrap { padding: 12px; display: flex; align-items: center; gap: 12px; flex: 1; }
.drive-file-card .item-icon-sm { font-size: 1.2rem; }
.drive-file-card .item-name { font-size: 0.9rem; font-weight: 500; font-family: 'Inter', sans-serif; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; }
.drive-file-card .action-wrapper, .drive-file-card .item-checkbox, .drive-file-card .col-owner, .drive-file-card .col-date, .drive-file-card .col-size { display: none; }
.drive-file-card:hover .action-wrapper { display: flex; position: absolute; right: 8px; bottom: 8px; }

/* Context Menu */
.drive-context-menu, .action-dropdown, .dropdown-content { position: absolute; background: var(--surface-2); border: 1px solid var(--border-md); border-radius: 12px; padding: 8px 0; min-width: 220px; box-shadow: 0 8px 32px rgba(0,0,0,0.4); z-index: 9999; font-family: 'Inter', sans-serif; display: none; backdrop-filter: blur(10px); }
.drive-context-menu.show, .action-dropdown.show, .dropdown-content.show { display: block; animation: fadeIn 0.15s ease-out; }
.drive-context-item, .action-dropdown a, .action-dropdown button, .dropdown-content button { padding: 10px 16px; font-size: 0.875rem; color: var(--text-main); display: flex; align-items: center; gap: 12px; cursor: pointer; transition: background 0.15s; background: transparent; border: none; width: 100%; text-align: left; }
.drive-context-item:hover, .action-dropdown a:hover, .action-dropdown button:hover, .dropdown-content button:hover { background: var(--surface-3); }
.drive-context-item i, .action-dropdown i, .dropdown-content i { font-size: 1.1rem; color: var(--text-secondary); width: 20px; text-align: center; }
.drive-context-divider, .menu-divider { height: 1px; background: var(--border); margin: 8px 0; border: none; }

/* List View Overrides */
.view-list .drive-grid-folders, .view-list .drive-grid-files { display: flex; flex-direction: column; gap: 8px; }
.view-list .drive-folder-card, .view-list .drive-file-card { display: grid; grid-template-columns: 36px 1fr 160px 140px 90px 40px; align-items: center; height: 56px; border-radius: 12px; border: 1px solid var(--border); padding: 0 16px; background: var(--surface-2); margin-bottom: 0; }
.view-list .drive-folder-card:hover, .view-list .drive-file-card:hover { background: var(--surface-3); transform: translateX(4px); }
.view-list .drive-folder-card.selected, .view-list .drive-file-card.selected { background: var(--accent-soft); }
.view-list .drive-file-preview { display: none; }
.view-list .drive-file-card .item-info-wrap, .view-list .drive-folder-card .item-info-wrap { height: auto; padding: 0; }
.view-list .col-owner, .view-list .col-date, .view-list .col-size { display: flex; align-items: center; font-size: 0.875rem; color: var(--text-secondary); }
.view-list .col-owner img { width: 24px; height: 24px; border-radius: 50%; margin-right: 8px; }
.view-list .item-checkbox { display: block; position: relative; top: 0; left: 0; opacity: 0.3; accent-color: var(--accent); }
.view-list .drive-folder-card:hover .item-checkbox, .view-list .drive-file-card:hover .item-checkbox { opacity: 1; }
.view-list .action-wrapper { display: flex !important; position: static !important; }
.view-list #driveListHeader { display: grid !important; color: var(--text-secondary) !important; border-bottom: 1px solid var(--border) !important; }
.view-list #driveListHeader a { color: var(--text-secondary) !important; }

/* Toolbar & Misc */
.toolbar-main { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.btn-new { display: flex; align-items: center; gap: 8px; background: var(--accent); color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(99,102,241,0.3); }
.btn-new:hover { background: var(--accent-2); transform: translateY(-1px); box-shadow: 0 6px 16px rgba(99,102,241,0.4); }
.btn-icon { background: var(--surface-2); color: var(--text-main); border: 1px solid var(--border); width: 40px; height: 40px; border-radius: 10px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
.btn-icon:hover { background: var(--surface-3); border-color: var(--border-md); }
.btn-dots { background: transparent; color: var(--text-secondary); border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; transition: all 0.2s; }
.btn-dots:hover { background: var(--surface-3); color: var(--text-main); }
.view-toggle { display: flex; background: var(--surface-2); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; }
.view-toggle button { background: transparent; color: var(--text-secondary); border: none; padding: 8px 12px; cursor: pointer; transition: all 0.2s; }
.view-toggle button:hover { color: var(--text-main); }
.view-toggle button.active { background: var(--surface-3); color: var(--accent); }
.bulk-toolbar { background: var(--accent); color: white; padding: 12px 24px; border-radius: 12px; display: flex; align-items: center; gap: 16px; margin-bottom: 24px; box-shadow: 0 4px 16px rgba(99,102,241,0.2); }
.bulk-btn { background: rgba(255,255,255,0.2); color: white; border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer; transition: background 0.2s; }
.bulk-btn:hover { background: rgba(255,255,255,0.3); }
.empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 20px; background: var(--surface-2); border: 1px dashed var(--border-md); border-radius: 16px; text-align: center; margin-top: 40px; cursor: pointer; transition: background 0.2s; }
.empty-state:hover { background: var(--surface-3); }

/* Right Sidebar Detail */
.right-sidebar { background: var(--surface-2); border-left: 1px solid var(--border); color: var(--text-main); padding: 20px; box-shadow: -4px 0 24px rgba(0,0,0,0.2); }
.rs-close { color: var(--text-secondary); }
.rs-close:hover { color: var(--text-main); background: var(--surface-3); }
.detail-icon-lg { background: var(--surface-3); border-radius: 16px; display: flex; align-items: center; justify-content: center; width: 80px; height: 80px; margin: 0 auto 20px; font-size: 2.5rem; }
.prop-label { color: var(--text-secondary); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
.prop-value { color: var(--text-main); font-weight: 500; }

<?php endif; ?>
</style>
</head>
<body>
<div class="top-navbar">
<?php if($current_page === 'workspace'): ?>
    <div class="header-left" style="width: 256px;">
        <button class="btn-icon btn-menu" onclick="toggleSidebar()" style="margin-right: 4px;"><i class="fa-solid fa-bars"></i></button>
        <div class="logo-mark" onclick="window.location='index.php?page=beranda'" style="cursor:pointer; display:flex; align-items:center; gap:8px;">
          <img src="https://upload.wikimedia.org/wikipedia/commons/1/12/Google_Drive_icon_%282020%29.svg" alt="Logo" style="height:40px; width:40px;">
          <span style="font-family:'Product Sans', 'Google Sans', sans-serif; font-size:1.35rem; font-weight:400; color:#5f6368;">Drive</span>
        </div>
    </div>
    <div class="header-center" style="justify-content: flex-start; padding-left: 0;">
        <div class="search-bar" style="max-width: 720px; width: 100%;">
            <form method="GET" action="index.php" style="position:relative;display:flex;align-items:center;width:100%;">
                <input type="hidden" name="page" value="workspace">
                <?php if($active_folder) echo "<input type='hidden' name='folder_id' value='{$active_folder}'>"; ?>
                <button type="submit" style="position:absolute;left:16px;background:none;border:none;color:#444746;cursor:pointer;"><i class="fa-solid fa-magnifying-glass" style="font-size: 1.1rem;"></i></button>
                <input type="text" name="q" placeholder="Telusuri di Drive" value="<?= h($search_query) ?>" autocomplete="off" style="width:100%; border-radius: 24px; padding: 12px 48px 12px 56px; background: #e9eef6; border: none; font-size: 1rem; color: #1f1f1f; font-family: 'Google Sans', 'Inter', sans-serif; outline: none;">
                <i class="fa-solid fa-sliders" style="position:absolute; right:20px; color:#444746; cursor:pointer; font-size: 1.1rem;"></i>
            </form>
        </div>
    </div>
    <div class="header-right">
        <button class="btn-icon" style="margin-right:8px;"><i class="fa-regular fa-circle-question" style="font-size:1.3rem;"></i></button>
        <button class="btn-icon" style="margin-right:8px;"><i class="fa-solid fa-gear" style="font-size:1.3rem;"></i></button>
        <button class="btn-icon" style="margin-right:16px;"><i class="fa-solid fa-braille" style="font-size:1.3rem;"></i></button>
        <div class="profile-container">
            <img src="<?= h($path_foto) ?>" alt="Profile" class="avatar" onclick="toggleProfileMenu()" style="width:32px;height:32px;border-radius:50%;cursor:pointer;">
            <div id="profileMenu" class="profile-menu">
                <div class="profile-header-info">
                    <img src="<?= h($path_foto) ?>" alt="">
                    <div>
                        <strong><?= h(!empty($profile_data['identitas']['nama_sebutan'])?$profile_data['identitas']['nama_sebutan']:$nama_lengkap) ?></strong>
                        <span><?= h($role) ?></span>
                    </div>
                </div>
                <div class="profile-menu-links">
                    <button onclick="openModal('settingsModal');closeAllMenus();"><i class="fa-solid fa-gear"></i> Pengaturan Akun</button>
                    <hr class="menu-divider">
                    <a href="?logout=true" style="color:#d93025;"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="header-left">
        <button class="btn-icon btn-menu" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
        <div class="logo-mark" onclick="window.location='index.php?page=beranda'" style="cursor:pointer;">
          <img src="07_aset_visual/images/LOGO_GAWE.svg" alt="Logo" onerror="this.style.display='none'">
          <span>WORKSPACE</span>
        </div>
        <?php if(isSuperAdmin()){?><span class="sa-badge"><i class="fa-solid fa-crown" style="margin-right:3px;font-size:.8em;"></i>God Mode</span><?php }?>
    </div>
    <div class="header-center">
        <div class="search-bar">
            <form method="GET" action="index.php">
                <input type="hidden" name="page" value="workspace">
                <?php if($active_folder) echo "<input type='hidden' name='folder_id' value='{$active_folder}'>"; ?>
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" placeholder="Cari dokumen atau folder..." value="<?= h($search_query) ?>" autocomplete="off">
            </form>
        </div>
    </div>
    <div class="header-right">
        <span class="stats-badge"><?= $size_used ?> / 1 GB</span>
        <div class="profile-container">
            <img src="<?= h($path_foto) ?>" alt="Profile" class="avatar" onclick="toggleProfileMenu()">
            <div id="profileMenu" class="profile-menu">
                <div class="profile-header-info">
                    <img src="<?= h($path_foto) ?>" alt="">
                    <div>
                        <strong><?= h(!empty($profile_data['identitas']['nama_sebutan'])?$profile_data['identitas']['nama_sebutan']:$nama_lengkap) ?></strong>
                        <span><?= h($role) ?><?= !empty($profile_data['identitas']['profesi'])?' &middot; '.h($profile_data['identitas']['profesi']):'' ?></span>
                    </div>
                </div>
                <div class="profile-menu-links">
                    <a href="index.php?page=beranda"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                    <a href="index.php?page=workspace"><i class="fa-solid fa-folder-open"></i> Workspace</a>
                    <a href="index.php?page=profile"><i class="fa-solid fa-id-card"></i> CV Builder</a>
                    <a href="<?= h($portfolio_url) ?>" target="_blank"><i class="fa-solid fa-globe"></i> Lihat Portfolio</a>
                    <?php if(isSuperAdmin()){?><a href="index.php?page=manajemen-pengguna" style="color:var(--superadmin);"><i class="fa-solid fa-users-gear"></i> Manajemen User</a><?php }?>
                    <hr class="menu-divider">
                    <button onclick="openModal('settingsModal');closeAllMenus();"><i class="fa-solid fa-gear"></i> Pengaturan Akun</button>
                    <hr class="menu-divider">
                    <a href="?logout=true" style="color:var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
</div>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
<div class="sidebar" id="sidebar">
<?php if($current_page === 'workspace'): ?>
    <div style="padding: 8px 16px 16px;">
        <button class="btn-drive-new" onclick="openModal('addItemModal');switchType('file');">
            <svg width="36" height="36" viewBox="0 0 36 36"><path fill="#EA4335" d="M16 16v-10h4v10z"></path><path fill="#FBBC05" d="M26 16v4h-10v-4z"></path><path fill="#4285F4" d="M16 26v-10h-4v10z"></path><path fill="#34A853" d="M6 16v4h10v-4z"></path></svg>
            <span>Baru</span>
        </button>
    </div>
    
    <a href="index.php?page=beranda" class="nav-item <?= $current_page==='beranda'?'active':'' ?>"><i class="fa-solid fa-house"></i> Beranda</a>
    <a href="index.php?page=workspace" class="nav-item <?= ($current_page==='workspace' && empty($_GET['view']))?'active':'' ?>"><i class="fa-brands fa-google-drive"></i> Drive Saya</a>
    <a href="#" class="nav-item"><i class="fa-solid fa-computer"></i> Komputer</a>
    <a href="#" class="nav-item"><i class="fa-solid fa-user-group"></i> Dibagikan kepada saya</a>
    <a href="index.php?page=workspace&view=recent" class="nav-item <?= ($current_page==='workspace'&&($_GET['view']??'')==='recent')?'active':'' ?>"><i class="fa-regular fa-clock"></i> Terbaru</a>
    <a href="#" class="nav-item"><i class="fa-regular fa-star"></i> Berbintang</a>
    <a href="#" class="nav-item"><i class="fa-solid fa-circle-exclamation"></i> Spam</a>
    <a href="index.php?page=workspace&view=trash" class="nav-item <?= ($current_page==='workspace'&&($_GET['view']??'')==='trash')?'active':'' ?>"><i class="fa-regular fa-trash-can"></i> Sampah</a>
    
    <div style="margin-top: 16px; padding: 16px 20px;">
        <a href="index.php?page=workspace&view=stats" style="display:flex; align-items:center; gap:12px; color:#444746; font-size:0.875rem; text-decoration:none; margin-bottom:8px;"><i class="fa-solid fa-cloud" style="font-size:1.1rem; width:24px;"></i> Penyimpanan</a>
        <div class="sidebar-storage" style="padding-left:0; margin-top:12px;">
            <div class="storage-bar" style="background:#e0e0e0; height:4px; border-radius:2px;"><div class="storage-bar-fill" style="background:#0b57d0; height:100%; border-radius:2px; width:<?= $storage_pct ?>%;"></div></div>
            <div class="storage-text" style="font-size:0.8rem; color:#444746; margin-top:8px;"><?= $size_used ?> dari 15 GB telah digunakan</div>
            <button style="margin-top:12px; width:100%; padding:8px 16px; border-radius:20px; border:1px solid #c2e7ff; background:transparent; color:#0b57d0; font-weight:600; cursor:pointer; font-size:0.875rem; transition:background 0.2s;" onmouseover="this.style.background='#f0f4f9'" onmouseout="this.style.background='transparent'">Dapatkan penyimpanan ekstra</button>
        </div>
    </div>
<?php else: ?>
    <div class="sidebar-section">
        <div class="sidebar-section-label">Main</div>
        <a href="index.php?page=beranda" class="nav-item <?= $current_page==='beranda'?'active':'' ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="index.php?page=workspace" class="nav-item <?= $current_page==='workspace'?'active':'' ?>"><i class="fa-solid fa-folder-open"></i> Workspace</a>
        <a href="index.php?page=workspace&view=recent" class="nav-item"><i class="fa-solid fa-clock-rotate-left"></i> Akses Terbaru</a>
        <a href="index.php?page=workspace&view=assets" class="nav-item"><i class="fa-solid fa-images"></i> Aset Visual</a>
        <a href="index.php?page=workspace&view=stats" class="nav-item"><i class="fa-solid fa-chart-bar"></i> Statistik</a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-section-label">Profil</div>
        <a href="index.php?page=profile" class="nav-item <?= $current_page==='profile'?'active':'' ?>"><i class="fa-solid fa-id-card"></i> CV Builder</a>
        <a href="<?= h($portfolio_url) ?>" target="_blank" class="nav-item"><i class="fa-solid fa-globe"></i> Lihat Portfolio</a>
        <a href="index.php" target="_blank" class="nav-item"><i class="fa-solid fa-users"></i> Direktori Talent</a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-section-label">Lainnya</div>
        <a href="index.php?page=workspace&view=trash" class="nav-item"><i class="fa-solid fa-trash-can"></i> Tong Sampah</a>
    </div>
    <?php if(isSuperAdmin()){?>
    <div class="sidebar-section">
        <div class="sidebar-section-label" style="color:var(--superadmin);">God Mode</div>
        <a href="index.php?page=manajemen-pengguna" class="nav-item superadmin-item <?= $current_page==='manajemen-pengguna'?'active':'' ?>"><i class="fa-solid fa-users-gear"></i> Manajemen User</a>
    </div>
    <?php }?>
    <div class="sidebar-storage">
        <div class="storage-label">Penyimpanan</div>
        <div class="storage-bar"><div class="storage-bar-fill" style="width:<?= $storage_pct ?>%;"></div></div>
        <div class="storage-text"><?= $size_used ?> dari 1 GB</div>
    </div>
<?php endif; ?>
</div>

<div class="main-wrapper">
<div class="content-area" id="mainContextArea">
<?php if(!empty($alert_msg)){
    $at=(str_contains($alert_msg,'gagal')||str_contains($alert_msg,'tidak valid')||str_contains($alert_msg,'Sesi'))?'error':'success';
    $ico=($at==='success')?'fa-circle-check':'fa-circle-exclamation';
    echo "<div class='alert-bar $at'><i class='fa-solid $ico'></i> ".h($alert_msg)."</div>";
}?>

<?php
        require_once '02_dasbor_utama/3_tampilan_beranda.php';
        require_once '03_pengelola_drive/3_tampilan_workspace.php';
        require_once '04_pembuat_cv/1_tampilan_cv_builder.php';
        require_once '05_panel_superadmin/1_tampilan_pengguna.php';
    </div><!-- end content-area -->

    <!-- RIGHT SIDEBAR -->
    <?php if($current_page==='workspace'){?>
    <div class="right-sidebar" id="rightSidebar">
        <div class="rs-header">
            <h3 id="rs_title"><i class="fa-solid fa-circle-info"></i> Detail Item</h3>
            <button class="btn-icon" onclick="toggleRightSidebar()" style="width:30px;height:30px;font-size:.85rem;border:none;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="rs-content">
            <div id="rs_icon" class="rs-preview"><i class="fa-solid fa-folder" style="font-size:3rem;"></i></div>
            <div id="rs_actions" class="rs-action-buttons"></div>
            <div class="rs-qr-box" id="rs_qr_container"><img id="rs_qr_img" src="" alt="QR Code"><p>Scan QR untuk berbagi</p></div>
            <div class="rs-group"><label>Nama</label><div class="rs-val" id="rs_name">&mdash;</div></div>
            <div class="rs-group"><label>Jenis</label><div class="rs-val" id="rs_type">&mdash;</div></div>
            <div class="rs-group"><label>Pemilik</label><div class="rs-val" id="rs_owner">&mdash;</div></div>
            <div class="rs-group"><label>Tanggal</label><div class="rs-val" id="rs_date">&mdash;</div></div>
            <div class="rs-group"><label>Ukuran</label><div class="rs-val" id="rs_size">&mdash;</div></div>
            <div class="rs-group"><label>Catatan</label><div class="rs-val" id="rs_desc">&mdash;</div></div>
            <div class="rs-group"><label>Label</label><div class="rs-val" id="rs_tags">&mdash;</div></div>
        </div>
    </div>
    <?php }?>
</div><!-- end main-wrapper -->

<!-- PREVIEW MODAL -->
<div class="preview-overlay" id="previewOverlay">
    <div class="preview-header">
        <div class="preview-filename"><i class="fa-solid fa-file"></i> <span id="previewFileName">File</span></div>
        <div class="preview-actions">
            <a href="#" id="previewDownloadBtn"><i class="fa-solid fa-download"></i> Download</a>
            <a href="#" id="previewOpenBtn" target="_blank"><i class="fa-regular fa-eye"></i> Buka Tab Baru</a>
            <button onclick="closePreview()"><i class="fa-solid fa-xmark"></i> Tutup</button>
        </div>
    </div>
    <div class="preview-body" id="previewBody"></div>
</div>

<!-- CONFIRM DIALOG -->
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <div class="confirm-icon" id="confirmIcon">&#9888;&#65039;</div>
        <h3 id="confirmTitle">Konfirmasi</h3>
        <p id="confirmMessage">Apakah Anda yakin?</p>
        <div class="confirm-btns">
            <button class="confirm-cancel" onclick="closeConfirm()">Batal</button>
            <button class="confirm-danger" id="confirmActionBtn" onclick="executeConfirmAction()">Konfirmasi</button>
        </div>
    </div>
</div>

<!-- DROP OVERLAY -->
<div class="global-drop-overlay" id="globalDropOverlay">
    <div style="text-align:center;">
        <i class="fa-solid fa-cloud-arrow-up" style="font-size:5rem;margin-bottom:20px;display:block;animation:bounce 2s infinite;"></i>
        <div class="drop-pill">Lepaskan untuk mengunggah<?php if($active_folder) echo ' ke folder ini';?></div>
    </div>
</div>

<div id="toast"></div>

<!-- MOBILE BOTTOM NAV -->
<div class="bottom-nav">
    <a href="index.php?page=beranda" class="bottom-nav-item <?= $current_page==='beranda'?'active':'' ?>"><i class="fa-solid fa-gauge"></i><span>Dashboard</span></a>
    <a href="index.php?page=workspace" class="bottom-nav-item <?= $current_page==='workspace'?'active':'' ?>"><i class="fa-solid fa-folder-open"></i><span>Files</span></a>
    <a href="index.php?page=profile" class="bottom-nav-item <?= $current_page==='profile'?'active':'' ?>"><i class="fa-solid fa-id-card"></i><span>Profil</span></a>
    <?php if(isSuperAdmin()){?><a href="index.php?page=manajemen-pengguna" class="bottom-nav-item <?= $current_page==='manajemen-pengguna'?'active':'' ?>"><i class="fa-solid fa-users-gear"></i><span>Users</span></a><?php }?>
    <a href="?logout=true" class="bottom-nav-item"><i class="fa-solid fa-right-from-bracket"></i><span>Keluar</span></a>
</div>

<?php if($current_page==='workspace'&&$active_folder){?>
<div class="fab-container">
    <div class="fab-menu" id="fabMenu">
        <button class="fab-item" onclick="openModal('addFolderModal');toggleFab();"><i class="fa-solid fa-folder-plus"></i> Folder Baru</button>
        <button class="fab-item" onclick="openModal('addItemModal');switchType('file');toggleFab();"><i class="fa-solid fa-file-arrow-up"></i> Upload File</button>
        <button class="fab-item" onclick="openModal('addItemModal');switchType('link');toggleFab();"><i class="fa-solid fa-link"></i> Simpan Tautan</button>
    </div>
    <button class="fab" id="fabBtn" onclick="toggleFab()"><i class="fa-solid fa-plus"></i></button>
</div>
<div class="mobile-panel-overlay" id="mobilePanelOverlay" onclick="closeMobilePanel()"></div>
<div class="mobile-detail-panel" id="mobileDetailPanel">
    <div class="mobile-panel-handle"></div>
    <div id="mobileDetailContent" style="padding:16px 20px 32px;"></div>
</div>
<?php }?>


<!-- âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
require_once '02_dasbor_utama/4_komponen_modal.php';
     JAVASCRIPT â COMPLETE
ââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ -->
<script>
const CSRF = '<?= h($csrf_token) ?>';
const CURRENT_PAGE = '<?= h($current_page) ?>';

// ââ SIDEBAR ââââââââââââââââââââââââââââââââââââââââââââââââââ
let sidebarOpen = false;
function toggleSidebar() {
    const sb = document.getElementById('sidebar');
    const ov = document.getElementById('sidebarOverlay');
    sidebarOpen = !sidebarOpen;
    if (sidebarOpen) { sb.classList.add('active'); ov.classList.add('active'); document.body.style.overflow = 'hidden'; }
    else { sb.classList.remove('active'); ov.classList.remove('active'); document.body.style.overflow = ''; }
}

// ââ PROFILE MENU âââââââââââââââââââââââââââââââââââââââââââââ
function toggleProfileMenu() {
    document.getElementById('profileMenu').classList.toggle('show');
}
function closeAllMenus() {
    document.getElementById('profileMenu').classList.remove('show');
    document.querySelectorAll('.action-dropdown.show').forEach(d => d.classList.remove('show'));
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.profile-container')) document.getElementById('profileMenu').classList.remove('show');
    if (!e.target.closest('.action-wrapper') && !e.target.closest('.btn-dots')) document.querySelectorAll('.action-dropdown.show').forEach(d => d.classList.remove('show'));
    if (!e.target.closest('.dropdown')) document.querySelectorAll('.dropdown-content').forEach(d => d.style.display = '');
});

// ââ MODALS âââââââââââââââââââââââââââââââââââââââââââââââââââ
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = '';
}
document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('click', function(e) { if (e.target === m) closeModal(m.id); });
});
function openEditModal(id, nama, desc, icon, warna) {
    document.getElementById('edit_folder_id').value = id;
    document.getElementById('edit_folder_nama').value = nama;
    document.getElementById('edit_folder_desc').value = desc;
    document.getElementById('edit_folder_icon').value = icon || 'fa-folder';
    document.getElementById('edit_folder_warna').value = warna || '#0a0a0a';
    openModal('editFolderModal');
}
function openMoveModal(type, id, name) {
    document.getElementById('move_type_input').value = type;
    document.getElementById('move_id_input').value = id;
    document.getElementById('move_item_name').textContent = 'ð¦ ' + name;
    openModal('moveModal');
}

// ââ RIGHT SIDEBAR âââââââââââââââââââââââââââââââââââââââââââââ
let isSidebarOpen = false;
function toggleRightSidebar() {
    const rs = document.getElementById('rightSidebar');
    if (!rs) return;
    isSidebarOpen = !isSidebarOpen;
    if (isSidebarOpen) rs.classList.add('active'); else rs.classList.remove('active');
}

// ââ TOAST âââââââââââââââââââââââââââââââââââââââââââââââââââââ
function showToast(msg) {
    const t = document.getElementById('toast');
    if (!t) return;
    t.innerHTML = msg;
    t.classList.remove('show');
    void t.offsetWidth;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3700);
}

// ââ VIEW MODE âââââââââââââââââââââââââââââââââââââââââââââââââ
function setViewMode(mode) {
    const container = document.getElementById('workspaceContainer');
    const btnListEl = document.getElementById('btnList');
    const btnGridEl = document.getElementById('btnGrid');
    if (!container) return;
    if (mode === 'list') { container.className = 'view-list'; if (btnListEl) btnListEl.classList.add('active'); if (btnGridEl) btnGridEl.classList.remove('active'); }
    else { container.className = 'view-grid'; if (btnGridEl) btnGridEl.classList.add('active'); if (btnListEl) btnListEl.classList.remove('active'); }
    localStorage.setItem('viewMode', mode);
}
setViewMode(localStorage.getItem('viewMode') || 'list');

// ââ ITEM CLICK (select + right sidebar) ââââââââââââââââââââââ
function handleItemClick(event, el) {
    if (event.target.classList.contains('item-checkbox')) return;
    if (event.target.closest('.action-wrapper')) return;
    document.querySelectorAll('#workspaceContainer .item-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    selectItem(el);
}
function selectItem(el) {
    if (window.innerWidth <= 768) { openMobilePanel(el); return; }
    if (!isSidebarOpen) toggleRightSidebar();
    const { type, name, icon: iconClass, color, owner, date, size, desc, url: fileUrl, tags, share: shareLink } = el.dataset;
    const ah = el.querySelector('.hidden-action-html');
    document.getElementById('rs_title').innerHTML = type === 'folder' ? '<i class="fa-solid fa-folder"></i> Detail Folder' : '<i class="fa-solid fa-file"></i> Detail File';
    const previewIcon = document.getElementById('rs_icon');
    const isImage = fileUrl && /\.(png|jpe?g|gif|webp|svg)$/i.test(name);
    if (isImage) {
        previewIcon.innerHTML = `<img src="${fileUrl}" style="max-width:100%;max-height:180px;object-fit:contain;" onerror="this.outerHTML='<i class=\\'${iconClass}\\' style=\\'font-size:3rem;\\'></i>'">`;
        previewIcon.style.cssText = 'padding:8px;background:#f5f5f5;border:1px solid var(--border);text-align:center;margin-bottom:16px;min-height:120px;display:flex;align-items:center;justify-content:center;overflow:hidden;';
    } else {
        previewIcon.innerHTML = `<i class="${iconClass}" style="font-size:3rem;"></i>`;
        previewIcon.style.cssText = `padding:28px;background:#f5f5f5;border:1px solid var(--border);text-align:center;margin-bottom:16px;display:flex;align-items:center;justify-content:center;`;
    }
    document.getElementById('rs_name').innerText = name;
    document.getElementById('rs_type').innerText = type === 'folder' ? 'Folder' : (type === 'link' ? 'Tautan Website' : 'File Dokumen');
    document.getElementById('rs_owner').innerText = owner;
    document.getElementById('rs_date').innerText = date;
    document.getElementById('rs_size').innerText = size;
    document.getElementById('rs_desc').innerText = (desc && desc !== '-') ? desc : 'Tidak ada catatan.';
    document.getElementById('rs_tags').innerText = (tags && tags !== '') ? tags : 'Tidak ada label';
    const actCont = document.getElementById('rs_actions');
    if (ah) { actCont.innerHTML = ah.innerHTML; actCont.style.display = 'flex'; } else { actCont.innerHTML = ''; actCont.style.display = 'none'; }
    const qrCont = document.getElementById('rs_qr_container'), qrImg = document.getElementById('rs_qr_img');
    if (shareLink && shareLink !== '') { qrCont.style.display = 'block'; qrImg.src = 'https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=' + encodeURIComponent(shareLink); }
    else { qrCont.style.display = 'none'; }
}

// ââ MOBILE PANEL âââââââââââââââââââââââââââââââââââââââââââââ
function openMobilePanel(el) {
    const o = document.getElementById('mobilePanelOverlay');
    const p = document.getElementById('mobileDetailPanel');
    const c = document.getElementById('mobileDetailContent');
    if (!o || !p || !c) return;
    const ah = el.querySelector('.hidden-action-html');
    const html_a = ah ? ah.innerHTML : '';
    c.innerHTML = `<div style="text-align:center;margin-bottom:16px;"><div style="font-size:2.5rem;margin-bottom:10px;"><i class="${el.dataset.icon}"></i></div><h3 style="margin:0;font-size:1.05rem;font-family:'Playfair Display',serif;word-wrap:break-word;">${el.dataset.name}</h3></div><div style="display:flex;flex-direction:column;gap:0;border:1px solid var(--border-dark);">${html_a}</div>`;
    o.classList.add('active'); p.classList.add('active');
}
function closeMobilePanel() {
    const o = document.getElementById('mobilePanelOverlay'), p = document.getElementById('mobileDetailPanel');
    if (o) o.classList.remove('active'); if (p) p.classList.remove('active');
}

// ââ CHECKBOXES & BULK âââââââââââââââââââââââââââââââââââââââââ
function handleCheckbox(event, cb) {
    event.stopPropagation();
    const card = cb.closest('.item-card');
    if (cb.checked) card.classList.add('selected'); else card.classList.remove('selected');
    updateBulkToolbar();
}
function toggleSelectAll(master) {
    document.querySelectorAll('#workspaceContainer .item-checkbox:not(#selectAllMain):not(#selectAllHeader)').forEach(cb => {
        cb.checked = master.checked;
        const card = cb.closest('.item-card');
        if (card) { if (master.checked) card.classList.add('selected'); else card.classList.remove('selected'); }
    });
    updateBulkToolbar();
}
function getSelectedItems() {
    const items = [];
    document.querySelectorAll('#workspaceContainer .item-card.selected').forEach(c => { items.push({ id: c.dataset.id, type: c.dataset.itemType }); });
    return items;
}
function updateBulkToolbar() {
    const items = getSelectedItems();
    const tb = document.getElementById('bulkToolbar');
    const bc = document.getElementById('bulkCount');
    if (!tb) return;
    if (items.length > 0) { tb.classList.add('active'); if (bc) bc.textContent = items.length + ' dipilih'; }
    else tb.classList.remove('active');
}
function deselectAll() {
    document.querySelectorAll('#workspaceContainer .item-card').forEach(c => c.classList.remove('selected'));
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
    const tb = document.getElementById('bulkToolbar'); if (tb) tb.classList.remove('active');
}
function bulkDelete() {
    const items = getSelectedItems(); if (items.length === 0) return;
    showConfirm('Hapus ' + items.length + ' Item?', 'Item dipindahkan ke Tong Sampah. Bisa dipulihkan nanti.', function() {
        document.getElementById('bulkDeleteIds').value   = JSON.stringify(items.map(i => i.id));
        document.getElementById('bulkDeleteTypes').value = JSON.stringify(items.map(i => i.type));
        document.getElementById('bulkDeleteForm').submit();
    });
}
function bulkMove() { const items = getSelectedItems(); if (items.length === 0) return; openModal('bulkMoveModal'); }
function executeBulkMove() {
    const items = getSelectedItems();
    const target = document.getElementById('bulkMoveTargetSelect').value;
    document.getElementById('bulkMoveIds').value    = JSON.stringify(items.map(i => i.id));
    document.getElementById('bulkMoveTypes').value  = JSON.stringify(items.map(i => i.type));
    document.getElementById('bulkMoveTarget').value = target;
    document.getElementById('bulkMoveForm').submit();
}

// ââ CONFIRM DIALOG ââââââââââââââââââââââââââââââââââââââââââââ
let confirmCallback = null;
function showConfirm(title, message, callback, icon = 'â ï¸') {
    document.getElementById('confirmTitle').textContent   = title;
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmIcon').textContent    = icon;
    document.getElementById('confirmOverlay').classList.add('active');
    confirmCallback = callback;
}
function closeConfirm() { document.getElementById('confirmOverlay').classList.remove('active'); confirmCallback = null; }
function executeConfirmAction() { if (confirmCallback) confirmCallback(); closeConfirm(); }

// ââ ACTION DROPDOWN TOGGLE âââââââââââââââââââââââââââââââââââ
function toggleActionMenu(event, id) {
    event.stopPropagation();
    const dd = document.getElementById(id);
    const isOpen = dd.classList.contains('show');
    document.querySelectorAll('.action-dropdown.show').forEach(d => d.classList.remove('show'));
    if (!isOpen) dd.classList.add('show');
}

// ââ INLINE RENAME âââââââââââââââââââââââââââââââââââââââââââââ
function startInlineRename(card) {
    if (!card) return;
    const nameEl = card.querySelector('.item-name');
    if (!nameEl || nameEl.querySelector('.rename-inline')) return;
    const oldName = nameEl.textContent.trim();
    const input = document.createElement('input');
    input.type = 'text'; input.value = oldName; input.className = 'rename-inline';
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); submitRename(card, input.value); }
        if (e.key === 'Escape') { e.preventDefault(); nameEl.textContent = oldName; }
    });
    input.addEventListener('blur', function() { if (nameEl.contains(input)) nameEl.textContent = oldName; });
    input.addEventListener('click', e => e.stopPropagation());
    nameEl.textContent = ''; nameEl.appendChild(input);
    input.focus(); input.select();
}
function submitRename(card, newName) {
    if (!newName.trim()) return;
    document.getElementById('renameItemId').value   = card.dataset.id;
    document.getElementById('renameItemType').value = card.dataset.itemType;
    document.getElementById('renameNewName').value  = newName.trim();
    document.getElementById('renameForm').submit();
}

// ââ PREVIEW MODAL âââââââââââââââââââââââââââââââââââââââââââââ
function openPreview(filename, fileUrl, previewType, fileId) {
    const overlay  = document.getElementById('previewOverlay');
    const body     = document.getElementById('previewBody');
    const fnEl     = document.getElementById('previewFileName');
    const dlBtn    = document.getElementById('previewDownloadBtn');
    const opBtn    = document.getElementById('previewOpenBtn');
    fnEl.textContent = filename;
    dlBtn.href = '?action=download_file&file_id=' + fileId;
    opBtn.href = '?action=view_file&file_id=' + fileId;
    body.innerHTML = '';
    if (previewType === 'image') {
        body.innerHTML = `<img src="${fileUrl}" alt="${filename}" style="cursor:zoom-in;" onclick="this.style.transform=this.style.transform==='scale(1.6)'?'none':'scale(1.6)';this.style.transition='transform .3s';">`;
    } else if (previewType === 'pdf') {
        body.innerHTML = `<iframe src="?action=view_file&file_id=${fileId}#toolbar=1" style="width:100%;height:100%;border:none;background:#fff;border-radius:0;"></iframe>`;
    } else if (previewType === 'video') {
        body.innerHTML = `<video controls autoplay style="max-width:90%;max-height:80vh;"><source src="${fileUrl}">Browser tidak mendukung video.</video>`;
    } else if (previewType === 'audio') {
        body.innerHTML = `<div style="text-align:center;color:#fff;"><i class="fa-solid fa-music" style="font-size:5rem;margin-bottom:24px;display:block;opacity:.5;"></i><h3 style="margin-bottom:20px;font-family:'Playfair Display',serif;">${filename}</h3><audio controls autoplay style="width:100%;max-width:500px;"><source src="${fileUrl}">Browser tidak mendukung audio.</audio></div>`;
    } else {
        body.innerHTML = `<div class="preview-unsupported"><i class="fa-solid fa-file-circle-question"></i><h3>Pratinjau Tidak Tersedia</h3><p>Format ini tidak bisa dipratinjau. Silakan download.</p></div>`;
    }
    overlay.classList.add('active');
}
function closePreview() { document.getElementById('previewOverlay').classList.remove('active'); document.getElementById('previewBody').innerHTML = ''; }

// ââ FAB âââââââââââââââââââââââââââââââââââââââââââââââââââââââ
function toggleFab() {
    const m = document.getElementById('fabMenu'), b = document.getElementById('fabBtn');
    if (!m) return;
    m.classList.toggle('active');
    b.innerHTML = m.classList.contains('active') ? '<i class="fa-solid fa-xmark"></i>' : '<i class="fa-solid fa-plus"></i>';
}

// ââ SWITCH TYPE (file/link in addItem modal) ââââââââââââââââââ
function switchType(type) {
    document.getElementById('jenis_input').value = type;
    const ff = document.getElementById('form_file'), fl = document.getElementById('form_link');
    const tf = document.getElementById('tabFile'), tl = document.getElementById('tabLink');
    if (type === 'file') {
        ff.style.display = 'block'; fl.style.display = 'none';
        if (tf) { tf.style.background = 'var(--text-main)'; tf.style.color = '#fff'; }
        if (tl) { tl.style.background = '#f5f5f5'; tl.style.color = 'var(--text-main)'; }
    } else {
        ff.style.display = 'none'; fl.style.display = 'block';
        if (tl) { tl.style.background = 'var(--text-main)'; tl.style.color = '#fff'; }
        if (tf) { tf.style.background = '#f5f5f5'; tf.style.color = 'var(--text-main)'; }
    }
}

// ââ FILE INPUT DISPLAY ââââââââââââââââââââââââââââââââââââââââ
const modalFileInput = document.getElementById('modal_file_input');
if (modalFileInput) {
    modalFileInput.addEventListener('change', function() {
        const list = document.getElementById('selectedFilesList');
        if (this.files.length > 0) {
            let html = '<div style="margin-top:10px;border:1px solid var(--border);">';
            for (let i = 0; i < this.files.length; i++) {
                html += `<div style="padding:8px 12px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;font-size:.83rem;"><i class="fa-solid fa-file" style="color:var(--text-muted);"></i> ${this.files[i].name} <span style="margin-left:auto;font-size:.72rem;color:var(--text-muted);">${(this.files[i].size/1024/1024).toFixed(2)} MB</span></div>`;
            }
            html += '</div>';
            list.innerHTML = html;
        }
    });
}
const uploadZone = document.getElementById('modalUploadZone');
if (uploadZone) {
    ['dragenter','dragover'].forEach(ev => uploadZone.addEventListener(ev, function(e){ e.preventDefault(); this.classList.add('dragover'); }));
    ['dragleave','drop'].forEach(ev => uploadZone.addEventListener(ev, function(e){ e.preventDefault(); this.classList.remove('dragover'); }));
    uploadZone.addEventListener('drop', function(e) {
        if (e.dataTransfer.files.length) { modalFileInput.files = e.dataTransfer.files; modalFileInput.dispatchEvent(new Event('change')); }
    });
}

// ââ DRAG & DROP BETWEEN FOLDERS ââââââââââââââââââââââââââââââ
document.addEventListener('dragstart', function(e) {
    const card = e.target.closest('.item-card');
    if (!card) { e.preventDefault(); return; }
    card.classList.add('dragging');
    e.dataTransfer.setData('text/plain', JSON.stringify({ id: card.dataset.id, type: card.dataset.itemType }));
    e.dataTransfer.effectAllowed = 'move';
});
document.addEventListener('dragend', function() {
    document.querySelectorAll('.item-card.dragging').forEach(c => c.classList.remove('dragging'));
    document.querySelectorAll('.item-card.drag-over').forEach(c => c.classList.remove('drag-over'));
});
document.addEventListener('dragover', function(e) {
    const card = e.target.closest('.item-card[data-type="folder"]');
    if (card && !card.classList.contains('dragging')) { e.preventDefault(); e.dataTransfer.dropEffect = 'move'; card.classList.add('drag-over'); }
});
document.addEventListener('dragleave', function(e) {
    const card = e.target.closest('.item-card[data-type="folder"]');
    if (card) card.classList.remove('drag-over');
});
document.addEventListener('drop', function(e) {
    const folderCard = e.target.closest('.item-card[data-type="folder"]');
    if (!folderCard) return;
    e.preventDefault(); folderCard.classList.remove('drag-over');
    try {
        const data = JSON.parse(e.dataTransfer.getData('text/plain'));
        if (data.id && folderCard.dataset.id) {
            const form = new FormData();
            form.append('action', 'drag_move'); form.append('csrf_token', CSRF);
            form.append('item_id', data.id); form.append('item_type', data.type);
            form.append('target_folder', folderCard.dataset.id);
            fetch('index.php', { method: 'POST', body: form })
                .then(r => r.json())
                .then(d => { if (d.ok) { showToast('<i class="fa-solid fa-check-circle"></i> Item dipindahkan!'); setTimeout(() => location.reload(), 900); } });
        }
    } catch(err) { /* desktop file drop handled by global overlay */ }
});

// ââ GLOBAL FILE DROP OVERLAY ââââââââââââââââââââââââââââââââââ
const dropOverlay = document.getElementById('globalDropOverlay');
const autoForm    = document.getElementById('autoUploadForm');
const autoInput   = document.getElementById('autoFileInput');
let dragCounter   = 0;
if (dropOverlay && autoInput) {
    document.addEventListener('dragenter', function(e) {
        if (!e.dataTransfer.types.includes('Files')) return;
        dragCounter++;
        dropOverlay.classList.add('active');
    });
    document.addEventListener('dragleave', function() {
        dragCounter = Math.max(0, dragCounter - 1);
        if (dragCounter === 0) dropOverlay.classList.remove('active');
    });
    document.addEventListener('dragover', function(e) { if (e.dataTransfer.types.includes('Files')) e.preventDefault(); });
    document.addEventListener('drop', function(e) {
        dragCounter = 0; dropOverlay.classList.remove('active');
        if (e.dataTransfer.files.length && autoForm) {
            autoInput.files = e.dataTransfer.files;
            autoForm.submit();
        }
    });
}

// ââ COPY LINK ââââââââââââââââââââââââââââââââââââââââââââââââ
function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => { showToast('<i class="fa-solid fa-check-circle"></i> Link berhasil disalin!'); });
}
function copyPortfolioLink() {
    const inp = document.getElementById('portfolioLinkInput');
    if (inp) navigator.clipboard.writeText(inp.value).then(() => { showToast('<i class="fa-solid fa-check-circle"></i> Link portfolio disalin!'); });
}

// ââ KEYBOARD SHORTCUTS ââââââââââââââââââââââââââââââââââââââââ
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeConfirm(); closePreview(); closeMobilePanel();
        document.querySelectorAll('.modal').forEach(m => { if (m.style.display === 'flex') closeModal(m.id); });
    }
    const focused = document.activeElement;
    const isInput = ['INPUT','TEXTAREA','SELECT'].includes(focused.tagName);
    if (!isInput) {
        const selected = document.querySelector('#workspaceContainer .item-card.selected');
        if (e.key === 'F2' && selected) { e.preventDefault(); startInlineRename(selected); }
        if (e.key === 'Delete' && selected) {
            e.preventDefault();
            showConfirm('Hapus Item?', 'Item akan dipindahkan ke Tong Sampah.', function() {
                const type = selected.dataset.itemType;
                if (type === 'folder') window.location = `?page=workspace&action=soft_delete_folder&id=${selected.dataset.id}`;
                else window.location = `?page=workspace&action=soft_delete_item&item_id=${selected.dataset.id}`;
            });
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
            e.preventDefault();
            const master = document.getElementById('selectAllMain');
            if (master) { master.checked = true; toggleSelectAll(master); }
        }
    }
});

// ââ PROFILE TAB SWITCHING âââââââââââââââââââââââââââââââââââââ
function switchTab(tabId) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    const panel = document.getElementById('tab-' + tabId);
    if (panel) panel.classList.add('active');
    document.querySelectorAll('.tab-btn').forEach(b => {
        if (b.getAttribute('onclick') && b.getAttribute('onclick').includes("'" + tabId + "'")) b.classList.add('active');
    });
}

// ââ AJAX PROFILE SAVE WITH SWEETALERT ââââââââââââââââââââââââ
function submitProfileForm(formId, label) {
    const form = document.getElementById(formId);
    if (!form) return;
    const fd = new FormData(form);
    fetch('index.php', { method: 'POST', body: fd })
        .then(r => { if (r.ok) return r.text(); throw new Error('Network error'); })
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Tersimpan!',
                text: label + ' berhasil disimpan.',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        })
        .catch(() => {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan. Coba lagi.' });
        });
}

// ââ DYNAMIC ACCORDION ITEM BUILDERS ââââââââââââââââââââââââââ
function _dynField(name, label, placeholder, value) {
    return `<div class="dyn-field"><label>${label}</label><input type="text" name="${name}" value="${value||''}" placeholder="${placeholder||label}"></div>`;
}
function _dynTextarea(name, placeholder) {
    return `<div class="dyn-field full-width"><label>Deskripsi</label><textarea name="${name}" rows="3" placeholder="${placeholder}"></textarea></div>`;
}
let eduCount = <?= count($profile_data['pendidikan'] ?? []) ?>;
let expCount = <?= count($profile_data['pengalaman'] ?? []) ?>;
let skillCount = <?= count($profile_data['keahlian'] ?? []) ?>;
let portoCount = <?= count($profile_data['portfolio'] ?? []) ?>;

function addEduItem() {
    const list = document.getElementById('edu-list');
    const div = document.createElement('div');
    div.className = 'dyn-item is-open';
    div.innerHTML = `
        <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
            <h4><i class="fa-solid fa-graduation-cap"></i> Pendidikan Baru <span class="dyn-preview"> &mdash; Isi data di bawah</span></h4>
            <div class="dyn-item-header-btns">
                <button type="button" class="btn-remove-dyn" onclick="event.stopPropagation();this.closest('.dyn-item').remove()"><i class="fa-solid fa-trash"></i> Hapus</button>
                <i class="fa-solid fa-chevron-down dyn-chevron"></i>
            </div>
        </div>
        <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
            ${_dynField('edu_institusi[]','Nama Institusi','cth: Universitas Indonesia')}
            ${_dynField('edu_gelar[]','Gelar / Jenjang','cth: S1 Teknik Informatika')}
            ${_dynField('edu_bidang[]','Bidang Studi','cth: Informatika')}
            ${_dynField('edu_mulai[]','Tahun Mulai','cth: 2020')}
            ${_dynField('edu_selesai[]','Tahun Selesai','cth: 2024 / Sekarang')}
            ${_dynTextarea('edu_desc[]','Prestasi, kegiatan, atau keterangan tambahan...')}
        </div></div></div>`;
    list.appendChild(div);
    div.querySelector('input') && div.querySelector('input').focus();
    eduCount++;
}

function addExpItem() {
    const list = document.getElementById('exp-list');
    const div = document.createElement('div');
    div.className = 'dyn-item is-open';
    div.innerHTML = `
        <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
            <h4><i class="fa-solid fa-briefcase"></i> Pengalaman Baru <span class="dyn-preview"> &mdash; Isi data di bawah</span></h4>
            <div class="dyn-item-header-btns">
                <button type="button" class="btn-remove-dyn" onclick="event.stopPropagation();this.closest('.dyn-item').remove()"><i class="fa-solid fa-trash"></i> Hapus</button>
                <i class="fa-solid fa-chevron-down dyn-chevron"></i>
            </div>
        </div>
        <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
            ${_dynField('exp_jabatan[]','Jabatan / Posisi','cth: UI/UX Designer')}
            ${_dynField('exp_perusahaan[]','Perusahaan / Organisasi','cth: PT Alfatih Digital')}
            ${_dynField('exp_periode[]','Periode','cth: 2022 â 2024')}
            ${_dynTextarea('exp_desc[]','Uraikan tanggung jawab, pencapaian, atau kontribusi Anda...')}
        </div></div></div>`;
    list.appendChild(div);
    div.querySelector('input') && div.querySelector('input').focus();
    expCount++;
}

function addSkillItem() {
    const i = skillCount++;
    const list = document.getElementById('skill-list');
    const div = document.createElement('div');
    div.className = 'dyn-item is-open';
    div.innerHTML = `
        <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
            <h4><i class="fa-solid fa-star"></i> Keahlian Baru <span class="dyn-preview"> &mdash; <strong>70%</strong></span></h4>
            <div class="dyn-item-header-btns">
                <button type="button" class="btn-remove-dyn" onclick="event.stopPropagation();this.closest('.dyn-item').remove()"><i class="fa-solid fa-trash"></i> Hapus</button>
                <i class="fa-solid fa-chevron-down dyn-chevron"></i>
            </div>
        </div>
        <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
            ${_dynField('skill_nama[]','Nama Keahlian','PHP, JavaScript, Figma...')}
            ${_dynField('skill_kategori[]','Kategori','Frontend, Backend, Design...')}
            <div class="dyn-field full-width">
                <label>Level: <span id="slv_n${i}" style="font-weight:700;">70%</span></label>
                <div class="skill-slider-wrap">
                    <input type="range" name="skill_level[]" min="10" max="100" step="5" value="70"
                        oninput="document.getElementById('slv_n${i}').textContent=this.value+'%'">
                    <span style="font-size:.82rem;font-weight:700;min-width:40px;text-align:right;">70%</span>
                </div>
            </div>
        </div></div></div>`;
    list.appendChild(div);
    div.querySelector('input') && div.querySelector('input').focus();
}

function addPortoItem() {
    const list = document.getElementById('porto-list');
    const div = document.createElement('div');
    div.className = 'dyn-item is-open';
    div.innerHTML = `
        <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
            <h4><i class="fa-solid fa-diagram-project"></i> Proyek Baru <span class="dyn-preview"> &mdash; Isi data di bawah</span></h4>
            <div class="dyn-item-header-btns">
                <button type="button" class="btn-remove-dyn" onclick="event.stopPropagation();this.closest('.dyn-item').remove()"><i class="fa-solid fa-trash"></i> Hapus</button>
                <i class="fa-solid fa-chevron-down dyn-chevron"></i>
            </div>
        </div>
        <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
            ${_dynField('porto_nama[]','Nama Proyek','cth: Website Company Profile')}
            ${_dynField('porto_url[]','URL / Link Proyek','https://...')}
            ${_dynField('porto_tech[]','Teknologi (pisah koma)','PHP, MySQL, Tailwind...')}
            ${_dynTextarea('porto_desc[]','Ceritakan proyek ini, tujuannya, dan peran Anda...')}
        </div></div></div>`;
    list.appendChild(div);
    div.querySelector('input') && div.querySelector('input').focus();
    portoCount++;
}

// ââ USER MANAGEMENT (SuperAdmin) âââââââââââââââââââââââââââââ
function openEditUserModal(id, username, nama, role) {
    document.getElementById('eu_id').value       = id;
    document.getElementById('eu_username').value = username;
    document.getElementById('eu_nama').value     = nama;
    document.getElementById('eu_role').value     = role;
    openModal('editUserModal');
}
function confirmDeleteUser(id, username) {
    showConfirm(
        'Hapus User "' + username + '"?',
        'Akun akan dihapus permanen. File mereka tetap tersimpan di database.',
        function() {
            document.getElementById('del_uid_input').value = id;
            document.getElementById('deleteUserForm').submit();
        }, 'ðï¸'
    );
}

// ââ AUTO SHOW TOAST for server-side alert âââââââââââââââââââââ
<?php if (!empty($alert_msg)) {
    $is_err = (str_contains($alert_msg,'gagal') || str_contains($alert_msg,'tidak valid') || str_contains($alert_msg,'Sesi'));
    if (!$is_err) { ?>
setTimeout(() => showToast('<i class="fa-solid fa-circle-check" style="margin-right:6px;color:#16a34a;"></i> <?= h($alert_msg) ?>'), 300);
<?php } } ?>

/* âââââââââââââââââââââââââââââââââââââââââââââââââââ
   DASHBOARD MICRO-INTERACTIONS & ANIMATION v2
   Stagger reveals, accordion grid-rows, hover glow
   âââââââââââââââââââââââââââââââââââââââââââââââââââ */

// ââ Page Load: Stagger all stat blocks ââ
document.querySelectorAll('.bento-card,.stat-block,.ed-card,.section-card').forEach((el, i) => {
  el.classList.add('stagger-child');
  el.style.animationDelay = (0.03 + i * 0.05) + 's';
});

// ââ Accordion: Smooth grid-rows toggle ââ
function toggleAccordion(item) {
  const wasOpen = item.classList.contains('is-open');
  const list = item.closest('.dyn-list');
  if (list) {
    list.querySelectorAll('.dyn-item.is-open').forEach(open => {
      open.classList.remove('is-open');
    });
  }
  if (!wasOpen) item.classList.add('is-open');
}

// ââ Item Card: hover glow border effect ââ
document.querySelectorAll('.item-card').forEach(card => {
  card.addEventListener('mouseenter', () => {
    card.style.transition = 'background .15s,box-shadow .2s';
  });
});

// ââ Sidebar nav items: Ripple on click ââ
document.querySelectorAll('.nav-item').forEach(item => {
  item.addEventListener('click', function(e) {
    const ripple = document.createElement('span');
    const rect = this.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    Object.assign(ripple.style, {
      position: 'absolute',
      width: size + 'px', height: size + 'px',
      left: (e.clientX - rect.left - size/2) + 'px',
      top: (e.clientY - rect.top - size/2) + 'px',
      background: 'rgba(255,255,255,0.15)',
      borderRadius: '50%',
      transform: 'scale(0)',
      animation: 'ripple-expand .5s ease forwards',
      pointerEvents: 'none',
      zIndex: 0,
    });
    this.style.position = 'relative';
    this.style.overflow = 'hidden';
    this.appendChild(ripple);
    setTimeout(() => ripple.remove(), 600);
  });
});

const rippleStyle = document.createElement('style');
rippleStyle.textContent = `@keyframes ripple-expand{to{transform:scale(2.5);opacity:0;}}`;
document.head.appendChild(rippleStyle);

// ââ Smooth Scroll Reveal for content area ââ
const dashReveal = new IntersectionObserver(entries => {
  entries.forEach((e, i) => {
    if (e.isIntersecting) {
      e.target.style.opacity = '1';
      e.target.style.transform = 'translateY(0)';
      dashReveal.unobserve(e.target);
    }
  });
}, { threshold: 0.04, rootMargin: '0px 0px -32px 0px' });

document.querySelectorAll('.user-table tr,.activity-table tr,.profile-check-item').forEach((el, i) => {
  el.style.cssText += ';opacity:0;transform:translateY(8px);transition:opacity .4s ease ' + (i * 0.04) + 's,transform .4s ease ' + (i * 0.04) + 's';
  dashReveal.observe(el);
});

// ââ Storage bar animated fill on load ââ
document.querySelectorAll('.storage-bar-fill').forEach(bar => {
  const target = bar.style.width;
  bar.style.width = '0';
  setTimeout(() => {
    bar.style.transition = 'width .9s cubic-bezier(.16,1,.3,1)';
    bar.style.width = target;
  }, 400);
});

// ââ Avatar hover: remove grayscale ââ
document.querySelectorAll('.user-avatar-sm').forEach(img => {
  img.addEventListener('mouseenter', () => {img.style.filter = 'none';img.style.transform = 'scale(1.05)';});
  img.addEventListener('mouseleave', () => {img.style.filter = '';img.style.transform = '';});
});

// ââ PWA SERVICE WORKER ââââââââââââââââââââââââââââââââââââââââ
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js').catch(() => {});
}
</script>
<script>
  const CSRF = '<?= h($csrf_token) ?>';
  const CURRENT_USERNAME = '<?= h($username) ?>';
</script>
<script src="07_aset_visual/js/context_menu.js"></script>
</body>
</html>
