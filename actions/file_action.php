<?php
// ============================================================
// ALFATIH DIGITAL WORKSPACE — actions/file_action.php
// Fokus: Semua operasi file & folder.
// Mencakup:
//   GET  — track_document (JSON search), share publik, download,
//          view, print, download_zip, create_share,
//          soft_delete_folder, soft_delete_item, restore,
//          hard_delete
//   POST — drag_move, rename_item, add_folder, edit_folder,
//          add_item (upload file & simpan link), move_item,
//          bulk_delete, bulk_move
//
// Dipanggil oleh: index.php
// Requires: config/database.php, core/auth.php, core/helpers.php
// Variabel global yang dibutuhkan dari index.php:
//   $mysqli, $username, $uid (int), $alert_msg (string ref)
// ============================================================

// ════════════════════════════════════════════════════════════
// SECTION 1 — EARLY GET ACTIONS (berjalan sebelum cek login)
// Ini adalah endpoint publik atau binary-output yang harus
// di-exit secepat mungkin sebelum ada output HTML apapun.
// ════════════════════════════════════════════════════════════

// ── 1a. AJAX: Track / Cari Dokumen (JSON) ────────────────────
// Endpoint: ?action=track_document&q=keyword
// Dipakai oleh search bar di navbar untuk autocomplete.
if (isset($_GET['action']) && $_GET['action'] === 'track_document' && isset($_GET['q'])) {
    $like = '%' . $_GET['q'] . '%';
    $stmt = $mysqli->prepare(
        "SELECT id, nama_file, jenis, tanggal_upload, tags
         FROM files
         WHERE is_deleted=0 AND (tags LIKE ? OR nama_file LIKE ?)
         ORDER BY tanggal_upload DESC
         LIMIT 10"
    );
    $stmt->bind_param('ss', $like, $like);
    $stmt->execute();
    $res     = $stmt->get_result();
    $results = [];
    while ($r = $res->fetch_assoc()) {
        $results[] = $r;
    }
    $stmt->close();
    header('Content-Type: application/json');
    echo json_encode($results);
    exit;
}

// ── 1b. Share Publik — Serve File via Token ───────────────────
// Endpoint: ?share=TOKEN
// Siapapun dengan link ini bisa mengakses file (tanpa login).
if (isset($_GET['share'])) {
    $token = $_GET['share'];
    $stmt  = $mysqli->prepare(
        "SELECT * FROM files WHERE share_token=? AND jenis='file' AND is_deleted=0"
    );
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($r) {
        $fp = UPLOAD_DIR . $r['file_path'];
        if (file_exists($fp)) {
            $mime = mime_content_type($fp) ?: 'application/octet-stream';
            header('Content-Type: ' . $mime);
            header('Content-Disposition: inline; filename="' . basename($r['nama_file']) . '"');
            readfile($fp);
            exit;
        }
    }
    die("Link tidak valid atau file telah dihapus.");
}

// ── 1c. Download / View / Print File (perlu login) ───────────
// Endpoint: ?action=download_file|view_file|print_file&file_id=ID
if (
    isset($_GET['action']) &&
    in_array($_GET['action'], ['download_file', 'view_file', 'print_file']) &&
    isset($_GET['file_id']) &&
    !empty($_SESSION['username'])
) {
    $fid  = (int)$_GET['file_id'];
    $stmt = $mysqli->prepare("SELECT * FROM files WHERE id=? AND jenis='file' AND is_deleted=0");
    $stmt->bind_param('i', $fid);
    $stmt->execute();
    $fd = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($fd && (isAdmin() || $fd['owner_username'] === $_SESSION['username'])) {
        $fp = UPLOAD_DIR . $fd['file_path'];
        if (file_exists($fp)) {
            // Mode print: buka iframe lalu trigger window.print()
            if ($_GET['action'] === 'print_file') {
                echo "<!DOCTYPE html><html><head><title>Print</title></head>"
                   . "<body style='margin:0;background:#525659;'>"
                   . "<iframe src='?action=view_file&file_id={$fid}' "
                   . "style='width:100%;height:100vh;border:none;' "
                   . "onload='this.contentWindow.print();'></iframe>"
                   . "</body></html>";
                exit;
            }
            $mime = mime_content_type($fp) ?: 'application/octet-stream';
            $disp = ($_GET['action'] === 'view_file') ? 'inline' : 'attachment';
            header('Content-Type: ' . $mime);
            header('Content-Disposition: ' . $disp . '; filename="' . basename($fd['nama_file']) . '"');
            header('Content-Length: ' . filesize($fp));
            readfile($fp);
            exit;
        }
    }
    exit;
}

