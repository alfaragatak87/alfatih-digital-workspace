import re

with open('c:/hosting/index.php', 'r', encoding='latin-1') as f:
    content = f.read()

new_css = """/*  ? ? GOOGLE DRIVE LAYOUT (DARK SAAS PREMIUM)  ? ? */
.drive-layout { padding: 16px; background: transparent; min-height: calc(100vh - 80px); display: block !important; margin: 0 16px 16px 0; }
body { background: var(--bg); color: var(--text-main); }
.top-navbar { background: var(--bg); border-bottom: 1px solid var(--border); box-shadow: none; height: 64px; padding: 8px 16px; }
.sidebar { background: var(--bg); border-right: 1px solid var(--border); width: 256px; padding-top: 12px; }
.main-wrapper { margin-left: 256px; padding-top: 64px; }
.nav-item { margin: 2px 12px; border-radius: 12px; padding: 8px 16px; color: var(--text-secondary); font-size: 0.875rem; font-weight: 500; font-family: 'Inter', sans-serif; transition: all 0.2s ease; }
.nav-item:hover { background: var(--surface-2); color: var(--text-main); }
.nav-item.active { background: var(--accent-soft); color: var(--accent); font-weight: 600; }
.nav-item i { width: 24px; font-size: 1.1rem; color: var(--text-secondary); transition: all 0.2s ease; }
.nav-item:hover i { color: var(--text-main); }
.nav-item.active i { color: var(--accent); }
.btn-drive-new { display: flex; align-items: center; gap: 12px; background: var(--surface-2); border: 1px solid var(--border); border-radius: 16px; padding: 12px 20px; font-size: 0.875rem; font-weight: 600; font-family: 'Inter', sans-serif; color: var(--text-main); cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: all 0.2s ease; margin-bottom: 8px; }
.btn-drive-new:hover { background: var(--surface-3); border-color: var(--border-md); transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,0,0,0.15); }

.drive-section-title { font-size: 0.875rem; font-weight: 600; font-family: 'Inter', sans-serif; color: var(--text-secondary); margin-bottom: 16px; margin-top: 24px; letter-spacing: 0.5px; text-transform: uppercase; }
.drive-grid-folders { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; margin-bottom: 24px; }
.drive-folder-card { display: flex; align-items: center; background: var(--surface-2); border: 1px solid var(--border); border-radius: 16px; padding: 12px 16px; cursor: pointer; transition: all 0.2s ease; position: relative; gap: 12px; height: 56px; backdrop-filter: blur(10px); }
.drive-folder-card:hover { background: var(--surface-3); border-color: var(--border-md); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
.drive-folder-card.selected { background: var(--accent-soft); border-color: var(--accent); }
.drive-folder-card .item-info-wrap { flex: 1; display: flex; align-items: center; gap: 12px; overflow: hidden; }
.drive-folder-card .item-icon-lg { font-size: 1.4rem; color: var(--text-secondary); }
.drive-folder-card .item-name { font-size: 0.9rem; font-weight: 500; font-family: 'Inter', sans-serif; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.drive-folder-card .action-wrapper, .drive-folder-card .item-checkbox, .drive-folder-card .col-owner, .drive-folder-card .col-date, .drive-folder-card .col-size { display: none; }
.drive-folder-card:hover .action-wrapper { display: flex; position: absolute; right: 8px; top: 8px; }

.drive-grid-files { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }
.drive-file-card { display: flex; flex-direction: column; background: var(--surface-2); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; cursor: pointer; transition: all 0.2s ease; position: relative; height: 200px; backdrop-filter: blur(10px); }
.drive-file-card:hover { background: var(--surface-3); border-color: var(--border-md); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
.drive-file-card.selected { background: var(--accent-soft); border-color: var(--accent); }
.drive-file-preview { height: 140px; background: var(--surface); display: flex; align-items: center; justify-content: center; border-bottom: 1px solid var(--border); overflow: hidden; }
.drive-file-preview-img { width: 100%; height: 100%; object-fit: cover; opacity: 0.9; transition: transform 0.3s ease; }
.drive-file-card:hover .drive-file-preview-img { transform: scale(1.05); }
.drive-file-icon-placeholder i { font-size: 4rem; opacity: 0.7; }
.drive-file-card .item-info-wrap { padding: 12px; display: flex; align-items: center; gap: 12px; flex: 1; }
.drive-file-card .item-icon-sm { font-size: 1.2rem; }
.drive-file-card .item-name { font-size: 0.9rem; font-weight: 500; font-family: 'Inter', sans-serif; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; }
.drive-file-card .action-wrapper, .drive-file-card .item-checkbox, .drive-file-card .col-owner, .drive-file-card .col-date, .drive-file-card .col-size { display: none; }
.drive-file-card:hover .action-wrapper { display: flex; position: absolute; right: 8px; bottom: 8px; }

/* Context Menu */
.drive-context-menu, .action-dropdown, .dropdown-content { position: absolute; background: var(--surface-2); border: 1px solid var(--border-md); border-radius: 12px; padding: 8px 0; min-width: 220px; box-shadow: 0 8px 32px rgba(0,0,0,0.4); z-index: 9999; font-family: 'Inter', sans-serif; display: none; backdrop-filter: blur(10px); }
.drive-context-menu.show, .action-dropdown.show, .dropdown-content.show { display: block; animation: fadeIn 0.15s ease-out; }
.drive-context-item, .action-dropdown a, .action-dropdown button, .dropdown-content button { padding: 10px 16px; font-size: 0.875rem; color: var(--text-main); display: flex; align-items: center; gap: 12px; cursor: pointer; transition: background 0.15s; background: transparent; border: none; width: 100%; text-align: left; }
.drive-context-item:hover, .action-dropdown a:hover, .action-dropdown button:hover, .dropdown-content button:hover { background: var(--surface-3); }
.drive-context-item i, .action-dropdown i, .dropdown-content i { font-size: 1.1rem; color: var(--text-secondary); width: 20px; text-align: center; }
.drive-context-divider, .menu-divider { height: 1px; background: var(--border); margin: 8px 0; border: none; }

/* List View Overrides */
.view-list .drive-grid-folders, .view-list .drive-grid-files { display: flex; flex-direction: column; gap: 8px; }
.view-list .drive-folder-card, .view-list .drive-file-card { display: grid; grid-template-columns: 36px 1fr 160px 140px 90px 40px; align-items: center; height: 56px; border-radius: 12px; border: 1px solid var(--border); padding: 0 16px; background: var(--surface-2); margin-bottom: 0; }
.view-list .drive-folder-card:hover, .view-list .drive-file-card:hover { background: var(--surface-3); transform: translateX(4px); }
.view-list .drive-folder-card.selected, .view-list .drive-file-card.selected { background: var(--accent-soft); }
.view-list .drive-file-preview { display: none; }
.view-list .drive-file-card .item-info-wrap, .view-list .drive-folder-card .item-info-wrap { height: auto; padding: 0; }
.view-list .col-owner, .view-list .col-date, .view-list .col-size { display: flex; align-items: center; font-size: 0.875rem; color: var(--text-secondary); }
.view-list .col-owner img { width: 24px; height: 24px; border-radius: 50%; margin-right: 8px; }
.view-list .item-checkbox { display: block; position: relative; top: 0; left: 0; opacity: 0.3; accent-color: var(--accent); }
.view-list .drive-folder-card:hover .item-checkbox, .view-list .drive-file-card:hover .item-checkbox { opacity: 1; }
.view-list .action-wrapper { display: flex !important; position: static !important; }
.view-list #driveListHeader { display: grid !important; color: var(--text-secondary) !important; border-bottom: 1px solid var(--border) !important; }
.view-list #driveListHeader a { color: var(--text-secondary) !important; }

/* Toolbar & Misc */
.toolbar-main { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.btn-new { display: flex; align-items: center; gap: 8px; background: var(--accent); color: white; border: none; padding: 10px 20px; border-radius: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(99,102,241,0.3); }
.btn-new:hover { background: var(--accent-2); transform: translateY(-1px); box-shadow: 0 6px 16px rgba(99,102,241,0.4); }
.btn-icon { background: var(--surface-2); color: var(--text-main); border: 1px solid var(--border); width: 40px; height: 40px; border-radius: 10px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
.btn-icon:hover { background: var(--surface-3); border-color: var(--border-md); }
.btn-dots { background: transparent; color: var(--text-secondary); border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; transition: all 0.2s; }
.btn-dots:hover { background: var(--surface-3); color: var(--text-main); }
.view-toggle { display: flex; background: var(--surface-2); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; }
.view-toggle button { background: transparent; color: var(--text-secondary); border: none; padding: 8px 12px; cursor: pointer; transition: all 0.2s; }
.view-toggle button:hover { color: var(--text-main); }
.view-toggle button.active { background: var(--surface-3); color: var(--accent); }
.bulk-toolbar { background: var(--accent); color: white; padding: 12px 24px; border-radius: 12px; display: flex; align-items: center; gap: 16px; margin-bottom: 24px; box-shadow: 0 4px 16px rgba(99,102,241,0.2); }
.bulk-btn { background: rgba(255,255,255,0.2); color: white; border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer; transition: background 0.2s; }
.bulk-btn:hover { background: rgba(255,255,255,0.3); }
.empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 20px; background: var(--surface-2); border: 1px dashed var(--border-md); border-radius: 16px; text-align: center; margin-top: 40px; cursor: pointer; transition: background 0.2s; }
.empty-state:hover { background: var(--surface-3); }

/* Right Sidebar Detail */
.right-sidebar { background: var(--surface-2); border-left: 1px solid var(--border); color: var(--text-main); padding: 20px; box-shadow: -4px 0 24px rgba(0,0,0,0.2); }
.rs-close { color: var(--text-secondary); }
.rs-close:hover { color: var(--text-main); background: var(--surface-3); }
.detail-icon-lg { background: var(--surface-3); border-radius: 16px; display: flex; align-items: center; justify-content: center; width: 80px; height: 80px; margin: 0 auto 20px; font-size: 2.5rem; }
.prop-label { color: var(--text-secondary); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
.prop-value { color: var(--text-main); font-weight: 500; }
"""

start_marker = r'/\*.*?GOOGLE DRIVE LAYOUT.*?\*/'
pattern = re.compile(start_marker + r'.*?(?=<\?php endif; \?>\s*</style>|</style>)', re.DOTALL)

if pattern.search(content):
    parts = re.split(start_marker, content)
    if len(parts) > 1:
        pre_content = parts[0]
        rest_content = parts[-1]
        # We know from git show that <?php endif; ?> is right before </style>
        # So we can safely replace everything up to <?php endif; ?> or </style>
        # Let's just split by '<?php endif; ?>' if it exists in the rest_content, else '</style>'
        if '<?php endif; ?>' in rest_content:
            end_split = rest_content.split('<?php endif; ?>', 1)
            final_content = pre_content + new_css + '\n<?php endif; ?>' + end_split[1]
        else:
            end_split = rest_content.split('</style>', 1)
            final_content = pre_content + new_css + '\n</style>' + end_split[1]
            
        with open('c:/hosting/index.php', 'w', encoding='latin-1') as f:
            f.write(final_content)
        print("Replaced CSS successfully, preserving endif.")
    else:
         print("No match for GOOGLE DRIVE LAYOUT.")
else:
    print("Could not find the GOOGLE DRIVE LAYOUT section.")
