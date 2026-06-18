<?php
// +------------------------------------------------------------------------------+
// |  FILE: 1_proses_cek_login.php                                                |
// |                                                                              |
// |  DESKRIPSI:                                                                  |
// |  File ini bertanggung jawab penuh untuk menangani proses autentikasi.        |
// |  Tugas utamanya adalah memverifikasi username dan kata sandi yang dikirim    |
// |  melalui form login, mengatur variabel sesi (session) jika login berhasil,   |
// |  serta mencatat aktivitas login ke dalam log keamanan.                       |
// |                                                                              |
// |  KONEKSI & RELASI:                                                           |
// |  - Menerima data $_POST dari form login di index.php.                        |
// |  - Menggunakan $mysqli dari 1_koneksi_database.php untuk query ke tabel      |
// |    'users'.                                                                  |
// +------------------------------------------------------------------------------+

// Menyiapkan variabel kosong untuk menampung pesan error (jika login gagal)
$error_msg = '';

// Mengecek apakah ada data yang dikirim dengan metode POST dan aksinya adalah 'register'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'register') {
    $nama_panggilan = trim($_POST['nama_panggilan'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $profesi = trim($_POST['profesi'] ?? '');
    
    // Mengecek apakah email sudah terdaftar
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $error_msg = "Pendaftaran Gagal: Email tersebut sudah terdaftar. Silakan login atau gunakan email lain.";
    } else {
        // Membuat username dan password acak
        $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nama_panggilan)) . rand(100, 999);
        $password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$'), 0, 8);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $role = ($email === 's.s.6624844@gmail.com') ? 'superadmin' : 'user';
        
        $stmt2 = $mysqli->prepare("INSERT INTO users (username, password, email, nama_lengkap, nama_panggilan, profesi, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt2) {
            $stmt2->bind_param("sssssss", $username, $password_hash, $email, $nama_lengkap, $nama_panggilan, $profesi, $role);
            if ($stmt2->execute()) {
                // Kirim Email
                $subject = "Akses Akun Alfatih Workspace";
                $message = "Halo $nama_panggilan,\n\nAkun Anda telah berhasil dibuat sesuai profesi Anda sebagai $profesi.\n\nBerikut adalah detail login Anda:\nUsername: $username\nKata Sandi: $password\n\nSilakan login ke sistem dan ubah kata sandi Anda secepatnya demi keamanan.\n\nSalam,\nAdmin Alfatih Workspace";
                $headers = "From: noreply@mckmmukg.myhost.id";
                
                @mail($email, $subject, $message, $headers);
                
                $error_msg = "✅ Pendaftaran berhasil!<br>Email mungkin tidak terkirim (SMTP belum diatur).<br>Harap simpan data berikut untuk login:<br><strong>Username: $username</strong><br><strong>Kata Sandi: $password</strong>";
            } else {
                $error_msg = "Terjadi kesalahan sistem saat mendaftar: " . $mysqli->error;
            }
        } else {
            $error_msg = "Error DB: " . $mysqli->error;
        }
    }
}

