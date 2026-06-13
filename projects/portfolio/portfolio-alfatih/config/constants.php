<?php
// Base URL Configuration
define('BASE_URL', 'https://gawe.my.id/portfolio-alfatih');
define('ROOT_PATH', dirname(__DIR__));

// Database Configuration
define("DB_HOST", "localhost");
define("DB_PORT", "3306"); // WAJIB 3306 atau hapus saja baris ini jika tidak dipakai di koneksi
define("DB_NAME", "mckmmukg_undangan"); // Ganti dengan nama DB yang benar di cPanel
define("DB_USER", "mckmmukg_alfa"); // Ganti dengan user DB cPanel
define("DB_PASS", "Alfaragatak87"); // Pastikan password ini benar
// Owner Information
define('OWNER_NAME', 'Muhammad Alfatih');
define('OWNER_EMAIL', 's.s.6624844@gmail.com');
define('OWNER_WHATSAPP', '+62 831-8881-3237');
define('OWNER_GITHUB', 'https://github.com/alfaragatak87');
define('OWNER_EDUCATION', 'S1 Informatika, ITB Widya Gama Lumajang (Semester 3)');
define('OWNER_SKILLS', 'PHP, JavaScript, HTML, CSS, MySQL, UI/UX (Figma), Digital Marketing');

// Include language file
require_once __DIR__ . '/language.php';

?>