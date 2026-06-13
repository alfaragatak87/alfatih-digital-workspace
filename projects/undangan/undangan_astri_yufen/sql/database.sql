-- Membuat database jika belum ada
CREATE DATABASE IF NOT EXISTS db_undangan;

-- Menggunakan database tersebut
USE db_undangan;

-- Membuat tabel untuk menyimpan form ucapan dan RSVP tamu
CREATE TABLE IF NOT EXISTS ucapan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    kehadiran VARCHAR(50) NOT NULL,
    pesan TEXT NOT NULL,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);