// Mengecek apakah ada data yang dikirim dengan metode POST dan aksinya adalah 'login'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    
    // Mengambil dan membersihkan spasi berlebih pada username yang dimasukkan
    $uname = trim($_POST['username'] ?? ''); 
    
    // Mengambil kata sandi yang dimasukkan oleh pengguna
    $upass = $_POST['password'] ?? '';

    // Menyiapkan perintah SQL (Prepared Statement) untuk mencari user yang aktif
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username=? AND status!='inactive' LIMIT 1");
    
    // Jika query pertama gagal (mungkin kolom status belum ada), gunakan query alternatif
    if (!$stmt) {
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    }

    // Menggabungkan variabel username ke dalam perintah SQL (mencegah SQL Injection)
    $stmt->bind_param('s', $uname); 
    
    // Menjalankan perintah SQL ke database
    $stmt->execute(); 
    
    // Mengambil satu baris data pengguna dari hasil query
    $row = $stmt->get_result()->fetch_assoc(); 
    
    // Menutup perintah SQL untuk menghemat memori
    $stmt->close();

    // Mengecek apakah data pengguna ditemukan dan kata sandinya cocok dengan database
    if ($row && password_verify($upass, $row['password'])) {
        
        // Menyimpan username ke dalam memori sesi (tanda bahwa pengguna sudah login)
        $_SESSION['username'] = $row['username']; 
        
        // Menyimpan hak akses (role) pengguna (superadmin/admin/user) ke sesi
        $_SESSION['role'] = $row['role'];
        
        // Menyimpan ID unik pengguna ke sesi
        $_SESSION['uid'] = $row['id']; 
        
        // Menyimpan nama lengkap pengguna (jika kosong, gunakan username)
        $_SESSION['nama'] = $row['nama_lengkap'] ?? $row['username'];
        
        $_SESSION['profesi'] = $row['profesi'] ?? '';
        $_SESSION['email'] = $row['email'] ?? '';
        $_SESSION['nama_panggilan'] = $row['nama_panggilan'] ?? '';

        // Menyiapkan perintah SQL untuk memperbarui kolom 'last_login' dengan waktu saat ini
        $stmt2 = $mysqli->prepare("UPDATE users SET last_login=NOW() WHERE id=?");
        
        // Memasukkan ID pengguna ke dalam perintah SQL
        $stmt2->bind_param('i', $row['id']); 
        
        // Mengeksekusi perintah update waktu login
        $stmt2->execute(); 
        
        // Menutup perintah SQL
        $stmt2->close();

        // Memanggil fungsi pencatat riwayat (log) bahwa pengguna ini baru saja login beserta IP-nya
        logActivity($mysqli, $row['id'], 'LOGIN_SUCCESS', 'User logged in successfully');
        
        // Mengalihkan halaman (Redirect) ke halaman beranda dasbor setelah berhasil login
        if (empty($_SESSION['profesi']) && $_SESSION['role'] !== 'superadmin') {
            header("Location: index.php?page=lengkapi_profil");
        } else {
            header("Location: index.php?page=beranda");
        }
        
        // Menghentikan proses eksekusi kode di bawahnya agar langsung beralih halaman
        exit;
    } else { 
        // Jika username atau password tidak cocok, isi variabel dengan pesan error
        $error_msg = "Username atau password salah."; 
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'google_login') {
    $credential = $_POST['credential'] ?? '';
    if ($credential) {
        $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $credential;
        $response = @file_get_contents($url);
        if (!$response && function_exists('curl_version')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
        }
        if ($response) {
            $payload = json_decode($response, true);
            if ($payload && isset($payload['email']) && isset($payload['sub'])) {
                $email = $payload['email'];
                $google_id = $payload['sub'];
                $name = $payload['name'] ?? explode('@', $email)[0];
                
                $stmt = $mysqli->prepare("SELECT * FROM users WHERE google_id=? OR email=? LIMIT 1");
                if ($stmt) {
                    $stmt->bind_param('ss', $google_id, $email);
                    $stmt->execute();
                    $row = $stmt->get_result()->fetch_assoc();
                    $stmt->close();
                    
                    if ($row) {
                        if (empty($row['google_id'])) {
                            $mysqli->query("UPDATE users SET google_id='$google_id' WHERE id=".$row['id']);
                        }
                        $_SESSION['username'] = $row['username'];
                        $_SESSION['role'] = $row['role'];
                        $_SESSION['uid'] = $row['id'];
                        $_SESSION['nama'] = $row['nama_lengkap'] ?? $row['username'];
                        $_SESSION['profesi'] = $row['profesi'] ?? '';
                        $_SESSION['email'] = $row['email'] ?? '';
                        $_SESSION['nama_panggilan'] = $row['nama_panggilan'] ?? '';
                        $mysqli->query("UPDATE users SET last_login=NOW() WHERE id=".$row['id']);
                        logActivity($mysqli, $row['id'], 'LOGIN_GOOGLE', 'User logged in via Google');
                        
                        if (empty($_SESSION['profesi']) && $_SESSION['role'] !== 'superadmin') {
                            header("Location: index.php?page=lengkapi_profil");
                        } else {
                            header("Location: index.php?page=beranda");
                        }
                        exit;
                    } else {
                        // Registrasi otomatis
                        $new_username = explode('@', $email)[0];
                        $check_un = $mysqli->query("SELECT id FROM users WHERE username='$new_username'");
                        if ($check_un && $check_un->num_rows > 0) {
                            $new_username .= '_' . rand(100, 999);
                        }
                        $random_pass = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
                        
                        $stmt_ins = $mysqli->prepare("INSERT INTO users (username, password, nama_lengkap, role, email, google_id) VALUES (?, ?, ?, 'user', ?, ?)");
                        if ($stmt_ins) {
                            $stmt_ins->bind_param('sssss', $new_username, $random_pass, $name, $email, $google_id);
                            if ($stmt_ins->execute()) {
                                $new_uid = $stmt_ins->insert_id;
                                $_SESSION['username'] = $new_username;
                                $_SESSION['role'] = 'user';
                                $_SESSION['uid'] = $new_uid;
                                $_SESSION['nama'] = $name;
                                $_SESSION['profesi'] = '';
                                $_SESSION['email'] = $email;
                                $_SESSION['nama_panggilan'] = explode(' ', $name)[0];
                                logActivity($mysqli, $new_uid, 'REGISTER_GOOGLE', 'New user registered via Google');
                                
                                header("Location: index.php?page=lengkapi_profil");
                                exit;
                            } else {
                                $error_msg = "Gagal membuat akun Google.";
                            }
                        }
                    }
                } else {
                    $error_msg = "Sistem database belum diupdate untuk Google.";
                }
            } else {
                $error_msg = "Data Google tidak lengkap.";
            }
        } else {
            $error_msg = "Token Google tidak valid atau kedaluwarsa.";
        }
    }
}

