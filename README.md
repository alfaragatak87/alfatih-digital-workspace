# 🕵️‍♀️ Alfatih Digital Workspace (Traceface)

**Platform Manajemen Terpadu: Cloud Storage, CV Builder, dan Direktori Talenta Profesional.**

Alfatih Digital Workspace (Traceface) adalah sebuah platform web komprehensif yang dirancang dengan estetika modern eksklusif (*Premium Dark SaaS*) untuk memfasilitasi publik dan *admin* dalam manajemen *file*, pembuatan Portofolio/CV secara instan, serta manajemen pengguna dengan fitur tingkat tinggi.

Proyek ini dibangun menggunakan arsitektur **Monolitik PHP Native** yang sangat ringan, memastikan akses super cepat, keamanan tangguh, dan kompatibilitas di segala perangkat (Desktop maupun Mobile).

![Status](https://img.shields.io/badge/Status-Completed-success?style=for-the-badge) ![Engine](https://img.shields.io/badge/Engine-PHP_8.x-blue?style=for-the-badge) ![Database](https://img.shields.io/badge/Database-MySQLi-informational?style=for-the-badge) ![UI](https://img.shields.io/badge/UI-Glassmorphism_Dark-purple?style=for-the-badge)

---

## 🌟 Demonstrasi Alur & Fitur Utama (System Flow)

Platform ini dirancang dengan alur kerja (*flow*) yang sangat jelas dari hulu ke hilir. Berikut adalah demonstrasi perjalanan pengguna (User Journey) dari awal hingga akhir:

### 1. Halaman Pendaratan (Landing Page) & Autentikasi
Pintu gerbang utama platform. Pengunjung akan disambut dengan halaman depan yang profesional.
- **Login / Register:** Pengguna dapat mendaftar atau masuk menggunakan sistem autentikasi yang aman (dilengkapi *password hashing*).
- **Akses Publik:** Pengunjung umum dapat mencari portofolio *freelancer*/*talent* tanpa harus *login*.

### 2. Dasbor Utama (Main Dashboard)
Pusat kendali (Command Center) setelah pengguna berhasil *login*.
- **Desain Premium:** Mengusung tema *Dark Mode* dengan gradasi ungu/indigo dan efek *glassmorphism* (kaca).
- **Statistik Cepat:** Menampilkan ringkasan penyimpanan yang digunakan, jumlah *file*, dan status kelengkapan profil.
- **Navigasi Cepat:** Tombol aksi langsung ke *Workspace* (Drive) dan *CV Builder*.

### 3. Workspace / Pengelola File (Google Drive Clone)
Fitur *Cloud Storage* pribadi bergaya *SaaS Premium*.
- **Manajemen File & Folder:** Unggah (*upload*), buat folder baru, ganti nama, hapus, dan pindahkan *file*.
- **Mode Tampilan Ganda:** Dukungan transisi mulus antara **Grid View** (Kartu besar dengan *preview* gambar) dan **List View** (Daftar tabel padat).
- **Context Menu:** Klik kanan interaktif pada *file*/*folder* memunculkan menu *dropdown* mewah.
- **Batas Penyimpanan:** Sistem dilengkapi kalkulasi kuota memori (*storage limit*).

### 4. Pembuat CV & Profil (CV Builder)
Fitur magis untuk membangun halaman portofolio secara otomatis.
- **Formulir Dinamis:** Pengguna mengisi data diri, pendidikan, pengalaman, dan keahlian (*skills*).
- **Penyimpanan JSON:** Semua struktur CV kompleks disimpan dalam basis data secara dinamis menggunakan payload JSON untuk fleksibilitas tinggi.
- **Real-time:** Setiap perubahan profil langsung diterapkan pada halaman publik.

### 5. Portofolio Publik (Public Showcase)
Hasil akhir dari *CV Builder*. Halaman ini berfungsi sebagai kartu nama digital profesional.
- **URL Khusus:** Dapat diakses oleh *recruiter* atau klien melalui parameter URL khusus (contoh: `?portfolio=username`).
- **Desain Responsif:** *Layout* CV diatur sedemikian rupa agar sangat mudah dibaca di layar HP maupun laptop.

### 6. Mode Admin (God Mode)
Hak akses tertinggi (*Super Admin*) untuk mengelola ekosistem.
- **Manajemen Pengguna:** Admin dapat melihat daftar seluruh pengguna, mengedit status, atau menghapus akun pelanggar.
- **Pemantauan Kapasitas:** Admin dapat melihat beban memori di *server*.

---

## 🏗️ Peta Perjalanan Pengguna & Fitur Aplikasi (User Journey)

Bagian ini memetakan secara keseluruhan **Arsitektur Lengkap (Mega Flowchart)** dari setiap kemungkinan aksi, rute, dan fitur yang bisa dilakukan oleh pengguna di dalam ekosistem Alfatih Digital Workspace.

> [!TIP]
> Garis putus-putus bercahaya pada bagan di bawah ini merepresentasikan **Alur Perjalanan Pengguna** dari awal menekan tombol hingga fitur selesai diproses di _database_.

### 🗺️ Master Flowchart: Alur Seluruh Fitur Sistem (Kompleksitas Penuh)

Bagan ini menunjukkan percabangan rumit dari `index.php` (sebagai pengatur pusat) ke seluruh fitur aplikasi, termasuk manajemen sesi, hak akses tingkat dewa (*Super Admin*), penyimpanan file, dan pembuat CV dinamis.

```mermaid
flowchart TD
    %% Gaya Desain Node Gelap & Bercahaya
    classDef startEnd fill:#0f172a,stroke:#3b82f6,stroke-width:3px,color:#fff,rx:10,ry:10;
    classDef page fill:#1e293b,stroke:#8b5cf6,stroke-width:2px,color:#fff;
    classDef btn fill:#334155,stroke:#f59e0b,stroke-width:2px,color:#f59e0b,shape:diamond;
    classDef process fill:#1e1b4b,stroke:#ec4899,stroke-width:2px,color:#fff;
    classDef finish fill:#020617,stroke:#10b981,stroke-width:2px,color:#10b981;
    classDef warning fill:#450a0a,stroke:#ef4444,stroke-width:2px,color:#f87171;

    Awal(["🌐 Pengunjung Akses Web"]):::startEnd --> CekSesiAwal{"Sistem Mengecek <br/>Session Login"}:::btn
    
    %% ================= JALUR PUBLIK ================= %%
    CekSesiAwal -- "Belum Login" --> JalurPublik{"Apakah URL memiliki <br/> parameter ?portfolio= ?"}:::btn
    JalurPublik -- "Ya" --> CekPortfolio["Sistem Mencari Username di Database"]:::process
    CekPortfolio --> HasilPortfolio{"Data Ditemukan?"}:::btn
    HasilPortfolio -- "Tidak" --> Error404["Tampilkan Halaman 404 Not Found"]:::warning
    HasilPortfolio -- "Ya" --> RenderCVPublik["Ubah Data JSON Menjadi HTML Visual"]:::finish
    RenderCVPublik --> TampilCV(["Halaman CV / Portofolio Publik Ditampilkan"]):::startEnd
    
    JalurPublik -- "Tidak Ada" --> HalDepan["Menampilkan Halaman Pendaratan (Landing Page)"]:::page
    HalDepan --> AksiDepan{"Tombol di Halaman Depan"}:::btn
    AksiDepan -- "Klik Tombol Login" --> FormLogin["Mengisi Email & Password"]:::process
    FormLogin --> ValidasiLogin{"Sistem Cek Database"}:::btn
    ValidasiLogin -- "Gagal" --> PesanError["Muncul Notifikasi Merah (Gagal)"]:::warning --> HalDepan
    ValidasiLogin -- "Berhasil" --> SetSesi["Membuat Sesi Login"]:::process --> MasukDasbor
    
    AksiDepan -- "Klik Tombol Daftar" --> FormData["Mengisi Identitas Baru"]:::process
    FormData --> CekEmail{"Apakah Email <br/>Sudah Terdaftar?"}:::btn
    CekEmail -- "Sudah" --> PeringatanEmail["Peringatan: Email Telah Digunakan"]:::warning --> HalDepan
    CekEmail -- "Belum" --> BuatAkun["Akun Dibuat di Database"]:::finish --> SetSesi
    
    %% ================= JALUR DASBOR & SUPER ADMIN ================= %%
    CekSesiAwal -- "Sudah Login" --> MasukDasbor["Menampilkan Halaman Dasbor Utama"]:::page
    MasukDasbor --> HakAkses{"Apakah Level Akun <br/>Super Admin?"}:::btn
    
    HakAkses -- "Ya" --> TampilMenuAdmin["Membuka Akses Menu 'Kelola Pengguna'"]:::page
    TampilMenuAdmin --> AksiAdmin{"Pilihan Aksi Admin"}:::btn
    AksiAdmin -- "Klik Edit Pengguna" --> FormEdit["Ubah Nama/Password User Lain"]:::process --> UpdateUser["Data Berhasil Diperbarui"]:::finish
    AksiAdmin -- "Klik Hapus Pengguna" --> KonfirmasiHapusAkun{"Yakin Ingin Menghapus?"}:::btn
    KonfirmasiHapusAkun -- "Batal" --> TampilMenuAdmin
    KonfirmasiHapusAkun -- "Ya" --> EksekusiHapus["Seluruh Data Pengguna & File Terhapus"]:::finish
    
    HakAkses -- "Bukan (Admin Biasa)" --> MenuBiasa{"Pilihan Modul Aplikasi"}:::btn
    
    %% ================= JALUR PEMBUAT CV ================= %%
    MenuBiasa -- "Klik Buat CV" --> ModulCV["Membuka Halaman Pembuat CV"]:::page
    ModulCV --> AksiCV{"Interaksi Formulir CV"}:::btn
    AksiCV -- "Isi Profil Dasar" --> KetikProfil["Mengetik Nama, Gelar, Deskripsi"]:::process
    AksiCV -- "Klik Tambah Pendidikan" --> FormEdukasi["Mengisi Sekolah & Tahun Lulus"]:::process
    AksiCV -- "Klik Tambah Keahlian" --> FormSkill["Menambah Label Keahlian Baru"]:::process
    KetikProfil & FormEdukasi & FormSkill --> TombolSimpanCV{"Klik 'Simpan Profil'"}:::btn
    TombolSimpanCV --> CompileJSON["JavaScript Mengonversi Data ke Paket JSON"]:::process
    CompileJSON --> SimpanKeDB["Menyimpan JSON ke Kolom profile_data"]:::finish
    SimpanKeDB --> SuksesCV(["Profil CV Berhasil Diperbarui secara Real-time!"]):::startEnd
    
    %% ================= JALUR PENGELOLA FILE (DRIVE) ================= %%
    MenuBiasa -- "Klik Workspace Drive" --> ModulDrive["Membuka Halaman Pengelola File"]:::page
    ModulDrive --> AksiDrive{"Pilihan Tindakan Drive"}:::btn
    
    AksiDrive -- "Klik Buat Folder" --> KetikFolder["Input Nama Folder"]:::process --> FolderBaru["Folder Virtual Dibuat"]:::finish
    
    AksiDrive -- "Seret / Upload File" --> CekLimit{"Cek Kapasitas <br/>(Sisa Storage)"}:::btn
    CekLimit -- "Penuh" --> ToastGagal["Muncul Pesan: Storage Penuh!"]:::warning
    CekLimit -- "Masih Cukup" --> CekEkstensi{"Apakah Ekstensi Aman? <br/>(Bukan .php/.exe)"}:::btn
    CekEkstensi -- "Berbahaya" --> ToastBahaya["Pesan: Ekstensi Tidak Diizinkan!"]:::warning
    CekEkstensi -- "Aman" --> SimpanFisik["File Dipindahkan ke Folder /unggahan"]:::finish
    SimpanFisik --> InsertMeta["Informasi File Dicatat ke Database"]:::process --> TampilBaru(["Muncul di Antarmuka Drive"]):::startEnd
    
    AksiDrive -- "Klik Kanan File" --> KonteksMenu{"Menu Pilihan Tindakan"}:::btn
    KonteksMenu -- "Ganti Nama" --> InputNamaBaru["Ketik Nama Baru"]:::process --> TersimpanNama["Nama Diperbarui"]:::finish
    KonteksMenu -- "Buat Tautan (Share)" --> GenerateLink["Membuat URL Rahasia"]:::process --> CopyLink["Tautan Disalin ke Clipboard"]:::finish
    KonteksMenu -- "Hapus File" --> KonfirmasiDel{"Konfirmasi Hapus"}:::btn
    KonfirmasiDel -- "Batal" --> ModulDrive
    KonfirmasiDel -- "Ya" --> DeleteFisik["File Dihapus dari Server & Database"]:::finish
    
    %% ================= KELUAR ================= %%
    MenuBiasa -- "Klik Keluar" --> AksiLogout["Menghapus Sesi (Session Destroy)"]:::process
    AksiLogout --> HalDepan

    linkStyle default stroke-width:2px;
```

## 📂 Struktur Direktori Tingkat Lanjut

Proyek ini dibangun berdasarkan prinsip *Separation of Concerns* (Pemisahan Tanggung Jawab) antara sisi antarmuka (*Front-end View*), logika pemrosesan (*Backend Action*), dan *routing* utama (*Controller*). Berikut adalah rincian lengkap struktur direktori tanpa masalah penjajaran spasi:

```text
hosting/
│
├── [Sistem Utama]
│   ├── index.php                  - Front Controller: Mengatur 100% routing, sesi login, dan HTTP requests.
│   ├── manifest.json              - Web App Manifest: Konfigurasi nama, ikon, dan tema saat diinstall (PWA).
│   └── sw.js                      - Service Worker: Bertugas melakukan caching file agar web bisa offline (PWA).
│
├── aksi/                          - Direktori Backend (Memproses data & Query ke MySQL)
│   ├── aksi_autentikasi.php       - Menangani proses Login, Registrasi, Logout, dan Hash Password.
│   ├── aksi_file.php              - Menangani CRUD file Workspace (Upload, Rename, Hapus, Pindah).
│   ├── aksi_pengguna.php          - [God Mode] Menangani operasi Admin untuk menghapus/edit akun lain.
│   └── aksi_profil.php            - Menangkap input Form CV Builder dan mengubahnya menjadi Payload JSON.
│
├── aset/                          - Direktori Front-end (Resource Statis UI)
│   ├── css/
│   │   └── style.css              - Styling utama selain framework, mengatur Glassmorphism & Dark Mode.
│   ├── js/
│   │   └── app.js                 - Skrip AJAX (Vanilla JS) untuk asinkronisasi Drive & Drag-n-Drop.
│   └── images/                    - Menyimpan ikon web, logo SVG, dan placeholder gambar.
│
├── tampilan/                      - Direktori View (Antarmuka Pengguna HTML/PHP)
│   ├── dasbor/                    - Modul Internal (Hanya bisa diakses setelah Login)
│   │   ├── beranda.php            - Dasbor Pusat: Menampilkan statistik, kuota, & shortcut.
│   │   ├── pembuat_cv.php         - CV Builder: Form dinamis untuk membuat Portofolio (Pendidikan, Keahlian).
│   │   ├── pengelola_file.php     - Google Drive Clone: Antarmuka cloud storage, grid/list view.
│   │   └── pengelola_pengguna.php - God Mode Panel: Antarmuka khusus Superadmin mengelola pengguna.
│   │
│   ├── halaman/                   - Modul Publik (Bisa diakses siapa saja)
│   │   ├── halaman_pendaratan.php - Landing Page: Pintu depan pemasaran dan perkenalan platform.
│   │   └── halaman_portofolio.php - Public Showcase: Hasil akhir CV pengguna yang ditampilkan indah ke klien.
│   │
│   └── komponen/                  - Modul Parsial (Potongan UI yang dipanggil berulang-ulang)
│       ├── navbar.php             - Navigasi Atas (Top-bar), breadcrumbs, profile dropdown.
│       ├── sidebar.php            - Menu Samping, Navigasi modul (Dashboard, Workspace, dll).
│       └── modal.php              - Kumpulan jendela popup (Modal) untuk notifikasi/konfirmasi.
│
├── unggahan/                      - [Directory Server] Penyimpanan fisik untuk semua file & foto pengguna.
└── README.md                      - Dokumentasi Utama Proyek (File ini).
```

---
## 📱 Siap Ekosistem Mobile (Android Integration)

Meskipun ini adalah sistem Web PHP, arsitekturnya telah disiapkan sebagai "Nyawa Utama" untuk aplikasi Mobile (Android/iOS):

- **PWA (Progressive Web App)**: Keberadaan `manifest.json` dan `sw.js` memungkinkan situs ini di- *install* langsung dari Google Chrome ke *homescreen* HP menyerupai aplikasi *native* tanpa perlu masuk Play Store.
- **Android WebView Wrapper**: Desain antarmuka sudah 100% responsif (*Mobile First*). Aplikasi web ini bisa dibungkus menggunakan Android Studio (Java/Kotlin) ke dalam komponen `WebView` dan di *build* menjadi format `.apk` / `.aab` yang sangat ringan.

---

## ⚙️ Panduan Instalasi & Deployment Terlengkap

Berikut adalah panduan *step-by-step* komprehensif untuk menjalankan aplikasi ini baik di lingkungan komputer lokal (Localhost) maupun peladen (*Server/Hosting*).

### 🖥️ Persyaratan Sistem (System Requirements)
Pastikan lingkungan *server* Anda memenuhi spesifikasi minimum berikut:
- **Web Server:** Apache (direkomendasikan) atau Nginx.
- **PHP Version:** PHP 8.0 atau yang lebih baru (Aplikasi ditulis dengan gaya Native PHP 8).
- **Database:** MySQL 5.7+ atau MariaDB 10+.
- **Ekstensi PHP Wajib:** `mysqli`, `fileinfo` (untuk pengecekan tipe file), `json` (untuk portofolio), dan `mbstring`.

### 🚀 A. Instalasi di Komputer Lokal (XAMPP / Laragon)

1. **Unduh Repositori**
   - Lakukan *Clone* repositori GitHub ini atau unduh dalam bentuk `.zip`.
   - Pindahkan/ekstrak seluruh folder proyek ini (misalnya dengan nama folder `workspace`) ke dalam direktori publik server lokal Anda:
     - Pengguna XAMPP: `C:/xampp/htdocs/workspace`
     - Pengguna Laragon: `C:/laragon/www/workspace`

2. **Konfigurasi Basis Data (Database)**
   - Buka **phpMyAdmin** (biasanya di `http://localhost/phpmyadmin`).
   - Buat *database* baru dengan nama `db_workspace` (atau nama lain sesuai selera).
   - *Import* file kerangka _database_ (`.sql`) jika disertakan di dalam *repository* ini.
   - **Catatan:** Jika file `.sql` tidak tersedia, aplikasi secara otomatis akan membuat tabel yang dibutuhkan jika Anda melakukan penyesuaian koding instalasi otomatis.

3. **Konfigurasi Variabel Sistem (Koneksi Database)**
   - Buka file `index.php` menggunakan *text editor* (VSCode, Sublime, dll).
   - Cari baris paling atas (Baris ke-20 hingga 25), dan sesuaikan kredensial berikut dengan MySQL lokal Anda:
     ```php
     // Konfigurasi Database Lokal
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root'); // Default XAMPP/Laragon biasanya 'root'
     define('DB_PASS', '');     // Default XAMPP biasanya kosong (tanpa spasi)
     define('DB_NAME', 'db_workspace'); // Sesuaikan dengan nama DB yang dibuat di langkah 2
     ```

4. **Pengaturan Izin Akses Direktori (Folder Permissions)**
   - Pastikan folder `unggahan/` memiliki izin baca dan tulis (*Read & Write*). Di Windows hal ini otomatis, namun jika Anda menggunakan Mac/Linux pastikan permissionnya adalah `775` atau `777` dengan perintah terminal: `chmod -R 777 unggahan/`.

5. **Akses Aplikasi**
   - Buka browser (Google Chrome / Edge).
   - Akses URL: `http://localhost/workspace` (sesuaikan dengan nama folder Anda).
   - Silakan register akun pertama Anda, akun pertama otomatis akan menjadi Super Admin (tergantung logika di *source code*).

---

### 🌍 B. Panduan Deployment ke Hosting / cPanel (Produksi)

Jika Anda ingin meng-*online*-kan aplikasi ini, langkahnya sedikit berbeda dan butuh pengamanan ekstra:

1. **Upload File ke Server**
   - Kompres semua file ke dalam bentuk `.zip`.
   - Buka **File Manager** di cPanel Anda, masuk ke folder `public_html` (atau direktori *subdomain*).
   - *Upload* dan *Extract* file `.zip` tersebut.

2. **Konfigurasi Database Server**
   - Buka menu **MySQL® Databases** di cPanel.
   - Buat *Database* baru dan *User* baru, lalu sambungkan *User* tersebut ke *Database* dengan memberikan **ALL PRIVILEGES**.
   - Ingat dengan baik *Username*, *Password Database*, dan *Nama Database* tersebut.

3. **Update Koneksi di `index.php`**
   - Sama seperti lokal, ubah baris konfigurasi di `index.php` dengan kredensial cPanel Anda:
     ```php
     define('DB_HOST', 'localhost'); // Tetap localhost untuk cPanel
     define('DB_USER', 'u1234567_admin'); // Username DB Hosting Anda
     define('DB_PASS', 'PasswordSuperKuat123!'); // Password DB Hosting
     define('DB_NAME', 'u1234567_workspace'); // Nama DB Hosting Anda
     ```

4. **Tips Keamanan (Sangat Penting)**
   - Karena file di-*upload* di ruang publik (`public_html`), sangat disarankan untuk membuat file `.htaccess` (jika belum ada) di dalam folder root aplikasi untuk mencegah orang dari luar melacak atau mengunduh direktori/file penting:
     ```apache
     Options -Indexes
     ```
   - Pastikan folder `unggahan/` diubah *permission*-nya menjadi `0755` via cPanel File Manager.

5. **Uji Coba Live**
   - Kunjungi nama domain Anda secara langsung (misal: `https://namadomainanda.com`).
   - Coba unggah file sebesar 2MB untuk memastikan fungsi *Upload* berjalan normal dan tidak dicekal oleh pembatasan *upload_max_filesize* di PHP cPanel Anda.

---
*Didesain dan dikembangkan dengan ❤️ untuk masa depan kolaborasi digital.*
