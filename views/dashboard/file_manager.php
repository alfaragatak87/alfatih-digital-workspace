<?php
/**
 * views/dashboard/file_manager.php — v2 (Starred + Context Menu)
 * ================================================================
 * Perubahan dari versi sebelumnya:
 *  1. Query SQL sertakan kolom is_starred dari folders & files
 *  2. Setiap item-card mendapat atribut data-starred="0|1"
 *  3. Item yang dibintangi mendapat class .is-starred dan ikon bintang
 *  4. Tombol titik-tiga (btn-dots) diganti fungsi showContextMenu()
 *     untuk trigger context menu pada desktop
 *  5. Long-press (500ms) pada item-card trigger bottom-sheet di mobile
 *  6. Struktur HTML bersih — tidak ada inline action-dropdown lagi
 *     (semua aksi dikelola oleh context_menu.js)
 * ================================================================
 */
if (!defined('SITE_URL')) exit;

// ── Inisialisasi variabel ─────────────────────────────────────
$active_folder = isset($_GET['folder_id']) ? (int)$_GET['folder_id'] : null;
$search_query  = $_GET['q']      ?? '';
$sort          = $_GET['sort']   ?? 'nama_asc';
$admin_filter  = $_GET['filter'] ?? 'semua';

$order_f = 'nama_folder ASC'; $order_i = 'nama_file ASC';
if ($sort === 'nama_desc')  { $order_f = 'nama_folder DESC'; $order_i = 'nama_file DESC'; }
elseif ($sort === 'date_asc')  { $order_f = 'created_at ASC'; $order_i = 'tanggal_upload ASC'; }
elseif ($sort === 'date_desc') { $order_f = 'created_at DESC'; $order_i = 'tanggal_upload DESC'; }

// Breadcrumbs
$breadcrumbs = [];
if ($active_folder) {
    $curr = $active_folder;
    while ($curr) {
        $sbc = $mysqli->prepare('SELECT id, parent_id, nama_folder FROM folders WHERE id=?');
        $sbc->bind_param('i', $curr);
        $sbc->execute();
        $rbc = $sbc->get_result()->fetch_assoc();
        $sbc->close();
        if ($rbc) { array_unshift($breadcrumbs, $rbc); $curr = $rbc['parent_id']; }
        else break;
    }
}

$ws_view  = $_GET['view'] ?? 'home';
$base_url = '?page=workspace&' . ($active_folder ? "folder_id={$active_folder}&" : '') . ($ws_view === 'trash' ? 'view=trash&' : '');

// Helper: build context-menu data attrs dari sebuah item card
function cmAttrs(array $d): string {
    $out = '';
    foreach ($d as $k => $v) {
        $out .= ' data-' . $k . '="' . htmlspecialchars((string)$v, ENT_QUOTES) . '"';
    }
    return $out;
}
?>

<!-- ── TOOLBAR ─────────────────────────────────────────────── -->
<div class="toolbar-main">
    <div class="toolbar-left">
        <div class="dropdown">
            <button class="btn-new"><i class="fa-solid fa-plus"></i> Buat Baru</button>
            <div class="dropdown-content">
                <button onclick="openModal('addFolderModal')">
                    <i class="fa-solid fa-folder-plus"></i>
                    <div><strong>Folder Baru</strong><div class="dd-desc">Buat ruang penyimpanan baru</div></div>
                </button>
                <hr class="menu-divider">
                <?php if ($active_folder) { ?>
                <button onclick="openModal('addItemModal');switchType('file');">
                    <i class="fa-solid fa-file-arrow-up"></i>
                    <div><strong>Upload File</strong><div class="dd-desc">Pilih dari komputer Anda</div></div>
                </button>
                <button onclick="openModal('addItemModal');switchType('link');">
                    <i class="fa-solid fa-link"></i>
                    <div><strong>Simpan Tautan</strong><div class="dd-desc">Simpan URL website</div></div>
                </button>
                <?php } else { ?>
                <button disabled style="opacity:.4;cursor:not-allowed;">
                    <i class="fa-solid fa-file-arrow-up"></i>
                    <div>Upload File<div class="dd-desc">Masuk folder dulu</div></div>
                </button>
                <?php } ?>
            </div>
        </div>

        <!-- Starred filter chip -->
        <a href="?page=workspace&view=starred"
           class="chip <?= ($ws_view === 'starred') ? 'active' : '' ?>"
           style="height:38px;border-radius:var(--radius-sm);">
            <i class="fa-solid fa-star" style="color:#f59e0b;"></i> Berbintang
        </a>
    </div>
    <div class="toolbar-right">
        <button class="btn-icon" onclick="toggleRightSidebar()" data-tooltip="Detail">
            <i class="fa-solid fa-circle-info"></i>
        </button>
        <div class="view-toggle">
            <button id="btnList" onclick="setViewMode('list')" data-tooltip="List">
                <i class="fa-solid fa-list-ul"></i>
            </button>
            <button id="btnGrid" onclick="setViewMode('grid')" data-tooltip="Grid">
                <i class="fa-solid fa-border-all"></i>
            </button>
        </div>
    </div>
