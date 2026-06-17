<?php
if (!defined('SITE_URL')) exit;

$uid = $_SESSION['uid'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'simpan_profil') {
    $profesi = trim($_POST['profesi'] ?? '');
    $nama_panggilan = trim($_POST['nama_panggilan'] ?? '');
    
    if ($profesi && $nama_panggilan && $uid) {
        $stmt = $mysqli->prepare("UPDATE users SET profesi=?, nama_panggilan=? WHERE id=?");
        if ($stmt) {
            $stmt->bind_param("ssi", $profesi, $nama_panggilan, $uid);
            if ($stmt->execute()) {
                $_SESSION['profesi'] = $profesi;
                $_SESSION['nama_panggilan'] = $nama_panggilan;
                header("Location: index.php?page=beranda");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Profil — Alfatih Workspace</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background: #09090b; color: #f8fafc; font-family: 'Inter', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .box { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); padding: 32px; border-radius: 16px; width: 100%; max-width: 400px; text-align: center; }
        .box h2 { font-family: 'Outfit', sans-serif; margin-top: 0; }
        .form-group { margin-bottom: 16px; text-align: left; }
        label { display: block; margin-bottom: 8px; font-size: 0.9rem; color: #94a3b8; }
        input, select { width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #8b5cf6; border: none; border-radius: 8px; color: #fff; font-weight: 600; cursor: pointer; margin-top: 16px; }
        button:hover { background: #7c3aed; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Satu Langkah Lagi...</h2>
        <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 24px;">Silakan lengkapi profil Anda agar kami dapat menyiapkan fitur yang sesuai dengan profesi Anda.</p>
        <form method="POST" action="index.php?page=lengkapi_profil">
            <input type="hidden" name="action" value="simpan_profil">
            
            <div class="form-group">
                <label>Nama Panggilan</label>
                <input type="text" name="nama_panggilan" required placeholder="Cth: Budi" value="<?= htmlspecialchars($_SESSION['nama_panggilan'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Profesi / Pekerjaan Anda</label>
                <select name="profesi" required>
                    <option value="">-- Pilih Profesi --</option>
                    <option value="Pegawai Balai Desa">Pegawai Balai Desa</option>
                    <option value="Guru / Dosen">Guru / Dosen</option>
                    <option value="Pekerja Lepas / Kreatif">Pekerja Lepas / Programmer</option>
                    <option value="Mahasiswa">Mahasiswa</option>
                    <option value="Pelajar">Pelajar</option>
                    <option value="Lainnya">Lainnya...</option>
                </select>
            </div>

            <button type="submit">Selesai & Masuk Dashboard</button>
        </form>
    </div>
</body>
</html>
