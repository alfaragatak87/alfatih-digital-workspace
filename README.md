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

Bagian ini memetakan secara detail setiap tombol yang dapat diklik oleh pengguna, ke mana arahnya, dan apa yang terjadi di balik layar menggunakan bahasa yang mudah dipahami (tanpa bahasa pemrograman rumit).

> [!TIP]
> Garis putus-putus bercahaya pada bagan di bawah ini merepresentasikan **Alur Perjalanan Pengguna** dari awal menekan tombol hingga fitur selesai diproses.

### 1. Alur Masuk (Pintu Gerbang & Akses Utama)
Memetakan perjalanan pertama kali seseorang mengunjungi website, dari mulai melihat halaman depan, mendaftar, hingga berhasil masuk ke Dasbor.

```mermaid
flowchart TD
    %% Gaya Desain Node Gelap & Bercahaya
    classDef startEnd fill:#0f172a,stroke:#3b82f6,stroke-width:3px,color:#fff,rx:10,ry:10;
    classDef page fill:#1e293b,stroke:#8b5cf6,stroke-width:2px,color:#fff;
    classDef btn fill:#334155,stroke:#f59e0b,stroke-width:2px,color:#f59e0b,shape:diamond;
    classDef process fill:#020617,stroke:#10b981,stroke-width:2px,color:#10b981;

    Mulai(["🌐 Pengunjung Membuka Website"]):::startEnd --> CekStatus{"Apakah Pengunjung <br/>Sudah Login Sebelumnya?"}:::btn
    
    CekStatus -- "Belum" --> HalamanDepan["Berada di Halaman Pendaratan (Depan)"]:::page
    CekStatus -- "Sudah" --> Dasbor["Langsung Masuk ke Dasbor Utama"]:::page
    
    %% Alur Publik
    HalamanDepan --> TombolLihatCV{"Membuka Tautan CV <br/>Milik Orang Lain"}:::btn
    TombolLihatCV -- "Klik Tautan" --> LihatCV["Menampilkan Halaman CV/Portofolio Publik"]:::page
    
    %% Alur Login / Daftar
    HalamanDepan --> TombolMasuk{"Klik Tombol <br/>'Masuk' atau 'Daftar'"}:::btn
    TombolMasuk --> IsiForm["Mengisi Email & Kata Sandi"]:::process
    IsiForm --> CekData["Sistem Memeriksa Kecocokan Akun"]:::process
    
    CekData -- "Kata Sandi Salah" --> PesanGagal["Menampilkan Peringatan Merah"]:::page
    PesanGagal --> TombolMasuk
    
    CekData -- "Berhasil" --> Dasbor
    
    %% Alur Interaksi Dasbor
    Dasbor --> PilihanDasbor{"Menu Dasbor Utama"}:::btn
    PilihanDasbor -- "Klik 'Drive Storage'" --> KeDrive["Membuka Halaman Pengelola File (Drive)"]:::page
    PilihanDasbor -- "Klik 'Buat CV'" --> KeCV["Membuka Halaman Pembuat CV"]:::page
    PilihanDasbor -- "Klik 'Keluar'" --> Logout["Sistem Mengeluarkan Akun"]:::process
    Logout --> HalamanDepan

    linkStyle default stroke:#00e5ff,stroke-width:2px,stroke-dasharray: 15 250,animation: dash 2s linear infinite;
```

### 2. Alur Pengelola File (Drive Storage Pribadi)
Memetakan seluruh fitur dan tombol yang ada di dalam menu Workspace/Drive, mulai dari mengunggah file hingga menghapus file.

