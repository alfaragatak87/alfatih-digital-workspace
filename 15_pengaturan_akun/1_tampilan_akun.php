<?php
// +------------------------------------------------------------------------------+
// |  FILE: 15_pengaturan_akun/1_tampilan_akun.php                                |
// |  DESKRIPSI: UI Pengaturan Akun, ganti password, & statistik.                 |
// +------------------------------------------------------------------------------+
if (!defined('SITE_URL')) exit;

// Ambil data user lengkap dari tabel users
$stmt = $mysqli->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
$stmt->bind_param('s', $username);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

$u_foto = $user_data['foto_profil'] ?? '';
$u_path = ($u_foto && $u_foto !== 'default.png' && file_exists(PROFILE_IMG_DIR . $u_foto)) ? PROFILE_IMG_DIR . $u_foto : 'https://ui-avatars.com/api/?name='.urlencode($user_data['nama_lengkap']??$username).'&background=1a1a1a&color=ffffff&bold=true';

// Hitung Statistik
$stmt = $mysqli->prepare("SELECT COUNT(*) FROM files WHERE owner_username=? AND is_deleted=0 AND jenis='file'");
$stmt->bind_param('s', $username); $stmt->execute(); $stat_files = $stmt->get_result()->fetch_row()[0]; $stmt->close();

$stmt = $mysqli->prepare("SELECT COUNT(*) FROM todo_list WHERE owner_username=?");
$stmt->bind_param('s', $username); $stmt->execute(); $stat_tasks = $stmt->get_result()->fetch_row()[0]; $stmt->close();

$stmt = $mysqli->prepare("SELECT SUM(nominal) FROM keuangan WHERE owner_username=? AND tipe='Pemasukan'");
$stmt->bind_param('s', $username); $stmt->execute(); $stat_money = $stmt->get_result()->fetch_row()[0] ?? 0; $stmt->close();

