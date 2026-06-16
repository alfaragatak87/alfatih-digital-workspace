import re

with open('c:/hosting/index.php', 'r', encoding='latin-1') as f:
    content = f.read()

# 1. Remove the !important overrides block
start_str = ":root, body, html, .main-wrapper, .drive-layout {"
end_str = "/*  ? ? GOOGLE DRIVE LAYOUT (MATERIAL 3 LIGHT)  ? ? */"

if start_str in content:
    idx_start = content.find(start_str)
    idx_end = content.find("}", idx_start) + 1
    content = content[:idx_start] + content[idx_end:]

# 2. Replace hardcoded light colors with dark theme variables
replacements = {
    "#ffffff": "var(--surface)",
    "#f8fafd": "var(--bg)",
    "#f0f4f9": "var(--surface-2)",
    "#e8eaed": "var(--surface-3)",
    "#c2e7ff": "rgba(99, 102, 241, 0.2)",
    "#001d35": "var(--text-main)",
    "#1f1f1f": "var(--text-main)",
    "#444746": "var(--text-secondary)",
    "#5f6368": "var(--text-muted)",
    "#e0e0e0": "var(--border)",
    "#dadce0": "var(--border)"
}

# Only replace within the Google Drive Layout CSS section to avoid breaking other things
css_start = content.find("/* ══ GOOGLE DRIVE LAYOUT (MATERIAL 3 LIGHT) ══ */")
if css_start != -1:
    css_end = content.find("</style>", css_start)
    if css_end != -1:
        css_block = content[css_start:css_end]
        for old, new in replacements.items():
            # case insensitive replacement
            css_block = re.sub(old, new, css_block, flags=re.IGNORECASE)
            
        content = content[:css_start] + css_block + content[css_end:]

with open('c:/hosting/index.php', 'w', encoding='latin-1') as f:
    f.write(content)

print("index.php updated to dark theme.")
