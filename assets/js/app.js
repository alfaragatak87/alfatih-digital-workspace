/** * ALFATIH DIGITAL WORKSPACE — assets/js/app.js 
 * Semua skrip client-side: sidebar, modal, drag-drop, 
 * preview, CV builder, animasi, ripple, SweetAlert2. 
 * * Variabel PHP yang harus di-inject via <script> sebelum file ini: 
 * const CSRF = '<?= h($csrf_token) ?>'; 
 * const CURRENT_PAGE = '<?= h($current_page) ?>'; 
 * * Variabel PHP untuk CV builder (inject terpisah di cv_builder.php): 
 * let eduCount   = <?= count($profile_data['pendidikan'] ?? []) ?>; 
 * let expCount   = <?= count($profile_data['pengalaman'] ?? []) ?>; 
 * let skillCount = <?= count($profile_data['keahlian']  ?? []) ?>; 
 * let portoCount = <?= count($profile_data['portfolio'] ?? []) ?>; 
 */

// ── SIDEBAR ──────────────────────────────────────────────────
let sidebarOpen = false;
function toggleSidebar() {
    const sb = document.getElementById('sidebar');
    const ov = document.getElementById('sidebarOverlay');
    sidebarOpen = !sidebarOpen;
    if (sidebarOpen) {
        sb.classList.add('active');
        ov.classList.add('active');
        document.body.style.overflow = 'hidden';
    } else {
        sb.classList.remove('active');
        ov.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// ── PROFILE MENU ─────────────────────────────────────────────
function toggleProfileMenu() {
    document.getElementById('profileMenu').classList.toggle('show');
}
function closeAllMenus() {
    document.getElementById('profileMenu').classList.remove('show');
    document.querySelectorAll('.action-dropdown.show').forEach(d => d.classList.remove('show'));
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.profile-container'))
        document.getElementById('profileMenu').classList.remove('show');
    if (!e.target.closest('.action-wrapper') && !e.target.closest('.btn-dots'))
        document.querySelectorAll('.action-dropdown.show').forEach(d => d.classList.remove('show'));
    if (!e.target.closest('.dropdown'))
        document.querySelectorAll('.dropdown-content').forEach(d => d.style.display = '');
});

// ── MODALS ───────────────────────────────────────────────────
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = '';
}
document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('click', function(e) {
        if (e.target === m) closeModal(m.id);
    });
});

function openEditModal(id, nama, desc, icon, warna) {
    document.getElementById('edit_folder_id').value   = id;
    document.getElementById('edit_folder_nama').value = nama;
    document.getElementById('edit_folder_desc').value = desc;
    document.getElementById('edit_folder_icon').value = icon || 'fa-folder';
    document.getElementById('edit_folder_warna').value = warna || '#0a0a0a';
    openModal('editFolderModal');
}
function openMoveModal(type, id, name) {
    document.getElementById('move_type_input').value = type;
    document.getElementById('move_id_input').value   = id;
    document.getElementById('move_item_name').textContent = '📦 ' + name;
    openModal('moveModal');
}

// ── RIGHT SIDEBAR ─────────────────────────────────────────────
let isSidebarOpen = false;
function toggleRightSidebar() {
    const rs = document.getElementById('rightSidebar');
    if (!rs) return;
    isSidebarOpen = !isSidebarOpen;
    if (isSidebarOpen) rs.classList.add('active');
    else rs.classList.remove('active');
}

// ── TOAST ─────────────────────────────────────────────────────
function showToast(msg) {
    const t = document.getElementById('toast');
    if (!t) return;
    t.innerHTML = msg;
    t.classList.remove('show');
    void t.offsetWidth; // trigger reflow
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3700);
}

// ── VIEW MODE (list / grid) ───────────────────────────────────
function setViewMode(mode) {
    const container = document.getElementById('workspaceContainer');
    const btnListEl = document.getElementById('btnList');
    const btnGridEl = document.getElementById('btnGrid');
    if (!container) return;
    if (mode === 'list') {
        container.className = 'view-list';
        if (btnListEl) btnListEl.classList.add('active');
        if (btnGridEl) btnGridEl.classList.remove('active');
    } else {
        container.className = 'view-grid';
        if (btnGridEl) btnGridEl.classList.add('active');
        if (btnListEl) btnListEl.classList.remove('active');
    }
    localStorage.setItem('viewMode', mode);
}
setViewMode(localStorage.getItem('viewMode') || 'list');