// ── 1d. Download ZIP Seluruh Isi Folder ──────────────────────
// Endpoint: ?action=download_zip&folder_id=ID
if (
    isset($_GET['action']) &&
    $_GET['action'] === 'download_zip' &&
    isset($_GET['folder_id']) &&
    !empty($_SESSION['username'])
) {
    if (!class_exists('ZipArchive')) {
        die("ZIP tidak didukung oleh server ini.");
    }

    $fid  = (int)$_GET['folder_id'];
    $stmt = $mysqli->prepare(
        "SELECT * FROM files WHERE folder_id=? AND is_deleted=0 AND jenis='file'"
    );
    $stmt->bind_param('i', $fid);
    $stmt->execute();
    $files = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    if (empty($files)) {
        die("Folder kosong, tidak ada file untuk diunduh.");
    }

    $zip   = new ZipArchive();
    $zname = "Folder_Export_" . time() . ".zip";
    $zpath = "uploads/" . $zname;

    if ($zip->open($zpath, ZipArchive::CREATE) === true) {
        foreach ($files as $f) {
            $fp = UPLOAD_DIR . $f['file_path'];
            if (file_exists($fp)) {
                $zip->addFile($fp, $f['nama_file']);
            }
        }
        $zip->close();
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename=' . $zname);
        header('Content-Length: ' . filesize($zpath));
        readfile($zpath);
        unlink($zpath); // hapus file ZIP sementara
        exit;
    }
    die("Gagal membuat file ZIP.");
}

// ── 1e. Buat / Generate Share Token untuk File ───────────────
// Endpoint: ?action=create_share&file_id=ID
if (
    isset($_GET['action']) &&
    $_GET['action'] === 'create_share' &&
    isset($_GET['file_id']) &&
    !empty($_SESSION['username'])
) {
    $fid   = (int)$_GET['file_id'];
    $token = bin2hex(random_bytes(16));
    $stmt  = $mysqli->prepare("UPDATE files SET share_token=? WHERE id=?");
    $stmt->bind_param('si', $token, $fid);
    $stmt->execute();
    $stmt->close();
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=workspace'));
    exit;
}


// ════════════════════════════════════════════════════════════
// SECTION 2 — EARLY POST ACTIONS (AJAX, binary response)
// Kedua handler ini return JSON — harus exit sebelum HTML.
// ════════════════════════════════════════════════════════════

// ── 2a. AJAX: Drag & Drop Pindah Item ────────────────────────
// Endpoint: POST action=drag_move
// Dipanggil oleh app.js saat user melepas drag ke folder target.
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) &&
    $_POST['action'] === 'drag_move' &&
    !empty($_SESSION['username'])
) {
    if (!validateCSRF()) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'CSRF tidak valid']);
        exit;
    }

    $iid   = (int)$_POST['item_id'];
    $itype = $_POST['item_type'];
    $tid   = (int)$_POST['target_folder'];

    if ($itype === 'folder') {
        // Cegah folder dipindah ke dalam dirinya sendiri (id != tid)
        $stmt = $mysqli->prepare("UPDATE folders SET parent_id=? WHERE id=? AND id!=?");
        $stmt->bind_param('iii', $tid, $iid, $tid);
    } else {
        $stmt = $mysqli->prepare("UPDATE files SET folder_id=? WHERE id=?");
        $stmt->bind_param('ii', $tid, $iid);
    }
    $stmt->execute();
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}

// ── 2b. AJAX: Rename Item (File atau Folder) ─────────────────
// Endpoint: POST action=rename_item
// Dipanggil oleh app.js saat user konfirmasi rename inline.
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) &&
    $_POST['action'] === 'rename_item' &&
    !empty($_SESSION['username'])
) {
    if (!validateCSRF()) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'CSRF tidak valid']);
        exit;
    }

    $rid   = (int)$_POST['item_id'];
    $rtype = $_POST['item_type'];
    $rname = trim($_POST['new_name']);
    $usr   = $_SESSION['username'];

    if ($rtype === 'folder') {
        if (isAdmin()) {
            $stmt = $mysqli->prepare("UPDATE folders SET nama_folder=? WHERE id=?");
        } else {
            $stmt = $mysqli->prepare(
                "UPDATE folders SET nama_folder=? WHERE id=? AND owner_username='"
                . $mysqli->real_escape_string($usr) . "'"
            );
        }
    } else {
        if (isAdmin()) {
            $stmt = $mysqli->prepare("UPDATE files SET nama_file=? WHERE id=?");
        } else {
            $stmt = $mysqli->prepare(
                "UPDATE files SET nama_file=? WHERE id=? AND owner_username='"
                . $mysqli->real_escape_string($usr) . "'"
            );
        }
    }
    $stmt->bind_param('si', $rname, $rid);
    $stmt->execute();
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}


