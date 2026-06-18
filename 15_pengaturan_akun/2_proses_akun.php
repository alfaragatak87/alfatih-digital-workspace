<?php
// +------------------------------------------------------------------------------+
// |  FILE: 15_pengaturan_akun/2_proses_akun.php                                  |
// |  DESKRIPSI: Memproses aksi pengubahan profil dan password.                   |
// +------------------------------------------------------------------------------+
if (!defined('SITE_URL')) exit;

$action = $_POST['action'] ?? ($_GET['action'] ?? '');

// AKSI 1: UPDATE PROFIL AKUN
if ($action === 'update_pengaturan_akun' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF Token tidak valid!");
    }

    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $nama_panggilan = trim($_POST['nama_panggilan'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $profesi = trim($_POST['profesi'] ?? '');
    
    // Check Email unique (if changed)
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE email=? AND username!=?");
    $stmt->bind_param('ss', $email, $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['flash_error'] = "Email sudah digunakan oleh akun lain.";
        header("Location: index.php?page=pengaturan-akun");
        exit;
    }
    $stmt->close();

    // Update query
    $stmt = $mysqli->prepare("UPDATE users SET nama_lengkap=?, nama_panggilan=?, email=?, profesi=? WHERE username=?");
    $stmt->bind_param('sssss', $nama_lengkap, $nama_panggilan, $email, $profesi, $username);
    if ($stmt->execute()) {
        $_SESSION['nama_lengkap'] = $nama_lengkap; // Perbarui sesi
        $_SESSION['flash_success'] = "Profil akun berhasil diperbarui!";
    } else {
        $_SESSION['flash_error'] = "Gagal memperbarui profil: " . $mysqli->error;
    }
    $stmt->close();
    header("Location: index.php?page=pengaturan-akun");
    exit;
}

// AKSI 2: UPDATE PASSWORD
if ($action === 'update_password_akun' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF Token tidak valid!");
    }

    $pw_lama = $_POST['password_lama'] ?? '';
    $pw_baru = $_POST['password_baru'] ?? '';
    $pw_konfirm = $_POST['password_konfirmasi'] ?? '';

    // Cek password lama
    $stmt = $mysqli->prepare("SELECT password FROM users WHERE username=?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!password_verify($pw_lama, $res['password'])) {
        $_SESSION['flash_error'] = "Password saat ini salah!";
    } elseif ($pw_baru !== $pw_konfirm) {
        $_SESSION['flash_error'] = "Konfirmasi password baru tidak cocok!";
    } elseif (strlen($pw_baru) < 6) {
        $_SESSION['flash_error'] = "Password baru minimal 6 karakter!";
    } else {
        $hash_baru = password_hash($pw_baru, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("UPDATE users SET password=? WHERE username=?");
        $stmt->bind_param('ss', $hash_baru, $username);
        if ($stmt->execute()) {
            $_SESSION['flash_success'] = "Password berhasil diperbarui!";
        } else {
            $_SESSION['flash_error'] = "Terjadi kesalahan server.";
        }
        $stmt->close();
    }
    header("Location: index.php?page=pengaturan-akun");
    exit;
}

?>
