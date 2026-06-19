<?php
if (!defined('SITE_URL') && !isset($mysqli)) exit; // Proteksi

// Script ini dijalankan satu kali untuk memperbarui struktur database

try {
    // 1. Tambah kolom profesi ke tabel users jika belum ada
    $checkProfesi = $mysqli->query("SHOW COLUMNS FROM users LIKE 'profesi'");
    if ($checkProfesi && $checkProfesi->num_rows == 0) {
        $mysqli->query("ALTER TABLE users ADD COLUMN profesi VARCHAR(100) DEFAULT NULL");
    }

    // 2. Tambah kolom nama_panggilan jika belum ada
    $checkNama = $mysqli->query("SHOW COLUMNS FROM users LIKE 'nama_panggilan'");
    if ($checkNama && $checkNama->num_rows == 0) {
        $mysqli->query("ALTER TABLE users ADD COLUMN nama_panggilan VARCHAR(50) DEFAULT NULL");
    }

    // 3. Tambah kolom role jika belum ada
    $checkRole = $mysqli->query("SHOW COLUMNS FROM users LIKE 'role'");
    if ($checkRole && $checkRole->num_rows == 0) {
        $mysqli->query("ALTER TABLE users ADD COLUMN role ENUM('user', 'admin', 'superadmin') DEFAULT 'user'");
    }

    // Tetapkan s.s.6624844@gmail.com sebagai superadmin
    $mysqli->query("UPDATE users SET role='superadmin', profesi='Super Administrator' WHERE email='s.s.6624844@gmail.com'");
    
    // Buat atau perbarui user 'alfa' dengan password 'password'
    $hash_alfa = password_hash('password', PASSWORD_DEFAULT);
    $resAlfa = $mysqli->query("SELECT id FROM users WHERE username='alfa'");
    if ($resAlfa && $resAlfa->num_rows == 0) {
        $mysqli->query("INSERT INTO users (username, password, nama_lengkap, role, profesi) VALUES ('alfa', '$hash_alfa', 'Alfa Admin', 'superadmin', 'Super Administrator')");
    } else {
        $mysqli->query("UPDATE users SET password='$hash_alfa', role='superadmin', profesi='Super Administrator' WHERE username='alfa'");
    }

    // 2. Buat tabel penduduk (untuk Pegawai Balai Desa)
    $mysqli->query("CREATE TABLE IF NOT EXISTS penduduk (
        id INT AUTO_INCREMENT PRIMARY KEY,
        owner_username VARCHAR(100) NOT NULL,
        nik VARCHAR(20) NOT NULL,
        nama VARCHAR(150) NOT NULL,
        tempat_lahir VARCHAR(100),
        tanggal_lahir DATE,
        jenis_kelamin ENUM('Laki-Laki', 'Perempuan'),
        alamat TEXT,
        rt_rw VARCHAR(20),
        kel_desa VARCHAR(100),
        kecamatan VARCHAR(100),
        agama VARCHAR(50),
        status_perkawinan VARCHAR(50),
        pekerjaan VARCHAR(100),
        kewarganegaraan VARCHAR(50) DEFAULT 'WNI',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // 3. Buat tabel siswa/tugas (untuk Guru)
    $mysqli->query("CREATE TABLE IF NOT EXISTS data_siswa (
        id INT AUTO_INCREMENT PRIMARY KEY,
        owner_username VARCHAR(100) NOT NULL,
        nis VARCHAR(50),
        nama_siswa VARCHAR(150),
        kelas VARCHAR(50),
        nilai INT DEFAULT 0,
        catatan TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 4. Buat tabel proyek_klien (untuk Pekerja Lepas)
    $mysqli->query("CREATE TABLE IF NOT EXISTS proyek_klien (
        id INT AUTO_INCREMENT PRIMARY KEY,
        owner_username VARCHAR(50) NOT NULL,
        nama_klien VARCHAR(255) NOT NULL,
        nama_proyek VARCHAR(255) NOT NULL,
        status ENUM('Sedang Berjalan', 'Selesai', 'Batal') DEFAULT 'Sedang Berjalan',
        tenggat_waktu DATE,
        nilai_proyek VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 5. Buat tabel todo_list (untuk Produktivitas Umum)
    $mysqli->query("CREATE TABLE IF NOT EXISTS todo_list (
        id INT AUTO_INCREMENT PRIMARY KEY,
        owner_username VARCHAR(50) NOT NULL,
        tugas VARCHAR(255) NOT NULL,
        status ENUM('Belum Selesai', 'Selesai') DEFAULT 'Belum Selesai',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 6. Buat tabel keuangan (untuk Manajemen Bisnis)
    $mysqli->query("CREATE TABLE IF NOT EXISTS keuangan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        owner_username VARCHAR(50) NOT NULL,
        tipe ENUM('Pemasukan', 'Pengeluaran') NOT NULL,
        nominal DECIMAL(15,2) NOT NULL,
        keterangan VARCHAR(255),
        tanggal DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 7. Buat tabel inventaris (untuk Manajemen Bisnis)
    $mysqli->query("CREATE TABLE IF NOT EXISTS inventaris (
        id INT AUTO_INCREMENT PRIMARY KEY,
        owner_username VARCHAR(50) NOT NULL,
        nama_barang VARCHAR(255) NOT NULL,
        jumlah_stok INT NOT NULL DEFAULT 0,
        tipe_pergerakan ENUM('Masuk', 'Keluar', 'Stok Awal') DEFAULT 'Stok Awal',
        keterangan VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

} catch (Exception $e) {
    // Abaikan jika error (misal tabel sudah ada)
}
?>
