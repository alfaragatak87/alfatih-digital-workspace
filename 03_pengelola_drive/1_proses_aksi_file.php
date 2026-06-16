<?php
// +------------------------------------------------------------------------------+
// |  FILE: 03_pengelola_drive/1_proses_aksi_file.php                             |
// |                                                                              |
// |  DESKRIPSI:                                                                  |
// |  Pusat pengolahan (Backend Action Handler) untuk operasi manipulasi data.    |
// |  Memproses penambahan, pemindahan, pengubahan nama, dan penghapusan file     |
// |  atau folder. Juga menangani validasi keamanan unggahan file.                |
// |                                                                              |
// |  KONEKSI & RELASI:                                                           |
// |  - Di-require oleh index.php saat terdapat $_POST dari formulir (form).      |
// |  - Bekerja berdampingan dengan 2_pengaturan_workspace.php untuk mengambil    |
// |    status tampilan drive.                                                    |
// |                                                                              |
// |  BARIS KODE PENTING:                                                         |
// |  - finfo_file() : Verifikasi jenis file sejati untuk mencegah bypass MIME.   |
// |  - move_uploaded_file() : Memindahkan file sementara ke direktori unggahan.  |
// +------------------------------------------------------------------------------+

// BLOK: PENANGANAN FORM (POST)
// Fungsi: Menyimpan data folder, file, pembaruan profil, dan aksi pengguna
// =========================================
if ($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['action'])) {
    if ($_POST['action'] !== 'login' && !validateCSRF()) {
        $alert_msg = "Sesi keamanan tidak valid. Muat ulang halaman.";
    } else {
        $act = $_POST['action'];
        if ($act === 'update_settings') {
            $new_name = trim($_POST['nama_lengkap'] ?? ''); $new_pass = $_POST['new_password'] ?? ''; $foto_set = '';
            if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error']===0) {
                $ext = strtolower(pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                    $new_fn = $username . "_" . time() . "." . $ext;
                    if (!is_dir(PROFILE_IMG_DIR)) mkdir(PROFILE_IMG_DIR, 0777, true);
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $_FILES['foto_profil']['tmp_name']);
finfo_close($finfo);
$allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (in_array($mime, $allowed_mimes)) {
    if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], PROFILE_IMG_DIR . $new_fn)) $foto_set = $new_fn;
} else {
    $alert_msg = 'Format foto profil tidak diizinkan!';
}
                }
            }
            if (!empty($new_pass)) {
                $hash = password_hash($new_pass, PASSWORD_BCRYPT);
                if ($foto_set) { $stmt = $mysqli->prepare("UPDATE users SET nama_lengkap=?, password=?, foto_profil=? WHERE id=?"); $stmt->bind_param('sssi', $new_name, $hash, $foto_set, $uid); }
                else { $stmt = $mysqli->prepare("UPDATE users SET nama_lengkap=?, password=? WHERE id=?"); $stmt->bind_param('ssi', $new_name, $hash, $uid); }
            } else {
                if ($foto_set) { $stmt = $mysqli->prepare("UPDATE users SET nama_lengkap=?, foto_profil=? WHERE id=?"); $stmt->bind_param('ssi', $new_name, $foto_set, $uid); }
                else { $stmt = $mysqli->prepare("UPDATE users SET nama_lengkap=? WHERE id=?"); $stmt->bind_param('si', $new_name, $uid); }
            }
            $stmt->execute(); $stmt->close(); $_SESSION['nama'] = $new_name; $alert_msg = "Profil berhasil diperbarui!";
        }
        if ($act === 'add_folder') {
            $nf = trim($_POST['nama_folder'] ?? ''); $dk = trim($_POST['deskripsi'] ?? '');
            $ic = $_POST['icon'] ?? 'fa-folder'; $wr = $_POST['warna'] ?? '#000000';
            $owner = (isAdmin() && !empty($_POST['owner_username'])) ? $_POST['owner_username'] : $username;
            $parent = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            $stmt = $mysqli->prepare("INSERT INTO folders (parent_id, owner_username, nama_folder, icon, warna, deskripsi) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param('isssss', $parent, $owner, $nf, $ic, $wr, $dk); $stmt->execute(); $stmt->close();
            $alert_msg = "Folder berhasil dibuat!";
        }
        if ($act === 'edit_folder') {
            $id = (int)$_POST['folder_id']; $nf = trim($_POST['nama_folder'] ?? '');
            $dk = trim($_POST['deskripsi'] ?? ''); $ic = $_POST['icon'] ?? 'fa-folder'; $wr = $_POST['warna'] ?? '#000000';
            if (isAdmin()) { $stmt = $mysqli->prepare("UPDATE folders SET nama_folder=?, icon=?, warna=?, deskripsi=? WHERE id=?"); $stmt->bind_param('ssssi', $nf, $ic, $wr, $dk, $id); }
            else { $stmt = $mysqli->prepare("UPDATE folders SET nama_folder=?, icon=?, warna=?, deskripsi=? WHERE id=? AND owner_username=?"); $stmt->bind_param('ssssis', $nf, $ic, $wr, $dk, $id, $username); }
            $stmt->execute(); $stmt->close(); $alert_msg = "Folder diperbarui!";
        }
        if ($act === 'add_item') {
            $folder_id = (int)$_POST['folder_id']; $jenis = $_POST['jenis'] ?? 'file'; $tags = trim($_POST['tags'] ?? '');
            if ($jenis === 'link') {
                $nama_link = trim($_POST['nama_link'] ?? ''); $url_link = trim($_POST['link_url'] ?? '');
                if (!preg_match('~^(?:f|ht)tps?://~i', $url_link)) $url_link = 'https://' . $url_link;
                $stmt = $mysqli->prepare("INSERT INTO files (folder_id, owner_username, jenis, nama_file, link_url, tags) VALUES (?,?,'link',?,?,?)");
                $stmt->bind_param('issss', $folder_id, $username, $nama_link, $url_link, $tags); $stmt->execute(); $stmt->close();
                $alert_msg = "Tautan berhasil disimpan!";
            } elseif ($jenis === 'file') {
                if (isset($_FILES['file_upload']['name']) && is_array($_FILES['file_upload']['name'])) {
                    $ok = 0;
                    foreach ($_FILES['file_upload']['name'] as $i => $fname) {
                        if ($_FILES['file_upload']['error'][$i] !== 0) continue;
                        $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
                        $new_fn = $username . '_' . time() . '_' . rand(100,999) . '_' . $i . '.' . $ext;
                        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0777, true);
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $_FILES['file_upload']['tmp_name'][$i]);
finfo_close($finfo);
$blocked_mimes = ['application/x-httpd-php', 'application/x-php', 'text/x-php', 'text/html', 'application/javascript', 'text/javascript', 'application/x-msdownload', 'application/x-sh'];
if (!in_array($mime, $blocked_mimes)) {
    if (move_uploaded_file($_FILES['file_upload']['tmp_name'][$i], UPLOAD_DIR . $new_fn)) {
                            $stmt = $mysqli->prepare("INSERT INTO files (folder_id, owner_username, jenis, nama_file, file_path, tags) VALUES (?,?,'file',?,?,?)");
                            $stmt->bind_param('issss', $folder_id, $username, $fname, $new_fn, $tags); $stmt->execute(); $stmt->close(); $ok++;
                        }
                        }
                    }
                    if ($ok > 0) $alert_msg = "$ok file berhasil diunggah!";
                }
            }
        }
        if ($act === 'move_item') {
            $m_type = $_POST['move_type']; $m_id = (int)$_POST['move_id'];
            $target = ($_POST['target_folder'] === 'root') ? null : (int)$_POST['target_folder'];
            if ($m_type === 'folder') {
                if ($target === null) { $stmt = $mysqli->prepare("UPDATE folders SET parent_id=NULL WHERE id=?"); $stmt->bind_param('i', $m_id); }
                else { $stmt = $mysqli->prepare("UPDATE folders SET parent_id=? WHERE id=? AND id!=?"); $stmt->bind_param('iii', $target, $m_id, $target); }
            } else {
                if ($target === null) { $stmt = $mysqli->prepare("UPDATE files SET folder_id=NULL WHERE id=?"); $stmt->bind_param('i', $m_id); }
                else { $stmt = $mysqli->prepare("UPDATE files SET folder_id=? WHERE id=?"); $stmt->bind_param('ii', $target, $m_id); }
            }
            $stmt->execute(); $stmt->close(); $alert_msg = "Item berhasil dipindahkan!";
        }
        if ($act === 'bulk_delete') {
            $ids = json_decode($_POST['ids'] ?? '[]', true); $types = json_decode($_POST['types'] ?? '[]', true); $count = 0;
            for ($i = 0; $i < count($ids); $i++) {
                $bid = (int)$ids[$i]; $btype = $types[$i];
                if ($btype === 'folder') {
                    if (isAdmin()) { $stmt = $mysqli->prepare("UPDATE folders SET is_deleted=1 WHERE id=?"); $stmt->bind_param('i', $bid); }
                    else { $stmt = $mysqli->prepare("UPDATE folders SET is_deleted=1 WHERE id=? AND owner_username=?"); $stmt->bind_param('is', $bid, $username); }
                } else {
                    if (isAdmin()) { $stmt = $mysqli->prepare("UPDATE files SET is_deleted=1 WHERE id=?"); $stmt->bind_param('i', $bid); }
                    else { $stmt = $mysqli->prepare("UPDATE files SET is_deleted=1 WHERE id=? AND owner_username=?"); $stmt->bind_param('is', $bid, $username); }
                }
                $stmt->execute(); if ($stmt->affected_rows > 0) $count++; $stmt->close();
            }
            $alert_msg = "$count item dipindah ke Tong Sampah!";
        }
        if ($act === 'bulk_move') {
            $ids = json_decode($_POST['ids'] ?? '[]', true); $types = json_decode($_POST['types'] ?? '[]', true);
            $target = ($_POST['target_folder'] ?? 'root') === 'root' ? null : (int)$_POST['target_folder']; $count = 0;
            for ($i = 0; $i < count($ids); $i++) {
                $bid = (int)$ids[$i]; $btype = $types[$i];
                if ($btype === 'folder') {
                    if ($target === null) { $stmt = $mysqli->prepare("UPDATE folders SET parent_id=NULL WHERE id=?"); $stmt->bind_param('i', $bid); }
                    else { $stmt = $mysqli->prepare("UPDATE folders SET parent_id=? WHERE id=? AND id!=?"); $stmt->bind_param('iii', $target, $bid, $target); }
                } else {
                    if ($target === null) { $stmt = $mysqli->prepare("UPDATE files SET folder_id=NULL WHERE id=?"); $stmt->bind_param('i', $bid); }
                    else { $stmt = $mysqli->prepare("UPDATE files SET folder_id=? WHERE id=?"); $stmt->bind_param('ii', $target, $bid); }
                }
                $stmt->execute(); if ($stmt->affected_rows > 0) $count++; $stmt->close();
            }
            $alert_msg = "$count item berhasil dipindahkan!";
        }
        if ($act === 'add_user' && isSuperAdmin()) {
            $nu = trim($_POST['new_username'] ?? ''); $nn = trim($_POST['new_nama'] ?? '');
            $np = $_POST['new_password'] ?? ''; $nr = $_POST['new_role'] ?? 'admin';
            if ($nu && $np) {
                $hash = password_hash($np, PASSWORD_BCRYPT);
                $stmt = $mysqli->prepare("INSERT INTO users (username, nama_lengkap, password, role) VALUES (?,?,?,?)");
                $stmt->bind_param('ssss', $nu, $nn, $hash, $nr); $stmt->execute(); $stmt->close();
                $alert_msg = "User baru berhasil ditambahkan!";
            }
        }
        if ($act === 'edit_user' && isSuperAdmin()) {
            $eu_id = (int)$_POST['edit_uid']; $eu_name = trim($_POST['edit_nama'] ?? '');
            $eu_role = $_POST['edit_role'] ?? 'admin'; $eu_pass = $_POST['edit_password'] ?? '';
            if (!empty($eu_pass)) {
                $hash = password_hash($eu_pass, PASSWORD_BCRYPT);
                $stmt = $mysqli->prepare("UPDATE users SET nama_lengkap=?, role=?, password=? WHERE id=?"); $stmt->bind_param('sssi', $eu_name, $eu_role, $hash, $eu_id);
            } else { $stmt = $mysqli->prepare("UPDATE users SET nama_lengkap=?, role=? WHERE id=?"); $stmt->bind_param('ssi', $eu_name, $eu_role, $eu_id); }
            $stmt->execute(); $stmt->close(); $alert_msg = "User berhasil diperbarui!";
        }
        if ($act === 'delete_user' && isSuperAdmin()) {
            $du_id = (int)$_POST['del_uid'];
            if ($du_id !== $uid) { $stmt = $mysqli->prepare("DELETE FROM users WHERE id=?"); $stmt->bind_param('i', $du_id); $stmt->execute(); $stmt->close(); $alert_msg = "User berhasil dihapus."; }
        }
        if ($act === 'save_profile_data') {
            include "04_pembuat_cv/2_proses_aksi_profil.php";
        }
    }
}