// ════════════════════════════════════════════════════════════
// SECTION 3 — POST HANDLERS (halaman normal, set $alert_msg)
// Handler ini tidak langsung exit — mereka mengisi $alert_msg
// lalu membiarkan index.php melanjutkan render halaman.
// ════════════════════════════════════════════════════════════

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    !empty($_POST['action']) &&
    !empty($_SESSION['username'])
) {
    $act = $_POST['action'];

    // ── 3a. Tambah Folder Baru ────────────────────────────────
    if ($act === 'add_folder') {
        $nf     = trim($_POST['nama_folder'] ?? '');
        $dk     = trim($_POST['deskripsi']   ?? '');
        $ic     = $_POST['icon']  ?? 'fa-folder';
        $wr     = $_POST['warna'] ?? '#000000';
        // Admin bisa menentukan owner; user biasa hanya bisa membuat untuk dirinya
        $owner  = (isAdmin() && !empty($_POST['owner_username']))
                    ? $_POST['owner_username']
                    : $username;
        $parent = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

        $stmt = $mysqli->prepare(
            "INSERT INTO folders (parent_id, owner_username, nama_folder, icon, warna, deskripsi)
             VALUES (?,?,?,?,?,?)"
        );
        $stmt->bind_param('isssss', $parent, $owner, $nf, $ic, $wr, $dk);
        $stmt->execute();
        $stmt->close();
        $alert_msg = "Folder berhasil dibuat!";
    }

    // ── 3b. Edit / Perbarui Folder ────────────────────────────
    if ($act === 'edit_folder') {
        $id = (int)$_POST['folder_id'];
        $nf = trim($_POST['nama_folder'] ?? '');
        $dk = trim($_POST['deskripsi']   ?? '');
        $ic = $_POST['icon']  ?? 'fa-folder';
        $wr = $_POST['warna'] ?? '#000000';

        if (isAdmin()) {
            $stmt = $mysqli->prepare(
                "UPDATE folders SET nama_folder=?, icon=?, warna=?, deskripsi=? WHERE id=?"
            );
            $stmt->bind_param('ssssi', $nf, $ic, $wr, $dk, $id);
        } else {
            $stmt = $mysqli->prepare(
                "UPDATE folders SET nama_folder=?, icon=?, warna=?, deskripsi=?
                 WHERE id=? AND owner_username=?"
            );
            $stmt->bind_param('ssssis', $nf, $ic, $wr, $dk, $id, $username);
        }
        $stmt->execute();
        $stmt->close();
        $alert_msg = "Folder berhasil diperbarui!";
    }

    // ── 3c. Tambah Item — Upload File atau Simpan Tautan ─────
    if ($act === 'add_item') {
        $folder_id = (int)$_POST['folder_id'];
        $jenis     = $_POST['jenis'] ?? 'file';
        $tags      = trim($_POST['tags'] ?? '');

        if ($jenis === 'link') {
            // Simpan tautan/URL eksternal
            $nama_link = trim($_POST['nama_link'] ?? '');
            $url_link  = trim($_POST['link_url']  ?? '');
            // Pastikan URL memiliki protokol
            if (!preg_match('~^(?:f|ht)tps?://~i', $url_link)) {
                $url_link = 'https://' . $url_link;
            }
            $stmt = $mysqli->prepare(
                "INSERT INTO files (folder_id, owner_username, jenis, nama_file, link_url, tags)
                 VALUES (?,?,'link',?,?,?)"
            );
            $stmt->bind_param('issss', $folder_id, $username, $nama_link, $url_link, $tags);
            $stmt->execute();
            $stmt->close();
            $alert_msg = "Tautan berhasil disimpan!";

        } elseif ($jenis === 'file') {
            // Upload satu atau beberapa file sekaligus
            if (isset($_FILES['file_upload']['name']) && is_array($_FILES['file_upload']['name'])) {
                $ok = 0;
                foreach ($_FILES['file_upload']['name'] as $i => $fname) {
                    if ($_FILES['file_upload']['error'][$i] !== 0) continue;

                    $ext    = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
                    $new_fn = $username . '_' . time() . '_' . rand(100, 999) . '_' . $i . '.' . $ext;

                    if (!is_dir(UPLOAD_DIR)) {
                        mkdir(UPLOAD_DIR, 0777, true);
                    }
                    if (move_uploaded_file($_FILES['file_upload']['tmp_name'][$i], UPLOAD_DIR . $new_fn)) {
                        $stmt = $mysqli->prepare(
                            "INSERT INTO files (folder_id, owner_username, jenis, nama_file, file_path, tags)
                             VALUES (?,?,'file',?,?,?)"
                        );
                        $stmt->bind_param('issss', $folder_id, $username, $fname, $new_fn, $tags);
                        $stmt->execute();
                        $stmt->close();
                        $ok++;
                    }
                }
                if ($ok > 0) {
                    $alert_msg = "{$ok} file berhasil diunggah!";
                }
            }
        }
    }

    // ── 3d. Pindah Item (via Modal, bukan drag) ───────────────
    if ($act === 'move_item') {
        $m_type = $_POST['move_type'];
        $m_id   = (int)$_POST['move_id'];
        $target = ($_POST['target_folder'] === 'root') ? null : (int)$_POST['target_folder'];

        if ($m_type === 'folder') {
            if ($target === null) {
                $stmt = $mysqli->prepare("UPDATE folders SET parent_id=NULL WHERE id=?");
                $stmt->bind_param('i', $m_id);
            } else {
                // Cegah folder dipindah ke dalam dirinya sendiri
                $stmt = $mysqli->prepare("UPDATE folders SET parent_id=? WHERE id=? AND id!=?");
                $stmt->bind_param('iii', $target, $m_id, $target);
            }
        } else {
            if ($target === null) {
                $stmt = $mysqli->prepare("UPDATE files SET folder_id=NULL WHERE id=?");
                $stmt->bind_param('i', $m_id);
            } else {
                $stmt = $mysqli->prepare("UPDATE files SET folder_id=? WHERE id=?");
                $stmt->bind_param('ii', $target, $m_id);
            }
        }
        $stmt->execute();
        $stmt->close();
        $alert_msg = "Item berhasil dipindahkan!";
    }

    // ── 3e. Bulk Delete (Kirim ke Tong Sampah) ────────────────
    if ($act === 'bulk_delete') {
        $ids   = json_decode($_POST['ids']   ?? '[]', true);
        $types = json_decode($_POST['types'] ?? '[]', true);
        $count = 0;

        for ($i = 0; $i < count($ids); $i++) {
            $bid   = (int)$ids[$i];
            $btype = $types[$i];

            if ($btype === 'folder') {
                if (isAdmin()) {
                    $stmt = $mysqli->prepare("UPDATE folders SET is_deleted=1 WHERE id=?");
                    $stmt->bind_param('i', $bid);
                } else {
                    $stmt = $mysqli->prepare("UPDATE folders SET is_deleted=1 WHERE id=? AND owner_username=?");
                    $stmt->bind_param('is', $bid, $username);
                }
            } else {
                if (isAdmin()) {
                    $stmt = $mysqli->prepare("UPDATE files SET is_deleted=1 WHERE id=?");
                    $stmt->bind_param('i', $bid);
                } else {
                    $stmt = $mysqli->prepare("UPDATE files SET is_deleted=1 WHERE id=? AND owner_username=?");
                    $stmt->bind_param('is', $bid, $username);
                }
            }
            $stmt->execute();
            if ($stmt->affected_rows > 0) $count++;
            $stmt->close();
        }
        $alert_msg = "{$count} item dipindah ke Tong Sampah!";
    }

    // ── 3f. Bulk Move (Pindah Massal ke Folder Tujuan) ────────
    if ($act === 'bulk_move') {
        $ids    = json_decode($_POST['ids']   ?? '[]', true);
        $types  = json_decode($_POST['types'] ?? '[]', true);
        $target = (($_POST['target_folder'] ?? 'root') === 'root')
                    ? null
                    : (int)$_POST['target_folder'];
        $count  = 0;

        for ($i = 0; $i < count($ids); $i++) {
            $bid   = (int)$ids[$i];
            $btype = $types[$i];

            if ($btype === 'folder') {
                if ($target === null) {
                    $stmt = $mysqli->prepare("UPDATE folders SET parent_id=NULL WHERE id=?");
                    $stmt->bind_param('i', $bid);
                } else {
                    $stmt = $mysqli->prepare("UPDATE folders SET parent_id=? WHERE id=? AND id!=?");
                    $stmt->bind_param('iii', $target, $bid, $target);
                }
            } else {
                if ($target === null) {
                    $stmt = $mysqli->prepare("UPDATE files SET folder_id=NULL WHERE id=?");
                    $stmt->bind_param('i', $bid);
                } else {
                    $stmt = $mysqli->prepare("UPDATE files SET folder_id=? WHERE id=?");
                    $stmt->bind_param('ii', $target, $bid);
                }
            }
            $stmt->execute();
            if ($stmt->affected_rows > 0) $count++;
            $stmt->close();
        }
        $alert_msg = "{$count} item berhasil dipindahkan!";
    }
}


