<div class="admin-sidebar">
    <div class="sidebar-header">
        <h2><?= OWNER_NAME ?></h2>
        <p>Admin Panel</p>
    </div>

    <div class="sidebar-menu">
        <ul>
            <li>
                <a href="<?= BASE_URL ?>/admin/dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/admin/kelola_profil.php" class="<?= basename($_SERVER['PHP_SELF']) === 'kelola_profil.php' ? 'active' : '' ?>">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="sidebar-dropdown">
                <a href="#" class="dropdown-toggle <?= in_array(basename($_SERVER['PHP_SELF']), ['kelola_proyek.php']) ? 'active' : '' ?>">
                    <i class="fas fa-briefcase"></i>
                    <span>Projects</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="sidebar-submenu">
                    <ul>
                        <li>
                            <a href="<?= BASE_URL ?>/admin/kelola_proyek.php">
                                <i class="fas fa-list"></i>
                                <span>All Projects</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>/admin/kelola_proyek.php?action=add">
                                <i class="fas fa-plus"></i>
                                <span>Add New</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="sidebar-dropdown">
                <a href="#" class="dropdown-toggle <?= in_array(basename($_SERVER['PHP_SELF']), ['kelola_artikel.php', 'kelola_kategori.php']) ? 'active' : '' ?>">
                    <i class="fas fa-newspaper"></i>
                    <span>Blog</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="sidebar-submenu">
                    <ul>
                        <li>
                            <a href="<?= BASE_URL ?>/admin/kelola_artikel.php">
                                <i class="fas fa-list"></i>
                                <span>All Articles</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>/admin/kelola_artikel.php?action=add">
                                <i class="fas fa-plus"></i>
                                <span>Add New</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>/admin/kelola_kategori.php">
                                <i class="fas fa-tags"></i>
                                <span>Categories</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/admin/kelola_dokumen.php" class="<?= basename($_SERVER['PHP_SELF']) === 'kelola_dokumen.php' ? 'active' : '' ?>">
                    <i class="fas fa-file-alt"></i>
                    <span>Documents</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/admin/kelola_semester.php" class="<?= basename($_SERVER['PHP_SELF']) === 'kelola_semester.php' ? 'active' : '' ?>">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Semester Data</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/admin/kelola_testimonial.php" class="<?= basename($_SERVER['PHP_SELF']) === 'kelola_testimonial.php' ? 'active' : '' ?>">
                    <i class="fas fa-comment-dots"></i>
                    <span>Testimonials</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/admin/kelola_pesan.php" class="<?= basename($_SERVER['PHP_SELF']) === 'kelola_pesan.php' ? 'active' : '' ?>">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/admin/media_library.php" class="<?= basename($_SERVER['PHP_SELF']) === 'media_library.php' ? 'active' : '' ?>">
                    <i class="fas fa-photo-video"></i>
                    <span>Media Library</span>
                </a>
            </li>
            <li>
                <a href="<?= BASE_URL ?>/admin/kelola_skill.php" class="<?= basename($_SERVER['PHP_SELF']) === 'kelola_skill.php' ? 'active' : '' ?>">
                    <i class="fas fa-code"></i>
                    <span>Skills</span>
                </a>
            </li>
            <li class="sidebar-dropdown">
                <a href="#" class="dropdown-toggle <?= in_array(basename($_SERVER['PHP_SELF']), ['kelola_pengaturan.php', 'kelola_tema.php', 'kelola_content.php', 'backup.php']) ? 'active' : '' ?>">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <div class="sidebar-submenu">
                    <ul>
                        <li>
                            <a href="<?= BASE_URL ?>/admin/kelola_pengaturan.php">
                                <i class="fas fa-sliders-h"></i>
                                <span>General Settings</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>/admin/kelola_tema.php">
                                <i class="fas fa-paint-brush"></i>
                                <span>Theme Settings</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>/admin/kelola_content.php">
                                <i class="fas fa-edit"></i>
                                <span>Content Editor</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>/admin/backup.php">
                                <i class="fas fa-database"></i>
                                <span>Backup System</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>" class="visit-site-btn" target="_blank">
            <i class="fas fa-external-link-alt"></i>
            <span>Visit Site</span>
        </a>
        <a href="<?= BASE_URL ?>/admin/logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<?php

?>