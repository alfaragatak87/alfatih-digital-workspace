(function() {
    // 1. Create global context menu element
    const ctxMenu = document.createElement('div');
    ctxMenu.className = 'drive-context-menu';
    document.body.appendChild(ctxMenu);

    // Close menu on click anywhere
    document.addEventListener('click', (e) => {
        if (!ctxMenu.contains(e.target)) {
            ctxMenu.classList.remove('show');
        }
    });

    // Handle right click
    document.addEventListener('contextmenu', (e) => {
        // Only override if inside workspaceContainer
        const workspace = document.getElementById('workspaceContainer');
        if (!workspace || !workspace.contains(e.target)) return;

        e.preventDefault();

        const card = e.target.closest('.item-card');
        ctxMenu.innerHTML = ''; // Clear previous

        if (card) {
            // Select the card
            if (!card.classList.contains('selected')) {
                document.querySelectorAll('.item-card.selected').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
            }

            // Extract actions from .action-dropdown
            const dropdown = card.querySelector('.action-dropdown');
            if (dropdown) {
                // Convert links and buttons to context menu items
                Array.from(dropdown.children).forEach(child => {
                    if (child.tagName === 'HR') {
                        const hr = document.createElement('div');
                        hr.className = 'drive-context-divider';
                        ctxMenu.appendChild(hr);
                    } else if (child.tagName === 'A' || child.tagName === 'BUTTON') {
                        const item = document.createElement('div');
                        item.className = 'drive-context-item';
                        
                        const iconHtml = child.querySelector('i') ? child.querySelector('i').outerHTML : '';
                        const textContent = child.textContent.trim();
                        
                        let shortcut = '';
                        const lowerText = textContent.toLowerCase();
                        if (lowerText.includes('hapus')) shortcut = 'Del';
                        if (lowerText.includes('ganti nama')) shortcut = 'F2';
                        if (lowerText.includes('buka')) shortcut = 'Enter';
                        
                        item.innerHTML = `
                            ${iconHtml}
                            <span style="flex:1;">${textContent}</span>
                            <span style="font-size:0.75rem;color:var(--text-muted);margin-left:24px;">${shortcut}</span>
                        `;
                        
                        if (child.style.color) item.style.color = child.style.color;
                        
                        item.addEventListener('click', () => {
                            ctxMenu.classList.remove('show');
                            if (child.tagName === 'A') {
                                if (child.target === '_blank') window.open(child.href, '_blank');
                                else window.location.href = child.href;
                            } else {
                                child.click();
                            }
                        });
                        ctxMenu.appendChild(item);
                    }
                });
            }
        } else {
            // Right click on empty space
            ctxMenu.innerHTML = `
                <div class="drive-context-item" onclick="openModal('addFolderModal')">
                    <i class="fa-solid fa-folder-plus"></i>
                    <span style="flex:1;">Folder baru</span>
                    <span style="font-size:0.75rem;color:var(--text-muted);margin-left:24px;">Alt+C kemudian F</span>
                </div>
                <div class="drive-context-divider"></div>
                <div class="drive-context-item" onclick="openModal('addItemModal');switchType('file');">
                    <i class="fa-solid fa-file-arrow-up"></i>
                    <span style="flex:1;">Upload file</span>
                    <span style="font-size:0.75rem;color:var(--text-muted);margin-left:24px;">Alt+C kemudian U</span>
                </div>
                <div class="drive-context-item" onclick="document.getElementById('modal_folder_input').click();">
                    <i class="fa-solid fa-folder-arrow-up"></i>
                    <span style="flex:1;">Upload folder</span>
                    <span style="font-size:0.75rem;color:var(--text-muted);margin-left:24px;">Alt+C kemudian I</span>
                </div>
                <div class="drive-context-divider"></div>
                <div class="drive-context-item" onclick="openModal('addItemModal');switchType('link');">
                    <i class="fa-solid fa-link"></i>
                    <span style="flex:1;">Simpan Tautan</span>
                    <span style="font-size:0.75rem;color:var(--text-muted);margin-left:24px;"></span>
                </div>
            `;
        }

        // Position menu
        ctxMenu.style.display = 'block';
        let x = e.clientX;
        let y = e.clientY;
        const rect = ctxMenu.getBoundingClientRect();

        if (x + rect.width > window.innerWidth) x = window.innerWidth - rect.width - 10;
        if (y + rect.height > window.innerHeight) y = window.innerHeight - rect.height - 10;

        ctxMenu.style.left = x + 'px';
        ctxMenu.style.top = y + 'px';
        ctxMenu.classList.add('show');
    });

    // 2. Keyboard Shortcuts
    document.addEventListener('keydown', (e) => {
        const activeModal = document.querySelector('.modal.active');
        if (activeModal) return; // Don't trigger if modal is open
        if (document.activeElement && ['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) return; // Don't trigger if typing

        const selectedCards = document.querySelectorAll('.item-card.selected');

        // Ctrl+A: Select All
        if (e.ctrlKey && e.key.toLowerCase() === 'a') {
            e.preventDefault();
            document.querySelectorAll('.item-card').forEach(c => c.classList.add('selected'));
        }

        if (selectedCards.length === 0) return;

        // Delete / Backspace: Move to trash
        if (e.key === 'Delete' || e.key === 'Backspace') {
            e.preventDefault();
            const actionBtn = selectedCards[0].querySelector('a[href*="action=soft_delete"]');
            if (actionBtn) window.location.href = actionBtn.href;
        }

        // Enter: Open folder or preview file
        if (e.key === 'Enter') {
            e.preventDefault();
            const card = selectedCards[0];
            if (card.dataset.itemType === 'folder') {
                window.location.href = `?page=workspace&folder_id=${card.dataset.id}`;
            } else {
                const previewBtn = card.querySelector('button[onclick*="openPreview"]');
                if (previewBtn) previewBtn.click();
                else {
                    const linkBtn = card.querySelector('a[target="_blank"]');
                    if (linkBtn) window.open(linkBtn.href, '_blank');
                }
            }
        }

        // F2: Rename
        if (e.key === 'F2') {
            e.preventDefault();
            const renameBtn = selectedCards[0].querySelector('button[onclick*="startInlineRename"]');
            if (renameBtn) renameBtn.click();
        }
    });
})();
