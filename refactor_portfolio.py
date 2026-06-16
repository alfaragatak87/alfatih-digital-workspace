import os
import re

index_path = r"c:\hosting\index.php"
with open(index_path, "r", encoding="utf-8") as f:
    content = f.read()

# Replace the call at line 114
# Old: renderPortfolioPage($puser, $pd, $pFoto); exit;
# New: include "views/pages/portfolio_page.php"; exit;
content = re.sub(
    r"renderPortfolioPage\(\$puser,\s*\$pd,\s*\$pFoto\);\s*exit;",
    r'include "views/pages/portfolio_page.php"; exit;',
    content
)

# Remove the entire renderPortfolioPage function block.
# We know it starts at "// ======================================================\n// PORTFOLIO PAGE RENDER FUNCTION"
# and ends after "</body></html>\n<?php\n}"

# Find the start
start_str = "// ======================================================\n// PORTFOLIO PAGE RENDER FUNCTION\n// ======================================================"
end_str = "</body></html>\n<?php\n}\n"

start_idx = content.find(start_str)
end_idx = content.find(end_str, start_idx)

if start_idx != -1 and end_idx != -1:
    end_idx += len(end_str)
    content = content[:start_idx] + content[end_idx:]
    print("Function renderPortfolioPage removed.")
else:
    print("Could not find start or end bounds for renderPortfolioPage block.")

with open(index_path, "w", encoding="utf-8") as f:
    f.write(content)

print("index.php refactored.")
