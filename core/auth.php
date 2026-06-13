<?php
// ============================================================
// ALFATIH DIGITAL WORKSPACE — core/auth.php
// Fokus: Keamanan & proteksi akses.
// Berisi: generateCSRF(), validateCSRF(), logActivity(),
//         isSuperAdmin(), isAdmin(), requireLogin()
// ============================================================

// Pastikan file ini tidak di-load sebelum config/database.php
// (karena logActivity() membutuhkan objek $mysqli yang sudah ada).

/**
 * Membuat atau mengambil CSRF token dari session saat ini.
 * Token ini harus ditanamkan di setiap form sebagai hidden input
 * dengan name="csrf_token" untuk mencegah serangan CSRF.
 *
 * Contoh penggunaan di form HTML:
 *   <input type="hidden" name="csrf_token" value="<?= generateCSRF() ?>">
 *
 * @return string  Token CSRF berupa hex string 64 karakter.
 */
function generateCSRF(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Memvalidasi CSRF token dari request POST.
 * Wajib dipanggil di awal setiap handler POST (kecuali login)
 * sebelum memproses data apapun.
 *
 * Menggunakan hash_equals() untuk mencegah timing attack.
 *
 * @return bool  true jika token cocok, false jika tidak valid atau tidak ada.
 */
function validateCSRF(): bool
{
    return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

/**
 * Mencatat aktivitas user ke tabel admin_logs.
 * Dipanggil untuk event-event penting seperti login, upload file,
 * perubahan data user, dsb.
 *
 * Catatan: Parameter $userId dipakai untuk mengisi KEDUA kolom
 * admin_id dan user_id, sesuai dengan struktur tabel yang ada.
 *
 * @param  mysqli      $db       Koneksi database aktif.
 * @param  int|null    $userId   ID user yang melakukan aksi.
 * @param  string      $action   Nama aksi singkat (e.g., 'LOGIN', 'UPLOAD_FILE').
 * @param  string      $details  Detail tambahan (opsional).
 * @return void
 */
function logActivity(mysqli $db, ?int $userId, string $action, string $details = ''): void
{
    $ip   = $_SERVER['REMOTE_ADDR']     ?? '';
    $ua   = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $stmt = $db->prepare(
        "INSERT INTO admin_logs (admin_id, user_id, action, details, ip_address, user_agent)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    if ($stmt) {
        $stmt->bind_param('iissss', $userId, $userId, $action, $details, $ip, $ua);
        $stmt->execute();
        $stmt->close();
    }
}

/**
 * Memeriksa apakah user yang sedang login adalah SuperAdmin.
 *
 * @return bool  true jika role session adalah 'superadmin'.
 */
function isSuperAdmin(): bool
{
    return ($_SESSION['role'] ?? '') === 'superadmin';
}

/**
 * Memeriksa apakah user yang sedang login adalah Admin atau SuperAdmin.
 * Fungsi ini mencakup kedua role yang memiliki hak akses elevated.
 *
 * @return bool  true jika role session adalah 'superadmin' atau 'admin'.
 */
function isAdmin(): bool
{
    return in_array($_SESSION['role'] ?? '', ['superadmin', 'admin']);
}

/**
 * Memproteksi halaman dari akses tanpa login.
 * Panggil fungsi ini di awal setiap halaman/view yang membutuhkan autentikasi.
 * Jika session kosong (belum login), user akan di-redirect ke halaman login.
 *
 * @return void  Tidak mengembalikan nilai; langsung exit jika tidak terautentikasi.
 */
function requireLogin(): void
{
    if (empty($_SESSION['username'])) {
        header("Location: index.php?page=login");
        exit;
    }
}
