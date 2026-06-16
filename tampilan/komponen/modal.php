<!-- ADD FOLDER MODAL -->
<div id="addFolderModal" class="modal">
  <div class="modal-content">
    <div class="modal-title">
      <span><i class="fa-solid fa-folder-plus"></i> Folder Baru</span>
      <button class="close-btn" onclick="closeModal('addFolderModal')">&times;</button>
    </div>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="add_folder">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
      <?php if(isset($active_folder) && $active_folder) echo "<input type='hidden' name='parent_id' value='{$active_folder}'>"; ?>
      <label>Nama Folder</label>
      <input type="text" name="nama_folder" placeholder="cth: Dokumen Proyek" required>
      <label>Deskripsi <span style="font-weight:400;color:var(--text-muted);">(opsional)</span></label>
      <input type="text" name="deskripsi" placeholder="Catatan singkat tentang folder ini">
      <?php if(isSuperAdmin() || isAdmin()){ ?>
      <label>Pemilik</label>
      <select name="owner_username">
        <?php foreach($all_users as $u) echo "<option value='".h($u['username'])."'".($u['username']===($username??'')?' selected':'').">".h($u['nama_lengkap']??$u['username'])." (@".h($u['username']).")</option>"; ?>
      </select>
      <?php } ?>
      <button type="submit" class="btn-submit-modal"><i class="fa-solid fa-folder-plus"></i> Buat Folder</button>
    </form>
  </div>
</div>

<!-- EDIT FOLDER MODAL -->
<div id="editFolderModal" class="modal">
  <div class="modal-content">
    <div class="modal-title">
      <span><i class="fa-solid fa-pen"></i> Edit Folder</span>
      <button class="close-btn" onclick="closeModal('editFolderModal')">&times;</button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="edit_folder">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
      <input type="hidden" name="folder_id" id="edit_folder_id">
      <label>Nama Folder</label>
      <input type="text" name="nama_folder" id="edit_folder_nama" required>
      <label>Deskripsi</label>
      <input type="text" name="deskripsi" id="edit_folder_desc">
      <label>Icon <span style="font-weight:400;color:var(--text-muted);">(Font Awesome class)</span></label>
      <input type="text" name="icon" id="edit_folder_icon" placeholder="fa-folder">
      <label>Warna Folder</label>
      <input type="color" name="warna" id="edit_folder_warna" value="#0a0a0a">
      <button type="submit" class="btn-submit-modal"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
    </form>
  </div>
</div>

<!-- ADD ITEM MODAL (File / Link) -->
<div id="addItemModal" class="modal">
  <div class="modal-content">
    <div class="modal-title">
      <span><i class="fa-solid fa-file-circle-plus"></i> Tambah Item</span>
      <button class="close-btn" onclick="closeModal('addItemModal')">&times;</button>
    </div>
    <form method="POST" enctype="multipart/form-data" id="addItemForm">
      <input type="hidden" name="action" value="add_item">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
      <input type="hidden" name="folder_id" value="<?= (int)($active_folder ?? 0) ?>">
      <input type="hidden" name="jenis" id="jenis_input" value="file">
      <div style="display:flex;gap:0;margin-bottom:20px;border:1px solid var(--border-dark);">
        <button type="button" id="tabFile" onclick="switchType('file')" style="flex:1;padding:10px;font-size:.75rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;border:none;background:var(--text-main);color:#fff;font-family:'Inter',sans-serif;"><i class="fa-solid fa-file-arrow-up"></i> Upload File</button>
        <button type="button" id="tabLink" onclick="switchType('link')" style="flex:1;padding:10px;font-size:.75rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;border:none;background:#f5f5f5;color:var(--text-main);font-family:'Inter',sans-serif;border-left:1px solid var(--border);"><i class="fa-solid fa-link"></i> Simpan Tautan</button>
      </div>
      <div id="form_file">
        <label>Pilih File <span style="font-weight:400;color:var(--text-muted);">(multiple diperbolehkan)</span></label>
        <div class="upload-zone" id="modalUploadZone" onclick="document.getElementById('modal_file_input').click()">
          <i class="fa-solid fa-cloud-arrow-up"></i>
          <p>Klik atau drag &amp; drop file ke sini</p>
          <input type="file" id="modal_file_input" name="file_upload[]" multiple>
        </div>
        <div id="selectedFilesList"></div>
        <label>Label / Tag <span style="font-weight:400;color:var(--text-muted);">(opsional)</span></label>
        <input type="text" name="tags" placeholder="cth: penting, laporan, 2024">
      </div>
      <div id="form_link" style="display:none;">
        <label>Nama Tautan</label>
        <input type="text" name="nama_link" placeholder="cth: Website Referensi">
        <label>URL</label>
        <input type="url" name="link_url" placeholder="https://...">
        <label>Label / Tag <span style="font-weight:400;color:var(--text-muted);">(opsional)</span></label>
        <input type="text" name="tags" placeholder="cth: referensi, tools">
      </div>
      <button type="submit" class="btn-submit-modal"><i class="fa-solid fa-plus"></i> Tambahkan</button>
    </form>
  </div>
