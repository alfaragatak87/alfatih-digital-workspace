// ── SIDEBAR INTERACTION ──────────────────────────────────────
let sidebarOpen = false;
function toggleSidebar() {
    const sb = document.getElementById('sidebar');
    const ov = document.getElementById('sidebarOverlay');
    if (!sb || !ov) return;
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

// ── PROFILE DROPDOWN MENU ────────────────────────────────────
function toggleProfileMenu() {
    const pm = document.getElementById('profileMenu');
    if (pm) pm.classList.toggle('show');
}

function closeAllMenus() {
    const pm = document.getElementById('profileMenu');
    if (pm) pm.classList.remove('show');
    document.querySelectorAll('.action-dropdown.show').forEach(d => d.classList.remove('show'));
}

document.addEventListener('click', function (e) {
    if (!e.target.closest('.profile-container')) {
        const pm = document.getElementById('profileMenu');
        if (pm) pm.classList.remove('show');
    }
    if (!e.target.closest('.action-wrapper') && !e.target.closest('.btn-dots')) {
        document.querySelectorAll('.action-dropdown.show').forEach(d => d.classList.remove('show'));
    }
    if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-content').forEach(d => d.style.display = '');
    }
});

// ── WINDOW POPUP MODALS ──────────────────────────────────────
function openModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.style.display = 'none';
    document.body.style.overflow = '';
}

document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('click', function (e) {
        if (e.target === m) closeModal(m.id);
    });
});

function openEditModal(id, nama, desc, icon, warna) {
    const fid = document.getElementById('edit_folder_id');
    const fnm = document.getElementById('edit_folder_nama');
    const fds = document.getElementById('edit_folder_desc');
    const fic = document.getElementById('edit_folder_icon');
    const fwr = document.getElementById('edit_folder_warna');

    if (fid) fid.value = id;
    if (fnm) fnm.value = nama;
    if (fds) fds.value = desc;
    if (fic) fic.value = icon || 'fa-folder';
    if (fwr) fwr.value = warna || '#0a0a0a';
    openModal('editFolderModal');
}

function openMoveModal(type, id, name) {
    const mt = document.getElementById('move_type_input');
    const mi = document.getElementById('move_id_input');
    const mn = document.getElementById('move_item_name');

    if (mt) mt.value = type;
    if (mi) mi.value = id;
    if (mn) mn.textContent = '📦 ' + name;
    openModal('moveModal');
}

// ── RIGHT INFORMATION SIDEBAR ─────────────────────────────────
let isSidebarOpen = false;
function toggleRightSidebar() {
    const rs = document.getElementById('rightSidebar');
    if (!rs) return;
    isSidebarOpen = !isSidebarOpen;
    if (isSidebarOpen) rs.classList.add('active'); else rs.classList.remove('active');
}

// ── REALTIME SESSION TOASTS ───────────────────────────────────
function showToast(msg) {
    const t = document.getElementById('toast');
    if (!t) return;
    t.innerHTML = msg;
    t.classList.remove('show');
    void t.offsetWidth; // Trigger reflow
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3700);
}

