import os
import ftplib
import json
import sys

def create_remote_dir(ftp, remote_path):
    parts = [p for p in remote_path.split('/') if p]
    current = '/'
    for p in parts:
        current = current + p + '/'
        try:
            ftp.mkd(current)
        except ftplib.error_perm as e:
            if not str(e).startswith('550'):
                print(f"Directory creation warning for {current}: {str(e)}")

def upload_file(ftp, local_path, remote_path):
    with open(local_path, 'rb') as f:
        try:
            ftp.storbinary(f'STOR {remote_path}', f)
            print(f"Uploaded: {remote_path}")
        except Exception as e:
            print(f"Failed to upload {remote_path}: {str(e)}")

def deploy():
    try:
        with open('c:/hosting/.vscode/sftp.json') as f:
            config = json.load(f)
    except Exception as e:
        print("Failed to load sftp.json:", str(e))
        return

    ignores = config.get('ignore', [])
    ignores.extend([
        'patch_mime.py', 'renumber.py', 'gen_tree.py', 
        'update_readme.py', 'ftp_upload.py', 
        '10_konfigurasi_lama', '11_inti_lama'
    ])
    
    local_dir = 'c:/hosting'
    remote_base = config.get('remotePath', '/public_html')
    
    try:
        print("Connecting to FTP...")
        ftp = ftplib.FTP()
        ftp.connect(config['host'], config['port'], timeout=30)
        ftp.login(config['username'], config['password'])
        print("Connected!")
    except Exception as e:
        print("FTP connection failed:", str(e))
        return
        
    for root, dirs, files in os.walk(local_dir):
        dirs[:] = [d for d in dirs if d not in ignores and not d.startswith('.')]
        
        rel_path = os.path.relpath(root, local_dir).replace('\\', '/')
        if rel_path == '.':
            remote_dir = remote_base
        else:
            remote_dir = f"{remote_base}/{rel_path}"
            
        if remote_dir != remote_base:
            create_remote_dir(ftp, remote_dir)
            
        for file in files:
            if file in ignores or file.startswith('.'):
                continue
                
            local_file = os.path.join(root, file)
            remote_file = f"{remote_dir}/{file}"
            upload_file(ftp, local_file, remote_file)
            
    try:
        ftp.quit()
    except:
        pass
    print("Deployment completed successfully!")

if __name__ == '__main__':
    deploy()
