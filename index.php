<?php
// ============================================================
// ALFATIH DIGITAL WORKSPACE — ROUTER UTAMA
// Version 5.0 — Arsitektur Modular (MVC Sederhana)
// ============================================================

// 1. Panggil Pondasi Konfigurasi & Core
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/auth.php';

$csrf_token = generateCSRF();

// 2. Panggil Logika Aksi (Controller / Backend Process)
//    Catatan: File action hanya akan dieksekusi jika menerima $_POST / $_GET spesifik.
require_once __DIR__ . '/actions/auth_action.php';
require_once __DIR__ . '/actions/file_action.php';
require_once __DIR__ . '/actions/profile_action.php';
require_once __DIR__ . '/actions/user_action.php';

// 3. Routing Halaman Publik (Landing Page / Portfolio Eksternal)
if (empty($_SESSION['username'])) {
    $pub_page = $_GET['page'] ?? 'hub';

    if (isset($_GET['portfolio'])) {
        // Tampilkan halaman CV Portfolio Publik pengguna tertentu
        $uname = $_GET['portfolio'];
        $stmt = $mysqli->prepare("SELECT username, nama_lengkap, foto_profil, profile_data FROM users WHERE username=? AND role IN ('superadmin','admin','user')");
        $stmt->bind_param('s', $uname); $stmt->execute(); 
        $puser = $stmt->get_result()->fetch_assoc(); $stmt->close();

        if (!$puser) { die("Portfolio tidak ditemukan."); }
        
        // Mempersiapkan variabel yang dibutuhkan oleh views/pages/portfolio_page.php
        $pd = json_decode($puser['profile_data'] ?? '{}', true) ?? [];
        $pFoto = !empty($puser['foto_profil']) && $puser['foto_profil'] !== 'default.png' ? PROFILE_IMG_DIR . $puser['foto_profil'] : 'https://ui-avatars.com/api/?name=' . urlencode($puser['nama_lengkap'] ?? $uname) . '&background=1a1a1a&color=ffffff&bold=true&size=200';
        
        include __DIR__ . '/views/pages/portfolio_page.php';
        exit;
    } else {
        // Tampilkan halaman Landing Page / Form Login
        $talent_users = [];
        $res = $mysqli->query("SELECT username, nama_lengkap, foto_profil, profile_data FROM users WHERE role IN ('superadmin','admin','user') ORDER BY nama_lengkap ASC");
        while ($tu = $res->fetch_assoc()) {
            $tpd = json_decode($tu['profile_data'] ?? '{}', true) ?? [];
            if (!empty($tpd['identitas']['tampil_publik'])) { 
                $tu['_pd'] = $tpd; $talent_users[] = $tu; 
            }
        }
        
        // Error msg diambil dari /actions/auth_action.php jika login gagal
        include __DIR__ . '/views/pages/landing_page.php';
        exit;
    }
}

// 4. Routing Halaman Authenticated (Dasbor Internal)
//    Mempersiapkan Variabel Data Global untuk Seluruh Komponen Dasbor
$username  = $_SESSION['username'];
$role      = $_SESSION['role'];
$uid       = (int)($_SESSION['uid'] ?? 0);
$alert_msg = $alert_msg ?? ''; // Di-set oleh script action (profile/file action)

// Mengambil Data Profil User Aktif
$stmt = $mysqli->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
$stmt->bind_param('s', $username); $stmt->execute(); 
$data_user = $stmt->get_result()->fetch_assoc(); $stmt->close();

$nama_lengkap  = $data_user['nama_lengkap'] ?? $username;
$profile_data  = json_decode($data_user['profile_data'] ?? '{}', true) ?? [];
$display_name  = !empty($profile_data['identitas']['nama_sebutan']) ? $profile_data['identitas']['nama_sebutan'] : explode(' ', $nama_lengkap)[0];
$portfolio_url = SITE_URL . '/index.php?portfolio=' . urlencode($username);

// Menghitung Statistik Storage & File
$stat_files = 0; $stat_links = 0; $stat_size = 0; $stat_folders = 0;
$stmt = $mysqli->prepare("SELECT jenis, file_path FROM files WHERE owner_username=? AND is_deleted=0");
$stmt->bind_param('s', $username); $stmt->execute(); $res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
    if ($r['jenis'] === 'file') { 
        $stat_files++; $fp = UPLOAD_DIR.$r['file_path']; 
        if(file_exists($fp)) $stat_size += filesize($fp); 
    } else { $stat_links++; }
}
$stmt->close();

$stmt = $mysqli->prepare("SELECT COUNT(*) FROM folders WHERE owner_username=? AND is_deleted=0");
$stmt->bind_param('s', $username); $stmt->execute(); 
$stat_folders = $stmt->get_result()->fetch_row()[0]; $stmt->close();

$size_used = formatBytes($stat_size); 
$storage_limit = 1073741824; // 1 GB
$storage_pct = min(100, round(($stat_size / $storage_limit) * 100, 1));

