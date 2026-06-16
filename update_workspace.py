import re

def update_index_php():
    with open('c:/hosting/index.php', 'r', encoding='latin-1') as f:
        content = f.read()

    css_start = content.find("/* ══ GOOGLE DRIVE LAYOUT")
    if css_start == -1:
        css_start = content.find("/* ══ MODERN PREMIUM WORKSPACE LAYOUT")
    
    if css_start != -1:
        css_end = content.find("</style>", css_start)
        
        new_css = """/* ══ MODERN PREMIUM WORKSPACE LAYOUT ══ */
.modern-workspace { padding: 8px 16px; min-height: calc(100vh - 80px); display: block !important; }

/* Typography & Layout */
.section-title { font-size: 1.1rem; font-weight: 700; color: var(--text-main); margin: 32px 0 16px; letter-spacing: 0.5px; }
.grid-folders { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 32px; }
.grid-files { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px; }

/* Glassmorphism Cards */
.modern-folder-card { 
    display: flex; align-items: center; gap: 16px; height: 64px; padding: 0 16px;
    background: rgba(255,255,255, 0.03); border: 1px solid rgba(255,255,255, 0.05);
    border-radius: 16px; cursor: pointer; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    position: relative; overflow: hidden; backdrop-filter: blur(10px);
}
.modern-folder-card:hover {
    background: rgba(255,255,255, 0.07); border-color: rgba(255,255,255, 0.1);
    transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0, 0.2);
}
.modern-folder-card.selected { background: rgba(99, 102, 241, 0.2); border-color: var(--accent); }
.modern-folder-card .icon-wrap {
    width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
    border-radius: 10px; font-size: 1.2rem; background: rgba(255,255,255, 0.05);
}
.modern-folder-card .info-wrap { flex: 1; overflow: hidden; display: flex; flex-direction: column; justify-content: center;}
.modern-folder-card .item-name { font-weight: 600; font-size: 0.95rem; color: var(--text-main); white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
.modern-folder-card .item-meta { font-size: 0.75rem; color: var(--text-muted); margin-top: 2px; }

/* File Card */
.modern-file-card {
    display: flex; flex-direction: column; height: 210px;
    background: rgba(255,255,255, 0.03); border: 1px solid rgba(255,255,255, 0.05);
    border-radius: 16px; cursor: pointer; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    position: relative; overflow: hidden; backdrop-filter: blur(10px);
}
.modern-file-card:hover {
    background: rgba(255,255,255, 0.07); border-color: rgba(255,255,255, 0.1);
    transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0, 0.3);
}
.modern-file-card.selected { background: rgba(99, 102, 241, 0.2); border-color: var(--accent); }
.modern-file-card .preview-area {
    height: 140px; background: rgba(0,0,0, 0.2); display: flex; align-items: center; justify-content: center;
    overflow: hidden; position: relative; border-bottom: 1px solid rgba(255,255,255,0.05);
}
.modern-file-card .preview-area img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
.modern-file-card:hover .preview-area img { transform: scale(1.05); }
.modern-file-card .icon-placeholder { font-size: 3.5rem; opacity: 0.7; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.5)); transition: transform 0.3s; }
.modern-file-card:hover .icon-placeholder { transform: scale(1.1); }

.modern-file-card .info-area { padding: 12px 16px; flex: 1; display: flex; align-items: center; gap: 12px; }
.modern-file-card .item-icon { font-size: 1.4rem; }
.modern-file-card .item-details { flex: 1; overflow: hidden; display: flex; flex-direction: column;}
.modern-file-card .item-name { font-weight: 600; font-size: 0.9rem; color: var(--text-main); white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
.modern-file-card .item-meta { font-size: 0.75rem; color: var(--text-muted); margin-top: 3px; }

/* Context Menu */
.modern-context-menu {
    position: fixed; background: rgba(20, 25, 35, 0.85); backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255, 0.1); border-radius: 12px; padding: 8px 0;
    min-width: 220px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); z-index: 9999;
    display: none; animation: scaleIn 0.15s ease-out;
}
.modern-context-menu.show { display: block; }
@keyframes scaleIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }

.modern-context-item {
    padding: 8px 20px; font-size: 0.85rem; color: var(--text-main); display: flex; align-items: center;
    gap: 12px; cursor: pointer; transition: background 0.1s; font-weight: 500; text-decoration: none;
}
.modern-context-item:hover { background: rgba(255,255,255, 0.08); color: var(--text-main); }
.modern-context-item i { width: 18px; text-align: center; color: var(--text-secondary); }
.modern-context-divider { height: 1px; background: rgba(255,255,255, 0.1); margin: 6px 0; }

/* Actions & Checkbox */
.action-wrapper, .item-checkbox, .col-owner, .col-date, .col-size { display: none; }
.modern-folder-card:hover .action-wrapper, .modern-file-card:hover .action-wrapper { 
    display: flex; align-items: center; justify-content: center;
    position: absolute; right: 12px; top: 12px; width: 32px; height: 32px;
    background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); border-radius: 8px; color: #fff; cursor: pointer;
}
.modern-folder-card:hover .item-checkbox, .modern-file-card:hover .item-checkbox {
    display: block; position: absolute; left: 12px; top: 12px; z-index: 2;
    width: 20px; height: 20px; accent-color: var(--accent); cursor: pointer;
}

/* List View Overrides */
.view-list .grid-folders, .view-list .grid-files { display: flex; flex-direction: column; gap: 4px; margin-bottom: 16px; }
.view-list .modern-folder-card, .view-list .modern-file-card {
    display: grid; grid-template-columns: 40px 1fr 180px 140px 100px 40px; align-items: center;
    height: 56px; border-radius: 12px; padding: 0 16px; background: transparent; border: 1px solid transparent; gap: 0;
}
.view-list .modern-folder-card:hover, .view-list .modern-file-card:hover {
    background: rgba(255,255,255, 0.03); border-color: rgba(255,255,255, 0.05); transform: none; box-shadow: none;
}
.view-list .preview-area { display: none; }
.view-list .info-area { padding: 0; }
.view-list .col-owner, .view-list .col-date, .view-list .col-size { display: block; font-size: 0.85rem; color: var(--text-secondary); }
.view-list .col-owner { display: flex; align-items: center; gap: 8px; }
.view-list .col-owner img { width: 24px; height: 24px; border-radius: 50%; }
.view-list .modern-folder-card .item-checkbox, .view-list .modern-file-card .item-checkbox {
    display: block; position: static; margin: 0;
}
.view-list .action-wrapper { display: flex !important; position: static !important; background: transparent !important; color: var(--text-secondary) !important; width: auto !important; height: auto !important; margin-left: auto;}
.view-list .action-wrapper:hover { color: var(--text-main) !important; background: transparent !important; }

"""
        content = content[:css_start] + new_css + content[css_end:]
        with open('c:/hosting/index.php', 'w', encoding='latin-1') as f:
            f.write(content)
        print("Updated index.php")

