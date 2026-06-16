@echo off
echo ========================================================
echo Memulai proses sinkronisasi otomatis ke GitHub...
echo ========================================================

:: Pindah ke direktori repository
cd /d C:\hosting

:: Menambahkan semua perubahan file
git add .

:: Menyimpan perubahan dengan pesan otomatis (menggunakan tanggal ^& jam)
set timestamp=%DATE% %TIME%
git commit -m "Auto update: %timestamp%"

:: Mengirim perubahan ke GitHub
git push origin main

echo ========================================================
echo Selesai! Jika tidak ada error merah di atas, kode Anda 
echo sudah berhasil di-update ke GitHub.
echo ========================================================
pause
