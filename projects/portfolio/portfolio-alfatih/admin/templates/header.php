<?php
// Path yang benar untuk memanggil file dari root project
require_once dirname(__DIR__) . '/../config/constants.php';
require_once dirname(__DIR__) . '/../config/koneksi.php';
require_once dirname(__DIR__) . '/../includes/helpers.php';

// Buffer output untuk mencegah error "headers already sent"
ob_start();

// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek jika sudah login
if (!isset($_SESSION['admin_id']) && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

// Ambil jumlah notifikasi
$notificationCount = 0;
if (isset($_SESSION['admin_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE admin_id = :admin_id AND is_read = 0");
        $stmt->bindParam(':admin_id', $_SESSION['admin_id']);
        $stmt->execute();
        $notificationCount = $stmt->fetchColumn();
    } catch (PDOException $e) { /* Abaikan error jika tabel belum ada */ }
}

// Ambil pengaturan tema dari database
$defaultSettings = [
    'theme_color_type' => 'solid', 'theme_color_solid' => '#00e5ff',
    'theme_color_gradient_start' => '#00e5ff', 'theme_color_gradient_end' => '#7c4dff',
    'theme_color_gradient_angle' => '90'
];
try {
    $stmt = $pdo->query("SELECT kunci, nilai FROM pengaturan");
    $dbSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $themeSettings = array_merge($defaultSettings, $dbSettings);
} catch (Exception $e) {
    $themeSettings = $defaultSettings;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin Panel' ?> - <?= OWNER_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">

    <style>
        :root {
            <?php
            if ($themeSettings['theme_color_type'] === 'gradient') {
                $gradStart = htmlspecialchars($themeSettings['theme_color_gradient_start']);
                $gradEnd = htmlspecialchars($themeSettings['theme_color_gradient_end']);
                $gradAngle = htmlspecialchars($themeSettings['theme_color_gradient_angle']) . 'deg';

                echo "--primary-color: linear-gradient({$gradAngle}, {$gradStart}, {$gradEnd});";
                echo "--primary-dark: linear-gradient({$gradAngle}, " . adjustBrightness($gradStart, -20) . ", " . adjustBrightness($gradEnd, -20) . ");";
                echo "--primary-border-color: {$gradStart};";
            } else {
                $solidColor = htmlspecialchars($themeSettings['theme_color_solid']);
                echo "--primary-color: {$solidColor};";
                echo "--primary-dark: " . adjustBrightness($solidColor, -20) . ";";
                echo "--primary-border-color: {$solidColor};";
            }
            ?>
        }
    </style>
</head>
<body>
    <div class="admin-container">
        
        <?php require_once 'sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-topbar">
                <div class="topbar-left">
                    <button id="sidebartoggle" class="sidebar-Toggle"><i class="fas fa-bars"></i></button>
                    <div class="topbar-title"><h2><?= $pageTitle ?? 'Dashboard' ?></h2></div>
                </div>
                <div class="topbar-right">
                    <div class="topbar-actions">
                        <a href="<?= BASE_URL ?>/admin/notifications.php" class="topbar-btn" title="Notifications">
                            <i class="fas fa-bell"></i>
                            <?php if ($notificationCount > 0): ?><span class="notification-badge"><?= $notificationCount ?></span><?php endif; ?>
                        </a>
                        <a href="<?= BASE_URL ?>" target="_blank" class="topbar-btn" title="View Site"><i class="fas fa-external-link-alt"></i></a>
                    </div>
                    <?php if (isset($_SESSION['admin_id'])): ?>
                        <?php
                        $adminUsername = 'Admin';
                        $adminRole = 'Administrator';
                        try {
                            $stmt = $pdo->prepare("SELECT a.username, r.role_name FROM admin a LEFT JOIN admin_roles r ON a.role_id = r.id WHERE a.id = :id");
                            $stmt->bindParam(':id', $_SESSION['admin_id']);
                            $stmt->execute();
                            $admin = $stmt->fetch();
                            if ($admin) {
                                $adminUsername = $admin['username'];
                                $adminRole = $admin['role_name'] ?? 'Administrator';
                            }
                        } catch (PDOException $e) { /* Abaikan error */ }
                        ?>
                        <div class="admin-user">
                            <div class="admin-avatar-container">
                                <img src="<?= BASE_URL ?>/assets/img/default-avatar.png" alt="Admin" class="admin-avatar">
                                <div class="admin-dropdown">
                                    <a href="<?= BASE_URL ?>/admin/kelola_profil.php" class="dropdown-item"><i class="fas fa-user"></i> Profile</a>
                                    <a href="<?= BASE_URL ?>/admin/change_password.php" class="dropdown-item"><i class="fas fa-key"></i> Change Password</a>
                                    <a href="<?= BASE_URL ?>/admin/logout.php" class="dropdown-item text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                                </div>
                            </div>
                            <div class="admin-user-info">
                                <span><?= htmlspecialchars($adminUsername) ?></span>
                                <small><?= htmlspecialchars($adminRole) ?></small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php displayAlert(); ?>