def update_pengelola_file_php():
    with open('c:/hosting/tampilan/dasbor/pengelola_file.php', 'r', encoding='latin-1') as f:
        content = f.read()

    # 1. Update container class
    content = content.replace('class="view-grid drive-layout"', 'class="view-grid modern-workspace"')

    # 2. Update Folder section HTML
    # We replace: drive-folder-card -> modern-folder-card
    content = content.replace("drive-folder-card", "modern-folder-card")
    content = content.replace("drive-section-title", "section-title")
    content = content.replace("drive-grid-folders", "grid-folders")
    
    # Modify the internal folder HTML building block
    # Instead of <div class='item-info-wrap'><div class='item-icon-lg'>...
    old_folder_html = """<div class='item-info-wrap'><div class='item-icon-lg' style='color:var(--text-main);'><i class='fa-solid fa-folder'></i></div><div class='item-name'>$sn</div></div>"""
    new_folder_html = """<div class='icon-wrap' style='color:var(--text-main);'><i class='fa-solid fa-folder'></i></div><div class='info-wrap'><div class='item-name'>$sn</div><div class='item-meta'>Folder</div></div>"""
    content = content.replace(old_folder_html, new_folder_html)

    # 3. Update File section HTML
    content = content.replace("drive-file-card", "modern-file-card")
    content = content.replace("drive-grid-files", "grid-files")
    content = content.replace("drive-file-preview", "preview-area")
    content = content.replace("drive-file-preview-img", "preview-img")
    content = content.replace("drive-file-icon-placeholder", "icon-placeholder")
    
    # Modify internal file HTML block
    old_file_info = """<div class='item-info-wrap'><div class='item-icon-sm'><i class='$js_icon' style='color:$ic_col;'></i></div><div class='item-name'>$sn".($st?"<span class='tag-badge'><i class='fa-solid fa-tag'></i> $st</span>":"")."</div></div>"""
    new_file_info = """<div class='info-area'><div class='item-icon'><i class='$js_icon' style='color:$ic_col;'></i></div><div class='item-details'><div class='item-name'>$sn".($st?"<span class='tag-badge'><i class='fa-solid fa-tag'></i> $st</span>":"")."</div><div class='item-meta'>$sz &bull; $ds</div></div></div>"""
    content = content.replace(old_file_info, new_file_info)

    # 4. Context menus class rename
    content = content.replace("action-dropdown", "modern-context-menu")
    
    # 5. Fix JS context menu item rendering
    # Actually wait, JS dynamically builds modern-context-item? The context menu has links inside it
    # We should add modern-context-item class to <a> and <button> inside modern-context-menu
    # Let's just do a regex
    content = re.sub(r"<a href='(.*?)'([^>]*)>", r"<a href='\1'\2 class='modern-context-item'>", content)
    content = re.sub(r"<button onclick=\\\"(.*?)\\\"([^>]*)>", r"<button onclick=\\\"\1\\\"\2 class='modern-context-item'>", content)

    with open('c:/hosting/tampilan/dasbor/pengelola_file.php', 'w', encoding='latin-1') as f:
        f.write(content)
    print("Updated pengelola_file.php")

if __name__ == '__main__':
    update_index_php()
    update_pengelola_file_php()

