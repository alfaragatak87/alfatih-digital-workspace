<?php

// +------------------------------------------------------------------------------+
// �  FILE: index.php                                                             �
// �                                                                              �
// �  DESKRIPSI:                                                                  �
// �  File utama (Front Controller) yang mengatur seluruh alur lalu lintas web.   �
// �  Berfungsi sebagai pintu gerbang (router) untuk menerima permintaan HTTP     �
// �  (GET/POST), memverifikasi sesi login, mengeksekusi operasi database, dan    �
// �  memanggil file tampilan yang sesuai.                                        �
// �                                                                              �
// �  KONEKSI & RELASI:                                                           �
// �  - Memanggil seluruh file di dalam 	ampilan/ untuk merender antarmuka.     �
// �  - Memanggil file di dalam ksi/ jika ada pemrosesan data spesifik.        �
// �                                                                              �
// �  BARIS KODE PENTING:                                                         �
// �  - Konfigurasi DB (Baris 10-14): Parameter koneksi ke MySQL.                 �
// �  -  Handlers (Baris 230+): Memproses aksi seperti buat folder/file.    �
// �  - renderPublicPage() (Baris 450+): Fungsi perender untuk halaman publik.    �
// +------------------------------------------------------------------------------+
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// ============================================================
// ALFATIH DIGITAL WORKSPACE — Editorial Edition
// Version 5.0 — Full Redesign: Editorial Minimalist + Talent Directory + PWA
// Engine: Native PHP 8.x | DB: MySQLi OOP | UI: B&W Editorial
// ============================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'mckmmukg_alfa');
define('DB_PASS', 'Alfaragatak87');
define('DB_NAME', 'mckmmukg_utama');
define('SITE_URL', 'https://gawe.my.id');
define('UPLOAD_DIR', 'uploads/files/');
define('PROFILE_IMG_DIR', 'uploads/');

mysqli_report(MYSQLI_REPORT_OFF);
session_start();

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) { die("DB Error: " . $mysqli->connect_error); }
$mysqli->set_charset('utf8mb4');

$migrations = [
    "ALTER TABLE `users` MODIFY `role` ENUM('superadmin','admin','user') NOT NULL DEFAULT 'user'",
    "ALTER TABLE `users` ADD COLUMN `email` VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE `users` ADD COLUMN `phone` VARCHAR(30) DEFAULT NULL",
    "ALTER TABLE `users` ADD COLUMN `profile_data` LONGTEXT DEFAULT NULL",
    "ALTER TABLE `users` ADD COLUMN `last_login` TIMESTAMP NULL DEFAULT NULL",
    "ALTER TABLE `users` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    "ALTER TABLE `admin_logs` ADD COLUMN `user_id` INT(11) DEFAULT NULL",
];
foreach ($migrations as $sql) { @$mysqli->query($sql); }
$mysqli->query("UPDATE `users` SET `role` = 'superadmin' WHERE `username` = 'alfa'");
$mysqli->query("UPDATE `users` SET `role` = 'admin' WHERE `username` != 'alfa' AND (`role` = 'bapak' OR `role` = 'ajay' OR `role` = 'user')");

function h(?string $str): string { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }
function formatBytes(int $bytes): string {
    if ($bytes <= 0) return '0 B';
    $k = 1024; $sizes = ['B','KB','MB','GB','TB'];
    $i = (int)floor(log(max($bytes,1)) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}
function generateCSRF(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function validateCSRF(): bool {
    return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}
function getFileIcon(string $filename): array {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $map = [
        'pdf'=>['fa-file-pdf','#dc2626'],'doc'=>['fa-file-word','#2563eb'],'docx'=>['fa-file-word','#2563eb'],
        'xls'=>['fa-file-excel','#16a34a'],'xlsx'=>['fa-file-excel','#16a34a'],'csv'=>['fa-file-excel','#16a34a'],
        'ppt'=>['fa-file-powerpoint','#ea580c'],'pptx'=>['fa-file-powerpoint','#ea580c'],
        'jpg'=>['fa-file-image','#0891b2'],'jpeg'=>['fa-file-image','#0891b2'],'png'=>['fa-file-image','#0891b2'],
        'gif'=>['fa-file-image','#0891b2'],'webp'=>['fa-file-image','#0891b2'],'svg'=>['fa-file-image','#0891b2'],
        'mp4'=>['fa-file-video','#7c3aed'],'avi'=>['fa-file-video','#7c3aed'],
        'mp3'=>['fa-file-audio','#db2777'],'wav'=>['fa-file-audio','#db2777'],
        'zip'=>['fa-file-zipper','#b45309'],'rar'=>['fa-file-zipper','#b45309'],
        'txt'=>['fa-file-lines','#475569'],'log'=>['fa-file-lines','#475569'],
        'html'=>['fa-file-code','#be185d'],'css'=>['fa-file-code','#be185d'],
        'js'=>['fa-file-code','#be185d'],'php'=>['fa-file-code','#be185d'],
    ];
    return $map[$ext] ?? ['fa-file','#94a3b8'];
}
function getPreviewType(string $filename): string {
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (in_array($ext,['jpg','jpeg','png','gif','webp','svg','bmp'])) return 'image';
    if ($ext === 'pdf') return 'pdf';
    if (in_array($ext,['mp4','webm','ogg','mov'])) return 'video';
    if (in_array($ext,['mp3','wav','ogg','flac','aac'])) return 'audio';
    if (in_array($ext,['txt','log','csv','json','xml','html','css','js','php','md'])) return 'text';
    return 'none';
}
function logActivity(mysqli $db, ?int $userId, string $action, string $details = ''): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? ''; $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $stmt = $db->prepare("INSERT INTO admin_logs (admin_id, user_id, action, details, ip_address, user_agent) VALUES (?,?,?,?,?,?)");
    if ($stmt) { $stmt->bind_param('iissss', $userId, $userId, $action, $details, $ip, $ua); $stmt->execute(); $stmt->close(); }
}
function isSuperAdmin(): bool { return ($_SESSION['role'] ?? '') === 'superadmin'; }
function isAdmin(): bool { return in_array($_SESSION['role'] ?? '', ['superadmin','admin']); }
function requireLogin(): void { if (empty($_SESSION['username'])) { header("Location: index.php?page=login"); exit; } }

$csrf_token = generateCSRF();

// ══════════════════════════════════════════════════════════════
// PENANGANAN AKSI BINER (UNDUH, CETAK, PORTFOLIO)
// ══════════════════════════════════════════════════════════════
if (isset($_GET['action']) && $_GET['action'] === 'track_document' && isset($_GET['q'])) {
    $stmt = $mysqli->prepare("SELECT id, nama_file, jenis, tanggal_upload, tags FROM files WHERE is_deleted=0 AND (tags LIKE ? OR nama_file LIKE ?) ORDER BY tanggal_upload DESC LIMIT 10");
    $like = '%' . $_GET['q'] . '%'; $stmt->bind_param('ss', $like, $like);
    $stmt->execute(); $res = $stmt->get_result(); $results = [];
    while ($r = $res->fetch_assoc()) $results[] = $r;
    $stmt->close(); header('Content-Type: application/json'); echo json_encode($results); exit;
}
if (isset($_GET['share'])) {
    $token = $_GET['share'];
    $stmt = $mysqli->prepare("SELECT * FROM files WHERE share_token=? AND jenis='file' AND is_deleted=0");
    $stmt->bind_param('s', $token); $stmt->execute(); $r = $stmt->get_result()->fetch_assoc(); $stmt->close();
    if ($r) { $fp = UPLOAD_DIR . $r['file_path']; if (file_exists($fp)) { $mime = mime_content_type($fp) ?: 'application/octet-stream'; header('Content-Type: ' . $mime); header('Content-Disposition: inline; filename="' . basename($r['nama_file']) . '"'); readfile($fp); exit; } }
    die("Link tidak valid atau file telah dihapus.");
}
if (isset($_GET['portfolio'])) {
    $uname = $_GET['portfolio'];
    $stmt = $mysqli->prepare("SELECT username, nama_lengkap, foto_profil, profile_data FROM users WHERE username=? AND role IN ('superadmin','admin','user')");
    $stmt->bind_param('s', $uname); $stmt->execute(); $puser = $stmt->get_result()->fetch_assoc(); $stmt->close();
    if (!$puser) { die("Portfolio tidak ditemukan."); }
    $pd = json_decode($puser['profile_data'] ?? '{}', true) ?? [];
    $pFoto = !empty($puser['foto_profil']) && $puser['foto_profil'] !== 'default.png' ? PROFILE_IMG_DIR . $puser['foto_profil'] : 'https://ui-avatars.com/api/?name=' . urlencode($puser['nama_lengkap'] ?? $uname) . '&background=1a1a1a&color=ffffff&bold=true&size=200';
    include "tampilan/halaman/halaman_portofolio.php"; exit;
}
if (isset($_GET['logout'])) { session_destroy(); header("Location: index.php"); exit; }
if (isset($_GET['action']) && in_array($_GET['action'],['download_file','view_file','print_file']) && isset($_GET['file_id']) && !empty($_SESSION['username'])) {
    $fid = (int)$_GET['file_id'];
    $stmt = $mysqli->prepare("SELECT * FROM files WHERE id=? AND jenis='file' AND is_deleted=0");
    $stmt->bind_param('i', $fid); $stmt->execute(); $fd = $stmt->get_result()->fetch_assoc(); $stmt->close();
    if ($fd && (isAdmin() || $fd['owner_username'] === $_SESSION['username'])) {
        $fp = UPLOAD_DIR . $fd['file_path'];
        if (file_exists($fp)) {
            if ($_GET['action'] === 'print_file') { echo "<!DOCTYPE html><html><head><title>Print</title></head><body style='margin:0;background:#525659;'><iframe src='?action=view_file&file_id=$fid' style='width:100%;height:100vh;border:none;' onload='this.contentWindow.print();'></iframe></body></html>"; exit; }
            $mime = mime_content_type($fp) ?: 'application/octet-stream'; header('Content-Type: ' . $mime);
            $disp = ($_GET['action'] === 'view_file') ? 'inline' : 'attachment';
            header('Content-Disposition: ' . $disp . '; filename="' . basename($fd['nama_file']) . '"');
            header('Content-Length: ' . filesize($fp)); readfile($fp); exit;
        }
    }
    exit;
}
if (isset($_GET['action']) && $_GET['action'] === 'download_zip' && isset($_GET['folder_id']) && !empty($_SESSION['username'])) {
    if (!class_exists('ZipArchive')) die("ZIP not supported.");
    $fid = (int)$_GET['folder_id'];
    $stmt = $mysqli->prepare("SELECT * FROM files WHERE folder_id=? AND is_deleted=0 AND jenis='file'");
    $stmt->bind_param('i', $fid); $stmt->execute(); $files = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();
    if (empty($files)) die("Folder kosong.");
    $zip = new ZipArchive(); $zname = "Folder_Export_" . time() . ".zip"; $zpath = "uploads/" . $zname;
    if ($zip->open($zpath, ZipArchive::CREATE) === true) {
        foreach ($files as $f) { $fp = UPLOAD_DIR . $f['file_path']; if (file_exists($fp)) $zip->addFile($fp, $f['nama_file']); }
        $zip->close(); header('Content-Type: application/zip'); header('Content-Disposition: attachment; filename=' . $zname);
        header('Content-Length: ' . filesize($zpath)); readfile($zpath); unlink($zpath); exit;
    }
    die("Gagal membuat ZIP.");
}
if (isset($_GET['action']) && $_GET['action'] === 'create_share' && isset($_GET['file_id']) && !empty($_SESSION['username'])) {
    $fid = (int)$_GET['file_id']; $token = bin2hex(random_bytes(16));
    $stmt = $mysqli->prepare("UPDATE files SET share_token=? WHERE id=?");
    $stmt->bind_param('si', $token, $fid); $stmt->execute(); $stmt->close();
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=workspace')); exit;
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='drag_move' && !empty($_SESSION['username'])) {
    if (!validateCSRF()) { header('Content-Type: application/json'); echo json_encode(['ok'=>false]); exit; }
    $iid = (int)$_POST['item_id']; $itype = $_POST['item_type']; $tid = (int)$_POST['target_folder'];
    if ($itype === 'folder') { $stmt = $mysqli->prepare("UPDATE folders SET parent_id=? WHERE id=? AND id!=?"); $stmt->bind_param('iii', $tid, $iid, $tid); }
    else { $stmt = $mysqli->prepare("UPDATE files SET folder_id=? WHERE id=?"); $stmt->bind_param('ii', $tid, $iid); }
    $stmt->execute(); $stmt->close(); header('Content-Type: application/json'); echo json_encode(['ok'=>true]); exit;
}
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='rename_item' && !empty($_SESSION['username'])) {
    if (!validateCSRF()) { echo json_encode(['ok'=>false]); exit; }
    $rid = (int)$_POST['item_id']; $rtype = $_POST['item_type']; $rname = trim($_POST['new_name']); $usr = $_SESSION['username'];
    if ($rtype === 'folder') {
        $stmt = isAdmin() ? $mysqli->prepare("UPDATE folders SET nama_folder=? WHERE id=?") : $mysqli->prepare("UPDATE folders SET nama_folder=? WHERE id=? AND owner_username='" . $mysqli->real_escape_string($usr) . "'");
        $stmt->bind_param('si', $rname, $rid);
    } else {
        $stmt = isAdmin() ? $mysqli->prepare("UPDATE files SET nama_file=? WHERE id=?") : $mysqli->prepare("UPDATE files SET nama_file=? WHERE id=? AND owner_username='" . $mysqli->real_escape_string($usr) . "'");
        $stmt->bind_param('si', $rname, $rid);
    }
    $stmt->execute(); $stmt->close(); header('Content-Type: application/json'); echo json_encode(['ok'=>true]); exit;
}

// =========================================`n// BLOK: PENANGANAN AUTENTIKASI (LOGIN)`n// Fungsi: Memverifikasi nama pengguna dan kata sandi untuk masuk ke dasbor`n// =========================================
$error_msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='login') {
    $uname = trim($_POST['username'] ?? ''); $upass = $_POST['password'] ?? '';
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username=? AND status!='inactive' LIMIT 1");
    if (!$stmt) $stmt = $mysqli->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param('s', $uname); $stmt->execute(); $row = $stmt->get_result()->fetch_assoc(); $stmt->close();
    if ($row && password_verify($upass, $row['password'])) {
        $_SESSION['username'] = $row['username']; $_SESSION['role'] = $row['role'];
        $_SESSION['uid'] = $row['id']; $_SESSION['nama'] = $row['nama_lengkap'] ?? $row['username'];
        $stmt2 = $mysqli->prepare("UPDATE users SET last_login=NOW() WHERE id=?");
        $stmt2->bind_param('i', $row['id']); $stmt2->execute(); $stmt2->close();
        logActivity($mysqli, $row['id'], 'LOGIN', 'User logged in from IP: '.($_SERVER['REMOTE_ADDR']??''));
        header("Location: index.php?page=beranda"); exit;
    } else { $error_msg = "Username atau password salah."; }
}

// =========================================`n// BLOK: PORTAL PUBLIK (BELUM LOGIN)`n// Fungsi: Menampilkan halaman utama bagi pengunjung yang belum masuk`n// =========================================
if (empty($_SESSION['username'])) {
    $pub_page = $_GET['page'] ?? 'hub';
    renderPublicPage($pub_page, $error_msg, $mysqli);
    exit;
}

// =========================================`n// BLOK: PENGATURAN PENGGUNA TERAUTENTIKASI`n// Fungsi: Memuat data profil, status folder, dan batas penyimpanan`n// =========================================
$username    = $_SESSION['username'];
$role        = $_SESSION['role'];
$uid         = (int)($_SESSION['uid'] ?? 0);
$alert_msg   = '';

$stmt = $mysqli->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
$stmt->bind_param('s', $username); $stmt->execute(); $data_user = $stmt->get_result()->fetch_assoc(); $stmt->close();
$nama_lengkap = $data_user['nama_lengkap'] ?? $username;
$foto_profil  = $data_user['foto_profil'] ?? '';
$path_foto    = ($foto_profil && $foto_profil !== 'default.png' && file_exists(PROFILE_IMG_DIR . $foto_profil))
    ? PROFILE_IMG_DIR . $foto_profil
    : 'https://ui-avatars.com/api/?name=' . urlencode($nama_lengkap) . '&background=1a1a1a&color=ffffff&bold=true';

$current_page = $_GET['page'] ?? 'beranda';
$csrf_token   = generateCSRF();

