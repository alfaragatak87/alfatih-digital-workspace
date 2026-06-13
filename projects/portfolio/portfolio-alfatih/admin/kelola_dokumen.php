<?php
ob_start();
$pageTitle = "Kelola Dokumen";
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';
require_once 'templates/header.php';

// Process action
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Delete document
if ($action === 'delete' && $id > 0) {
    try {
        // Get document filename
        $stmt = $pdo->prepare("SELECT file FROM dokumen WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $document = $stmt->fetch();
        
        if ($document) {
            // Delete file
            $filePath = '../uploads/documents/' . $document['file'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Delete document from database
            $stmt = $pdo->prepare("DELETE FROM dokumen WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            setAlert('success', 'Document deleted successfully');
        } else {
            setAlert('error', 'Document not found');
        }
    } catch (PDOException $e) {
        setAlert('error', 'Error: ' . $e->getMessage());
    }
    
    header('Location: ' . BASE_URL . '/admin/kelola_dokumen.php');
    exit;
}

// Process form submission (Add)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $semester = isset($_POST['semester']) ? (int)$_POST['semester'] : 0;
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    
    // Validate inputs
    $errors = [];
    
    if (empty($nama)) {
        $errors[] = 'Document name is required';
    }
    
    if (empty($kategori)) {
        $errors[] = 'Category is required';
    }
    
    if (empty($_FILES['file']['name'])) {
        $errors[] = 'File is required';
    }
    
    // If no errors, process form
    if (empty($errors)) {
        try {
            // Upload file
            $uploadDir = '../uploads/documents/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $originalFileName = $_FILES['file']['name'];
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $newFileName = 'document_' . time() . '.' . $fileExtension;
            $targetPath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                // Insert document into database
                $stmt = $pdo->prepare("INSERT INTO dokumen (nama, file, kategori, semester, deskripsi) VALUES (:nama, :file, :kategori, :semester, :deskripsi)");
                $stmt->bindParam(':nama', $nama);
                $stmt->bindParam(':file', $newFileName);
                $stmt->bindParam(':kategori', $kategori);
                $stmt->bindParam(':semester', $semester);
                $stmt->bindParam(':deskripsi', $deskripsi);
                $stmt->execute();
                
                setAlert('success', 'Document uploaded successfully');
                header('Location: ' . BASE_URL . '/admin/kelola_dokumen.php');
                exit;
            } else {
                setAlert('error', 'Failed to upload file');
            }
        } catch (PDOException $e) {
            setAlert('error', 'Error: ' . $e->getMessage());
        }
    } else {
        setAlert('error', implode('<br>', $errors));
    }
}

// Get all documents
$stmt = $pdo->query("SELECT * FROM dokumen ORDER BY tanggal_upload DESC");
$documents = $stmt->fetchAll();
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
            <i class="fas fa-file-alt"></i> 
            <?= ($action === 'add') ? 'Upload New Document' : 'Manage Documents' ?>
        </h1>
        <p><?= ($action === 'add') ? 'Upload documents, assignments, or your CV' : 'View or delete your documents' ?></p>
    </div>
    
    <?php if ($action === 'add'): ?>
        <!-- Upload Document Form -->
        <div class="admin-form-container">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nama">Document Name*</label>
                        <input type="text" id="nama" name="nama" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="kategori">Category*</label>
                        <select id="kategori" name="kategori" class="form-control" required>
                            <option value="">Select Category</option>
                            <option value="Tugas">Assignment</option>
                            <option value="Sertifikat">Certificate</option>
                            <option value="CV">CV</option>
                            <option value="Lainnya">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="semester">Semester (if applicable)</label>
                        <select id="semester" name="semester" class="form-control">
                            <option value="0">Not Applicable</option>
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                            <option value="3">Semester 3</option>
                            <option value="4">Semester 4</option>
                            <option value="5">Semester 5</option>
                            <option value="6">Semester 6</option>
                            <option value="7">Semester 7</option>
                            <option value="8">Semester 8</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="file">File*</label>
                        <div class="custom-file">
                            <input type="file" id="file" name="file" class="custom-file-input" required>
                            <label class="custom-file-label" for="file">Choose file</label>
                        </div>
                        <p class="form-text">Supported file types: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, etc.</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="deskripsi">Description</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload Document</button>
                    <a href="<?= BASE_URL ?>/admin/kelola_dokumen.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Cancel</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <!-- Documents List -->
        <div class="admin-actions">
            <a href="<?= BASE_URL ?>/admin/kelola_dokumen.php?action=add" class="btn btn-primary"><i class="fas fa-upload"></i> Upload New Document</a>
        </div>
        
        <div class="admin-card">
            <div class="card-body">
                <?php if (count($documents) > 0): ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Semester</th>
                                    <th>Upload Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documents as $document): ?>
                                    <tr>
                                        <td><?= $document['nama'] ?></td>
                                        <td><?= $document['kategori'] ?></td>
                                        <td><?= $document['semester'] > 0 ? 'Semester ' . $document['semester'] : 'N/A' ?></td>
                                        <td><?= date('d M Y', strtotime($document['tanggal_upload'])) ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/uploads/documents/<?= $document['file'] ?>" class="action-btn view-btn" title="Download" target="_blank">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/admin/kelola_dokumen.php?action=delete&id=<?= $document['id'] ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this document?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="no-data">No documents found. <a href="<?= BASE_URL ?>/admin/kelola_dokumen.php?action=add">Upload your first document</a>.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php 
require_once 'templates/footer.php';
ob_end_flush();
?>