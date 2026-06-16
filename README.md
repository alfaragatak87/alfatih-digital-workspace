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

Berikut adalah rincian alur kerja (*flowchart*) untuk setiap fitur utama di platform ini secara komprehensif.

> [!TIP]
> Garis putus-putus berwarna terang yang menghubungkan setiap proses merepresentasikan **"Energy Flow" (Alur Cahaya)** perjalanan data secara asinkron dari awal eksekusi (*Client*) hingga akhir (*Server/Database*).

### 1. Alur Autentikasi, Routing Utama, & Front Controller
Alur ini membedakan secara ketat antara akses **Publik** (tanpa batas akses) dan **Sistem Internal** (harus login). Semua proses bermuara di `index.php`.

```mermaid
flowchart TD
    %% Define Styles untuk Efek Cahaya / Neon
    classDef startEnd fill:#0f172a,stroke:#3b82f6,stroke-width:3px,color:#fff,rx:10,ry:10;
    classDef process fill:#1e293b,stroke:#8b5cf6,stroke-width:2px,color:#fff;
    classDef db fill:#020617,stroke:#10b981,stroke-width:2px,color:#10b981,shape:cylinder;
    classDef decision fill:#334155,stroke:#f59e0b,stroke-width:2px,color:#f59e0b,shape:diamond;
    
    Start(["🌐 Mulai: Kunjungan Web"]):::startEnd --> FrontController["index.php <br/>(Front Controller & Router)"]:::process
    
    FrontController --> Init["Inisialisasi require_once config DB & helper"]:::process
    Init --> CheckSession{"Validasi <br/>$_SESSION['user_id']"}:::decision
    
    %% Alur Publik
    CheckSession -- "Tidak Ada Sesi (Akses Publik)" --> CekParam{"URL Parameter <br/>GET ?portfolio=... ?"}:::decision
    CekParam -- "Kosong" --> LandingPage["Render halaman_pendaratan.php"]:::process
    CekParam -- "Ada Username" --> CekDBPorto[("SELECT profile_data <br/>FROM users")]:::db
    CekDBPorto --> Portofolio["Render halaman_portofolio.php"]:::process
    
    %% Alur Autentikasi / Login
    LandingPage --> FormAuth["Submit Form Login (POST)"]:::process
    FormAuth --> AksiAuth["Panggil aksi_autentikasi.php"]:::process
    AksiAuth --> CekPass[("Cek password_verify() <br/>di Database")]:::db
    CekPass -- "Hash Tidak Valid" --> ErrorMSG["Set Alert Error & Redirect"]:::process
    ErrorMSG --> LandingPage
    CekPass -- "Valid" --> SetSession["Set $_SESSION['uid'] <br/> Update last_login"]:::process
    SetSession --> RedirectDasbor["Redirect ke ?page=beranda"]:::process
    RedirectDasbor --> FrontController
    
    %% Alur Dasbor
    CheckSession -- "Sesi Valid (Login Aktif)" --> RoutingDasbor{"Routing Berdasarkan <br/>$_GET['page']"}:::decision
    RoutingDasbor -- "page=beranda" --> Dasbor["Dasbor Pusat beranda.php"]:::process
    RoutingDasbor -- "page=drive" --> Workspace["Workspace Drive pengelola_file.php"]:::process
    RoutingDasbor -- "page=cv" --> CVBuilder["Pembuat CV pembuat_cv.php"]:::process
    
    %% Animasi Efek Cahaya / Energy Flow
    linkStyle default stroke:#00e5ff,stroke-width:2px,stroke-dasharray: 5 5,animation: dash 1s linear infinite;
```

### 2. Alur Pengelola File (Cloud Storage Workspace)
Ini adalah anatomi lengkap manajemen file, meliputi keamanan, unggahan (*upload*), limitasi kuota, hingga eksekusi asinkron menggunakan *AJAX Vanilla*.

