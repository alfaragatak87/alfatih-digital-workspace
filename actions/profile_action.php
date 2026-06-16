<?php
// ============================================================
// ALFATIH DIGITAL WORKSPACE — actions/profile_action.php
// Fokus: Menyimpan data form CV Builder & pengaturan profil.
// Mencakup:
//   POST action=update_settings  — ubah nama, password, foto profil
//   POST action=save_profile_data — simpan tab CV (identitas,
//     pendidikan, pengalaman, keahlian + portfolio)
//
// Dipanggil oleh: index.php (dalam blok POST authenticated)
// Requires: config/database.php, core/auth.php
// Variabel global yang dibutuhkan dari index.php:
//   $mysqli, $username (string), $uid (int), $alert_msg (string ref)
// ============================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    !empty($_POST['action'])  &&
    !empty($_SESSION['username'])
) {
    $act = $_POST['action'];

    // ── Update Pengaturan Akun ────────────────────────────────
    // Menangani perubahan nama tampilan, password, dan foto profil.
    // Semua kombinasi field (foto+pass, foto saja, pass saja, nama saja)
    // ditangani agar query UPDATE selalu efisien dan tidak null-override.
    if ($act === 'update_settings') {
        $new_name = trim($_POST['nama_lengkap'] ?? '');
        $new_pass = $_POST['new_password'] ?? '';
        $foto_set = ''; // Akan diisi jika upload foto berhasil

        // Proses upload foto profil baru (jika ada)
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === 0) {
            $ext = strtolower(pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $new_fn = $username . '_' . time() . '.' . $ext;
                if (!is_dir(PROFILE_IMG_DIR)) {
                    mkdir(PROFILE_IMG_DIR, 0777, true);
                }
                if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], PROFILE_IMG_DIR . $new_fn)) {
                    $foto_set = $new_fn;
                }
            }
        }

        // Pilih query UPDATE sesuai kombinasi field yang berubah
        if (!empty($new_pass)) {
            $hash = password_hash($new_pass, PASSWORD_BCRYPT);
            if ($foto_set) {
                // Password + Foto baru
                $stmt = $mysqli->prepare(
                    "UPDATE users SET nama_lengkap=?, password=?, foto_profil=? WHERE id=?"
                );
                $stmt->bind_param('sssi', $new_name, $hash, $foto_set, $uid);
            } else {
                // Password saja (tanpa ganti foto)
                $stmt = $mysqli->prepare(
                    "UPDATE users SET nama_lengkap=?, password=? WHERE id=?"
                );
                $stmt->bind_param('ssi', $new_name, $hash, $uid);
            }
        } else {
            if ($foto_set) {
                // Foto baru (tanpa ganti password)
                $stmt = $mysqli->prepare(
                    "UPDATE users SET nama_lengkap=?, foto_profil=? WHERE id=?"
                );
                $stmt->bind_param('ssi', $new_name, $foto_set, $uid);
            } else {
                // Hanya perbarui nama tampilan
                $stmt = $mysqli->prepare(
                    "UPDATE users SET nama_lengkap=? WHERE id=?"
                );
                $stmt->bind_param('si', $new_name, $uid);
            }
        }

        $stmt->execute();
        $stmt->close();
        // Perbarui nama di session agar navbar langsung berubah tanpa logout
        $_SESSION['nama'] = $new_name;
        $alert_msg = "Profil berhasil diperbarui!";
    }

    // ── Simpan Data CV Builder (per Tab) ─────────────────────
    // Sistem CV Builder menggunakan pola partial-save per tab.
    // Data disimpan sebagai JSON di kolom profile_data di tabel users.
    // Setiap submit hanya memperbarui key tab yang aktif, sehingga
    // tab lain tidak tertimpa.
    if ($act === 'save_profile_data') {
        $tab = $_POST['profile_tab'] ?? 'identitas';

        // Ambil data JSON yang sudah ada untuk digabung (merge)
        $stmt = $mysqli->prepare("SELECT profile_data FROM users WHERE id=?");
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $prow = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $pd = json_decode($prow['profile_data'] ?? '{}', true) ?? [];

        // ── Tab: Identitas Diri ───────────────────────────────
        if ($tab === 'identitas') {
            $pd['identitas'] = [
                'nama_sebutan'  => trim($_POST['pd_nama_sebutan'] ?? ''),
                'nama_lengkap'  => trim($_POST['pd_nama']         ?? ''),
                'profesi'       => trim($_POST['pd_profesi']      ?? ''),
                'tagline'       => trim($_POST['pd_tagline']      ?? ''),
                'provinsi'      => trim($_POST['pd_provinsi']     ?? ''),
                'kabupaten'     => trim($_POST['pd_kabupaten']    ?? ''),
                'kecamatan'     => trim($_POST['pd_kecamatan']    ?? ''),
                'desa'          => trim($_POST['pd_desa']         ?? ''),
                'email'         => trim($_POST['pd_email']        ?? ''),
                'phone'         => trim($_POST['pd_phone']        ?? ''),
                'github'        => trim($_POST['pd_github']       ?? ''),
                'linkedin'      => trim($_POST['pd_linkedin']     ?? ''),
                'instagram'     => trim($_POST['pd_instagram']    ?? ''),
                'website'       => trim($_POST['pd_website']      ?? ''),
                'summary'       => trim($_POST['pd_summary']      ?? ''),
                // Checkbox: 1 jika dicentang, 0 jika tidak
                'tampil_publik' => isset($_POST['pd_tampil_publik']) ? 1 : 0,
            ];
        }

        // ── Tab: Riwayat Pendidikan ───────────────────────────
        // Input berupa array paralel (nama field dengan []) dari
        // dynamic item yang bisa ditambah/hapus oleh user.
        elseif ($tab === 'pendidikan') {
            $edu_items      = [];
            $institusi_list = $_POST['edu_institusi'] ?? [];

            foreach ($institusi_list as $ei => $inst) {
                $edu_items[] = [
                    'institusi'     => trim($inst),
                    'gelar'         => trim($_POST['edu_gelar'][$ei]    ?? ''),
                    'bidang'        => trim($_POST['edu_bidang'][$ei]   ?? ''),
                    'tahun_mulai'   => trim($_POST['edu_mulai'][$ei]    ?? ''),
                    'tahun_selesai' => trim($_POST['edu_selesai'][$ei]  ?? ''),
                    'is_current'    => (isset($_POST['edu_is_current_tmp'][$ei]) && $_POST['edu_is_current_tmp'][$ei] === '1') ? 1 : 0,
                    'ipk_nilai'     => trim($_POST['edu_ipk'][$ei]      ?? ''),
                    'deskripsi'     => trim($_POST['edu_desc'][$ei]     ?? ''),
                ];
            }
            $pd['pendidikan'] = $edu_items;
        }

        // ── Tab: Pengalaman Kerja ─────────────────────────────
        elseif ($tab === 'pengalaman') {
            $exp_items    = [];
            $jabatan_list = $_POST['exp_jabatan'] ?? [];

            foreach ($jabatan_list as $ei => $jab) {
                $exp_items[] = [
                    'jabatan'    => trim($jab),
                    'perusahaan' => trim($_POST['exp_perusahaan'][$ei] ?? ''),
                    'tahun_mulai'=> trim($_POST['exp_mulai'][$ei]      ?? ''),
                    'tahun_selesai'=> trim($_POST['exp_selesai'][$ei]  ?? ''),
                    'is_current' => (isset($_POST['exp_is_current_tmp'][$ei]) && $_POST['exp_is_current_tmp'][$ei] === '1') ? 1 : 0,
                    'deskripsi'  => trim($_POST['exp_desc'][$ei]       ?? ''),
                ];
            }
            $pd['pengalaman'] = $exp_items;
        }

        // ── Tab: Keahlian & Portfolio ─────────────────────────
        // Tab ini menyimpan DUA entitas sekaligus: skill dan portfolio
        // karena keduanya berada dalam satu tab yang sama di UI.
        elseif ($tab === 'keahlian') {
            // Bagian Skill / Keahlian
            $skill_items = [];
            $skill_names = $_POST['skill_nama'] ?? [];

            foreach ($skill_names as $si => $sn) {
                $skill_items[] = [
                    'nama'      => trim($sn),
                    'level'     => trim($_POST['skill_level'][$si]     ?? 'Menengah'),
                    'kategori'  => trim($_POST['skill_kategori'][$si]  ?? ''),
                    'logo_icon' => trim($_POST['skill_logo'][$si]      ?? ''),
                ];
            }
            $pd['keahlian'] = $skill_items;

            // Bagian Portfolio / Proyek
            $porto_items = [];
            $porto_names = $_POST['porto_nama'] ?? [];

            foreach ($porto_names as $pi => $pn) {
                $porto_items[] = [
                    'nama'      => trim($pn),
                    'url'       => trim($_POST['porto_url'][$pi]  ?? ''),
                    'deskripsi' => trim($_POST['porto_desc'][$pi] ?? ''),
                    'tech'      => trim($_POST['porto_tech'][$pi] ?? ''),
                ];
            }
            $pd['portfolio'] = $porto_items;
        }

        // Encode kembali ke JSON dan simpan ke database
        $json = json_encode($pd, JSON_UNESCAPED_UNICODE);
        $stmt = $mysqli->prepare("UPDATE users SET profile_data=? WHERE id=?");
        $stmt->bind_param('si', $json, $uid);
        $stmt->execute();
        $stmt->close();
        $alert_msg = "Data profil berhasil disimpan!";
    }
}
