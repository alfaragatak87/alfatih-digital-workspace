<?php
ob_start();
$pageTitle = "Kelola Artikel";
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';
require_once 'templates/header.php';



// Process action
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Delete article
if ($action === 'delete' && $id > 0) {
    try {
        // Get article image filename
        $stmt = $pdo->prepare("SELECT gambar_unggulan FROM artikel WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $article = $stmt->fetch();
        
        if ($article) {
            // Delete image file
            $imagePath = '../uploads/articles/' . $article['gambar_unggulan'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
            
            // Delete article from database
            $stmt = $pdo->prepare("DELETE FROM artikel WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            setAlert('success', 'Article deleted successfully');
        } else {
            setAlert('error', 'Article not found');
        }
    } catch (PDOException $e) {
        setAlert('error', 'Error: ' . $e->getMessage());
    }
    
    header('Location: ' . BASE_URL . '/admin/kelola_artikel.php');
    exit;
}

// Process form submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $id_kategori = (int)($_POST['id_kategori'] ?? 0);
    $konten = trim($_POST['konten'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $article_id = isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0;
    
    // Generate slug from title
    $slug = generateSlug($judul);
    
    // Validate inputs
    $errors = [];
    
    if (empty($judul)) {
        $errors[] = 'Title is required';
    }
    
    if ($id_kategori <= 0) {
        $errors[] = 'Category is required';
    }
    
    if (empty($konten)) {
        $errors[] = 'Content is required';
    }
    
    // Check if slug exists (for new articles or when changing title)
    if ($article_id === 0 || (isset($_POST['old_title']) && $_POST['old_title'] !== $judul)) {
        $stmt = $pdo->prepare("SELECT id FROM artikel WHERE slug = :slug AND id != :id");
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':id', $article_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // Add timestamp to make slug unique
            $slug = $slug . '-' . time();
        }
    }
    
    // If no errors, process form
    if (empty($errors)) {
        try {
            // Upload image if provided
            $gambar_unggulan = '';
            
            if ($article_id > 0) {
                // Get current image if editing
                $stmt = $pdo->prepare("SELECT gambar_unggulan FROM artikel WHERE id = :id");
                $stmt->bindParam(':id', $article_id);
                $stmt->execute();
                $currentArticle = $stmt->fetch();
                $gambar_unggulan = $currentArticle['gambar_unggulan'] ?? '';
            }
            
            if (!empty($_FILES['gambar_unggulan']['name'])) {
                $uploadDir = '../uploads/articles/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileExtension = pathinfo($_FILES['gambar_unggulan']['name'], PATHINFO_EXTENSION);
                $newFileName = 'article_' . time() . '.' . $fileExtension;
                $targetPath = $uploadDir . $newFileName;
                
                if (move_uploaded_file($_FILES['gambar_unggulan']['tmp_name'], $targetPath)) {
                    // Delete old image if exists and updating
                    if ($article_id > 0 && !empty($gambar_unggulan) && file_exists($uploadDir . $gambar_unggulan)) {
                        unlink($uploadDir . $gambar_unggulan);
                    }
                    $gambar_unggulan = $newFileName;
                } else {
                    setAlert('error', 'Failed to upload image');
                    header('Location: ' . BASE_URL . '/admin/kelola_artikel.php');
                    exit;
                }
            } elseif ($article_id === 0 && empty($gambar_unggulan)) {
                // Require image for new articles
                setAlert('error', 'Featured image is required');
                header('Location: ' . BASE_URL . '/admin/kelola_artikel.php?action=add');
                exit;
            }
            
            if ($article_id > 0) {
                // Update existing article
                $stmt = $pdo->prepare("UPDATE artikel SET judul = :judul, id_kategori = :id_kategori, konten = :konten, slug = :slug, is_featured = :is_featured" . (!empty($gambar_unggulan) ? ", gambar_unggulan = :gambar_unggulan" : "") . " WHERE id = :id");
                $stmt->bindParam(':id', $article_id);
            } else {
                // Add new article
                $stmt = $pdo->prepare("INSERT INTO artikel (judul, id_kategori, konten, gambar_unggulan, slug, is_featured) VALUES (:judul, :id_kategori, :konten, :gambar_unggulan, :slug, :is_featured)");
            }
            
            $stmt->bindParam(':judul', $judul);
            $stmt->bindParam(':id_kategori', $id_kategori);
            $stmt->bindParam(':konten', $konten);
            $stmt->bindParam(':slug', $slug);
            $stmt->bindParam(':is_featured', $is_featured);
            
            if ($article_id === 0 || !empty($gambar_unggulan)) {
                $stmt->bindParam(':gambar_unggulan', $gambar_unggulan);
            }
            
            $stmt->execute();
            
            setAlert('success', ($article_id > 0 ? 'Article updated' : 'Article added') . ' successfully');
            header('Location: ' . BASE_URL . '/admin/kelola_artikel.php');
            exit;
        } catch (PDOException $e) {
            setAlert('error', 'Error: ' . $e->getMessage());
        }
    } else {
        setAlert('error', implode('<br>', $errors));
    }
}

// Get article data if editing
$editArticle = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM artikel WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $editArticle = $stmt->fetch();
    
    if (!$editArticle) {
        setAlert('error', 'Article not found');
        header('Location: ' . BASE_URL . '/admin/kelola_artikel.php');
        exit;
    }
}

// Get all articles for listing
$stmt = $pdo->query("SELECT a.*, k.nama_kategori 
                     FROM artikel a 
                     JOIN kategori k ON a.id_kategori = k.id 
                     ORDER BY a.tanggal_dibuat DESC");
$articles = $stmt->fetchAll();

// Get categories for dropdown
$stmt = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori");
$categories = $stmt->fetchAll();
?>


<div class="admin-content-container">
    <!-- Tambahkan di awal semua halaman admin (seperti kelola_proyek.php, kelola_artikel.php, dll) -->

<div class="admin-breadcrumb">
    <div class="breadcrumb-container">
        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="breadcrumb-item"><i class="fas fa-home"></i> Dashboard</a>
        <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
        <span class="breadcrumb-item active"><?= $pageTitle ?></span>
    </div>
    <div class="breadcrumb-actions">
        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        <a href="<?= BASE_URL ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fas fa-external-link-alt"></i> View Site</a>
    </div>
</div>
    <div class="admin-content-header">
        <h1>
            <i class="fas fa-newspaper"></i> 
            <?= ($action === 'add' || $action === 'edit') ? ($action === 'add' ? 'Add New Article' : 'Edit Article') : 'Manage Articles' ?>
        </h1>
        <p><?= ($action === 'add' || $action === 'edit') ? 'Enter article details below' : 'View, edit or delete your articles' ?></p>
    </div>
    
    <?php if ($action === 'add' || $action === 'edit'): ?>
        <!-- Add/Edit Article Form -->
        <div class="admin-form-container">
            <form action="" method="POST" enctype="multipart/form-data">
                <?php if ($action === 'edit'): ?>
                    <input type="hidden" name="article_id" value="<?= $editArticle['id'] ?>">
                    <input type="hidden" name="old_title" value="<?= $editArticle['judul'] ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="judul">Article Title*</label>
                        <input type="text" id="judul" name="judul" class="form-control" value="<?= $editArticle['judul'] ?? '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="id_kategori">Category*</label>
                        <select id="id_kategori" name="id_kategori" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= (isset($editArticle['id_kategori']) && $editArticle['id_kategori'] == $category['id']) ? 'selected' : '' ?>>
                                    <?= $category['nama_kategori'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="konten">Content*</label>
                    <textarea id="konten" name="konten" class="form-control" rows="15" required><?= $editArticle['konten'] ?? '' ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="gambar_unggulan">Featured Image<?= ($action === 'add') ? '*' : '' ?></label>
                    <div class="custom-file">
                        <input type="file" id="gambar_unggulan" name="gambar_unggulan" class="custom-file-input" accept="image/*" <?= ($action === 'add') ? 'required' : '' ?>>
                        <label class="custom-file-label" for="gambar_unggulan">Choose file</label>
                    </div>
                    <?php if ($action === 'edit' && !empty($editArticle['gambar_unggulan'])): ?>
                        <div class="image-preview">
                            <img src="<?= BASE_URL ?>/uploads/articles/<?= $editArticle['gambar_unggulan'] ?>" alt="Featured Image">
                        </div>
                        <p class="form-text">Leave empty to keep current image</p>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= (isset($editArticle['is_featured']) && $editArticle['is_featured'] == 1) ? 'checked' : '' ?>>
                        <label for="is_featured">Feature this article (show on homepage)</label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= ($action === 'edit') ? 'Update' : 'Save' ?> Article</button>
                    <a href="<?= BASE_URL ?>/admin/kelola_artikel.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Cancel</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <!-- Articles List -->
        <div class="admin-actions">
            <a href="<?= BASE_URL ?>/admin/kelola_artikel.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Article</a>
        </div>
        
        <div class="admin-card">
            <div class="card-body">
                <?php if (count($articles) > 0): ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th>Featured</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($articles as $article): ?>
                                    <tr>
                                        <td>
                                            <div class="table-image">
                                                <img src="<?= BASE_URL ?>/uploads/articles/<?= $article['gambar_unggulan'] ?>" alt="<?= $article['judul'] ?>">
                                            </div>
                                        </td>
                                        <td><?= $article['judul'] ?></td>
                                        <td><?= $article['nama_kategori'] ?></td>
                                        <td><?= date('d M Y', strtotime($article['tanggal_dibuat'])) ?></td>
                                        <td>
                                            <span class="status-badge <?= $article['is_featured'] ? 'status-active' : 'status-inactive' ?>">
                                                <?= $article['is_featured'] ? 'Yes' : 'No' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/admin/kelola_artikel.php?action=edit&id=<?= $article['id'] ?>" class="action-btn edit-btn" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/admin/kelola_artikel.php?action=delete&id=<?= $article['id'] ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this article?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="no-data">No articles found. <a href="<?= BASE_URL ?>/admin/kelola_artikel.php?action=add">Add your first article</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php 
require_once 'templates/footer.php';
ob_end_flush();
?>