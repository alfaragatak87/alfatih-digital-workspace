<?php
ob_start();
$pageTitle = "Perpustakaan Media";
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';
require_once 'templates/header.php';

// Cek apakah sudah login
if (!isset($_SESSION['admin_id'])) {
    setAlert('error', 'Silakan login untuk mengakses panel admin');
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

// Konfigurasi
$uploadDir = '../uploads/media/';
$thumbsDir = '../uploads/media/thumbnails/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'video/mp4', 'application/zip'];
$maxFileSize = 10 * 1024 * 1024; // 10MB
$itemsPerPage = 20;

// Buat direktori jika belum ada
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}
if (!file_exists($thumbsDir)) {
    mkdir($thumbsDir, 0755, true);
}

// Proses upload file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media_files'])) {
    $uploadCount = 0;
    $errorCount = 0;
    $fileCount = count($_FILES['media_files']['name']);
    
    for ($i = 0; $i < $fileCount; $i++) {
        if ($_FILES['media_files']['error'][$i] === UPLOAD_ERR_OK) {
            $tempName = $_FILES['media_files']['tmp_name'][$i];
            $fileName = $_FILES['media_files']['name'][$i];
            $fileType = $_FILES['media_files']['type'][$i];
            $fileSize = $_FILES['media_files']['size'][$i];
            
            // Validasi file
            if (!in_array($fileType, $allowedTypes)) {
                $errorCount++;
                continue;
            }
            
            if ($fileSize > $maxFileSize) {
                $errorCount++;
                continue;
            }
            
            // Generate nama file unik
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = 'media_' . time() . '_' . $i . '.' . $fileExtension;
            $targetPath = $uploadDir . $newFileName;
            
            // Upload file
            if (move_uploaded_file($tempName, $targetPath)) {
                $thumbnailPath = null;
                
                // Generate thumbnail untuk gambar
                if (strpos($fileType, 'image/') === 0) {
                    $thumbnailPath = 'thumbnails/' . $newFileName;
                    createThumbnail($targetPath, $thumbsDir . $newFileName, 200, 200);
                }
                
                // Insert ke database
                $title = pathinfo($fileName, PATHINFO_FILENAME);
                $stmt = $pdo->prepare("INSERT INTO media_library (file_name, file_type, file_size, file_path, thumbnail_path, title, alt_text, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$fileName, $fileType, $fileSize, $newFileName, $thumbnailPath, $title, $title, $_SESSION['admin_id']]);
                
                $uploadCount++;
                logActivity('Upload media baru: ' . $fileName);
            } else {
                $errorCount++;
            }
        } else {
            $errorCount++;
        }
    }
    
    // Set pesan upload
    if ($uploadCount > 0) {
        $message = $uploadCount . ' file berhasil diupload';
        if ($errorCount > 0) {
            $message .= ', ' . $errorCount . ' file gagal';
        }
        setAlert('success', $message);
    } elseif ($errorCount > 0) {
        setAlert('error', 'Gagal mengupload ' . $errorCount . ' file');
    }
    
    header('Location: ' . BASE_URL . '/admin/media_library.php');
    exit;
}

// Proses upload folder
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['folder_upload'])) {
    // Ini akan ditangani oleh JavaScript karena PHP tidak bisa mengakses struktur folder langsung
    // JavaScript akan mengirim file satu per satu, dan kita akan mengelompokkannya berdasarkan struktur folder
}

// Proses hapus file
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $mediaId = (int)$_GET['id'];
    
    $stmt = $pdo->prepare("SELECT file_path, thumbnail_path FROM media_library WHERE id = ?");
    $stmt->execute([$mediaId]);
    $media = $stmt->fetch();
    
    if ($media) {
        // Hapus file fisik
        $filePath = $uploadDir . $media['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        if ($media['thumbnail_path']) {
            $thumbPath = $uploadDir . $media['thumbnail_path'];
            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }
        }
        
        // Hapus dari database
        $stmt = $pdo->prepare("DELETE FROM media_library WHERE id = ?");
        $stmt->execute([$mediaId]);
        
        logActivity('Hapus media (ID: ' . $mediaId . ')');
        setAlert('success', 'Media berhasil dihapus');
    } else {
        setAlert('error', 'Media tidak ditemukan');
    }
    
    header('Location: ' . BASE_URL . '/admin/media_library.php');
    exit;
}