$stat_files = 0; $stat_links = 0; $stat_size = 0; $stat_folders = 0;
$stmt = $mysqli->prepare("SELECT jenis, file_path FROM files WHERE owner_username=? AND is_deleted=0");
$stmt->bind_param('s', $username); $stmt->execute(); $res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
    if ($r['jenis']==='file') { $stat_files++; $fp=UPLOAD_DIR.$r['file_path']; if(file_exists($fp)) $stat_size+=filesize($fp); }
    else $stat_links++;
}
$stmt->close();
$stmt = $mysqli->prepare("SELECT COUNT(*) FROM folders WHERE owner_username=? AND is_deleted=0");
$stmt->bind_param('s', $username); $stmt->execute(); $stat_folders = $stmt->get_result()->fetch_row()[0]; $stmt->close();
$size_used = formatBytes($stat_size); $storage_limit = 1073741824;
$storage_pct = min(100, round(($stat_size / $storage_limit) * 100, 1));

$all_users = [];
if (isAdmin()) {
    $res = $mysqli->query("SELECT id, username, nama_lengkap, role, foto_profil FROM users ORDER BY username ASC");
    while ($u = $res->fetch_assoc()) $all_users[] = $u;
}

// =========================================`n// BLOK: PENANGANAN FORM (POST)`n// Fungsi: Menyimpan data folder, file, pembaruan profil, dan aksi pengguna`n// =========================================
if ($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['action'])) {
    if ($_POST['action'] !== 'login' && !validateCSRF()) {
        $alert_msg = "Sesi keamanan tidak valid. Muat ulang halaman.";
    } else {
        $act = $_POST['action'];
        if ($act === 'update_settings') {
            $new_name = trim($_POST['nama_lengkap'] ?? ''); $new_pass = $_POST['new_password'] ?? ''; $foto_set = '';
            if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error']===0) {
                $ext = strtolower(pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                    $new_fn = $username . "_" . time() . "." . $ext;
                    if (!is_dir(PROFILE_IMG_DIR)) mkdir(PROFILE_IMG_DIR, 0777, true);
                    if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], PROFILE_IMG_DIR . $new_fn)) $foto_set = $new_fn;
                }
            }
            if (!empty($new_pass)) {
                $hash = password_hash($new_pass, PASSWORD_BCRYPT);
                if ($foto_set) { $stmt = $mysqli->prepare("UPDATE users SET nama_lengkap=?, password=?, foto_profil=? WHERE id=?"); $stmt->bind_param('sssi', $new_name, $hash, $foto_set, $uid); }
                else { $stmt = $mysqli->prepare("UPDATE users SET nama_lengkap=?, password=? WHERE id=?"); $stmt->bind_param('ssi', $new_name, $hash, $uid); }
            } else {
                if ($foto_set) { $stmt = $mysqli->prepare("UPDATE users SET nama_lengkap=?, foto_profil=? WHERE id=?"); $stmt->bind_param('ssi', $new_name, $foto_set, $uid); }
                else { $stmt = $mysqli->prepare("UPDATE users SET nama_lengkap=? WHERE id=?"); $stmt->bind_param('si', $new_name, $uid); }
            }
            $stmt->execute(); $stmt->close(); $_SESSION['nama'] = $new_name; $alert_msg = "Profil berhasil diperbarui!";
        }
        if ($act === 'add_folder') {
            $nf = trim($_POST['nama_folder'] ?? ''); $dk = trim($_POST['deskripsi'] ?? '');
            $ic = $_POST['icon'] ?? 'fa-folder'; $wr = $_POST['warna'] ?? '#000000';
            $owner = (isAdmin() && !empty($_POST['owner_username'])) ? $_POST['owner_username'] : $username;
            $parent = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            $stmt = $mysqli->prepare("INSERT INTO folders (parent_id, owner_username, nama_folder, icon, warna, deskripsi) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param('isssss', $parent, $owner, $nf, $ic, $wr, $dk); $stmt->execute(); $stmt->close();
            $alert_msg = "Folder berhasil dibuat!";
        }
        if ($act === 'edit_folder') {
            $id = (int)$_POST['folder_id']; $nf = trim($_POST['nama_folder'] ?? '');
            $dk = trim($_POST['deskripsi'] ?? ''); $ic = $_POST['icon'] ?? 'fa-folder'; $wr = $_POST['warna'] ?? '#000000';
            if (isAdmin()) { $stmt = $mysqli->prepare("UPDATE folders SET nama_folder=?, icon=?, warna=?, deskripsi=? WHERE id=?"); $stmt->bind_param('ssssi', $nf, $ic, $wr, $dk, $id); }
            else { $stmt = $mysqli->prepare("UPDATE folders SET nama_folder=?, icon=?, warna=?, deskripsi=? WHERE id=? AND owner_username=?"); $stmt->bind_param('ssssis', $nf, $ic, $wr, $dk, $id, $username); }
            $stmt->execute(); $stmt->close(); $alert_msg = "Folder diperbarui!";
        }
        if ($act === 'add_item') {
            $folder_id = (int)$_POST['folder_id']; $jenis = $_POST['jenis'] ?? 'file'; $tags = trim($_POST['tags'] ?? '');
            if ($jenis === 'link') {
                $nama_link = trim($_POST['nama_link'] ?? ''); $url_link = trim($_POST['link_url'] ?? '');
                if (!preg_match('~^(?:f|ht)tps?://~i', $url_link)) $url_link = 'https://' . $url_link;
                $stmt = $mysqli->prepare("INSERT INTO files (folder_id, owner_username, jenis, nama_file, link_url, tags) VALUES (?,?,'link',?,?,?)");
                $stmt->bind_param('issss', $folder_id, $username, $nama_link, $url_link, $tags); $stmt->execute(); $stmt->close();
                $alert_msg = "Tautan berhasil disimpan!";
            } elseif ($jenis === 'file') {
                if (isset($_FILES['file_upload']['name']) && is_array($_FILES['file_upload']['name'])) {
                    $ok = 0;
                    foreach ($_FILES['file_upload']['name'] as $i => $fname) {
                        if ($_FILES['file_upload']['error'][$i] !== 0) continue;
                        $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
                        $new_fn = $username . '_' . time() . '_' . rand(100,999) . '_' . $i . '.' . $ext;
                        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0777, true);
                        if (move_uploaded_file($_FILES['file_upload']['tmp_name'][$i], UPLOAD_DIR . $new_fn)) {
                            $stmt = $mysqli->prepare("INSERT INTO files (folder_id, owner_username, jenis, nama_file, file_path, tags) VALUES (?,?,'file',?,?,?)");
                            $stmt->bind_param('issss', $folder_id, $username, $fname, $new_fn, $tags); $stmt->execute(); $stmt->close(); $ok++;
                        }
                    }
                    if ($ok > 0) $alert_msg = "$ok file berhasil diunggah!";
                }
            }
        }
        if ($act === 'move_item') {
            $m_type = $_POST['move_type']; $m_id = (int)$_POST['move_id'];
            $target = ($_POST['target_folder'] === 'root') ? null : (int)$_POST['target_folder'];
            if ($m_type === 'folder') {
                if ($target === null) { $stmt = $mysqli->prepare("UPDATE folders SET parent_id=NULL WHERE id=?"); $stmt->bind_param('i', $m_id); }
                else { $stmt = $mysqli->prepare("UPDATE folders SET parent_id=? WHERE id=? AND id!=?"); $stmt->bind_param('iii', $target, $m_id, $target); }
            } else {
                if ($target === null) { $stmt = $mysqli->prepare("UPDATE files SET folder_id=NULL WHERE id=?"); $stmt->bind_param('i', $m_id); }
                else { $stmt = $mysqli->prepare("UPDATE files SET folder_id=? WHERE id=?"); $stmt->bind_param('ii', $target, $m_id); }
            }
            $stmt->execute(); $stmt->close(); $alert_msg = "Item berhasil dipindahkan!";
        }
        if ($act === 'bulk_delete') {
            $ids = json_decode($_POST['ids'] ?? '[]', true); $types = json_decode($_POST['types'] ?? '[]', true); $count = 0;
            for ($i = 0; $i < count($ids); $i++) {
                $bid = (int)$ids[$i]; $btype = $types[$i];
                if ($btype === 'folder') {
                    if (isAdmin()) { $stmt = $mysqli->prepare("UPDATE folders SET is_deleted=1 WHERE id=?"); $stmt->bind_param('i', $bid); }
                    else { $stmt = $mysqli->prepare("UPDATE folders SET is_deleted=1 WHERE id=? AND owner_username=?"); $stmt->bind_param('is', $bid, $username); }
                } else {
                    if (isAdmin()) { $stmt = $mysqli->prepare("UPDATE files SET is_deleted=1 WHERE id=?"); $stmt->bind_param('i', $bid); }
                    else { $stmt = $mysqli->prepare("UPDATE files SET is_deleted=1 WHERE id=? AND owner_username=?"); $stmt->bind_param('is', $bid, $username); }
                }
                $stmt->execute(); if ($stmt->affected_rows > 0) $count++; $stmt->close();
            }
            $alert_msg = "$count item dipindah ke Tong Sampah!";
        }
        if ($act === 'bulk_move') {
            $ids = json_decode($_POST['ids'] ?? '[]', true); $types = json_decode($_POST['types'] ?? '[]', true);
            $target = ($_POST['target_folder'] ?? 'root') === 'root' ? null : (int)$_POST['target_folder']; $count = 0;
            for ($i = 0; $i < count($ids); $i++) {
                $bid = (int)$ids[$i]; $btype = $types[$i];
                if ($btype === 'folder') {
                    if ($target === null) { $stmt = $mysqli->prepare("UPDATE folders SET parent_id=NULL WHERE id=?"); $stmt->bind_param('i', $bid); }
                    else { $stmt = $mysqli->prepare("UPDATE folders SET parent_id=? WHERE id=? AND id!=?"); $stmt->bind_param('iii', $target, $bid, $target); }
                } else {
                    if ($target === null) { $stmt = $mysqli->prepare("UPDATE files SET folder_id=NULL WHERE id=?"); $stmt->bind_param('i', $bid); }
                    else { $stmt = $mysqli->prepare("UPDATE files SET folder_id=? WHERE id=?"); $stmt->bind_param('ii', $target, $bid); }
                }
                $stmt->execute(); if ($stmt->affected_rows > 0) $count++; $stmt->close();
            }
            $alert_msg = "$count item berhasil dipindahkan!";
        }
        if ($act === 'add_user' && isSuperAdmin()) {
            $nu = trim($_POST['new_username'] ?? ''); $nn = trim($_POST['new_nama'] ?? '');
            $np = $_POST['new_password'] ?? ''; $nr = $_POST['new_role'] ?? 'admin';
            if ($nu && $np) {
                $hash = password_hash($np, PASSWORD_BCRYPT);
                $stmt = $mysqli->prepare("INSERT INTO users (username, nama_lengkap, password, role) VALUES (?,?,?,?)");
                $stmt->bind_param('ssss', $nu, $nn, $hash, $nr); $stmt->execute(); $stmt->close();
                $alert_msg = "User baru berhasil ditambahkan!";
            }
        }
        if ($act === 'edit_user' && isSuperAdmin()) {
            $eu_id = (int)$_POST['edit_uid']; $eu_name = trim($_POST['edit_nama'] ?? '');
            $eu_role = $_POST['edit_role'] ?? 'admin'; $eu_pass = $_POST['edit_password'] ?? '';
            if (!empty($eu_pass)) {
                $hash = password_hash($eu_pass, PASSWORD_BCRYPT);
                $stmt = $mysqli->prepare("UPDATE users SET nama_lengkap=?, role=?, password=? WHERE id=?"); $stmt->bind_param('sssi', $eu_name, $eu_role, $hash, $eu_id);
            } else { $stmt = $mysqli->prepare("UPDATE users SET nama_lengkap=?, role=? WHERE id=?"); $stmt->bind_param('ssi', $eu_name, $eu_role, $eu_id); }
            $stmt->execute(); $stmt->close(); $alert_msg = "User berhasil diperbarui!";
        }
        if ($act === 'delete_user' && isSuperAdmin()) {
            $du_id = (int)$_POST['del_uid'];
            if ($du_id !== $uid) { $stmt = $mysqli->prepare("DELETE FROM users WHERE id=?"); $stmt->bind_param('i', $du_id); $stmt->execute(); $stmt->close(); $alert_msg = "User berhasil dihapus."; }
        }
        if ($act === 'save_profile_data') {
            include "aksi/aksi_profil.php";
        }
    }
}

// =========================================`n// BLOK: PENANGANAN TONG SAMPAH (GET)`n// Fungsi: Mengembalikan atau menghapus secara permanen file/folder`n// =========================================
if (isset($_GET['action'])) {
    $gact = $_GET['action'];
    if ($gact === 'soft_delete_folder' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        if (isAdmin()) { $stmt = $mysqli->prepare("UPDATE folders SET is_deleted=1 WHERE id=?"); $stmt->bind_param('i', $id); }
        else { $stmt = $mysqli->prepare("UPDATE folders SET is_deleted=1 WHERE id=? AND owner_username=?"); $stmt->bind_param('is', $id, $username); }
        $stmt->execute(); $stmt->close(); header("Location: index.php?page=workspace"); exit;
    }
    if ($gact === 'soft_delete_item' && isset($_GET['item_id'])) {
        $id = (int)$_GET['item_id'];
        if (isAdmin()) { $stmt = $mysqli->prepare("UPDATE files SET is_deleted=1 WHERE id=?"); $stmt->bind_param('i', $id); }
        else { $stmt = $mysqli->prepare("UPDATE files SET is_deleted=1 WHERE id=? AND owner_username=?"); $stmt->bind_param('is', $id, $username); }
        $stmt->execute(); $stmt->close(); header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=workspace')); exit;
    }
    if ($gact === 'restore' && isset($_GET['id'], $_GET['type'])) {
        $id = (int)$_GET['id']; $type = $_GET['type']; $table = ($type === 'folder') ? 'folders' : 'files';
        if (isAdmin()) { $stmt = $mysqli->prepare("UPDATE $table SET is_deleted=0 WHERE id=?"); $stmt->bind_param('i', $id); }
        else { $stmt = $mysqli->prepare("UPDATE $table SET is_deleted=0 WHERE id=? AND owner_username=?"); $stmt->bind_param('is', $id, $username); }
        $stmt->execute(); $stmt->close(); header("Location: index.php?page=workspace&view=trash"); exit;
    }
    if ($gact === 'hard_delete' && isset($_GET['id'], $_GET['type'])) {
        $id = (int)$_GET['id']; $type = $_GET['type'];
        if ($type === 'file') {
            $stmt = $mysqli->prepare("SELECT * FROM files WHERE id=? AND is_deleted=1"); $stmt->bind_param('i', $id); $stmt->execute(); $fd = $stmt->get_result()->fetch_assoc(); $stmt->close();
            if ($fd && (isAdmin() || $fd['owner_username']===$username)) {
                if ($fd['jenis']==='file' && file_exists(UPLOAD_DIR . $fd['file_path'])) unlink(UPLOAD_DIR . $fd['file_path']);
                $stmt = $mysqli->prepare("DELETE FROM files WHERE id=?"); $stmt->bind_param('i', $id); $stmt->execute(); $stmt->close();
            }
        } else {
            if (isAdmin()) { $stmt = $mysqli->prepare("DELETE FROM folders WHERE id=? AND is_deleted=1"); $stmt->bind_param('i', $id); }
            else { $stmt = $mysqli->prepare("DELETE FROM folders WHERE id=? AND is_deleted=1 AND owner_username=?"); $stmt->bind_param('is', $id, $username); }
            $stmt->execute(); $stmt->close();
        }
        header("Location: index.php?page=workspace&view=trash"); exit;
    }
}

// =========================================`n// BLOK: PENGATURAN RUANG KERJA (WORKSPACE)`n// Fungsi: Menyiapkan variabel penyortiran, pencarian, dan rekam jejak aktivitas`n// =========================================
$search_query = trim($_GET['q'] ?? ''); $current_view = $_GET['view'] ?? 'home';
$active_folder = isset($_GET['folder_id']) ? (int)$_GET['folder_id'] : null;
$admin_filter = $_GET['filter'] ?? $username; $sort = $_GET['sort'] ?? 'nama_asc';
$order_f = 'nama_folder ASC'; $order_i = 'nama_file ASC';
if ($sort==='nama_desc') { $order_f='nama_folder DESC'; $order_i='nama_file DESC'; }
elseif ($sort==='date_asc') { $order_f='id ASC'; $order_i='tanggal_upload ASC'; }
elseif ($sort==='date_desc') { $order_f='id DESC'; $order_i='tanggal_upload DESC'; }

