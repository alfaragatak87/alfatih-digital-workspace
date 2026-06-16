import os

filepath = r"c:\hosting\index.php"
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Wrap CSS in <?php if($current_page === 'workspace'): ?>
css_target = """/* ══ GOOGLE DRIVE LAYOUT (MATERIAL 3 LIGHT) ══ */"""
css_replace = """<?php if($current_page === 'workspace'): ?>
/* ══ GOOGLE DRIVE LAYOUT (MATERIAL 3 LIGHT) ══ */"""

if css_target in content:
    content = content.replace(css_target, css_replace)
    
css_end_target = """/* List View Overrides */
.view-list .drive-grid-folders, .view-list .drive-grid-files { display: flex; flex-direction: column; gap: 0; }
.view-list .drive-folder-card, .view-list .drive-file-card { display: grid; grid-template-columns: 36px 1fr 160px 140px 90px 40px; align-items: center; height: 48px; border-radius: 0; border: none; border-bottom: 1px solid #e0e0e0; padding: 0 16px; background: transparent; }
.view-list .drive-folder-card:hover, .view-list .drive-file-card:hover { background: #f0f4f9; }
.view-list .drive-folder-card.selected, .view-list .drive-file-card.selected { background: #c2e7ff; }
.view-list .drive-file-preview { display: none; }
.view-list .drive-file-card .item-info-wrap, .view-list .drive-folder-card .item-info-wrap { height: auto; padding: 0; }
.view-list .col-owner, .view-list .col-date, .view-list .col-size { display: flex; align-items: center; font-size: 0.875rem; color: #5f6368; }
.view-list .col-owner img { width: 24px; height: 24px; border-radius: 50%; margin-right: 8px; }
.view-list .item-checkbox { display: block; position: relative; top: 0; left: 0; opacity: 0.3; }
.view-list .drive-folder-card:hover .item-checkbox, .view-list .drive-file-card:hover .item-checkbox { opacity: 1; }
.view-list .action-wrapper { display: block !important; position: static !important; }
.view-list #driveListHeader { display: grid !important; }
</style>"""
css_end_replace = css_end_target.replace("</style>", "</style>\n<?php endif; ?>")
if css_end_target in content:
    content = content.replace(css_end_target, css_end_replace)

# 2. Add PHP if-else around the Navbar and Sidebar.
original_navbar_sidebar = """<?php else: ?>
    <div class="header-left">
        <button class="btn-icon btn-menu" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
        <div class="logo-mark" onclick="window.location='index.php?page=beranda'" style="cursor:pointer;">
          <img src="assets/images/LOGO_GAWE.svg" alt="Logo" onerror="this.style.display='none'">
          <span>WORKSPACE</span>
        </div>
        <?php if(isSuperAdmin()){?><span class="sa-badge"><i class="fa-solid fa-crown" style="margin-right:3px;font-size:.8em;"></i>God Mode</span><?php }?>
    </div>
    <div class="header-center">
        <div class="search-bar">
            <form method="GET" action="index.php">
                <input type="hidden" name="page" value="workspace">
                <?php if($active_folder) echo "<input type='hidden' name='folder_id' value='{$active_folder}'>"; ?>
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" placeholder="Cari dokumen atau folder..." value="<?= h($search_query) ?>" autocomplete="off">
            </form>
        </div>
    </div>
    <div class="header-right">
        <span class="stats-badge"><?= $size_used ?> / 1 GB</span>
        <div class="profile-container">
            <img src="<?= h($path_foto) ?>" alt="Profile" class="avatar" onclick="toggleProfileMenu()">
            <div id="profileMenu" class="profile-menu">
                <div class="profile-header-info">
                    <img src="<?= h($path_foto) ?>" alt="">
                    <div>
                        <strong><?= h(!empty($profile_data['identitas']['nama_sebutan'])?$profile_data['identitas']['nama_sebutan']:$nama_lengkap) ?></strong>
                        <span><?= h($role) ?><?= !empty($profile_data['identitas']['profesi'])?' &middot; '.h($profile_data['identitas']['profesi']):'' ?></span>
                    </div>
                </div>
                <div class="profile-menu-links">
                    <a href="index.php?page=beranda"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                    <a href="index.php?page=workspace"><i class="fa-solid fa-folder-open"></i> Workspace</a>
                    <a href="index.php?page=profile"><i class="fa-solid fa-id-card"></i> CV Builder</a>
                    <a href="<?= h($portfolio_url) ?>" target="_blank"><i class="fa-solid fa-globe"></i> Lihat Portfolio</a>
                    <?php if(isSuperAdmin()){?><a href="index.php?page=manajemen-pengguna" style="color:var(--superadmin);"><i class="fa-solid fa-users-gear"></i> Manajemen User</a><?php }?>
                    <hr class="menu-divider">
                    <button onclick="openModal('settingsModal');closeAllMenus();"><i class="fa-solid fa-gear"></i> Pengaturan Akun</button>
                    <hr class="menu-divider">
                    <a href="?logout=true" style="color:var(--danger);"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
<div class="sidebar" id="sidebar">
<?php if($current_page === 'workspace'): ?>"""

