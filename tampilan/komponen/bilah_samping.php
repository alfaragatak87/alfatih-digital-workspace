<?php
// +------------------------------------------------------------------------------+
// ¦  FILE: tampilan/komponen/bilah_samping.php                                   ¦
// ¦                                                                              ¦
// ¦  DESKRIPSI:                                                                  ¦
// ¦  Komponen antarmuka (UI Component) untuk navigasi vertikal di sisi kiri      ¦
// ¦  dasbor. Berisi logo aplikasi dan tautan utama ke menu navigasi.             ¦
// ¦                                                                              ¦
// ¦  KONEKSI & RELASI:                                                           ¦
// ¦  - Di-include di seluruh halaman dalam Dasbor (kecuali landing page).        ¦
// ¦                                                                              ¦
// ¦  BARIS KODE PENTING:                                                         ¦
// ¦  - Tag <a> Navigasi : Menggunakan logika class ctive secara dinamis     ¦
// ¦    berdasarkan variabel $current_page agar menu yang diklik menyala.       ¦
// +------------------------------------------------------------------------------+
?>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
<div class="sidebar" id="sidebar">
    <div class="sidebar-section">
        <div class="sidebar-section-label">Main</div>
        <a href="index.php?page=beranda" class="nav-item <?= ($current_page ?? '')==='beranda'?'active':'' ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="index.php?page=workspace" class="nav-item <?= ($current_page ?? '')==='workspace'?'active':'' ?>"><i class="fa-solid fa-folder-open"></i> Workspace</a>
        <a href="index.php?page=workspace&view=recent" class="nav-item"><i class="fa-solid fa-clock-rotate-left"></i> Akses Terbaru</a>
        <a href="index.php?page=workspace&view=assets" class="nav-item"><i class="fa-solid fa-images"></i> Aset Visual</a>
        <a href="index.php?page=workspace&view=stats" class="nav-item"><i class="fa-solid fa-chart-bar"></i> Statistik</a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-section-label">Profil</div>
        <a href="index.php?page=profile" class="nav-item <?= ($current_page ?? '')==='profile'?'active':'' ?>"><i class="fa-solid fa-id-card"></i> CV Builder</a>
        <a href="<?= h($portfolio_url ?? '#') ?>" target="_blank" class="nav-item"><i class="fa-solid fa-globe"></i> Lihat Portfolio</a>
        <a href="index.php" target="_blank" class="nav-item"><i class="fa-solid fa-users"></i> Direktori Talent</a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-section-label">Lainnya</div>
        <a href="index.php?page=workspace&view=trash" class="nav-item"><i class="fa-solid fa-trash-can"></i> Tong Sampah</a>
    </div>
    <?php if(isSuperAdmin()){?>
    <div class="sidebar-section">
        <div class="sidebar-section-label" style="color:var(--superadmin);">God Mode</div>
        <a href="index.php?page=manajemen-pengguna" class="nav-item superadmin-item <?= ($current_page ?? '')==='manajemen-pengguna'?'active':'' ?>"><i class="fa-solid fa-users-gear"></i> Manajemen User</a>
    </div>
    <?php }?>
    <div class="sidebar-storage">
        <div class="storage-label">Penyimpanan</div>
        <div class="storage-bar"><div class="storage-bar-fill" style="width:<?= $storage_pct ?? 0 ?>%;"></div></div>
        <div class="storage-text"><?= $size_used ?? '0 B' ?> dari 1 GB</div>
    </div>
</div>