// ==========================================
// AKSI IMPERSONATE (HANYA SUPERADMIN)
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'impersonate' && function_exists('isSuperAdmin') && isSuperAdmin()) {
    $target = $_GET['target_user'] ?? '';
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username=?");
    if ($stmt) {
        $stmt->bind_param('s', $target);
        $stmt->execute();
        $target_row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($target_row && $target_row['role'] !== 'superadmin') {
            // Simpan sesi asli
            $_SESSION['impersonator'] = $_SESSION['username'];
            $_SESSION['impersonator_role'] = $_SESSION['role'];
            // Timpa dengan sesi target
            $_SESSION['username'] = $target_row['username'];
            $_SESSION['role'] = $target_row['role'];
            $_SESSION['uid'] = $target_row['id'];
            $_SESSION['nama'] = $target_row['nama_lengkap'] ?? $target_row['username'];
            $_SESSION['profesi'] = $target_row['profesi'] ?? '';
            $_SESSION['email'] = $target_row['email'] ?? '';
            $_SESSION['nama_panggilan'] = $target_row['nama_panggilan'] ?? '';
            header("Location: index.php");
            exit;
        } else {
            $_SESSION['flash_error'] = "Tidak dapat meminjam sesi Superadmin lain atau pengguna tidak ditemukan.";
            header("Location: index.php?page=manajemen-pengguna");
            exit;
        }
    }
}

// ==========================================
// STOP IMPERSONATE
// ==========================================
if (isset($_GET['action']) && $_GET['action'] === 'stop_impersonate') {
    if (isset($_SESSION['impersonator'])) {
        // Kembalikan ke sesi asli
        $_SESSION['username'] = $_SESSION['impersonator'];
        $_SESSION['role'] = $_SESSION['impersonator_role'];
        // Hapus penanda
        unset($_SESSION['impersonator']);
        unset($_SESSION['impersonator_role']);
        // Redirect kembali ke God Mode
        header("Location: index.php?page=manajemen-pengguna");
        exit;
    }
}