// ── ITEM CLICK (select + right sidebar) ──────────────────────
function handleItemClick(event, el) {
    if (event.target.classList.contains('item-checkbox')) return;
    if (event.target.closest('.action-wrapper')) return;
    document.querySelectorAll('#workspaceContainer .item-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    selectItem(el);
}

function selectItem(el) {
    if (window.innerWidth <= 768) { openMobilePanel(el); return; }
    if (!isSidebarOpen) toggleRightSidebar();
    
    const { type, name, icon: iconClass, color, owner, date, size, desc, url: fileUrl, tags, share: shareLink } = el.dataset;
    const ah = el.querySelector('.hidden-action-html');
    
    document.getElementById('rs_title').innerHTML = type === 'folder'
        ? '<i class="fa-solid fa-folder"></i> Detail Folder'
        : '<i class="fa-solid fa-file"></i> Detail File';
        
    const previewIcon = document.getElementById('rs_icon');
    const isImage = fileUrl && /\.(png|jpe?g|gif|webp|svg)$/i.test(name);
    
    if (isImage) {
        previewIcon.innerHTML = `<img src="${fileUrl}" style="max-width:100%;max-height:180px;object-fit:contain;" onerror="this.outerHTML='<i class=\\'${iconClass}\\' style=\\'font-size:3rem;\\'></i>'">`;
        previewIcon.style.cssText = 'padding:8px;background:#f5f5f5;border:1px solid var(--border);text-align:center;margin-bottom:16px;min-height:120px;display:flex;align-items:center;justify-content:center;overflow:hidden;';
    } else {
        previewIcon.innerHTML = `<i class="${iconClass}" style="font-size:3rem;"></i>`;
        previewIcon.style.cssText = `padding:28px;background:#f5f5f5;border:1px solid var(--border);text-align:center;margin-bottom:16px;display:flex;align-items:center;justify-content:center;`;
    }
    
    document.getElementById('rs_name').innerText  = name;
    document.getElementById('rs_type').innerText  = type === 'folder' ? 'Folder' : (type === 'link' ? 'Tautan Website' : 'File Dokumen');
    document.getElementById('rs_owner').innerText = owner;
    document.getElementById('rs_date').innerText  = date;
    document.getElementById('rs_size').innerText  = size;
    document.getElementById('rs_desc').innerText  = (desc && desc !== '-') ? desc : 'Tidak ada catatan.';
    document.getElementById('rs_tags').innerText  = (tags && tags !== '') ? tags : 'Tidak ada label';
    
    const actCont = document.getElementById('rs_actions');
    if (ah) { actCont.innerHTML = ah.innerHTML; actCont.style.display = 'flex'; }
    else { actCont.innerHTML = ''; actCont.style.display = 'none'; }
    
    const qrCont = document.getElementById('rs_qr_container');
    const qrImg  = document.getElementById('rs_qr_img');
    if (shareLink && shareLink !== '') {
        qrCont.style.display = 'block';
        qrImg.src = 'https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=' + encodeURIComponent(shareLink);
    } else {
        qrCont.style.display = 'none';
    }
}

// ── MOBILE PANEL ─────────────────────────────────────────────
function openMobilePanel(el) {
    const o = document.getElementById('mobilePanelOverlay');
    const p = document.getElementById('mobileDetailPanel');
    const c = document.getElementById('mobileDetailContent');
    if (!o || !p || !c) return;
    const ah = el.querySelector('.hidden-action-html');
    const html_a = ah ? ah.innerHTML : '';
    c.innerHTML = `<div style="text-align:center;margin-bottom:16px;"><div style="font-size:2.5rem;margin-bottom:10px;"><i class="${el.dataset.icon}"></i></div><h3 style="margin:0;font-size:1.05rem;font-family:'Playfair Display',serif;word-wrap:break-word;">${el.dataset.name}</h3></div><div style="display:flex;flex-direction:column;gap:0;border:1px solid var(--border-dark);">${html_a}</div>`;
    o.classList.add('active');
    p.classList.add('active');
}
function closeMobilePanel() {
    const o = document.getElementById('mobilePanelOverlay');
    const p = document.getElementById('mobileDetailPanel');
    if (o) o.classList.remove('active');
    if (p) p.classList.remove('active');
}

// ── CHECKBOXES & BULK SELECTION ───────────────────────────────
function handleCheckbox(event, cb) {
    event.stopPropagation();
    const card = cb.closest('.item-card');
    if (cb.checked) card.classList.add('selected');
    else card.classList.remove('selected');
    updateBulkToolbar();
}
function toggleSelectAll(master) {
    document.querySelectorAll('#workspaceContainer .item-checkbox:not(#selectAllMain):not(#selectAllHeader)').forEach(cb => {
        cb.checked = master.checked;
        const card = cb.closest('.item-card');
        if (card) {
            if (master.checked) card.classList.add('selected');
            else card.classList.remove('selected');
        }
    });
    updateBulkToolbar();
}
function getSelectedItems() {
    const items = [];
    document.querySelectorAll('#workspaceContainer .item-card.selected').forEach(c => {
        items.push({ id: c.dataset.id, type: c.dataset.itemType });
    });
    return items;
}
function updateBulkToolbar() {
    const items = getSelectedItems();
    const tb = document.getElementById('bulkToolbar');
    const bc = document.getElementById('bulkCount');
    if (!tb) return;
    if (items.length > 0) { tb.classList.add('active'); if (bc) bc.textContent = items.length + ' dipilih'; }
    else tb.classList.remove('active');
}
function deselectAll() {
    document.querySelectorAll('#workspaceContainer .item-card').forEach(c => c.classList.remove('selected'));
    document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
    const tb = document.getElementById('bulkToolbar');
    if (tb) tb.classList.remove('active');
}
function bulkDelete() {
    const items = getSelectedItems();
    if (items.length === 0) return;
    showConfirm('Hapus ' + items.length + ' Item?', 'Item dipindahkan ke Tong Sampah. Bisa dipulihkan nanti.', function() {
        document.getElementById('bulkDeleteIds').value   = JSON.stringify(items.map(i => i.id));
        document.getElementById('bulkDeleteTypes').value = JSON.stringify(items.map(i => i.type));
        document.getElementById('bulkDeleteForm').submit();
    });
}
function bulkMove() {
    const items = getSelectedItems();
    if (items.length === 0) return;
    openModal('bulkMoveModal');
}
function executeBulkMove() {
    const items  = getSelectedItems();
    const target = document.getElementById('bulkMoveTargetSelect').value;
    document.getElementById('bulkMoveIds').value    = JSON.stringify(items.map(i => i.id));
    document.getElementById('bulkMoveTypes').value  = JSON.stringify(items.map(i => i.type));
    document.getElementById('bulkMoveTarget').value = target;
    document.getElementById('bulkMoveForm').submit();
}

// ── CONFIRM DIALOG ────────────────────────────────────────────
let confirmCallback = null;
function showConfirm(title, message, callback, icon = '⚠️') {
    document.getElementById('confirmTitle').textContent   = title;
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmIcon').textContent    = icon;
    document.getElementById('confirmOverlay').classList.add('active');
    confirmCallback = callback;
}
function closeConfirm() {
    document.getElementById('confirmOverlay').classList.remove('active');
    confirmCallback = null;
}
function executeConfirmAction() {
    if (confirmCallback) confirmCallback();
    closeConfirm();
}

// ── ACTION DROPDOWN TOGGLE ───────────────────────────────────
function toggleActionMenu(event, id) {
    event.stopPropagation();
    const dd = document.getElementById(id);
    const isOpen = dd.classList.contains('show');
    document.querySelectorAll('.action-dropdown.show').forEach(d => d.classList.remove('show'));
    if (!isOpen) dd.classList.add('show');
}

// ── INLINE RENAME ─────────────────────────────────────────────
function startInlineRename(card) {
    if (!card) return;
    const nameEl = card.querySelector('.item-name');
    if (!nameEl || nameEl.querySelector('.rename-inline')) return;
    
    const oldName = nameEl.textContent.trim();
    const input   = document.createElement('input');
    input.type      = 'text';
    input.value     = oldName;
    input.className = 'rename-inline';
    
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter')  { e.preventDefault(); submitRename(card, input.value); }
        if (e.key === 'Escape') { e.preventDefault(); nameEl.textContent = oldName; }
    });
    input.addEventListener('blur',  function() { if (nameEl.contains(input)) nameEl.textContent = oldName; });
    input.addEventListener('click', e => e.stopPropagation());
    
    nameEl.textContent = '';
    nameEl.appendChild(input);
    input.focus();
    input.select();
}
function submitRename(card, newName) {
    if (!newName.trim()) return;
    document.getElementById('renameItemId').value   = card.dataset.id;
    document.getElementById('renameItemType').value = card.dataset.itemType;
    document.getElementById('renameNewName').value  = newName.trim();
    document.getElementById('renameForm').submit();
}

