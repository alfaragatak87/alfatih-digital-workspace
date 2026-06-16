<?php if (!defined('SITE_URL')) exit; 
$ws_view = $_GET['view'] ?? 'home';
$base_url = "?page=workspace&";
if ($active_folder) $base_url .= "folder_id={$active_folder}&";
if (isset($_GET['filter'])) $base_url .= "filter=" . h($_GET['filter']) . "&";
if ($ws_view === 'trash') $base_url .= "view=trash&";
?>
<div class="toolbar-main">
    <div class="toolbar-left">
        <div class="dropdown">
            <button class="btn-new"><i class="fa-solid fa-plus"></i> Buat Baru</button>
            <div class="dropdown-content">
                <button onclick="openModal('addFolderModal')"><i class="fa-solid fa-folder-plus"></i><div><strong>Folder Baru</strong><div class="dd-desc">Buat ruang penyimpanan baru</div></div></button>
                <hr class="menu-divider">
                <?php if($active_folder){?>
                <button onclick="openModal('addItemModal');switchType('file');"><i class="fa-solid fa-file-arrow-up"></i><div><strong>Upload File</strong><div class="dd-desc">Pilih dari komputer Anda</div></div></button>
                <button onclick="openModal('addItemModal');switchType('link');"><i class="fa-solid fa-link"></i><div><strong>Simpan Tautan</strong><div class="dd-desc">Simpan URL website</div></div></button>
                <?php }else{?><button disabled style="opacity:.4;cursor:not-allowed;"><i class="fa-solid fa-file-arrow-up"></i><div>Upload File<div class="dd-desc">Masuk folder dulu</div></div></button><?php }?>
            </div>
        </div>
    </div>
    <div class="toolbar-right">
        <button class="btn-icon" onclick="toggleRightSidebar()" data-tooltip="Detail"><i class="fa-solid fa-circle-info"></i></button>
        <div class="view-toggle">
            <button id="btnList" onclick="setViewMode('list')" data-tooltip="List"><i class="fa-solid fa-list-ul"></i></button>
            <button id="btnGrid" onclick="setViewMode('grid')" class="active" data-tooltip="Grid"><i class="fa-solid fa-border-all"></i></button>
        </div>
    </div>
</div>
<div class="bulk-toolbar" id="bulkToolbar">
    <input type="checkbox" class="item-checkbox" id="selectAllMain" onclick="toggleSelectAll(this)" style="opacity:1;">
    <span class="bulk-count" id="bulkCount">0 dipilih</span>
    <div class="bulk-actions">
        <button class="bulk-btn" onclick="bulkMove()"><i class="fa-solid fa-folder-tree"></i> Pindah</button>
        <button class="bulk-btn danger" onclick="bulkDelete()"><i class="fa-solid fa-trash"></i> Hapus</button>
        <button class="bulk-btn" onclick="deselectAll()"><i class="fa-solid fa-xmark"></i> Batal</button>
    </div>
</div>
<div class="breadcrumbs">
    <?php
    if ($ws_view === 'trash') { echo "<i class='fa-solid fa-trash-can' style='color:var(--danger);margin-right:4px;'></i> Tong Sampah"; }
    elseif ($ws_view === 'recent') { echo "<a href='index.php?page=workspace' class='modern-context-item'>Beranda</a> &rsaquo; Akses Terbaru"; }
    elseif ($ws_view === 'assets') { echo "<a href='index.php?page=workspace' class='modern-context-item'>Beranda</a> &rsaquo; Aset Visual"; }
    elseif ($ws_view === 'stats') { echo "<a href='index.php?page=workspace' class='modern-context-item'>Beranda</a> &rsaquo; Statistik"; }
    else {
        echo "<a href='index.php?page=workspace' class='modern-context-item'>Beranda</a>";
        foreach ($breadcrumbs as $bc) { echo " &rsaquo; <a href='?page=workspace&folder_id={$bc['id']}' class='modern-context-item'>" . h($bc['nama_folder']) . "</a>"; }
    }
    ?>