// ── WORKSPACE ITEM SELECTION ──────────────────────────────────
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

    const { type, name, icon: iconClass, owner, date, size, desc, url: fileUrl, tags, share: shareLink, preview: pt } = el.dataset;
    const ah = el.querySelector('.hidden-action-html');

    const rst = document.getElementById('rs_title');
    if (rst) rst.innerHTML = type === 'folder' ? '<i class="fa-solid fa-folder"></i> Detail Folder' : '<i class="fa-solid fa-file"></i> Detail File';

    const previewIcon = document.getElementById('rs_icon');
    const isImage = fileUrl && /\.(png|jpe?g|gif|webp|svg)$/i.test(name);
    if (previewIcon) {
        if (isImage) {
            previewIcon.innerHTML = `<img src="${fileUrl}" style="max-width:100%;max-height:180px;object-fit:contain;" onerror="this.outerHTML='<i class=\\'${iconClass}\\' style=\\'font-size:3rem;\\'></i>'">`;
            previewIcon.style.cssText = 'padding:8px;background:#f5f5f5;border:1px solid var(--border);text-align:center;margin-bottom:16px;min-height:120px;display:flex;align-items:center;justify-content:center;overflow:hidden;';
        } else {
            previewIcon.innerHTML = `<i class="${iconClass}" style="font-size:3rem;"></i>`;
            previewIcon.style.cssText = `padding:28px;background:#f5f5f5;border:1px solid var(--border);text-align:center;margin-bottom:16px;display:flex;align-items:center;justify-content:center;`;
        }
    }

    if (document.getElementById('rs_name')) document.getElementById('rs_name').innerText = name;
    if (document.getElementById('rs_type')) document.getElementById('rs_type').innerText = type === 'folder' ? 'Folder' : (type === 'link' ? 'Tautan Website' : 'File Dokumen');
    if (document.getElementById('rs_owner')) document.getElementById('rs_owner').innerText = owner;
    if (document.getElementById('rs_date')) document.getElementById('rs_date').innerText = date;
    if (document.getElementById('rs_size')) document.getElementById('rs_size').innerText = size;
    if (document.getElementById('rs_desc')) document.getElementById('rs_desc').innerText = (desc && desc !== '-') ? desc : 'Tidak ada catatan.';
    if (document.getElementById('rs_tags')) document.getElementById('rs_tags').innerText = (tags && tags !== '') ? tags : 'Tidak ada label';

    const actCont = document.getElementById('rs_actions');
    if (actCont) {
        if (ah) { actCont.innerHTML = ah.innerHTML; actCont.style.display = 'flex'; } else { actCont.innerHTML = ''; actCont.style.display = 'none'; }
    }

    const qrCont = document.getElementById('rs_qr_container');
    const qrImg = document.getElementById('rs_qr_img');
    if (qrCont && qrImg) {
        if (shareLink && shareLink !== '') { qrCont.style.display = 'block'; qrImg.src = 'https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=' + encodeURIComponent(shareLink); }
        else { qrCont.style.display = 'none'; }
    }
}

// ── RESPONSIVE MOBILE PANELS ──────────────────────────────────
function openMobilePanel(el) {
    const o = document.getElementById('mobilePanelOverlay');
    const p = document.getElementById('mobileDetailPanel');
    const c = document.getElementById('mobileDetailContent');
    if (!o || !p || !c) return;
    const ah = el.querySelector('.hidden-action-html');
    const html_a = ah ? ah.innerHTML : '';
    c.innerHTML = `<div style="text-align:center;margin-bottom:16px;"><div style="font-size:2.5rem;margin-bottom:10px;"><i class="${el.dataset.icon}"></i></div><h3 style="margin:0;font-size:1.05rem;font-family:'Playfair Display',serif;word-wrap:break-word;">${el.dataset.name}</h3></div><div style="display:flex;flex-direction:column;gap:0;border:1px solid var(--border-dark);">${html_a}</div>`;
    o.classList.add('active'); p.classList.add('active');
}

function closeMobilePanel() {
    const o = document.getElementById('mobilePanelOverlay');
    const p = document.getElementById('mobileDetailPanel');
    if (o) o.classList.remove('active'); if (p) p.classList.remove('active');
}

// ── BULK MANAGEMENT & SELECTION ───────────────────────────────
function handleCheckbox(event, cb) {
    event.stopPropagation();
    const card = cb.closest('.item-card');
    if (cb.checked) card.classList.add('selected'); else card.classList.remove('selected');
    updateBulkToolbar();
}

function toggleSelectAll(master) {
    document.querySelectorAll('#workspaceContainer .item-checkbox:not(#selectAllMain):not(#selectAllHeader)').forEach(cb => {
        cb.checked = master.checked;
        const card = cb.closest('.item-card');
        if (card) { if (master.checked) card.classList.add('selected'); else card.classList.remove('selected'); }
    });
    updateBulkToolbar();
}

function getSelectedItems() {
    const items = [];
    document.querySelectorAll('#workspaceContainer .item-card.selected').forEach(c => { items.push({ id: c.dataset.id, type: c.dataset.itemType }); });
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
    const tb = document.getElementById('bulkToolbar'); if (tb) tb.classList.remove('active');
}

