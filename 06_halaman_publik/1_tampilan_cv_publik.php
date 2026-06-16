<?php
// +------------------------------------------------------------------------------+
// |  FILE: 1_tampilan_cv_publik.php                                              |
// |                                                                              |
// |  DESKRIPSI:                                                                  |
// |  File ini berfungsi sebagai pintu gerbang untuk pengunjung luar (publik)     |
// |  yang belum login. File ini bertugas untuk menyiapkan data profil pengguna   |
// |  (Talent) yang mengatur visibilitasnya menjadi "Tampil Publik", dan          |
// |  merender seluruh tampilan antarmuka (Landing Page & Portofolio).            |
// |                                                                              |
// |  KONEKSI & RELASI:                                                           |
// |  - Dipanggil oleh index.php jika session 'username' kosong (belum login).    |
// |  - Menghasilkan variabel $talent_users untuk ditampilkan di Landing Page.    |
// +------------------------------------------------------------------------------+

// Mengecek apakah pengunjung saat ini tidak memiliki sesi login (belum masuk)
if (empty($_SESSION['username'])) {
    
    // Mengambil parameter halaman dari URL (misal: ?page=login), jika kosong defaultnya 'hub'
    $pub_page = $_GET['page'] ?? 'hub';
    
    // Memanggil fungsi utama untuk menggambar/merender halaman HTML publik
    renderPublicPage($pub_page, $error_msg, $mysqli);
    
    // Menghentikan eksekusi kode di bawahnya agar tidak memuat dasbor internal
    exit;
}

// ══════════════════════════════════════════════════════════════════════════════
// FUNGSI PENAMPIL HALAMAN PUBLIK (LANDING PAGE & LOGIN)
// ══════════════════════════════════════════════════════════════════════════════

// Deklarasi fungsi perender halaman publik (menerima 3 parameter: nama halaman, pesan error, koneksi DB)
function renderPublicPage(string $pub_page, string $error_msg, mysqli $db): void {
    
    // Menyiapkan wadah kosong untuk menyimpan daftar pengguna yang ingin profilnya tampil di publik
    $talent_users = [];
    
    // Mengambil data username, nama, foto, dan CV dari semua pengguna yang memiliki hak akses
    $res = $db->query("SELECT username, nama_lengkap, foto_profil, profile_data FROM users WHERE role IN ('superadmin','admin','user') ORDER BY nama_lengkap ASC");
    
    // Mengulang setiap pengguna yang ditemukan di database
    while ($tu = $res->fetch_assoc()) {
        
        // Membaca data CV/Profil mereka yang berformat JSON menjadi struktur array PHP
        $tpd = json_decode($tu['profile_data'] ?? '{}', true) ?? [];
        
        // Mengecek apakah di dalam pengaturan CV mereka, opsi "Tampil Publik" diaktifkan
        if (!empty($tpd['identitas']['tampil_publik'])) { 
            
            // Masukkan data CV tersebut ke dalam array sementara pengguna ini
            $tu['_pd'] = $tpd; 
            
            // Tambahkan pengguna ini ke dalam daftar bakat (talent) yang akan ditampilkan
            $talent_users[] = $tu; 
        }
    }

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pub_page==='login' ? 'Login â Alfatih Workspace' : 'Alfatih Digital Workspace' ?></title>
    <meta name="theme-color" content="#080b14">
    <meta name="description" content="Alfatih Digital Workspace â Premium CMS, File Manager & Portfolio Builder.">
    <link rel="manifest" href="manifest.json">
    <link rel="icon" type="image/svg+xml" href="07_aset_visual/images/LOGO_GAWE.svg">
    <link rel="apple-touch-icon" href="07_aset_visual/images/LOGO_GAWE.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
   GAWE.MY.ID â PUBLIC PORTAL v3  |  Dark SaaS Premium
   Palette: Midnight + Electric Indigo + Violet + Cyan
   âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ */
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
