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
