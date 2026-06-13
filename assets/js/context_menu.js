/**
 * ================================================================
 * context_menu.js — Desktop Context Menu + Mobile Bottom Sheet
 * Alfatih Digital Workspace v2
 * ================================================================
 *
 * CARA PAKAI:
 *   1. Load file ini SETELAH app.js di index.php:
 *      <script src="assets/js/context_menu.js"></script>
 *
 *   2. PHP wajib inject CSRF & toggle_star endpoint sebelum load:
 *      <script>
 *        const CSRF = '<?= h($csrf_token) ?>';
 *        const CURRENT_USERNAME = '<?= h($username) ?>';
 *      </script>
 *
 * ── Cara kerja ──────────────────────────────────────────────
 *   Desktop : klik kanan pada .item-card → context menu muncul
 *             di posisi kursor (di-flip jika dekat tepi layar).
 *   Mobile  : tap-tahan (long press 500ms) pada .item-card
 *             ATAU tap tombol ⋮ → Bottom Sheet meluncur dari bawah.
 *   Keduanya membangun item menu secara dinamis dari dataset
 *   item-card yang bersangkutan.
 * ================================================================
 */

/* ─────────────────────────────────────────────────────────────
   1. INJECT CSS (dimasukkan satu kali ke <head>)
   ───────────────────────────────────────────────────────────── */
