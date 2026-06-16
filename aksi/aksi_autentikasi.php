<?php

// +------------------------------------------------------------------------------+
// �  FILE: aksi/aksi_autentikasi.php                                             �
// �                                                                              �
// �  DESKRIPSI:                                                                  �
// �  File ini menangani proses validasi khusus atau perluasan metode login/logout�
// �  (contohnya: Two Factor Auth, JWT generator) yang dapat dikembangkan nanti.  �
// �                                                                              �
// �  KONEKSI & RELASI:                                                           �
// �  - Berkaitan dengan sistem Sesi (Session) di index.php.                    �
// �                                                                              �
// �  BARIS KODE PENTING:                                                         �
// �  - Verifikasi Kredensial Lanjut : Fungsi-fungsi pendukung *Single Sign On*   �
// �    bila diaktifkan di masa mendatang.                                        �
// +------------------------------------------------------------------------------+
// ============================================================
// ALFATIH DIGITAL WORKSPACE — aksi/aksi_autentikasi.php
// Fokus: Logika autentikasi — form login & logout.
// Dipanggil oleh: index.php (sebelum cek session)
// Requires: config/database.php, core/auth.php
// ============================================================

// ── LOGOUT ───────────────────────────────────────────────────
// Menangani ?logout dari URL manapun.
// Session dihancurkan total, lalu redirect ke halaman publik.
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// ── LOGIN (POST Handler) ──────────────────────────────────────
// Dipicu oleh form login dengan action='login'.
// Variabel $error_msg diteruskan ke renderPublicPage() jika gagal.
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {

    $uname = trim($_POST['username'] ?? '');
    $upass = $_POST['password'] ?? '';

    // Coba query dengan filter status aktif; fallback jika kolom status belum ada.
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username=? AND status!='inactive' LIMIT 1");
    if (!$stmt) {
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    }
    $stmt->bind_param('s', $uname);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row && password_verify($upass, $row['password'])) {
        // Login berhasil — isi session
        $_SESSION['username'] = $row['username'];
        $_SESSION['role']     = $row['role'];
        $_SESSION['uid']      = $row['id'];
        $_SESSION['nama']     = $row['nama_lengkap'] ?? $row['username'];

        // Perbarui timestamp login terakhir
        $stmt2 = $mysqli->prepare("UPDATE users SET last_login=NOW() WHERE id=?");
        $stmt2->bind_param('i', $row['id']);
        $stmt2->execute();
        $stmt2->close();

        // Catat aktivitas login ke log
        logActivity(
            $mysqli,
            $row['id'],
            'LOGIN',
            'User logged in from IP: ' . ($_SERVER['REMOTE_ADDR'] ?? '')
        );

        header("Location: index.php?page=beranda");
        exit;

    } else {
        // Login gagal — pesan error akan ditampilkan di form publik
        $error_msg = "Username atau password salah.";
    }
}