// ── PREVIEW MODAL ─────────────────────────────────────────────
function openPreview(filename, fileUrl, previewType, fileId) {
    const overlay = document.getElementById('previewOverlay');
    const body    = document.getElementById('previewBody');
    const fnEl    = document.getElementById('previewFileName');
    const dlBtn   = document.getElementById('previewDownloadBtn');
    const opBtn   = document.getElementById('previewOpenBtn');
    
    fnEl.textContent = filename;
    dlBtn.href = '?action=download_file&file_id=' + fileId;
    opBtn.href = '?action=view_file&file_id=' + fileId;
    body.innerHTML = '';
    
    if (previewType === 'image') {
        body.innerHTML = `<img src="${fileUrl}" alt="${filename}" style="cursor:zoom-in;" onclick="this.style.transform=this.style.transform==='scale(1.6)'?'none':'scale(1.6)';this.style.transition='transform .3s';">`;
    } else if (previewType === 'pdf') {
        body.innerHTML = `<iframe src="?action=view_file&file_id=${fileId}#toolbar=1" style="width:100%;height:100%;border:none;background:#fff;border-radius:0;"></iframe>`;
    } else if (previewType === 'video') {
        body.innerHTML = `<video controls autoplay style="max-width:90%;max-height:80vh;"><source src="${fileUrl}">Browser tidak mendukung video.</video>`;
    } else if (previewType === 'audio') {
        body.innerHTML = `<div style="text-align:center;color:#fff;"><i class="fa-solid fa-music" style="font-size:5rem;margin-bottom:24px;display:block;opacity:.5;"></i><h3 style="margin-bottom:20px;font-family:'Playfair Display',serif;">${filename}</h3><audio controls autoplay style="width:100%;max-width:500px;"><source src="${fileUrl}">Browser tidak mendukung audio.</audio></div>`;
    } else {
        body.innerHTML = `<div class="preview-unsupported"><i class="fa-solid fa-file-circle-question"></i><h3>Pratinjau Tidak Tersedia</h3><p>Format ini tidak bisa dipratinjau. Silakan download.</p></div>`;
    }
    overlay.classList.add('active');
}
function closePreview() {
    document.getElementById('previewOverlay').classList.remove('active');
    document.getElementById('previewBody').innerHTML = '';
}