(function injectCSS() {
    if (document.getElementById('cm-styles')) return;
    const style = document.createElement('style');
    style.id = 'cm-styles';
    style.textContent = `

/* ════════════════════════════════════════════════════════
   DESKTOP CONTEXT MENU
   ════════════════════════════════════════════════════════ */
#ctxMenu {
    position: fixed;
    z-index: 1500;
    min-width: 210px;
    max-width: 260px;
    background: rgba(255,255,255,0.88);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid rgba(0,0,0,0.09);
    border-radius: 14px;
    box-shadow:
        0 8px 32px rgba(0,0,0,0.13),
        0 2px 8px rgba(0,0,0,0.07),
        0 0 0 0.5px rgba(0,0,0,0.06) inset;
    padding: 5px;
    transform-origin: top left;
    animation: ctxIn 0.18s cubic-bezier(.16,1,.3,1) both;
    user-select: none;
}
@keyframes ctxIn {
    from { opacity:0; transform:scale(.92) translateY(-4px); }
    to   { opacity:1; transform:scale(1) translateY(0); }
}

/* flip down: when near bottom */
#ctxMenu.flip-y { transform-origin: bottom left; animation: ctxInFlip 0.18s cubic-bezier(.16,1,.3,1) both; }
@keyframes ctxInFlip {
    from { opacity:0; transform:scale(.92) translateY(4px); }
    to   { opacity:1; transform:scale(1) translateY(0); }
}
/* flip right: when near right edge */
#ctxMenu.flip-x { transform-origin: top right; }

/* ── Header (item name preview) ── */
.ctx-header {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px 8px;
    border-bottom: 1px solid rgba(0,0,0,0.07);
    margin-bottom: 3px;
}
.ctx-header-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: #f5f5f5;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; flex-shrink: 0;
}
.ctx-header-name {
    font-size: .82rem; font-weight: 700;
    color: #0a0a0a;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    max-width: 160px;
}
.ctx-header-type {
    font-size: .65rem; color: #9ca3af;
    text-transform: uppercase; letter-spacing: .5px;
}

/* ── Menu items ── */
.ctx-item {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 12px;
    border-radius: 10px;
    font-size: .83rem; font-weight: 500;
    color: #1a1a1a;
    cursor: pointer;
    transition: background .12s, color .12s;
    position: relative;
    white-space: nowrap;
}
.ctx-item:hover { background: rgba(0,0,0,.05); }
.ctx-item:active { background: rgba(0,0,0,.09); transform: scale(.98); }
.ctx-item .ctx-icon {
    width: 20px; height: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: .82rem; color: #6b7280; flex-shrink: 0;
    transition: color .12s;
}
.ctx-item:hover .ctx-icon { color: #374151; }

/* Starred state */
.ctx-item.ctx-starred .ctx-icon { color: #f59e0b !important; }
.ctx-item.ctx-starred .ctx-label { color: #b45309 !important; }

/* Danger state */
.ctx-item.ctx-danger { color: #dc2626; }
.ctx-item.ctx-danger .ctx-icon { color: #dc2626; }
.ctx-item.ctx-danger:hover { background: #fef2f2; }

/* Keyboard shortcut hint */
.ctx-shortcut {
    margin-left: auto;
    font-size: .65rem; color: #d1d5db; font-weight: 600;
    background: #f9f9f9; border: 1px solid #e5e5e5;
    border-radius: 4px; padding: 1px 5px;
}

/* Separator */
.ctx-sep {
    height: 1px; background: rgba(0,0,0,.07);
    margin: 4px 8px;
}

/* Disabled item */
.ctx-item.ctx-disabled {
    opacity: .4; pointer-events: none;
}

/* ════════════════════════════════════════════════════════
   MOBILE BOTTOM SHEET
   ════════════════════════════════════════════════════════ */
#bsOverlay {
    position: fixed; inset: 0; z-index: 1490;
    background: rgba(0,0,0,.45);
    backdrop-filter: blur(4px);
    opacity: 0; visibility: hidden;
    transition: opacity .28s ease, visibility .28s;
}
#bsOverlay.active { opacity: 1; visibility: visible; }

#bsSheet {
    position: fixed; bottom: 0; left: 0; right: 0;
    z-index: 1500;
    background: #fff;
    border-radius: 22px 22px 0 0;
    box-shadow: 0 -8px 40px rgba(0,0,0,.16);
    transform: translateY(110%);
    transition: transform .38s cubic-bezier(.16,1,.3,1);
    max-height: 80dvh;
    overflow-y: auto;
    padding-bottom: max(20px, env(safe-area-inset-bottom));
}
#bsSheet.active { transform: translateY(0); }

.bs-handle {
    width: 36px; height: 4px; border-radius: 4px;
    background: #e0e0e0; margin: 12px auto 0;
}
.bs-header {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 20px 12px;
    border-bottom: 1px solid #f0f0f0;
}
.bs-header-icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: #f5f5f5;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; flex-shrink: 0;
}
.bs-header-info { flex: 1; min-width: 0; }
.bs-header-name {
    font-size: .95rem; font-weight: 700;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.bs-header-type {
    font-size: .72rem; color: #9ca3af;
    text-transform: uppercase; letter-spacing: .5px;
}

/* ── Bottom Sheet items ── */
.bs-items { padding: 8px 0 4px; }
.bs-item {
    display: flex; align-items: center; gap: 14px;
    padding: 13px 20px;
    font-size: .9rem; font-weight: 500; color: #1a1a1a;
    cursor: pointer; transition: background .12s;
    border: none; background: none; width: 100%;
    text-align: left; font-family: 'Inter', sans-serif;
}
.bs-item:hover, .bs-item:active { background: #f9f9f9; }
.bs-item .bs-icon {
    width: 38px; height: 38px; border-radius: 10px;
    background: #f5f5f5;
    display: flex; align-items: center; justify-content: center;
    font-size: .95rem; color: #374151; flex-shrink: 0;
    transition: background .15s, color .15s;
}
.bs-item:hover .bs-icon { background: #ebebeb; }
.bs-item.bs-starred .bs-icon { background: #fef3c7; color: #f59e0b; }
.bs-item.bs-danger .bs-icon  { background: #fef2f2; color: #dc2626; }
.bs-item.bs-danger { color: #dc2626; }

.bs-sep { height: 1px; background: #f0f0f0; margin: 4px 20px; }

/* Long-press ripple feedback */
.item-card.cm-pressing {
    background: #f0f0f0 !important;
    transition: background .1s !important;
}

`;
    document.head.appendChild(style);
})();


