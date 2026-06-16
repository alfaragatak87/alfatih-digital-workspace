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

## 🏗️ Arsitektur & Alur Kerja Sistem (System Workflows)

Proyek ini dibangun dengan arsitektur **Monolitik (Front Controller)**, di mana `index.php` bertindak sebagai pintu masuk tunggal (Single Entry Point) yang mengatur seluruh *routing*, keamanan, dan manajemen sesi.

Berikut adalah rincian alur kerja (*flowchart*) untuk setiap fitur utama di platform ini:

### 1. Alur Autentikasi & Routing Utama
Alur ini menjelaskan bagaimana sistem membedakan antara pengunjung publik dan pengguna terdaftar, serta bagaimana proses masuk (*login*) divalidasi.

```mermaid
flowchart TD
    Start([🌐 Pengguna Mengakses Web]) --> FrontController{index.php <br/>(Front Controller)}
    
    FrontController --> CheckSession{Cek Sesi <br/>$_SESSION['user_id'] ?}
    
    %% Alur Publik
    CheckSession -- Sesi Kosong --> RouterPublik[Router: Halaman Publik]
    RouterPublik --> LandingPage[tampilan/halaman_pendaratan.php]
    RouterPublik --> CekParam{Ada Parameter <br/>?portfolio= ?}
    CekParam -- Ya --> Portofolio[tampilan/halaman_portofolio.php]
    
    %% Alur Login
    LandingPage --> FormAuth[Isi Form Login / Register]
    FormAuth --> AksiAuth(aksi/aksi_autentikasi.php)
    AksiAuth --> ValidasiDB[(Query Database <br/>tabel 'users')]
    ValidasiDB -- Gagal --> LandingPage
    ValidasiDB -- Sukses --> SetSession[Set Sesi & Redirect]
    SetSession --> FrontController
    
    %% Alur Dasbor
    CheckSession -- Sesi Aktif --> RouterDasbor[Router: Halaman Internal]
    RouterDasbor --> Dasbor[tampilan/dasbor/beranda.php]
    
    Dasbor --> Modul1[Workspace / Drive]
    Dasbor --> Modul2[Pembuat CV]
    Dasbor --> Modul3[Pengaturan Admin]
```

### 2. Alur Pengelola File (Workspace / Cloud Storage)
Alur ini mendemonstrasikan proses unggah, manipulasi file, dan perhitungan kuota pada halaman Workspace.

```mermaid
sequenceDiagram
    autonumber
    actor U as Pengguna (User)
    participant UI as Workspace UI <br/>(pengelola_file.php)
    participant JS as app.js (AJAX)
    participant BE as Backend <br/>(aksi_file.php)
    participant DB as Database & <br/>Local Storage

    U->>UI: Klik 'Upload File' / Drag & Drop
    UI->>JS: Tangkap Event File
    JS->>BE: Kirim FormData (File + Metadata) via AJAX
    
    rect rgb(30, 30, 30)
        Note over BE, DB: Validasi Backend
        BE->>BE: Cek Ekstensi & Ukuran File
        BE->>DB: Cek Sisa Kuota Pengguna
    end
    
    alt Kuota Penuh / File Dilarang
        BE-->>JS: Return JSON Error (400)
        JS-->>UI: Tampilkan Toast Notifikasi Gagal
    else Validasi Lolos
        BE->>DB: Pindahkan File Fisik ke folder /unggahan
        BE->>DB: Insert Meta Data (Nama, Path, Size) ke DB
        BE-->>JS: Return JSON Success (200)
        JS-->>UI: Tampilkan Toast Sukses & Reload Grid
    end
    
    UI-->>U: File Muncul di Antarmuka Drive
```

### 3. Alur Pembuat CV & Direktori Portofolio
Alur ini menunjukkan bagaimana input form kompleks diubah menjadi representasi JSON yang dinamis dan ditampilkan ke publik.