// ── FAB (Mobile Floating Action Button) ──────────────────────
function toggleFab() {
    const m = document.getElementById('fabMenu');
    const b = document.getElementById('fabBtn');
    if (!m) return;
    m.classList.toggle('active');
    b.innerHTML = m.classList.contains('active')
        ? '<i class="fa-solid fa-xmark"></i>'
        : '<i class="fa-solid fa-plus"></i>';
}

// ── SWITCH TYPE (file/link in addItem modal) ──────────────────
function switchType(type) {
    document.getElementById('jenis_input').value = type;
    const ff = document.getElementById('form_file');
    const fl = document.getElementById('form_link');
    const tf = document.getElementById('tabFile');
    const tl = document.getElementById('tabLink');
    if (type === 'file') {
        ff.style.display = 'block'; fl.style.display = 'none';
        if (tf) { tf.style.background = 'var(--text-main)'; tf.style.color = '#fff'; }
        if (tl) { tl.style.background = '#f5f5f5'; tl.style.color = 'var(--text-main)'; }
    } else {
        ff.style.display = 'none'; fl.style.display = 'block';
        if (tl) { tl.style.background = 'var(--text-main)'; tl.style.color = '#fff'; }
        if (tf) { tf.style.background = '#f5f5f5'; tf.style.color = 'var(--text-main)'; }
    }
}