/* ─────────────────────────────────────────────────────────────
   2. DOM ELEMENTS (context menu + bottom sheet)
   ───────────────────────────────────────────────────────────── */
function buildDOM() {
    // Desktop context menu
    if (!document.getElementById('ctxMenu')) {
        const m = document.createElement('div');
        m.id = 'ctxMenu';
        m.style.display = 'none';
        document.body.appendChild(m);
    }
    // Mobile bottom sheet
    if (!document.getElementById('bsOverlay')) {
        const ov = document.createElement('div');
        ov.id = 'bsOverlay';
        ov.onclick = closeBs;
        document.body.appendChild(ov);

        const sh = document.createElement('div');
        sh.id = 'bsSheet';
        sh.innerHTML = `<div class="bs-handle"></div><div id="bsContent"></div>`;
        document.body.appendChild(sh);
    }
}
buildDOM();


/* ─────────────────────────────────────────────────────────────
   3. STATE
   ───────────────────────────────────────────────────────────── */
let _ctxTarget = null; // item-card yang sedang aktif


/* ─────────────────────────────────────────────────────────────
   4. BUILD MENU ITEMS — sesuai tipe item & status
   ───────────────────────────────────────────────────────────── */
function buildMenuItems(dataset) {
    const t       = dataset.type;           // folder | file | link
    const id      = dataset.id;
    const name    = dataset.name;
    const isLink  = (t === 'link');
    const isFolder= (t === 'folder');
    const isTrash = dataset.isTrash === '1';
    const starred = dataset.starred === '1';
    const preview = dataset.preview;       // image|pdf|video|audio|none
    const fileUrl = dataset.url;
    const shareUrl= dataset.share
        ? `${location.origin}${location.pathname}?share=${dataset.share}`
        : null;
    const folderId= dataset.folderId;
    const linkUrl = dataset.linkUrl;

    // Menu untuk item di tong sampah
    if (isTrash) {
        return [
            { icon:'fa-clock-rotate-left', label:'Pulihkan', action: () => { location.href = isFolder ? `?page=workspace&action=restore&type=folder&id=${id}` : `?page=workspace&action=restore&type=file&id=${id}`; } },
            { sep: true },
            { icon:'fa-fire', label:'Hapus Permanen', cls:'ctx-danger', action: () => {
                showConfirm(`Hapus "${name}" Selamanya?`, 'File tidak bisa dipulihkan setelah ini.', () => {
                    location.href = isFolder ? `?page=workspace&action=hard_delete&type=folder&id=${id}` : `?page=workspace&action=hard_delete&type=file&id=${id}`;
                }, '🔥');
            }},
        ];
    }

    const items = [];

    // ── Buka / Pratinjau
    if (isFolder) {
        items.push({ icon:'fa-folder-open', label:'Buka', shortcut:'↵', action: () => { location.href = `?page=workspace&folder_id=${id}`; } });
        items.push({ icon:'fa-file-zipper', label:'Download ZIP', action: () => { location.href = `?action=download_zip&folder_id=${id}`; } });
    } else if (isLink) {
        items.push({ icon:'fa-arrow-up-right-from-square', label:'Kunjungi URL', action: () => { window.open(linkUrl, '_blank'); } });
    } else {
        if (preview !== 'none') {
            items.push({ icon:'fa-eye', label:'Pratinjau', action: () => { openPreview(name, fileUrl, preview, id); } });
        }
        items.push({ icon:'fa-download', label:'Download', shortcut:'D', action: () => { location.href = `?action=download_file&file_id=${id}`; } });
        items.push({ icon:'fa-print', label:'Cetak', action: () => { window.open(`?action=print_file&file_id=${id}`, '_blank'); } });
    }

    items.push({ sep: true });

    // ── Bagikan Link
    if (!isFolder && shareUrl) {
        items.push({ icon:'fa-link', label:'Salin Link Berbagi', action: () => { cmCopyShare(shareUrl); } });
    } else if (!isFolder && !isLink) {
        items.push({ icon:'fa-share-from-square', label:'Buat Link Berbagi', action: () => { location.href = `?action=create_share&file_id=${id}`; } });
    }

    // ── Bintangi
    items.push({
        icon: starred ? 'fa-star' : 'fa-star',
        label: starred ? 'Hapus Bintang' : 'Bintangi',
        cls: starred ? 'ctx-starred' : '',
        shortcut: 'S',
        action: () => { cmToggleStar(id, t, _ctxTarget); },
    });

    items.push({ sep: true });

    // ── Edit / Rename
    if (isFolder) {
        items.push({ icon:'fa-pen', label:'Edit Folder', action: () => {
            openEditModal(id, name, dataset.desc, (dataset.icon.replace('fa-solid ','') || 'fa-folder'), dataset.color || '#0a0a0a');
        }});
    }
    items.push({ icon:'fa-i-cursor', label:'Ganti Nama', shortcut:'F2', action: () => { startInlineRename(_ctxTarget); } });
    items.push({ icon:'fa-folder-tree', label:'Pindahkan ke...', action: () => { openMoveModal(isFolder ? 'folder' : 'file', id, name); } });

    items.push({ sep: true });

    // ── Hapus
    items.push({ icon:'fa-trash', label:'Pindah ke Sampah', cls:'ctx-danger', shortcut:'Del', action: () => {
        showConfirm(`Hapus "${name}"?`, 'Item akan dipindahkan ke Tong Sampah.', () => {
            location.href = isFolder
                ? `?page=workspace&action=soft_delete_folder&id=${id}`
                : `?page=workspace&action=soft_delete_item&item_id=${id}`;
        }, '🗑️');
    }});

    return items;
}


