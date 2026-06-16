<?php
// +------------------------------------------------------------------------------+
// |  FILE: index.php                                                             |
// |                                                                              |
// |  DESKRIPSI:                                                                  |
// |  File utama (Entry Point) yang menghubungkan seluruh arsitektur sistem baru. |
// |  Berfungsi sebagai pengatur urutan eksekusi, mulai dari koneksi database,    |
// |  pemeriksaan sesi autentikasi, hingga menampilkan antarmuka dasbor utama.    |
// |                                                                              |
// |  KONEKSI & RELASI:                                                           |
// |  - Memuat 00_sistem_inti/ untuk koneksi dan fungsi global.                   |
// |  - Memuat 01_autentikasi/ untuk menangani login/logout.                      |
// |  - Memuat 02_dasbor_utama/ dan modul lainnya untuk merender antarmuka.       |
// |                                                                              |
// |  CATATAN:                                                                    |
// |  Seluruh logika dan antarmuka telah diekstrak ke dalam struktur folder       |
// |  bernomor (00 s/d 09) agar kode lebih rapi, modular, dan mudah dikelola.     |
// +------------------------------------------------------------------------------+

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ============================================================
// ALFATIH DIGITAL WORKSPACE
// Version 5.0 — Full Redesign: Modular System
// Engine: Native PHP 8.x | DB: MySQLi OOP
// ============================================================

require_once '00_sistem_inti/1_koneksi_database.php';
require_once '00_sistem_inti/3_fungsi_pembantu.php';
require_once '01_autentikasi/1_proses_cek_login.php';
require_once '06_halaman_publik/1_tampilan_cv_publik.php';
require_once '02_dasbor_utama/1_pengaturan_sesi.php';
require_once '02_dasbor_utama/2_tampilan_dasbor.php';