</div>
<?php if ($ws_view === 'home' && isAdmin() && !$active_folder) {
    echo "<div class='filter-chips'><a href='?page=workspace&filter={$username}' class='chip ".(($admin_filter===$username)?'active':'')."' class='modern-context-item'><i class='fa-regular fa-user'></i> Milikku</a>";
    foreach ($all_users as $u) {
        if ($u['username'] !== $username) {
            $lbl = !empty($u['nama_lengkap']) ? $u['nama_lengkap'] : $u['username'];
            echo "<a href='?page=workspace&filter=".h($u['username'])."' class='chip ".(($admin_filter===$u['username'])?'active':'')."' class='modern-context-item'><i class='fa-solid fa-user'></i> ".h($lbl)."</a>";
        }
    }
    echo "<a href='?page=workspace&filter=semua' class='chip ".(($admin_filter==='semua')?'active':'')."' class='modern-context-item'><i class='fa-solid fa-users'></i> Semua</a></div>";
} ?>

<div id="workspaceContainer" class="view-grid modern-workspace">
<div class="list-header" id="driveListHeader" style="display:none; padding: 0 16px; border-bottom: 1px solid #e0e0e0; font-size: 0.875rem; font-weight: 500; color: #444746; grid-template-columns: 36px 1fr 160px 140px 90px 40px; align-items: center; height: 48px;">
    <div class="select-all-wrap"><input type="checkbox" class="item-checkbox" id="selectAllHeader" onclick="toggleSelectAll(this)" style="opacity:1;"></div>
    <div><a href="<?= $base_url ?>sort=<?= ($sort==='nama_asc')?'nama_desc':'nama_asc' ?>" style="color:#444746; text-decoration:none;">Nama <?php if($sort==='nama_asc')echo '&darr;';elseif($sort==='nama_desc')echo '&uarr;';?></a></div>
    <div class="col-owner">Pemilik</div>
    <div class="col-date"><a href="<?= $base_url ?>sort=<?= ($sort==='date_desc')?'date_asc':'date_desc' ?>" style="color:#444746; text-decoration:none;">Terakhir diubah <?php if($sort==='date_asc')echo '&darr;';elseif($sort==='date_desc')echo '&uarr;';?></a></div>
    <div class="col-size" style="text-align:left;">Ukuran file</div>
    <div></div>