// ── FILE INPUT DISPLAY ────────────────────────────────────────
const modalFileInput = document.getElementById('modal_file_input');
if (modalFileInput) {
    modalFileInput.addEventListener('change', function() {
        const list = document.getElementById('selectedFilesList');
        if (this.files.length > 0) {
            let html = '<div style="margin-top:10px;border:1px solid var(--border);">';
            for (let i = 0; i < this.files.length; i++) {
                html += `<div style="padding:8px 12px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;font-size:.83rem;"><i class="fa-solid fa-file" style="color:var(--text-muted);"></i> ${this.files[i].name} <span style="margin-left:auto;font-size:.72rem;color:var(--text-muted);">${(this.files[i].size/1024/1024).toFixed(2)} MB</span></div>`;
            }
            html += '</div>';
            list.innerHTML = html;
        }
    });
}
const uploadZone = document.getElementById('modalUploadZone');
if (uploadZone) {
    ['dragenter','dragover'].forEach(ev => uploadZone.addEventListener(ev, function(e) { e.preventDefault(); this.classList.add('dragover'); }));
    ['dragleave','drop'].forEach(ev => uploadZone.addEventListener(ev, function(e) { e.preventDefault(); this.classList.remove('dragover'); }));
    uploadZone.addEventListener('drop', function(e) {
        if (e.dataTransfer.files.length) {
            modalFileInput.files = e.dataTransfer.files;
            modalFileInput.dispatchEvent(new Event('change'));
        }
    });
}

// ── DRAG & DROP BETWEEN FOLDERS ──────────────────────────────
document.addEventListener('dragstart', function(e) {
    const card = e.target.closest('.item-card');
    if (!card) { e.preventDefault(); return; }
    card.classList.add('dragging');
    e.dataTransfer.setData('text/plain', JSON.stringify({ id: card.dataset.id, type: card.dataset.itemType }));
    e.dataTransfer.effectAllowed = 'move';
});
document.addEventListener('dragend', function() {
    document.querySelectorAll('.item-card.dragging').forEach(c => c.classList.remove('dragging'));
    document.querySelectorAll('.item-card.drag-over').forEach(c => c.classList.remove('drag-over'));
});
document.addEventListener('dragover', function(e) {
    const card = e.target.closest('.item-card[data-type="folder"]');
    if (card && !card.classList.contains('dragging')) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        card.classList.add('drag-over');
    }
});
document.addEventListener('dragleave', function(e) {
    const card = e.target.closest('.item-card[data-type="folder"]');
    if (card) card.classList.remove('drag-over');
});
document.addEventListener('drop', function(e) {
    const folderCard = e.target.closest('.item-card[data-type="folder"]');
    if (!folderCard) return;
    e.preventDefault();
    folderCard.classList.remove('drag-over');
    try {
        const data = JSON.parse(e.dataTransfer.getData('text/plain'));
        if (data.id && folderCard.dataset.id) {
            const form = new FormData();
            form.append('action', 'drag_move');
            form.append('csrf_token', CSRF);
            form.append('item_id', data.id);
            form.append('item_type', data.type);
            form.append('target_folder', folderCard.dataset.id);
            fetch('index.php', { method: 'POST', body: form })
                .then(r => r.json())
                .then(d => {
                    if (d.ok) {
                        showToast('<i class="fa-solid fa-check-circle"></i> Item dipindahkan!');
                        setTimeout(() => location.reload(), 900);
                    }
                });
        }
    } catch(err) { /* desktop file drop handled by global overlay */ }
});

// ── GLOBAL FILE DROP OVERLAY ──────────────────────────────────
const dropOverlay = document.getElementById('globalDropOverlay');
const autoForm    = document.getElementById('autoUploadForm');
const autoInput   = document.getElementById('autoFileInput');
let dragCounter   = 0;
if (dropOverlay && autoInput) {
    document.addEventListener('dragenter', function(e) {
        if (!e.dataTransfer.types.includes('Files')) return;
        dragCounter++;
        dropOverlay.classList.add('active');
    });
    document.addEventListener('dragleave', function() {
        dragCounter = Math.max(0, dragCounter - 1);
        if (dragCounter === 0) dropOverlay.classList.remove('active');
    });
    document.addEventListener('dragover', function(e) {
        if (e.dataTransfer.types.includes('Files')) e.preventDefault();
    });
    document.addEventListener('drop', function(e) {
        dragCounter = 0;
        dropOverlay.classList.remove('active');
        if (e.dataTransfer.files.length && autoForm) {
            autoInput.files = e.dataTransfer.files;
            autoForm.submit();
        }
    });
}

