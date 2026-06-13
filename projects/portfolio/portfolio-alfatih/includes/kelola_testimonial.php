<?php
$pageTitle = "Kelola Testimonial";
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';
require_once 'templates/header.php';

// Check for action (approve, reject, delete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = (int)$_GET['id'];
    
    try {
        switch ($action) {
            case 'approve':
                $stmt = $pdo->prepare("UPDATE testimonials SET aktif = 1 WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                setAlert('success', 'Testimonial approved successfully');
                break;
                
            case 'reject':
                $stmt = $pdo->prepare("UPDATE testimonials SET aktif = 0 WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                setAlert('success', 'Testimonial rejected');
                break;
                
            case 'delete':
                // Get photo filename first
                $stmt = $pdo->prepare("SELECT foto FROM testimonials WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $testimonial = $stmt->fetch();
                
                // Delete photo if exists
                if ($testimonial && !empty($testimonial['foto'])) {
                    $photoPath = '../uploads/testimonials/' . $testimonial['foto'];
                    if (file_exists($photoPath)) {
                        unlink($photoPath);
                    }
                }
                
                // Delete testimonial
                $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                setAlert('success', 'Testimonial deleted successfully');
                break;
        }
    } catch (PDOException $e) {
        setAlert('error', 'Error: ' . $e->getMessage());
    }
    
    header('Location: ' . BASE_URL . '/admin/kelola_testimonial.php');
    exit;
}

// Get testimonials
$stmt = $pdo->query("SELECT * FROM testimonials ORDER BY aktif ASC, id DESC");
$testimonials = $stmt->fetchAll();
?>

<div class="admin-content-container">
    <div class="admin-content-header">
        <h1><i class="fas fa-comment-dots"></i> Kelola Testimonial</h1>
        <p>Manage client testimonials</p>
    </div>
    
    <!-- Testimonials List -->
    <div class="admin-card">
        <div class="card-header">
            <h2>All Testimonials</h2>
        </div>
        <div class="card-body">
            <?php if (count($testimonials) > 0): ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Rating</th>
                                <th>Testimonial</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($testimonials as $testimonial): ?>
                                <tr>
                                    <td><?= $testimonial['nama'] ?></td>
                                    <td><?= $testimonial['posisi'] ?><?= !empty($testimonial['perusahaan']) ? ', ' . $testimonial['perusahaan'] : '' ?></td>
                                    <td>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?= ($i <= $testimonial['rating']) ? 'star-filled' : 'star-empty' ?>"></i>
                                        <?php endfor; ?>
                                    </td>
                                    <td><?= truncateText($testimonial['testimonial'], 100) ?></td>
                                    <td>
                                        <span class="status-badge <?= $testimonial['aktif'] ? 'status-active' : 'status-inactive' ?>">
                                            <?= $testimonial['aktif'] ? 'Published' : 'Pending' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!$testimonial['aktif']): ?>
                                            <a href="?action=approve&id=<?= $testimonial['id'] ?>" class="action-btn approve-btn" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="?action=reject&id=<?= $testimonial['id'] ?>" class="action-btn reject-btn" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="?action=delete&id=<?= $testimonial['id'] ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this testimonial?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-data">No testimonials found.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Add New Testimonial Form -->
    <div class="admin-card mt-4">
        <div class="card-header">
            <h2>Add New Testimonial</h2>
        </div>
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nama">Name</label>
                        <input type="text" id="nama" name="nama" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="posisi">Position</label>
                        <input type="text" id="posisi" name="posisi" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="perusahaan">Company</label>
                        <input type="text" id="perusahaan" name="perusahaan" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="rating">Rating</label>
                        <select id="rating" name="rating" class="form-control">
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="testimonial">Testimonial</label>
                    <textarea id="testimonial" name="testimonial" class="form-control" rows="5" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="foto">Photo</label>
                    <div class="custom-file">
                        <input type="file" id="foto" name="foto" class="custom-file-input" accept="image/*">
                        <label class="custom-file-label" for="foto">Choose file</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="aktif" name="aktif" value="1" checked>
                        <label for="aktif">Publish immediately</label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Add Testimonial</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>