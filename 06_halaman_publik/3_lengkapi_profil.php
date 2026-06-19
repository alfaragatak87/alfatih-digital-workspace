<?php
if (!defined('SITE_URL')) exit;

$uid = $_SESSION['uid'] ?? 0;
$error_msg = '';

$req_action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($req_action === 'simpan_profil') {
    $profesi = trim($_POST['profesi'] ?? $_GET['profesi'] ?? '');
    $nama_panggilan = trim($_POST['nama_panggilan'] ?? $_GET['nama_panggilan'] ?? '');
    
    if ($profesi && $nama_panggilan && $uid) {
        $stmt = $mysqli->prepare("UPDATE users SET profesi=?, nama_panggilan=? WHERE id=?");
        if ($stmt) {
            $stmt->bind_param("ssi", $profesi, $nama_panggilan, $uid);
            if ($stmt->execute()) {
                $_SESSION['profesi'] = $profesi;
                $_SESSION['nama_panggilan'] = $nama_panggilan;
                header("Location: index.php?page=beranda");
                exit;
            } else {
                $error_msg = "Gagal memperbarui data: " . $stmt->error;
            }
        } else {
            $error_msg = "Error sistem: " . $mysqli->error;
        }
    } else {
        $error_msg = "Pastikan semua data terisi dengan benar. (UID: $uid, Profesi: $profesi, Nama: $nama_panggilan)";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alfatih Digital Workspace - Lengkapi Profil</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background: #0f172a; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; color: #f8fafc; }
        .card { background: #1e293b; padding: 40px; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); width: 100%; max-width: 450px; text-align: center; }
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; margin-bottom: 8px; font-size: 0.9rem; color: #94a3b8; }
        .form-control { width: 100%; padding: 12px 16px; border-radius: 8px; background: #0f172a; border: 1px solid #334155; color: #f8fafc; font-family: inherit; font-size: 1rem; box-sizing: border-box; }
        .form-control:focus { outline: none; border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,0.2); }
        .btn { display: inline-block; width: 100%; padding: 14px; border-radius: 8px; background: #8b5cf6; color: white; font-weight: 600; border: none; cursor: pointer; transition: all 0.3s ease; font-family: inherit; font-size: 1rem; }
        .btn:hover { background: #7c3aed; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="card">
        <div style="margin-bottom: 30px;">
            <h2>Satu Langkah Lagi...</h2>
            <p style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 24px;">Silakan lengkapi profil Anda agar kami dapat menyiapkan fitur yang sesuai dengan profesi Anda.</p>
        </div>

        <div style="background: #2d2d2d; border: 1px solid #444; color: #a3e635; padding: 10px; border-radius: 8px; margin-bottom: 16px; font-family: monospace; font-size: 0.75rem; word-break: break-all;">
            <strong>DIAGNOSTIC (HARAP SCREENSHOT INI!):</strong><br>
            REQ_METHOD: <?= $_SERVER['REQUEST_METHOD'] ?><br>
            GET: <?= json_encode($_GET) ?><br>
            POST: <?= json_encode($_POST) ?><br>
            SESSION UID: <?= $uid ?><br>
            REQ_ACTION: <?= $req_action ?>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #f87171; padding: 12px; border-radius: 8px; margin-bottom: 16px; font-size: 0.85rem;">
                <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <form method="GET" action="index.php">
            <input type="hidden" name="page" value="lengkapi_profil">
            <input type="hidden" name="action" value="simpan_profil">
            
            <div class="form-group">
                <label>Nama Panggilan</label>
                <input type="text" name="nama_panggilan" class="form-control" required placeholder="Cth: Budi" value="<?= htmlspecialchars($_SESSION['nama_panggilan'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Profesi / Pekerjaan Anda</label>
                <select name="profesi" class="form-control" required>
                    <option value="">-- Pilih Profesi --</option>
                    <option value="Pegawai Balai Desa">Pegawai Balai Desa</option>
                    <option value="Guru / Dosen">Guru / Dosen</option>
                    <option value="Pekerja Lepas / Kreatif">Pekerja Lepas / Programmer</option>
                    <option value="Mahasiswa">Mahasiswa</option>
                    <option value="Pelajar">Pelajar</option>
                    <option value="Lainnya">Lainnya...</option>
                </select>
            </div>

            <button type="submit" class="btn">Selesai & Masuk Dashboard</button>
        </form>
    </div>
</body>
</html>