/* ─────────────────────────────────────────────────────────────
   5. DESKTOP CONTEXT MENU — render & position
   ───────────────────────────────────────────────────────────── */
function showContextMenu(e, card) {
    e.preventDefault();
    e.stopPropagation();
    _ctxTarget = card;

    const isMobile = window.innerWidth <= 768;
    if (isMobile) { showBs(card); return; }

    const menu  = document.getElementById('ctxMenu');
    const items = buildMenuItems(card.dataset);
    const ds    = card.dataset;

    // Header
    const typeLabel = ds.type === 'folder' ? 'Folder' : (ds.type === 'link' ? 'Tautan' : 'File');
    let html = `
        <div class="ctx-header">
            <div class="ctx-header-icon"><i class="${ds.icon}" style="color:${ds.color}"></i></div>
            <div>
                <div class="ctx-header-name">${ds.name}</div>
                <div class="ctx-header-type">${typeLabel} · ${ds.size}</div>
            </div>
        </div>`;

    // Items
    items.forEach(item => {
        if (item.sep) {
            html += `<div class="ctx-sep"></div>`;
        } else {
            html += `<div class="ctx-item ${item.cls || ''}" data-cm-action>
                <div class="ctx-icon"><i class="fa-solid ${item.icon}"></i></div>
                <span class="ctx-label">${item.label}</span>
                ${item.shortcut ? `<span class="ctx-shortcut">${item.shortcut}</span>` : ''}
            </div>`;
        }
    });

    menu.innerHTML = html;
    menu.style.display = 'block';
    menu.classList.remove('flip-x', 'flip-y');

    // Bind click handlers
    const actionEls = menu.querySelectorAll('[data-cm-action]');
    actionEls.forEach((el, i) => {
        // skip separators
        const actionItems = items.filter(x => !x.sep);
        if (actionItems[i]) {
            el.addEventListener('click', () => { closeCtx(); actionItems[i].action(); });
        }
    });

    // Position: default top-left at cursor, flip if overflowing
    menu.style.top  = '-9999px';
    menu.style.left = '-9999px';

    const vw = window.innerWidth;
    const vh = window.innerHeight;
    let x = e.clientX + 4;
    let y = e.clientY + 4;

    // Flip horizontal
    if (x + menu.offsetWidth > vw - 16) {
        x = e.clientX - menu.offsetWidth - 4;
        menu.classList.add('flip-x');
    }
    // Flip vertical
    if (y + menu.offsetHeight > vh - 16) {
        y = e.clientY - menu.offsetHeight - 4;
        menu.classList.add('flip-y');
    }

    menu.style.top  = y + 'px';
    menu.style.left = x + 'px';
}