function bulkDelete() {
    const items = getSelectedItems(); if (items.length === 0) return;
    showConfirm('Hapus ' + items.length + ' Item?', 'Item dipindahkan ke Tong Sampah. Bisa dipulihkan nanti.', function () {
        document.getElementById('bulkDeleteIds').value = JSON.stringify(items.map(i => i.id));
        document.getElementById('bulkDeleteTypes').value = JSON.stringify(items.map(i => i.type));
        document.getElementById('bulkDeleteForm').submit();
    });
}

function bulkMove() { const items = getSelectedItems(); if (items.length === 0) return; openModal('bulkMoveModal'); }

function executeBulkMove() {
    const items = getSelectedItems();
    const target = document.getElementById('bulkMoveTargetSelect').value;
    document.getElementById('bulkMoveIds').value = JSON.stringify(items.map(i => i.id));
    document.getElementById('bulkMoveTypes').value = JSON.stringify(items.map(i => i.type));
    document.getElementById('bulkMoveTarget').value = target;
    document.getElementById('bulkMoveForm').submit();
}

// ── CUSTOM LIGHTWEIGHT DIALOG OVERLAYS ─────────────────────────
let confirmCallback = null;
function showConfirm(title, message, callback, icon = '⚠️') {
    if (document.getElementById('confirmTitle')) document.getElementById('confirmTitle').textContent = title;
    if (document.getElementById('confirmMessage')) document.getElementById('confirmMessage').textContent = message;
    if (document.getElementById('confirmIcon')) document.getElementById('confirmIcon').textContent = icon;
    if (document.getElementById('confirmOverlay')) document.getElementById('confirmOverlay').classList.add('active');
    confirmCallback = callback;
}

function closeConfirm() {
    if (document.getElementById('confirmOverlay')) document.getElementById('confirmOverlay').classList.remove('active');
    confirmCallback = null;
}

function executeConfirmAction() { if (confirmCallback) confirmCallback(); closeConfirm(); }

function toggleActionMenu(event, id) {
    event.stopPropagation();
    const dd = document.getElementById(id);
    if (!dd) return;
    const isOpen = dd.classList.contains('show');
    document.querySelectorAll('.action-dropdown.show').forEach(d => d.classList.remove('show'));
    if (!isOpen) dd.classList.add('show');
}

// ── INLINE RENAME HANDLERS ────────────────────────────────────
function startInlineRename(card) {
    if (!card) return;
    const nameEl = card.querySelector('.item-name');
    if (!nameEl || nameEl.querySelector('.rename-inline')) return;
    const oldName = nameEl.textContent.trim();
    const input = document.createElement('input');
    input.type = 'text'; input.value = oldName; input.className = 'rename-inline';
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); submitRename(card, input.value); }
        if (e.key === 'Escape') { e.preventDefault(); nameEl.textContent = oldName; }
    });
    input.addEventListener('blur', function () { if (nameEl.contains(input)) nameEl.textContent = oldName; });
    input.addEventListener('click', e => e.stopPropagation());
    nameEl.textContent = ''; nameEl.appendChild(input);
    input.focus(); input.select();
}

function submitRename(card, newName) {
    if (!newName.trim()) return;
    document.getElementById('renameItemId').value = card.dataset.id;
    document.getElementById('renameItemType').value = card.dataset.itemType;
    document.getElementById('renameNewName').value = newName.trim();
    document.getElementById('renameForm').submit();
}

// ── INTERACTIVE IMAGE / MEDIA PREVIEWS ─────────────────────────
function openPreview(filename, fileUrl, previewType, fileId) {
    const overlay = document.getElementById('previewOverlay');
    const body = document.getElementById('previewBody');
    const fnEl = document.getElementById('previewFileName');
    const dlBtn = document.getElementById('previewDownloadBtn');
    const opBtn = document.getElementById('previewOpenBtn');
    if (!overlay || !body) return;

    if (fnEl) fnEl.textContent = filename;
    if (dlBtn) dlBtn.href = '?action=download_file&file_id=' + fileId;
    if (opBtn) opBtn.href = '?action=view_file&file_id=' + fileId;
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
    if (document.getElementById('previewOverlay')) document.getElementById('previewOverlay').classList.remove('active');
    if (document.getElementById('previewBody')) document.getElementById('previewBody').innerHTML = '';
}