</div>
<?php
// QUERY DATA SESUAI VIEW
if ($ws_view === 'stats') {
    // --- MODE STATISTIK ---
    $tc = [];
    $stmt = $mysqli->prepare("SELECT nama_file FROM files WHERE owner_username=? AND is_deleted=0 AND jenis='file'");
    $stmt->bind_param('s', $username); $stmt->execute(); $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) { $ext = strtolower(pathinfo($r['nama_file'], PATHINFO_EXTENSION)); $tc[$ext] = ($tc[$ext]??0)+1; }
    $stmt->close(); arsort($tc);
    echo "<div style='display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:0;border:1px solid var(--border-dark);margin:24px 32px;'>";
    foreach ([['fa-folder','Folder',$stat_folders],['fa-file','File',$stat_files],['fa-link','Tautan',$stat_links],['fa-hard-drive','Penyimpanan',$size_used]] as [$ic,$lbl,$val]) {
        echo "<div style='padding:24px;border-right:1px solid var(--border-dark);'><div style='font-size:.65rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--text-muted);margin-bottom:10px;'><i class='fa-solid $ic' style='margin-right:6px;'></i>$lbl</div><div style='font-family:\"Playfair Display\",serif;font-size:2rem;font-weight:900;'>$val</div></div>";
    }
    echo "</div>";
    if (!empty($tc)) {
        echo "<div style='margin:0 32px 32px;border:1px solid var(--border-dark);'><div style='padding:16px 20px;border-bottom:1px solid var(--border);font-size:.75rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase;'>Jenis File</div><div style='padding:20px;'>";
        $max = max($tc);
        foreach (array_slice($tc,0,12) as $ext => $cnt) {
            $id2 = getFileIcon("f.$ext"); $p2 = round(($cnt/$max)*100);
            echo "<div style='display:flex;align-items:center;gap:12px;margin-bottom:10px;'><span style='width:36px;font-size:.72rem;font-weight:700;text-transform:uppercase;'>".h($ext)."</span><div style='flex:1;height:3px;background:var(--border);'><div style='height:100%;width:{$p2}%;background:var(--text-main);'></div></div><span style='font-size:.78rem;font-weight:700;width:30px;text-align:right;'>$cnt</span></div>";
        }
        echo "</div></div>";
    }
} elseif ($ws_view === 'recent') {
    // --- MODE RECENT ---
    $stmt = $mysqli->prepare("SELECT f.*, fo.nama_folder as folder_name FROM files f LEFT JOIN folders fo ON f.folder_id=fo.id WHERE f.is_deleted=0" . (isAdmin() ? "" : " AND f.owner_username=?") . " ORDER BY f.tanggal_upload DESC LIMIT 30");
    if (isAdmin()) $stmt->execute(); else { $stmt->bind_param('s', $username); $stmt->execute(); }
    $res = $stmt->get_result(); $has = false;
    while ($item = $res->fetch_assoc()) {
        $has = true; $is_lnk = ($item['jenis']==='link'); $ds = date('d M Y H:i', strtotime($item['tanggal_upload']));
        $sn = h($item['nama_file']); $st = h($item['tags'] ?? '');
        $av = 'https://ui-avatars.com/api/?name='.urlencode($item['owner_username']).'&background=1a1a1a&color=ffffff&size=32';
        if ($is_lnk) { $id2=['fa-link','#555']; $sz='Tautan'; } else { $id2=getFileIcon($item['nama_file']); $fp2=UPLOAD_DIR.$item['file_path']; $sz=file_exists($fp2)?formatBytes(filesize($fp2)):'-'; }
        $fl = !empty($item['folder_name']) ? h($item['folder_name']) : 'Root'; $pt = $is_lnk?'none':getPreviewType($item['nama_file']);
        $ah = $is_lnk ? "<a href='".h($item['link_url'])."' target='_blank' class='btn-rs-action btn-rs-primary' class='modern-context-item'><i class='fa-solid fa-arrow-up-right-from-square'></i> Kunjungi</a>" : "<a href='?action=download_file&file_id={$item['id']}' class='btn-rs-action btn-rs-secondary' class='modern-context-item'><i class='fa-solid fa-download'></i> Download</a>";
        echo "<div class='item-card' onclick='handleItemClick(event,this)' data-id='{$item['id']}' data-item-type='{$item['jenis']}' data-type='{$item['jenis']}' data-name='$sn' data-icon='fa-solid {$id2[0]}' data-color='{$id2[1]}' data-owner='".h($item['owner_username'])."' data-date='$ds' data-size='$sz' data-desc='Folder: $fl' data-url='' data-tags='$st' data-share='' data-preview='$pt'><input type='checkbox' class='item-checkbox' onclick='handleCheckbox(event,this)'><div class='hidden-action-html' style='display:none;'>$ah</div><div class='item-info-wrap'><div class='item-icon-lg' style='color:{$id2[1]};'><i class='fa-solid {$id2[0]}'></i></div><div class='item-details'><div class='item-name'>$sn</div><span style='font-size:.72rem;color:var(--text-muted);'>$fl</span></div></div><div class='col-owner'><img src='$av' alt=''> ".h($item['owner_username'])."</div><div class='col-date'>$ds</div><div class='col-size'>$sz</div><div class='action-wrapper'></div></div>";
    }
    $stmt->close();
    if (!$has) echo "<div class='empty-state' style='cursor:default;'><i class='fa-solid fa-clock-rotate-left'></i><h3>Belum Ada Aktivitas Terbaru</h3></div>";
} elseif ($ws_view === 'assets') {
    // --- MODE ASSETS GAMBAR ---
    $stmt = isAdmin() ? $mysqli->prepare("SELECT * FROM files WHERE is_deleted=0 AND jenis='file' AND (nama_file LIKE '%.jpg' OR nama_file LIKE '%.jpeg' OR nama_file LIKE '%.png' OR nama_file LIKE '%.gif' OR nama_file LIKE '%.webp') ORDER BY tanggal_upload DESC") : $mysqli->prepare("SELECT * FROM files WHERE owner_username=? AND is_deleted=0 AND jenis='file' AND (nama_file LIKE '%.jpg' OR nama_file LIKE '%.jpeg' OR nama_file LIKE '%.png' OR nama_file LIKE '%.gif' OR nama_file LIKE '%.webp') ORDER BY tanggal_upload DESC");
    if (!isAdmin()) { $stmt->bind_param('s', $username); } $stmt->execute(); $res = $stmt->get_result(); $has = false;
    if ($res->num_rows > 0) {
        $has = true;
        echo "<div style='display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:0;border:1px solid var(--border-dark);margin:0;'>";
        while ($img = $res->fetch_assoc()) {
            $tu = UPLOAD_DIR.$img['file_path']; $sn = h($img['nama_file']);
            echo "<div style='border-right:1px solid var(--border);border-bottom:1px solid var(--border);cursor:pointer;' onmouseover='this.style.opacity=\".8\"' onmouseout='this.style.opacity=\"1\"' onclick=\"openPreview('{$sn}','$tu','image',{$img['id']})\"><div style='width:100%;height:120px;overflow:hidden;background:#f5f5f5;'><img src='$tu' alt='$sn' style='width:100%;height:100%;object-fit:cover;filter:grayscale(100%);'></div><div style='padding:10px 12px;'><div style='font-size:.78rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;'>$sn</div><div style='font-size:.7rem;color:var(--text-muted);margin-top:2px;'>".date('d M Y',strtotime($img['tanggal_upload']))."</div></div></div>";
        }
        echo "</div>";
    }
    $stmt->close();
    if (!$has) echo "<div class='empty-state' style='cursor:default;'><i class='fa-solid fa-images'></i><h3>Belum Ada Aset Visual</h3></div>";
} elseif ($ws_view === 'trash') {
    // --- MODE TONG SAMPAH ---
    $tc2 = 0;
    if (isAdmin()) { $stmt=$mysqli->prepare("SELECT * FROM folders WHERE is_deleted=1 ORDER BY $order_f"); $stmt->execute(); }
    else { $stmt=$mysqli->prepare("SELECT * FROM folders WHERE owner_username=? AND is_deleted=1 ORDER BY $order_f"); $stmt->bind_param('s', $username); $stmt->execute(); }
    $res=$stmt->get_result(); $stmt->close();
    while ($f=$res->fetch_assoc()) {
        $tc2++; $sn=h($f['nama_folder']);
        echo "<div class='item-card' style='opacity:.6;' onclick='handleItemClick(event,this)' data-id='{$f['id']}' data-item-type='folder' data-type='folder' data-name='$sn' data-icon='fa-solid {$f['icon']}' data-color='{$f['warna']}' data-owner='".h($f['owner_username'])."' data-date='-' data-size='-' data-desc='".h($f['deskripsi'])."' data-url='' data-tags='' data-share='' data-preview='none'><input type='checkbox' class='item-checkbox' onclick='handleCheckbox(event,this)'><div class='hidden-action-html' style='display:none;'><a href='?page=workspace&action=restore&type=folder&id={$f['id']}' class='btn-rs-action btn-rs-primary' class='modern-context-item'><i class='fa-solid fa-clock-rotate-left'></i> Pulihkan</a><a href='?page=workspace&action=hard_delete&type=folder&id={$f['id']}' onclick=\"return confirm('Hapus Permanen?');\" class='btn-rs-action btn-rs-danger' class='modern-context-item'><i class='fa-solid fa-fire'></i> Hapus Permanen</a></div><div class='item-info-wrap'><div class='item-icon-lg' style='color:#555;'><i class='fa-solid {$f['icon']}'></i></div><div class='item-name' style='text-decoration:line-through;'>$sn</div></div><div class='col-owner'>".h($f['owner_username'])."</div><div class='col-date'>-</div><div class='col-size'>-</div><div class='action-wrapper'><button class='btn-dots' onclick='toggleActionMenu(event,\"tmf_{$f['id']}\")'><i class='fa-solid fa-ellipsis-vertical'></i></button><div id='tmf_{$f['id']}' class='modern-context-menu'><a href='?page=workspace&action=restore&type=folder&id={$f['id']}' class='modern-context-item'><i class='fa-solid fa-clock-rotate-left'></i> Pulihkan</a><a href='?page=workspace&action=hard_delete&type=folder&id={$f['id']}' onclick=\"return confirm('Hapus Permanen?');\" style='color:var(--danger);' class='modern-context-item'><i class='fa-solid fa-fire'></i> Hapus Selamanya</a></div></div></div>";
    }
    if (isAdmin()) { $stmt=$mysqli->prepare("SELECT * FROM files WHERE is_deleted=1 ORDER BY $order_i"); $stmt->execute(); }
    else { $stmt=$mysqli->prepare("SELECT * FROM files WHERE owner_username=? AND is_deleted=1 ORDER BY $order_i"); $stmt->bind_param('s', $username); $stmt->execute(); }
    $res=$stmt->get_result(); $stmt->close();
    while ($f=$res->fetch_assoc()) {
        $tc2++; $sn=h($f['nama_file']); $is_lnk=($f['jenis']==='link'); $ic=$is_lnk?'fa-link':getFileIcon($f['nama_file'])[0]; $ds=date('d M Y',strtotime($f['tanggal_upload']));
        echo "<div class='item-card' style='opacity:.6;' onclick='handleItemClick(event,this)' data-id='{$f['id']}' data-item-type='{$f['jenis']}' data-type='{$f['jenis']}' data-name='$sn' data-icon='fa-solid $ic' data-color='#555' data-owner='".h($f['owner_username'])."' data-date='$ds' data-size='-' data-desc='Dihapus' data-url='' data-tags='' data-share='' data-preview='none'><input type='checkbox' class='item-checkbox' onclick='handleCheckbox(event,this)'><div class='hidden-action-html' style='display:none;'><a href='?page=workspace&action=restore&type=file&id={$f['id']}' class='btn-rs-action btn-rs-primary' class='modern-context-item'><i class='fa-solid fa-clock-rotate-left'></i> Pulihkan</a><a href='?page=workspace&action=hard_delete&type=file&id={$f['id']}' onclick=\"return confirm('Hapus Permanen?');\" class='btn-rs-action btn-rs-danger' class='modern-context-item'><i class='fa-solid fa-fire'></i> Hapus Permanen</a></div><div class='item-info-wrap'><div class='item-icon-lg' style='color:#555;'><i class='fa-solid $ic'></i></div><div class='item-name' style='text-decoration:line-through;'>$sn</div></div><div class='col-owner'>".h($f['owner_username'])."</div><div class='col-date'>$ds</div><div class='col-size'>-</div><div class='action-wrapper'><button class='btn-dots' onclick='toggleActionMenu(event,\"tmi_{$f['id']}\")'><i class='fa-solid fa-ellipsis-vertical'></i></button><div id='tmi_{$f['id']}' class='modern-context-menu'><a href='?page=workspace&action=restore&type=file&id={$f['id']}' class='modern-context-item'><i class='fa-solid fa-clock-rotate-left'></i> Pulihkan</a><a href='?page=workspace&action=hard_delete&type=file&id={$f['id']}' onclick=\"return confirm('Hapus Permanen?');\" style='color:var(--danger);' class='modern-context-item'><i class='fa-solid fa-fire'></i> Hapus Selamanya</a></div></div></div>";
    }
    if ($tc2 === 0) echo "<div class='empty-state' style='cursor:default;'><i class='fa-solid fa-recycle'></i><h3>Tong Sampah Bersih</h3></div>";
} else {
    // --- MODE BERANDA WORKSPACE (FOLDER & FILE NORMAL) ---
    $f_sql = "SELECT * FROM folders WHERE is_deleted=0"; $f_params = []; $f_types = '';
    if ($active_folder) { $f_sql .= " AND parent_id=?"; $f_types .= 'i'; $f_params[] = $active_folder; }
    else { $f_sql .= " AND parent_id IS NULL"; }
    if (isAdmin() && !$active_folder && !$search_query) {
        if ($admin_filter !== 'semua') { $f_sql .= " AND owner_username=?"; $f_types .= 's'; $f_params[] = $admin_filter; }
    } elseif (!isAdmin()) { $f_sql .= " AND owner_username=?"; $f_types .= 's'; $f_params[] = $username; }
    if ($search_query) { $f_sql .= " AND nama_folder LIKE ?"; $f_types .= 's'; $f_params[] = '%' . $search_query . '%'; }
    $f_sql .= " ORDER BY $order_f";
    
    $stmt = $mysqli->prepare($f_sql);
    if ($f_types) { $stmt->bind_param($f_types, ...$f_params); }
    $stmt->execute(); $res = $stmt->get_result(); $has_folders = false; $has_files = false;
    
    // RENDER FOLDERS
    $folder_html = "";
    while ($f = $res->fetch_assoc()) {
        $has_folders = true; $sn = h($f['nama_folder']); $sd = h($f['deskripsi']??'');
        $av = 'https://ui-avatars.com/api/?name='.urlencode($f['owner_username']).'&background=1a1a1a&color=ffffff&size=32';
        $ah = "<a href='?page=workspace&folder_id={$f['id']}' class='btn-rs-action btn-rs-primary' class='modern-context-item'><i class='fa-solid fa-folder-open'></i> Buka</a><a href='?action=download_zip&folder_id={$f['id']}' class='btn-rs-action btn-rs-secondary' class='modern-context-item'><i class='fa-solid fa-file-zipper'></i> ZIP</a><button onclick=\"openMoveModal('folder',{$f['id']},'$sn')\" class='btn-rs-action btn-rs-secondary' class='modern-context-item'><i class='fa-solid fa-folder-tree'></i> Pindah</button><a href='?page=workspace&action=soft_delete_folder&id={$f['id']}' class='btn-rs-action btn-rs-danger' class='modern-context-item'><i class='fa-solid fa-trash-can'></i> Hapus</a>";
        $folder_html .= "<div class='item-card modern-folder-card' draggable='true' ondblclick=\"window.location='?page=workspace&folder_id={$f['id']}'\" onclick='handleItemClick(event,this)' data-id='{$f['id']}' data-item-type='folder' data-type='folder' data-name='$sn' data-icon='fa-solid {$f['icon']}' data-color='{$f['warna']}' data-owner='".h($f['owner_username'])."' data-date='-' data-size='-' data-desc='$sd' data-url='' data-tags='' data-share='' data-preview='none'><input type='checkbox' class='item-checkbox' onclick='handleCheckbox(event,this)'><div class='hidden-action-html' style='display:none;'>$ah</div><div class='icon-wrap' style='color:var(--text-main);'><i class='fa-solid fa-folder'></i></div><div class='info-wrap'><div class='item-name'>$sn</div><div class='item-meta'>Folder</div></div><div class='col-owner'><img src='$av' alt=''> ".h($f['owner_username'])."</div><div class='col-date'>-</div><div class='col-size'>-</div><div class='action-wrapper'><button class='btn-dots' onclick='toggleActionMenu(event,\"mf_{$f['id']}\")'><i class='fa-solid fa-ellipsis-vertical'></i></button><div id='mf_{$f['id']}' class='modern-context-menu'><a href='?page=workspace&folder_id={$f['id']}' class='modern-context-item'><i class='fa-solid fa-folder-open'></i> Buka folder</a><a href='?action=download_zip&folder_id={$f['id']}' class='modern-context-item'><i class='fa-solid fa-file-zipper'></i> Download ZIP</a><button onclick=\"openMoveModal('folder',{$f['id']},'$sn');closeAllMenus();\" class='modern-context-item'><i class='fa-solid fa-folder-tree'></i> Pindahkan</button><button onclick=\"openEditModal({$f['id']},'$sn','$sd','{$f['icon']}','{$f['warna']}');closeAllMenus();\" class='modern-context-item'><i class='fa-solid fa-pen'></i> Edit</button><button onclick=\"startInlineRename(this.closest('.item-card'));closeAllMenus();\" class='modern-context-item'><i class='fa-solid fa-i-cursor'></i> Ganti nama</button><hr class='menu-divider'><a href='?page=workspace&action=soft_delete_folder&id={$f['id']}' style='color:var(--danger);' class='modern-context-item'><i class='fa-solid fa-trash'></i> Hapus</a></div></div></div>";
    }
    $stmt->close();
    
    if($has_folders){
        echo "<div class='section-title'>Folder</div>";
        echo "<div class='grid-folders'>$folder_html</div>";
    }

    $file_html = "";
    if ($active_folder) {
        $i_sql = "SELECT * FROM files WHERE folder_id=? AND is_deleted=0"; $i_types = 'i'; $i_params = [$active_folder];
        if ($search_query) { $i_sql .= " AND (nama_file LIKE ? OR tags LIKE ?)"; $i_types .= 'ss'; $i_params[] = '%' . $search_query . '%'; $i_params[] = '%' . $search_query . '%'; }
        $i_sql .= " ORDER BY $order_i";
        $stmt = $mysqli->prepare($i_sql); $stmt->bind_param($i_types, ...$i_params); $stmt->execute(); $res = $stmt->get_result(); $stmt->close();
        while ($item = $res->fetch_assoc()) {
            $has_files = true; $is_lnk = ($item['jenis']==='link'); $ds = date('M d, Y', strtotime($item['tanggal_upload']));
            $av = 'https://ui-avatars.com/api/?name='.urlencode($item['owner_username']).'&background=1a1a1a&color=ffffff&size=32';
            $sn = h($item['nama_file']); $st = h($item['tags']??''); $pt = $is_lnk?'none':getPreviewType($item['nama_file']);
            if ($is_lnk) {
                $sz="Link"; $js_icon="fa-solid fa-link"; $ic_col="var(--text-main)"; $file_url='';
                $ah="<a href='".h($item['link_url'])."' target='_blank' class='btn-rs-action btn-rs-primary' class='modern-context-item'><i class='fa-solid fa-arrow-up-right-from-square'></i> Kunjungi</a><button onclick=\"copyLink('".h($item['link_url'])."')\" class='btn-rs-action btn-rs-secondary' class='modern-context-item'><i class='fa-solid fa-copy'></i> Salin URL</button><a href='?page=workspace&action=soft_delete_item&item_id={$item['id']}' class='btn-rs-action btn-rs-danger' class='modern-context-item'><i class='fa-solid fa-trash-can'></i> Hapus</a>";
                $dot_actions="<a href='".h($item['link_url'])."' target='_blank' class='modern-context-item'><i class='fa-solid fa-arrow-up-right-from-square'></i> Buka</a>";
            } else {
                $id_data=getFileIcon($item['nama_file']); $js_icon="fa-solid ".$id_data[0]; $ic_col=$id_data[1];
                $fp2=UPLOAD_DIR.$item['file_path']; $file_url=$fp2; $sz=file_exists($fp2)?formatBytes(filesize($fp2)):'Invalid';
                $tok=$item['share_token']??''; $share_full=SITE_URL.'/index.php?share='.$tok;
                $wa_txt=urlencode("Halo, berikut file:\n*{$item['nama_file']}*\nLink: {$share_full}"); $wa_link="https://api.whatsapp.com/send?text=".$wa_txt;
                $ah="<button onclick=\"openPreview('".addslashes($sn)."','$fp2','$pt',{$item['id']})\" class='btn-rs-action btn-rs-primary' class='modern-context-item'><i class='fa-regular fa-eye'></i> Pratinjau</button><a href='?action=download_file&file_id={$item['id']}' class='btn-rs-action btn-rs-secondary' class='modern-context-item'><i class='fa-solid fa-download'></i> Download</a>";
                if ($tok) $ah.="<button onclick=\"copyLink('".h($share_full)."')\" class='btn-rs-action btn-rs-secondary' class='modern-context-item'><i class='fa-solid fa-copy'></i> Salin Link</button><a href='$wa_link' target='_blank' class='btn-rs-action btn-rs-whatsapp' class='modern-context-item'><i class='fa-brands fa-whatsapp'></i> WA</a>";
                else $ah.="<a href='?action=create_share&file_id={$item['id']}' class='btn-rs-action btn-rs-secondary' class='modern-context-item'><i class='fa-solid fa-earth-asia'></i> Buat Link</a>";
                $ah.="<button onclick=\"openMoveModal('file',{$item['id']},'$sn')\" class='btn-rs-action btn-rs-secondary' class='modern-context-item'><i class='fa-solid fa-folder-tree'></i> Pindah</button><a href='?page=workspace&action=soft_delete_item&item_id={$item['id']}' class='btn-rs-action btn-rs-danger' class='modern-context-item'><i class='fa-solid fa-trash-can'></i> Hapus</a>";
                $dot_actions="<button onclick=\"openPreview('".addslashes($sn)."','$fp2','$pt',{$item['id']});closeAllMenus();\" class='modern-context-item'><i class='fa-regular fa-eye'></i> Pratinjau</button><a href='?action=download_file&file_id={$item['id']}' class='modern-context-item'><i class='fa-solid fa-download'></i> Download</a>";
            }
            // Tampilan File: menggunakan ikon preview (jika gambar) atau ikon biasa.
            $img_preview = ($pt === 'image') ? "<img src='$fp2' class='preview-area-img'>" : "<div class='icon-placeholder'><i class='$js_icon' style='color:$ic_col;'></i></div>";
            
            $file_html .= "<div class='item-card modern-file-card' draggable='true' onclick='handleItemClick(event,this)' ondblclick=\"".($pt==='image'?"openPreview('".addslashes($sn)."','$fp2','$pt',{$item['id']})":"")."\" data-id='{$item['id']}' data-item-type='{$item['jenis']}' data-type='{$item['jenis']}' data-name='$sn' data-icon='$js_icon' data-color='$ic_col' data-owner='".h($item['owner_username'])."' data-date='$ds' data-size='$sz' data-desc='' data-url='$file_url' data-tags='$st' data-share='".h($tok??'')."' data-preview='$pt'><input type='checkbox' class='item-checkbox' onclick='handleCheckbox(event,this)'><div class='hidden-action-html' style='display:none;'>$ah</div><div class='preview-area'>$img_preview</div><div class='info-area'><div class='item-icon'><i class='$js_icon' style='color:$ic_col;'></i></div><div class='item-details'><div class='item-name'>$sn".($st?"<span class='tag-badge'><i class='fa-solid fa-tag'></i> $st</span>":"")."</div><div class='item-meta'>$sz &bull; $ds</div></div></div><div class='col-owner'><img src='$av' alt=''> ".h($item['owner_username'])."</div><div class='col-date'>$ds</div><div class='col-size'>$sz</div><div class='action-wrapper'><button class='btn-dots' onclick='toggleActionMenu(event,\"mi_{$item['id']}\")'><i class='fa-solid fa-ellipsis-vertical'></i></button><div id='mi_{$item['id']}' class='modern-context-menu'>$dot_actions<button onclick=\"startInlineRename(this.closest('.item-card'));closeAllMenus();\" class='modern-context-item'><i class='fa-solid fa-i-cursor'></i> Ganti nama</button><button onclick=\"openMoveModal('file',{$item['id']},'$sn');closeAllMenus();\" class='modern-context-item'><i class='fa-solid fa-folder-tree'></i> Pindahkan</button><hr class='menu-divider'><a href='?page=workspace&action=soft_delete_item&item_id={$item['id']}' style='color:var(--danger);' class='modern-context-item'><i class='fa-solid fa-trash'></i> Hapus</a></div></div></div>";
        }
    }
    
    if($has_files){
        echo "<div class='section-title'>File</div>";
        echo "<div class='grid-files'>$file_html</div>";
    }

    if (!$has_folders && !$has_files) {
        echo "<div class='empty-state' onclick=\"openModal('addFolderModal')\"><i class='fa-brands fa-google-drive' style='font-size:4rem;color:var(--text-muted);opacity:0.3;'></i><h3 style='margin-top:20px;font-size:1.4rem;'>Tempat untuk semua file Anda</h3><p style='font-size:1rem;'>Gunakan tombol 'Baru' untuk mengunggah atau membuat.</p></div>";
    }
}
?>
</div>