?>
<style>
.acc-header {
    background: linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 100%);
    border: 1px solid rgba(139, 92, 246, 0.2);
    border-radius: 20px;
    padding: 32px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 24px;
    position: relative;
    overflow: hidden;
}
.acc-header::before {
    content: ''; position: absolute; top: -50%; right: -10%; width: 50%; height: 200%;
    background: radial-gradient(circle, rgba(139, 92, 246, 0.1) 0%, transparent 70%);
    pointer-events: none;
}
.acc-avatar {
    width: 100px; height: 100px; border-radius: 50%; object-fit: cover;
    border: 4px solid var(--surface); box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.5), 0 8px 24px rgba(0,0,0,0.5);
}
.acc-info h2 { margin: 0 0 4px 0; color: var(--text-main); font-size: 1.8rem; }
.acc-info p { margin: 0; color: #94a3b8; }
.acc-role-badge {
    display: inline-block; background: rgba(139, 92, 246, 0.2); color: #a78bfa;
    padding: 4px 10px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; margin-top: 8px; border: 1px solid rgba(139, 92, 246, 0.4);
}

.acc-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
}
.acc-card {
    background: var(--surface);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 20px;
    padding: 24px;
}
.acc-card h3 { margin: 0 0 20px 0; color: var(--text-main); font-size: 1.2rem; display: flex; align-items: center; gap: 8px; }
.acc-card h3 i { color: #8b5cf6; }

.acc-form-group { margin-bottom: 16px; }
.acc-form-group label { display: block; font-size: 0.85rem; color: #94a3b8; font-weight: 500; margin-bottom: 6px; }
.acc-input {
    width: 100%; background: var(--surface-3); border: 1px solid rgba(255,255,255,0.05);
    color: var(--text-main); padding: 12px 16px; border-radius: 10px; font-size: 0.95rem; box-sizing: border-box; transition: all 0.2s;
}
.acc-input:focus { border-color: #8b5cf6; outline: none; box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2); }
.acc-input:read-only { opacity: 0.7; cursor: not-allowed; }

.stat-box {
    background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);
    border-radius: 12px; padding: 16px; margin-bottom: 12px; display: flex; align-items: center; gap: 16px;
}
.stat-icon {
    width: 48px; height: 48px; border-radius: 12px; background: rgba(139, 92, 246, 0.1);
    color: #a78bfa; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;
}
.stat-val { font-size: 1.4rem; font-weight: 700; color: var(--text-main); margin-bottom: 2px; }
.stat-lbl { font-size: 0.8rem; color: #94a3b8; }

.btn-save {
    background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: white; border: none;
    padding: 12px 24px; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.2s;
    display: inline-flex; align-items: center; gap: 8px; width: 100%; justify-content: center;
}
.btn-save:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4); }

@media (max-width: 900px) {
    .acc-grid { grid-template-columns: 1fr; }
    .acc-header { flex-direction: column; text-align: center; }
}
</style>

<div style="padding: 24px;">

    <?php if(isset($_SESSION['flash_success'])){ ?>
        <div style="background:rgba(16,185,129,0.1);border:1px solid #10b981;color:#10b981;padding:16px;border-radius:12px;margin-bottom:24px;font-weight:600;"><i class="fa-solid fa-check-circle"></i> <?= $_SESSION['flash_success'] ?></div>
    <?php unset($_SESSION['flash_success']); } ?>
    <?php if(isset($_SESSION['flash_error'])){ ?>
        <div style="background:rgba(239,68,68,0.1);border:1px solid #ef4444;color:#ef4444;padding:16px;border-radius:12px;margin-bottom:24px;font-weight:600;"><i class="fa-solid fa-triangle-exclamation"></i> <?= $_SESSION['flash_error'] ?></div>
    <?php unset($_SESSION['flash_error']); } ?>

    <!-- HEADER -->
    <div class="acc-header">
        <img src="<?= h($u_path) ?>" alt="Avatar" class="acc-avatar">
        <div class="acc-info">
            <h2><?= h($user_data['nama_lengkap'] ?? $username) ?></h2>
            <p>@<?= h($username) ?> &middot; Anggota sejak <?= date('d M Y', strtotime($user_data['created_at'] ?? '2024-01-01')) ?></p>
            <div class="acc-role-badge"><i class="fa-solid fa-shield-halved"></i> Role: <?= strtoupper($user_data['role'] ?? 'USER') ?></div>
            <?php if(isset($_SESSION['impersonator'])){ ?>
                <a href="index.php?action=stop_impersonate" style="display:inline-block; margin-left:12px; background:#ef4444; color:var(--text-main); padding:4px 10px; border-radius:8px; font-size:0.8rem; font-weight:bold; text-decoration:none;"><i class="fa-solid fa-person-walking-arrow-right"></i> Kembali ke God Mode</a>
            <?php } ?>
        </div>
    </div>

    <div class="acc-grid">
        <!-- LEFT COL: FORMS -->
        <div style="display:flex; flex-direction:column; gap:24px;">
            
            <!-- INFORMASI PRIBADI -->
            <div class="acc-card">
                <h3><i class="fa-regular fa-id-card"></i> Informasi Pribadi</h3>
                <form action="index.php" method="POST">
                    <input type="hidden" name="action" value="update_pengaturan_akun">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="acc-form-group">
                            <label>Username (Tidak dapat diubah)</label>
                            <input type="text" class="acc-input" value="<?= h($username) ?>" readonly>
                        </div>
                        <div class="acc-form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="acc-input" value="<?= h($user_data['nama_lengkap']??'') ?>" required>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="acc-form-group">
                            <label>Nama Panggilan</label>
                            <input type="text" name="nama_panggilan" class="acc-input" value="<?= h($user_data['nama_panggilan']??'') ?>">
                        </div>
                        <div class="acc-form-group">
                            <label>Email Utama</label>
                            <input type="email" name="email" class="acc-input" value="<?= h($user_data['email']??'') ?>" required>
                        </div>
                    </div>
                    <div class="acc-form-group">
                        <label>Profesi Saat Ini</label>
                        <input type="text" name="profesi" class="acc-input" value="<?= h($user_data['profesi']??'') ?>">
                    </div>
                    <div style="margin-top:24px; text-align:right;">
                        <button type="submit" class="btn-save" style="width:auto;"><i class="fa-solid fa-floppy-disk"></i> Simpan Profil</button>
                    </div>
                </form>
            </div>

            <!-- KEAMANAN / GANTI PASSWORD -->
            <div class="acc-card">
                <h3><i class="fa-solid fa-lock"></i> Keamanan & Kata Sandi</h3>
                <form action="index.php" method="POST">
                    <input type="hidden" name="action" value="update_password_akun">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="acc-form-group">
                        <label>Kata Sandi Saat Ini</label>
                        <input type="password" name="password_lama" class="acc-input" required placeholder="Masukkan password lama Anda">
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        <div class="acc-form-group">
                            <label>Kata Sandi Baru</label>
                            <input type="password" name="password_baru" class="acc-input" required placeholder="Minimal 6 karakter">
                        </div>
                        <div class="acc-form-group">
                            <label>Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_konfirmasi" class="acc-input" required placeholder="Ulangi password baru">
                        </div>
                    </div>
                    <div style="margin-top:24px; text-align:right;">
                        <button type="submit" class="btn-save" style="width:auto; background:linear-gradient(135deg, #10b981, #059669);"><i class="fa-solid fa-key"></i> Perbarui Kata Sandi</button>
                    </div>
                </form>
            </div>

        </div>

        <!-- RIGHT COL: STATS -->
        <div>
            <div class="acc-card">
                <h3><i class="fa-solid fa-chart-pie"></i> Statistik Penggunaan</h3>
                
                <div class="stat-box">
                    <div class="stat-icon"><i class="fa-regular fa-file"></i></div>
                    <div>
                        <div class="stat-val"><?= number_format($stat_files) ?></div>
                        <div class="stat-lbl">File di Workspace</div>
                    </div>
                </div>
                
                <div class="stat-box">
                    <div class="stat-icon" style="color:#10b981; background:rgba(16,185,129,0.1);"><i class="fa-solid fa-list-check"></i></div>
                    <div>
                        <div class="stat-val"><?= number_format($stat_tasks) ?></div>
                        <div class="stat-lbl">Tugas (To-Do List)</div>
                    </div>
                </div>
                
                <div class="stat-box">
                    <div class="stat-icon" style="color:#f59e0b; background:rgba(245,158,11,0.1);"><i class="fa-solid fa-wallet"></i></div>
                    <div>
                        <div class="stat-val" style="font-size:1.1rem;">Rp <?= number_format($stat_money, 0, ',', '.') ?></div>
                        <div class="stat-lbl">Total Pemasukan Dicatat</div>
                    </div>
                </div>

                <div style="margin-top:24px; text-align:center; padding-top:24px; border-top:1px solid rgba(255,255,255,0.05);">
                    <p style="font-size:0.8rem; color:#64748b; margin-bottom:12px;">Ingin menghapus akun Anda sepenuhnya? Tindakan ini tidak dapat dibatalkan.</p>
                    <button class="btn-save" style="background:rgba(239,68,68,0.1); color:#ef4444; border:1px solid rgba(239,68,68,0.3);" onclick="alert('Fitur hapus akun perlu persetujuan Admin Hub. Silakan kontak Admin.')"><i class="fa-solid fa-user-xmark"></i> Hapus Akun Secara Permanen</button>
                </div>

            </div>
        </div>
    </div>
</div>

