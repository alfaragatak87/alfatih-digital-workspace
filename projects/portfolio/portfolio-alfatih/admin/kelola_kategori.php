<?php
ob_start();
$pageTitle = "Kelola Kategori";
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';
require_once 'templates/header.php';

// Process action
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Delete category
if ($action === 'delete' && $id > 0) {
    try {
        // Check if category is used in articles
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM artikel WHERE id_kategori = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            setAlert('error', 'Cannot delete category. It is used by ' . $count . ' article(s).');
        } else {
            // Delete category
            $stmt = $pdo->prepare("DELETE FROM kategori WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            setAlert('success', 'Category deleted successfully');
        }
    } catch (PDOException $e) {
        setAlert('error', 'Error: ' . $e->getMessage());
    }
    
    header('Location: ' . BASE_URL . '/admin/kelola_kategori.php');
    exit;
}

// Process form submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    
    // Validate inputs
    $errors = [];
    
    if (empty($nama_kategori)) {
        $errors[] = 'Category name is required';
    }
    
    // If no errors, process form
    if (empty($errors)) {
        try {
            // Check if category name already exists
            $stmt = $pdo->prepare("SELECT id FROM kategori WHERE nama_kategori = :nama_kategori AND id != :id");
            $stmt->bindParam(':nama_kategori', $nama_kategori);
            $stmt->bindParam(':id', $category_id);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                setAlert('error', 'Category name already exists');
            } else {
                if ($category_id > 0) {
                    // Update existing category
                    $stmt = $pdo->prepare("UPDATE kategori SET nama_kategori = :nama_kategori WHERE id = :id");
                    $stmt->bindParam(':id', $category_id);
                    $stmt->bindParam(':nama_kategori', $nama_kategori);
                    $stmt->execute();
                    
                    setAlert('success', 'Category updated successfully');
                } else {
                    // Add new category
                    $stmt = $pdo->prepare("INSERT INTO kategori (nama_kategori) VALUES (:nama_kategori)");
                    $stmt->bindParam(':nama_kategori', $nama_kategori);
                    $stmt->execute();
                    
                    setAlert('success', 'Category added successfully');
                }
                
                header('Location: ' . BASE_URL . '/admin/kelola_kategori.php');
                exit;
            }
        } catch (PDOException $e) {
            setAlert('error', 'Error: ' . $e->getMessage());
        }
    } else {
        setAlert('error', implode('<br>', $errors));
    }
}

// Get category data if editing
$editCategory = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM kategori WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $editCategory = $stmt->fetch();
    
    if (!$editCategory) {
        setAlert('error', 'Category not found');
        header('Location: ' . BASE_URL . '/admin/kelola_kategori.php');
        exit;
    }
}

// Get all categories
$stmt = $pdo->query("SELECT k.*, (SELECT COUNT(*) FROM artikel WHERE id_kategori = k.id) AS article_count FROM kategori k ORDER BY k.nama_kategori");
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
            <i class="fas fa-tags"></i> 
            <?= ($action === 'add' || $action === 'edit') ? ($action === 'add' ? 'Add New Category' : 'Edit Category') : 'Manage Categories' ?>
        </h1>
        <p><?= ($action === 'add' || $action === 'edit') ? 'Enter category details below' : 'View, edit or delete your categories' ?></p>
    </div>
    
    <div class="admin-grid">
        <div class="admin-main">
            <!-- Categories List -->
            <div class="admin-card">
                <div class="card-header">
                    <h2>All Categories</h2>
                </div>
                <div class="card-body">
                    <?php if (count($categories) > 0): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Category Name</th>
                                        <th>Articles</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?= $category['nama_kategori'] ?></td>
                                            <td><?= $category['article_count'] ?></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/admin/kelola_kategori.php?action=edit&id=<?= $category['id'] ?>" class="action-btn edit-btn" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>/admin/kelola_kategori.php?action=delete&id=<?= $category['id'] ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this category?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="no-data">No categories found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="admin-sidebar">
            <!-- Add/Edit Category Form -->
            <div class="admin-card">
                <div class="card-header">
                    <h2><?= ($action === 'edit') ? 'Edit Category' : 'Add New Category' ?></h2>
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <?php if ($action === 'edit'): ?>
                            <input type="hidden" name="category_id" value="<?= $editCategory['id'] ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="nama_kategori">Category Name*</label>
                            <input type="text" id="nama_kategori" name="nama_kategori" class="form-control" value="<?= $editCategory['nama_kategori'] ?? '' ?>" required>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= ($action === 'edit') ? 'Update' : 'Add' ?> Category</button>
                            <?php if ($action === 'edit'): ?>
                                <a href="<?= BASE_URL ?>/admin/kelola_kategori.php" class="btn btn-outline"><i class="fas fa-times"></i> Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once 'templates/footer.php';
ob_end_flush();
?>