// ── COPY LINK ────────────────────────────────────────────────
function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        showToast('<i class="fa-solid fa-check-circle"></i> Link berhasil disalin!');
    });
}
function copyPortfolioLink() {
    const inp = document.getElementById('portfolioLinkInput');
    if (inp) {
        navigator.clipboard.writeText(inp.value).then(() => {
            showToast('<i class="fa-solid fa-check-circle"></i> Link portfolio disalin!');
        });
    }
}

// ── KEYBOARD SHORTCUTS ────────────────────────────────────────
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeConfirm();
        closePreview();
        closeMobilePanel();
        document.querySelectorAll('.modal').forEach(m => {
            if (m.style.display === 'flex') closeModal(m.id);
        });
    }
    const focused = document.activeElement;
    const isInput = ['INPUT','TEXTAREA','SELECT'].includes(focused.tagName);
    if (!isInput) {
        const selected = document.querySelector('#workspaceContainer .item-card.selected');
        if (e.key === 'F2' && selected) {
            e.preventDefault();
            startInlineRename(selected);
        }
        if (e.key === 'Delete' && selected) {
            e.preventDefault();
            showConfirm('Hapus Item?', 'Item akan dipindahkan ke Tong Sampah.', function() {
                const type = selected.dataset.itemType;
                if (type === 'folder') window.location = `?page=workspace&action=soft_delete_folder&id=${selected.dataset.id}`;
                else window.location = `?page=workspace&action=soft_delete_item&item_id=${selected.dataset.id}`;
            });
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
            e.preventDefault();
            const master = document.getElementById('selectAllMain');
            if (master) { master.checked = true; toggleSelectAll(master); }
        }
    }
});

// ── PROFILE TAB SWITCHING ─────────────────────────────────────
function switchTab(tabId) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    
    const panel = document.getElementById('tab-' + tabId);
    if (panel) panel.classList.add('active');
    
    document.querySelectorAll('.tab-btn').forEach(b => {
        if (b.getAttribute('onclick') && b.getAttribute('onclick').includes("'" + tabId + "'"))
            b.classList.add('active');
    });
}

// ── AJAX PROFILE SAVE WITH SWEETALERT2 ───────────────────────
function submitProfileForm(formId, label) {
    const form = document.getElementById(formId);
    if (!form) return;
    const fd = new FormData(form);
    fetch('index.php', { method: 'POST', body: fd })
        .then(r => { if (r.ok) return r.text(); throw new Error('Network error'); })
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Tersimpan!',
                text: label + ' berhasil disimpan.',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        })
        .catch(() => {
            Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan. Coba lagi.' });
        });
}

// ── DYNAMIC ACCORDION ITEM BUILDERS ──────────────────────────
function _dynField(name, label, placeholder, value) {
    return `<div class="dyn-field"><label>${label}</label><input type="text" name="${name}" value="${value||''}" placeholder="${placeholder||label}"></div>`;
}
function _dynTextarea(name, placeholder) {
    return `<div class="dyn-field full-width"><label>Deskripsi</label><textarea name="${name}" rows="3" placeholder="${placeholder}"></textarea></div>`;
}

function addEduItem() {
    const list = document.getElementById('edu-list');
    const div  = document.createElement('div');
    div.className = 'dyn-item is-open';
    div.innerHTML = `
        <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
            <h4><i class="fa-solid fa-graduation-cap"></i> Pendidikan Baru <span class="dyn-preview"> &mdash; Isi data di bawah</span></h4>
            <div class="dyn-item-header-btns">
                <button type="button" class="btn-remove-dyn" onclick="event.stopPropagation();this.closest('.dyn-item').remove()"><i class="fa-solid fa-trash"></i> Hapus</button>
                <i class="fa-solid fa-chevron-down dyn-chevron"></i>
            </div>
        </div>
        <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
            ${_dynField('edu_institusi[]','Nama Institusi','cth: Universitas Indonesia')}
            ${_dynField('edu_gelar[]','Gelar / Jenjang','cth: S1 Teknik Informatika')}
            ${_dynField('edu_bidang[]','Bidang Studi','cth: Informatika')}
            ${_dynField('edu_mulai[]','Tahun Mulai','cth: 2020')}
            ${_dynField('edu_selesai[]','Tahun Selesai','cth: 2024 / Sekarang')}
            ${_dynTextarea('edu_desc[]','Prestasi, kegiatan, atau keterangan tambahan...')}
        </div></div></div>`;
    list.appendChild(div);
    div.querySelector('input') && div.querySelector('input').focus();
    if (typeof eduCount !== 'undefined') eduCount++;
}