function closeCtx() {
    const menu = document.getElementById('ctxMenu');
    if (menu) {
        menu.style.animation = 'none';
        menu.style.opacity   = '0';
        menu.style.transform = 'scale(.95)';
        setTimeout(() => {
            menu.style.display = 'none';
            menu.style.opacity = '';
            menu.style.transform = '';
            menu.style.animation = '';
        }, 120);
    }
}

// Close on click outside / scroll / Escape
document.addEventListener('click',    e => { if (!e.target.closest('#ctxMenu')) closeCtx(); });
document.addEventListener('keydown',  e => { if (e.key === 'Escape') { closeCtx(); closeBs(); } });
document.addEventListener('scroll',   closeCtx, true);


/* ─────────────────────────────────────────────────────────────
   6. MOBILE BOTTOM SHEET
   ───────────────────────────────────────────────────────────── */
function showBs(card) {
    _ctxTarget = card;
    const ds    = card.dataset;
    const items = buildMenuItems(ds);

    const typeLabel = ds.type === 'folder' ? 'Folder' : (ds.type === 'link' ? 'Tautan' : 'File');

    let html = `
        <div class="bs-header">
            <div class="bs-header-icon"><i class="${ds.icon}" style="color:${ds.color};font-size:1.4rem;"></i></div>
            <div class="bs-header-info">
                <div class="bs-header-name">${ds.name}</div>
                <div class="bs-header-type">${typeLabel} · ${ds.size}</div>
            </div>
        </div>
        <div class="bs-items">`;

    items.forEach(item => {
        if (item.sep) {
            html += `<div class="bs-sep"></div>`;
        } else {
            html += `<button class="bs-item ${
                item.cls?.includes('danger') ? 'bs-danger' : ''} ${
                item.cls?.includes('ctx-starred') ? 'bs-starred' : ''}" data-bs-action>
                <div class="bs-icon"><i class="fa-solid ${item.icon}"></i></div>
                <span>${item.label}</span>
            </button>`;
        }
    });

    html += `</div>`;

    document.getElementById('bsContent').innerHTML = html;

    // Bind handlers
    const actionEls = document.querySelectorAll('[data-bs-action]');
    const actionItems = items.filter(x => !x.sep);
    actionEls.forEach((el, i) => {
        if (actionItems[i]) {
            el.addEventListener('click', () => { closeBs(); actionItems[i].action(); });
        }
    });

    document.getElementById('bsOverlay').classList.add('active');
    // Slight delay so transition triggers after display
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            document.getElementById('bsSheet').classList.add('active');
        });
    });
}

function closeBs() {
    const sh = document.getElementById('bsSheet');
    const ov = document.getElementById('bsOverlay');
    if (sh) sh.classList.remove('active');
    if (ov) ov.classList.remove('active');
}

// Swipe down to close
(function setupSwipe() {
    let startY = 0;
    document.addEventListener('touchstart', e => {
        if (e.target.closest('#bsSheet')) startY = e.touches[0].clientY;
    }, { passive: true });
    document.addEventListener('touchend', e => {
        if (!e.target.closest('#bsSheet')) return;
        const diff = e.changedTouches[0].clientY - startY;
        if (diff > 80) closeBs();
    }, { passive: true });
})();


/* ─────────────────────────────────────────────────────────────
   7. LONG PRESS — trigger bottom sheet on mobile
   ───────────────────────────────────────────────────────────── */
