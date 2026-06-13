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
<div class="page-header">
    <div class="page-header-left">
        <div class="page-eyebrow">God Mode</div>
        <h1 class="page-title">Manajemen Pengguna</h1>
        <p class="page-sub">Kontrol penuh atas semua akun dalam sistem.</p>
    </div>
    <div class="page-actions"><button class="btn-primary" onclick="openModal('addUserModal')"><i class="fa-solid fa-user-plus"></i> Tambah User</button></div>
</div>

<div style="padding:32px;">
<?php if($ctrl_user && $ctrl_data){ ?>
<div class="section-card" style="margin-bottom:24px;border-left:3px solid var(--superadmin);">
    <div class="section-card-header" style="background:#fff8f0;">
        <h3 style="color:var(--superadmin);"><i class="fa-solid fa-eye"></i> Control Panel: <?= h($ctrl_data['username']) ?></h3>
        <a href="index.php?page=manajemen-pengguna" style="font-size:.75rem;color:var(--text-muted);font-weight:700;text-transform:uppercase;">Tutup &times;</a>
    </div>
    <div class="section-card-body">
        <?php
        $cu_foto = $ctrl_data['foto_profil'] ?? '';
        $cu_path = ($cu_foto && $cu_foto !== 'default.png' && file_exists(PROFILE_IMG_DIR . $cu_foto)) ? PROFILE_IMG_DIR . $cu_foto : 'https://ui-avatars.com/api/?name='.urlencode($ctrl_data['nama_lengkap']??$ctrl_data['username']).'&background=1a1a1a&color=ffffff&bold=true';
        ?>
        <div style="display:flex;align-items:center;gap:20px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border);">
            <img src="<?= h($cu_path) ?>" style="width:72px;height:72px;object-fit:cover;filter:grayscale(100%);border:1px solid var(--border-dark);" alt="">
            <div>
                <div style="font-family:'Playfair Display',serif;font-size:1.3rem;font-weight:700;margin-bottom:4px;"><?= h($ctrl_data['nama_lengkap']??$ctrl_data['username']) ?></div>
                <div style="font-size:.8rem;color:var(--text-muted);">@<?= h($ctrl_data['username']) ?> &middot; <span class="role-badge <?= h($ctrl_data['role']) ?>"><?= h($ctrl_data['role']) ?></span></div>
                <div style="margin-top:10px;display:flex;gap:0;">
                    <a href="<?= SITE_URL ?>/index.php?portfolio=<?= urlencode($ctrl_data['username']) ?>" target="_blank" class="action-btn-sm view-workspace-btn" style="margin-right:8px;"><i class="fa-solid fa-globe"></i> Portfolio</a>
                    <button class="action-btn-sm edit-btn" onclick="openEditUserModal(<?= $ctrl_data['id'] ?>,'<?= h($ctrl_data['username']) ?>','<?= h($ctrl_data['nama_lengkap']??'') ?>','<?= h($ctrl_data['role']) ?>')"><i class="fa-solid fa-pen"></i> Edit User</button>
                </div>
            </div>
        </div>
        
        <h4 style="font-size:.82rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);">Workspace Files &mdash; <?= h($ctrl_user) ?></h4>
        <?php
        $stmt = $mysqli->prepare("SELECT f.*, fo.nama_folder FROM files f LEFT JOIN folders fo ON f.folder_id=fo.id WHERE f.owner_username=? AND f.is_deleted=0 ORDER BY f.tanggal_upload DESC LIMIT 50");
        $stmt->bind_param('s', $ctrl_user); $stmt->execute(); $ctrl_files = $stmt->get_result()->fetch_all(MYSQLI_ASSOC); $stmt->close();
        if (empty($ctrl_files)) { echo "<p style='color:var(--text-muted);font-size:.9rem;'>User ini belum memiliki file apapun.</p>"; }
        else {
            echo "<div style='display:flex;flex-direction:column;gap:6px;max-height:360px;overflow-y:auto;'>";
            foreach ($ctrl_files as $cf) {
                $ic2 = ($cf['jenis'] === 'link') ? ['fa-link','#555'] : getFileIcon($cf['nama_file']);
                $fp2 = UPLOAD_DIR . $cf['file_path'];
                $sz2 = ($cf['jenis'] === 'file' && file_exists($fp2)) ? formatBytes(filesize($fp2)) : 'Tautan';
                echo "<div style='display:flex;align-items:center;gap:12px;padding:10px 14px;background:#f9f9f9;border:1px solid var(--border);'><i class='fa-solid {$ic2[0]}' style='color:{$ic2[1]};width:20px;text-align:center;'></i><span style='flex:1;font-size:.88rem;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;'>".h($cf['nama_file'])."</span><span style='font-size:.75rem;color:var(--text-muted);'>".h($cf['nama_folder']??'Root')."</span><span style='font-size:.75rem;color:var(--text-muted);min-width:60px;text-align:right;'>$sz2</span>";
                if ($cf['jenis'] === 'file') echo "<a href='?action=download_file&file_id={$cf['id']}' style='color:var(--text-main);font-size:.8rem;' title='Download'><i class='fa-solid fa-download'></i></a>";
                echo "</div>";
            }
            echo "</div>";
            $stmt=$mysqli->prepare("SELECT COUNT(*) FROM folders WHERE owner_username=? AND is_deleted=0"); $stmt->bind_param('s',$ctrl_user); $stmt->execute(); $fc=$stmt->get_result()->fetch_row()[0]; $stmt->close();
            echo "<p style='margin-top:12px;font-size:.82rem;color:var(--text-muted);'>".count($ctrl_files)." file(s) dalam $fc folder.</p>";
        }
        ?>
    </div>
</div>
<?php } ?>