```mermaid
flowchart TD
    %% Define Styles
    classDef client fill:#0f172a,stroke:#0ea5e9,stroke-width:2px,color:#fff;
    classDef ajax fill:#1e293b,stroke:#eab308,stroke-width:2px,color:#fff;
    classDef backend fill:#1e1b4b,stroke:#8b5cf6,stroke-width:2px,color:#fff;
    classDef db fill:#020617,stroke:#10b981,stroke-width:2px,color:#10b981,shape:cylinder;
    classDef decision fill:#334155,stroke:#ef4444,stroke-width:2px,color:#fff,shape:diamond;

    Client(["💻 User Action: Upload File / Drag Drop"]):::client --> UI["Workspace UI pengelola_file.php"]:::client
    UI --> AppJS["JavaScript app.js <br/>(Event Listener)"]:::ajax
    AppJS --> AjaxPost["AJAX POST: Kirim FormData <br/>+ CSRF Token"]:::ajax
    
    AjaxPost --> RouteBackend["Routing di index.php"]:::backend
    RouteBackend --> ModulAksi["Panggil aksi_file.php"]:::backend
    
    ModulAksi --> CekStorage{"Validasi Kapasitas <br/>(Cek Total Size vs Limit)"}:::decision
    CekStorage -- "Overlimit" --> ResErr1["Response JSON 400 <br/>(Storage Penuh)"]:::ajax
    
    CekStorage -- "Aman" --> CekEkstensi{"Validasi Ekstensi <br/>(.exe / .php ditolak)"}:::decision
    CekEkstensi -- "Ilegal" --> ResErr2["Response JSON 400 <br/>(File Dilarang)"]:::ajax
    
    CekEkstensi -- "Legal" --> MoveFile["Eksekusi move_uploaded_file()"]:::backend
    MoveFile --> HDD[("Simpan Fisik ke <br/>/unggahan")]:::db
    
    HDD --> DBInsert[("INSERT INTO tabel_file <br/>size, path, mime_type")]:::db
    DBInsert --> ResOk["Response JSON 200 <br/>(Upload Sukses)"]:::ajax
    
    ResErr1 & ResErr2 --> ToastFail(["Tampilkan Toast Gagal"]):::client
    ResOk --> ToastOk(["Tampilkan Toast Sukses"]):::client
    ToastOk --> ReloadUI["Render Ulang Grid/List AJAX"]:::client
    
    %% Animasi Efek Cahaya / Energy Flow
    linkStyle default stroke:#facc15,stroke-width:2px,stroke-dasharray: 6 4,animation: dash 0.8s linear infinite;
```

### 3. Alur Pembuat CV, Data JSON, & Direktori Portofolio Publik
Alur ini sangat unik karena data tidak disimpan di banyak tabel berbeda, melainkan menggunakan satu payload JSON yang sangat masif dan fleksibel.

```mermaid
flowchart LR
    %% Define Styles
    classDef client fill:#0f172a,stroke:#ec4899,stroke-width:2px,color:#fff;
    classDef process fill:#1e293b,stroke:#8b5cf6,stroke-width:2px,color:#fff;
    classDef db fill:#020617,stroke:#10b981,stroke-width:2px,color:#10b981,shape:cylinder;
    classDef public fill:#022c22,stroke:#14b8a6,stroke-width:2px,color:#fff;

    subgraph Sisi_Klien ["1. Panel Pengguna (CV Builder)"]
        direction TB
        Form(["Akses pembuat_cv.php"]):::client --> Input1["Isi Identitas & Profil"]:::client
        Input1 --> Input2["Tambah Entri Pendidikan"]:::client
        Input2 --> Input3["Tambah Entri Keahlian"]:::client
        Input3 --> PayloadJS["JavaScript Melakukan Stringify <br/>ke Format JSON"]:::process
        PayloadJS --> AjaxPush["AJAX PUT / POST Request"]:::process
    end

    subgraph Server_DB ["2. Proses Pembaruan di Server"]
        direction TB
        AjaxPush --> AksiProfil["aksi_profil.php"]:::process
        AksiProfil --> ValidasiXSS["Sanitasi & Bersihkan Tag HTML"]:::process
        ValidasiXSS --> UpdateDB[("UPDATE users <br/>SET profile_data = JSON <br/>WHERE id = sesi_user")]:::db
    end

    subgraph Akses_Publik ["3. Tampilan Hasil Akhir (Recruiter)"]
        direction TB
        KlienLuar(["URL Klien Luar <br/>?portfolio=nama"]):::public --> Index("index.php"):::public
        Index --> Query[("SELECT profile_data <br/>Berdasarkan URL")]:::db
        Query --> ParsePHP["PHP json_decode()"]:::process
        ParsePHP --> RenderCV["Generate HTML CV Profesional"]:::public
        RenderCV --> ShowCV(["🎉 Tampilan CV Selesai"]):::public
    end

    %% Animasi Efek Cahaya / Energy Flow
    linkStyle default stroke:#ec4899,stroke-width:2px,stroke-dasharray: 4 6,animation: dash 1.2s linear infinite;
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
