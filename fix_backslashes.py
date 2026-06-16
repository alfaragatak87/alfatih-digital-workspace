with open('c:/hosting/tampilan/dasbor/pengelola_file.php', 'r', encoding='latin-1') as f:
    c = f.read()

# Replace \\" with \"
c = c.replace(r'\\"', r'\"')

with open('c:/hosting/tampilan/dasbor/pengelola_file.php', 'w', encoding='latin-1') as f:
    f.write(c)

print('Done fixing backslashes!')