<div class="section-card">
    <div class="section-card-header"><h3><i class="fa-solid fa-users"></i> Semua Pengguna (<?= count($all_users) ?>)</h3></div>
    <div class="section-card-body" style="padding:0;">
        <table class="user-table">
            <thead><tr><th>Pengguna</th><th>Username</th><th>Role</th><th>Last Login</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php foreach($all_users as $u){
                $u_foto = $u['foto_profil'] ?? '';
                $u_path = ($u_foto && $u_foto !== 'default.png' && file_exists(PROFILE_IMG_DIR . $u_foto)) ? PROFILE_IMG_DIR . $u_foto : 'https://ui-avatars.com/api/?name='.urlencode($u['nama_lengkap']??$u['username']).'&background=1a1a1a&color=ffffff&bold=true';
                $u_ll = isset($u['last_login']) ? date('d M Y H:i', strtotime($u['last_login'])) : 'Belum pernah';
            ?>
            <tr>
                <td style="display:flex;align-items:center;gap:12px;"><img src="<?= h($u_path) ?>" class="user-avatar-sm" alt=""><span style="font-weight:600;"><?= h($u['nama_lengkap']??$u['username']) ?></span></td>
                <td style="font-family:monospace;color:var(--text-muted);">@<?= h($u['username']) ?></td>
                <td><span class="role-badge <?= h($u['role']) ?>"><?= h($u['role']) ?></span></td>
                <td style="font-size:.8rem;"><?= $u_ll ?></td>
                <td>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                        <button class="action-btn-sm view-workspace-btn" onclick="window.location='?page=manajemen-pengguna&ctrl_user=<?= h($u['username']) ?>'"><i class="fa-solid fa-eye"></i> Data</button>
                        <?php if($u['username'] !== $username){?>
                        <button class="action-btn-sm edit-btn" onclick="openEditUserModal(<?= $u['id'] ?>,'<?= h($u['username']) ?>','<?= h($u['nama_lengkap']??'') ?>','<?= h($u['role']) ?>')"><i class="fa-solid fa-pen"></i> Edit</button>
                        <button class="action-btn-sm del-btn" onclick="confirmDeleteUser(<?= $u['id'] ?>,'<?= h($u['username']) ?>')"><i class="fa-solid fa-trash"></i> Hapus</button>
                        <?php } else { ?><span style="font-size:.75rem;color:var(--text-muted);padding:6px 10px;">(Anda)</span><?php } ?>
                    </div>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
</div>