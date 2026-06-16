<?php
// +------------------------------------------------------------------------------+
// |  FILE: 2_pengaturan_workspace.php                                            |
// |                                                                              |
// |  DESKRIPSI:                                                                  |
// |  File ini menangani konfigurasi tata letak dan pengurutan data di halaman    |
// |  Dasbor (Workspace). Berfungsi mengatur sistem pencarian file, filter        |
// |  penyortiran (berdasarkan nama atau tanggal), pembuatan navigasi direktori   |
// |  (breadcrumbs), dan memuat daftar riwayat aktivitas terakhir pengguna.       |
// |                                                                              |
// |  KONEKSI & RELASI:                                                           |
// |  - Menerima parameter GET dari URL (?q=, ?sort=, ?folder_id=).               |
// |  - Menghasilkan variabel-variabel yang digunakan oleh tampilan HTML Dasbor.  |
// +------------------------------------------------------------------------------+

// Mengambil kata kunci pencarian dari URL jika ada, dan menghapus spasi ekstra
$search_query = trim($_GET['q'] ?? ''); 

// Menentukan mode tampilan (grid/list/home) dari URL, defaultnya adalah 'home'
$current_view = $_GET['view'] ?? 'home';

// Menentukan ID folder yang sedang dibuka, jika tidak ada berarti di direktori utama (null)
$active_folder = isset($_GET['folder_id']) ? (int)$_GET['folder_id'] : null;

// Filter khusus admin untuk melihat file pengguna lain (secara bawaan melihat file sendiri)
$admin_filter = $_GET['filter'] ?? $username; 

// Mengambil parameter penyortiran dari URL, defaultnya adalah urut berdasarkan nama A-Z
$sort = $_GET['sort'] ?? 'nama_asc';

// Variabel bawaan untuk aturan penyortiran folder (A-Z)
$order_f = 'nama_folder ASC'; 

// Variabel bawaan untuk aturan penyortiran file (A-Z)
$order_i = 'nama_file ASC';

// Mengubah aturan penyortiran jika pengguna memilih opsi lain
if ($sort === 'nama_desc') { 
    // Urutkan berdasarkan nama (Z-A)
    $order_f = 'nama_folder DESC'; 
    $order_i = 'nama_file DESC'; 
} elseif ($sort === 'date_asc') { 
    // Urutkan berdasarkan tanggal terlama
    $order_f = 'id ASC'; 
    $order_i = 'tanggal_upload ASC'; 
} elseif ($sort === 'date_desc') { 
    // Urutkan berdasarkan tanggal terbaru
    $order_f = 'id DESC'; 
    $order_i = 'tanggal_upload DESC'; 
}

// Menyiapkan wadah kosong untuk navigasi jejak folder (Breadcrumbs)
$breadcrumbs = [];

// Jika pengguna sedang berada di dalam sebuah folder
if ($active_folder) {
    // Tentukan folder saat ini
    $curr = $active_folder; 
    
    // Simpan daftar folder yang sudah dikunjungi untuk mencegah siklus tanpa henti (infinite loop)
    $visited = [];
    
    // Terus mencari folder induk (parent) ke atas sampai mencapai direktori utama
    while ($curr !== null) {
        // Jika folder ini sudah pernah dicek, hentikan (mencegah folder yang saling memasukkan)
        if (in_array($curr, $visited)) break; 
        
        // Catat folder ini sebagai sudah dikunjungi
        $visited[] = $curr;
        
        // Menyiapkan SQL untuk mengambil informasi folder ini dan ID folder induknya
        $stmt = $mysqli->prepare("SELECT id, nama_folder, parent_id FROM folders WHERE id=?");
        $stmt->bind_param('i', $curr); 
        $stmt->execute(); 
        
        // Mengambil hasil informasi folder
        $rb = $stmt->get_result()->fetch_assoc(); 
        $stmt->close();
        
        // Jika folder tidak ditemukan di database, hentikan pencarian
        if (!$rb) break;
        
        // Masukkan nama folder ini ke urutan paling depan di navigasi breadcrumb
        array_unshift($breadcrumbs, $rb); 
        
        // Lanjutkan pencarian ke folder induknya (naik satu tingkat)
        $curr = $rb['parent_id'];
    }
}

// Menyiapkan wadah kosong untuk daftar semua folder (biasanya digunakan untuk menu "Pindahkan File")
$all_folders_list = [];

// Jika pengguna adalah Admin, ambil semua folder dari semua orang
if (isAdmin()) {
    $res = $mysqli->query("SELECT id, nama_folder, owner_username FROM folders WHERE is_deleted=0 ORDER BY nama_folder ASC");
    while ($af = $res->fetch_assoc()) {
        $all_folders_list[] = $af;
    }
} else {
    // Jika pengguna biasa, ambil hanya folder milik sendiri
    $stmt = $mysqli->prepare("SELECT id, nama_folder, owner_username FROM folders WHERE is_deleted=0 AND owner_username=? ORDER BY nama_folder ASC");
    $stmt->bind_param('s', $username); 
    $stmt->execute(); 
    $res = $stmt->get_result();
    while ($af = $res->fetch_assoc()) {
        $all_folders_list[] = $af; 
    }
    $stmt->close();
}

// Menyiapkan wadah kosong untuk riwayat aktivitas terakhir (Log)
$recent_activity = [];

// Mengambil 10 aktivitas terakhir dari pengguna ini di database log
$stmt = $mysqli->prepare("SELECT action, details, created_at, ip_address FROM admin_logs WHERE user_id=? ORDER BY created_at DESC LIMIT 10");
$stmt->bind_param('i', $uid); 
$stmt->execute(); 
$res = $stmt->get_result();

// Memasukkan setiap aktivitas yang ditemukan ke dalam wadah $recent_activity
while ($ra = $res->fetch_assoc()) {
    $recent_activity[] = $ra; 
}
$stmt->close();

// Membaca data CV (JSON) milik pengguna dan mengubahnya menjadi bentuk array PHP
$profile_data  = json_decode($data_user['profile_data'] ?? '{}', true) ?? [];

// Membentuk Tautan (URL) menuju halaman Portofolio/CV Publik pengguna ini
$portfolio_url = SITE_URL . '/index.php?portfolio=' . urlencode($username);

// Menentukan nama panggilan yang akan ditampilkan di profil Dasbor
// Jika di dalam CV ada 'nama_sebutan', gunakan itu. Jika tidak, gunakan kata pertama dari nama lengkap.
$display_name  = !empty($profile_data['identitas']['nama_sebutan'])
    ? $profile_data['identitas']['nama_sebutan']
    : explode(' ', $nama_lengkap)[0];

// FUNGSI PENAMPIL HALAMAN PUBLIK (LANDING PAGE & LOGIN)
// â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â•