$breadcrumbs = [];
if ($active_folder) {
    $curr = $active_folder; $visited = [];
    while ($curr !== null) {
        if (in_array($curr, $visited)) break; $visited[] = $curr;
        $stmt = $mysqli->prepare("SELECT id, nama_folder, parent_id FROM folders WHERE id=?");
        $stmt->bind_param('i', $curr); $stmt->execute(); $rb = $stmt->get_result()->fetch_assoc(); $stmt->close();
        if (!$rb) break;
        array_unshift($breadcrumbs, $rb); $curr = $rb['parent_id'];
    }
}
$all_folders_list = [];
if (isAdmin()) {
    $res = $mysqli->query("SELECT id, nama_folder, owner_username FROM folders WHERE is_deleted=0 ORDER BY nama_folder ASC");
    while ($af = $res->fetch_assoc()) $all_folders_list[] = $af;
} else {
    $stmt = $mysqli->prepare("SELECT id, nama_folder, owner_username FROM folders WHERE is_deleted=0 AND owner_username=? ORDER BY nama_folder ASC");
    $stmt->bind_param('s', $username); $stmt->execute(); $res = $stmt->get_result();
    while ($af = $res->fetch_assoc()) $all_folders_list[] = $af; $stmt->close();
}
$recent_activity = [];
$stmt = $mysqli->prepare("SELECT action, details, created_at, ip_address FROM admin_logs WHERE user_id=? ORDER BY created_at DESC LIMIT 10");
$stmt->bind_param('i', $uid); $stmt->execute(); $res = $stmt->get_result();
while ($ra = $res->fetch_assoc()) $recent_activity[] = $ra; $stmt->close();

$profile_data  = json_decode($data_user['profile_data'] ?? '{}', true) ?? [];
$portfolio_url = SITE_URL . '/index.php?portfolio=' . urlencode($username);
$display_name  = !empty($profile_data['identitas']['nama_sebutan'])
    ? $profile_data['identitas']['nama_sebutan']
    : explode(' ', $nama_lengkap)[0];