```mermaid
flowchart TD
    classDef page fill:#0f172a,stroke:#0ea5e9,stroke-width:2px,color:#fff;
    classDef btn fill:#1e293b,stroke:#eab308,stroke-width:2px,color:#fff,shape:diamond;
    classDef process fill:#1e1b4b,stroke:#8b5cf6,stroke-width:2px,color:#fff;
    classDef finish fill:#020617,stroke:#10b981,stroke-width:2px,color:#10b981;

    Drive(["📂 Berada di Halaman Drive / Workspace"]):::page --> TombolDrive{"Pilih Aksi / Tombol"}:::btn
    
    %% Alur Upload File
    TombolDrive -- "Klik Tombol 'Unggah File'" --> PilihFile["Memilih File dari Komputer"]:::process
    PilihFile --> CekPenyimpanan["Sistem Mengecek Sisa Ruang Penyimpanan"]:::process
    
    CekPenyimpanan -- "Penyimpanan Penuh" --> GagalUpload["Pesan Gagal: 'Ruang Tidak Cukup'"]:::page
    CekPenyimpanan -- "Sisa Ruang Cukup" --> CekBahaya["Sistem Mengecek Apakah File Berbahaya (Virus)"]:::process
    
    CekBahaya -- "File Terlarang" --> GagalUpload
    CekBahaya -- "File Aman" --> SimpanFile["File Berhasil Disimpan ke Server"]:::finish
    SimpanFile --> TampilBaru["Muncul Otomatis di Daftar File (Selesai)"]:::page
    
    %% Alur Buat Folder
    TombolDrive -- "Klik Tombol 'Buat Folder'" --> KetikFolder["Mengetik Nama Folder Baru"]:::process
    KetikFolder --> BuatFolder["Folder Baru Berhasil Dibuat"]:::finish
    BuatFolder --> TampilBaru
    
    %% Alur Klik Kanan
    TombolDrive -- "Klik Kanan pada File" --> MenuKonteks{"Menu Pilihan File"}:::btn
    MenuKonteks -- "Klik 'Ganti Nama'" --> Rename["Ubah Teks Nama File"]:::process --> TampilBaru
    MenuKonteks -- "Klik 'Bagikan'" --> Share["Membuat Tautan (Link) Rahasia"]:::finish --> Salin["Pengguna Menyalin Tautan"]:::page
    MenuKonteks -- "Klik 'Hapus'" --> KonfirmasiHapus{"Apakah Anda Yakin?"}:::btn
    KonfirmasiHapus -- "Ya, Hapus" --> Terhapus["File Dihapus Permanen"]:::finish --> TampilBaru

    linkStyle default stroke:#facc15,stroke-width:2px,stroke-dasharray: 15 250,animation: dash 2s linear infinite;
```

### 3. Alur Pembuat CV Digital & Portofolio (CV Builder)
Memetakan perjalanan dari mengisi data pribadi yang kosong hingga menghasilkan halaman CV profesional yang siap dibagikan ke perusahaan.

```mermaid
flowchart TD
    classDef page fill:#0f172a,stroke:#ec4899,stroke-width:2px,color:#fff;
    classDef btn fill:#1e293b,stroke:#8b5cf6,stroke-width:2px,color:#fff,shape:diamond;
    classDef process fill:#1e1b4b,stroke:#f59e0b,stroke-width:2px,color:#fff;
    classDef finish fill:#022c22,stroke:#14b8a6,stroke-width:2px,color:#fff;

    HalamanCV(["📝 Berada di Halaman Pembuat CV"]):::page --> FormProfil["Mengisi Nama, Deskripsi Diri, & Kontak"]:::process
    
    FormProfil --> AksiKeahlian{"Tombol 'Tambah Keahlian'"}:::btn
    AksiKeahlian -- "Klik Tambah" --> IsiSkill["Mengetik Keahlian (Contoh: Desain Grafis)"]:::process
    
    FormProfil --> AksiPendidikan{"Tombol 'Tambah Pendidikan'"}:::btn
    AksiPendidikan -- "Klik Tambah" --> IsiSekolah["Mengisi Nama Sekolah & Tahun Lulus"]:::process
    
    FormProfil --> AksiPengalaman{"Tombol 'Tambah Pengalaman'"}:::btn
    AksiPengalaman -- "Klik Tambah" --> IsiKerja["Mengisi Riwayat Pekerjaan Sebelumnya"]:::process
    
    IsiSkill & IsiSekolah & IsiKerja --> TombolSimpan{"Klik Tombol 'Simpan Profil'"}:::btn
    
    TombolSimpan -- "Sedang Menyimpan..." --> KemasData["Sistem Menyusun Semua Data Menjadi Satu Paket"]:::process
    KemasData --> SimpanSelesai["Data Sukses Tersimpan di Server"]:::finish
    
    SimpanSelesai --> TampilTombolLihat{"Tombol 'Lihat Portofolio Saya' Muncul"}:::btn
    TampilTombolLihat -- "Klik Lihat" --> BukaCV["Membuka Halaman Desain CV Profesional"]:::finish
    
    BukaCV --> Bagikan["Tautan Halaman CV Siap Dibagikan ke Perusahaan / Klien (Selesai)"]:::page

    linkStyle default stroke:#ec4899,stroke-width:2px,stroke-dasharray: 15 250,animation: dash 2s linear infinite;
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

## ⚙️ Panduan Instalasi Lokal

1. **Persiapan:** Instal XAMPP / Laragon di komputer Anda.
2. **Kloning Repositori:** Pindahkan seluruh folder `hosting/` ke dalam folder `htdocs/` (atau direktori root server lokal Anda).
3. **Database:** Buat *database* baru di MySQL (misal: `db_workspace`) dan eksekusi skema *database* (import file `.sql` jika tersedia).
4. **Konfigurasi:** Buka file `index.php` menggunakan *text editor* atau IDE Anda.
5. Sesuaikan parameter koneksi pada konfigurasi sistem di baris awal:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'db_workspace');
   ```
6. **Selesai:** Buka *browser* kesayangan Anda dan akses `http://localhost/hosting`. Sistem siap digunakan!

---
*Didesain dan dikembangkan dengan ❤️ untuk masa depan kolaborasi digital.*