```mermaid
flowchart LR
    subgraph Sisi Pengguna (Terdaftar)
        UI_CV[Halaman Pembuat CV <br/>(pembuat_cv.php)] --> Input[Isi Data: Profil, <br/>Pendidikan, Skill]
        Input --> JS_Payload[JavaScript menyusun <br/>Payload JSON]
        JS_Payload --> POST_Profil(aksi_profil.php)
    end
    
    subgraph Database
        POST_Profil --> |Update| KolomJSON[(Tabel 'users' <br/>kolom 'profile_data')]
    end
    
    subgraph Sisi Publik (Recruiter)
        ReqPublik([Akses ?portfolio=username]) --> Router(index.php)
        Router --> QueryDB(Ambil JSON dari DB)
        QueryDB --> KolomJSON
        QueryDB --> RenderHTML[tampilan/halaman_portofolio.php]
        RenderHTML --> TampilCV[Tampilkan Desain CV Profesional]
    end
```

## 📂 Struktur Direktori Tingkat Lanjut

Proyek ini dibangun berdasarkan prinsip *Separation of Concerns* (Pemisahan Tanggung Jawab) antara sisi antarmuka (*Front-end View*), logika pemrosesan (*Backend Action*), dan *routing* utama (*Controller*). Berikut adalah rincian lengkap struktur direktori:

```text
hosting/
│
├── ⚙️ [Sistem Utama]
│   ├── index.php                  # Front Controller: Mengatur 100% routing, sesi login, dan HTTP requests.
│   ├── manifest.json              # Web App Manifest: Konfigurasi nama, ikon, dan tema saat diinstall (PWA).
│   └── sw.js                      # Service Worker: Bertugas melakukan caching file agar web bisa offline (PWA).
│
├── 📁 aksi/                       # Direktori Backend (Memproses data & Query ke MySQL)
│   ├── aksi_autentikasi.php       # Menangani proses Login, Registrasi, Logout, dan Hash Password.
│   ├── aksi_file.php              # Menangani CRUD file Workspace (Upload, Rename, Hapus, Pindah).
│   ├── aksi_pengguna.php          # [God Mode] Menangani operasi Admin untuk menghapus/edit akun lain.
│   └── aksi_profil.php            # Menangkap input Form CV Builder dan mengubahnya menjadi Payload JSON.
│
├── 📁 aset/                       # Direktori Front-end (Resource Statis UI)
│   ├── css/
│   │   └── style.css              # Styling utama selain framework, mengatur Glassmorphism & Dark Mode.
│   ├── js/
│   │   └── app.js                 # Skrip AJAX (Vanilla JS) untuk asinkronisasi Drive & Drag-n-Drop.
│   └── images/                    # Menyimpan ikon web, logo SVG, dan placeholder gambar.
│
├── 📁 tampilan/                   # Direktori View (Antarmuka Pengguna HTML/PHP)
│   ├── 📂 dasbor/                 # Modul Internal (Hanya bisa diakses setelah Login)
│   │   ├── beranda.php            # Dasbor Pusat: Menampilkan statistik, kuota, & shortcut.
│   │   ├── pembuat_cv.php         # CV Builder: Form dinamis untuk membuat Portofolio (Pendidikan, Keahlian).
│   │   ├── pengelola_file.php     # Google Drive Clone: Antarmuka cloud storage, grid/list view, dan context menu.
│   │   └── pengelola_pengguna.php # God Mode Panel: Antarmuka khusus Superadmin mengelola pengguna.
│   │
│   ├── 📂 halaman/                # Modul Publik (Bisa diakses siapa saja)
│   │   ├── halaman_pendaratan.php # Landing Page: Pintu depan pemasaran dan perkenalan platform.
│   │   └── halaman_portofolio.php # Public Showcase: Hasil akhir CV pengguna yang ditampilkan indah ke klien.
│   │
│   └── 📂 komponen/               # Modul Parsial (Potongan UI yang dipanggil berulang-ulang)
│       ├── navbar.php             # Navigasi Atas (Top-bar), breadcrumbs, profile dropdown.
│       ├── sidebar.php            # Menu Samping, Navigasi modul (Dashboard, Workspace, dll).
│       └── modal.php              # Kumpulan jendela popup (Modal) untuk notifikasi/konfirmasi.
│
├── 📁 unggahan/                   # [Directory Server] Penyimpanan fisik untuk semua file & foto pengguna.
└── 📄 README.md                   # Dokumentasi Utama Proyek (File ini).
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
