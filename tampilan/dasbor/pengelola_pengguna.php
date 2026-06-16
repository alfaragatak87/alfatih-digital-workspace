<?php if (!defined('SITE_URL')) exit; 
if (!isSuperAdmin()) {
    echo "<div class='empty-state' style='cursor:default;'><i class='fa-solid fa-lock'></i><h3>Akses Ditolak</h3><p>Hanya untuk Super Admin.</p></div>";
    return;
}

$ctrl_user = isset($_GET['ctrl_user']) ? $_GET['ctrl_user'] : null; 
$ctrl_data = null;
if ($ctrl_user) {
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param('s', $ctrl_user); $stmt->execute(); 
    $ctrl_data = $stmt->get_result()->fetch_assoc(); $stmt->close();
}
?>
<style>
/* User Management specific styles */
.um-header-card {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
    border: 1px solid rgba(99, 102, 241, 0.3);
    border-radius: 20px;
    padding: 32px;
    margin-bottom: 32px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}
.um-header-left h1 { margin: 0 0 8px 0; font-size: 2rem; color: #fff; }
.um-header-left p { margin: 0; color: #a5b4fc; font-size: 0.95rem; }
.god-mode-badge {
    background: #4f46e5;
    color: white;
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: bold;
    display: inline-block;
    margin-bottom: 12px;
}
.um-control-panel {
    background: #1c1e29;
    border: 1px solid rgba(245, 158, 11, 0.3);
    border-left: 4px solid #f59e0b;
    border-radius: 16px;
    margin-bottom: 32px;
    overflow: hidden;
}
.um-cp-header {
    background: rgba(245, 158, 11, 0.1);
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid rgba(245, 158, 11, 0.1);
}
.um-cp-header h3 { margin: 0; color: #fbbf24; font-size: 1.1rem; }
.um-cp-header a { color: #94a3b8; text-decoration: none; font-size: 0.85rem; transition: color 0.2s; font-weight: 600; }
.um-cp-header a:hover { color: #fff; }
.um-cp-body { padding: 24px; }

.user-list-container {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.um-table-header {
    margin-bottom: 20px;
    padding: 0 8px;
}
.um-table-header h3 { margin: 0; color: #fff; font-size: 1.2rem; display: flex; align-items: center; gap: 8px; }

.user-list-item {
    background: #1c1e29;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 16px;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    transition: all 0.2s;
}
.user-list-item:hover {
    background: #252836;
    border-color: rgba(99, 102, 241, 0.4);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
}

.uli-profile { display: flex; align-items: center; gap: 16px; flex: 2; min-width: 250px; }
.uli-meta { display: flex; align-items: center; gap: 24px; flex: 3; justify-content: space-between; }
.uli-username { font-family: monospace; color: #94a3b8; font-size: 0.95rem; }
.uli-role { min-width: 100px; }
.uli-last-login { font-size: 0.85rem; color: #cbd5e1; display: flex; align-items: center; gap: 6px; min-width: 140px; }
.uli-actions { display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end; flex: 1.5; min-width: 180px; }

.action-btn-sm {
    background: rgba(255,255,255,0.05);
    border: none;
    color: #cbd5e1;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.action-btn-sm:hover { background: rgba(255,255,255,0.1); color: #fff; }
.action-btn-sm.edit-btn:hover { background: rgba(16, 185, 129, 0.2); color: #10b981; }
.action-btn-sm.del-btn:hover { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
.action-btn-sm.view-btn:hover { background: rgba(56, 189, 248, 0.2); color: #38bdf8; }

@media (max-width: 900px) {
    .um-header-card { flex-direction: column; text-align: center; }
    .um-header-left { align-items: center; display: flex; flex-direction: column; }
    .user-list-item { flex-direction: column; align-items: flex-start; gap: 16px; }
    .uli-meta { width: 100%; justify-content: flex-start; flex-wrap: wrap; gap: 16px; }
    .uli-actions { width: 100%; justify-content: flex-start; }
}
</style>

<div style="padding: 24px;">
    <div class="um-header-card">
        <div class="um-header-left">
            <span class="god-mode-badge"><i class="fa-solid fa-bolt"></i> God Mode</span>
            <h1>Manajemen Pengguna</h1>
            <p>Kontrol penuh atas semua akun dalam sistem.</p>
        </div>
        <div>
            <button class="btn-primary" onclick="openModal('addUserModal')" style="padding: 12px 24px; border-radius: 10px; font-size: 0.95rem; font-weight: 600; display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-user-plus"></i> Tambah User</button>
        </div>
    </div>
<?php if($ctrl_user && $ctrl_data){ ?>
    <div class="um-control-panel">
        <div class="um-cp-header">
            <h3><i class="fa-solid fa-user-shield"></i> Control Panel: @<?= h($ctrl_data['username']) ?></h3>
            <a href="index.php?page=manajemen-pengguna"><i class="fa-solid fa-xmark"></i> Tutup Panel</a>
        </div>
        <div class="um-cp-body">
            <?php
            $cu_foto = $ctrl_data['foto_profil'] ?? '';
            $cu_path = ($cu_foto && $cu_foto !== 'default.png' && file_exists(PROFILE_IMG_DIR . $cu_foto)) ? PROFILE_IMG_DIR . $cu_foto : 'https://ui-avatars.com/api/?name='.urlencode($ctrl_data['nama_lengkap']??$ctrl_data['username']).'&background=1a1a1a&color=ffffff&bold=true';
            ?>
            <div style="display:flex;align-items:center;gap:20px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid rgba(255,255,255,0.05); flex-wrap: wrap;">
                <img src="<?= h($cu_path) ?>" style="width:80px;height:80px;object-fit:cover;border-radius:12px;border:2px solid rgba(255,255,255,0.1);" alt="">
                <div>
                    <div style="font-size:1.4rem;font-weight:800;margin-bottom:4px;color:#fff;"><?= h($ctrl_data['nama_lengkap']??$ctrl_data['username']) ?></div>
                    <div style="font-size:.85rem;color:#94a3b8;margin-bottom:12px;">@<?= h($ctrl_data['username']) ?> &middot; <span class="role-badge <?= h($ctrl_data['role']) ?>" style="background:rgba(255,255,255,0.1);padding:4px 8px;border-radius:6px;"><?= h($ctrl_data['role']) ?></span></div>
                    <div style="display:flex;gap:8px; flex-wrap: wrap;">
                        <a href="<?= SITE_URL ?>/index.php?portfolio=<?= urlencode($ctrl_data['username']) ?>" target="_blank" class="action-btn-sm view-btn"><i class="fa-solid fa-globe"></i> Lihat Portfolio</a>
                        <button class="action-btn-sm edit-btn" onclick="openEditUserModal(<?= $ctrl_data['id'] ?>,'<?= h($ctrl_data['username']) ?>','<?= h($ctrl_data['nama_lengkap']??'') ?>','<?= h($ctrl_data['role']) ?>')"><i class="fa-solid fa-pen"></i> Edit Profil User</button>
                    </div>
                </div>
            </div>
            
            <h4 style="font-size:.85rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin-bottom:16px;color:#cbd5e1;">File Workspace &mdash; <?= h($ctrl_user) ?></h4>
            <?php
            $stmt = $mysqli->prepare("SELECT f.*, fo.nama_folder FROM files f LEFT JOIN folders fo ON f.folder_id=fo.id WHERE f.owner_username=? AND f.is_deleted=0 ORDER BY f.tanggal_upload DESC LIMIT 50");
            $stmt->bind_param('s', $ctrl_user); $stmt->execute(); $ctrl_files = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();
            if (empty($ctrl_files)) { echo "<div class='empty-state' style='padding:24px;background:rgba(255,255,255,0.02);border-radius:12px;'><p style='color:#94a3b8;margin:0;'><i class='fa-solid fa-folder-open'></i> User ini belum memiliki file apapun.</p></div>"; }
            else {
                echo "<div style='display:flex;flex-direction:column;gap:8px;max-height:360px;overflow-y:auto;padding-right:8px;'>";
                foreach ($ctrl_files as $cf) {
                    $ic2 = ($cf['jenis'] === 'link') ? ['fa-link','#94a3b8'] : getFileIcon($cf['nama_file']);
                    $fp2 = UPLOAD_DIR . $cf['file_path'];
                    $sz2 = ($cf['jenis'] === 'file' && file_exists($fp2)) ? formatBytes(filesize($fp2)) : 'Tautan';
                    echo "<div style='display:flex;align-items:center;gap:16px;padding:12px 16px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.05);border-radius:10px;'>";
                    echo "<i class='fa-solid {$ic2[0]}' style='color:{$ic2[1]};width:24px;font-size:1.1rem;text-align:center;'></i>";
                    echo "<div style='flex:1;min-width:0;'><div style='font-size:.9rem;font-weight:600;color:#e2e8f0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".h($cf['nama_file'])."</div><div style='font-size:.75rem;color:#64748b;'>Folder: ".h($cf['nama_folder']??'Root')."</div></div>";
                    echo "<span style='font-size:.8rem;color:#94a3b8;min-width:70px;text-align:right;'>$sz2</span>";
                    if ($cf['jenis'] === 'file') echo "<a href='?action=download_file&file_id={$cf['id']}' class='action-btn-sm' title='Download'><i class='fa-solid fa-download'></i></a>";
                    echo "</div>";
                }
                echo "</div>";
                $stmt=$mysqli->prepare("SELECT COUNT(*) FROM folders WHERE owner_username=? AND is_deleted=0"); $stmt->bind_param('s',$ctrl_user); $stmt->execute(); $fc=$stmt->get_result()->fetch_row()[0]; $stmt->close();
                echo "<p style='margin-top:16px;font-size:.85rem;color:#64748b;'><i class='fa-solid fa-chart-pie'></i> Total: ".count($ctrl_files)." file dalam $fc folder.</p>";
            }
            ?>
        </div>
    </div>
<?php } ?>

    <div class="um-table-header">
        <h3><i class="fa-solid fa-users" style="color:#8b5cf6;"></i> Semua Pengguna <span style="background:rgba(255,255,255,0.1);padding:2px 8px;border-radius:12px;font-size:0.8rem;margin-left:8px;"><?= count($all_users) ?></span></h3>
    </div>
    
    <div class="user-list-container">
        <?php foreach($all_users as $u){
            $u_foto = $u['foto_profil'] ?? '';
            $u_path = ($u_foto && $u_foto !== 'default.png' && file_exists(PROFILE_IMG_DIR . $u_foto)) ? PROFILE_IMG_DIR . $u_foto : 'https://ui-avatars.com/api/?name='.urlencode($u['nama_lengkap']??$u['username']).'&background=1a1a1a&color=ffffff&bold=true';
            $u_ll = isset($u['last_login']) ? date('d M Y H:i', strtotime($u['last_login'])) : 'Belum pernah';
        ?>
        <div class="user-list-item">
            <div class="uli-profile">
                <img src="<?= h($u_path) ?>" style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,0.1);" alt="">
                <div>
                    <div style="font-weight:700;color:#fff;font-size:1.05rem;margin-bottom:2px;"><?= h($u['nama_lengkap']??$u['username']) ?></div>
                    <div style="font-size:0.8rem;color:#64748b;"><?= h($u['email']??'-') ?></div>
                </div>
            </div>
            <div class="uli-meta">
                <div class="uli-username">@<?= h($u['username']) ?></div>
                <div class="uli-role"><span class="role-badge <?= h($u['role']) ?>" style="background:rgba(255,255,255,0.05);padding:6px 12px;border-radius:8px;border:1px solid rgba(255,255,255,0.1);"><?= h($u['role']) ?></span></div>
                <div class="uli-last-login"><i class="fa-regular fa-clock" style="color:#64748b;"></i> <?= $u_ll ?></div>
            </div>
            <div class="uli-actions">
                <button class="action-btn-sm view-btn" onclick="window.location='?page=manajemen-pengguna&ctrl_user=<?= h($u['username']) ?>'"><i class="fa-solid fa-folder-open"></i> File</button>
                <?php if($u['username'] !== $username){?>
                <button class="action-btn-sm edit-btn" onclick="openEditUserModal(<?= $u['id'] ?>,'<?= h($u['username']) ?>','<?= h($u['nama_lengkap']??'') ?>','<?= h($u['role']) ?>')"><i class="fa-solid fa-pen"></i></button>
                <button class="action-btn-sm del-btn" onclick="confirmDeleteUser(<?= $u['id'] ?>,'<?= h($u['username']) ?>')"><i class="fa-solid fa-trash"></i></button>
                <?php } else { ?><span style="font-size:.75rem;color:#10b981;padding:6px 10px;background:rgba(16,185,129,0.1);border-radius:6px;font-weight:600;"><i class="fa-solid fa-check"></i> Akun Anda</span><?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>