</div>

<!-- ── BULK TOOLBAR ───────────────────────────────────────────── -->
<div class="bulk-toolbar" id="bulkToolbar">
    <input type="checkbox" class="item-checkbox" id="selectAllMain" onclick="toggleSelectAll(this)" style="opacity:1;">
    <span class="bulk-count" id="bulkCount">0 dipilih</span>
    <div class="bulk-actions">
        <button class="bulk-btn" onclick="bulkMove()"><i class="fa-solid fa-folder-tree"></i> Pindah</button>
        <button class="bulk-btn danger" onclick="bulkDelete()"><i class="fa-solid fa-trash"></i> Hapus</button>
        <button class="bulk-btn" onclick="deselectAll()"><i class="fa-solid fa-xmark"></i> Batal</button>
    </div>
</div>

<!-- ── BREADCRUMBS ────────────────────────────────────────────── -->
<div class="breadcrumbs">
    <?php
    if ($ws_view === 'trash')   { echo "<i class='fa-solid fa-trash-can' style='color:var(--danger);margin-right:4px;'></i> Tong Sampah"; }
    elseif ($ws_view === 'starred') { echo "<i class='fa-solid fa-star' style='color:#f59e0b;margin-right:4px;'></i> File &amp; Folder Berbintang"; }
    elseif ($ws_view === 'recent'){ echo "<a href='index.php?page=workspace'>Beranda</a> &rsaquo; Akses Terbaru"; }
    elseif ($ws_view === 'assets'){ echo "<a href='index.php?page=workspace'>Beranda</a> &rsaquo; Aset Visual"; }
    elseif ($ws_view === 'stats') { echo "<a href='index.php?page=workspace'>Beranda</a> &rsaquo; Statistik"; }
    else {
        echo "<a href='index.php?page=workspace'>Beranda</a>";
        foreach ($breadcrumbs as $bc) {
            echo " &rsaquo; <a href='?page=workspace&folder_id={$bc['id']}'>" . h($bc['nama_folder']) . "</a>";
        }
    }
    ?>
</div>

<!-- ── Admin Filter Chips ─────────────────────────────────────── -->
<?php if ($ws_view === 'home' && isAdmin() && !$active_folder) {
    echo "<div class='filter-chips'>";
    echo "<a href='?page=workspace&filter={$username}' class='chip " . (($admin_filter === $username) ? 'active' : '') . "'><i class='fa-regular fa-user'></i> Milikku</a>";
    foreach ($all_users as $u) {
        if ($u['username'] !== $username) {
            $lbl = !empty($u['nama_lengkap']) ? $u['nama_lengkap'] : $u['username'];
            echo "<a href='?page=workspace&filter=" . h($u['username']) . "' class='chip " . (($admin_filter === $u['username']) ? 'active' : '') . "'><i class='fa-solid fa-user'></i> " . h($lbl) . "</a>";
        }
    }
    echo "<a href='?page=workspace&filter=semua' class='chip " . (($admin_filter === 'semua') ? 'active' : '') . "'><i class='fa-solid fa-users'></i> Semua</a></div>";
} ?>

