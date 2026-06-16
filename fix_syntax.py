
with open('c:/hosting/index.php', 'r', encoding='latin-1') as f:
    content = f.read()

target = '''  .view-list .btn-dots { opacity: 1; background: transparent; }
  .view-list .modern-context-menu { right: 30px; top: auto; }

</style>'''

replacement = '''  .view-list .btn-dots { opacity: 1; background: transparent; }
  .view-list .modern-context-menu { right: 30px; top: auto; }

<?php endif; ?>
</style>'''

if target in content:
    content = content.replace(target, replacement)
    with open('c:/hosting/index.php', 'w', encoding='latin-1') as f:
        f.write(content)
    print('Fixed syntax error in index.php')
else:
    print('Target string not found!')