// Proses update file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_media'])) {
    $mediaId = (int)$_POST['media_id'];
    $title = trim($_POST['title']);
    $altText = trim($_POST['alt_text']);
    $description = trim($_POST['description']);
    
    $stmt = $pdo->prepare("UPDATE media_library SET title = ?, alt_text = ?, description = ? WHERE id = ?");
    $stmt->execute([$title, $altText, $description, $mediaId]);
    
    logActivity('Update detail media (ID: ' . $mediaId . ')');
    setAlert('success', 'Detail media berhasil diperbarui');
    header('Location: ' . BASE_URL . '/admin/media_library.php');
    exit;
}

// Ambil data untuk ditampilkan
$filter = $_GET['filter'] ?? 'all';
$searchTerm = $_GET['search'] ?? '';
$currentPage = (int)($_GET['page'] ?? 1);

// Bangun query
$sql = "SELECT * FROM media_library WHERE 1=1";
$params = [];

if ($filter === 'images') {
    $sql .= " AND file_type LIKE ?";
    $params[] = 'image/%';
} elseif ($filter === 'documents') {
    $sql .= " AND file_type LIKE ?";
    $params[] = 'application/%';
} elseif ($filter === 'videos') {
    $sql .= " AND file_type LIKE ?";
    $params[] = 'video/%';
}

if (!empty($searchTerm)) {
    $sql .= " AND (file_name LIKE ? OR title LIKE ? OR description LIKE ?)";
    $searchParam = '%' . $searchTerm . '%';
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

// Hitung total item
$countSql = str_replace('SELECT *', 'SELECT COUNT(*)', $sql);
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalItems = $countStmt->fetchColumn();

// Ambil hasil dengan pagination
$offset = ($currentPage - 1) * $itemsPerPage;
$sql .= " ORDER BY upload_date DESC LIMIT ? OFFSET ?";
$params[] = $itemsPerPage;
$params[] = $offset;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$mediaItems = $stmt->fetchAll();

$totalPages = ceil($totalItems / $itemsPerPage);

// Fungsi helper untuk membuat thumbnail
function createThumbnail($sourcePath, $targetPath, $width, $height) {
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) return false;
    
    $imageType = $imageInfo[2];
    
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }
    
    if (!$sourceImage) return false;
    
    $sourceWidth = imagesx($sourceImage);
    $sourceHeight = imagesy($sourceImage);
    
    $ratio = min($width / $sourceWidth, $height / $sourceHeight);
    $newWidth = $sourceWidth * $ratio;
    $newHeight = $sourceHeight * $ratio;
    
    $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
    
    if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
        imagefilledrectangle($thumbnail, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
    
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumbnail, $targetPath, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($thumbnail, $targetPath);
            break;
        case IMAGETYPE_GIF:
            imagegif($thumbnail, $targetPath);
            break;
    }
    
    imagedestroy($sourceImage);
    imagedestroy($thumbnail);
    
    return true;
}

// Fungsi helper untuk mendapatkan icon file
function getFileIcon($fileType) {
    if (strpos($fileType, 'image/') === 0) return 'fas fa-image';
    if (strpos($fileType, 'application/pdf') === 0) return 'fas fa-file-pdf';
    if (strpos($fileType, 'video/') === 0) return 'fas fa-file-video';
    if (strpos($fileType, 'application/zip') === 0) return 'fas fa-file-archive';
    return 'fas fa-file';
}
?>

<style>
/* Media Library Styles */
.media-upload-area {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 40px;
    text-align: center;
    background: #f9f9f9;
    transition: all 0.3s ease;
    cursor: pointer;
}

.media-upload-area:hover, .media-upload-area.highlight {
    border-color: #007bff;
    background: #f0f8ff;
}

.media-upload-area i {
    font-size: 48px;
    color: #6c757d;
    margin-bottom: 20px;
}

.file-input {
    display: none;
}

