<div class="top-navbar">
    <div class="header-left">
        <button class="btn-icon btn-menu" onclick="toggleSidebar()"><i class="fa-solid fa-bars"></i></button>
        <span class="logo-mark" onclick="window.location='index.php?page=beranda'">WORKSPACE</span>
        <?php if(isSuperAdmin()){?><span class="sa-badge"><i class="fa-solid fa-crown" style="margin-right:3px;font-size:.8em;"></i>God Mode</span><?php }?>
    </div>
    <div class="header-center">
        <div class="search-bar">
            <form method="GET" action="index.php">
                <input type="hidden" name="page" value="workspace">
                <?php if(isset($active_folder) && $active_folder) echo "<input type='hidden' name='folder_id' value='{$active_folder}'>"; ?>
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" placeholder="Cari dokumen atau folder..." value="<?= h($search_query ?? '') ?>" autocomplete="off">
            </form>
        </div>
    </div>
    <div class="header-right">
        <span class="stats-badge"><?= $size_used ?? '0 B' ?> / 1 GB</span>
        <div class="profile-container">
            <img src="<?= h($path_foto ?? '') ?>" alt="Profile" class="avatar" onclick="toggleProfileMenu()">
            <div id="profileMenu" class="profile-menu">
                <div class="profile-header-info">
                    <img src="<?= h($path_foto ?? '') ?>" alt="">
                    <div>
                        <strong><?= h(!empty($profile_data['identitas']['nama_sebutan']) ? $profile_data['identitas']['nama_sebutan'] : ($nama_lengkap ?? 'User')) ?></strong>
                        <span><?= h($role ?? 'user') ?><?= !empty($profile_data['identitas']['profesi']) ? ' &middot; '.h($profile_data['identitas']['profesi']) : '' ?></span>
                    </div>
                </div>
                <div class="profile-menu-links">
                    <a href="index.php?page=beranda"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                    <a href="index.php?page=workspace"><i class="fa-solid fa-folder-open"></i> Workspace</a>
                    <a href="index.php?page=profile"><i class="fa-solid fa-id-card"></i> CV Builder</a>
                    <a href="<?= h($portfolio_url ?? '#') ?>" target="_blank"><i class="fa-solid fa-globe"></i> Lihat Portfolio</a>
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