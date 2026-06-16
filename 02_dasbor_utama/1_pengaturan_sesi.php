<?php
// +------------------------------------------------------------------------------+
// |  FILE: 1_pengaturan_sesi.php                                                 |
// |                                                                              |
// |  DESKRIPSI:                                                                  |
// |  File ini bertugas mengonfigurasi data pengguna yang sedang login.           |
// |  Mengambil data profil dari database (nama, foto profil), menyiapkan         |
// |  statistik penyimpanan file (berapa MB yang digunakan), dan mengambil        |
// |  daftar seluruh pengguna jika yang login adalah Super Admin.                 |
// |                                                                              |
// |  KONEKSI & RELASI:                                                           |
// |  - Dipanggil oleh index.php setelah pengguna berhasil login.                 |
// |  - Mensuplai data statistik untuk ditampilkan di dasbor utama.               |
// +------------------------------------------------------------------------------+

// Mengambil username dari memori sesi pengguna yang sedang login
$username    = $_SESSION['username'];

// Mengambil hak akses (role) pengguna dari sesi
$role        = $_SESSION['role'];

// Mengambil ID unik pengguna dari sesi (diubah menjadi tipe integer)
$uid         = (int)($_SESSION['uid'] ?? 0);

// Menyiapkan variabel kosong untuk menampung pesan notifikasi
$alert_msg   = '';

// Menyiapkan perintah SQL untuk mengambil seluruh data pengguna berdasarkan username
$stmt = $mysqli->prepare("SELECT * FROM users WHERE username=? LIMIT 1");

// Menggabungkan variabel username ke dalam perintah SQL
$stmt->bind_param('s', $username); 

// Menjalankan perintah SQL
$stmt->execute(); 

// Mengambil hasil baris data pengguna
$data_user = $stmt->get_result()->fetch_assoc(); 

// Menutup perintah SQL
$stmt->close();

// Menentukan nama lengkap yang akan ditampilkan (jika kosong, gunakan username)
$nama_lengkap = $data_user['nama_lengkap'] ?? $username;

// Mengambil nama file foto profil dari database
$foto_profil  = $data_user['foto_profil'] ?? '';

// Menentukan jalur (URL/Path) foto profil yang valid
$path_foto    = ($foto_profil && $foto_profil !== 'default.png' && file_exists(PROFILE_IMG_DIR . $foto_profil))
    ? PROFILE_IMG_DIR . $foto_profil // Jika file foto asli ada di server, gunakan itu
    : 'https://ui-avatars.com/api/?name=' . urlencode($nama_lengkap) . '&background=1a1a1a&color=ffffff&bold=true'; // Jika tidak, hasilkan avatar inisial otomatis

// Mengambil halaman yang sedang diakses saat ini dari URL (default: beranda)
$current_page = $_GET['page'] ?? 'beranda';

// Membuat token keamanan (CSRF) untuk mencegah serangan manipulasi form
$csrf_token   = generateCSRF();

// Menyiapkan variabel penghitung statistik ke angka 0
$stat_files = 0; $stat_links = 0; $stat_size = 0; $stat_folders = 0;

// Menyiapkan SQL untuk mengambil semua file dan tautan milik pengguna yang belum dihapus permanen
$stmt = $mysqli->prepare("SELECT jenis, file_path FROM files WHERE owner_username=? AND is_deleted=0");

// Mengeksekusi pencarian file milik pengguna ini
$stmt->bind_param('s', $username); $stmt->execute(); $res = $stmt->get_result();

// Mengulang setiap file/tautan yang ditemukan
while ($r = $res->fetch_assoc()) {
    // Jika jenisnya adalah 'file' (bukan tautan)
    if ($r['jenis'] === 'file') { 
        $stat_files++; // Tambahkan jumlah file
        $fp = UPLOAD_DIR . $r['file_path']; // Tentukan lokasi file fisik
        if (file_exists($fp)) {
            $stat_size += filesize($fp); // Jika file fisiknya ada, tambahkan ukuran byte-nya
        }
    } else {
        // Jika jenisnya tautan (link), tambahkan jumlah tautan
        $stat_links++;
    }
}
$stmt->close(); // Tutup koneksi pencarian file

// Menyiapkan SQL untuk menghitung jumlah folder milik pengguna yang belum dihapus
$stmt = $mysqli->prepare("SELECT COUNT(*) FROM folders WHERE owner_username=? AND is_deleted=0");
$stmt->bind_param('s', $username); $stmt->execute(); 
$stat_folders = $stmt->get_result()->fetch_row()[0]; // Mengambil angka hasil hitungan
$stmt->close(); // Tutup pencarian folder

// Mengubah total ukuran byte menjadi format yang mudah dibaca (KB/MB/GB)
$size_used = formatBytes($stat_size); 

// Menentukan batas maksimal penyimpanan (Kapasitas: 1 GB = 1073741824 Bytes)
$storage_limit = 1073741824;

// Menghitung persentase penggunaan penyimpanan (Maksimal 100%)
$storage_pct = min(100, round(($stat_size / $storage_limit) * 100, 1));

// Menyiapkan wadah kosong untuk daftar semua pengguna
$all_users = [];

// Jika pengguna yang login ini adalah Admin atau Super Admin
if (isAdmin()) {
    // Ambil data penting dari seluruh pengguna yang terdaftar di database
    $res = $mysqli->query("SELECT id, username, nama_lengkap, role, foto_profil FROM users ORDER BY username ASC");
    
    // Masukkan setiap data pengguna ke dalam wadah $all_users
    while ($u = $res->fetch_assoc()) {
        $all_users[] = $u;
    }
}