// Daftar Semua Pengguna (Hanya diperlukan jika Superadmin/Admin)
$all_users = [];
if (isAdmin()) {
    $res = $mysqli->query("SELECT id, username, nama_lengkap, role, foto_profil, last_login FROM users ORDER BY username ASC");
    while ($u = $res->fetch_assoc()) $all_users[] = $u;
}

// Log Aktivitas Terbaru
$recent_activity = [];
$stmt = $mysqli->prepare("SELECT action, details, created_at, ip_address FROM admin_logs WHERE user_id=? ORDER BY created_at DESC LIMIT 10");
$stmt->bind_param('i', $uid); $stmt->execute(); $res = $stmt->get_result();
while ($ra = $res->fetch_assoc()) $recent_activity[] = $ra; $stmt->close();

// Foto Profil Global
$foto_profil = $data_user['foto_profil'] ?? '';
$path_foto   = ($foto_profil && $foto_profil !== 'default.png' && file_exists(PROFILE_IMG_DIR . $foto_profil))
    ? PROFILE_IMG_DIR . $foto_profil
    : 'https://ui-avatars.com/api/?name=' . urlencode($nama_lengkap) . '&background=1a1a1a&color=ffffff&bold=true';

// 5. Muat Tampilan Dashboard (Layout HTML)
$current_page = $_GET['page'] ?? 'beranda';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Workspace &mdash; <?= h($display_name) ?></title>
    <meta name="theme-color" content="#fafafa">
    <meta name="application-name" content="Alfatih Workspace">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="assets/images/LOGO_GAWE.svg">
    <link rel="icon" type="image/svg+xml" href="assets/images/LOGO_GAWE.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>

    <?php include __DIR__ . '/views/components/navbar.php'; ?>
    <?php include __DIR__ . '/views/components/sidebar.php'; ?>

    <div class="main-wrapper">
        <div class="content-area" id="mainContextArea">
            
            <?php 
            // Tampilkan baris peringatan sukses/error dari proses action jika ada
            if(!empty($alert_msg)){
                $at = (str_contains($alert_msg,'gagal') || str_contains($alert_msg,'tidak valid') || str_contains($alert_msg,'Sesi')) ? 'error' : 'success';
                $ico = ($at === 'success') ? 'fa-circle-check' : 'fa-circle-exclamation';
                echo "<div class='alert-bar $at'><i class='fa-solid $ico'></i> ".h($alert_msg)."</div>";
            }
            ?>

            <?php
            // MENYUNTIKKAN KONTEN HALAMAN TENGAH SESUAI URL (?page=...)
            if ($current_page === 'beranda') {
                include __DIR__ . '/views/dashboard/home.php';
            } elseif ($current_page === 'workspace') {
                include __DIR__ . '/views/dashboard/file_manager.php';
            } elseif ($current_page === 'profile') {
                include __DIR__ . '/views/dashboard/cv_builder.php';
            } elseif ($current_page === 'manajemen-pengguna') {
                include __DIR__ . '/views/dashboard/user_manager.php';
            }
            ?>

        </div><?php if ($current_page === 'workspace') include __DIR__ . '/views/components/right_sidebar.php'; ?>

    </div><?php include __DIR__ . '/views/components/modals.php'; ?>

    <?php if($current_page === 'workspace' && isset($active_folder) && $active_folder){ ?>
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
    <?php } ?>

    <div class="bottom-nav">
        <a href="index.php?page=beranda" class="bottom-nav-item <?= $current_page==='beranda'?'active':'' ?>"><i class="fa-solid fa-gauge"></i><span>Dashboard</span></a>
        <a href="index.php?page=workspace" class="bottom-nav-item <?= $current_page==='workspace'?'active':'' ?>"><i class="fa-solid fa-folder-open"></i><span>Files</span></a>
        <a href="index.php?page=profile" class="bottom-nav-item <?= $current_page==='profile'?'active':'' ?>"><i class="fa-solid fa-id-card"></i><span>Profil</span></a>
        <?php if(isSuperAdmin()){?><a href="index.php?page=manajemen-pengguna" class="bottom-nav-item <?= $current_page==='manajemen-pengguna'?'active':'' ?>"><i class="fa-solid fa-users-gear"></i><span>Users</span></a><?php }?>
        <a href="?logout=true" class="bottom-nav-item"><i class="fa-solid fa-right-from-bracket"></i><span>Keluar</span></a>
    </div>

    <?php if (!empty($alert_msg)) {
        $is_err = (str_contains($alert_msg,'gagal') || str_contains($alert_msg,'tidak valid') || str_contains($alert_msg,'Sesi'));
        if (!$is_err) { echo "<script>setTimeout(() => showToast('<i class=\"fa-solid fa-circle-check\" style=\"margin-right:6px;color:#16a34a;\"></i> " . h($alert_msg) . "'), 300);</script>"; }
    } ?>

    <script src="assets/js/app.js"></script>

</body>
</html>