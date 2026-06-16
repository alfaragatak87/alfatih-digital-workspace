
with open('c:/hosting/index.php', 'r', encoding='latin-1') as f:
    content = f.read()

idx_google = content.find('GOOGLE DRIVE LAYOUT')
idx_start = content.rfind('/*', 0, idx_google)
idx_end = content.find('</style>', idx_start)

if idx_start != -1 and idx_end != -1:
    new_css = '''/* === MODERN PREMIUM WORKSPACE LAYOUT === */
  :root {
    --bg-main: #0f1115;
    --bg-card: rgba(25, 28, 36, 0.6);
    --border-dark: rgba(255, 255, 255, 0.08);
    --text-main: #f1f5f9;
    --text-muted: #94a3b8;
    --accent: #3b82f6;
    --accent-hover: #2563eb;
    --danger: #ef4444;
  }

  /* Core Layout */
  .drive-layout { 
    padding: 24px; 
    background: var(--bg-main); 
    border-radius: 20px; 
    min-height: calc(100vh - 80px); 
    display: block !important; 
    margin: 0 16px 16px 0;
    box-shadow: inset 0 0 100px rgba(0,0,0,0.5);
  }
  body { background: #0a0a0c; color: var(--text-main); font-family: 'Inter', sans-serif; }
  
  /* Glassmorphism Cards */
  .item-card {
    background: var(--bg-card);
    border: 1px solid var(--border-dark);
    border-radius: 16px;
    padding: 16px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }
  .item-card:hover {
    transform: translateY(-4px) scale(1.02);
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 10px 30px -10px rgba(0,0,0,0.5);
    background: rgba(30, 34, 45, 0.8);
  }
  .item-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  .item-card:hover::before { opacity: 1; }

  /* Folder Styling */
  .modern-folder-card {
    display: flex;
    align-items: center;
    gap: 16px;
    height: 72px;
  }
  .modern-folder-card .icon-wrap {
    width: 40px; height: 40px;
    background: rgba(255,255,255,0.05);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
  }
  .modern-folder-card .info-wrap { flex: 1; }
  .item-name { font-weight: 600; font-size: 0.95rem; margin-bottom: 4px; color: var(--text-main); }
  .item-meta { font-size: 0.75rem; color: var(--text-muted); }

  /* File Styling */
  .modern-file-card {
    display: flex;
    flex-direction: column;
    height: 220px;
    padding: 0;
  }
  .preview-area {
    height: 140px;
    background: rgba(0,0,0,0.2);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
    border-bottom: 1px solid var(--border-dark);
  }
  .preview-area-img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform 0.5s ease;
  }
  .modern-file-card:hover .preview-area-img { transform: scale(1.05); }
  .icon-placeholder { font-size: 3rem; opacity: 0.8; }
  .info-area { padding: 12px 16px; display: flex; align-items: center; gap: 12px; }
  .info-area .item-icon { font-size: 1.5rem; }

  /* Context Menu */
  .action-wrapper { position: absolute; top: 12px; right: 12px; }
  .btn-dots {
    background: rgba(0,0,0,0.4);
    border: none;
    color: var(--text-main);
    width: 32px; height: 32px;
    border-radius: 50%;
    cursor: pointer;
    backdrop-filter: blur(4px);
    opacity: 0;
    transition: all 0.2s ease;
  }
  .item-card:hover .btn-dots { opacity: 1; }
  .btn-dots:hover { background: rgba(255,255,255,0.1); }
  
  .modern-context-menu {
    position: absolute;
    top: 40px; right: 0;
    background: rgba(20, 23, 30, 0.95);
    border: 1px solid var(--border-dark);
    border-radius: 12px;
    padding: 8px;
    min-width: 180px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.6);
    backdrop-filter: blur(16px);
    display: none;
    z-index: 100;
    transform: translateY(-10px);
    opacity: 0;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .modern-context-menu.show {
    display: block;
    transform: translateY(0);
    opacity: 1;
  }
  .modern-context-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 12px;
    color: var(--text-main);
    text-decoration: none;
    border-radius: 8px;
    font-size: 0.85rem;
    transition: background 0.2s;
    background: transparent;
    border: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
  }
  .modern-context-item:hover { background: rgba(255,255,255,0.08); }
  .menu-divider { border-color: var(--border-dark); margin: 6px 0; }

  /* List View Overrides */
  .view-list .item-card { height: auto !important; display: grid !important; grid-template-columns: 3fr 2fr 1.5fr 1fr 40px; padding: 12px 16px; align-items: center; }
  .view-list .preview-area { display: none; }
  .view-list .info-area { padding: 0; border: none; }
  .col-owner, .col-date, .col-size { display: none; color: var(--text-muted); font-size: 0.85rem; }
  .view-list .col-owner, .view-list .col-date, .view-list .col-size { display: flex; align-items: center; gap: 8px; }
  .col-owner img { width: 24px; height: 24px; border-radius: 50%; }
  .view-list .action-wrapper { position: static; }
  .view-list .btn-dots { opacity: 1; background: transparent; }
  .view-list .modern-context-menu { right: 30px; top: auto; }

</style>'''
    
    new_content = content[:idx_start] + new_css + content[idx_end + len('</style>'):]
    with open('c:/hosting/index.php', 'w', encoding='latin-1') as f:
        f.write(new_content)
    print('Updated index.php safely!')
else:
    print('Could not find markers!')