<!-- ── SORT HEADER ────────────────────────────────────────────── -->
<?php if ($ws_view === 'home' || $ws_view === 'starred'): ?>
<div class="list-header">
    <div class="select-all-wrap">
        <input type="checkbox" class="item-checkbox" id="selectAllHeader" onclick="toggleSelectAll(this)" style="opacity:1;">
    </div>
    <div>
        <a href="<?= $base_url ?>sort=<?= ($sort === 'nama_asc') ? 'nama_desc' : 'nama_asc' ?>">
            Nama <?= $sort === 'nama_asc' ? '↓' : ($sort === 'nama_desc' ? '↑' : '') ?>
        </a>
    </div>
    <div class="col-owner">Pemilik</div>
    <div class="col-date">
        <a href="<?= $base_url ?>sort=<?= ($sort === 'date_desc') ? 'date_asc' : 'date_desc' ?>">Tanggal</a>
    </div>
    <div class="col-size">Ukuran</div>
    <div></div>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════════
     WORKSPACE CONTAINER
     ══════════════════════════════════════════════════════════ -->
<div id="workspaceContainer" class="view-list">
<?php

// ── Helper render item-card ───────────────────────────────────
// Semua item (folder & file) dicetak lewat fungsi ini agar
// konsisten. Context menu diaktifkan via JS menggunakan data-*.
function renderItemCard(array $d, bool $is_trash = false): void {
    $starred_cls   = $d['starred']    ? 'is-starred' : '';
    $trash_style   = $is_trash        ? "style='opacity:.6;'" : '';
    $name_style    = $is_trash        ? "style='text-decoration:line-through;'" : '';
    $draggable     = $is_trash        ? '' : "draggable='true'";
    $dbl_open      = (!$is_trash && $d['type'] === 'folder')
                     ? "ondblclick=\"window.location='?page=workspace&folder_id={$d['id']}'\"" : '';

    // data-* attrs yang dibaca oleh context_menu.js
    $cm_data = cmAttrs([
        'id'        => $d['id'],
        'type'      => $d['type'],        // folder|file|link
        'item-type' => $d['type'],
        'name'      => $d['name'],
        'icon'      => $d['icon'],
        'color'     => $d['color'],
        'owner'     => $d['owner'],
        'date'      => $d['date'],
        'size'      => $d['size'],
        'desc'      => $d['desc'],
        'url'       => $d['url'],
        'tags'      => $d['tags'],
        'share'     => $d['share'],
        'preview'   => $d['preview'],
        'starred'   => $d['starred'] ? '1' : '0',
        'is-trash'  => $is_trash ? '1' : '0',
        'link-url'  => $d['link_url'] ?? '',
        'folder-id' => $d['folder_id'] ?? '',
    ]);

    echo "
<div class='item-card {$starred_cls}' {$draggable} {$dbl_open}
     onclick='handleItemClick(event,this)'
     oncontextmenu='showContextMenu(event,this)'
     {$cm_data}
     {$trash_style}>
    <input type='checkbox' class='item-checkbox' onclick='handleCheckbox(event,this)'>
    <div class='item-info-wrap'>
        <div class='item-icon-lg' style='color:{$d['color']};position:relative;'>
            <i class='{$d['icon']}'></i>
            " . ($d['starred'] ? "<span class='item-star-badge'><i class='fa-solid fa-star'></i></span>" : "") . "
        </div>
        <div class='item-details'>
            <div class='item-name' {$name_style}>{$d['name']}" .
            ($d['tags'] ? "<span class='tag-badge'><i class='fa-solid fa-tag'></i> {$d['tags']}</span>" : "") .
            "</div>
        </div>
    </div>
    <div class='col-owner'>
        <img src='https://ui-avatars.com/api/?name=" . urlencode($d['owner']) . "&background=1a1a1a&color=ffffff&size=32' alt=''>
        " . htmlspecialchars($d['owner']) . "
    </div>
    <div class='col-date'>{$d['date']}</div>
    <div class='col-size'>{$d['size']}</div>
    <div class='action-wrapper'>
        <button class='btn-dots' onclick='showContextMenu(event,this.closest(\".item-card\"))' aria-label='Menu aksi'>
            <i class='fa-solid fa-ellipsis-vertical'></i>
        </button>
    </div>
</div>";
}

