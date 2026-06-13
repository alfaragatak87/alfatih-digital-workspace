<?php
ob_start();
$pageTitle = "Data Semester";
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';
require_once 'templates/header.php';

// Process action
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$semester = isset($_GET['semester']) ? (int)$_GET['semester'] : 0;

// Update table structure if 'path' column doesn't exist
try {
    $pdo->query("SELECT path FROM semester_data LIMIT 1");
} catch (PDOException $e) {
    try {
        $pdo->exec("ALTER TABLE `semester_data` ADD `path` VARCHAR(1024) NULL DEFAULT NULL AFTER `original_file_name`");
    } catch (PDOException $e) {
        // Silently fail if it still fails
    }
}


// Delete semester data
if ($action === 'delete' && $id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT file FROM semester_data WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $data = $stmt->fetch();
        
        if ($data && !empty($data['file'])) {
            $filePath = '../uploads/semester/' . $data['file'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        $stmt = $pdo->prepare("DELETE FROM semester_data WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        setAlert('success', 'Data deleted successfully');
        header('Location: ' . BASE_URL . '/admin/kelola_semester.php?semester=' . $semester);
        exit;
    } catch (PDOException $e) {
        setAlert('error', 'Error: ' . $e->getMessage());
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mata_kuliah = trim($_POST['mata_kuliah'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $semester_post = (int)($_POST['semester'] ?? 1);
    
    $errors = [];
    if (empty($mata_kuliah)) $errors[] = 'Course name is required';
    if ($semester_post < 1 || $semester_post > 8) $errors[] = 'Invalid semester';
    
    if (empty($errors)) {
        try {
            $uploadDir = '../uploads/semester/';
            if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

            if (isset($_FILES['file']) && is_array($_FILES['file']['name']) && !empty($_FILES['file']['name'][0])) {
                $fileCount = count($_FILES['file']['name']);
                $uploadedCount = 0;
                $paths = $_POST['paths'] ?? [];

                for ($i = 0; $i < $fileCount; $i++) {
                    if ($_FILES['file']['error'][$i] === UPLOAD_ERR_OK) {
                        $originalFileName = $_FILES['file']['name'][$i];
                        $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                        $newFileName = 'semester_' . $semester_post . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
                        $targetPath = $uploadDir . $newFileName;
                        $filePathInFolder = $paths[$i] ?? $originalFileName;

                        if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $targetPath)) {
                            $stmt = $pdo->prepare("INSERT INTO semester_data (mata_kuliah, deskripsi, semester, file, original_file_name, path) VALUES (:mata_kuliah, :deskripsi, :semester, :file, :original_file_name, :path)");
                            $stmt->bindParam(':mata_kuliah', $mata_kuliah);
                            $stmt->bindParam(':deskripsi', $deskripsi);
                            $stmt->bindParam(':semester', $semester_post);
                            $stmt->bindParam(':file', $newFileName);
                            $stmt->bindParam(':original_file_name', $originalFileName);
                            $stmt->bindParam(':path', $filePathInFolder);
                            $stmt->execute();
                            $uploadedCount++;
                        }
                    }
                }
                if ($uploadedCount > 0) {
                    setAlert('success', $uploadedCount . ' file(s) added successfully to course: ' . htmlspecialchars($mata_kuliah));
                } else {
                    setAlert('error', 'No files were uploaded. Please check your files and try again.');
                }
                header('Location: ' . BASE_URL . '/admin/kelola_semester.php?semester=' . $semester_post);
                exit;
            } else {
                 setAlert('error', 'Please select at least one file to upload.');
                 header('Location: ' . $_SERVER['REQUEST_URI']);
                 exit;
            }

        } catch (PDOException $e) {
            setAlert('error', 'Error: ' . $e->getMessage());
        }
    } else {
        setAlert('error', implode('<br>', $errors));
    }
}

// Get semester data for listing
if ($semester > 0) {
    $stmt = $pdo->prepare("SELECT * FROM semester_data WHERE semester = :semester ORDER BY mata_kuliah, path");
    $stmt->bindParam(':semester', $semester);
    $stmt->execute();
    $semesterData = $stmt->fetchAll();
} else {
    $stmt = $pdo->query("SELECT semester, COUNT(DISTINCT mata_kuliah) as count FROM semester_data GROUP BY semester ORDER BY semester");
    $semesterCounts = $stmt->fetchAll();
}
?>

<div class="admin-content-container">
    <div class="admin-breadcrumb">
        <div class="breadcrumb-container">
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="breadcrumb-item"><i class="fas fa-home"></i> Dashboard</a>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
            <span class="breadcrumb-item active"><?= $pageTitle ?></span>
        </div>
    </div>
    <div class="admin-content-header">
        <h1><i class="fas fa-graduation-cap"></i> <?= $semester > 0 ? 'Semester ' . $semester . ' Data' : 'Semester Data' ?></h1>
        <p><?= $semester > 0 ? 'Manage course materials' : 'Select a semester to manage' ?></p>
    </div>
    
    <?php if ($semester === 0): ?>
        <div class="semester-grid">
            <?php for ($i = 1; $i <= 8; $i++): ?>
                <?php 
                $count = 0;
                foreach ($semesterCounts as $semCount) {
                    if ($semCount['semester'] == $i) {
                        $count = $semCount['count'];
                        break;
                    }
                }
                ?>
                <a href="<?= BASE_URL ?>/admin/kelola_semester.php?semester=<?= $i ?>" class="semester-card">
                    <div class="semester-number"><?= $i ?></div>
                    <h3>Semester <?= $i ?></h3>
                    <p><?= $count ?> course(s)</p>
                </a>
            <?php endfor; ?>
        </div>
    <?php else: ?>
        <div class="admin-actions">
             <a href="<?= BASE_URL ?>/admin/kelola_semester.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Semesters</a>
        </div>
        <div class="admin-form-container">
            <form action="" method="POST" enctype="multipart/form-data" id="courseForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="mata_kuliah">Course Name / Main Folder*</label>
                        <input type="text" id="mata_kuliah" name="mata_kuliah" class="form-control" required placeholder="e.g., Sistem Basis Data">
                    </div>
                    <div class="form-group">
                        <label for="semester">Semester*</label>
                        <select id="semester" name="semester" class="form-control" required>
                            <?php for ($i = 1; $i <= 8; $i++): ?>
                                <option value="<?= $i ?>" <?= $semester == $i ? 'selected' : '' ?>>Semester <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Description (Optional)</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3" placeholder="Short description for all files in this upload batch"></textarea>
                </div>
                <div class="form-group">
                    <label for="file">Files / Folder*</label>
                     <div class="d-flex align-items-center gap-2">
                        <div class="custom-file flex-grow-1">
                            <input type="file" id="file" name="file[]" class="custom-file-input" required multiple>
                            <label class="custom-file-label" for="file">Choose file(s)</label>
                        </div>
                        <button type="button" id="folderBtn" class="btn btn-secondary"><i class="fas fa-folder"></i> Choose Folder</button>
                    </div>
                    <input type="file" id="folderInput" webkitdirectory directory multiple style="display: none;">
                    <div id="file-preview-wrapper" class="file-preview-wrapper" style="display: none; margin-top: 15px;"></div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload Files</button>
                </div>
            </form>
        </div>

        <div class="admin-card" style="margin-top: 2rem;">
            <div class="card-header"><h2>Existing Files for Semester <?= $semester ?></h2></div>
            <div class="card-body">
                <?php if (count($semesterData) > 0): ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead><tr><th>Course</th><th>File Path</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php foreach ($semesterData as $data): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($data['mata_kuliah']) ?></td>
                                        <td><i class="fas fa-file-alt file-icon-sm"></i> <?= htmlspecialchars($data['path'] ?? $data['original_file_name']) ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/admin/kelola_semester.php?action=delete&id=<?= $data['id'] ?>&semester=<?= $semester ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this file?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="no-data">No data found for Semester <?= $semester ?>.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .semester-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
    .semester-card { background-color: var(--dark-card); border-radius: 10px; padding: 30px 20px; text-align: center; transition: all 0.3s ease; text-decoration: none; color: var(--text-primary); position: relative; overflow: hidden; }
    .semester-card:hover { transform: translateY(-10px); box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); background-color: var(--dark-surface); }
    .semester-card:hover .semester-number { background-color: var(--primary-color); color: var(--dark-bg); }
    .semester-number { width: 60px; height: 60px; border-radius: 50%; background-color: rgba(0, 229, 255, 0.1); color: var(--primary-color); display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 24px; font-weight: 700; transition: all 0.3s ease; }
    .semester-card h3 { margin-bottom: 10px; }
    .semester-card p { color: var(--text-secondary); margin-bottom: 0; }
    .file-link { display: inline-flex; align-items: center; color: var(--primary-color); }
    .file-link i { margin-right: 5px; }
    .d-flex { display: flex; }
    .align-items-center { align-items: center; }
    .gap-2 { gap: 10px; }
    .flex-grow-1 { flex-grow: 1; }
    .file-preview-wrapper { border: 1px dashed var(--primary-color); border-radius: 8px; padding: 15px; background-color: var(--dark-surface); max-height: 250px; overflow-y: auto; }
    .file-preview-list { width: 100%; }
    .file-preview-item { display: flex; align-items: center; gap: 15px; padding: 10px; border-radius: 6px; }
    .file-preview-item .file-icon { font-size: 24px; color: var(--text-secondary); width: 30px; text-align: center; }
    .file-preview-info p { margin: 0; color: var(--text-primary); font-weight: 500; }
    .file-preview-info small { color: var(--text-muted); font-size: 0.8rem; }
    .preview-folder-group { margin-bottom: 15px; }
    .preview-folder-header { font-weight: 600; color: var(--primary-color); margin-bottom: 8px; padding-bottom: 5px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center; gap: 8px; }
    .file-icon-sm { margin-right: 8px; color: var(--text-secondary); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const folderBtn = document.getElementById('folderBtn');
    const folderInput = document.getElementById('folderInput');
    const fileInput = document.getElementById('file');
    const fileLabel = document.querySelector('.custom-file-label[for="file"]');
    const filePreviewWrapper = document.getElementById('file-preview-wrapper');
    const courseNameInput = document.getElementById('mata_kuliah');
    const form = document.getElementById('courseForm');

    function getFileIconClass(fileName) {
        const extension = fileName.split('.').pop().toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) return 'fa-file-image';
        if (extension === 'pdf') return 'fa-file-pdf';
        if (['doc', 'docx'].includes(extension)) return 'fa-file-word';
        if (['xls', 'xlsx'].includes(extension)) return 'fa-file-excel';
        if (['ppt', 'pptx'].includes(extension)) return 'fa-file-powerpoint';
        if (['zip', 'rar'].includes(extension)) return 'fa-file-archive';
        return 'fa-file-alt';
    }

    function updatePreview(files) {
        if (!files || files.length === 0) {
            if (filePreviewWrapper) filePreviewWrapper.style.display = 'none';
            fileLabel.textContent = 'Choose file(s)';
            return;
        }

        fileInput.files = files;
        fileLabel.textContent = `${files.length} file(s) selected`;

        const groupedFiles = {};
        Array.from(files).forEach((file) => {
            const pathParts = file.webkitRelativePath ? file.webkitRelativePath.split('/') : [file.name];
            const folderPath = pathParts.length > 1 ? pathParts.slice(0, -1).join('/') : 'Root Folder';
            if (!groupedFiles[folderPath]) {
                groupedFiles[folderPath] = [];
            }
            groupedFiles[folderPath].push(file);
        });

        let previewListHTML = '<div class="file-preview-list">';
        for (const folderPath in groupedFiles) {
            previewListHTML += `<div class="preview-folder-group">
                                  <div class="preview-folder-header"><i class="fas fa-folder-open"></i> ${folderPath}</div>`;
            groupedFiles[folderPath].forEach(file => {
                const fileSize = (file.size / 1024).toFixed(2) + ' KB';
                const iconClass = getFileIconClass(file.name);
                previewListHTML += `<div class="file-preview-item">
                                        <div class="file-icon"><i class="fas ${iconClass}"></i></div>
                                        <div class="file-preview-info">
                                            <p>${file.name}</p>
                                            <small>${fileSize}</small>
                                        </div>
                                    </div>`;
            });
            previewListHTML += `</div>`;
        }
        previewListHTML += '</div>';
        
        filePreviewWrapper.innerHTML = previewListHTML;
        filePreviewWrapper.style.display = 'block';
    }
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const files = fileInput.files;
            if (files.length > 0) {
                form.querySelectorAll('input[name="paths[]"]').forEach(input => input.remove());
                Array.from(files).forEach(file => {
                    if (file.webkitRelativePath) {
                        const pathInput = document.createElement('input');
                        pathInput.type = 'hidden';
                        pathInput.name = 'paths[]';
                        pathInput.value = file.webkitRelativePath;
                        form.appendChild(pathInput);
                    }
                });
            }
        });
    }

    if (folderBtn) {
        folderBtn.addEventListener('click', () => {
            folderInput.click();
        });
    }

    if (folderInput) {
        folderInput.addEventListener('change', function(e) {
            updatePreview(e.target.files);
            if (e.target.files.length > 0 && e.target.files[0].webkitRelativePath) {
                courseNameInput.value = e.target.files[0].webkitRelativePath.split('/')[0];
            }
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            updatePreview(e.target.files);
        });
    }
});
</script>

<?php 
require_once 'templates/footer.php';
ob_end_flush();
?>
