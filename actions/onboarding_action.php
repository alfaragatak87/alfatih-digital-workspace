<?php
// ============================================================
// actions/onboarding_action.php
// Menyimpan data dari wizard onboarding (step 1–3) ke database.
//
// Di-require dari index.php sebelum routing halaman.
// Dipanggil saat: POST action=save_onboarding
//
// Variabel global yang dibutuhkan:
//   $mysqli, $uid (int), $username (string), $csrf_token (string)
// ============================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    ($_POST['action'] ?? '') === 'save_onboarding' &&
    !empty($_SESSION['username'])
) {
    // Validasi CSRF
    if (!validateCSRF()) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'msg' => 'CSRF tidak valid']);
        exit;
    }

    // ── Ambil data dari POST ──────────────────────────────────
    $profesi_key    = trim($_POST['ob_profesi_key']    ?? '');
    $profesi_custom = trim($_POST['ob_profesi_custom'] ?? '');
    $nama_sebutan   = trim($_POST['ob_nama_sebutan']   ?? '');
    $tagline        = trim($_POST['ob_tagline']        ?? '');
    $skills_json    = $_POST['ob_skills']              ?? '[]';

    // Parse skills yang dikirim sebagai JSON string
    $skills_raw = json_decode($skills_json, true) ?? [];

    // ── Tentukan label profesi ────────────────────────────────
    $profesi_label_map = [
        'it'         => 'IT / Developer',
        'kreatif'    => 'Kreatif & Desain',
        'pendidikan' => 'Pendidikan',
        'bisnis'     => 'Bisnis & Usaha',
        'kesehatan'  => 'Kesehatan',
        'hukum'      => 'Hukum & Pemerintahan',
        'teknik'     => 'Teknik & Produksi',
        'lainnya'    => $profesi_custom ?: 'Lainnya',
    ];
    $profesi_label = $profesi_label_map[$profesi_key] ?? ($profesi_custom ?: 'Lainnya');

    // ── Ambil profile_data lama (merge, jangan overwrite) ────
    $stmt = $mysqli->prepare("SELECT profile_data FROM users WHERE id=?");
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $pd = json_decode($row['profile_data'] ?? '{}', true) ?? [];

    // ── Update section identitas ──────────────────────────────
    if (!isset($pd['identitas'])) $pd['identitas'] = [];

    if (!empty($nama_sebutan)) {
        $pd['identitas']['nama_sebutan'] = $nama_sebutan;
    }
    if (!empty($tagline)) {
        $pd['identitas']['tagline'] = $tagline;
    }
    if (!empty($profesi_label)) {
        $pd['identitas']['profesi'] = $profesi_label;
        $pd['identitas']['profesi_category'] = $profesi_key;
    }

    // ── Simpan skill ke section skill_umum ───────────────────
    // Bedakan skill teknis (IT) vs skill umum
    $it_cats = ['Frontend','Backend','Database','Mobile','DevOps / CI-CD','Cloud','UI/UX','Infrastruktur'];
    $skill_umum   = [];
    $skill_teknis = [];

    foreach ($skills_raw as $s) {
        $item = [
            'nama'      => trim($s['nama']  ?? ''),
            'kategori'  => trim($s['kat']   ?? ''),
            'level'     => 70, // default level saat onboarding
            'preset_id' => null,
        ];
        if (empty($item['nama'])) continue;

        if (in_array($item['kategori'], $it_cats)) {
            $skill_teknis[] = $item;
        } else {
            $skill_umum[] = $item;
        }
    }

    // Merge dengan skill yang sudah ada (jangan duplikat)
    $existing_umum   = $pd['skill_umum']   ?? [];
    $existing_teknis = $pd['skill_teknis'] ?? [];

    foreach ($skill_umum as $ns) {
        $exists = array_filter($existing_umum, fn($e) => $e['nama'] === $ns['nama']);
        if (empty($exists)) $existing_umum[] = $ns;
    }
    foreach ($skill_teknis as $ns) {
        $exists = array_filter($existing_teknis, fn($e) => $e['nama'] === $ns['nama']);
        if (empty($exists)) $existing_teknis[] = $ns;
    }

    $pd['skill_umum']   = array_values($existing_umum);
    $pd['skill_teknis'] = array_values($existing_teknis);

    // ── Proses upload foto profil (jika ada) ─────────────────
    $foto_updated = '';
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === 0) {
        $file     = $_FILES['foto_profil'];
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $allowed) && $file['size'] <= 3 * 1024 * 1024) {
            $new_fn = $_SESSION['username'] . '_' . time() . '.' . $ext;
            if (!is_dir(PROFILE_IMG_DIR)) mkdir(PROFILE_IMG_DIR, 0777, true);
            if (move_uploaded_file($file['tmp_name'], PROFILE_IMG_DIR . $new_fn)) {
                $foto_updated = $new_fn;
            }
        }
    }

    // ── Simpan ke database ───────────────────────────────────
    $pd_json = json_encode($pd, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    if ($foto_updated) {
        // Update profile_data + foto_profil + is_onboarded + profesi_category
        $stmt = $mysqli->prepare(
            "UPDATE users
             SET profile_data=?, foto_profil=?, is_onboarded=1, profesi_category=?
             WHERE id=?"
        );
        $stmt->bind_param('sssi', $pd_json, $foto_updated, $profesi_key, $uid);
        // Juga update session nama_sebutan agar langsung terasa
        if (!empty($nama_sebutan)) $_SESSION['nama'] = $nama_sebutan;
    } else {
        $stmt = $mysqli->prepare(
            "UPDATE users
             SET profile_data=?, is_onboarded=1, profesi_category=?
             WHERE id=?"
        );
        $stmt->bind_param('ssi', $pd_json, $profesi_key, $uid);
    }
    $stmt->execute();
    $stmt->close();

    // Update session nama jika ada
    if (!empty($nama_sebutan)) {
        $_SESSION['nama'] = $nama_sebutan;
    }

    // Catat aktivitas
    logActivity($mysqli, $uid, 'ONBOARDING_COMPLETE', 'User menyelesaikan wizard onboarding');

    // Kembalikan response sukses (request dari JS fetch)
    // Jika request normal POST, redirect saja
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
    } else {
        header('Location: index.php?page=beranda');
    }
    exit;
}