// ── FLOATING ACTION BUTTON (MOBILE FAB) ──────────────────────────
function toggleFab() {
    const m = document.getElementById('fabMenu'), b = document.getElementById('fabBtn');
    if (!m || !b) return;
    m.classList.toggle('active');
    b.innerHTML = m.classList.contains('active') ? '<i class="fa-solid fa-xmark"></i>' : '<i class="fa-solid fa-plus"></i>';
}

function switchType(type) {
    if (!document.getElementById('jenis_input')) return;
    document.getElementById('jenis_input').value = type;
    const ff = document.getElementById('form_file'), fl = document.getElementById('form_link');
    const tf = document.getElementById('tabFile'), tl = document.getElementById('tabLink');
    if (type === 'file') {
        if (ff) ff.style.display = 'block'; if (fl) fl.style.display = 'none';
        if (tf) { tf.style.background = 'var(--text-main)'; tf.style.color = '#fff'; }
        if (tl) { tl.style.background = '#f5f5f5'; tl.style.color = 'var(--text-main)'; }
    } else {
        if (ff) ff.style.display = 'none'; if (fl) fl.style.display = 'block';
        if (tl) { tl.style.background = 'var(--text-main)'; tl.style.color = '#fff'; }
        if (tf) { tf.style.background = '#f5f5f5'; tf.style.color = 'var(--text-main)'; }
    }
}

// ── KEYBOARD SHORTCUTS BINDING ──────────────────────────────────
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeConfirm(); closePreview(); closeMobilePanel();
        document.querySelectorAll('.modal').forEach(m => { if (m.style.display === 'flex') closeModal(m.id); });
    }
    const focused = document.activeElement;
    const isInput = ['INPUT', 'TEXTAREA', 'SELECT'].includes(focused.tagName);
    if (!isInput) {
        const selected = document.querySelector('#workspaceContainer .item-card.selected');
        if (e.key === 'F2' && selected) { e.preventDefault(); startInlineRename(selected); }
        if (e.key === 'Delete' && selected) {
            e.preventDefault();
            showConfirm('Hapus Item?', 'Item akan dipindahkan ke Tong Sampah.', function () {
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

// ── PROFILE CV BUILDER TAB ROUTER ───────────────────────────────
function switchTab(tabId) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    const panel = document.getElementById('tab-' + tabId);
    if (panel) panel.classList.add('active');
    document.querySelectorAll('.tab-btn').forEach(b => {
        if (b.getAttribute('onclick') && b.getAttribute('onclick').includes("'" + tabId + "'")) b.classList.add('active');
    });
}

function toggleAccordion(item) {
    const wasOpen = item.classList.contains('is-open');
    const list = item.closest('.dyn-list');
    if (list) {
        list.querySelectorAll('.dyn-item.is-open').forEach(open => open.classList.remove('is-open'));
    }
    if (!wasOpen) item.classList.add('is-open');
}

// ── PREMIUM MICRO-INTERACTIONS ANIMATIONS v2 ───────────────────
document.querySelectorAll('.bento-card, .stat-block, .ed-card, .section-card').forEach((el, i) => {
    el.classList.add('stagger-child');
    el.style.animationDelay = (0.03 + i * 0.05) + 's';
});

const dashReveal = new IntersectionObserver(entries => {
    entries.forEach((e) => {
        if (e.isIntersecting) {
            e.target.style.opacity = '1';
            e.target.style.transform = 'translateY(0)';
            dashReveal.unobserve(e.target);
        }
    });
}, { threshold: 0.04, rootMargin: '0px 0px -32px 0px' });

document.querySelectorAll('.user-table tr, .activity-table tr, .profile-check-item').forEach((el, i) => {
    el.style.cssText += ';opacity:0;transform:translateY(8px);transition:opacity .4s ease ' + (i * 0.04) + 's,transform .4s ease ' + (i * 0.04) + 's';
    dashReveal.observe(el);
});

document.querySelectorAll('.storage-bar-fill').forEach(bar => {
    const target = bar.style.width;
    bar.style.width = '0';
    setTimeout(() => {
        bar.style.transition = 'width .9s cubic-bezier(.16,1,.3,1)';
        bar.style.width = target;
    }, 400);
});