function addExpItem() {
    const list = document.getElementById('exp-list');
    const div  = document.createElement('div');
    div.className = 'dyn-item is-open';
    div.innerHTML = `
        <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
            <h4><i class="fa-solid fa-briefcase"></i> Pengalaman Baru <span class="dyn-preview"> &mdash; Isi data di bawah</span></h4>
            <div class="dyn-item-header-btns">
                <button type="button" class="btn-remove-dyn" onclick="event.stopPropagation();this.closest('.dyn-item').remove()"><i class="fa-solid fa-trash"></i> Hapus</button>
                <i class="fa-solid fa-chevron-down dyn-chevron"></i>
            </div>
        </div>
        <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
            ${_dynField('exp_jabatan[]','Jabatan / Posisi','cth: UI/UX Designer')}
            ${_dynField('exp_perusahaan[]','Perusahaan / Organisasi','cth: PT Alfatih Digital')}
            ${_dynField('exp_periode[]','Periode','cth: 2022 — 2024')}
            ${_dynTextarea('exp_desc[]','Uraikan tanggung jawab, pencapaian, atau kontribusi Anda...')}
        </div></div></div>`;
    list.appendChild(div);
    div.querySelector('input') && div.querySelector('input').focus();
    if (typeof expCount !== 'undefined') expCount++;
}

function addSkillItem() {
    const i    = (typeof skillCount !== 'undefined') ? skillCount++ : Date.now();
    const list = document.getElementById('skill-list');
    const div  = document.createElement('div');
    div.className = 'dyn-item is-open';
    div.innerHTML = `
        <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
            <h4><i class="fa-solid fa-star"></i> Keahlian Baru <span class="dyn-preview"> &mdash; <strong>70%</strong></span></h4>
            <div class="dyn-item-header-btns">
                <button type="button" class="btn-remove-dyn" onclick="event.stopPropagation();this.closest('.dyn-item').remove()"><i class="fa-solid fa-trash"></i> Hapus</button>
                <i class="fa-solid fa-chevron-down dyn-chevron"></i>
            </div>
        </div>
        <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
            ${_dynField('skill_nama[]','Nama Keahlian','PHP, JavaScript, Figma...')}
            ${_dynField('skill_kategori[]','Kategori','Frontend, Backend, Design...')}
            <div class="dyn-field full-width">
                <label>Level: <span id="slv_n${i}" style="font-weight:700;">70%</span></label>
                <div class="skill-slider-wrap">
                    <input type="range" name="skill_level[]" min="10" max="100" step="5" value="70"
                        oninput="document.getElementById('slv_n${i}').textContent=this.value+'%'">
                    <span style="font-size:.82rem;font-weight:700;min-width:40px;text-align:right;">70%</span>
                </div>
            </div>
        </div></div></div>`;
    list.appendChild(div);
    div.querySelector('input') && div.querySelector('input').focus();
}

function addPortoItem() {
    const list = document.getElementById('porto-list');
    const div  = document.createElement('div');
    div.className = 'dyn-item is-open';
    div.innerHTML = `
        <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
            <h4><i class="fa-solid fa-diagram-project"></i> Proyek Baru <span class="dyn-preview"> &mdash; Isi data di bawah</span></h4>
            <div class="dyn-item-header-btns">
                <button type="button" class="btn-remove-dyn" onclick="event.stopPropagation();this.closest('.dyn-item').remove()"><i class="fa-solid fa-trash"></i> Hapus</button>
                <i class="fa-solid fa-chevron-down dyn-chevron"></i>
            </div>
        </div>
        <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
            ${_dynField('porto_nama[]','Nama Proyek','cth: Website Company Profile')}
            ${_dynField('porto_url[]','URL / Link Proyek','https://...')}
            ${_dynField('porto_tech[]','Teknologi (pisah koma)','PHP, MySQL, Tailwind...')}
            ${_dynTextarea('porto_desc[]','Ceritakan proyek ini, tujuannya, dan peran Anda...')}
        </div></div></div>`;
    list.appendChild(div);
    div.querySelector('input') && div.querySelector('input').focus();
    if (typeof portoCount !== 'undefined') portoCount++;
}

