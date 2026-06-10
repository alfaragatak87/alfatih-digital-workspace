<?php
// ============================================================
// ALFATIH DIGITAL WORKSPACE — actions/user_action.php
// Fokus: Manajemen User oleh SuperAdmin.
// Mencakup:
//   POST action=add_user    — tambah user baru
//   POST action=edit_user   — edit nama, role, password user
//   POST action=delete_user — hapus user (tidak bisa hapus diri sendiri)
//
// PENTING: Semua handler di file ini dibungkus dengan pengecekan
// isSuperAdmin() — hanya SuperAdmin yang boleh mengeksekusinya.
//
// Dipanggil oleh: index.php (dalam blok POST authenticated)
// Requires: config/database.php, core/auth.php
// Variabel global yang dibutuhkan dari index.php:
//   $mysqli, $uid (int, ID user yang sedang login), $alert_msg (string ref)
// ============================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    !empty($_POST['action'])  &&
    !empty($_SESSION['username'])
) {
    $act = $_POST['action'];

    // ── Tambah User Baru ──────────────────────────────────────
    // Hanya SuperAdmin yang boleh menambah user.
    // Username dan password wajib diisi; nama dan role opsional.
    if ($act === 'add_user' && isSuperAdmin()) {
        $nu = trim($_POST['new_username'] ?? '');
        $nn = trim($_POST['new_nama']     ?? '');
        $np = $_POST['new_password']      ?? '';
        $nr = $_POST['new_role']          ?? 'admin';

        if ($nu && $np) {
            $hash = password_hash($np, PASSWORD_BCRYPT);
            $stmt = $mysqli->prepare(
                "INSERT INTO users (username, nama_lengkap, password, role)
                 VALUES (?,?,?,?)"
            );
            $stmt->bind_param('ssss', $nu, $nn, $hash, $nr);
            $stmt->execute();
            $stmt->close();
            $alert_msg = "User baru '{$nu}' berhasil ditambahkan!";
        } else {
            $alert_msg = "Username dan password wajib diisi.";
        }
    }

    // ── Edit Data User ────────────────────────────────────────
    // SuperAdmin bisa mengubah nama tampilan, role, dan password user lain.
    // Jika kolom password dikosongkan, password lama tetap dipertahankan
    // (tidak ada perubahan pada kolom password di database).
    if ($act === 'edit_user' && isSuperAdmin()) {
        $eu_id   = (int)$_POST['edit_uid'];
        $eu_name = trim($_POST['edit_nama'] ?? '');
        $eu_role = $_POST['edit_role']      ?? 'admin';
        $eu_pass = $_POST['edit_password']  ?? '';

        if (!empty($eu_pass)) {
            // Ganti password sekaligus
            $hash = password_hash($eu_pass, PASSWORD_BCRYPT);
            $stmt = $mysqli->prepare(
                "UPDATE users SET nama_lengkap=?, role=?, password=? WHERE id=?"
            );
            $stmt->bind_param('sssi', $eu_name, $eu_role, $hash, $eu_id);
        } else {
            // Hanya update nama dan role, password tidak disentuh
            $stmt = $mysqli->prepare(
                "UPDATE users SET nama_lengkap=?, role=? WHERE id=?"
            );
            $stmt->bind_param('ssi', $eu_name, $eu_role, $eu_id);
        }
        $stmt->execute();
        $stmt->close();
        $alert_msg = "Data user berhasil diperbarui!";
    }

    // ── Hapus User Permanen ───────────────────────────────────
    // SuperAdmin tidak bisa menghapus akunnya sendiri ($du_id !== $uid).
    // File/data milik user yang dihapus tetap tersimpan di database
    // (tidak di-cascade delete) — sesuai perilaku asli aplikasi.
    if ($act === 'delete_user' && isSuperAdmin()) {
        $du_id = (int)$_POST['del_uid'];

        if ($du_id === $uid) {
            // Proteksi: tolak penghapusan akun sendiri
            $alert_msg = "Tidak dapat menghapus akun Anda sendiri.";
        } else {
            $stmt = $mysqli->prepare("DELETE FROM users WHERE id=?");
            $stmt->bind_param('i', $du_id);
            $stmt->execute();
            $stmt->close();
            $alert_msg = "User berhasil dihapus.";
        }
    }
}