// ════════════════════════════════════════════════════════════
// SECTION 4 — TRASH GET ACTIONS (perlu login + $username)
// Semua action di sini melakukan redirect setelah selesai.
// ════════════════════════════════════════════════════════════

if (isset($_GET['action']) && !empty($_SESSION['username'])) {
    $gact = $_GET['action'];

    // ── 4a. Soft Delete Folder → Tong Sampah ─────────────────
    if ($gact === 'soft_delete_folder' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        if (isAdmin()) {
            $stmt = $mysqli->prepare("UPDATE folders SET is_deleted=1 WHERE id=?");
            $stmt->bind_param('i', $id);
        } else {
            $stmt = $mysqli->prepare("UPDATE folders SET is_deleted=1 WHERE id=? AND owner_username=?");
            $stmt->bind_param('is', $id, $username);
        }
        $stmt->execute();
        $stmt->close();
        header("Location: index.php?page=workspace");
        exit;
    }

    // ── 4b. Soft Delete Item (File/Link) → Tong Sampah ───────
    if ($gact === 'soft_delete_item' && isset($_GET['item_id'])) {
        $id = (int)$_GET['item_id'];
        if (isAdmin()) {
            $stmt = $mysqli->prepare("UPDATE files SET is_deleted=1 WHERE id=?");
            $stmt->bind_param('i', $id);
        } else {
            $stmt = $mysqli->prepare("UPDATE files SET is_deleted=1 WHERE id=? AND owner_username=?");
            $stmt->bind_param('is', $id, $username);
        }
        $stmt->execute();
        $stmt->close();
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=workspace'));
        exit;
    }

    // ── 4c. Restore Item dari Tong Sampah ─────────────────────
    if ($gact === 'restore' && isset($_GET['id'], $_GET['type'])) {
        $id    = (int)$_GET['id'];
        $type  = $_GET['type'];
        // Tentukan tabel berdasarkan tipe item
        $table = ($type === 'folder') ? 'folders' : 'files';

        if (isAdmin()) {
            $stmt = $mysqli->prepare("UPDATE {$table} SET is_deleted=0 WHERE id=?");
            $stmt->bind_param('i', $id);
        } else {
            $stmt = $mysqli->prepare("UPDATE {$table} SET is_deleted=0 WHERE id=? AND owner_username=?");
            $stmt->bind_param('is', $id, $username);
        }
        $stmt->execute();
        $stmt->close();
        header("Location: index.php?page=workspace&view=trash");
        exit;
    }

    // ── 4d. Hard Delete — Hapus Permanen dari Tong Sampah ────
    if ($gact === 'hard_delete' && isset($_GET['id'], $_GET['type'])) {
        $id   = (int)$_GET['id'];
        $type = $_GET['type'];

        if ($type === 'file') {
            // Ambil data dulu untuk bisa menghapus file fisik jika ada
            $stmt = $mysqli->prepare("SELECT * FROM files WHERE id=? AND is_deleted=1");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $fd = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($fd && (isAdmin() || $fd['owner_username'] === $username)) {
                // Hapus file fisik dari disk jika bukan link
                if ($fd['jenis'] === 'file' && file_exists(UPLOAD_DIR . $fd['file_path'])) {
                    unlink(UPLOAD_DIR . $fd['file_path']);
                }
                // Hapus record dari database
                $stmt = $mysqli->prepare("DELETE FROM files WHERE id=?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            // Hapus folder (hanya yang sudah di tong sampah)
            if (isAdmin()) {
                $stmt = $mysqli->prepare("DELETE FROM folders WHERE id=? AND is_deleted=1");
                $stmt->bind_param('i', $id);
            } else {
                $stmt = $mysqli->prepare(
                    "DELETE FROM folders WHERE id=? AND is_deleted=1 AND owner_username=?"
                );
                $stmt->bind_param('is', $id, $username);
            }
            $stmt->execute();
            $stmt->close();
        }
        header("Location: index.php?page=workspace&view=trash");
        exit;
    }
}