(function setupLongPress() {
    let timer = null;
    let moved = false;

    document.addEventListener('touchstart', e => {
        const card = e.target.closest('.item-card');
        if (!card) return;
        // Don't trigger on checkbox or action buttons
        if (e.target.closest('.item-checkbox,.action-wrapper,.btn-dots')) return;
        moved = false;
        card.classList.add('cm-pressing');
        timer = setTimeout(() => {
            if (!moved) {
                navigator.vibrate?.(40); // haptic feedback
                card.classList.remove('cm-pressing');
                showBs(card);
            }
        }, 480);
    }, { passive: true });

    document.addEventListener('touchmove',  () => { moved = true; clearTimeout(timer); document.querySelectorAll('.cm-pressing').forEach(c => c.classList.remove('cm-pressing')); }, { passive: true });
    document.addEventListener('touchend',   () => { clearTimeout(timer); document.querySelectorAll('.cm-pressing').forEach(c => c.classList.remove('cm-pressing')); }, { passive: true });
    document.addEventListener('touchcancel',() => { clearTimeout(timer); document.querySelectorAll('.cm-pressing').forEach(c => c.classList.remove('cm-pressing')); }, { passive: true });
})();


/* ─────────────────────────────────────────────────────────────
   8. AKSI: Toggle Star (AJAX)
   ───────────────────────────────────────────────────────────── */
async function cmToggleStar(id, type, card) {
    const fd = new FormData();
    fd.append('action',    'toggle_star');
    fd.append('csrf_token', CSRF);
    fd.append('item_id',    id);
    fd.append('item_type',  type);

    try {
        const res  = await fetch('index.php', { method: 'POST', body: fd });
        const data = await res.json();

        if (data.ok) {
            const isNowStarred = data.starred;

            // Update data-attribute & class on card
            if (card) {
                card.dataset.starred = isNowStarred ? '1' : '0';
                card.classList.toggle('is-starred', isNowStarred);

                // Update or add/remove the star badge
                let badge = card.querySelector('.item-star-badge');
                if (isNowStarred && !badge) {
                    const iconWrap = card.querySelector('.item-icon-lg');
                    if (iconWrap) {
                        iconWrap.style.position = 'relative';
                        const b = document.createElement('span');
                        b.className = 'item-star-badge';
                        b.innerHTML = '<i class="fa-solid fa-star"></i>';
                        iconWrap.appendChild(b);
                    }
                } else if (!isNowStarred && badge) {
                    badge.remove();
                }
            }

            const msg = isNowStarred
                ? '<i class="fa-solid fa-star" style="color:#f59e0b;margin-right:6px;"></i> Ditambahkan ke Berbintang!'
                : '<i class="fa-regular fa-star" style="margin-right:6px;"></i> Dihapus dari Berbintang';
            if (typeof showToast === 'function') showToast(msg);
        }
    } catch (err) {
        console.error('Toggle star error:', err);
    }
}


/* ─────────────────────────────────────────────────────────────
   9. AKSI: Salin share link
   ───────────────────────────────────────────────────────────── */
function cmCopyShare(url) {
    navigator.clipboard.writeText(url).then(() => {
        if (typeof showToast === 'function')
            showToast('<i class="fa-solid fa-check-circle"></i> Link berbagi disalin!');
    }).catch(() => {
        prompt('Salin link ini:', url);
    });
}


/* ─────────────────────────────────────────────────────────────
   10. KEYBOARD SHORTCUTS saat item terpilih
   ───────────────────────────────────────────────────────────── */
document.addEventListener('keydown', function(e) {
    const focused = document.activeElement;
    if (['INPUT','TEXTAREA','SELECT'].includes(focused.tagName)) return;

    const selected = document.querySelector('#workspaceContainer .item-card.selected');
    if (!selected) return;
    _ctxTarget = selected;

    // S = toggle star
    if (e.key.toLowerCase() === 's' && !e.ctrlKey && !e.metaKey) {
        e.preventDefault();
        cmToggleStar(selected.dataset.id, selected.dataset.type, selected);
    }

    // D = download
    if (e.key.toLowerCase() === 'd' && !e.ctrlKey && !e.metaKey) {
        if (selected.dataset.type !== 'folder' && selected.dataset.type !== 'link') {
            e.preventDefault();
            location.href = `?action=download_file&file_id=${selected.dataset.id}`;
        }
    }
});