// ═══════════════════════════════════════════════════════════
// VIEW: STARRED
// ═══════════════════════════════════════════════════════════
if ($ws_view === 'starred') {
    $has = false;

    // Folder berbintang
    $sq = isAdmin()
        ? "SELECT * FROM folders WHERE is_starred=1 AND is_deleted=0 ORDER BY {$order_f}"
        : "SELECT * FROM folders WHERE is_starred=1 AND is_deleted=0 AND owner_username=? ORDER BY {$order_f}";
    $stmt = $mysqli->prepare($sq);
    if (!isAdmin()) { $stmt->bind_param('s', $username); }
    $stmt->execute(); $res = $stmt->get_result(); $stmt->close();

    while ($f = $res->fetch_assoc()) {
        $has = true;
        renderItemCard([
            'id'       => $f['id'],      'type'   => 'folder',
            'name'     => h($f['nama_folder']),
            'icon'     => 'fa-solid ' . ($f['icon'] ?? 'fa-folder'),
            'color'    => $f['warna'] ?? '#555',
            'owner'    => $f['owner_username'],
            'date'     => '-',           'size'    => '-',
            'desc'     => h($f['deskripsi'] ?? ''),
            'url'      => '',            'tags'    => '',
            'share'    => '',            'preview' => 'none',
            'starred'  => true,
            'folder_id'=> $f['id'],
        ]);
    }

    // File berbintang
    $sq = isAdmin()
        ? "SELECT * FROM files WHERE is_starred=1 AND is_deleted=0 ORDER BY {$order_i}"
        : "SELECT * FROM files WHERE is_starred=1 AND is_deleted=0 AND owner_username=? ORDER BY {$order_i}";
    $stmt = $mysqli->prepare($sq);
    if (!isAdmin()) { $stmt->bind_param('s', $username); }
    $stmt->execute(); $res = $stmt->get_result(); $stmt->close();

    while ($f = $res->fetch_assoc()) {
        $has = true; $is_lnk = ($f['jenis'] === 'link');
        $ic  = $is_lnk ? ['fa-link', '#555'] : getFileIcon($f['nama_file']);
        $fp2 = UPLOAD_DIR . $f['file_path'];
        $sz  = ($f['jenis'] === 'file' && file_exists($fp2)) ? formatBytes(filesize($fp2)) : ($is_lnk ? 'Tautan' : '-');
        renderItemCard([
            'id'       => $f['id'],      'type'    => $f['jenis'],
            'name'     => h($f['nama_file']),
            'icon'     => 'fa-solid ' . $ic[0],
            'color'    => $ic[1],
            'owner'    => $f['owner_username'],
            'date'     => date('d M Y', strtotime($f['tanggal_upload'])),
            'size'     => $sz,           'desc'    => '',
            'url'      => $is_lnk ? '' : ($fp2),
            'tags'     => h($f['tags'] ?? ''),
            'share'    => h($f['share_token'] ?? ''),
            'preview'  => $is_lnk ? 'none' : getPreviewType($f['nama_file']),
            'starred'  => true,
            'link_url' => h($f['link_url'] ?? ''),
            'folder_id'=> $f['folder_id'] ?? '',
        ]);
    }

    if (!$has) echo "<div class='empty-state' style='cursor:default;'><i class='fa-solid fa-star' style='color:#f59e0b;'></i><h3>Belum Ada Yang Dibintangi</h3><p>Klik kanan pada file atau folder, lalu pilih Bintangi.</p></div>";

// ═══════════════════════════════════════════════════════════
// VIEW: TRASH
// ═══════════════════════════════════════════════════════════
} elseif ($ws_view === 'trash') {
    $tc2 = 0;

    $sq = isAdmin()
        ? "SELECT * FROM folders WHERE is_deleted=1 ORDER BY {$order_f}"
        : "SELECT * FROM folders WHERE owner_username=? AND is_deleted=1 ORDER BY {$order_f}";
    $stmt = $mysqli->prepare($sq);
    if (!isAdmin()) { $stmt->bind_param('s', $username); }
    $stmt->execute(); $res = $stmt->get_result(); $stmt->close();

    while ($f = $res->fetch_assoc()) {
        $tc2++;
        renderItemCard([
            'id'      => $f['id'],  'type'    => 'folder',
            'name'    => h($f['nama_folder']),
            'icon'    => 'fa-solid ' . ($f['icon'] ?? 'fa-folder'),
            'color'   => $f['warna'] ?? '#555',
            'owner'   => $f['owner_username'],
            'date'    => '-', 'size'   => '-',
            'desc'    => h($f['deskripsi'] ?? ''),
            'url'     => '', 'tags'   => '', 'share'  => '',
            'preview' => 'none', 'starred' => false,
            'folder_id' => $f['id'],
        ], true);
    }

    $sq = isAdmin()
        ? "SELECT * FROM files WHERE is_deleted=1 ORDER BY {$order_i}"
        : "SELECT * FROM files WHERE owner_username=? AND is_deleted=1 ORDER BY {$order_i}";
    $stmt = $mysqli->prepare($sq);
    if (!isAdmin()) { $stmt->bind_param('s', $username); }
    $stmt->execute(); $res = $stmt->get_result(); $stmt->close();

    while ($f = $res->fetch_assoc()) {
        $tc2++; $is_lnk = ($f['jenis'] === 'link');
        $ic = $is_lnk ? ['fa-link', '#555'] : getFileIcon($f['nama_file']);
        renderItemCard([
            'id'      => $f['id'],  'type'    => $f['jenis'],
            'name'    => h($f['nama_file']),
            'icon'    => 'fa-solid ' . $ic[0], 'color'   => $ic[1],
            'owner'   => $f['owner_username'],
            'date'    => date('d M Y', strtotime($f['tanggal_upload'])),
            'size'    => '-', 'desc'   => 'Dihapus',
            'url'     => '', 'tags'   => '', 'share'  => '',
            'preview' => 'none', 'starred' => false,
            'link_url'=> h($f['link_url'] ?? ''), 'folder_id' => '',
        ], true);
    }

    if ($tc2 === 0) echo "<div class='empty-state' style='cursor:default;'><i class='fa-solid fa-recycle'></i><h3>Tong Sampah Bersih</h3></div>";

// ═══════════════════════════════════════════════════════════
// VIEW: HOME (folder & file normal)
// ═══════════════════════════════════════════════════════════
} else {
    // ── FOLDER QUERY ──
    $f_sql = 'SELECT * FROM folders WHERE is_deleted=0';
    $f_params = []; $f_types = '';
    if ($active_folder) { $f_sql .= ' AND parent_id=?'; $f_types .= 'i'; $f_params[] = $active_folder; }
    else $f_sql .= ' AND parent_id IS NULL';
    if (isAdmin() && !$active_folder && !$search_query) {
        if ($admin_filter !== 'semua') { $f_sql .= ' AND owner_username=?'; $f_types .= 's'; $f_params[] = $admin_filter; }
    } elseif (!isAdmin()) {
        $f_sql .= ' AND owner_username=?'; $f_types .= 's'; $f_params[] = $username;
    }
    if ($search_query) { $f_sql .= ' AND nama_folder LIKE ?'; $f_types .= 's'; $f_params[] = '%' . $search_query . '%'; }
    $f_sql .= " ORDER BY is_starred DESC, {$order_f}"; // bintang duluan

    $stmt = $mysqli->prepare($f_sql);
    if ($f_types) { $stmt->bind_param($f_types, ...$f_params); }
    $stmt->execute(); $res = $stmt->get_result();
    $has = false;

    while ($f = $res->fetch_assoc()) {
        $has = true;
        renderItemCard([
            'id'       => $f['id'],  'type'   => 'folder',
            'name'     => h($f['nama_folder']),
            'icon'     => 'fa-solid ' . ($f['icon'] ?? 'fa-folder'),
            'color'    => $f['warna'] ?? '#555',
            'owner'    => $f['owner_username'],
            'date'     => '-',  'size'    => '-',
            'desc'     => h($f['deskripsi'] ?? ''),
            'url'      => '',   'tags'    => '',
            'share'    => '',   'preview' => 'none',
            'starred'  => (bool)$f['is_starred'],
            'folder_id'=> $f['id'],
        ]);
    }
    $stmt->close();

    // ── FILE QUERY ──
    if ($active_folder || $search_query) {
        $i_sql = 'SELECT * FROM files WHERE is_deleted=0';
        $i_types = ''; $i_params = [];
        if ($active_folder) { $i_sql .= ' AND folder_id=?'; $i_types .= 'i'; $i_params[] = $active_folder; }
        if ($search_query)  { $i_sql .= ' AND (nama_file LIKE ? OR tags LIKE ?)'; $i_types .= 'ss'; $i_params[] = '%'.$search_query.'%'; $i_params[] = '%'.$search_query.'%'; }
        if (!isAdmin())     { $i_sql .= ' AND owner_username=?'; $i_types .= 's'; $i_params[] = $username; }
        $i_sql .= " ORDER BY is_starred DESC, {$order_i}";

        $stmt = $mysqli->prepare($i_sql);
        if ($i_types) { $stmt->bind_param($i_types, ...$i_params); }
        $stmt->execute(); $res = $stmt->get_result();

        while ($item = $res->fetch_assoc()) {
            $has = true;
            $is_lnk  = ($item['jenis'] === 'link');
            $ic      = $is_lnk ? ['fa-link', '#555'] : getFileIcon($item['nama_file']);
            $fp2     = UPLOAD_DIR . $item['file_path'];
            $sz      = ($item['jenis'] === 'file' && file_exists($fp2)) ? formatBytes(filesize($fp2)) : ($is_lnk ? 'Tautan' : '-');
            $file_url = $is_lnk ? '' : $fp2;
            renderItemCard([
                'id'       => $item['id'],
                'type'     => $item['jenis'],
                'name'     => h($item['nama_file']),
                'icon'     => 'fa-solid ' . $ic[0],
                'color'    => $ic[1],
                'owner'    => $item['owner_username'],
                'date'     => date('d M Y', strtotime($item['tanggal_upload'])),
                'size'     => $sz,
                'desc'     => '',
                'url'      => $file_url,
                'tags'     => h($item['tags'] ?? ''),
                'share'    => h($item['share_token'] ?? ''),
                'preview'  => $is_lnk ? 'none' : getPreviewType($item['nama_file']),
                'starred'  => (bool)$item['is_starred'],
                'link_url' => h($item['link_url'] ?? ''),
                'folder_id'=> $item['folder_id'] ?? '',
            ]);
        }
        $stmt->close();
    }

    if (!$has) {
        echo "<div class='empty-state' onclick=\"openModal('addFolderModal')\">
            <i class='fa-solid fa-folder-plus'></i>
            <h3>Workspace Kosong</h3>
            <p>Klik untuk membuat folder baru.</p>
        </div>";
    }
}
?>
</div><!-- end #workspaceContainer -->

<!-- CSS Tambahan: Starred Indicator & Item Improvements -->
<style>
/* ── Starred badge di pojok icon ─────────────────────────── */
.item-star-badge {
    position: absolute;
    top: -4px; right: -4px;
    width: 16px; height: 16px;
    background: #fff;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 1px 4px rgba(0,0,0,.15);
}
.item-star-badge i {
    font-size: .52rem;
    color: #f59e0b;
}

/* ── Starred item — faint gold accent ───────────────────── */
.item-card.is-starred {
    background: linear-gradient(to right, #fffbeb 0%, var(--surface) 100%);
    border-left: 2px solid #fbbf24 !important;
}
.item-card.is-starred:hover { background: #fef3c7; }

/* Starred in grid view: top bar */
.view-grid .item-card.is-starred {
    background: var(--surface);
    border-top: 2px solid #fbbf24 !important;
    border-left: 1px solid var(--border) !important;
}
</style>