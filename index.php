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
/* === MODERN PREMIUM WORKSPACE LAYOUT === */
.modern-workspace { padding: 8px 16px; min-height: calc(100vh - 80px); display: block !important; }

/* Typography & Layout */
.section-title { font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin: 32px 0 16px; letter-spacing: 0.5px; }
.grid-folders { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 32px; }
.grid-files { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }

/* Glassmorphism Cards */
.modern-folder-card { 
    display: flex; align-items: center; gap: 16px; height: 64px; padding: 0 16px;
    background: rgba(255,255,255, 0.03); border: 1px solid rgba(255,255,255, 0.05);
    border-radius: 16px; cursor: pointer; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    position: relative; overflow: hidden; backdrop-filter: blur(10px);
}
.modern-folder-card:hover {
    background: rgba(255,255,255, 0.07); border-color: rgba(255,255,255, 0.1);
    transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0, 0.2);
}
.modern-folder-card.selected { background: rgba(99, 102, 241, 0.2); border-color: var(--accent); }
.modern-folder-card .icon-wrap {
    width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
    border-radius: 10px; font-size: 1.2rem; background: rgba(255,255,255, 0.05);
}
.modern-folder-card .info-wrap { flex: 1; overflow: hidden; display: flex; flex-direction: column; justify-content: center;}
.modern-folder-card .item-name { font-weight: 600; font-size: 0.95rem; color: var(--text-main); white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
.modern-folder-card .item-meta { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }

/* File Card */
.modern-file-card {
    display: flex; flex-direction: column; height: 210px;
    background: rgba(255,255,255, 0.03); border: 1px solid rgba(255,255,255, 0.05);
    border-radius: 16px; cursor: pointer; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    position: relative; overflow: hidden; backdrop-filter: blur(10px);
}
.modern-file-card:hover {
    background: rgba(255,255,255, 0.07); border-color: rgba(255,255,255, 0.1);
    transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0, 0.3);
}
.modern-file-card.selected { background: rgba(99, 102, 241, 0.2); border-color: var(--accent); }
.modern-file-card .preview-area {
    height: 140px; background: rgba(0,0,0, 0.2); display: flex; align-items: center; justify-content: center;
    overflow: hidden; position: relative; border-bottom: 1px solid rgba(255,255,255,0.05);
}
.modern-file-card .preview-area img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
.modern-file-card:hover .preview-area img { transform: scale(1.05); }
.modern-file-card .icon-placeholder { font-size: 3.5rem; opacity: 0.7; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.5)); transition: transform 0.3s; }
.modern-file-card:hover .icon-placeholder { transform: scale(1.1); }

.modern-file-card .info-area { padding: 12px 16px; flex: 1; display: flex; align-items: center; gap: 12px; }
.modern-file-card .item-icon { font-size: 1.4rem; }
.modern-file-card .item-details { flex: 1; overflow: hidden; display: flex; flex-direction: column;}
.modern-file-card .item-name { font-weight: 600; font-size: 0.9rem; color: var(--text-main); white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
.modern-file-card .item-meta { font-size: 0.75rem; color: var(--text-muted); margin-top: 3px; }

/* Context Menu */
.modern-context-menu {
    position: fixed; background: rgba(20, 25, 35, 0.85); backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255, 0.1); border-radius: 12px; padding: 8px 0;
    min-width: 220px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); z-index: 9999;
    display: none; animation: scaleIn 0.15s ease-out;
}
.modern-context-menu.show { display: block; }
@keyframes scaleIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }

.modern-context-item {
    padding: 8px 20px; font-size: 0.85rem; color: var(--text-main); display: flex; align-items: center;
    gap: 12px; cursor: pointer; transition: background 0.1s; font-weight: 500; text-decoration: none;
}
.modern-context-item:hover { background: rgba(255,255,255, 0.08); color: var(--text-main); }
.modern-context-item i { width: 18px; text-align: center; color: var(--text-secondary); }
.modern-context-divider { height: 1px; background: rgba(255,255,255, 0.1); margin: 6px 0; }

/* Actions & Checkbox */
.action-wrapper, .item-checkbox, .col-owner, .col-date, .col-size { display: none; }
.modern-folder-card:hover .action-wrapper, .modern-file-card:hover .action-wrapper { 
    display: flex; align-items: center; justify-content: center;
    position: absolute; right: 12px; top: 12px; width: 32px; height: 32px;
    background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); border-radius: 8px; color: #fff; cursor: pointer;
}
.modern-folder-card:hover .item-checkbox, .modern-file-card:hover .item-checkbox {
    display: block; position: absolute; left: 12px; top: 12px; z-index: 2;
    width: 20px; height: 20px; accent-color: var(--accent); cursor: pointer;
}

/* List View Overrides */
.view-list .grid-folders, .view-list .grid-files { display: flex; flex-direction: column; gap: 4px; margin-bottom: 16px; }
.view-list .modern-folder-card, .view-list .modern-file-card {
    display: grid; grid-template-columns: 40px 1fr 180px 140px 100px 40px; align-items: center;
    height: 56px; border-radius: 12px; padding: 0 16px; background: transparent; border: 1px solid transparent; gap: 0;
}
.view-list .modern-folder-card:hover, .view-list .modern-file-card:hover {
    background: rgba(255,255,255, 0.03); border-color: rgba(255,255,255, 0.05); transform: none; box-shadow: none;
}
.view-list .preview-area { display: none; }
.view-list .info-area { padding: 0; }
.view-list .col-owner, .view-list .col-date, .view-list .col-size { display: block; font-size: 0.85rem; color: var(--text-secondary); }
.view-list .col-owner { display: flex; align-items: center; gap: 8px; }
.view-list .col-owner img { width: 24px; height: 24px; border-radius: 50%; }
.view-list .modern-folder-card .item-checkbox, .view-list .modern-file-card .item-checkbox {
    display: block; position: static; margin: 0;
}
.view-list .action-wrapper { display: flex !important; position: static !important; background: transparent !important; color: var(--text-secondary) !important; width: auto !important; height: auto !important; margin-left: auto;}
.view-list .action-wrapper:hover { color: var(--text-main) !important; background: transparent !important; }
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