// ── USER MANAGEMENT (SuperAdmin) ─────────────────────────────
function openEditUserModal(id, username, nama, role) {
    document.getElementById('eu_id').value       = id;
    document.getElementById('eu_username').value = username;
    document.getElementById('eu_nama').value     = nama;
    document.getElementById('eu_role').value     = role;
    openModal('editUserModal');
}

function confirmDeleteUser(id, username) {
    showConfirm(
        'Hapus User "' + username + '"?',
        'Akun akan dihapus permanen. File mereka tetap tersimpan di database.',
        function() {
            document.getElementById('del_uid_input').value = id;
            document.getElementById('deleteUserForm').submit();
        },
        '🗑️'
    );
}

/* ═══════════════════════════════════════════════════
   DASHBOARD MICRO-INTERACTIONS & ANIMATION v2
   Stagger reveals, accordion grid-rows, hover glow
   ═══════════════════════════════════════════════════ */

// ── Page Load: Stagger all stat blocks ──
document.querySelectorAll('.bento-card,.stat-block,.ed-card,.section-card').forEach((el, i) => {
    el.classList.add('stagger-child');
    el.style.animationDelay = (0.03 + i * 0.05) + 's';
});

// ── Accordion: Smooth grid-rows toggle ──
function toggleAccordion(item) {
    const wasOpen = item.classList.contains('is-open');
    const list = item.closest('.dyn-list');
    if (list) {
        list.querySelectorAll('.dyn-item.is-open').forEach(open => {
            open.classList.remove('is-open');
        });
    }
    if (!wasOpen) item.classList.add('is-open');
}

// ── Item Card: hover glow border effect ──
document.querySelectorAll('.item-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
        card.style.transition = 'background .15s,box-shadow .2s';
    });
});

// ── Sidebar nav items: Ripple on click ──
document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect   = this.getBoundingClientRect();
        const size   = Math.max(rect.width, rect.height);
        Object.assign(ripple.style, {
            position: 'absolute',
            width:    size + 'px',
            height:   size + 'px',
            left:     (e.clientX - rect.left - size/2) + 'px',
            top:      (e.clientY - rect.top  - size/2) + 'px',
            background:   'rgba(255,255,255,0.15)',
            borderRadius: '50%',
            transform:    'scale(0)',
            animation:    'ripple-expand .5s ease forwards',
            pointerEvents:'none',
            zIndex: 0,
        });
        this.style.position = 'relative';
        this.style.overflow = 'hidden';
        this.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    });
});
const rippleStyle = document.createElement('style');
rippleStyle.textContent = `@keyframes ripple-expand{to{transform:scale(2.5);opacity:0;}}`;
document.head.appendChild(rippleStyle);

// ── Smooth Scroll Reveal for content area ──
const dashReveal = new IntersectionObserver(entries => {
    entries.forEach((e) => {
        if (e.isIntersecting) {
            e.target.style.opacity    = '1';
            e.target.style.transform  = 'translateY(0)';
            dashReveal.unobserve(e.target);
        }
    });
}, { threshold: 0.04, rootMargin: '0px 0px -32px 0px' });
document.querySelectorAll('.user-table tr,.activity-table tr,.profile-check-item').forEach((el, i) => {
    el.style.cssText += ';opacity:0;transform:translateY(8px);transition:opacity .4s ease ' + (i * 0.04) + 's,transform .4s ease ' + (i * 0.04) + 's';
    dashReveal.observe(el);
});

// ── Storage bar animated fill on load ──
document.querySelectorAll('.storage-bar-fill').forEach(bar => {
    const target = bar.style.width;
    bar.style.width = '0';
    setTimeout(() => {
        bar.style.transition = 'width .9s cubic-bezier(.16,1,.3,1)';
        bar.style.width = target;
    }, 400);
});

// ── Avatar hover: remove grayscale ──
document.querySelectorAll('.user-avatar-sm').forEach(img => {
    img.addEventListener('mouseenter', () => { img.style.filter = 'none'; img.style.transform = 'scale(1.05)'; });
    img.addEventListener('mouseleave', () => { img.style.filter = '';    img.style.transform = '';          });
});

// ── PWA SERVICE WORKER ────────────────────────────────────────
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js').catch(() => {});
}

// ── AUTO SHOW TOAST for server-side alert ─────────────────────
// Fungsi ini dipanggil dari view setelah PHP inject alert_msg:
// if (ALERT_MSG) { setTimeout(() => showToast(...), 300); }