<?php
// ============================================================
// ALFATIH DIGITAL WORKSPACE — config/database.php
// Fokus: Konfigurasi koneksi, session_start, & migrasi tabel.
// ============================================================

// ── Error Reporting (Nonaktifkan di production) ──────────────
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ── Konstanta Global Aplikasi ─────────────────────────────────
define('DB_HOST',         'localhost');
define('DB_USER',         'mckmmukg_alfa');
define('DB_PASS',         'Alfaragatak87');
define('DB_NAME',         'mckmmukg_undangan');
define('SITE_URL',        'https://gawe.my.id');
define('UPLOAD_DIR',      'uploads/files/');
define('PROFILE_IMG_DIR', 'uploads/');

// ── Session & MySQLi Report ───────────────────────────────────
mysqli_report(MYSQLI_REPORT_OFF);
session_start();

// ── Koneksi Database ──────────────────────────────────────────
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    die("DB Error: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

// ── Migrasi Tabel (ALTER IF NOT EXISTS via @suppress error) ──
// Setiap query dijalankan sekali; jika kolom sudah ada, error
// ditekan oleh @ sehingga tidak mengganggu eksekusi normal.
$migrations = [
    "ALTER TABLE `users` MODIFY `role` ENUM('superadmin','admin','user') NOT NULL DEFAULT 'user'",
    "ALTER TABLE `users` ADD COLUMN `email`        VARCHAR(100)  DEFAULT NULL",
    "ALTER TABLE `users` ADD COLUMN `phone`        VARCHAR(30)   DEFAULT NULL",
    "ALTER TABLE `users` ADD COLUMN `profile_data` LONGTEXT      DEFAULT NULL",
    "ALTER TABLE `users` ADD COLUMN `last_login`   TIMESTAMP     NULL DEFAULT NULL",
    "ALTER TABLE `users` ADD COLUMN `created_at`   TIMESTAMP     DEFAULT CURRENT_TIMESTAMP",
    "ALTER TABLE `admin_logs` ADD COLUMN `user_id` INT(11)       DEFAULT NULL",
];
foreach ($migrations as $sql) {
    @$mysqli->query($sql);
}

// ── Seed Role Awal ────────────────────────────────────────────
// Memastikan user 'alfa' selalu menjadi superadmin,
// dan user-user lama dengan role non-standar di-normalisasi.
$mysqli->query("UPDATE `users` SET `role` = 'superadmin' WHERE `username` = 'alfa'");
$mysqli->query("UPDATE `users` SET `role` = 'admin'
                WHERE `username` != 'alfa'
                AND (`role` = 'bapak' OR `role` = 'ajay' OR `role` = 'user')");
$migrations_tambahan = [

    // ── Onboarding ──────────────────────────────────────────
    "ALTER TABLE `users` ADD COLUMN `is_onboarded`     TINYINT(1) NOT NULL DEFAULT 0 AFTER `profile_data`",
    "ALTER TABLE `users` ADD COLUMN `profesi_category` VARCHAR(80) DEFAULT NULL AFTER `is_onboarded`",
    "ALTER TABLE `users` ADD COLUMN `tgl_lahir`        DATE DEFAULT NULL AFTER `profesi_category`",
    "ALTER TABLE `users` ADD COLUMN `status`           ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active' AFTER `tgl_lahir`",

    // ── Starred: Files ──────────────────────────────────────
    "ALTER TABLE `files` ADD COLUMN `is_starred`   TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_deleted`",
    "ALTER TABLE `files` ADD COLUMN `deskripsi`    TEXT DEFAULT NULL AFTER `is_starred`",
    "ALTER TABLE `files` ADD COLUMN `label_color`  VARCHAR(7) DEFAULT NULL AFTER `deskripsi`",

    // ── Starred: Folders ────────────────────────────────────
    "ALTER TABLE `folders` ADD COLUMN `is_starred`    TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_deleted`",
    "ALTER TABLE `folders` ADD COLUMN `cover_emoji`   VARCHAR(10) DEFAULT NULL AFTER `is_starred`",

    // ── Tabel baru ──────────────────────────────────────────
    "CREATE TABLE IF NOT EXISTS `skill_presets` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nama` VARCHAR(100) NOT NULL,
        `kategori` VARCHAR(80) NOT NULL,
        `sub_kategori` VARCHAR(80) DEFAULT NULL,
        `icon` VARCHAR(50) DEFAULT 'fa-star',
        `warna` VARCHAR(7) DEFAULT '#6b7280',
        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`id`),
        KEY `idx_kategori` (`kategori`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `onboarding_progress` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) NOT NULL,
        `step` VARCHAR(50) NOT NULL,
        `completed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_step` (`user_id`, `step`),
        KEY `idx_user` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];