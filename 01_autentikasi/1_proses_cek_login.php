<?php
// +------------------------------------------------------------------------------+
// |  FILE: 1_proses_cek_login.php                                                |
// |                                                                              |
// |  DESKRIPSI:                                                                  |
// |  File ini bertanggung jawab penuh untuk menangani proses autentikasi.        |
// |  Tugas utamanya adalah memverifikasi username dan kata sandi yang dikirim    |
// |  melalui form login, mengatur variabel sesi (session) jika login berhasil,   |
// |  serta mencatat aktivitas login ke dalam log keamanan.                       |
// |                                                                              |
// |  KONEKSI & RELASI:                                                           |
// |  - Menerima data $_POST dari form login di index.php.                        |
// |  - Menggunakan $mysqli dari 1_koneksi_database.php untuk query ke tabel      |
// |    'users'.                                                                  |
// +------------------------------------------------------------------------------+

// Menyiapkan variabel kosong untuk menampung pesan error (jika login gagal)
$error_msg = '';

// Mengecek apakah ada data yang dikirim dengan metode POST dan aksinya adalah 'login'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    
    // Mengambil dan membersihkan spasi berlebih pada username yang dimasukkan
    $uname = trim($_POST['username'] ?? ''); 
    
    // Mengambil kata sandi yang dimasukkan oleh pengguna
    $upass = $_POST['password'] ?? '';

    // Menyiapkan perintah SQL (Prepared Statement) untuk mencari user yang aktif
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username=? AND status!='inactive' LIMIT 1");
    
    // Jika query pertama gagal (mungkin kolom status belum ada), gunakan query alternatif
    if (!$stmt) {
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    }

    // Menggabungkan variabel username ke dalam perintah SQL (mencegah SQL Injection)
    $stmt->bind_param('s', $uname); 
    
    // Menjalankan perintah SQL ke database
    $stmt->execute(); 
    
    // Mengambil satu baris data pengguna dari hasil query
    $row = $stmt->get_result()->fetch_assoc(); 
    
    // Menutup perintah SQL untuk menghemat memori
    $stmt->close();

    // Mengecek apakah data pengguna ditemukan dan kata sandinya cocok dengan database
    if ($row && password_verify($upass, $row['password'])) {
        
        // Menyimpan username ke dalam memori sesi (tanda bahwa pengguna sudah login)
        $_SESSION['username'] = $row['username']; 
        
        // Menyimpan hak akses (role) pengguna (superadmin/admin/user) ke sesi
        $_SESSION['role'] = $row['role'];
        
        // Menyimpan ID unik pengguna ke sesi
        $_SESSION['uid'] = $row['id']; 
        
        // Menyimpan nama lengkap pengguna (jika kosong, gunakan username)
        $_SESSION['nama'] = $row['nama_lengkap'] ?? $row['username'];

        // Menyiapkan perintah SQL untuk memperbarui kolom 'last_login' dengan waktu saat ini
        $stmt2 = $mysqli->prepare("UPDATE users SET last_login=NOW() WHERE id=?");
        
        // Memasukkan ID pengguna ke dalam perintah SQL
        $stmt2->bind_param('i', $row['id']); 
        
        // Mengeksekusi perintah update waktu login
        $stmt2->execute(); 
        
        // Menutup perintah SQL
        $stmt2->close();

        // Memanggil fungsi pencatat riwayat (log) bahwa pengguna ini baru saja login beserta IP-nya
        logActivity($mysqli, $row['id'], 'LOGIN', 'User logged in from IP: ' . ($_SERVER['REMOTE_ADDR'] ?? ''));
        
        // Mengalihkan halaman (Redirect) ke halaman beranda dasbor setelah berhasil login
        header("Location: index.php?page=beranda"); 
        
        // Menghentikan proses eksekusi kode di bawahnya agar langsung beralih halaman
        exit;
    } else { 
        // Jika username atau password tidak cocok, isi variabel dengan pesan error
        $error_msg = "Username atau password salah."; 
    }
}