</div>

<!-- MOVE MODAL -->
<div id="moveModal" class="modal">
  <div class="modal-content">
    <div class="modal-title">
      <span><i class="fa-solid fa-folder-tree"></i> Pindahkan Item</span>
      <button class="close-btn" onclick="closeModal('moveModal')">&times;</button>
    </div>
    <form method="POST" id="moveForm">
      <input type="hidden" name="action" value="move_item">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
      <input type="hidden" name="move_type" id="move_type_input">
      <input type="hidden" name="move_id" id="move_id_input">
      <p id="move_item_name" style="font-size:.88rem;font-weight:600;color:var(--text-muted);margin-bottom:16px;padding:12px;background:#f9f9f9;border:1px solid var(--border);"></p>
      <label>Pindah ke Folder</label>
      <select name="target_folder" id="moveTargetSelect">
        <option value="root">&#8212; Root (Tanpa Folder) &#8212;</option>
        <?php foreach($all_folders_list as $af) echo "<option value='".(int)$af['id']."'>".h($af['nama_folder'])." (".h($af['owner_username']).")</option>"; ?>
      </select>
      <button type="submit" class="btn-submit-modal"><i class="fa-solid fa-check"></i> Pindahkan</button>
    </form>
  </div>
</div>

<!-- BULK MOVE MODAL -->
<div id="bulkMoveModal" class="modal">
  <div class="modal-content">
    <div class="modal-title">
      <span><i class="fa-solid fa-folder-tree"></i> Pindah Massal</span>
      <button class="close-btn" onclick="closeModal('bulkMoveModal')">&times;</button>
    </div>
    <form method="POST" id="bulkMoveForm">
      <input type="hidden" name="action" value="bulk_move">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
      <input type="hidden" name="ids" id="bulkMoveIds">
      <input type="hidden" name="types" id="bulkMoveTypes">
      <input type="hidden" name="target_folder" id="bulkMoveTarget">
      <label>Pindah ke Folder</label>
      <select id="bulkMoveTargetSelect">
        <option value="root">&#8212; Root (Tanpa Folder) &#8212;</option>
        <?php foreach($all_folders_list as $af) echo "<option value='".(int)$af['id']."'>".h($af['nama_folder'])." (".h($af['owner_username']).")</option>"; ?>
      </select>
      <button type="button" onclick="executeBulkMove()" class="btn-submit-modal"><i class="fa-solid fa-check"></i> Pindahkan Semua</button>
    </form>
  </div>
</div>

<!-- SETTINGS MODAL -->
<div id="settingsModal" class="modal">
  <div class="modal-content">
    <div class="modal-title">
      <span><i class="fa-solid fa-gear"></i> Pengaturan Akun</span>
      <button class="close-btn" onclick="closeModal('settingsModal')">&times;</button>
    </div>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="action" value="update_settings">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
      <label>Foto Profil</label>
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:4px;">
        <img src="<?= h($path_foto ?? '') ?>" style="width:56px;height:56px;object-fit:cover;filter:grayscale(100%);border:1px solid var(--border-dark);" alt="Foto Profil">
        <input type="file" name="foto_profil" accept="image/*" style="font-size:.82rem;">
      </div>
      <label>Nama Lengkap</label>
      <input type="text" name="nama_lengkap" value="<?= h($nama_lengkap ?? '') ?>" required>
      <label>Password Baru <span style="font-weight:400;color:var(--text-muted);">(kosongkan jika tidak ingin ganti)</span></label>
      <input type="password" name="new_password" placeholder="Minimal 8 karakter" autocomplete="new-password">
      <button type="submit" class="btn-submit-modal"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
    </form>
  </div>
</div>

