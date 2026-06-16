<?php
// +------------------------------------------------------------------------------+
// |  FILE: 1_koneksi_database.php                                                |
// |                                                                              |
// |  DESKRIPSI:                                                                  |
// |  File ini adalah pondasi paling dasar dari sistem Alfatih Workspace.         |
// |  Berfungsi untuk mengatur koneksi ke database MySQL, menginisiasi sesi       |
// |  pengguna, mendefinisikan tautan utama (URL), dan secara otomatis            |
// |  melakukan migrasi (pembuatan tabel/kolom) jika belum tersedia.              |
// |                                                                              |
// |  KONEKSI & RELASI:                                                           |
// |  - Dipanggil pertama kali oleh index.php pada baris paling atas.             |
// |  - Mensuplai variabel global ($mysqli) ke seluruh fungsi lain.               |
// |                                                                              |
// |  BARIS KODE PENTING:                                                         |
// |  - define('DB_HOST', ...): Baris 20-23 (Kredensial Login Database).          |
// |  - $mysqli = new mysqli(): Baris 33 (Eksekusi Koneksi ke Server).            |
// |  - $migrations: Baris 40+ (Penambahan kolom otomatis ke database).           |
// +------------------------------------------------------------------------------+

// Mendefinisikan Konstanta untuk URL Server Database (biasanya 'localhost')
define('DB_HOST', 'localhost');

// Mendefinisikan Konstanta untuk Username Database di cPanel/Server
define('DB_USER', 'mckmmukg_alfa');

// Mendefinisikan Konstanta untuk Kata Sandi Database
define('DB_PASS', 'Alfaragatak87');

// Mendefinisikan Konstanta untuk Nama Database yang digunakan
define('DB_NAME', 'mckmmukg_utama');

// Mendefinisikan Konstanta URL Utama Website agar mudah dipanggil di halaman lain
define('SITE_URL', 'https://gawe.my.id');

// Mendefinisikan Konstanta untuk lokasi folder tempat penyimpanan file pengguna
define('UPLOAD_DIR', '08_unggahan/files/');

// Mendefinisikan Konstanta untuk lokasi folder tempat penyimpanan foto profil
define('PROFILE_IMG_DIR', '08_unggahan/');

// Mematikan laporan error bawaan MySQLi agar pesan error bisa kita buat sendiri
mysqli_report(MYSQLI_REPORT_OFF);

// Mengecek apakah sesi login belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    // Memulai sesi untuk mengingat pengguna yang login
    session_start();
}

// Mencoba melakukan koneksi ke Database menggunakan sistem MySQLi OOP
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Mengecek apakah terjadi kegagalan/error saat mencoba terhubung ke database
if ($mysqli->connect_errno) { 
    // Menghentikan seluruh proses web dan menampilkan pesan error merah
    die("DB Error: " . $mysqli->connect_error); 
}

// Menyetel format karakter ke UTF-8 agar mendukung emoji dan teks multi-bahasa
$mysqli->set_charset('utf8mb4');

// Daftar perintah SQL (Kueri) untuk memperbarui atau menambah kolom otomatis di tabel
$migrations = [
    // Mengubah pengaturan kolom 'role' agar memiliki 3 hak akses
    "ALTER TABLE `users` MODIFY `role` ENUM('superadmin','admin','user') NOT NULL DEFAULT 'user'",
    // Menambahkan kolom email untuk pengguna
    "ALTER TABLE `users` ADD COLUMN `email` VARCHAR(100) DEFAULT NULL",
    // Menambahkan kolom nomor HP
    "ALTER TABLE `users` ADD COLUMN `phone` VARCHAR(30) DEFAULT NULL",
    // Menambahkan kolom penyimpanan Data CV dalam bentuk teks JSON panjang
    "ALTER TABLE `users` ADD COLUMN `profile_data` LONGTEXT DEFAULT NULL",
    // Menambahkan kolom untuk mencatat kapan terakhir kali pengguna login
    "ALTER TABLE `users` ADD COLUMN `last_login` TIMESTAMP NULL DEFAULT NULL",
    // Menambahkan kolom untuk mencatat kapan akun ini pertama kali dibuat
    "ALTER TABLE `users` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
    // Menambahkan kolom pencatat riwayat (Log) agar tahu aksi dari ID pengguna mana
    "ALTER TABLE `admin_logs` ADD COLUMN `user_id` INT(11) DEFAULT NULL",
];

// Melakukan perulangan untuk mengeksekusi setiap perintah SQL di atas
foreach ($migrations as $sql) { 
    // Tanda '@' digunakan agar jika kolom sudah ada, tidak akan memunculkan pesan error
    @$mysqli->query($sql); 
}

// Menetapkan akun dengan username 'alfa' menjadi penguasa tertinggi (Superadmin)
$mysqli->query("UPDATE `users` SET `role` = 'superadmin' WHERE `username` = 'alfa'");

// Menetapkan akun selain 'alfa' yang tadinya memiliki role bapak/ajay menjadi Admin biasa
$mysqli->query("UPDATE `users` SET `role` = 'admin' WHERE `username` != 'alfa' AND (`role` = 'bapak' OR `role` = 'ajay' OR `role` = 'user')");