.upload-progress {
    margin-top: 20px;
    display: none;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #007bff, #0056b3);
    width: 0%;
    transition: width 0.3s ease;
}

.folder-upload-container {
    margin-top: 20px;
    padding: 20px;
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    background: #fff;
}

.custom-file {
    position: relative;
    margin-bottom: 10px;
}

.custom-file-input {
    position: absolute;
    left: -9999px;
}

.custom-file-label {
    display: block;
    padding: 10px 15px;
    background: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.custom-file-label:hover {
    background: #e9ecef;
}

.media-filter-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.filter-tab {
    padding: 10px 20px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    text-decoration: none;
    color: #495057;
    transition: all 0.3s ease;
}

.filter-tab:hover {
    background: #e9ecef;
    text-decoration: none;
    color: #495057;
}

.filter-tab.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.media-search {
    flex: 1;
    max-width: 300px;
}

.search-input-group {
    display: flex;
    border: 1px solid #ced4da;
    border-radius: 6px;
    overflow: hidden;
}

.search-input {
    flex: 1;
    padding: 10px 15px;
    border: none;
    outline: none;
}

.search-btn {
    padding: 10px 15px;
    background: #007bff;
    color: white;
    border: none;
    cursor: pointer;
}

.search-btn:hover {
    background: #0056b3;
}

.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.media-item {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.media-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.media-preview {
    width: 100%;
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    overflow: hidden;
}

.media-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.media-icon {
    font-size: 48px;
    color: #6c757d;
}

.media-info {
    padding: 15px;
}

.media-title {
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 8px 0;
    line-height: 1.4;
}

.media-date {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 10px;
}

.media-actions {
    display: flex;
    gap: 5px;
}

.media-action {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 12px;
}

.media-action:hover {
    background: #f8f9fa;
    color: #495057;
    text-decoration: none;
}

.media-action.delete:hover {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
}

.media-action.copy:hover {
    background: #17a2b8;
    color: white;
    border-color: #17a2b8;
}

.media-action.edit:hover {
    background: #ffc107;
    color: #212529;
    border-color: #ffc107;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-top: 30px;
}

.pagination-btn {
    padding: 10px 15px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    text-decoration: none;
    color: #495057;
    transition: all 0.3s ease;
}

.pagination-btn:hover {
    background: #e9ecef;
    text-decoration: none;
    color: #495057;
}

.pagination-page {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: #495057;
    transition: all 0.3s ease;
}

.pagination-page:hover {
    background: #f8f9fa;
    text-decoration: none;
    color: #495057;
}

.pagination-page.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.media-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
}

.modal-content {
    background: white;
    margin: 5% auto;
    padding: 30px;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    position: relative;
}

.close-modal {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 28px;
    color: #aaa;
    cursor: pointer;
}

.close-modal:hover {
    color: #000;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #495057;
}

.form-control {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #ced4da;
    border-radius: 6px;
    font-size: 14px;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.no-data {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.no-data i {
    font-size: 64px;
    margin-bottom: 20px;
    color: #e9ecef;
}

@media (max-width: 768px) {
    .media-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
    }
    
    .admin-actions {
        flex-direction: column;
        gap: 15px;
    }
    
    .media-filter-tabs {
        justify-content: center;
    }
    
    .media-search {
        max-width: none;
    }
}
</style>

<div class="admin-content-container">
    <div class="admin-breadcrumb">
        <div class="breadcrumb-container">
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="breadcrumb-item"><i class="fas fa-home"></i> Dashboard</a>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
            <span class="breadcrumb-item active"><?= $pageTitle ?></span>
        </div>
        <div class="breadcrumb-actions">
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
            <a href="<?= BASE_URL ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fas fa-external-link-alt"></i> Lihat Situs</a>
        </div>
    </div>

    <div class="admin-content-header">
        <h1><i class="fas fa-photo-video"></i> Perpustakaan Media</h1>
        <p>Kelola gambar, dokumen, dan file media lainnya</p>
    </div>
    
    <!-- Bagian Upload -->
    <div class="admin-card mb-4">
        <div class="card-header">
            <h2><i class="fas fa-upload"></i> Upload Media</h2>
        </div>
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data" id="uploadForm">
                <div class="media-upload-area" id="dropArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Seret & lepas file di sini atau klik untuk browse</p>
                    <p style="font-size: 14px; color: #6c757d;">Maksimal ukuran file: 10MB. Tipe yang diizinkan: JPG, PNG, GIF, PDF, MP4, ZIP</p>
                    <input type="file" name="media_files[]" id="fileInput" multiple class="file-input" accept="image/*,application/pdf,video/mp4,application/zip">
                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="fas fa-upload"></i> Upload File
                    </button>
                </div>
            </form>
            
            <div class="upload-progress" id="progressContainer">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <p id="progressText" style="text-align: center; margin-top: 10px;">0% - Mengupload...</p>
            </div>
            
            <div class="mt-3">
                <div class="folder-upload-container">
                    <p><strong>ATAU</strong> upload seluruh folder:</p>
                    <div class="custom-file">
                        <input type="file" id="folderInput" webkitdirectory directory multiple class="custom-file-input">
                        <label class="custom-file-label" for="folderInput">Pilih folder</label>
                    </div>
                    <button type="button" class="btn btn-secondary mt-2" id="uploadFolderBtn">
                        <i class="fas fa-folder-upload"></i> Upload Folder
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter & Pencarian -->
    <div class="admin-actions">
        <div class="media-filter-tabs">
            <a href="?filter=all" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>">Semua Media</a>
            <a href="?filter=images" class="filter-tab <?= $filter === 'images' ? 'active' : '' ?>">Gambar</a>
            <a href="?filter=documents" class="filter-tab <?= $filter === 'documents' ? 'active' : '' ?>">Dokumen</a>
            <a href="?filter=videos" class="filter-tab <?= $filter === 'videos' ? 'active' : '' ?>">Video</a>
        </div>
        
        <div class="media-search">
            <form action="" method="GET">
                <input type="hidden" name="filter" value="<?= $filter ?>">
                <div class="search-input-group">
                    <input type="text" name="search" placeholder="Cari media..." value="<?= htmlspecialchars($searchTerm) ?>" class="search-input">
                    <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Grid Media -->
    <div class="admin-card">
        <div class="card-body">
            <?php if (count($mediaItems) > 0): ?>
                <div class="media-grid">
                    <?php foreach ($mediaItems as $item): ?>
                        <div class="media-item">
                            <div class="media-preview">
                                <?php if (strpos($item['file_type'], 'image/') === 0): ?>
                                    <img src="<?= BASE_URL ?>/uploads/media/<?= $item['thumbnail_path'] ?? $item['file_path'] ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                                <?php else: ?>
                                    <div class="media-icon"><i class="<?= getFileIcon($item['file_type']) ?>"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="media-info">
                                <h4 class="media-title"><?= htmlspecialchars($item['title']) ?></h4>
                                <p class="media-date"><?= date('d M Y', strtotime($item['upload_date'])) ?></p>
                                <div class="media-actions">
                                    <button class="media-action edit" onclick="editMedia(<?= $item['id'] ?>, '<?= addslashes($item['title']) ?>', '<?= addslashes($item['alt_text'] ?? '') ?>', '<?= addslashes($item['description'] ?? '') ?>')" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="media-action copy" onclick="copyUrl('<?= BASE_URL ?>/uploads/media/<?= $item['file_path'] ?>')" title="Salin URL">
                                        <i class="fas fa-link"></i>
                                    </button>
                                    <a href="<?= BASE_URL ?>/uploads/media/<?= $item['file_path'] ?>" class="media-action" download title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="?action=delete&id=<?= $item['id'] ?>" class="media-action delete" onclick="return confirm('Apakah Anda yakin ingin menghapus media ini?')" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <a href="?page=<?= $currentPage - 1 ?>&filter=<?= $filter ?>&search=<?= urlencode($searchTerm) ?>" class="pagination-btn">
                                <i class="fas fa-chevron-left"></i> Sebelumnya
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                            <a href="?page=<?= $i ?>&filter=<?= $filter ?>&search=<?= urlencode($searchTerm) ?>" class="pagination-page <?= $i === $currentPage ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?page=<?= $currentPage + 1 ?>&filter=<?= $filter ?>&search=<?= urlencode($searchTerm) ?>" class="pagination-btn">
                                Selanjutnya <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-photo-video"></i>
                    <h3>Tidak ada media ditemukan</h3>
                    <p>Upload beberapa file media untuk memulai</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="media-modal" id="editModal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <h2>Edit Detail Media</h2>
        <form method="POST" action="">
            <input type="hidden" name="update_media" value="1">
            <input type="hidden" name="media_id" id="editMediaId">
            
            <div class="form-group">
                <label for="editTitle">Judul</label>
                <input type="text" id="editTitle" name="title" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="editAltText">Alt Text</label>
                <input type="text" id="editAltText" name="alt_text" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="editDescription">Deskripsi</label>
                <textarea id="editDescription" name="description" class="form-control" rows="4"></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Fungsi drag and drop
const dropArea = document.getElementById('dropArea');
const fileInput = document.getElementById('fileInput');
const progressContainer = document.getElementById('progressContainer');
const progressFill = document.getElementById('progressFill');
const progressText = document.getElementById('progressText');
const folderInput = document.getElementById('folderInput');
const uploadFolderBtn = document.getElementById('uploadFolderBtn');

// Prevent default drag behaviors
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

// Highlight drop area when item is dragged over it
['dragenter', 'dragover'].forEach(eventName => {
    dropArea.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, unhighlight, false);
});

function highlight() {
    dropArea.classList.add('highlight');
}

function unhighlight() {
    dropArea.classList.remove('highlight');
}

// Handle dropped files
dropArea.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    fileInput.files = files;
    updateFileText(files);
}