<!-- ADD USER MODAL (SuperAdmin) -->
<?php if(isSuperAdmin()){ ?>
<div id="addUserModal" class="modal">
  <div class="modal-content">
    <div class="modal-title">
      <span><i class="fa-solid fa-user-plus"></i> Tambah User Baru</span>
      <button class="close-btn" onclick="closeModal('addUserModal')">&times;</button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="add_user">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
      <label>Username</label>
      <input type="text" name="new_username" placeholder="Username unik" required>
      <label>Nama Lengkap</label>
      <input type="text" name="new_nama" placeholder="Nama tampilan">
      <label>Password</label>
      <input type="password" name="new_password" placeholder="Minimal 8 karakter" required>
      <label>Role</label>
      <select name="new_role">
        <option value="user">User</option>
        <option value="admin">Admin</option>
        <option value="superadmin">Super Admin</option>
      </select>
      <button type="submit" class="btn-submit-modal"><i class="fa-solid fa-user-plus"></i> Buat Akun</button>
    </form>
  </div>
</div>

<!-- EDIT USER MODAL -->
<div id="editUserModal" class="modal">
  <div class="modal-content">
    <div class="modal-title">
      <span><i class="fa-solid fa-user-pen"></i> Edit User</span>
      <button class="close-btn" onclick="closeModal('editUserModal')">&times;</button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="edit_user">
      <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
      <input type="hidden" name="edit_uid" id="eu_id">
      <label>Username</label>
      <input type="text" id="eu_username" disabled style="color:var(--text-muted);">
      <label>Nama Lengkap</label>
      <input type="text" name="edit_nama" id="eu_nama" required>
      <label>Role</label>
      <select name="edit_role" id="eu_role">
        <option value="user">User</option>
        <option value="admin">Admin</option>
        <option value="superadmin">Super Admin</option>
      </select>
      <label>Password Baru <span style="font-weight:400;color:var(--text-muted);">(kosongkan jika tidak ingin ganti)</span></label>
      <input type="password" name="edit_password" placeholder="Kosongkan untuk tidak mengubah">
      <button type="submit" class="btn-submit-modal"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
    </form>
  </div>
</div>
<?php } ?>

<!-- PREVIEW OVERLAY -->
<div class="preview-overlay" id="previewOverlay">
    <div class="preview-header">
        <div class="preview-filename"><i class="fa-solid fa-file"></i> <span id="previewFileName">File</span></div>
        <div class="preview-actions">
            <a href="#" id="previewDownloadBtn"><i class="fa-solid fa-download"></i> Download</a>
            <a href="#" id="previewOpenBtn" target="_blank"><i class="fa-regular fa-eye"></i> Buka Tab Baru</a>
            <button onclick="closePreview()"><i class="fa-solid fa-xmark"></i> Tutup</button>
        </div>
    </div>
    <div class="preview-body" id="previewBody"></div>
</div>

<!-- CONFIRM DIALOG -->
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <div class="confirm-icon" id="confirmIcon">&#9888;&#65039;</div>
        <h3 id="confirmTitle">Konfirmasi</h3>
        <p id="confirmMessage">Apakah Anda yakin?</p>
        <div class="confirm-btns">
            <button class="confirm-cancel" onclick="closeConfirm()">Batal</button>
            <button class="confirm-danger" id="confirmActionBtn" onclick="executeConfirmAction()">Konfirmasi</button>
        </div>
    </div>
</div>

<!-- DROP OVERLAY -->
<div class="global-drop-overlay" id="globalDropOverlay">
    <div style="text-align:center;">
        <i class="fa-solid fa-cloud-arrow-up" style="font-size:5rem;margin-bottom:20px;display:block;animation:bounce 2s infinite;"></i>
        <div class="drop-pill">Lepaskan untuk mengunggah<?php if(isset($active_folder) && $active_folder) echo ' ke folder ini';?></div>
    </div>
</div>

<!-- TOAST -->
<div id="toast"></div>

<!-- HIDDEN FORMS for bulk operations and rename -->
<form id="bulkDeleteForm" method="POST" style="display:none;">
  <input type="hidden" name="action" value="bulk_delete">
  <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
  <input type="hidden" name="ids" id="bulkDeleteIds">
  <input type="hidden" name="types" id="bulkDeleteTypes">
</form>
<form id="deleteUserForm" method="POST" style="display:none;">
  <input type="hidden" name="action" value="delete_user">
  <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
  <input type="hidden" name="del_uid" id="del_uid_input">
</form>
<form id="renameForm" method="POST" style="display:none;">
  <input type="hidden" name="action" value="rename_item">
  <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
  <input type="hidden" name="item_id" id="renameItemId">
  <input type="hidden" name="item_type" id="renameItemType">
  <input type="hidden" name="new_name" id="renameNewName">
</form>
<form id="autoUploadForm" method="POST" enctype="multipart/form-data" style="display:none;">
  <input type="hidden" name="action" value="add_item">
  <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
  <input type="hidden" name="folder_id" value="<?= (int)($active_folder ?? 0) ?>">
  <input type="hidden" name="jenis" value="file">
  <input type="file" id="autoFileInput" name="file_upload[]" multiple>
</form>