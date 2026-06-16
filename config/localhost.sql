-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 16 Jun 2026 pada 21.18
-- Versi server: 10.11.16-MariaDB-cll-lve
-- Versi PHP: 8.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mckmmukg_portfolio-alfatih`
--
CREATE DATABASE IF NOT EXISTS `mckmmukg_portfolio-alfatih` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `mckmmukg_portfolio-alfatih`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `artikel`
--

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

--
-- Struktur dari tabel `contact_messages`
--

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

--
-- Struktur dari tabel `job_titles`
--

CREATE TABLE `job_titles` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `job_titles`
--

INSERT INTO `job_titles` (`id`, `title`, `urutan`) VALUES
(1, 'Web Developer', 1),
(2, 'UI/UX Designer', 2),
(3, 'Mahasiswa Informatika', 3),
(4, 'Problem Solver', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`) VALUES
(3, 'Digital Marketing'),
(4, 'Programming'),
(2, 'UI/UX Design'),
(1, 'Web Development');

-- --------------------------------------------------------

--
-- Struktur dari tabel `page_content`
--

CREATE TABLE `page_content` (
  `id` int(11) NOT NULL,
  `page` varchar(100) NOT NULL,
  `section` varchar(100) NOT NULL,
  `content` text DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `page_content`
--

INSERT INTO `page_content` (`id`, `page`, `section`, `content`, `last_updated`) VALUES
(1, 'home', 'hero_title', '', '2025-07-15 17:51:26'),
(2, 'home', 'hero_subtitle', 'MAHASISWA S1 INFORMATIKA', '2025-07-15 17:51:26'),
(3, 'home', 'hero_description', '', '2025-07-15 17:51:26'),
(4, 'home', 'projects_title', '', '2025-07-15 17:51:26'),
(5, 'home', 'projects_subtitle', '', '2025-07-15 17:51:26'),
(6, 'home', 'articles_title', '', '2025-07-15 17:51:26'),
(7, 'home', 'articles_subtitle', '', '2025-07-15 17:51:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendidikan`
--

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

--
-- Dumping data untuk tabel `pendidikan`
--

INSERT INTO `pendidikan` (`id`, `institusi`, `gelar`, `bidang_studi`, `tahun_mulai`, `tahun_selesai`, `deskripsi`, `urutan`) VALUES
(1, 'ITB Widya Gama Lumajang', 'S1 Informatika', 'Ilmu Komputer', '2023', NULL, 'Fokus pada pengembangan web, algoritma, dan database management.', 1),
(2, 'SMK Miftahul Islam Kunir', 'Teknik Komputer & Jaringan', 'Jaringan Komputer', '2019', '2022', 'Belajar tentang jaringan komputer, troubleshooting hardware, dan pemrograman dasar.', 2),
(3, 'MTs Salafiyah Al-Yasiny', 'MTs', 'Pendidikan Umum', '2016', '2019', 'Pendidikan menengah pertama dengan tambahan studi Islam.', 3),
(4, 'MI Salafiyah Al-Yasiny', 'MI', 'Pendidikan Dasar', '2010', '2016', 'Pendidikan dasar dengan kursus literasi komputer dasar.', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `profil`
--

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

--
-- Dumping data untuk tabel `profil`
--

INSERT INTO `profil` (`id`, `nama`, `email`, `whatsapp`, `github`, `profile_image`, `summary`, `location`, `current_status`, `last_updated`) VALUES
(1, 'Muhammad Alfatih', 's.s.6624844@gmail.com', '+62 831-8881-3237', 'https://github.com/alfaragatak87', 'profile_1752498484.png', 'Web Developer dan UI/UX Designer dengan keahlian dalam PHP, JavaScript, dan teknologi web terkini. Saya menciptakan solusi digital yang tidak hanya berfungsi dengan baik, tetapi juga memberikan pengalaman pengguna yang optimal.', 'Lumajang, East Java, Indonesia', 'Mahasiswa', '2025-07-14 13:08:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `proyek`
--

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

--
-- Struktur dari tabel `semester_data`
--

CREATE TABLE `semester_data` (
  `id` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `mata_kuliah` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `skills`
--

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

--
-- Struktur dari tabel `testimonials`
--

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

--
-- Dumping data untuk tabel `testimonials`
--

INSERT INTO `testimonials` (`id`, `nama`, `posisi`, `perusahaan`, `testimonial`, `foto`, `aktif`, `tanggal_dibuat`, `rating`) VALUES
(4, 'unknow', '', '', 'ngeri bosssss', '', 1, '2025-07-13 12:36:46', 5);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `artikel`
--
ALTER TABLE `artikel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `job_titles`
--
ALTER TABLE `job_titles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indeks untuk tabel `page_content`
--
ALTER TABLE `page_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_section` (`page`,`section`);

--
-- Indeks untuk tabel `pendidikan`
--
ALTER TABLE `pendidikan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `profil`
--
ALTER TABLE `profil`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `proyek`
--
ALTER TABLE `proyek`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `semester_data`
--
ALTER TABLE `semester_data`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `artikel`
--
ALTER TABLE `artikel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `job_titles`
--
ALTER TABLE `job_titles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `page_content`
--
ALTER TABLE `page_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `pendidikan`
--
ALTER TABLE `pendidikan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `profil`
--
ALTER TABLE `profil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `proyek`
--
ALTER TABLE `proyek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `semester_data`
--
ALTER TABLE `semester_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `artikel`
--
ALTER TABLE `artikel`
  ADD CONSTRAINT `artikel_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE CASCADE;
--
-- Database: `mckmmukg_undangan`
--
CREATE DATABASE IF NOT EXISTS `mckmmukg_undangan` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `mckmmukg_undangan`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

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

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `role_id`, `email`, `phone`, `profile_image`, `last_login`, `status`, `login_attempts`, `recovery_token`, `recovery_expires`) VALUES
(1, 'admin', 'admin1234', 1, NULL, NULL, NULL, '2025-07-16 11:35:10', 'active', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`, `user_id`) VALUES
(1, 1, 'LOGIN', 'User logged in from IP: 140.213.42.215', '140.213.42.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-07 16:09:08', 1),
(2, 2, 'LOGIN', 'User logged in from IP: 103.4.82.154', '103.4.82.154', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 16:54:23', 2),
(3, 1, 'LOGIN', 'User logged in from IP: 103.4.82.154', '103.4.82.154', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 17:01:47', 1),
(4, 1, 'LOGIN', 'User logged in from IP: 140.213.241.53', '140.213.241.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-06-08 12:21:46', 1),
(5, 1, 'LOGIN', 'User logged in from IP: 140.213.245.219', '140.213.245.219', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 12:42:43', 1),
(6, 1, 'LOGIN', 'User logged in from IP: 140.213.239.239', '140.213.239.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 12:43:50', 1),
(7, 1, 'LOGIN', 'User logged in from IP: 140.213.239.239', '140.213.239.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 12:44:49', 1),
(8, 1, 'LOGIN', 'User logged in from IP: 140.213.239.239', '140.213.239.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 13:06:31', 1),
(9, 1, 'LOGIN', 'User logged in from IP: 140.213.246.28', '140.213.246.28', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-06-08 13:16:04', 1),
(10, 1, 'LOGIN', 'User logged in from IP: 140.213.246.28', '140.213.246.28', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-06-08 13:32:48', 1),
(11, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 17:18:37', 1),
(12, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 17:20:00', 1),
(13, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 18:22:57', 1),
(14, 3, 'LOGIN', 'User logged in from IP: 103.182.52.211', '103.182.52.211', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-09 17:51:59', 3),
(15, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 08:40:22', 1),
(16, 2, 'LOGIN', 'User logged in from IP: 103.182.52.212', '103.182.52.212', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-06-10 09:44:08', 2),
(17, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 10:09:44', 1),
(18, 3, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 10:19:49', 3),
(19, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 15:34:00', 1),
(20, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 16:01:53', 1),
(21, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 17:23:47', 1),
(22, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 17:50:11', 1),
(23, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 18:07:36', 1),
(24, 2, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 18:10:30', 2),
(25, 3, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 18:14:09', 3),
(26, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 02:14:36', 1),
(27, 3, 'LOGIN', 'User logged in from IP: 140.213.48.249', '140.213.48.249', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-06-11 11:14:27', 3),
(28, 1, 'LOGIN', 'User logged in from IP: 140.213.244.36', '140.213.244.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 05:47:24', 1),
(29, 1, 'LOGIN', 'User logged in from IP: 112.215.172.151', '112.215.172.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 07:16:48', 1),
(30, 1, 'LOGIN', 'User logged in from IP: 112.215.237.44', '112.215.237.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 10:03:35', 1),
(31, 1, 'LOGIN', 'User logged in from IP: 112.215.237.44', '112.215.237.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 10:55:38', 1),
(32, 3, 'LOGIN', 'User logged in from IP: 140.213.40.10', '140.213.40.10', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-12 11:44:47', 3),
(33, 3, 'LOGIN', 'User logged in from IP: 140.213.40.10', '140.213.40.10', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-12 11:45:56', 3),
(34, 1, 'LOGIN', 'User logged in from IP: 140.213.40.10', '140.213.40.10', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-12 11:46:33', 1),
(35, 1, 'LOGIN', 'User logged in from IP: 112.215.153.133', '112.215.153.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 13:45:55', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin_roles`
--

CREATE TABLE `admin_roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `permissions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin_roles`
--

INSERT INTO `admin_roles` (`id`, `role_name`, `permissions`) VALUES
(1, 'Super Admin', '{\"all\":true}'),
(2, 'Editor', '{\"dashboard\":true,\"content\":true,\"media\":true,\"settings\":false,\"users\":false}'),
(3, 'Author', '{\"dashboard\":true,\"content\":{\"view\":true,\"add\":true,\"edit\":true,\"delete\":false},\"media\":{\"view\":true,\"add\":true,\"delete\":false}}');

-- --------------------------------------------------------

--
-- Struktur dari tabel `artikel`
--

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

--
-- Struktur dari tabel `contact_messages`
--

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

--
-- Struktur dari tabel `dokumen`
--

CREATE TABLE `dokumen` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `kategori` enum('Tugas','Sertifikat','CV','Lainnya') NOT NULL,
  `semester` int(11) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dokumen`
--

INSERT INTO `dokumen` (`id`, `nama`, `file`, `kategori`, `semester`, `deskripsi`, `tanggal_upload`) VALUES
(4, 'CV Muhammad Alfatih', 'document_1752432909.pdf', 'CV', 0, '', '2025-07-13 18:55:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `owner_username` varchar(50) NOT NULL,
  `jenis` enum('file','link') DEFAULT 'file',
  `nama_file` varchar(255) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `link_url` text DEFAULT NULL,
  `share_token` varchar(100) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `tanggal_upload` timestamp NULL DEFAULT current_timestamp(),
  `status_cetak` varchar(50) DEFAULT 'Menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `files`
--

INSERT INTO `files` (`id`, `folder_id`, `owner_username`, `jenis`, `nama_file`, `file_path`, `tags`, `link_url`, `share_token`, `is_deleted`, `tanggal_upload`, `status_cetak`) VALUES
(5, 5, 'alfa', 'file', 'BPJS ALFATIH.pdf', 'alfa_1780294770_855_0.pdf', NULL, NULL, NULL, 0, '2026-06-01 06:19:30', 'Selesai'),
(6, 5, 'alfa', 'file', 'KTM UMM.pdf', 'alfa_1780294770_696_1.pdf', NULL, NULL, NULL, 0, '2026-06-01 06:19:30', 'Selesai'),
(7, 5, 'alfa', 'file', 'KTM WIGA ALFATIH.pdf', 'alfa_1780294770_691_2.pdf', NULL, NULL, NULL, 0, '2026-06-01 06:19:30', 'Selesai'),
(8, 5, 'alfa', 'file', 'REKENING BRI.pdf', 'alfa_1780294770_467_3.pdf', NULL, NULL, NULL, 0, '2026-06-01 06:19:30', 'Menunggu'),
(9, 5, 'alfa', 'file', 'SIM C .pdf', 'alfa_1780294770_695_4.pdf', NULL, NULL, NULL, 0, '2026-06-01 06:19:30', 'Menunggu'),
(10, 5, 'alfa', 'file', 'NPWP_Muhammad Alfatih(1).PNG', 'alfa_1780294830_774_0.png', NULL, NULL, NULL, 1, '2026-06-01 06:20:30', 'Menunggu'),
(11, 5, 'alfa', 'file', 'NPWP_MUHAMMAD ALFATIH.pdf', 'alfa_1780294929_555_0.pdf', NULL, NULL, NULL, 0, '2026-06-01 06:22:09', 'Menunggu'),
(12, 5, 'alfa', 'file', 'KARTU KELUARGA ZAINUL AMIN.pdf', 'alfa_1780294960_155_0.pdf', NULL, NULL, NULL, 0, '2026-06-01 06:22:40', 'Selesai'),
(13, 3, 'alfa', 'file', 'PENGEMB.MANAJ & KWIRA USHN.doc', 'alfa_1780509819_800_0.doc', '', NULL, NULL, 0, '2026-06-03 18:03:39', 'Diproses');

-- --------------------------------------------------------

--
-- Struktur dari tabel `folders`
--

CREATE TABLE `folders` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `owner_username` varchar(50) NOT NULL,
  `nama_folder` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-folder',
  `warna` varchar(10) DEFAULT '#22d3ee',
  `deskripsi` text DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deadline_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `folders`
--

INSERT INTO `folders` (`id`, `parent_id`, `owner_username`, `nama_folder`, `icon`, `warna`, `deskripsi`, `is_deleted`, `deadline_date`) VALUES
(1, NULL, 'alfa', 'DATA ALFA', 'fa-folder', '#22d3ee', '.', 0, NULL),
(2, NULL, 'bapak', 'Arsip Warga', 'fa-file-contract', '#22d3ee', 'Scan dokumen KTP dan KK.', 0, NULL),
(3, NULL, 'ajay', 'Lab Modul Coding', 'fa-laptop-code', '#22d3ee', 'Tugas pemrograman sekolah.', 0, NULL),
(4, NULL, 'bapak', 'DOKUMEN KELUARGA', 'fa-folder', '#ff0000', 'KK, KTP, AKTE, BPJS, DLL', 0, NULL),
(5, 4, 'bapak', 'ALFA', 'fa-folder-tree', '#00ff2a', '', 0, NULL),
(6, 4, 'bapak', 'AJAY', 'fa-folder-tree', '#ff0000', '', 0, NULL),
(7, 4, 'bapak', 'BAPAK', 'fa-folder-tree', '#38bdf8', '', 0, NULL),
(8, 4, 'alfa', 'IBUK', 'fa-folder-tree', '#fff700', '', 0, NULL),
(9, 4, 'alfa', 'RIFDA', 'fa-folder-tree', '#ff00d0', '', 0, NULL),
(10, 4, 'bapak', 'IBUK', 'fa-folder-tree', '#ff00ff', '', 0, NULL),
(11, 4, 'bapak', 'RIFDA', 'fa-folder-tree', '#ffffff', '', 0, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_titles`
--

CREATE TABLE `job_titles` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `job_titles`
--

INSERT INTO `job_titles` (`id`, `title`, `urutan`) VALUES
(1, 'Web Developer', 1),
(2, 'UI/UX Designer', 2),
(3, 'Mahasiswa Informatika', 3),
(4, 'Problem Solver', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`) VALUES
(3, 'Digital Marketing'),
(4, 'Programming'),
(2, 'UI/UX Design'),
(1, 'Web Development');

-- --------------------------------------------------------

--
-- Struktur dari tabel `media_library`
--

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

--
-- Struktur dari tabel `notifications`
--

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

--
-- Struktur dari tabel `page_content`
--

CREATE TABLE `page_content` (
  `id` int(11) NOT NULL,
  `page` varchar(100) NOT NULL,
  `section` varchar(100) NOT NULL,
  `content` text DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `page_content`
--

INSERT INTO `page_content` (`id`, `page`, `section`, `content`, `last_updated`) VALUES
(1, 'home', 'hero_title', '', '2025-07-15 17:51:26'),
(2, 'home', 'hero_subtitle', 'MAHASISWA S1 INFORMATIKA', '2025-07-15 17:51:26'),
(3, 'home', 'hero_description', '', '2025-07-15 17:51:26'),
(4, 'home', 'projects_title', '', '2025-07-15 17:51:26'),
(5, 'home', 'projects_subtitle', '', '2025-07-15 17:51:26'),
(6, 'home', 'articles_title', '', '2025-07-15 17:51:26'),
(7, 'home', 'articles_subtitle', '', '2025-07-15 17:51:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendidikan`
--

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

--
-- Dumping data untuk tabel `pendidikan`
--

INSERT INTO `pendidikan` (`id`, `institusi`, `gelar`, `bidang_studi`, `tahun_mulai`, `tahun_selesai`, `deskripsi`, `urutan`) VALUES
(1, 'ITB Widya Gama Lumajang', 'S1 Informatika', 'Ilmu Komputer', '2023', NULL, 'Fokus pada pengembangan web, algoritma, dan database management.', 1),
(2, 'SMK Miftahul Islam Kunir', 'Teknik Komputer & Jaringan', 'Jaringan Komputer', '2019', '2022', 'Belajar tentang jaringan komputer, troubleshooting hardware, dan pemrograman dasar.', 2),
(3, 'MTs Salafiyah Al-Yasiny', 'MTs', 'Pendidikan Umum', '2016', '2019', 'Pendidikan menengah pertama dengan tambahan studi Islam.', 3),
(4, 'MI Salafiyah Al-Yasiny', 'MI', 'Pendidikan Dasar', '2010', '2016', 'Pendidikan dasar dengan kursus literasi komputer dasar.', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `kunci` varchar(50) NOT NULL,
  `nilai` text DEFAULT NULL,
  `deskripsi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan`
--

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

--
-- Struktur dari tabel `profil`
--

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

--
-- Dumping data untuk tabel `profil`
--

INSERT INTO `profil` (`id`, `nama`, `email`, `whatsapp`, `github`, `profile_image`, `summary`, `location`, `current_status`, `last_updated`) VALUES
(1, 'Muhammad Alfatih', 's.s.6624844@gmail.com', '+62 831-8881-3237', 'https://github.com/alfaragatak87', 'profile_1752498484.png', 'Web Developer dan UI/UX Designer dengan keahlian dalam PHP, JavaScript, dan teknologi web terkini. Saya menciptakan solusi digital yang tidak hanya berfungsi dengan baik, tetapi juga memberikan pengalaman pengguna yang optimal.', 'Lumajang, East Java, Indonesia', 'Mahasiswa', '2025-07-14 13:08:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `proyek`
--

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

--
-- Struktur dari tabel `semester_data`
--

CREATE TABLE `semester_data` (
  `id` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `mata_kuliah` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `skills`
--

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

--
-- Struktur dari tabel `system_backups`
--

CREATE TABLE `system_backups` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `backup_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `testimonials`
--

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

--
-- Dumping data untuk tabel `testimonials`
--

INSERT INTO `testimonials` (`id`, `nama`, `posisi`, `perusahaan`, `testimonial`, `foto`, `aktif`, `tanggal_dibuat`, `rating`) VALUES
(4, 'unknow', '', '', 'ngeri bosssss', '', 1, '2025-07-13 12:36:46', 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `ucapan`
--

CREATE TABLE `ucapan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kehadiran` varchar(50) NOT NULL,
  `pesan` text NOT NULL,
  `tanggal` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `ucapan`
--

INSERT INTO `ucapan` (`id`, `nama`, `kehadiran`, `pesan`, `tanggal`) VALUES
(1, 'dek alfa', 'Tidak Hadir', 'done, 500k', '2026-05-01 07:17:15'),
(2, 'Zainul amin', 'Hadir', 'SEMOGA BAROKAH DAN MANFAAT... AAMIIN', '2026-05-01 22:08:50'),
(3, 'silfi', 'Hadir', 'anjayy akhire rekk, lancar\" ya besssðŸ«¶ðŸ» semoga sakinah mawadah warohmah till jannahðŸ¥°ðŸ¥°', '2026-05-24 13:18:20'),
(4, 'Hilda Widiana', 'Hadir', 'Selamat dan langgeng ðŸ’ðŸ’', '2026-05-24 23:08:02'),
(5, 'Eva', 'Hadir', 'Happy wedding astrii n suami ,langgeng yaaa ðŸ’ðŸ’ž', '2026-05-25 03:13:34'),
(6, 'rosalia', 'Hadir', 'Samawah till jannah ', '2026-05-25 09:19:42'),
(7, 'Ilfi rahma', 'Tidak Hadir', 'Happy wedding ya astrii', '2026-06-05 10:28:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT 'default.png',
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','admin','user') NOT NULL DEFAULT 'user',
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `profile_data` longtext DEFAULT NULL,
  `is_onboarded` tinyint(1) NOT NULL DEFAULT 0,
  `profesi_category` varchar(80) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `nama_lengkap`, `foto_profil`, `password`, `role`, `email`, `phone`, `profile_data`, `is_onboarded`, `profesi_category`, `tgl_lahir`, `last_login`, `created_at`, `status`) VALUES
(1, 'alfa', 'MUHAMMAD ALFATIH', 'alfa_1780284127.png', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin', NULL, NULL, '{\"keahlian\":[{\"nama\":\"php\",\"level\":70,\"kategori\":\"fronend\"},{\"nama\":\"SQL\",\"level\":70,\"kategori\":\"backend\"}],\"portfolio\":[{\"nama\":\"undangan web\",\"url\":\"https:\\/\\/gawe.my.id\\/projects\\/undangan\\/undangan_astri_yufen\",\"deskripsi\":\"\",\"tech\":\"\"}],\"identitas\":{\"nama_sebutan\":\"ALFA\",\"nama_lengkap\":\"MUHAMMAD ALFATIH\",\"profesi\":\"Mahasiswa\",\"tagline\":\"seorang mahasiswa itb widyagama lumajang\",\"email\":\"s.s.6624844@gmail.com\",\"phone\":\"083188813237\",\"location\":\"LUMAJANG, JAWA TIMUR\",\"github\":\"https:\\/\\/github.com\\/alfaragatak87\",\"linkedin\":\"https:\\/\\/www.linkedin.com\\/in\\/muhammad-alfatih-45a5ba262?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app\",\"instagram\":\"https:\\/\\/www.instagram.com\\/alfamuhammad__?igsh=MXdkM2h6am5mcGplbQ==\",\"website\":\"https:\\/\\/gawe.my.id\\/portfolio-alfatih\",\"summary\":\"Mahasiswa aktif (Kelas Malam) dengan latar belakang pendidikan Teknik\\r\\nKomputer dan Jaringan.\\r\\nMemiliki pemahaman yang komprehensif dalam administrasi perkantoran,\\r\\npengelolaan infrastruktur TI, serta tata letak dokumen digital.\\r\\nBerpengalaman dalam menangani korespondensi formal, penyusunan\\r\\nlaporan, pelayanan klien, hingga pengembangan dan pemeliharaan\\r\\nwebsite.\\r\\nBerkomitmen tinggi, adaptif, dan siap mendedikasikan keterampilan\\r\\nsecara profesional untuk mendukung kelancaran administrasi maupun\\r\\noperasional di instansi atau perusahaan tempat saya bekerja.\",\"tampil_publik\":1},\"pendidikan\":[{\"institusi\":\"INSTITUT TEKNOLOGI DAN BISNIS WIDYAGAMA LUMAJANG\",\"gelar\":\"S1 TEKNIK\",\"bidang\":\"INFORMATIKA\",\"tahun_mulai\":\"2024\",\"tahun_selesai\":\"SEKARANG\",\"deskripsi\":\"\"},{\"institusi\":\"SMK MIFTAHUL ISLAM KUNIR\",\"gelar\":\"Teknik Komputer & Jaringan\",\"bidang\":\".\",\"tahun_mulai\":\"2020\",\"tahun_selesai\":\"2023\",\"deskripsi\":\"\"},{\"institusi\":\"MTS SALAFIYAH AL-YASINY PANDANWANGI TEMPEH LUMAJANG\",\"gelar\":\"\",\"bidang\":\"\",\"tahun_mulai\":\"2017\",\"tahun_selesai\":\"2020\",\"deskripsi\":\"\"},{\"institusi\":\"MI SALAFIYAH AL-YASINY PANDANWANGI TEMPEH LUMAJANG\",\"gelar\":\"\",\"bidang\":\"\",\"tahun_mulai\":\"2011\",\"tahun_selesai\":\"2017\",\"deskripsi\":\"\"}],\"pengalaman\":[{\"jabatan\":\"Penyedia Jasa Pengetikan & Desain Presentasi\",\"perusahaan\":\"PANDAWA BUSINESS (Freelance Pribadi)\",\"periode\":\"2024 - sekarang\",\"deskripsi\":\"Mengelola layanan pengetikan dan tata letak dokumen untuk berbagai\\r\\nkebutuhan akademik dan perkantoran secara profesional.\\r\\nBertanggung jawab penuh atas penyuntingan dokumen (laporan, makalah)\\r\\ndengan standar kerapian format (Microsoft Word) yang tinggi dan bebas\\r\\nkesalahan ketik (proofreading).\\r\\nMendesain materi presentasi visual (PowerPoint) yang rapi, informatif, dan\\r\\nprofesional.\"},{\"jabatan\":\"Web & IT Administrator\",\"perusahaan\":\"Proyek Mandiri\",\"periode\":\"Saat ini\",\"deskripsi\":\"Membangun dan mengelola website secara mandiri (termasuk domain\\r\\nutama gawe.my.id).\\r\\nMelakukan pemeliharaan sistem back-end, manajemen DNS, serta\\r\\npengelolaan web hosting untuk memastikan situs beroperasi secara\\r\\noptimal.\"},{\"jabatan\":\"Teknisi IT & Staf Administrasi (PKL)\",\"perusahaan\":\"Global computer, Lumajang\",\"periode\":\"Sep 2022 - Nov 2022\",\"deskripsi\":\"Menyelesaikan program dengan predikat Baik.\\r\\nMenerima dan mencatat keluhan perangkat dari pelanggan serta\\r\\nmenyusun laporan perbaikan barang harian.\\r\\nMembantu proses instalasi software, perakitan, dan pemeliharaan\\r\\nperangkat keras (PC\\/Laptop).\"}]}', 0, NULL, NULL, '2026-06-12 13:45:55', '2026-06-07 16:07:54', 'active'),
(2, 'bapak', 'ZAINUL AMIN', 'default.png', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, '{\"identitas\":{\"nama_sebutan\":\"BAPAK AMIN\",\"nama_lengkap\":\"ZAINUL AMIN\",\"profesi\":\"Karyawan Swasta\",\"tagline\":\"\",\"email\":\"amin78zainul@gmail.com\",\"phone\":\"+6285859553898\",\"location\":\"LUMAJANG, JAWA TIMUR\",\"github\":\"\",\"linkedin\":\"\",\"instagram\":\"\",\"website\":\"\",\"summary\":\"\",\"tampil_publik\":1}}', 0, NULL, NULL, '2026-06-10 18:10:30', '2026-06-07 16:07:54', 'active'),
(3, 'ajay', 'Ajay', 'default.png', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, NULL, 0, NULL, NULL, '2026-06-12 11:45:56', '2026-06-07 16:07:54', 'active');

-- --------------------------------------------------------

--
-- Struktur dari tabel `website_analytics`
--

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

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `admin_roles`
--
ALTER TABLE `admin_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `artikel`
--
ALTER TABLE `artikel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `dokumen`
--
ALTER TABLE `dokumen`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `job_titles`
--
ALTER TABLE `job_titles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indeks untuk tabel `media_library`
--
ALTER TABLE `media_library`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `page_content`
--
ALTER TABLE `page_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_section` (`page`,`section`);

--
-- Indeks untuk tabel `pendidikan`
--
ALTER TABLE `pendidikan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kunci` (`kunci`);

--
-- Indeks untuk tabel `profil`
--
ALTER TABLE `profil`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `proyek`
--
ALTER TABLE `proyek`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `semester_data`
--
ALTER TABLE `semester_data`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `system_backups`
--
ALTER TABLE `system_backups`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `ucapan`
--
ALTER TABLE `ucapan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `website_analytics`
--
ALTER TABLE `website_analytics`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `admin_roles`
--
ALTER TABLE `admin_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `artikel`
--
ALTER TABLE `artikel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dokumen`
--
ALTER TABLE `dokumen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `job_titles`
--
ALTER TABLE `job_titles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `media_library`
--
ALTER TABLE `media_library`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `page_content`
--
ALTER TABLE `page_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `pendidikan`
--
ALTER TABLE `pendidikan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT untuk tabel `profil`
--
ALTER TABLE `profil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `proyek`
--
ALTER TABLE `proyek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `semester_data`
--
ALTER TABLE `semester_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `system_backups`
--
ALTER TABLE `system_backups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `ucapan`
--
ALTER TABLE `ucapan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `website_analytics`
--
ALTER TABLE `website_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `artikel`
--
ALTER TABLE `artikel`
  ADD CONSTRAINT `artikel_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE CASCADE;
--
-- Database: `mckmmukg_utama`
--
CREATE DATABASE IF NOT EXISTS `mckmmukg_utama` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `mckmmukg_utama`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

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

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `role_id`, `email`, `phone`, `profile_image`, `last_login`, `status`, `login_attempts`, `recovery_token`, `recovery_expires`) VALUES
(1, 'admin', 'admin1234', 1, NULL, NULL, NULL, '2025-07-16 11:35:10', 'active', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `admin_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`, `user_id`) VALUES
(1, 1, 'LOGIN', 'User logged in from IP: 140.213.42.215', '140.213.42.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-07 16:09:08', 1),
(2, 2, 'LOGIN', 'User logged in from IP: 103.4.82.154', '103.4.82.154', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 16:54:23', 2),
(3, 1, 'LOGIN', 'User logged in from IP: 103.4.82.154', '103.4.82.154', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-07 17:01:47', 1),
(4, 1, 'LOGIN', 'User logged in from IP: 140.213.241.53', '140.213.241.53', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-06-08 12:21:46', 1),
(5, 1, 'LOGIN', 'User logged in from IP: 140.213.245.219', '140.213.245.219', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 12:42:43', 1),
(6, 1, 'LOGIN', 'User logged in from IP: 140.213.239.239', '140.213.239.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 12:43:50', 1),
(7, 1, 'LOGIN', 'User logged in from IP: 140.213.239.239', '140.213.239.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 12:44:49', 1),
(8, 1, 'LOGIN', 'User logged in from IP: 140.213.239.239', '140.213.239.239', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 13:06:31', 1),
(9, 1, 'LOGIN', 'User logged in from IP: 140.213.246.28', '140.213.246.28', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-06-08 13:16:04', 1),
(10, 1, 'LOGIN', 'User logged in from IP: 140.213.246.28', '140.213.246.28', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-06-08 13:32:48', 1),
(11, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 17:18:37', 1),
(12, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-06-08 17:20:00', 1),
(13, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-08 18:22:57', 1),
(14, 3, 'LOGIN', 'User logged in from IP: 103.182.52.211', '103.182.52.211', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-09 17:51:59', 3),
(15, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 08:40:22', 1),
(16, 2, 'LOGIN', 'User logged in from IP: 103.182.52.212', '103.182.52.212', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-06-10 09:44:08', 2),
(17, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 10:09:44', 1),
(18, 3, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 10:19:49', 3),
(19, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 15:34:00', 1),
(20, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 16:01:53', 1),
(21, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 17:23:47', 1),
(22, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 17:50:11', 1),
(23, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 18:07:36', 1),
(24, 2, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 18:10:30', 2),
(25, 3, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-10 18:14:09', 3),
(26, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-11 02:14:36', 1),
(27, 3, 'LOGIN', 'User logged in from IP: 140.213.48.249', '140.213.48.249', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-06-11 11:14:27', 3),
(28, 1, 'LOGIN', 'User logged in from IP: 140.213.244.36', '140.213.244.36', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 05:47:24', 1),
(29, 1, 'LOGIN', 'User logged in from IP: 112.215.172.151', '112.215.172.151', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 07:16:48', 1),
(30, 1, 'LOGIN', 'User logged in from IP: 112.215.237.44', '112.215.237.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 10:03:35', 1),
(31, 1, 'LOGIN', 'User logged in from IP: 112.215.237.44', '112.215.237.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 10:55:38', 1),
(32, 3, 'LOGIN', 'User logged in from IP: 140.213.40.10', '140.213.40.10', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-12 11:44:47', 3),
(33, 3, 'LOGIN', 'User logged in from IP: 140.213.40.10', '140.213.40.10', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-12 11:45:56', 3),
(34, 1, 'LOGIN', 'User logged in from IP: 140.213.40.10', '140.213.40.10', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', '2026-06-12 11:46:33', 1),
(35, 1, 'LOGIN', 'User logged in from IP: 112.215.153.133', '112.215.153.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-12 13:45:55', 1),
(36, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-13 08:34:13', 1),
(37, 1, 'LOGIN', 'User logged in from IP: 103.182.52.212', '103.182.52.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-13 11:46:32', 1),
(38, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-13 12:24:10', 1),
(39, 1, 'LOGIN', 'User logged in from IP: 103.182.52.213', '103.182.52.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-14 15:42:43', 1),
(40, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-14 15:43:32', 1),
(41, 1, 'LOGIN', 'User logged in from IP: 103.182.52.214', '103.182.52.214', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-06-14 15:45:40', 1),
(42, 3, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-14 18:07:28', 3),
(43, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-14 18:42:07', 1),
(44, 1, 'LOGIN', 'User logged in from IP: 103.182.52.213', '103.182.52.213', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-06-14 18:51:23', 1),
(45, 1, 'LOGIN', 'User logged in from IP: 103.182.52.212', '103.182.52.212', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-06-14 19:31:33', 1),
(46, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-14 19:40:18', 1),
(47, 1, 'LOGIN', 'User logged in from IP: 103.182.52.212', '103.182.52.212', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-06-14 20:06:33', 1),
(48, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-14 22:00:30', 1),
(49, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-14 22:03:14', 1),
(50, 1, 'LOGIN', 'User logged in from IP: 103.182.52.214', '103.182.52.214', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-06-14 22:09:42', 1),
(51, 3, 'LOGIN', 'User logged in from IP: 103.182.52.214', '103.182.52.214', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Mobile Safari/537.36', '2026-06-14 22:10:17', 3),
(52, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 04:32:48', 1),
(53, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 05:52:18', 1),
(54, 1, 'LOGIN', 'User logged in from IP: 103.182.52.210', '103.182.52.210', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 08:22:07', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin_roles`
--

CREATE TABLE `admin_roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `permissions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin_roles`
--

INSERT INTO `admin_roles` (`id`, `role_name`, `permissions`) VALUES
(1, 'Super Admin', '{\"all\":true}'),
(2, 'Editor', '{\"dashboard\":true,\"content\":true,\"media\":true,\"settings\":false,\"users\":false}'),
(3, 'Author', '{\"dashboard\":true,\"content\":{\"view\":true,\"add\":true,\"edit\":true,\"delete\":false},\"media\":{\"view\":true,\"add\":true,\"delete\":false}}');

-- --------------------------------------------------------

--
-- Struktur dari tabel `artikel`
--

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

--
-- Struktur dari tabel `contact_messages`
--

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

--
-- Struktur dari tabel `dokumen`
--

CREATE TABLE `dokumen` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `kategori` enum('Tugas','Sertifikat','CV','Lainnya') NOT NULL,
  `semester` int(11) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dokumen`
--

INSERT INTO `dokumen` (`id`, `nama`, `file`, `kategori`, `semester`, `deskripsi`, `tanggal_upload`) VALUES
(4, 'CV Muhammad Alfatih', 'document_1752432909.pdf', 'CV', 0, '', '2025-07-13 18:55:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `owner_username` varchar(50) NOT NULL,
  `jenis` enum('file','link') DEFAULT 'file',
  `nama_file` varchar(255) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `link_url` text DEFAULT NULL,
  `share_token` varchar(100) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `tanggal_upload` timestamp NULL DEFAULT current_timestamp(),
  `status_cetak` varchar(50) DEFAULT 'Menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `files`
--

INSERT INTO `files` (`id`, `folder_id`, `owner_username`, `jenis`, `nama_file`, `file_path`, `tags`, `link_url`, `share_token`, `is_deleted`, `tanggal_upload`, `status_cetak`) VALUES
(5, 5, 'alfa', 'file', 'BPJS ALFATIH.pdf', 'alfa_1780294770_855_0.pdf', NULL, NULL, NULL, 0, '2026-06-01 06:19:30', 'Selesai'),
(6, 5, 'alfa', 'file', 'KTM UMM.pdf', 'alfa_1780294770_696_1.pdf', NULL, NULL, NULL, 0, '2026-06-01 06:19:30', 'Selesai'),
(7, 5, 'alfa', 'file', 'KTM WIGA ALFATIH.pdf', 'alfa_1780294770_691_2.pdf', NULL, NULL, NULL, 0, '2026-06-01 06:19:30', 'Selesai'),
(8, 5, 'alfa', 'file', 'REKENING BRI.pdf', 'alfa_1780294770_467_3.pdf', NULL, NULL, NULL, 0, '2026-06-01 06:19:30', 'Menunggu'),
(9, 5, 'alfa', 'file', 'SIM C .pdf', 'alfa_1780294770_695_4.pdf', NULL, NULL, NULL, 0, '2026-06-01 06:19:30', 'Menunggu'),
(10, 5, 'alfa', 'file', 'NPWP_Muhammad Alfatih(1).PNG', 'alfa_1780294830_774_0.png', NULL, NULL, NULL, 1, '2026-06-01 06:20:30', 'Menunggu'),
(11, 5, 'alfa', 'file', 'NPWP_MUHAMMAD ALFATIH.pdf', 'alfa_1780294929_555_0.pdf', NULL, NULL, NULL, 0, '2026-06-01 06:22:09', 'Menunggu'),
(12, 5, 'alfa', 'file', 'KARTU KELUARGA ZAINUL AMIN.pdf', 'alfa_1780294960_155_0.pdf', NULL, NULL, NULL, 0, '2026-06-01 06:22:40', 'Selesai'),
(13, 3, 'alfa', 'file', 'PENGEMB.MANAJ & KWIRA USHN.doc', 'alfa_1780509819_800_0.doc', '', NULL, NULL, 0, '2026-06-03 18:03:39', 'Diproses');

-- --------------------------------------------------------

--
-- Struktur dari tabel `folders`
--

CREATE TABLE `folders` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `owner_username` varchar(50) NOT NULL,
  `nama_folder` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-folder',
  `warna` varchar(10) DEFAULT '#22d3ee',
  `deskripsi` text DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deadline_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `folders`
--

INSERT INTO `folders` (`id`, `parent_id`, `owner_username`, `nama_folder`, `icon`, `warna`, `deskripsi`, `is_deleted`, `deadline_date`) VALUES
(1, NULL, 'alfa', 'DATA ALFA', 'fa-folder', '#22d3ee', '.', 0, NULL),
(2, NULL, 'bapak', 'Arsip Warga', 'fa-file-contract', '#22d3ee', 'Scan dokumen KTP dan KK.', 0, NULL),
(3, NULL, 'ajay', 'Lab Modul Coding', 'fa-laptop-code', '#22d3ee', 'Tugas pemrograman sekolah.', 0, NULL),
(4, NULL, 'bapak', 'DOKUMEN KELUARGA', 'fa-folder', '#ff0000', 'KK, KTP, AKTE, BPJS, DLL', 0, NULL),
(5, 4, 'bapak', 'ALFA', 'fa-folder-tree', '#00ff2a', '', 0, NULL),
(6, 4, 'bapak', 'AJAY', 'fa-folder-tree', '#ff0000', '', 0, NULL),
(7, 4, 'bapak', 'BAPAK', 'fa-folder-tree', '#38bdf8', '', 0, NULL),
(8, 4, 'alfa', 'IBUK', 'fa-folder-tree', '#fff700', '', 0, NULL),
(9, 4, 'alfa', 'RIFDA', 'fa-folder-tree', '#ff00d0', '', 0, NULL),
(10, 4, 'bapak', 'IBUK', 'fa-folder-tree', '#ff00ff', '', 0, NULL),
(11, 4, 'bapak', 'RIFDA', 'fa-folder-tree', '#ffffff', '', 0, NULL),
(12, 1, 'alfa', 'a', 'fa-folder', '#000000', '', 0, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_titles`
--

CREATE TABLE `job_titles` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `job_titles`
--

INSERT INTO `job_titles` (`id`, `title`, `urutan`) VALUES
(1, 'Web Developer', 1),
(2, 'UI/UX Designer', 2),
(3, 'Mahasiswa Informatika', 3),
(4, 'Problem Solver', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`) VALUES
(3, 'Digital Marketing'),
(4, 'Programming'),
(2, 'UI/UX Design'),
(1, 'Web Development');

-- --------------------------------------------------------

--
-- Struktur dari tabel `media_library`
--

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

--
-- Struktur dari tabel `notifications`
--

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

--
-- Struktur dari tabel `page_content`
--

CREATE TABLE `page_content` (
  `id` int(11) NOT NULL,
  `page` varchar(100) NOT NULL,
  `section` varchar(100) NOT NULL,
  `content` text DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `page_content`
--

INSERT INTO `page_content` (`id`, `page`, `section`, `content`, `last_updated`) VALUES
(1, 'home', 'hero_title', '', '2025-07-15 17:51:26'),
(2, 'home', 'hero_subtitle', 'MAHASISWA S1 INFORMATIKA', '2025-07-15 17:51:26'),
(3, 'home', 'hero_description', '', '2025-07-15 17:51:26'),
(4, 'home', 'projects_title', '', '2025-07-15 17:51:26'),
(5, 'home', 'projects_subtitle', '', '2025-07-15 17:51:26'),
(6, 'home', 'articles_title', '', '2025-07-15 17:51:26'),
(7, 'home', 'articles_subtitle', '', '2025-07-15 17:51:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pendidikan`
--

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

--
-- Dumping data untuk tabel `pendidikan`
--

INSERT INTO `pendidikan` (`id`, `institusi`, `gelar`, `bidang_studi`, `tahun_mulai`, `tahun_selesai`, `deskripsi`, `urutan`) VALUES
(1, 'ITB Widya Gama Lumajang', 'S1 Informatika', 'Ilmu Komputer', '2023', NULL, 'Fokus pada pengembangan web, algoritma, dan database management.', 1),
(2, 'SMK Miftahul Islam Kunir', 'Teknik Komputer & Jaringan', 'Jaringan Komputer', '2019', '2022', 'Belajar tentang jaringan komputer, troubleshooting hardware, dan pemrograman dasar.', 2),
(3, 'MTs Salafiyah Al-Yasiny', 'MTs', 'Pendidikan Umum', '2016', '2019', 'Pendidikan menengah pertama dengan tambahan studi Islam.', 3),
(4, 'MI Salafiyah Al-Yasiny', 'MI', 'Pendidikan Dasar', '2010', '2016', 'Pendidikan dasar dengan kursus literasi komputer dasar.', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `kunci` varchar(50) NOT NULL,
  `nilai` text DEFAULT NULL,
  `deskripsi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan`
--

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

--
-- Struktur dari tabel `profil`
--

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

--
-- Dumping data untuk tabel `profil`
--

INSERT INTO `profil` (`id`, `nama`, `email`, `whatsapp`, `github`, `profile_image`, `summary`, `location`, `current_status`, `last_updated`) VALUES
(1, 'Muhammad Alfatih', 's.s.6624844@gmail.com', '+62 831-8881-3237', 'https://github.com/alfaragatak87', 'profile_1752498484.png', 'Web Developer dan UI/UX Designer dengan keahlian dalam PHP, JavaScript, dan teknologi web terkini. Saya menciptakan solusi digital yang tidak hanya berfungsi dengan baik, tetapi juga memberikan pengalaman pengguna yang optimal.', 'Lumajang, East Java, Indonesia', 'Mahasiswa', '2025-07-14 13:08:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `proyek`
--

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

--
-- Struktur dari tabel `semester_data`
--

CREATE TABLE `semester_data` (
  `id` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `mata_kuliah` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `skills`
--

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

--
-- Struktur dari tabel `system_backups`
--

CREATE TABLE `system_backups` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `backup_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `testimonials`
--

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

--
-- Dumping data untuk tabel `testimonials`
--

INSERT INTO `testimonials` (`id`, `nama`, `posisi`, `perusahaan`, `testimonial`, `foto`, `aktif`, `tanggal_dibuat`, `rating`) VALUES
(4, 'unknow', '', '', 'ngeri bosssss', '', 1, '2025-07-13 12:36:46', 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `ucapan`
--

CREATE TABLE `ucapan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kehadiran` varchar(50) NOT NULL,
  `pesan` text NOT NULL,
  `tanggal` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `ucapan`
--

INSERT INTO `ucapan` (`id`, `nama`, `kehadiran`, `pesan`, `tanggal`) VALUES
(1, 'dek alfa', 'Tidak Hadir', 'done, 500k', '2026-05-01 07:17:15'),
(2, 'Zainul amin', 'Hadir', 'SEMOGA BAROKAH DAN MANFAAT... AAMIIN', '2026-05-01 22:08:50'),
(3, 'silfi', 'Hadir', 'anjayy akhire rekk, lancar\" ya besssðŸ«¶ðŸ» semoga sakinah mawadah warohmah till jannahðŸ¥°ðŸ¥°', '2026-05-24 13:18:20'),
(4, 'Hilda Widiana', 'Hadir', 'Selamat dan langgeng ðŸ’ðŸ’', '2026-05-24 23:08:02'),
(5, 'Eva', 'Hadir', 'Happy wedding astrii n suami ,langgeng yaaa ðŸ’ðŸ’ž', '2026-05-25 03:13:34'),
(6, 'rosalia', 'Hadir', 'Samawah till jannah ', '2026-05-25 09:19:42'),
(7, 'Ilfi rahma', 'Tidak Hadir', 'Happy wedding ya astrii', '2026-06-05 10:28:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `foto_profil` varchar(255) DEFAULT 'default.png',
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','admin','user') NOT NULL DEFAULT 'user',
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `profile_data` longtext DEFAULT NULL,
  `is_onboarded` tinyint(1) NOT NULL DEFAULT 0,
  `profesi_category` varchar(80) DEFAULT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `nama_lengkap`, `foto_profil`, `password`, `role`, `email`, `phone`, `profile_data`, `is_onboarded`, `profesi_category`, `tgl_lahir`, `last_login`, `created_at`, `status`) VALUES
(1, 'alfa', 'MUHAMMAD ALFATIH', 'alfa_1780284127.png', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'superadmin', NULL, NULL, '{\"keahlian\":[{\"nama\":\"php\",\"level\":\"Pemula\",\"kategori\":\"Pemrograman Web\",\"logo_icon\":\"\"},{\"nama\":\"SQL\",\"level\":\"Pemula\",\"kategori\":\"Pemrograman Web\",\"logo_icon\":\"\"}],\"portfolio\":[],\"identitas\":{\"nama_sebutan\":\"ALFA\",\"nama_lengkap\":\"MUHAMMAD ALFATIH\",\"profesi\":\"Mahasiswa\",\"tagline\":\"seorang mahasiswa itb widyagama lumajang\",\"provinsi\":\"JAWA TIMUR\",\"kabupaten\":\"KABUPATEN LUMAJANG\",\"kecamatan\":\"TEMPEH\",\"desa\":\"PANDANWANGI\",\"email\":\"s.s.6624844@gmail.com\",\"phone\":\"083188813237\",\"github\":\"https:\\/\\/github.com\\/alfaragatak87\",\"linkedin\":\"https:\\/\\/www.linkedin.com\\/in\\/muhammad-alfatih-45a5ba262?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app\",\"instagram\":\"https:\\/\\/www.instagram.com\\/alfamuhammad__?igsh=MXdkM2h6am5mcGplbQ==\",\"website\":\"https:\\/\\/gawe.my.id\\/portfolio-alfatih\",\"summary\":\"\",\"tampil_publik\":1},\"pendidikan\":[{\"institusi\":\"INSTITUT TEKNOLOGI DAN BISNIS WIDYAGAMA LUMAJANG\",\"gelar\":\"S1 TEKNIK\",\"bidang\":\"INFORMATIKA\",\"tahun_mulai\":\"2024\",\"tahun_selesai\":\"SEKARANG\",\"deskripsi\":\"\"},{\"institusi\":\"SMK MIFTAHUL ISLAM KUNIR\",\"gelar\":\"Teknik Komputer & Jaringan\",\"bidang\":\".\",\"tahun_mulai\":\"2020\",\"tahun_selesai\":\"2023\",\"deskripsi\":\"\"},{\"institusi\":\"MTS SALAFIYAH AL-YASINY PANDANWANGI TEMPEH LUMAJANG\",\"gelar\":\"\",\"bidang\":\"\",\"tahun_mulai\":\"2017\",\"tahun_selesai\":\"2020\",\"deskripsi\":\"\"},{\"institusi\":\"MI SALAFIYAH AL-YASINY PANDANWANGI TEMPEH LUMAJANG\",\"gelar\":\"\",\"bidang\":\"\",\"tahun_mulai\":\"2011\",\"tahun_selesai\":\"2017\",\"deskripsi\":\"\"}],\"pengalaman\":[{\"jabatan\":\"Penyedia Jasa Pengetikan & Desain Presentasi\",\"perusahaan\":\"PANDAWA BUSINESS (Freelance Pribadi)\",\"periode\":\"2024 - sekarang\",\"deskripsi\":\"Mengelola layanan pengetikan dan tata letak dokumen untuk berbagai\\r\\nkebutuhan akademik dan perkantoran secara profesional.\\r\\nBertanggung jawab penuh atas penyuntingan dokumen (laporan, makalah)\\r\\ndengan standar kerapian format (Microsoft Word) yang tinggi dan bebas\\r\\nkesalahan ketik (proofreading).\\r\\nMendesain materi presentasi visual (PowerPoint) yang rapi, informatif, dan\\r\\nprofesional.\"},{\"jabatan\":\"Web & IT Administrator\",\"perusahaan\":\"Proyek Mandiri\",\"periode\":\"Saat ini\",\"deskripsi\":\"Membangun dan mengelola website secara mandiri (termasuk domain\\r\\nutama gawe.my.id).\\r\\nMelakukan pemeliharaan sistem back-end, manajemen DNS, serta\\r\\npengelolaan web hosting untuk memastikan situs beroperasi secara\\r\\noptimal.\"},{\"jabatan\":\"Teknisi IT & Staf Administrasi (PKL)\",\"perusahaan\":\"Global computer, Lumajang\",\"periode\":\"Sep 2022 - Nov 2022\",\"deskripsi\":\"Menyelesaikan program dengan predikat Baik.\\r\\nMenerima dan mencatat keluhan perangkat dari pelanggan serta\\r\\nmenyusun laporan perbaikan barang harian.\\r\\nMembantu proses instalasi software, perakitan, dan pemeliharaan\\r\\nperangkat keras (PC\\/Laptop).\"}]}', 0, NULL, NULL, '2026-06-15 08:22:07', '2026-06-07 16:07:54', 'active'),
(2, 'bapak', 'ZAINUL AMIN', 'default.png', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, '{\"identitas\":{\"nama_sebutan\":\"BAPAK AMIN\",\"nama_lengkap\":\"ZAINUL AMIN\",\"profesi\":\"Karyawan Swasta\",\"tagline\":\"\",\"email\":\"amin78zainul@gmail.com\",\"phone\":\"+6285859553898\",\"location\":\"LUMAJANG, JAWA TIMUR\",\"github\":\"\",\"linkedin\":\"\",\"instagram\":\"\",\"website\":\"\",\"summary\":\"\",\"tampil_publik\":1}}', 0, NULL, NULL, '2026-06-10 18:10:30', '2026-06-07 16:07:54', 'active'),
(3, 'ajay', 'ABDURROHMAN ZAINULLOH', 'ajay_1781475876.jpg', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, '{\"identitas\":{\"nama_sebutan\":\"Ajay\",\"nama_lengkap\":\"ABDURROHMAN ZAINULLOH\",\"profesi\":\"Pelajar\",\"tagline\":\"Pelajar Smk\",\"provinsi\":\"JAWA TIMUR\",\"kabupaten\":\"KABUPATEN LUMAJANG\",\"kecamatan\":\"TEMPEH\",\"desa\":\"PANDANWANGI\",\"email\":\"\",\"phone\":\"\",\"github\":\"\",\"linkedin\":\"\",\"instagram\":\"https:\\/\\/www.instagram.com\\/abdrrhmnzainllh?igsh=MXRvcjBkMjBzOGZldA==\",\"website\":\"\",\"summary\":\"\",\"tampil_publik\":1}}', 0, NULL, NULL, '2026-06-14 22:10:17', '2026-06-07 16:07:54', 'active');

-- --------------------------------------------------------

--
-- Struktur dari tabel `website_analytics`
--

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

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `admin_roles`
--
ALTER TABLE `admin_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `artikel`
--
ALTER TABLE `artikel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `dokumen`
--
ALTER TABLE `dokumen`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `job_titles`
--
ALTER TABLE `job_titles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indeks untuk tabel `media_library`
--
ALTER TABLE `media_library`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `page_content`
--
ALTER TABLE `page_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_section` (`page`,`section`);

--
-- Indeks untuk tabel `pendidikan`
--
ALTER TABLE `pendidikan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kunci` (`kunci`);

--
-- Indeks untuk tabel `profil`
--
ALTER TABLE `profil`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `proyek`
--
ALTER TABLE `proyek`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `semester_data`
--
ALTER TABLE `semester_data`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `system_backups`
--
ALTER TABLE `system_backups`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `ucapan`
--
ALTER TABLE `ucapan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `website_analytics`
--
ALTER TABLE `website_analytics`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT untuk tabel `admin_roles`
--
ALTER TABLE `admin_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `artikel`
--
ALTER TABLE `artikel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dokumen`
--
ALTER TABLE `dokumen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `job_titles`
--
ALTER TABLE `job_titles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `media_library`
--
ALTER TABLE `media_library`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `page_content`
--
ALTER TABLE `page_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `pendidikan`
--
ALTER TABLE `pendidikan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT untuk tabel `profil`
--
ALTER TABLE `profil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `proyek`
--
ALTER TABLE `proyek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `semester_data`
--
ALTER TABLE `semester_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `system_backups`
--
ALTER TABLE `system_backups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `ucapan`
--
ALTER TABLE `ucapan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `website_analytics`
--
ALTER TABLE `website_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `artikel`
--
ALTER TABLE `artikel`
  ADD CONSTRAINT `artikel_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