dropArea.addEventListener('click', () => {
    fileInput.click();
});

fileInput.addEventListener('change', () => {
    updateFileText(fileInput.files);
});

function updateFileText(files) {
    if (files.length > 0) {
        const fileNames = Array.from(files).map(file => file.name);
        dropArea.querySelector('p').textContent = fileNames.join(', ');
    }
}

// Upload folder functionality
uploadFolderBtn.addEventListener('click', () => {
    folderInput.click();
});

folderInput.addEventListener('change', () => {
    if (folderInput.files.length > 0) {
        processFolderUpload();
    }
});

function processFolderUpload() {
    const files = folderInput.files;
    const totalFiles = files.length;
    
    // Show progress
    progressContainer.style.display = 'block';
    dropArea.style.display = 'none';
    
    let uploadedCount = 0;
    
    // Group files by folder
    const folders = {};
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const path = file.webkitRelativePath;
        const folderName = path.split('/')[0];
        
        if (!folders[folderName]) {
            folders[folderName] = [];
        }
        
        folders[folderName].push(file);
    }
    
    // Upload files by folder
    const folderNames = Object.keys(folders);
    uploadNextFolder(folderNames, folders, 0, totalFiles, uploadedCount);
}

function uploadNextFolder(folderNames, folders, index, totalFiles, uploadedCount) {
    if (index >= folderNames.length) {
        // All folders uploaded
        progressText.textContent = '100% - Semua folder berhasil diupload!';
        setTimeout(() => {
            window.location.reload();
        }, 1000);
        return;
    }
    
    const folderName = folderNames[index];
    const folderFiles = folders[folderName];
    
    // Create FormData
    const formData = new FormData();
    formData.append('folder_name', folderName);
    
    for (let i = 0; i < folderFiles.length; i++) {
        const file = folderFiles[i];
        formData.append('media_files[]', file);
        formData.append('file_paths[]', file.webkitRelativePath);
    }
    
    // Upload folder
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const currentProgress = uploadedCount + e.loaded;
            const percentComplete = Math.round((currentProgress / totalFiles) * 100);
            progressFill.style.width = percentComplete + '%';
            progressText.textContent = percentComplete + '% - Mengupload folder: ' + folderName;
       }
   });
   
   xhr.addEventListener('load', function() {
       uploadedCount += folderFiles.length;
       
       // Update progress
       const percentComplete = Math.round((uploadedCount / totalFiles) * 100);
       progressFill.style.width = percentComplete + '%';
       progressText.textContent = percentComplete + '% - Folder ' + folderName + ' berhasil diupload!';
       
       // Upload next folder
       uploadNextFolder(folderNames, folders, index + 1, totalFiles, uploadedCount);
   });
   
   xhr.addEventListener('error', function() {
       progressText.textContent = 'Upload gagal untuk folder: ' + folderName + '. Melanjutkan ke folder berikutnya...';
       uploadNextFolder(folderNames, folders, index + 1, totalFiles, uploadedCount);
   });
   
   xhr.open('POST', window.location.href, true);
   xhr.setRequestHeader('X-Folder-Upload', 'true');
   xhr.send(formData);
}

