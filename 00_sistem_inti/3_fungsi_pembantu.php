<?php
// +------------------------------------------------------------------------------+
// |  FILE: 00_sistem_inti/3_fungsi_pembantu.php                                  |
// |                                                                              |
// |  DESKRIPSI:                                                                  |
// |  Berisi kumpulan fungsi-fungsi utilitas (pembantu) yang dapat dipanggil      |
// |  kapan saja. Fungsi-fungsi ini bersifat global untuk membantu operasi format,|
// |  keamanan (seperti CSRF dan XSS protection), dan routing sederhana.          |
// |                                                                              |
// |  KONEKSI & RELASI:                                                           |
// |  - Di-require oleh index.php pada awal proses pemuatan sistem.               |
// |  - Fungsi h() digunakan di semua tampilan HTML untuk mencegah serangan XSS.  |
// |                                                                              |
// |  BARIS KODE PENTING:                                                         |
// |  - function h() : Membersihkan string sebelum dirender di HTML (Anti XSS).   |
// |  - function generateCSRF() : Membuat token keamanan form (Anti CSRF).        |
// +------------------------------------------------------------------------------+

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

// ââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
// PENANGANAN AKSI BINER (UNDUH, CETAK, PORTFOLIO)
// ââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
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
    include "06_halaman_publik/2_tampilan_portofolio.php"; exit;
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
    $zip = new ZipArchive(); $zname = "Folder_Export_" . time() . ".zip"; $zpath = "08_unggahan/" . $zname;
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

// =========================================
