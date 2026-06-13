-- phpMyAdmin SQL Dump
-- version 5.2.1
-- Siap untuk cPanel / Production

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT 1,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `login_attempts` int(11) DEFAULT 0,
  `recovery_token` varchar(255) DEFAULT NULL,
  `recovery_expires` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin` (`id`, `username`, `password`, `role_id`, `email`, `phone`, `profile_image`, `last_login`, `status`, `login_attempts`, `recovery_token`, `recovery_expires`) VALUES
(1, 'admin', '$2y$10$TCZLfbegzpRbPzoXvC25ZubMF2cQ.4xrp1LFNi6zEzlf4znQkfaLS', 1, NULL, NULL, NULL, '2025-07-16 11:35:10', 'active', 0, NULL, NULL);

-- --------------------------------------------------------

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `admin_roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `permissions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin_roles` (`id`, `role_name`, `permissions`) VALUES
(1, 'Super Admin', '{"all":true}'),
(2, 'Editor', '{"dashboard":true,"content":true,"media":true,"settings":false,"users":false}'),
(3, 'Author', '{"dashboard":true,"content":{"view":true,"add":true,"edit":true,"delete":false},"media":{"view":true,"add":true,"delete":false}}');

-- --------------------------------------------------------

CREATE TABLE `artikel` (
  `id` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `konten` text NOT NULL,
  `gambar_unggulan` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_featured` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied','archived') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `dokumen` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `kategori` enum('Tugas','Sertifikat','CV','Lainnya') NOT NULL,
  `semester` int(11) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `dokumen` (`id`, `nama`, `file`, `kategori`, `semester`, `deskripsi`, `tanggal_upload`) VALUES
(4, 'CV Muhammad Alfatih', 'document_1752432909.pdf', 'CV', 0, '', '2025-07-13 18:55:09');

-- --------------------------------------------------------

CREATE TABLE `job_titles` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `job_titles` (`id`, `title`, `urutan`) VALUES
(1, 'Web Developer', 1),
(2, 'UI/UX Designer', 2),
(3, 'Mahasiswa Informatika', 3),
(4, 'Problem Solver', 4);

-- --------------------------------------------------------

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `kategori` (`id`, `nama_kategori`) VALUES
(3, 'Digital Marketing'),
(4, 'Programming'),
(2, 'UI/UX Design'),
(1, 'Web Development');

-- --------------------------------------------------------

CREATE TABLE `media_library` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `uploaded_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `page_content` (
  `id` int(11) NOT NULL,
  `page` varchar(100) NOT NULL,
  `section` varchar(100) NOT NULL,
  `content` text DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `page_content` (`id`, `page`, `section`, `content`, `last_updated`) VALUES
(1, 'home', 'hero_title', '', '2025-07-15 17:51:26'),
(2, 'home', 'hero_subtitle', 'MAHASISWA S1 INFORMATIKA', '2025-07-15 17:51:26'),
(3, 'home', 'hero_description', '', '2025-07-15 17:51:26'),
(4, 'home', 'projects_title', '', '2025-07-15 17:51:26'),
(5, 'home', 'projects_subtitle', '', '2025-07-15 17:51:26'),
(6, 'home', 'articles_title', '', '2025-07-15 17:51:26'),
(7, 'home', 'articles_subtitle', '', '2025-07-15 17:51:26');

-- --------------------------------------------------------

CREATE TABLE `pendidikan` (
  `id` int(11) NOT NULL,
  `institusi` varchar(255) NOT NULL,
  `gelar` varchar(255) NOT NULL,
  `bidang_studi` varchar(255) DEFAULT NULL,
  `tahun_mulai` year(4) NOT NULL,
  `tahun_selesai` year(4) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `pendidikan` (`id`, `institusi`, `gelar`, `bidang_studi`, `tahun_mulai`, `tahun_selesai`, `deskripsi`, `urutan`) VALUES
(1, 'ITB Widya Gama Lumajang', 'S1 Informatika', 'Ilmu Komputer', '2023', NULL, 'Fokus pada pengembangan web, algoritma, dan database management.', 1),
(2, 'SMK Miftahul Islam Kunir', 'Teknik Komputer & Jaringan', 'Jaringan Komputer', '2019', '2022', 'Belajar tentang jaringan komputer, troubleshooting hardware, dan pemrograman dasar.', 2),
(3, 'MTs Salafiyah Al-Yasiny', 'MTs', 'Pendidikan Umum', '2016', '2019', 'Pendidikan menengah pertama dengan tambahan studi Islam.', 3),
(4, 'MI Salafiyah Al-Yasiny', 'MI', 'Pendidikan Dasar', '2010', '2016', 'Pendidikan dasar dengan kursus literasi komputer dasar.', 4);

-- --------------------------------------------------------

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `kunci` varchar(50) NOT NULL,
  `nilai` text DEFAULT NULL,
  `deskripsi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `pengaturan` (`id`, `kunci`, `nilai`, `deskripsi`) VALUES
(1, 'site_title', 'Portfolio Muhammad Alfatih', 'Judul website yang ditampilkan di tab browser'),
(2, 'meta_description', 'Portfolio professional Muhammad Alfatih, Mahasiswa Informatika\r\nITB WIDYAGAMA LUMAJANG', 'Deskripsi meta untuk SEO'),
(3, 'footer_text', '© 2025 Muhammad Alfatih. All Rights Reserved.', 'Teks yang ditampilkan di footer'),
(4, 'enable_particles', '1', 'Aktifkan efek particles di background (1=aktif, 0=nonaktif)'),
(5, 'theme_color', '#00e5ff', 'Warna utama theme website'),
(6, 'default_language', 'id', 'Bahasa default untuk website (id = Indonesia, en = English)'),
(7, 'theme', 'dark-orange', NULL),
(10, 'animation_speed', 'normal', NULL),
(16, 'tagline', '', NULL),
(18, 'email', 's.s.6624844@gmail.com', NULL),
(19, 'whatsapp', 'https://wa.me/6283188813237', NULL),
(20, 'github_url', 'https://github.com/alfaragatak87', NULL),
(21, 'linkedin_url', '', NULL),
(22, 'instagram_url', 'https://www.instagram.com/alfamuhammad___/', NULL),
(24, 'meta_keywords', 'portofolio muhammad alfatih', NULL),
(38, 'theme_color_type', 'solid', NULL),
(39, 'theme_color_solid', '#00fffb', NULL),
(40, 'theme_color_gradient_start', '#00e5ff', NULL),
(41, 'theme_color_gradient_end', '#6943d0', NULL),
(42, 'theme_color_gradient_angle', '90', NULL);

-- --------------------------------------------------------

CREATE TABLE `profil` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `github` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `current_status` varchar(100) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `profil` (`id`, `nama`, `email`, `whatsapp`, `github`, `profile_image`, `summary`, `location`, `current_status`, `last_updated`) VALUES
(1, 'Muhammad Alfatih', 's.s.6624844@gmail.com', '+62 831-8881-3237', 'https://github.com/alfaragatak87', 'profile_1752498484.png', 'Web Developer dan UI/UX Designer dengan keahlian dalam PHP, JavaScript, dan teknologi web terkini. Saya menciptakan solusi digital yang tidak hanya berfungsi dengan baik, tetapi juga memberikan pengalaman pengguna yang optimal.', 'Lumajang, East Java, Indonesia', 'Mahasiswa', '2025-07-14 13:08:04');

-- --------------------------------------------------------

CREATE TABLE `proyek` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar_proyek` varchar(255) NOT NULL,
  `link_proyek` varchar(255) DEFAULT NULL,
  `tanggal_dibuat` date NOT NULL,
  `is_featured` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `semester_data` (
  `id` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `mata_kuliah` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `level` int(11) NOT NULL DEFAULT 70,
  `icon` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `system_backups` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `backup_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `posisi` varchar(100) DEFAULT NULL,
  `perusahaan` varchar(100) DEFAULT NULL,
  `testimonial` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 1,
  `tanggal_dibuat` timestamp NOT NULL DEFAULT current_timestamp(),
  `rating` int(11) NOT NULL DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `testimonials` (`id`, `nama`, `posisi`, `perusahaan`, `testimonial`, `foto`, `aktif`, `tanggal_dibuat`, `rating`) VALUES
(4, 'unknow', '', '', 'ngeri bosssss', '', 1, '2025-07-13 12:36:46', 5);

-- --------------------------------------------------------

CREATE TABLE `website_analytics` (
  `id` int(11) NOT NULL,
  `page_url` varchar(255) NOT NULL,
  `page_title` varchar(255) DEFAULT NULL,
  `visitor_ip` varchar(45) DEFAULT NULL,
  `visitor_country` varchar(100) DEFAULT NULL,
  `visitor_device` varchar(100) DEFAULT NULL,
  `visitor_browser` varchar(100) DEFAULT NULL,
  `visit_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `referrer` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dikosongkan agar analytics live site mulai dari nol, bukan dari localhost

-- --------------------------------------------------------

--
-- Indexes & AUTO_INCREMENT
--

ALTER TABLE `admin` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `username` (`username`);
ALTER TABLE `admin_logs` ADD PRIMARY KEY (`id`);
ALTER TABLE `admin_roles` ADD PRIMARY KEY (`id`);
ALTER TABLE `artikel` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `slug` (`slug`), ADD KEY `id_kategori` (`id_kategori`);
ALTER TABLE `contact_messages` ADD PRIMARY KEY (`id`);
ALTER TABLE `dokumen` ADD PRIMARY KEY (`id`);
ALTER TABLE `job_titles` ADD PRIMARY KEY (`id`);
ALTER TABLE `kategori` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);
ALTER TABLE `media_library` ADD PRIMARY KEY (`id`);
ALTER TABLE `notifications` ADD PRIMARY KEY (`id`);
ALTER TABLE `page_content` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `page_section` (`page`,`section`);
ALTER TABLE `pendidikan` ADD PRIMARY KEY (`id`);
ALTER TABLE `pengaturan` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `kunci` (`kunci`);
ALTER TABLE `profil` ADD PRIMARY KEY (`id`);
ALTER TABLE `proyek` ADD PRIMARY KEY (`id`);
ALTER TABLE `semester_data` ADD PRIMARY KEY (`id`);
ALTER TABLE `skills` ADD PRIMARY KEY (`id`);
ALTER TABLE `system_backups` ADD PRIMARY KEY (`id`);
ALTER TABLE `testimonials` ADD PRIMARY KEY (`id`);
ALTER TABLE `website_analytics` ADD PRIMARY KEY (`id`);

ALTER TABLE `admin` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `admin_logs` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `admin_roles` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `artikel` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `contact_messages` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `dokumen` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `job_titles` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `kategori` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `media_library` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `notifications` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `page_content` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
ALTER TABLE `pendidikan` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `pengaturan` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;
ALTER TABLE `profil` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `proyek` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `semester_data` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `skills` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `system_backups` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `testimonials` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `website_analytics` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `artikel` ADD CONSTRAINT `artikel_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;