// Edit media function
function editMedia(id, title, altText, description) {
    document.getElementById('editMediaId').value = id;
    document.getElementById('editTitle').value = title;
    document.getElementById('editAltText').value = altText || '';
    document.getElementById('editDescription').value = description || '';
    document.getElementById('editModal').style.display = 'block';
}

// Close modal function
function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Close modal when clicking outside
window.addEventListener('click', (e) => {
    const modal = document.getElementById('editModal');
    if (e.target === modal) {
        closeModal();
    }
});

// Copy URL function
function copyUrl(url) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(() => {
            showNotification('URL berhasil disalin ke clipboard!', 'success');
        }).catch(() => {
            fallbackCopyToClipboard(url);
        });
    } else {
        fallbackCopyToClipboard(url);
    }
}

// Fallback copy function for older browsers
function fallbackCopyToClipboard(text) {
    const tempInput = document.createElement('input');
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
    showNotification('URL berhasil disalin ke clipboard!', 'success');
}

// Show notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1100;
        padding: 15px;
        border-radius: 6px;
        background: ${type === 'success' ? '#d4edda' : '#d1ecf1'};
        border: 1px solid ${type === 'success' ? '#c3e6cb' : '#bee5eb'};
        color: ${type === 'success' ? '#155724' : '#0c5460'};
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease forwards';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Form submission with progress tracking
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const files = fileInput.files;
    if (files.length === 0) {
        showNotification('Silakan pilih file untuk diupload', 'warning');
        return;
    }
    
    const formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('media_files[]', files[i]);
    }
    
    const xhr = new XMLHttpRequest();
    
    // Show progress bar
    progressContainer.style.display = 'block';
    dropArea.style.display = 'none';
    
    // Progress tracking
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percentComplete = Math.round((e.loaded / e.total) * 100);
            progressFill.style.width = percentComplete + '%';
            progressText.textContent = percentComplete + '% - Mengupload...';
        }
    });
    
    xhr.addEventListener('load', function() {
        if (xhr.status === 200) {
            progressText.textContent = '100% - Upload selesai!';
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            progressText.textContent = 'Upload gagal. Silakan coba lagi.';
            setTimeout(() => {
                progressContainer.style.display = 'none';
                dropArea.style.display = 'block';
            }, 2000);
        }
    });
    
    xhr.addEventListener('error', function() {
        progressText.textContent = 'Upload gagal. Silakan coba lagi.';
        setTimeout(() => {
            progressContainer.style.display = 'none';
            dropArea.style.display = 'block';
        }, 2000);
    });
    
    xhr.open('POST', window.location.href, true);
    xhr.send(formData);
});

// Search functionality with debounce
let searchTimeout;
const searchInput = document.querySelector('.search-input');

if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 3 || this.value.length === 0) {
                this.form.submit();
            }
        }, 500);
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + U for upload
    if ((e.ctrlKey || e.metaKey) && e.key === 'u') {
        e.preventDefault();
        fileInput.click();
    }
    
    // Escape key to close modal
    if (e.key === 'Escape') {
        closeModal();
    }
    
    // Ctrl/Cmd + F for search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        searchInput.focus();
    }
});

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 500);
        }, 5000);
    });
});

// Lazy loading for images
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}
</script>

<?php
require_once 'templates/footer.php';
ob_end_flush();
?>