# First, replace the opening tag for the new Navbar.
nav_start_target = """<div class="top-navbar">
    <div class="header-left" style="width: 256px;">"""
nav_start_replace = """<div class="top-navbar">
<?php if($current_page === 'workspace'): ?>
    <div class="header-left" style="width: 256px;">"""

content = content.replace(nav_start_target, nav_start_replace)

nav_end_target = """        </div>
    </div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
<div class="sidebar" id="sidebar">
    <div style="padding: 8px 16px 16px;">"""

content = content.replace(nav_end_target, original_navbar_sidebar + """
    <div style="padding: 8px 16px 16px;">""")

sidebar_end_target = """            <button style="margin-top:12px; width:100%; padding:8px 16px; border-radius:20px; border:1px solid #c2e7ff; background:transparent; color:#0b57d0; font-weight:600; cursor:pointer; font-size:0.875rem; transition:background 0.2s;" onmouseover="this.style.background='#f0f4f9'" onmouseout="this.style.background='transparent'">Dapatkan penyimpanan ekstra</button>
        </div>
    </div>
</div>"""

original_sidebar_end = """            <button style="margin-top:12px; width:100%; padding:8px 16px; border-radius:20px; border:1px solid #c2e7ff; background:transparent; color:#0b57d0; font-weight:600; cursor:pointer; font-size:0.875rem; transition:background 0.2s;" onmouseover="this.style.background='#f0f4f9'" onmouseout="this.style.background='transparent'">Dapatkan penyimpanan ekstra</button>
        </div>
    </div>
<?php else: ?>
    <div class="sidebar-section">
        <div class="sidebar-section-label">Main</div>
        <a href="index.php?page=beranda" class="nav-item <?= $current_page==='beranda'?'active':'' ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a href="index.php?page=workspace" class="nav-item <?= $current_page==='workspace'?'active':'' ?>"><i class="fa-solid fa-folder-open"></i> Workspace</a>
        <a href="index.php?page=workspace&view=recent" class="nav-item"><i class="fa-solid fa-clock-rotate-left"></i> Akses Terbaru</a>
        <a href="index.php?page=workspace&view=assets" class="nav-item"><i class="fa-solid fa-images"></i> Aset Visual</a>
        <a href="index.php?page=workspace&view=stats" class="nav-item"><i class="fa-solid fa-chart-bar"></i> Statistik</a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-section-label">Profil</div>
        <a href="index.php?page=profile" class="nav-item <?= $current_page==='profile'?'active':'' ?>"><i class="fa-solid fa-id-card"></i> CV Builder</a>
        <a href="<?= h($portfolio_url) ?>" target="_blank" class="nav-item"><i class="fa-solid fa-globe"></i> Lihat Portfolio</a>
        <a href="index.php" target="_blank" class="nav-item"><i class="fa-solid fa-users"></i> Direktori Talent</a>
    </div>
    <div class="sidebar-section">
        <div class="sidebar-section-label">Lainnya</div>
        <a href="index.php?page=workspace&view=trash" class="nav-item"><i class="fa-solid fa-trash-can"></i> Tong Sampah</a>
    </div>
    <?php if(isSuperAdmin()){?>
    <div class="sidebar-section">
        <div class="sidebar-section-label" style="color:var(--superadmin);">God Mode</div>
        <a href="index.php?page=manajemen-pengguna" class="nav-item superadmin-item <?= $current_page==='manajemen-pengguna'?'active':'' ?>"><i class="fa-solid fa-users-gear"></i> Manajemen User</a>
    </div>
    <?php }?>
    <div class="sidebar-storage">
        <div class="storage-label">Penyimpanan</div>
        <div class="storage-bar"><div class="storage-bar-fill" style="width:<?= $storage_pct ?>%;"></div></div>
        <div class="storage-text"><?= $size_used ?> dari 1 GB</div>
    </div>
<?php endif; ?>
</div>"""

content = content.replace(sidebar_end_target, original_sidebar_end)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)

print("index.php patched successfully.")