// ══════════════════════════════════════════════════════════════
// FUNGSI PENAMPIL HALAMAN PUBLIK (LANDING PAGE & LOGIN)
// ══════════════════════════════════════════════════════════════
function renderPublicPage(string $pub_page, string $error_msg, mysqli $db): void {
    $talent_users = [];
    $res = $db->query("SELECT username, nama_lengkap, foto_profil, profile_data FROM users WHERE role IN ('superadmin','admin','user') ORDER BY nama_lengkap ASC");
    while ($tu = $res->fetch_assoc()) {
        $tpd = json_decode($tu['profile_data'] ?? '{}', true) ?? [];
        if (!empty($tpd['identitas']['tampil_publik'])) { $tu['_pd'] = $tpd; $talent_users[] = $tu; }
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pub_page==='login' ? 'Login — Alfatih Workspace' : 'Alfatih Digital Workspace' ?></title>
    <meta name="theme-color" content="#080b14">
    <meta name="description" content="Alfatih Digital Workspace — Premium CMS, File Manager & Portfolio Builder.">
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/svg+xml" href="aset/images/LOGO_GAWE.svg">
    <link rel="apple-touch-icon" href="aset/images/LOGO_GAWE.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* ═══════════════════════════════════════════════════════════
   GAWE.MY.ID — PUBLIC PORTAL v3  |  Dark SaaS Premium
   Palette: Midnight + Electric Indigo + Violet + Cyan
   ═══════════════════════════════════════════════════════════ */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
a{color:inherit;text-decoration:none;}
::selection{background:#6366f1;color:#fff;}
::-webkit-scrollbar{width:5px;}
::-webkit-scrollbar-track{background:#0d1117;}
::-webkit-scrollbar-thumb{background:#2d2f5c;border-radius:4px;}

:root{
  --bg:          #080b14;
  --bg-2:        #0d1117;
  --surface:     #111827;
  --surface-2:   #1a2235;
  --surface-3:   #1f2d44;
  --glass:       rgba(17,24,39,0.72);
  --border:      rgba(255,255,255,0.07);
  --border-md:   rgba(255,255,255,0.13);
  --text:        #f0f4ff;
  --text-2:      #a8b3cf;
  --muted:       #5a6888;
  --indigo:      #6366f1;
  --indigo-2:    #4f46e5;
  --violet:      #8b5cf6;
  --cyan:        #06b6d4;
  --emerald:     #10b981;
  --rose:        #f43f5e;
  --amber:       #f59e0b;
  --glow-indigo: 0 0 40px rgba(99,102,241,0.25);
  --glow-violet: 0 0 40px rgba(139,92,246,0.25);
  --f-display:   'Syne', system-ui, sans-serif;
  --f-body:      'Inter', system-ui, sans-serif;
  --nav-h:       64px;
  --ease-expo:   cubic-bezier(.16,1,.3,1);
  --ease-bounce: cubic-bezier(.34,1.56,.64,1);
}

body{
  font-family:var(--f-body);
  background:var(--bg);
  color:var(--text);
  overflow-x:hidden;
  -webkit-font-smoothing:antialiased;
}

/* ══ NOISE TEXTURE OVERLAY ══ */
body::before{
  content:'';
  position:fixed;inset:0;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
  pointer-events:none;z-index:0;
}

/* ══ ANIMATED GRADIENT ORBS ══ */
.orb-field{
  position:fixed;inset:0;overflow:hidden;
  pointer-events:none;z-index:0;
}
.orb{
  position:absolute;
  border-radius:50%;
  filter:blur(80px);
  opacity:.15;
  animation:orb-drift 18s ease-in-out infinite alternate;
}
.orb-1{width:600px;height:600px;background:var(--indigo);top:-200px;left:-200px;animation-delay:0s;}
.orb-2{width:500px;height:500px;background:var(--violet);top:40%;right:-200px;animation-delay:-6s;}
.orb-3{width:400px;height:400px;background:var(--cyan);bottom:-150px;left:30%;animation-delay:-12s;opacity:.1;}
@keyframes orb-drift{
  from{transform:translate(0,0) scale(1);}
  to{transform:translate(60px,40px) scale(1.15);}
}

/* ══ ENTRANCE GATE ANIMATION ══ */
.gate-overlay{
  position:fixed;inset:0;z-index:9999;
  display:flex;overflow:hidden;
  pointer-events:none;
}
.gate-panel{
  flex:1;height:100%;
  background:var(--bg);
  transform-origin:top center;
  animation:gate-open 1.2s var(--ease-expo) 0.2s both;
}
.gate-panel:nth-child(1){animation-delay:.1s;}
.gate-panel:nth-child(2){animation-delay:.15s;}
.gate-panel:nth-child(3){animation-delay:.2s;}
.gate-panel:nth-child(4){animation-delay:.25s;}
.gate-panel:nth-child(5){animation-delay:.3s;}
@keyframes gate-open{
  0%{transform:scaleY(1);}
  100%{transform:scaleY(0);}
}
.gate-logo{
  position:fixed;inset:0;z-index:10000;
  display:flex;align-items:center;justify-content:center;
  pointer-events:none;
  animation:gate-logo-out .6s var(--ease-expo) .7s both;
}
.gate-logo img{height:60px;filter:brightness(10);}
@keyframes gate-logo-out{
  to{opacity:0;transform:scale(.85);}
}

/* ══ NAVBAR ══ */
.pub-nav{
  position:fixed;top:0;left:0;right:0;
  height:var(--nav-h);
  display:flex;align-items:center;justify-content:space-between;
  padding:0 48px;
  z-index:100;
  background:rgba(8,11,20,0.7);
  border-bottom:1px solid var(--border);
  backdrop-filter:blur(24px);
  -webkit-backdrop-filter:blur(24px);
  animation:nav-in .8s var(--ease-expo) .9s both;
}
@keyframes nav-in{
  from{opacity:0;transform:translateY(-16px);}
  to{opacity:1;transform:none;}
}
.pub-nav-logo{display:flex;align-items:center;gap:10px;}
.pub-nav-logo img{height:26px;}
.pub-nav-logo span{
  font-family:var(--f-display);
  font-size:.85rem;font-weight:800;
  letter-spacing:1.5px;text-transform:uppercase;
  background:linear-gradient(90deg,var(--text),var(--text-2));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.pub-nav-links{display:flex;align-items:center;gap:2px;}
.pub-nav-links a{
  padding:7px 16px;
  font-size:.75rem;font-weight:600;
  letter-spacing:.3px;text-transform:uppercase;
  color:var(--text-2);
  border-radius:8px;
  transition:all .2s;
}
.pub-nav-links a:hover{color:var(--text);background:var(--surface);}
.pub-nav-links a.nav-cta{
  background:linear-gradient(135deg,var(--indigo),var(--violet));
  color:#fff;
  box-shadow:0 0 20px rgba(99,102,241,.3);
  padding:8px 20px;
}
.pub-nav-links a.nav-cta:hover{
  box-shadow:0 0 30px rgba(99,102,241,.5);
  transform:translateY(-1px);
}

/* ══ HERO ══ */
.pub-hero{
  min-height:100vh;
  display:flex;flex-direction:column;justify-content:center;
  padding:calc(var(--nav-h) + 60px) 80px 80px;
  position:relative;overflow:hidden;
}
.hero-badge{
  display:inline-flex;align-items:center;gap:8px;
  padding:6px 16px;
  background:rgba(99,102,241,.12);
  border:1px solid rgba(99,102,241,.3);
  border-radius:100px;
  font-size:.72rem;font-weight:700;
  letter-spacing:.5px;text-transform:uppercase;
  color:var(--indigo);
  margin-bottom:32px;
  animation:hero-up .8s var(--ease-expo) 1.2s both;
}
.hero-badge::before{
  content:'';
  width:6px;height:6px;
  background:var(--emerald);
  border-radius:50%;
  animation:pulse-green 2s ease infinite;
}
@keyframes pulse-green{
  0%,100%{box-shadow:0 0 0 0 rgba(16,185,129,.4);}
  50%{box-shadow:0 0 0 6px rgba(16,185,129,0);}
}
.hero-headline{
  font-family:var(--f-display);
  font-size:clamp(3rem,7vw,6.5rem);
  font-weight:800;
  line-height:.96;
  letter-spacing:-2px;
  margin-bottom:28px;
  animation:hero-up .9s var(--ease-expo) 1.35s both;
}
.hero-headline .grad{
  background:linear-gradient(135deg,var(--indigo),var(--violet),var(--cyan));
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
  background-clip:text;
}
@keyframes hero-up{
  from{opacity:0;transform:translateY(30px);}
  to{opacity:1;transform:none;}
}
.hero-sub{
  font-size:1.1rem;color:var(--text-2);
  max-width:520px;line-height:1.75;
  margin-bottom:48px;
  animation:hero-up .9s var(--ease-expo) 1.5s both;
}
.hero-ctas{
  display:flex;gap:14px;flex-wrap:wrap;
  animation:hero-up .9s var(--ease-expo) 1.65s both;
}
.btn-hero-primary{
  padding:16px 36px;
  background:linear-gradient(135deg,var(--indigo),var(--violet));
  color:#fff;
  font-size:.82rem;font-weight:700;
  letter-spacing:.5px;text-transform:uppercase;
  border:none;border-radius:12px;
  cursor:pointer;font-family:var(--f-body);
  box-shadow:0 0 30px rgba(99,102,241,.35);
  transition:all .25s var(--ease-expo);
  display:inline-flex;align-items:center;gap:9px;
  position:relative;overflow:hidden;
}
.btn-hero-primary::after{
  content:'';
  position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(255,255,255,.15),transparent);
  opacity:0;transition:opacity .2s;
}
.btn-hero-primary:hover{transform:translateY(-2px);box-shadow:0 0 40px rgba(99,102,241,.5);}
.btn-hero-primary:hover::after{opacity:1;}
.btn-hero-primary:active{transform:scale(.97);}
.btn-hero-ghost{
  padding:15px 36px;
  background:transparent;
  color:var(--text);
  font-size:.82rem;font-weight:700;
  letter-spacing:.5px;text-transform:uppercase;
  border:1px solid var(--border-md);
  border-radius:12px;
  cursor:pointer;font-family:var(--f-body);
  transition:all .25s;
  display:inline-flex;align-items:center;gap:9px;
}
.btn-hero-ghost:hover{border-color:rgba(99,102,241,.5);background:rgba(99,102,241,.08);transform:translateY(-2px);}

/* ══ HERO METRICS STRIP ══ */
.hero-metrics{
  display:flex;gap:0;margin-top:72px;
  border-top:1px solid var(--border);
  animation:hero-up .9s var(--ease-expo) 1.8s both;
}
.hero-metric{
  flex:1;padding:28px 0 0;
  border-right:1px solid var(--border);
  padding-right:40px;margin-right:40px;
}
.hero-metric:last-child{border-right:none;}
.hero-metric-num{
  font-family:var(--f-display);
  font-size:2.2rem;font-weight:800;
  background:linear-gradient(135deg,var(--text),var(--indigo));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  margin-bottom:4px;
}
.hero-metric-lbl{font-size:.72rem;color:var(--muted);font-weight:600;letter-spacing:.5px;text-transform:uppercase;}

/* ══ FEATURES BENTO ══ */
.feat-section{
  padding:100px 80px;
  position:relative;
  border-top:1px solid var(--border);
}
.feat-eyebrow{
  font-size:.72rem;font-weight:700;
  letter-spacing:2px;text-transform:uppercase;
  color:var(--indigo);margin-bottom:16px;
  display:flex;align-items:center;gap:10px;
}
.feat-eyebrow::before{content:'';width:20px;height:1px;background:var(--indigo);}
.feat-title{
  font-family:var(--f-display);
  font-size:clamp(2rem,4vw,3.5rem);
  font-weight:800;letter-spacing:-1.5px;
  margin-bottom:64px;
  background:linear-gradient(135deg,var(--text) 60%,var(--text-2));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.bento-feat-grid{
  display:grid;
  grid-template-columns:repeat(12,1fr);
  grid-template-rows:auto;
  gap:12px;
}
.bento-feat{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:20px;
  padding:32px;
  position:relative;overflow:hidden;
  transition:all .35s var(--ease-expo);
}
.bento-feat::before{
  content:'';
  position:absolute;inset:0;
  background:radial-gradient(circle at 80% 20%,rgba(99,102,241,.06),transparent 60%);
  opacity:0;transition:opacity .3s;
}
.bento-feat:hover{transform:translateY(-4px);border-color:rgba(99,102,241,.3);box-shadow:var(--glow-indigo);}
.bento-feat:hover::before{opacity:1;}
.bento-feat.col-8{grid-column:span 8;}
.bento-feat.col-4{grid-column:span 4;}
.bento-feat.col-6{grid-column:span 6;}
.bento-feat.col-3{grid-column:span 3;}
.bf-icon{
  width:44px;height:44px;
  background:linear-gradient(135deg,var(--indigo),var(--violet));
  border-radius:12px;
  display:flex;align-items:center;justify-content:center;
  font-size:1rem;color:#fff;
  margin-bottom:20px;
  box-shadow:0 4px 16px rgba(99,102,241,.3);
}
.bf-icon.cyan{background:linear-gradient(135deg,var(--cyan),var(--indigo));box-shadow:0 4px 16px rgba(6,182,212,.3);}
.bf-icon.violet{background:linear-gradient(135deg,var(--violet),#c026d3);box-shadow:0 4px 16px rgba(139,92,246,.3);}
.bf-icon.emerald{background:linear-gradient(135deg,var(--emerald),var(--cyan));box-shadow:0 4px 16px rgba(16,185,129,.3);}
.bf-title{
  font-family:var(--f-display);
  font-size:1.2rem;font-weight:700;
  margin-bottom:8px;color:var(--text);
}
.bf-desc{font-size:.85rem;color:var(--text-2);line-height:1.7;}
.bf-stat{
  font-family:var(--f-display);
  font-size:3rem;font-weight:800;
  background:linear-gradient(135deg,var(--indigo),var(--violet));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
  margin-top:20px;
}

/* ══ TALENT GRID ══ */
.talent-section{
  padding:100px 80px;
  border-top:1px solid var(--border);
}
.talent-grid-wrap{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
  gap:12px;
  margin-top:48px;
}
.talent-card{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:20px;
  padding:28px;
  position:relative;overflow:hidden;
  transition:all .35s var(--ease-expo);
  cursor:pointer;
}
.talent-card::after{
  content:'';
  position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(99,102,241,.05),rgba(139,92,246,.05));
  opacity:0;transition:opacity .3s;
  border-radius:20px;
}
.talent-card:hover{transform:translateY(-6px);border-color:rgba(99,102,241,.35);box-shadow:0 20px 40px rgba(0,0,0,.3),var(--glow-indigo);}
.talent-card:hover::after{opacity:1;}
.tc-header{display:flex;align-items:center;gap:14px;margin-bottom:18px;}
.tc-avatar{
  width:52px;height:52px;
  object-fit:cover;
  border-radius:14px;
  border:2px solid var(--border-md);
  transition:all .3s;
}
.talent-card:hover .tc-avatar{border-color:var(--indigo);box-shadow:0 0 16px rgba(99,102,241,.4);}
.tc-info-name{
  font-family:var(--f-display);
  font-size:1rem;font-weight:700;
  color:var(--text);
}
.tc-info-role{
  font-size:.72rem;font-weight:600;
  letter-spacing:.3px;text-transform:uppercase;
  color:var(--indigo);margin-top:2px;
}
.tc-summary{
  font-size:.84rem;color:var(--text-2);
  line-height:1.7;margin-bottom:20px;
  display:-webkit-box;-webkit-line-clamp:3;
  -webkit-box-orient:vertical;overflow:hidden;
}
.tc-skills{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:20px;}
.tc-skill{
  font-size:.65rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  padding:4px 10px;
  background:rgba(99,102,241,.1);
  border:1px solid rgba(99,102,241,.2);
  border-radius:100px;
  color:var(--indigo);
  transition:all .2s;
}
.talent-card:hover .tc-skill{background:rgba(99,102,241,.18);border-color:rgba(99,102,241,.4);}
.tc-link{
  font-size:.72rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  color:var(--text-2);
  display:inline-flex;align-items:center;gap:6px;
  transition:color .2s,gap .2s;
}
.tc-link:hover{color:var(--indigo);gap:10px;}
.talent-empty{
  grid-column:1/-1;
  text-align:center;padding:80px 20px;
  color:var(--muted);
}
.talent-empty i{font-size:3rem;margin-bottom:16px;display:block;color:var(--surface-3);}

/* ══ FOOTER ══ */
.pub-footer{
  padding:48px 80px;
  border-top:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;
  flex-wrap:wrap;gap:20px;
  background:var(--bg-2);
}
.pub-footer-brand{display:flex;align-items:center;gap:10px;}
.pub-footer-brand img{height:22px;opacity:.7;}
.pub-footer-brand span{font-size:.75rem;color:var(--muted);font-family:var(--f-display);font-weight:700;letter-spacing:1px;text-transform:uppercase;}
.pub-footer-links{display:flex;gap:24px;}
.pub-footer-links a{font-size:.75rem;color:var(--muted);transition:color .2s;}
.pub-footer-links a:hover{color:var(--text);}

/* ══ LOGIN PAGE ══ */
.login-page{
  min-height:100vh;
  display:grid;
  grid-template-columns:1fr 1fr;
  position:relative;z-index:1;
}
.login-left{
  position:relative;overflow:hidden;
  display:flex;flex-direction:column;justify-content:flex-end;
  padding:64px;
  background:linear-gradient(145deg,#0a0e1a 0%,#141b30 50%,#0f1628 100%);
  border-right:1px solid var(--border);
}
.login-left-grid{
  position:absolute;inset:0;
  background-image:linear-gradient(rgba(99,102,241,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(99,102,241,.06) 1px,transparent 1px);
  background-size:40px 40px;
  mask-image:radial-gradient(ellipse 80% 80% at 50% 50%,#000 40%,transparent 100%);
}
.login-left-orb{
  position:absolute;
  width:500px;height:500px;
  border-radius:50%;
  background:radial-gradient(circle,rgba(99,102,241,.2) 0%,transparent 70%);
  top:-100px;right:-150px;
  animation:orb-drift 12s ease infinite alternate;
}
.login-left-badge{
  display:inline-flex;align-items:center;gap:8px;
  padding:6px 16px;
  background:rgba(99,102,241,.12);
  border:1px solid rgba(99,102,241,.25);
  border-radius:100px;
  font-size:.7rem;font-weight:700;
  letter-spacing:.5px;text-transform:uppercase;
  color:rgba(99,102,241,.9);
  margin-bottom:24px;
  position:relative;z-index:1;
}
.login-left-title{
  font-family:var(--f-display);
  font-size:2.8rem;font-weight:800;
  letter-spacing:-1px;line-height:1.05;
  color:#fff;margin-bottom:16px;
  position:relative;z-index:1;
}
.login-left-title span{
  background:linear-gradient(135deg,var(--indigo),var(--violet));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.login-left-sub{
  font-size:.9rem;color:rgba(255,255,255,.45);
  line-height:1.75;max-width:380px;
  position:relative;z-index:1;
}
.login-left-features{margin-top:40px;display:flex;flex-direction:column;gap:12px;position:relative;z-index:1;}
.llf-item{
  display:flex;align-items:center;gap:12px;
  font-size:.82rem;color:rgba(255,255,255,.55);
}
.llf-icon{
  width:28px;height:28px;
  background:rgba(99,102,241,.15);
  border:1px solid rgba(99,102,241,.2);
  border-radius:8px;
  display:flex;align-items:center;justify-content:center;
  font-size:.7rem;color:var(--indigo);
  flex-shrink:0;
}

/* Login Form */
.login-right{
  display:flex;flex-direction:column;justify-content:center;
  padding:64px;background:var(--bg);position:relative;
}
.login-right-back{
  font-size:.72rem;font-weight:700;
  letter-spacing:.3px;text-transform:uppercase;
  color:var(--muted);
  display:inline-flex;align-items:center;gap:7px;
  margin-bottom:48px;
  transition:color .2s,gap .2s;
}
.login-right-back:hover{color:var(--text);gap:11px;}
.login-brand{margin-bottom:36px;}
.login-brand img{height:30px;}
.login-title{
  font-family:var(--f-display);
  font-size:2rem;font-weight:800;
  letter-spacing:-.8px;margin-bottom:6px;
  color:var(--text);
}
.login-sub{font-size:.88rem;color:var(--muted);margin-bottom:36px;}

/* Login error with shake */
.login-err{
  background:rgba(244,63,94,.08);
  border:1px solid rgba(244,63,94,.3);
  color:#fb7185;
  padding:13px 16px;
  font-size:.84rem;
  border-radius:10px;
  margin-bottom:22px;
  display:flex;align-items:center;gap:8px;
  animation:shake .5s var(--ease-expo);
}
@keyframes shake{
  0%,100%{transform:translateX(0);}
  15%{transform:translateX(-8px);}
  30%{transform:translateX(8px);}
  45%{transform:translateX(-6px);}
  60%{transform:translateX(6px);}
  75%{transform:translateX(-3px);}
  90%{transform:translateX(3px);}
}
.form-group{margin-bottom:18px;}
.form-label{
  display:block;
  font-size:.68rem;font-weight:700;
  letter-spacing:.8px;text-transform:uppercase;
  color:var(--text-2);margin-bottom:8px;
}
.form-input-wrap{position:relative;}
.form-input-wrap i{
  position:absolute;left:14px;top:50%;
  transform:translateY(-50%);
  color:var(--muted);font-size:.85rem;
  transition:color .2s;
}
.form-input{
  width:100%;
  padding:13px 14px 13px 40px;
  background:var(--surface);
  border:1.5px solid var(--border-md);
  border-radius:10px;
  color:var(--text);
  font-size:.92rem;font-family:var(--f-body);
  outline:none;
  transition:all .25s var(--ease-expo);
}
.form-input:focus{
  border-color:var(--indigo);
  background:var(--surface-2);
  box-shadow:0 0 0 3px rgba(99,102,241,.15);
}
.form-input:focus + i, .form-input-wrap:focus-within i{color:var(--indigo);}
.form-input::placeholder{color:var(--muted);}
.form-input.error{border-color:rgba(244,63,94,.5);animation:shake .4s var(--ease-expo);}

/* Submit button with ripple */
.btn-login{
  width:100%;padding:14px;
  background:linear-gradient(135deg,var(--indigo),var(--violet));
  color:#fff;
  font-size:.82rem;font-weight:700;
  letter-spacing:.5px;text-transform:uppercase;
  border:none;border-radius:10px;
  cursor:pointer;font-family:var(--f-body);
  margin-top:8px;
  box-shadow:0 4px 20px rgba(99,102,241,.35);
  transition:all .25s var(--ease-expo);
  position:relative;overflow:hidden;
  display:flex;align-items:center;justify-content:center;gap:9px;
}
.btn-login:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(99,102,241,.45);}
.btn-login:active{transform:scale(.97);}
.btn-login.loading{pointer-events:none;opacity:.8;}
.btn-login.loading .btn-text{display:none;}
.btn-login .btn-spinner{display:none;}
.btn-login.loading .btn-spinner{display:block;}
.btn-spinner{
  width:18px;height:18px;
  border:2.5px solid rgba(255,255,255,.3);
  border-top-color:#fff;
  border-radius:50%;
  animation:spin .7s linear infinite;
}
@keyframes spin{to{transform:rotate(360deg);}}
/* Ripple */
.ripple{
  position:absolute;
  border-radius:50%;
  background:rgba(255,255,255,.3);
  transform:scale(0);
  animation:ripple-anim .6s linear;
  pointer-events:none;
}
@keyframes ripple-anim{
  to{transform:scale(4);opacity:0;}
}

/* ══ RESPONSIVE ══ */
@media(max-width:1024px){
  .bento-feat.col-8{grid-column:span 12;}
  .bento-feat.col-4{grid-column:span 6;}
  .bento-feat.col-6{grid-column:span 12;}
  .bento-feat.col-3{grid-column:span 6;}
}
@media(max-width:768px){
  .pub-nav{padding:0 20px;}
  .pub-hero{padding:calc(var(--nav-h) + 40px) 24px 60px;}
  .feat-section,.talent-section{padding:60px 24px;}
  .pub-footer{padding:32px 24px;flex-direction:column;}
  .login-page{grid-template-columns:1fr;}
  .login-left{display:none;}
  .login-right{padding:40px 24px;}
  .hero-headline{font-size:clamp(2.2rem,10vw,4rem);}
  .hero-metrics{flex-wrap:wrap;}
  .hero-metric{border-right:none;padding-right:0;margin-right:0;padding-bottom:20px;flex-basis:50%;}
  .bento-feat.col-8,.bento-feat.col-4,.bento-feat.col-6,.bento-feat.col-3{grid-column:span 12;}
}
</style>
</head>
<body>

<!-- ENTRANCE GATE -->
<div class="gate-overlay">
  <div class="gate-panel"></div>
  <div class="gate-panel"></div>
  <div class="gate-panel"></div>
  <div class="gate-panel"></div>
  <div class="gate-panel"></div>
</div>
<div class="gate-logo">
  <img src="aset/images/LOGO_GAWE.svg" alt="Logo" onerror="this.src='LOGO_GAWE.svg'">
</div>

<!-- AMBIENT ORBS -->
<div class="orb-field">
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="orb orb-3"></div>
</div>

<!-- NAVBAR -->
<nav class="pub-nav">
  <div class="pub-nav-logo">
    <img src="aset/images/LOGO_GAWE.svg" alt="Logo" onerror="this.style.display='none'">
    <span>GAWE.MY.ID</span>
  </div>
  <div class="pub-nav-links">
    <?php if($pub_page !== 'login'){?>
    <a href="#talents">Talents</a>
    <a href="#features">Features</a>
    <?php }?>
    <a href="index.php?page=login" class="nav-cta"><i class="fa-solid fa-arrow-right-to-bracket" style="font-size:.75em;"></i> Login</a>
  </div>
</nav>

<?php if($pub_page === 'login'): ?>
<!-- ═══════════════ LOGIN PAGE ═══════════════ -->
<div class="login-page" style="padding-top:var(--nav-h);">
  <div class="login-left">
    <div class="login-left-grid"></div>
    <div class="login-left-orb"></div>
    <div class="login-left-badge"><i class="fa-solid fa-shield-halved"></i> Secure Workspace</div>
    <h2 class="login-left-title">Selamat Datang<br><span>Kembali.</span></h2>
    <p class="login-left-sub">Platform digital workspace premium Anda. Kelola file, bangun portfolio, dan tampilkan karya terbaik kepada dunia.</p>
    <div class="login-left-features">
      <div class="llf-item"><div class="llf-icon"><i class="fa-solid fa-folder"></i></div>Manajemen File & Folder Canggih</div>
      <div class="llf-item"><div class="llf-icon"><i class="fa-solid fa-id-card"></i></div>CV Builder & Portfolio Publik</div>
      <div class="llf-item"><div class="llf-icon"><i class="fa-solid fa-users-gear"></i></div>Manajemen Multi-User</div>
    </div>
  </div>
  <div class="login-right">
    <a href="index.php" class="login-right-back"><i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda</a>
    <div class="login-brand">
      <img src="aset/images/LOGO_GAWE.svg" alt="Logo" onerror="this.style.display='none'">
    </div>
    <h1 class="login-title">Masuk ke Workspace</h1>
    <p class="login-sub">Masukkan kredensial akun Anda untuk melanjutkan.</p>
    <?php if($error_msg):?>
    <div class="login-err" id="loginErr">
      <i class="fa-solid fa-circle-exclamation"></i>
      <?= htmlspecialchars($error_msg, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif;?>
    <form method="POST" action="index.php" id="loginForm" onsubmit="handleLogin(event)">
      <input type="hidden" name="action" value="login">
      <div class="form-group">
        <label class="form-label" for="login-user">Username</label>
        <div class="form-input-wrap">
          <input class="form-input<?= $error_msg?' error':'' ?>" type="text" id="login-user" name="username" placeholder="Masukkan username..." autocomplete="username" required>
          <i class="fa-solid fa-user"></i>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" for="login-pass">Password</label>
        <div class="form-input-wrap">
          <input class="form-input<?= $error_msg?' error':'' ?>" type="password" id="login-pass" name="password" placeholder="Masukkan password..." autocomplete="current-password" required>
          <i class="fa-solid fa-lock"></i>
        </div>
      </div>
      <button type="submit" class="btn-login" id="loginBtn">
        <span class="btn-text"><i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk Sekarang</span>
        <span class="btn-spinner"></span>
      </button>
    </form>
  </div>
</div>

<?php else: ?>
<!-- ═══════════════ LANDING PAGE ═══════════════ -->
<section class="pub-hero">
  <div class="hero-badge">Platform Digital Indonesia</div>
  <h1 class="hero-headline">Workspace<br>untuk Para <span class="grad">Kreator</span></h1>
  <p class="hero-sub">Platform manajemen file, CV builder, dan portfolio digital premium. Satu tempat untuk semua karya terbaik Anda.</p>
  <div class="hero-ctas">
    <a href="index.php?page=login" class="btn-hero-primary"><i class="fa-solid fa-rocket"></i> Mulai Sekarang</a>
    <a href="#talents" class="btn-hero-ghost"><i class="fa-solid fa-users"></i> Lihat Talent</a>
  </div>
  <div class="hero-metrics">
    <div class="hero-metric"><div class="hero-metric-num">100%</div><div class="hero-metric-lbl">Keamanan Data</div></div>
    <div class="hero-metric"><div class="hero-metric-num">∞</div><div class="hero-metric-lbl">Potensi Karya</div></div>
    <div class="hero-metric"><div class="hero-metric-num">24/7</div><div class="hero-metric-lbl">Akses Online</div></div>
  </div>
</section>

<section class="feat-section" id="features">
  <div class="feat-eyebrow">Fitur Unggulan</div>
  <h2 class="feat-title">Semua yang Anda<br>Butuhkan</h2>
  <div class="bento-feat-grid">
    <div class="bento-feat col-8">
      <div class="bf-icon"><i class="fa-solid fa-folder-tree"></i></div>
      <div class="bf-title">File Manager Canggih</div>
      <div class="bf-desc">Kelola file dan folder dengan drag-and-drop, bulk action, share link, preview real-time, dan sistem trash yang aman.</div>
      <div class="bf-stat">∞ GB</div>
    </div>
    <div class="bento-feat col-4">
      <div class="bf-icon cyan"><i class="fa-solid fa-id-card"></i></div>
      <div class="bf-title">CV Builder</div>
      <div class="bf-desc">Bangun profil profesional lengkap dengan identitas, riwayat pendidikan, pengalaman kerja, dan portofolio.</div>
    </div>
    <div class="bento-feat col-3">
      <div class="bf-icon violet"><i class="fa-solid fa-globe"></i></div>
      <div class="bf-title">Portfolio Publik</div>
      <div class="bf-desc">Halaman portfolio elegan yang bisa dibagikan ke dunia.</div>
    </div>
    <div class="bento-feat col-3">
      <div class="bf-icon emerald"><i class="fa-solid fa-users-gear"></i></div>
      <div class="bf-title">Multi-User</div>
      <div class="bf-desc">Kelola tim dengan sistem role superadmin, admin, dan user.</div>
    </div>
    <div class="bento-feat col-6">
      <div class="bf-icon"><i class="fa-solid fa-shield-halved"></i></div>
      <div class="bf-title">Keamanan Enterprise</div>
      <div class="bf-desc">CSRF protection, bcrypt password hashing, session management yang aman, dan activity logging lengkap untuk audit trail.</div>
    </div>
  </div>
</section>

<section class="talent-section" id="talents">
  <div class="feat-eyebrow">Direktori Talent</div>
  <h2 class="feat-title">Para Profesional<br>Terbaik</h2>
  <div class="talent-grid-wrap">
    <?php if(empty($talent_users)):?>
    <div class="talent-empty">
      <i class="fa-solid fa-users-slash"></i>
      <p>Belum ada talent yang menampilkan profil publik.</p>
    </div>
    <?php else: foreach($talent_users as $i=>$tu):
      $tpd=$tu['_pd'];
      $tident=$tpd['identitas']??[];
      $tFoto=!empty($tu['foto_profil'])&&$tu['foto_profil']!=='default.png'
        ?'uploads/'.$tu['foto_profil']
        :'https://ui-avatars.com/api/?name='.urlencode($tu['nama_lengkap']??$tu['username']).'&background=141b30&color=6366f1&bold=true&size=100';
      $tSkills=$tpd['keahlian']??[];
    ?>
    <a class="talent-card" href="index.php?portfolio=<?= urlencode($tu['username']) ?>">
      <div class="tc-header">
        <img src="<?= htmlspecialchars($tFoto,ENT_QUOTES,'UTF-8') ?>" alt="<?= htmlspecialchars($tu['nama_lengkap']??'',ENT_QUOTES,'UTF-8') ?>" class="tc-avatar" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($tu['nama_lengkap']??'U') ?>&background=141b30&color=6366f1&bold=true'">
        <div>
          <div class="tc-info-name"><?= htmlspecialchars($tident['nama_lengkap']??$tu['nama_lengkap']??'',ENT_QUOTES,'UTF-8') ?></div>
          <div class="tc-info-role"><?= htmlspecialchars($tident['profesi']??'Professional',ENT_QUOTES,'UTF-8') ?></div>
        </div>
      </div>
      <?php if(!empty($tident['summary'])):?>
      <p class="tc-summary"><?= htmlspecialchars($tident['summary'],ENT_QUOTES,'UTF-8') ?></p>
      <?php endif;?>
      <?php if(!empty($tSkills)):?>
      <div class="tc-skills">
        <?php foreach(array_slice($tSkills,0,4) as $sk):?>
        <span class="tc-skill"><?= htmlspecialchars($sk['nama']??'',ENT_QUOTES,'UTF-8') ?></span>
        <?php endforeach;?>
        <?php if(count($tSkills)>4):?><span class="tc-skill">+<?= count($tSkills)-4 ?></span><?php endif;?>
      </div>
      <?php endif;?>
      <span class="tc-link">Lihat Portfolio <i class="fa-solid fa-arrow-right" style="font-size:.75em;"></i></span>
    </a>
    <?php endforeach; endif; ?>
  </div>
</section>
<?php endif; ?>

<footer class="pub-footer">
  <div class="pub-footer-brand">
    <img src="aset/images/LOGO_GAWE.svg" alt="Logo" onerror="this.style.display='none'">
    <span>GAWE.MY.ID &mdash; Alfatih Digital Workspace</span>
  </div>
  <div class="pub-footer-links">
    <a href="index.php">Beranda</a>
    <a href="index.php?page=login">Login</a>
  </div>
</footer>

<script>
// =========================================`n// BLOK: PENANGANAN AUTENTIKASI (LOGIN)`n// Fungsi: Memverifikasi nama pengguna dan kata sandi untuk masuk ke dasbor`n// ========================================= form handler with loading + ripple
function handleLogin(e) {
  const btn = document.getElementById('loginBtn');
  if (btn) {
    btn.classList.add('loading');
    // Ripple effect
    const ripple = document.createElement('span');
    ripple.className = 'ripple';
    ripple.style.cssText = 'width:200px;height:200px;left:50%;top:50%;margin:-100px 0 0 -100px;';
    btn.appendChild(ripple);
    setTimeout(() => ripple.remove(), 700);
  }
}

// Input focus glow
document.querySelectorAll('.form-input').forEach(inp => {
  inp.addEventListener('focus', () => {
    const wrap = inp.closest('.form-input-wrap');
    if(wrap) wrap.querySelector('i').style.color = 'var(--indigo)';
  });
  inp.addEventListener('blur', () => {
    const wrap = inp.closest('.form-input-wrap');
    if(wrap) wrap.querySelector('i').style.color = '';
  });
});

// Scroll reveal
const revealObs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.style.opacity = '1';
      e.target.style.transform = 'none';
      revealObs.unobserve(e.target);
    }
  });
}, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });

document.querySelectorAll('.bento-feat, .talent-card').forEach((el, i) => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(20px)';
  el.style.transition = `opacity .6s cubic-bezier(.16,1,.3,1) ${i * 0.05}s, transform .6s cubic-bezier(.16,1,.3,1) ${i * 0.05}s`;
  revealObs.observe(el);
});
</script>
</body></html>
<?php
}


// ══════════════════════════════════════════════════════════════
// MAIN AUTHENTICATED DASHBOARD — HTML OUTPUT
// ══════════════════════════════════════════════════════════════
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
    <link rel="apple-touch-icon" href="aset/images/LOGO_GAWE.svg">
    <link rel="icon" type="image/svg+xml" href="aset/images/LOGO_GAWE.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <style>
/* ═══════════════════════════════════════════════════════════════
   WORKSPACE DASHBOARD — Dark SaaS Premium Edition v3
   Deep Indigo + Violet + Cyan accents, Bento Grid, Glass panels
   ═══════════════════════════════════════════════════════════════ */

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

/* ══ BASE ══ */
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

/* ══ KEYFRAMES ══ */
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

/* ══ STAGGER ANIMATION ══ */
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

/* ══ NAVBAR ══ */
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

/* ══ SIDEBAR ══ */
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

/* ══ MAIN LAYOUT ══ */
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

/* ══ ALERT BAR ══ */
.alert-bar{
  padding:12px 28px;
  display:flex;align-items:center;gap:10px;
  font-size:.84rem;font-weight:600;
  border-bottom:1px solid transparent;
  animation:slideDown .3s var(--ease-out-expo);
}
.alert-bar.success{background:var(--success-bg);color:var(--success);border-color:#bbf7d0;}
.alert-bar.error{background:var(--danger-bg);color:var(--danger);border-color:#fecaca;}

/* ══ PAGE HEADER ══ */
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

/* ══ BENTO DASHBOARD ══ */
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

/* ══ EDITORIAL CARDS ══ */
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

/* ══ WORKSPACE TOOLBAR ══ */
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

/* ══ FILE LISTING ══ */
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

/* ══ RIGHT SIDEBAR ══ */
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

/* ══ CV BUILDER ══ */
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

/* ══ ACCORDION (CV sections) ══ */
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

/* ══ USER MANAGEMENT TABLE ══ */
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

/* ══ MODALS ══ */
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

/* ══ PREVIEW OVERLAY ══ */
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

/* ══ CONFIRM DIALOG ══ */
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

/* ══ TOAST ══ */
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

/* ══ GLOBAL DROP OVERLAY ══ */
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

/* ══ SweetAlert2 OVERRIDES ══ */
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

/* ══ MOBILE ══ */
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
/* ══ OVERRIDE ROOT VARIABLES FOR LIGHT THEME ══ */
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
/* ══ GOOGLE DRIVE LAYOUT (MATERIAL 3 LIGHT) ══ */
.drive-layout { padding: 16px; background: #ffffff; border-radius: 16px; min-height: calc(100vh - 80px); display: block !important; margin: 0 16px 16px 0; }
body { background: #f8fafd; }
.top-navbar { background: #f8fafd; border-bottom: none; box-shadow: none; height: 64px; padding: 8px 16px; }
.sidebar { background: #f8fafd; border-right: none; width: 256px; padding-top: 12px; }
.main-wrapper { margin-left: 256px; padding-top: 64px; }
.nav-item { margin: 2px 12px; border-radius: 20px; padding: 8px 16px; color: #444746; font-size: 0.875rem; font-weight: 500; font-family: "Google Sans", "Inter", sans-serif; transition: background 0.15s; }
.nav-item:hover { background: #e8eaed; }
.nav-item.active { background: #c2e7ff; color: #001d35; font-weight: 600; }
.nav-item i { width: 24px; font-size: 1.1rem; color: #444746; }
.nav-item.active i { color: #001d35; }
.btn-drive-new { display: flex; align-items: center; gap: 12px; background: #ffffff; border: none; border-radius: 16px; padding: 16px 20px; font-size: 0.875rem; font-weight: 500; font-family: "Google Sans", sans-serif; color: #444746; cursor: pointer; box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3), 0 1px 3px 1px rgba(60,64,67,0.15); transition: box-shadow 0.15s, background 0.15s; }
.btn-drive-new:hover { background: #f4f7fc; box-shadow: 0 1px 3px 0 rgba(60,64,67,0.3), 0 4px 8px 3px rgba(60,64,67,0.15); }

.drive-section-title { font-size: 0.875rem; font-weight: 500; font-family: "Google Sans", "Inter", sans-serif; color: #444746; margin-bottom: 16px; margin-top: 24px; }
.drive-grid-folders { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 12px; margin-bottom: 24px; }
.drive-folder-card { display: flex; align-items: center; background: #f0f4f9; border: none; border-radius: 12px; padding: 12px 16px; cursor: pointer; transition: background 0.15s; position: relative; gap: 12px; height: 48px; }
.drive-folder-card:hover { background: #e8eaed; }
.drive-folder-card.selected { background: #c2e7ff; }
.drive-folder-card .item-info-wrap { flex: 1; display: flex; align-items: center; gap: 12px; overflow: hidden; }
.drive-folder-card .item-icon-lg { font-size: 1.2rem; color: #444746; }
.drive-folder-card .item-name { font-size: 0.875rem; font-weight: 500; font-family: "Google Sans", "Inter", sans-serif; color: #1f1f1f; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.drive-folder-card .action-wrapper, .drive-folder-card .item-checkbox, .drive-folder-card .col-owner, .drive-folder-card .col-date, .drive-folder-card .col-size { display: none; }
.drive-folder-card:hover .action-wrapper { display: block; position: absolute; right: 8px; top: 8px; }

.drive-grid-files { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px; }
.drive-file-card { display: flex; flex-direction: column; background: #f0f4f9; border: none; border-radius: 12px; overflow: hidden; cursor: pointer; transition: background 0.15s; position: relative; height: 180px; }
.drive-file-card:hover { background: #e8eaed; }
.drive-file-card.selected { background: #c2e7ff; }
.drive-file-preview { height: 130px; background: #ffffff; display: flex; align-items: center; justify-content: center; border-bottom: 1px solid #e0e0e0; overflow: hidden; margin: 1px 1px 0 1px; border-radius: 11px 11px 0 0; }
.drive-file-preview-img { width: 100%; height: 100%; object-fit: cover; }
.drive-file-icon-placeholder i { font-size: 3.5rem; opacity: 0.8; }
.drive-file-card .item-info-wrap { padding: 8px 12px; display: flex; align-items: center; gap: 12px; height: 50px; }
.drive-file-card .item-icon-sm { font-size: 1.1rem; }
.drive-file-card .item-name { font-size: 0.875rem; font-weight: 500; font-family: "Google Sans", "Inter", sans-serif; color: #1f1f1f; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; }
.drive-file-card .action-wrapper, .drive-file-card .item-checkbox, .drive-file-card .col-owner, .drive-file-card .col-date, .drive-file-card .col-size { display: none; }
.drive-file-card:hover .action-wrapper { display: block; position: absolute; right: 8px; bottom: 8px; }

/* Context Menu */
.drive-context-menu { position: fixed; background: #ffffff; border: none; border-radius: 8px; padding: 8px 0; min-width: 240px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 9999; font-family: "Google Sans", "Inter", sans-serif; display: none; }
.drive-context-menu.show { display: block; animation: fadeIn 0.1s ease-out; }
.drive-context-item { padding: 6px 16px; font-size: 0.875rem; color: #1f1f1f; display: flex; align-items: center; gap: 12px; cursor: pointer; transition: background 0.1s; }
.drive-context-item:hover { background: #f0f4f9; }
.drive-context-item i { font-size: 1.1rem; color: #444746; width: 20px; text-align: center; }
.drive-context-divider { height: 1px; background: #e0e0e0; margin: 8px 0; }

/* List View Overrides */
.view-list .drive-grid-folders, .view-list .drive-grid-files { display: flex; flex-direction: column; gap: 0; }
.view-list .drive-folder-card, .view-list .drive-file-card { display: grid; grid-template-columns: 36px 1fr 160px 140px 90px 40px; align-items: center; height: 48px; border-radius: 0; border: none; border-bottom: 1px solid #e0e0e0; padding: 0 16px; background: transparent; }
.view-list .drive-folder-card:hover, .view-list .drive-file-card:hover { background: #f0f4f9; }
.view-list .drive-folder-card.selected, .view-list .drive-file-card.selected { background: #c2e7ff; }
.view-list .drive-file-preview { display: none; }
.view-list .drive-file-card .item-info-wrap, .view-list .drive-folder-card .item-info-wrap { height: auto; padding: 0; }
.view-list .col-owner, .view-list .col-date, .view-list .col-size { display: flex; align-items: center; font-size: 0.875rem; color: #5f6368; }
.view-list .col-owner img { width: 24px; height: 24px; border-radius: 50%; margin-right: 8px; }
.view-list .item-checkbox { display: block; position: relative; top: 0; left: 0; opacity: 0.3; }
.view-list .drive-folder-card:hover .item-checkbox, .view-list .drive-file-card:hover .item-checkbox { opacity: 1; }
.view-list .action-wrapper { display: block !important; position: static !important; }
.view-list #driveListHeader { display: grid !important; }
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
          <img src="aset/images/LOGO_GAWE.svg" alt="Logo" onerror="this.style.display='none'">
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
// ══════════════ PAGE: BERANDA ══════════════
if($current_page==='beranda'){
    $hour=(int)date('G');
    $greeting=$hour<12?'Selamat Pagi':($hour<17?'Selamat Siang':($hour<20?'Selamat Sore':'Selamat Malam'));
?>
<div class="greeting-strip">
    <div class="greeting-content">
        <div class="greeting-label"><span class="pulse-dot"></span> Semua Sistem Normal</div>
        <h1 class="greeting-name"><?= $greeting ?>, <br><?= h($display_name) ?>.</h1>
        <p class="greeting-sub"><?= date('l, d F Y') ?> &mdash; Selamat datang kembali di Alfatih Workspace.</p>
        <div class="greeting-actions">
            <a href="index.php?page=workspace" class="gqa-btn dark-inv"><i class="fa-solid fa-folder-open"></i> Buka Workspace</a>
            <a href="index.php?page=profile" class="gqa-btn"><i class="fa-solid fa-pen-nib"></i> Edit CV</a>
            <a href="<?= h($portfolio_url) ?>" target="_blank" class="gqa-btn"><i class="fa-solid fa-globe"></i> Lihat Web</a>
            <?php if(isSuperAdmin()){?><a href="index.php?page=manajemen-pengguna" class="gqa-btn" style="border-color:rgba(245,158,11,0.3);color:var(--superadmin);"><i class="fa-solid fa-users-gear"></i> Manage</a><?php }?>
        </div>
    </div>
</div>
<div class="dash-inner" style="padding:0;">
<div class="bento-grid" style="padding:32px 32px 0;">
    <div class="bento-card stat-block">
        <div class="bento-card-icon dark"><i class="fa-solid fa-file-lines"></i></div>
        <div class="stat-info">
            <div class="stat-label">Total File</div>
            <div class="stat-value"><?= $stat_files ?></div>
            <div class="stat-sub">File Tersimpan</div>
        </div>
    </div>
    <div class="bento-card stat-block">
        <div class="bento-card-icon light"><i class="fa-solid fa-folder-tree"></i></div>
        <div class="stat-info">
            <div class="stat-label">Direktori</div>
            <div class="stat-value"><?= $stat_folders ?></div>
            <div class="stat-sub">Folder Aktif</div>
        </div>
    </div>
    <div class="bento-card stat-block storage-bento">
        <div class="bento-card-icon dark"><i class="fa-solid fa-hard-drive"></i></div>
        <div class="stat-info">
            <div class="stat-label">Penyimpanan</div>
            <div class="stat-value" style="font-size:1.6rem;"><?= $size_used ?></div>
            <div class="stat-sub">Kapasitas <?= $storage_pct ?>%</div>
        </div>
    </div>
    <div class="bento-card stat-block">
        <div class="bento-card-icon light"><i class="fa-solid fa-link"></i></div>
        <div class="stat-info">
            <div class="stat-label">Tautan</div>
            <div class="stat-value"><?= $stat_links ?></div>
            <div class="stat-sub">URL Disimpan</div>
        </div>
    </div>
</div>
</div>
<div class="dash-inner grid-1" style="display:grid;grid-template-columns:1fr;gap:24px;padding-top:24px;">
<?php
$ident_filled=!empty($profile_data['identitas']['nama_lengkap']);
$edu_filled=!empty($profile_data['pendidikan']);
$exp_filled=!empty($profile_data['pengalaman']);
$skill_filled=!empty($profile_data['keahlian']);
$pct_cv=(($ident_filled?25:0)+($edu_filled?25:0)+($exp_filled?25:0)+($skill_filled?25:0));
?>
<div class="ed-card">
    <div class="ed-card-head"><h3><i class="fa-solid fa-id-badge"></i> Kelengkapan Profile</h3><span class="pct-badge"><?= $pct_cv ?>%</span></div>
    <div class="ed-card-body">
        <div class="progress-wrap"><div class="progress-bar" style="width:<?= $pct_cv ?>%;"></div></div>
        <?php if($pct_cv == 100){ ?>
        <div style="text-align:center;padding:24px 0;">
            <i class="fa-solid fa-circle-check" style="font-size:3rem;color:#10b981;margin-bottom:12px;"></i>
            <div style="font-size:1.1rem;font-weight:600;color:#fff;">Profil Anda Sudah Lengkap!</div>
            <div style="font-size:0.9rem;color:#94a3b8;margin-top:4px;">Terima kasih telah melengkapi data profil Anda.</div>
        </div>
        <?php } else { ?>
        <div class="profile-check-list">
            <?php foreach([['Identitas Dasar',$ident_filled,'fa-user'],['Riwayat Pendidikan',$edu_filled,'fa-graduation-cap'],['Pengalaman Kerja',$exp_filled,'fa-briefcase'],['Keahlian / Skill',$skill_filled,'fa-code-branch']] as [$lbl,$done,$ico]){?>
            <div class="profile-check-row <?= $done?'is-done':'' ?>">
                <div class="pcr-icon"><i class="fa-solid <?= $ico ?>"></i></div>
                <div class="pcr-info">
                    <div class="pcr-title"><?= $lbl ?></div>
                    <div class="pcr-desc"><?= $done?'Sudah diisi dengan baik':'Belum dilengkapi' ?></div>
                </div>
                <div class="pcr-action">
                    <?php if(!$done){?><a href="index.php?page=profile" class="btn-fill">Isi Sekarang</a><?php }else{?><i class="fa-solid fa-check-circle text-success"></i><?php }?>
                </div>
            </div>
            <?php }?>
        </div>
        <?php } ?>
    </div>
</div>
</div>

<?php
// ══════════════ PAGE: WORKSPACE ══════════════
}elseif($current_page==='workspace'){
    $ws_view=$_GET['view']??'home';
    $base_url="?page=workspace&";
    if($active_folder) $base_url.="folder_id={$active_folder}&";
    if(isset($_GET['filter'])) $base_url.="filter=".h($_GET['filter'])."&";
    if($ws_view==='trash') $base_url.="view=trash&";
?>
<div class="toolbar-main">
    <div class="toolbar-left">
        <div class="dropdown">
            <button class="btn-drive-new"><i class="fa-solid fa-plus"></i> Buat Baru</button>
            <div class="dropdown-content">
                <button onclick="openModal('addFolderModal')"><i class="fa-solid fa-folder-plus"></i><div><strong>Folder Baru</strong><div class="dd-desc">Buat ruang penyimpanan baru</div></div></button>
                <hr class="menu-divider">
                <?php if($active_folder){?>
                <button onclick="openModal('addItemModal');switchType('file');"><i class="fa-solid fa-file-arrow-up"></i><div><strong>Upload File</strong><div class="dd-desc">Pilih dari komputer Anda</div></div></button>
                <button onclick="openModal('addItemModal');switchType('link');"><i class="fa-solid fa-link"></i><div><strong>Simpan Tautan</strong><div class="dd-desc">Simpan URL website</div></div></button>
                <?php }else{?><button disabled style="opacity:.4;cursor:not-allowed;"><i class="fa-solid fa-file-arrow-up"></i><div>Upload File<div class="dd-desc">Masuk folder dulu</div></div></button><?php }?>
            </div>
        </div>
    </div>
    <div class="toolbar-right">
        <button class="btn-icon" onclick="toggleRightSidebar()" data-tooltip="Detail"><i class="fa-solid fa-circle-info"></i></button>
        <div class="view-toggle">
            <button id="btnList" onclick="setViewMode('list')" data-tooltip="List"><i class="fa-solid fa-list-ul"></i></button>
            <button id="btnGrid" onclick="setViewMode('grid')" class="active" data-tooltip="Grid"><i class="fa-solid fa-border-all"></i></button>
        </div>
    </div>
</div>
<div class="bulk-toolbar" id="bulkToolbar">
    <input type="checkbox" class="item-checkbox" id="selectAllMain" onclick="toggleSelectAll(this)" style="opacity:1;">
    <span class="bulk-count" id="bulkCount">0 dipilih</span>
    <div class="bulk-actions">
        <button class="bulk-btn" onclick="bulkMove()"><i class="fa-solid fa-folder-tree"></i> Pindah</button>
        <button class="bulk-btn danger" onclick="bulkDelete()"><i class="fa-solid fa-trash"></i> Hapus</button>
        <button class="bulk-btn" onclick="deselectAll()"><i class="fa-solid fa-xmark"></i> Batal</button>
    </div>
</div>
<div class="breadcrumbs">
    <?php
    if($ws_view==='trash'){echo "<i class='fa-solid fa-trash-can' style='color:var(--danger);margin-right:4px;'></i> Tong Sampah";}
    elseif($ws_view==='recent'){echo "<a href='index.php?page=workspace'>Beranda</a> &rsaquo; Akses Terbaru";}
    elseif($ws_view==='assets'){echo "<a href='index.php?page=workspace'>Beranda</a> &rsaquo; Aset Visual";}
    elseif($ws_view==='stats'){echo "<a href='index.php?page=workspace'>Beranda</a> &rsaquo; Statistik";}
    else{
        echo "<a href='index.php?page=workspace'>Beranda</a>";
        foreach($breadcrumbs as $bc){echo " &rsaquo; <a href='?page=workspace&folder_id={$bc['id']}'>".h($bc['nama_folder'])."</a>";}
    }
    ?>
</div>
<?php if($ws_view==='home'&&isAdmin()&&!$active_folder){
    echo "<div class='filter-chips'><a href='?page=workspace&filter={$username}' class='chip ".(($admin_filter===$username)?'active':'')."'><i class='fa-regular fa-user'></i> Milikku</a>";
    foreach($all_users as $u){if($u['username']!==$username){$lbl=!empty($u['nama_lengkap'])?$u['nama_lengkap']:$u['username'];echo "<a href='?page=workspace&filter=".h($u['username'])."' class='chip ".(($admin_filter===$u['username'])?'active':'')."'><i class='fa-solid fa-user'></i> ".h($lbl)."</a>";}}
    echo "<a href='?page=workspace&filter=semua' class='chip ".(($admin_filter==='semua')?'active':'')."'><i class='fa-solid fa-users'></i> Semua</a></div>";
}?>

<div id="workspaceContainer" class="view-list drive-layout">
<div class="list-header">
    <div class="select-all-wrap"><input type="checkbox" class="item-checkbox" id="selectAllHeader" onclick="toggleSelectAll(this)" style="opacity:1;"></div>
    <div><a href="<?= $base_url ?>sort=<?= ($sort==='nama_asc')?'nama_desc':'nama_asc' ?>">Nama <?php if($sort==='nama_asc')echo '&darr;';elseif($sort==='nama_desc')echo '&uarr;';?></a></div>
    <div class="col-owner">Pemilik</div>
    <div class="col-date"><a href="<?= $base_url ?>sort=<?= ($sort==='date_desc')?'date_asc':'date_desc' ?>">Tanggal</a></div>
    <div class="col-size">Ukuran</div>
    <div></div>
</div>
<?php
if($ws_view==='stats'){
    $tc=[];
    $stmt=$mysqli->prepare("SELECT nama_file FROM files WHERE owner_username=? AND is_deleted=0 AND jenis='file'");
    $stmt->bind_param('s',$username);$stmt->execute();$res=$stmt->get_result();
    while($r=$res->fetch_assoc()){$ext=strtolower(pathinfo($r['nama_file'],PATHINFO_EXTENSION));$tc[$ext]=($tc[$ext]??0)+1;}
    $stmt->close();arsort($tc);
    echo "<div style='display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0;border:1px solid var(--border-dark);margin:24px 32px;'>";
    foreach([['fa-folder','Folder',$stat_folders],['fa-file','File',$stat_files],['fa-link','Tautan',$stat_links],['fa-hard-drive','Penyimpanan',$size_used]] as [$ic,$lbl,$val]){
        echo "<div style='padding:24px;border-right:1px solid var(--border-dark);'><div style='font-size:.65rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--text-muted);margin-bottom:10px;'><i class='fa-solid $ic' style='margin-right:6px;'></i>$lbl</div><div style='font-family:\"Playfair Display\",serif;font-size:2rem;font-weight:900;'>$val</div></div>";
    }
    echo "</div>";
    if(!empty($tc)){
        echo "<div style='margin:0 32px 32px;border:1px solid var(--border-dark);'><div style='padding:16px 20px;border-bottom:1px solid var(--border);font-size:.75rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase;'>Jenis File</div><div style='padding:20px;'>";
        $max=max($tc);
        foreach(array_slice($tc,0,12) as $ext=>$cnt){
            $id2=getFileIcon("f.$ext");$p2=round(($cnt/$max)*100);
            echo "<div style='display:flex;align-items:center;gap:12px;margin-bottom:10px;'><span style='width:36px;font-size:.72rem;font-weight:700;text-transform:uppercase;'>".h($ext)."</span><div style='flex:1;height:3px;background:var(--border);'><div style='height:100%;width:{$p2}%;background:var(--text-main);'></div></div><span style='font-size:.78rem;font-weight:700;width:30px;text-align:right;'>$cnt</span></div>";
        }
        echo "</div></div>";
    }
}elseif($ws_view==='recent'){
    $stmt=$mysqli->prepare("SELECT f.*,fo.nama_folder as folder_name FROM files f LEFT JOIN folders fo ON f.folder_id=fo.id WHERE f.is_deleted=0".(isAdmin()?"":" AND f.owner_username=?")." ORDER BY f.tanggal_upload DESC LIMIT 30");
    if(isAdmin())$stmt->execute();else{$stmt->bind_param('s',$username);$stmt->execute();}
    $res=$stmt->get_result();$has=false;
    while($item=$res->fetch_assoc()){
        $has=true;$is_lnk=($item['jenis']==='link');
        $ds=date('d M Y H:i',strtotime($item['tanggal_upload']));
        $sn=h($item['nama_file']);$st=h($item['tags']??'');
        $av='https://ui-avatars.com/api/?name='.urlencode($item['owner_username']).'&background=1a1a1a&color=ffffff&size=32';
        if($is_lnk){$id2=['fa-link','#555'];$sz='Tautan';}else{$id2=getFileIcon($item['nama_file']);$fp2=UPLOAD_DIR.$item['file_path'];$sz=file_exists($fp2)?formatBytes(filesize($fp2)):'-';}
        $fl=!empty($item['folder_name'])?h($item['folder_name']):'Root';$pt=$is_lnk?'none':getPreviewType($item['nama_file']);
        $ah=$is_lnk?"<a href='".h($item['link_url'])."' target='_blank' class='btn-rs-action btn-rs-primary'><i class='fa-solid fa-arrow-up-right-from-square'></i> Kunjungi</a>":"<a href='?action=download_file&file_id={$item['id']}' class='btn-rs-action btn-rs-secondary'><i class='fa-solid fa-download'></i> Download</a>";
        echo "<div class='item-card' onclick='handleItemClick(event,this)' data-id='{$item['id']}' data-item-type='{$item['jenis']}' data-type='{$item['jenis']}' data-name='$sn' data-icon='fa-solid {$id2[0]}' data-color='{$id2[1]}' data-owner='".h($item['owner_username'])."' data-date='$ds' data-size='$sz' data-desc='Folder: $fl' data-url='' data-tags='$st' data-share='' data-preview='$pt'>";
        echo "<input type='checkbox' class='item-checkbox' onclick='handleCheckbox(event,this)'>";
        echo "<div class='hidden-action-html' style='display:none;'>$ah</div>";
        echo "<div class='item-info-wrap'><div class='item-icon-lg' style='color:{$id2[1]};'><i class='fa-solid {$id2[0]}'></i></div><div class='item-details'><div class='item-name'>$sn</div><span style='font-size:.72rem;color:var(--text-muted);'>$fl</span></div></div>";
        echo "<div class='col-owner'><img src='$av' alt=''> ".h($item['owner_username'])."</div><div class='col-date'>$ds</div><div class='col-size'>$sz</div>";
        echo "<div class='action-wrapper'></div></div>";
    }
    $stmt->close();
    if(!$has)echo "<div class='empty-state' style='cursor:default;'><i class='fa-solid fa-clock-rotate-left'></i><h3>Belum Ada Aktivitas Terbaru</h3></div>";
}elseif($ws_view==='assets'){
    $stmt=isAdmin()
        ?$mysqli->prepare("SELECT * FROM files WHERE is_deleted=0 AND jenis='file' AND (nama_file LIKE '%.jpg' OR nama_file LIKE '%.jpeg' OR nama_file LIKE '%.png' OR nama_file LIKE '%.gif' OR nama_file LIKE '%.webp') ORDER BY tanggal_upload DESC")
        :$mysqli->prepare("SELECT * FROM files WHERE owner_username=? AND is_deleted=0 AND jenis='file' AND (nama_file LIKE '%.jpg' OR nama_file LIKE '%.jpeg' OR nama_file LIKE '%.png' OR nama_file LIKE '%.gif' OR nama_file LIKE '%.webp') ORDER BY tanggal_upload DESC");
    if(!isAdmin()){$stmt->bind_param('s',$username);}$stmt->execute();$res=$stmt->get_result();$has=false;
    if($res->num_rows>0){
        $has=true;
        echo "<div style='display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:0;border:1px solid var(--border-dark);margin:0;'>";
        while($img=$res->fetch_assoc()){
            $tu=UPLOAD_DIR.$img['file_path'];$sn=h($img['nama_file']);
            echo "<div style='border-right:1px solid var(--border);border-bottom:1px solid var(--border);cursor:pointer;' onmouseover='this.style.opacity=\".8\"' onmouseout='this.style.opacity=\"1\"' onclick=\"openPreview('{$sn}','$tu','image',{$img['id']})\"><div style='width:100%;height:120px;overflow:hidden;background:#f5f5f5;'><img src='$tu' alt='$sn' style='width:100%;height:100%;object-fit:cover;filter:grayscale(100%);'></div><div style='padding:10px 12px;'><div style='font-size:.78rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;'>$sn</div><div style='font-size:.7rem;color:var(--text-muted);margin-top:2px;'>".date('d M Y',strtotime($img['tanggal_upload']))."</div></div></div>";
        }
        echo "</div>";
    }
    $stmt->close();
    if(!$has)echo "<div class='empty-state' style='cursor:default;'><i class='fa-solid fa-images'></i><h3>Belum Ada Aset Visual</h3></div>";
}elseif($ws_view==='trash'){
    $tc2=0;
    if(isAdmin()){$stmt=$mysqli->prepare("SELECT * FROM folders WHERE is_deleted=1 ORDER BY $order_f");$stmt->execute();}
    else{$stmt=$mysqli->prepare("SELECT * FROM folders WHERE owner_username=? AND is_deleted=1 ORDER BY $order_f");$stmt->bind_param('s',$username);$stmt->execute();}
    $res=$stmt->get_result();$stmt->close();
    while($f=$res->fetch_assoc()){
        $tc2++;$sn=h($f['nama_folder']);
        echo "<div class='item-card' style='opacity:.6;' onclick='handleItemClick(event,this)' data-id='{$f['id']}' data-item-type='folder' data-type='folder' data-name='$sn' data-icon='fa-solid {$f['icon']}' data-color='{$f['warna']}' data-owner='".h($f['owner_username'])."' data-date='-' data-size='-' data-desc='".h($f['deskripsi'])."' data-url='' data-tags='' data-share='' data-preview='none'>";
        echo "<input type='checkbox' class='item-checkbox' onclick='handleCheckbox(event,this)'>";
        echo "<div class='hidden-action-html' style='display:none;'><a href='?page=workspace&action=restore&type=folder&id={$f['id']}' class='btn-rs-action btn-rs-primary'><i class='fa-solid fa-clock-rotate-left'></i> Pulihkan</a><a href='?page=workspace&action=hard_delete&type=folder&id={$f['id']}' onclick=\"return confirm('Hapus Permanen?');\" class='btn-rs-action btn-rs-danger'><i class='fa-solid fa-fire'></i> Hapus Permanen</a></div>";
        echo "<div class='item-info-wrap'><div class='item-icon-lg' style='color:#555;'><i class='fa-solid {$f['icon']}'></i></div><div class='item-name' style='text-decoration:line-through;'>$sn</div></div>";
        echo "<div class='col-owner'>".h($f['owner_username'])."</div><div class='col-date'>-</div><div class='col-size'>-</div>";
        echo "<div class='action-wrapper'><button class='btn-dots' onclick='toggleActionMenu(event,\"tmf_{$f['id']}\")'><i class='fa-solid fa-ellipsis-vertical'></i></button><div id='tmf_{$f['id']}' class='action-dropdown'><a href='?page=workspace&action=restore&type=folder&id={$f['id']}'><i class='fa-solid fa-clock-rotate-left'></i> Pulihkan</a><a href='?page=workspace&action=hard_delete&type=folder&id={$f['id']}' onclick=\"return confirm('Hapus Permanen?');\" style='color:var(--danger);'><i class='fa-solid fa-fire'></i> Hapus Selamanya</a></div></div></div>";
    }
    if(isAdmin()){$stmt=$mysqli->prepare("SELECT * FROM files WHERE is_deleted=1 ORDER BY $order_i");$stmt->execute();}
    else{$stmt=$mysqli->prepare("SELECT * FROM files WHERE owner_username=? AND is_deleted=1 ORDER BY $order_i");$stmt->bind_param('s',$username);$stmt->execute();}
    $res=$stmt->get_result();$stmt->close();
    while($f=$res->fetch_assoc()){
        $tc2++;$sn=h($f['nama_file']);$is_lnk=($f['jenis']==='link');
        $ic=$is_lnk?'fa-link':getFileIcon($f['nama_file'])[0];$ds=date('d M Y',strtotime($f['tanggal_upload']));
        echo "<div class='item-card' style='opacity:.6;' onclick='handleItemClick(event,this)' data-id='{$f['id']}' data-item-type='{$f['jenis']}' data-type='{$f['jenis']}' data-name='$sn' data-icon='fa-solid $ic' data-color='#555' data-owner='".h($f['owner_username'])."' data-date='$ds' data-size='-' data-desc='Dihapus' data-url='' data-tags='' data-share='' data-preview='none'>";
        echo "<input type='checkbox' class='item-checkbox' onclick='handleCheckbox(event,this)'>";
        echo "<div class='hidden-action-html' style='display:none;'><a href='?page=workspace&action=restore&type=file&id={$f['id']}' class='btn-rs-action btn-rs-primary'><i class='fa-solid fa-clock-rotate-left'></i> Pulihkan</a><a href='?page=workspace&action=hard_delete&type=file&id={$f['id']}' onclick=\"return confirm('Hapus Permanen?');\" class='btn-rs-action btn-rs-danger'><i class='fa-solid fa-fire'></i> Hapus Permanen</a></div>";
        echo "<div class='item-info-wrap'><div class='item-icon-lg' style='color:#555;'><i class='fa-solid $ic'></i></div><div class='item-name' style='text-decoration:line-through;'>$sn</div></div>";
        echo "<div class='col-owner'>".h($f['owner_username'])."</div><div class='col-date'>$ds</div><div class='col-size'>-</div>";
        echo "<div class='action-wrapper'><button class='btn-dots' onclick='toggleActionMenu(event,\"tmi_{$f['id']}\")'><i class='fa-solid fa-ellipsis-vertical'></i></button><div id='tmi_{$f['id']}' class='action-dropdown'><a href='?page=workspace&action=restore&type=file&id={$f['id']}'><i class='fa-solid fa-clock-rotate-left'></i> Pulihkan</a><a href='?page=workspace&action=hard_delete&type=file&id={$f['id']}' onclick=\"return confirm('Hapus Permanen?');\" style='color:var(--danger);'><i class='fa-solid fa-fire'></i> Hapus Selamanya</a></div></div></div>";
    }
    if($tc2===0)echo "<div class='empty-state' style='cursor:default;'><i class='fa-solid fa-recycle'></i><h3>Tong Sampah Bersih</h3></div>";
}else{
    // HOME
    $f_sql="SELECT * FROM folders WHERE is_deleted=0";
    $f_params=[];$f_types='';
    if($active_folder){$f_sql.=" AND parent_id=?";$f_types.='i';$f_params[]=$active_folder;}
    else{$f_sql.=" AND parent_id IS NULL";}
    if(isAdmin()&&!$active_folder&&!$search_query){
        if($admin_filter!=='semua'){$f_sql.=" AND owner_username=?";$f_types.='s';$f_params[]=$admin_filter;}
    }elseif(!isAdmin()){$f_sql.=" AND owner_username=?";$f_types.='s';$f_params[]=$username;}
    if($search_query){$f_sql.=" AND nama_folder LIKE ?";$f_types.='s';$f_params[]='%'.$search_query.'%';}
    $f_sql.=" ORDER BY $order_f";
    $stmt=$mysqli->prepare($f_sql);
    if($f_types){$stmt->bind_param($f_types,...$f_params);}
    $stmt->execute();$res=$stmt->get_result();$has=false;$stmt->close();
    while($f=$res->fetch_assoc()){
        $has=true;$sn=h($f['nama_folder']);$sd=h($f['deskripsi']??'');
        $av='https://ui-avatars.com/api/?name='.urlencode($f['owner_username']).'&background=1a1a1a&color=ffffff&size=32';
        $ah="<a href='?page=workspace&folder_id={$f['id']}' class='btn-rs-action btn-rs-primary'><i class='fa-solid fa-folder-open'></i> Buka</a><a href='?action=download_zip&folder_id={$f['id']}' class='btn-rs-action btn-rs-secondary'><i class='fa-solid fa-file-zipper'></i> ZIP</a><button onclick=\"openMoveModal('folder',{$f['id']},'$sn')\" class='btn-rs-action btn-rs-secondary'><i class='fa-solid fa-folder-tree'></i> Pindah</button><a href='?page=workspace&action=soft_delete_folder&id={$f['id']}' class='btn-rs-action btn-rs-danger'><i class='fa-solid fa-trash-can'></i> Hapus</a>";
        echo "<div class='item-card' draggable='true' ondblclick=\"window.location='?page=workspace&folder_id={$f['id']}'\" onclick='handleItemClick(event,this)' data-id='{$f['id']}' data-item-type='folder' data-type='folder' data-name='$sn' data-icon='fa-solid {$f['icon']}' data-color='{$f['warna']}' data-owner='".h($f['owner_username'])."' data-date='-' data-size='-' data-desc='$sd' data-url='' data-tags='' data-share='' data-preview='none'>";
        echo "<input type='checkbox' class='item-checkbox' onclick='handleCheckbox(event,this)'>";
        echo "<div class='hidden-action-html' style='display:none;'>$ah</div>";
        echo "<div class='item-info-wrap'><div class='item-icon-lg' style='color:#555;'><i class='fa-solid fa-folder'></i></div><div class='item-name'>$sn</div></div>";
        echo "<div class='col-owner'><img src='$av' alt=''> ".h($f['owner_username'])."</div><div class='col-date'>-</div><div class='col-size'>-</div>";
        echo "<div class='action-wrapper'><button class='btn-dots' onclick='toggleActionMenu(event,\"mf_{$f['id']}\")'><i class='fa-solid fa-ellipsis-vertical'></i></button><div id='mf_{$f['id']}' class='action-dropdown'><a href='?page=workspace&folder_id={$f['id']}'><i class='fa-solid fa-folder-open'></i> Buka folder</a><a href='?action=download_zip&folder_id={$f['id']}'><i class='fa-solid fa-file-zipper'></i> Download ZIP</a><button onclick=\"openMoveModal('folder',{$f['id']},'$sn');closeAllMenus();\"><i class='fa-solid fa-folder-tree'></i> Pindahkan</button><button onclick=\"openEditModal({$f['id']},'$sn','$sd','{$f['icon']}','{$f['warna']}');closeAllMenus();\"><i class='fa-solid fa-pen'></i> Edit</button><button onclick=\"startInlineRename(this.closest('.item-card'));closeAllMenus();\"><i class='fa-solid fa-i-cursor'></i> Ganti nama</button><hr class='menu-divider'><a href='?page=workspace&action=soft_delete_folder&id={$f['id']}' style='color:var(--danger);'><i class='fa-solid fa-trash'></i> Hapus</a></div></div></div>";
    }
    if($active_folder){
        $i_sql="SELECT * FROM files WHERE folder_id=? AND is_deleted=0";
        $i_types='i';$i_params=[$active_folder];
        if($search_query){$i_sql.=" AND (nama_file LIKE ? OR tags LIKE ?)";$i_types.='ss';$i_params[]='%'.$search_query.'%';$i_params[]='%'.$search_query.'%';}
        $i_sql.=" ORDER BY $order_i";
        $stmt=$mysqli->prepare($i_sql);$stmt->bind_param($i_types,...$i_params);$stmt->execute();$res=$stmt->get_result();$stmt->close();
        while($item=$res->fetch_assoc()){
            $has=true;$is_lnk=($item['jenis']==='link');
            $ds=date('d M Y',strtotime($item['tanggal_upload']));
            $av='https://ui-avatars.com/api/?name='.urlencode($item['owner_username']).'&background=1a1a1a&color=ffffff&size=32';
            $sn=h($item['nama_file']);$st=h($item['tags']??'');
            $pt=$is_lnk?'none':getPreviewType($item['nama_file']);
            if($is_lnk){
                $sz="Tautan";$js_icon="fa-solid fa-link";$ic_col="#555";$file_url='';
                $ah="<a href='".h($item['link_url'])."' target='_blank' class='btn-rs-action btn-rs-primary'><i class='fa-solid fa-arrow-up-right-from-square'></i> Kunjungi</a><button onclick=\"copyLink('".h($item['link_url'])."')\" class='btn-rs-action btn-rs-secondary'><i class='fa-solid fa-copy'></i> Salin URL</button><a href='?page=workspace&action=soft_delete_item&item_id={$item['id']}' class='btn-rs-action btn-rs-danger'><i class='fa-solid fa-trash-can'></i> Hapus</a>";
                $dot_actions="<a href='".h($item['link_url'])."' target='_blank'><i class='fa-solid fa-arrow-up-right-from-square'></i> Buka</a>";
            }else{
                $id_data=getFileIcon($item['nama_file']);$js_icon="fa-solid ".$id_data[0];$ic_col=$id_data[1];
                $fp2=UPLOAD_DIR.$item['file_path'];$file_url=$fp2;
                $sz=file_exists($fp2)?formatBytes(filesize($fp2)):'Tidak valid';
                $tok=$item['share_token']??'';$share_full=SITE_URL.'/index.php?share='.$tok;
                $wa_txt=urlencode("Halo, berikut file:\n*{$item['nama_file']}*\nLink: {$share_full}");
                $wa_link="https://api.whatsapp.com/send?text=".$wa_txt;
                $ah="<button onclick=\"openPreview('".addslashes($sn)."','$fp2','$pt',{$item['id']})\" class='btn-rs-action btn-rs-primary'><i class='fa-regular fa-eye'></i> Pratinjau</button><a href='?action=download_file&file_id={$item['id']}' class='btn-rs-action btn-rs-secondary'><i class='fa-solid fa-download'></i> Download</a>";
                if($tok)$ah.="<button onclick=\"copyLink('".h($share_full)."')\" class='btn-rs-action btn-rs-secondary'><i class='fa-solid fa-copy'></i> Salin Link</button><a href='$wa_link' target='_blank' class='btn-rs-action btn-rs-whatsapp'><i class='fa-brands fa-whatsapp'></i> WA</a>";
                else $ah.="<a href='?action=create_share&file_id={$item['id']}' class='btn-rs-action btn-rs-secondary'><i class='fa-solid fa-earth-asia'></i> Buat Link</a>";
                $ah.="<button onclick=\"openMoveModal('file',{$item['id']},'$sn')\" class='btn-rs-action btn-rs-secondary'><i class='fa-solid fa-folder-tree'></i> Pindah</button><a href='?page=workspace&action=soft_delete_item&item_id={$item['id']}' class='btn-rs-action btn-rs-danger'><i class='fa-solid fa-trash-can'></i> Hapus</a>";
                $dot_actions="<button onclick=\"openPreview('".addslashes($sn)."','$fp2','$pt',{$item['id']});closeAllMenus();\"><i class='fa-regular fa-eye'></i> Pratinjau</button><a href='?action=download_file&file_id={$item['id']}'><i class='fa-solid fa-download'></i> Download</a>";
            }
            echo "<div class='item-card' draggable='true' onclick='handleItemClick(event,this)' data-id='{$item['id']}' data-item-type='{$item['jenis']}' data-type='{$item['jenis']}' data-name='$sn' data-icon='$js_icon' data-color='$ic_col' data-owner='".h($item['owner_username'])."' data-date='$ds' data-size='$sz' data-desc='' data-url='$file_url' data-tags='$st' data-share='".h($tok??'')."' data-preview='$pt'>";
            echo "<input type='checkbox' class='item-checkbox' onclick='handleCheckbox(event,this)'>";
            echo "<div class='hidden-action-html' style='display:none;'>$ah</div>";
            echo "<div class='item-info-wrap'><div class='item-icon-lg' style='color:#555;'><i class='$js_icon'></i></div><div class='item-name'>$sn".($st?"<span class='tag-badge'><i class='fa-solid fa-tag'></i> $st</span>":"")."</div></div>";
            echo "<div class='col-owner'><img src='$av' alt=''> ".h($item['owner_username'])."</div><div class='col-date'>$ds</div><div class='col-size'>$sz</div>";
            echo "<div class='action-wrapper'><button class='btn-dots' onclick='toggleActionMenu(event,\"mi_{$item['id']}\")'><i class='fa-solid fa-ellipsis-vertical'></i></button><div id='mi_{$item['id']}' class='action-dropdown'>$dot_actions<button onclick=\"startInlineRename(this.closest('.item-card'));closeAllMenus();\"><i class='fa-solid fa-i-cursor'></i> Ganti nama</button><button onclick=\"openMoveModal('file',{$item['id']},'$sn');closeAllMenus();\"><i class='fa-solid fa-folder-tree'></i> Pindahkan</button><hr class='menu-divider'><a href='?page=workspace&action=soft_delete_item&item_id={$item['id']}' style='color:var(--danger);'><i class='fa-solid fa-trash'></i> Hapus</a></div></div></div>";
        }
    }
    if(!$has)echo "<div class='empty-state' onclick=\"openModal('addFolderModal')\"><i class='fa-solid fa-folder-plus'></i><h3>Workspace Kosong</h3><p>Klik untuk membuat folder baru.</p></div>";
}
?>
</div><!-- workspaceContainer -->

<?php
// ══════════════ PAGE: PROFILE ══════════════
}elseif($current_page==='profile'){
    include "tampilan/dasbor/pembuat_cv.php";
    // ══════════════ PAGE: MANAJEMEN PENGGUNA ══════════════
}elseif($current_page==='manajemen-pengguna'){
    include "tampilan/dasbor/pengelola_pengguna.php";
}// end page routing
?>
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


<!-- ═══════════════════════════════════════════════════════════
     ALL MODALS
════════════════════════════════════════════════════════════ -->

<!-- ADD FOLDER MODAL -->
<div id="addFolderModal" class="modal">
  <div class="modal-content">
    <div class="modal-title">
      <span><i class="fa-solid fa-folder-plus"></i> Folder Baru</span>
      <button class="close-btn" onclick="closeModal('addFolderModal')">&times;</button>
    </div>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="add_folder">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
      <?php if($active_folder) echo "<input type='hidden' name='parent_id' value='{$active_folder}'>"; ?>
      <label>Nama Folder</label>
      <input type="text" name="nama_folder" placeholder="cth: Dokumen Proyek" required>
      <label>Deskripsi <span style="font-weight:400;color:var(--text-muted);">(opsional)</span></label>
      <input type="text" name="deskripsi" placeholder="Catatan singkat tentang folder ini">
      <?php if(isAdmin()){ ?>
      <label>Pemilik</label>
      <select name="owner_username">
        <?php foreach($all_users as $u) echo "<option value='".h($u['username'])."'".($u['username']===$username?' selected':'').">".h($u['nama_lengkap']??$u['username'])." (@".h($u['username']).")</option>"; ?>
      </select>
      <?php } ?>
      <button type="submit" class="btn-submit-modal"><i class="fa-solid fa-folder-plus"></i> Buat Folder</button>
    </form>
  </div>
</div>

<!-- EDIT FOLDER MODAL -->
<div id="editFolderModal" class="modal">
  <div class="modal-content">
    <div class="modal-title">
      <span><i class="fa-solid fa-pen"></i> Edit Folder</span>
      <button class="close-btn" onclick="closeModal('editFolderModal')">&times;</button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="edit_folder">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
      <input type="hidden" name="folder_id" id="edit_folder_id">
      <label>Nama Folder</label>
      <input type="text" name="nama_folder" id="edit_folder_nama" required>
      <label>Deskripsi</label>
      <input type="text" name="deskripsi" id="edit_folder_desc">
      <label>Icon <span style="font-weight:400;color:var(--text-muted);">(Font Awesome class)</span></label>
      <input type="text" name="icon" id="edit_folder_icon" placeholder="fa-folder">
      <label>Warna Folder</label>
      <input type="color" name="warna" id="edit_folder_warna" value="#0a0a0a">
      <button type="submit" class="btn-submit-modal"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
    </form>
  </div>
</div>

<!-- ADD ITEM MODAL (File / Link) -->
<div id="addItemModal" class="modal">
  <div class="modal-content">
    <div class="modal-title">
      <span><i class="fa-solid fa-file-circle-plus"></i> Tambah Item</span>
      <button class="close-btn" onclick="closeModal('addItemModal')">&times;</button>
    </div>
    <form method="POST" enctype="multipart/form-data" id="addItemForm">
      <input type="hidden" name="action" value="add_item">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
      <input type="hidden" name="folder_id" value="<?= (int)$active_folder ?>">
      <input type="hidden" name="jenis" id="jenis_input" value="file">
      <div style="display:flex;gap:0;margin-bottom:20px;border:1px solid var(--border-dark);">
        <button type="button" id="tabFile" onclick="switchType('file')" style="flex:1;padding:10px;font-size:.75rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;border:none;background:var(--text-main);color:#fff;font-family:'Inter',sans-serif;"><i class="fa-solid fa-file-arrow-up"></i> Upload File</button>
        <button type="button" id="tabLink" onclick="switchType('link')" style="flex:1;padding:10px;font-size:.75rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;border:none;background:#f5f5f5;color:var(--text-main);font-family:'Inter',sans-serif;border-left:1px solid var(--border);"><i class="fa-solid fa-link"></i> Simpan Tautan</button>
      </div>
      <div id="form_file">
        <label>Pilih File <span style="font-weight:400;color:var(--text-muted);">(multiple diperbolehkan)</span></label>
        <div class="upload-zone" id="modalUploadZone" onclick="document.getElementById('modal_file_input').click()">
          <i class="fa-solid fa-cloud-arrow-up"></i>
          <p>Klik atau drag &amp; drop file ke sini</p>
          <input type="file" id="modal_file_input" name="file_upload[]" multiple>
        </div>
        <input type="file" id="modal_folder_input" name="file_upload[]" webkitdirectory directory multiple style="display:none;" onchange="document.getElementById('addItemForm').submit()">
        <div id="selectedFilesList"></div>
        <label>Label / Tag <span style="font-weight:400;color:var(--text-muted);">(opsional)</span></label>
        <input type="text" name="tags" placeholder="cth: penting, laporan, 2024">
      </div>
      <div id="form_link" style="display:none;">
        <label>Nama Tautan</label>
        <input type="text" name="nama_link" placeholder="cth: Website Referensi">
        <label>URL</label>
        <input type="url" name="link_url" placeholder="https://...">
        <label>Label / Tag <span style="font-weight:400;color:var(--text-muted);">(opsional)</span></label>
        <input type="text" name="tags" placeholder="cth: referensi, tools">
      </div>
      <button type="submit" class="btn-submit-modal"><i class="fa-solid fa-plus"></i> Tambahkan</button>
    </form>
  </div>
</div>

<!-- MOVE MODAL -->
<div id="moveModal" class="modal">
  <div class="modal-content">
    <div class="modal-title">
      <span><i class="fa-solid fa-folder-tree"></i> Pindahkan Item</span>
      <button class="close-btn" onclick="closeModal('moveModal')">&times;</button>
    </div>
    <form method="POST" id="moveForm">
      <input type="hidden" name="action" value="move_item">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
      <input type="hidden" name="move_type" id="move_type_input">
      <input type="hidden" name="move_id" id="move_id_input">
      <p id="move_item_name" style="font-size:.88rem;font-weight:600;color:var(--text-muted);margin-bottom:16px;padding:12px;background:#f9f9f9;border:1px solid var(--border);"></p>
      <label>Pindah ke Folder</label>
      <select name="target_folder" id="moveTargetSelect">
        <option value="root">&#8212; Root (Tanpa Folder) &#8212;</option>
        <?php foreach($all_folders_list as $af) echo "<option value='".(int)$af['id']."'>".h($af['nama_folder'])." (".h($af['owner_username']).")</option>"; ?>
      </select>
      <button type="submit" class="btn-submit-modal"><i class="fa-solid fa-check"></i> Pindahkan</button>
    </form>
  </div>
</div>

<!-- BULK MOVE MODAL -->
<div id="bulkMoveModal" class="modal">
  <div class="modal-content">
    <div class="modal-title">
      <span><i class="fa-solid fa-folder-tree"></i> Pindah Massal</span>
      <button class="close-btn" onclick="closeModal('bulkMoveModal')">&times;</button>
    </div>
    <form method="POST" id="bulkMoveForm">
      <input type="hidden" name="action" value="bulk_move">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
      <input type="hidden" name="ids" id="bulkMoveIds">
      <input type="hidden" name="types" id="bulkMoveTypes">
      <input type="hidden" name="target_folder" id="bulkMoveTarget">
      <label>Pindah ke Folder</label>
      <select id="bulkMoveTargetSelect">
        <option value="root">&#8212; Root (Tanpa Folder) &#8212;</option>
        <?php foreach($all_folders_list as $af) echo "<option value='".(int)$af['id']."'>".h($af['nama_folder'])." (".h($af['owner_username']).")</option>"; ?>
      </select>
      <button type="button" onclick="executeBulkMove()" class="btn-submit-modal"><i class="fa-solid fa-check"></i> Pindahkan Semua</button>
    </form>
  </div>
</div>

<!-- SETTINGS MODAL -->
<div id="settingsModal" class="modal">
  <div class="modal-content">
    <div class="modal-title">
      <span><i class="fa-solid fa-gear"></i> Pengaturan Akun</span>
      <button class="close-btn" onclick="closeModal('settingsModal')">&times;</button>
    </div>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="update_settings">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
      <label>Foto Profil</label>
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:4px;">
        <img src="<?= h($path_foto) ?>" style="width:56px;height:56px;object-fit:cover;filter:grayscale(100%);border:1px solid var(--border-dark);" alt="Foto Profil">
        <input type="file" name="foto_profil" accept="image/*" style="font-size:.82rem;">
      </div>
      <label>Nama Lengkap</label>
      <input type="text" name="nama_lengkap" value="<?= h($nama_lengkap) ?>" required>
      <label>Password Baru <span style="font-weight:400;color:var(--text-muted);">(kosongkan jika tidak ingin ganti)</span></label>
      <input type="password" name="new_password" placeholder="Minimal 8 karakter" autocomplete="new-password">
      <button type="submit" class="btn-submit-modal"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
    </form>
  </div>
</div>

<!-- ADD USER MODAL (SuperAdmin) -->
<?php if(isSuperAdmin()){ ?>
<div id="addUserModal" class="modal">
  <div class="modal-content">
    <div class="modal-title">
      <span><i class="fa-solid fa-user-plus"></i> Tambah User Baru</span>
      <button class="close-btn" onclick="closeModal('addUserModal')">&times;</button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="add_user">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
      <label>Username</label>
      <input type="text" name="new_username" placeholder="Username unik" required>
      <label>Nama Lengkap</label>
      <input type="text" name="new_nama" placeholder="Nama tampilan">
      <label>Password</label>
      <input type="password" name="new_password" placeholder="Minimal 8 karakter" required>
      <label>Role</label>
      <select name="new_role">
        <option value="user">User</option>
        <option value="admin">Admin</option>
        <option value="superadmin">Super Admin</option>
      </select>
      <button type="submit" class="btn-submit-modal"><i class="fa-solid fa-user-plus"></i> Buat Akun</button>
    </form>
  </div>
</div>

<!-- EDIT USER MODAL -->
<div id="editUserModal" class="modal">
  <div class="modal-content">
    <div class="modal-title">
      <span><i class="fa-solid fa-user-pen"></i> Edit User</span>
      <button class="close-btn" onclick="closeModal('editUserModal')">&times;</button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="edit_user">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
      <input type="hidden" name="edit_uid" id="eu_id">
      <label>Username</label>
      <input type="text" id="eu_username" disabled style="color:var(--text-muted);">
      <label>Nama Lengkap</label>
      <input type="text" name="edit_nama" id="eu_nama" required>
      <label>Role</label>
      <select name="edit_role" id="eu_role">
        <option value="user">User</option>
        <option value="admin">Admin</option>
        <option value="superadmin">Super Admin</option>
      </select>
      <label>Password Baru <span style="font-weight:400;color:var(--text-muted);">(kosongkan jika tidak ingin ganti)</span></label>
      <input type="password" name="edit_password" placeholder="Kosongkan untuk tidak mengubah">
      <button type="submit" class="btn-submit-modal"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
    </form>
  </div>
</div>
<?php } ?>

<!-- HIDDEN FORMS for bulk operations and rename -->
<form id="bulkDeleteForm" method="POST" style="display:none;">
  <input type="hidden" name="action" value="bulk_delete">
  <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
  <input type="hidden" name="ids" id="bulkDeleteIds">
  <input type="hidden" name="types" id="bulkDeleteTypes">
</form>
<form id="deleteUserForm" method="POST" style="display:none;">
  <input type="hidden" name="action" value="delete_user">
  <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
  <input type="hidden" name="del_uid" id="del_uid_input">
</form>
<form id="renameForm" method="POST" style="display:none;">
  <input type="hidden" name="action" value="rename_item">
  <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
  <input type="hidden" name="item_id" id="renameItemId">
  <input type="hidden" name="item_type" id="renameItemType">
  <input type="hidden" name="new_name" id="renameNewName">
</form>
<form id="autoUploadForm" method="POST" enctype="multipart/form-data" style="display:none;">
  <input type="hidden" name="action" value="add_item">
  <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
  <input type="hidden" name="folder_id" value="<?= (int)$active_folder ?>">
  <input type="hidden" name="jenis" value="file">
  <input type="file" id="autoFileInput" name="file_upload[]" multiple>
</form>

<!-- ═══════════════════════════════════════════════════════════
     JAVASCRIPT — COMPLETE
════════════════════════════════════════════════════════════ -->
<script>
const CSRF = '<?= h($csrf_token) ?>';
const CURRENT_PAGE = '<?= h($current_page) ?>';

// ── SIDEBAR ──────────────────────────────────────────────────
let sidebarOpen = false;
function toggleSidebar() {
    const sb = document.getElementById('sidebar');
    const ov = document.getElementById('sidebarOverlay');
    sidebarOpen = !sidebarOpen;
    if (sidebarOpen) { sb.classList.add('active'); ov.classList.add('active'); document.body.style.overflow = 'hidden'; }
    else { sb.classList.remove('active'); ov.classList.remove('active'); document.body.style.overflow = ''; }
}

// ── PROFILE MENU ─────────────────────────────────────────────
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

// ── MODALS ───────────────────────────────────────────────────
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
    document.getElementById('move_item_name').textContent = '📦 ' + name;
    openModal('moveModal');
}

// ── RIGHT SIDEBAR ─────────────────────────────────────────────
let isSidebarOpen = false;
function toggleRightSidebar() {
    const rs = document.getElementById('rightSidebar');
    if (!rs) return;
    isSidebarOpen = !isSidebarOpen;
    if (isSidebarOpen) rs.classList.add('active'); else rs.classList.remove('active');
}

// ── TOAST ─────────────────────────────────────────────────────
function showToast(msg) {
    const t = document.getElementById('toast');
    if (!t) return;
    t.innerHTML = msg;
    t.classList.remove('show');
    void t.offsetWidth;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3700);
}

// ── VIEW MODE ─────────────────────────────────────────────────
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

// ── ITEM CLICK (select + right sidebar) ──────────────────────
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

// ── MOBILE PANEL ─────────────────────────────────────────────
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

// ── CHECKBOXES & BULK ─────────────────────────────────────────
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

// ── CONFIRM DIALOG ────────────────────────────────────────────
let confirmCallback = null;
function showConfirm(title, message, callback, icon = '⚠️') {
    document.getElementById('confirmTitle').textContent   = title;
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmIcon').textContent    = icon;
    document.getElementById('confirmOverlay').classList.add('active');
    confirmCallback = callback;
}
function closeConfirm() { document.getElementById('confirmOverlay').classList.remove('active'); confirmCallback = null; }
function executeConfirmAction() { if (confirmCallback) confirmCallback(); closeConfirm(); }

// ── ACTION DROPDOWN TOGGLE ───────────────────────────────────
function toggleActionMenu(event, id) {
    event.stopPropagation();
    const dd = document.getElementById(id);
    const isOpen = dd.classList.contains('show');
    document.querySelectorAll('.action-dropdown.show').forEach(d => d.classList.remove('show'));
    if (!isOpen) dd.classList.add('show');
}

// ── INLINE RENAME ─────────────────────────────────────────────
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

// ── PREVIEW MODAL ─────────────────────────────────────────────
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

// ── FAB ───────────────────────────────────────────────────────
function toggleFab() {
    const m = document.getElementById('fabMenu'), b = document.getElementById('fabBtn');
    if (!m) return;
    m.classList.toggle('active');
    b.innerHTML = m.classList.contains('active') ? '<i class="fa-solid fa-xmark"></i>' : '<i class="fa-solid fa-plus"></i>';
}

// ── SWITCH TYPE (file/link in addItem modal) ──────────────────
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

// ── FILE INPUT DISPLAY ────────────────────────────────────────
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

// ── DRAG & DROP BETWEEN FOLDERS ──────────────────────────────
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

// ── GLOBAL FILE DROP OVERLAY ──────────────────────────────────
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

// ── COPY LINK ────────────────────────────────────────────────
function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => { showToast('<i class="fa-solid fa-check-circle"></i> Link berhasil disalin!'); });
}
function copyPortfolioLink() {
    const inp = document.getElementById('portfolioLinkInput');
    if (inp) navigator.clipboard.writeText(inp.value).then(() => { showToast('<i class="fa-solid fa-check-circle"></i> Link portfolio disalin!'); });
}

// ── KEYBOARD SHORTCUTS ────────────────────────────────────────
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

// ── PROFILE TAB SWITCHING ─────────────────────────────────────
function switchTab(tabId) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    const panel = document.getElementById('tab-' + tabId);
    if (panel) panel.classList.add('active');
    document.querySelectorAll('.tab-btn').forEach(b => {
        if (b.getAttribute('onclick') && b.getAttribute('onclick').includes("'" + tabId + "'")) b.classList.add('active');
    });
}

// ── AJAX PROFILE SAVE WITH SWEETALERT ────────────────────────
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

// ── DYNAMIC ACCORDION ITEM BUILDERS ──────────────────────────
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
            ${_dynField('exp_periode[]','Periode','cth: 2022 — 2024')}
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

// ── USER MANAGEMENT (SuperAdmin) ─────────────────────────────
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
        }, '🗑️'
    );
}

// ── AUTO SHOW TOAST for server-side alert ─────────────────────
<?php if (!empty($alert_msg)) {
    $is_err = (str_contains($alert_msg,'gagal') || str_contains($alert_msg,'tidak valid') || str_contains($alert_msg,'Sesi'));
    if (!$is_err) { ?>
setTimeout(() => showToast('<i class="fa-solid fa-circle-check" style="margin-right:6px;color:#16a34a;"></i> <?= h($alert_msg) ?>'), 300);
<?php } } ?>

/* ═══════════════════════════════════════════════════
   DASHBOARD MICRO-INTERACTIONS & ANIMATION v2
   Stagger reveals, accordion grid-rows, hover glow
   ═══════════════════════════════════════════════════ */

// ── Page Load: Stagger all stat blocks ──
document.querySelectorAll('.bento-card,.stat-block,.ed-card,.section-card').forEach((el, i) => {
  el.classList.add('stagger-child');
  el.style.animationDelay = (0.03 + i * 0.05) + 's';
});

// ── Accordion: Smooth grid-rows toggle ──
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

// ── Item Card: hover glow border effect ──
document.querySelectorAll('.item-card').forEach(card => {
  card.addEventListener('mouseenter', () => {
    card.style.transition = 'background .15s,box-shadow .2s';
  });
});

// ── Sidebar nav items: Ripple on click ──
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

// ── Smooth Scroll Reveal for content area ──
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

// ── Storage bar animated fill on load ──
document.querySelectorAll('.storage-bar-fill').forEach(bar => {
  const target = bar.style.width;
  bar.style.width = '0';
  setTimeout(() => {
    bar.style.transition = 'width .9s cubic-bezier(.16,1,.3,1)';
    bar.style.width = target;
  }, 400);
});

// ── Avatar hover: remove grayscale ──
document.querySelectorAll('.user-avatar-sm').forEach(img => {
  img.addEventListener('mouseenter', () => {img.style.filter = 'none';img.style.transform = 'scale(1.05)';});
  img.addEventListener('mouseleave', () => {img.style.filter = '';img.style.transform = '';});
});

// ── PWA SERVICE WORKER ────────────────────────────────────────
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js').catch(() => {});
}
</script>
<script>
  const CSRF = '<?= h($csrf_token) ?>';
  const CURRENT_USERNAME = '<?= h($username) ?>';
</script>
<script src="aset/js/context_menu.js"></script>
</body>
</html>