// =========================================
// BLOK: PENANGANAN TONG SAMPAH (GET)
// Fungsi: Mengembalikan atau menghapus secara permanen file/folder
// =========================================
if (isset($_GET['action'])) {
    $gact = $_GET['action'];
    if ($gact === 'soft_delete_folder' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        if (isAdmin()) { $stmt = $mysqli->prepare("UPDATE folders SET is_deleted=1 WHERE id=?"); $stmt->bind_param('i', $id); }
        else { $stmt = $mysqli->prepare("UPDATE folders SET is_deleted=1 WHERE id=? AND owner_username=?"); $stmt->bind_param('is', $id, $username); }
        $stmt->execute(); $stmt->close(); header("Location: index.php?page=workspace"); exit;
    }
    if ($gact === 'soft_delete_item' && isset($_GET['item_id'])) {
        $id = (int)$_GET['item_id'];
        if (isAdmin()) { $stmt = $mysqli->prepare("UPDATE files SET is_deleted=1 WHERE id=?"); $stmt->bind_param('i', $id); }
        else { $stmt = $mysqli->prepare("UPDATE files SET is_deleted=1 WHERE id=? AND owner_username=?"); $stmt->bind_param('is', $id, $username); }
        $stmt->execute(); $stmt->close(); header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=workspace')); exit;
    }
    if ($gact === 'restore' && isset($_GET['id'], $_GET['type'])) {
        $id = (int)$_GET['id']; $type = $_GET['type']; $table = ($type === 'folder') ? 'folders' : 'files';
        if (isAdmin()) { $stmt = $mysqli->prepare("UPDATE $table SET is_deleted=0 WHERE id=?"); $stmt->bind_param('i', $id); }
        else { $stmt = $mysqli->prepare("UPDATE $table SET is_deleted=0 WHERE id=? AND owner_username=?"); $stmt->bind_param('is', $id, $username); }
        $stmt->execute(); $stmt->close(); header("Location: index.php?page=workspace&view=trash"); exit;
    }
    if ($gact === 'hard_delete' && isset($_GET['id'], $_GET['type'])) {
        $id = (int)$_GET['id']; $type = $_GET['type'];
        if ($type === 'file') {
            $stmt = $mysqli->prepare("SELECT * FROM files WHERE id=? AND is_deleted=1"); $stmt->bind_param('i', $id); $stmt->execute(); $fd = $stmt->get_result()->fetch_assoc(); $stmt->close();
            if ($fd && (isAdmin() || $fd['owner_username']===$username)) {
                if ($fd['jenis']==='file' && file_exists(UPLOAD_DIR . $fd['file_path'])) unlink(UPLOAD_DIR . $fd['file_path']);
                $stmt = $mysqli->prepare("DELETE FROM files WHERE id=?"); $stmt->bind_param('i', $id); $stmt->execute(); $stmt->close();
            }
        } else {
            if (isAdmin()) { $stmt = $mysqli->prepare("DELETE FROM folders WHERE id=? AND is_deleted=1"); $stmt->bind_param('i', $id); }
            else { $stmt = $mysqli->prepare("DELETE FROM folders WHERE id=? AND is_deleted=1 AND owner_username=?"); $stmt->bind_param('is', $id, $username); }
            $stmt->execute(); $stmt->close();
        }
        header("Location: index.php?page=workspace&view=trash"); exit;
    }
}

// =========================================
