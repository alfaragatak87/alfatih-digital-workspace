<?php
$pageTitle = "Data Semester";
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';
require_once '../templates/header.php';

// Debug koneksi database
if (!isset($pdo)) {
    die("Database connection failed");
}

// Get semester parameter
$semester = isset($_GET['semester']) ? (int)$_GET['semester'] : 0;

$courses = [];
if ($semester > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM semester_data WHERE semester = :semester ORDER BY mata_kuliah, path");
        $stmt->bindParam(':semester', $semester);
        $stmt->execute();
        $semesterData = $stmt->fetchAll();

        // Group files by course name (mata_kuliah)
        foreach ($semesterData as $data) {
            $courses[$data['mata_kuliah']][] = $data;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Get counts by semester for navigation
$semesterCounts = [];
try {
    $stmt = $pdo->query("SELECT semester, COUNT(DISTINCT mata_kuliah) as count FROM semester_data GROUP BY semester ORDER BY semester");
    while ($row = $stmt->fetch()) {
        $semesterCounts[$row['semester']] = $row['count'];
    }
} catch (PDOException $e) {
    echo "Error getting semester counts: " . $e->getMessage();
}

// Helper function to get file icon
function getFileIconClass($fileName) {
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    switch ($extension) {
        case 'pdf': return 'fa-file-pdf';
        case 'doc': case 'docx': return 'fa-file-word';
        case 'xls': case 'xlsx': return 'fa-file-excel';
        case 'ppt': case 'pptx': return 'fa-file-powerpoint';
        case 'zip': case 'rar': return 'fa-file-archive';
        case 'jpg': case 'jpeg': case 'png': case 'gif': return 'fa-file-image';
        case 'txt': return 'fa-file-alt';
        default: return 'fa-file';
    }
}

// Get file size in readable format
function getFileSize($filePath) {
    if (!file_exists($filePath)) return 'N/A';
    $size = filesize($filePath);
    $units = ['B', 'KB', 'MB', 'GB'];
    $i = 0;
    while ($size >= 1024 && $i < count($units) - 1) {
        $size /= 1024;
        $i++;
    }
    return round($size, 2) . ' ' . $units[$i];
}

// Get last modified date
function getLastModified($filePath) {
    if (!file_exists($filePath)) return 'N/A';
    return date('M j, Y, g:i A', filemtime($filePath));
}

// Function to build a tree structure from file paths
function buildFileTree(array $files) {
    $tree = [];
    foreach ($files as $file) {
        $path = $file['path'] ?? $file['original_file_name'];
        $pathParts = explode('/', $path);
        $currentLevel = &$tree;
        
        foreach ($pathParts as $i => $part) {
            if (empty($part)) continue;
            
            if (!isset($currentLevel[$part])) {
                if ($i === count($pathParts) - 1) {
                    $currentLevel[$part] = ['type' => 'file', 'data' => $file];
                } else {
                    $currentLevel[$part] = ['type' => 'folder', 'children' => []];
                }
            }
            
            if (isset($currentLevel[$part]['children'])) {
                $currentLevel = &$currentLevel[$part]['children'];
            }
        }
    }
    return $tree;
}

// Recursive function to render the file tree as HTML (file manager style)
function renderFileTree(array $tree, $level = 0) {
    $html = '';
    
    foreach ($tree as $name => $node) {
        if ($node['type'] === 'folder') {
            $html .= '<div class="file-item folder-item" data-level="' . $level . '">';
            $html .= '<div class="item-row">';
            $html .= '<div class="item-name">';
            $html .= '<span class="expand-icon"><i class="fas fa-chevron-right"></i></span>';
            $html .= '<i class="fas fa-folder folder-icon"></i>';
            $html .= '<span class="name-text">' . htmlspecialchars($name) . '</span>';
            $html .= '</div>';
            $html .= '<div class="item-size">-</div>';
            $html .= '<div class="item-date">-</div>';
            $html .= '<div class="item-type">Folder</div>';
            $html .= '<div class="item-actions"></div>';
            $html .= '</div>';
            $html .= '<div class="folder-content">';
            $html .= renderFileTree($node['children'], $level + 1);
            $html .= '</div>';
            $html .= '</div>';
        } else {
            $fileData = $node['data'];
            $filePath = '../uploads/semester/' . $fileData['file'];
            $downloadLink = BASE_URL . '/uploads/semester/' . htmlspecialchars($fileData['file']);
            $fileExists = file_exists($filePath);
            $extension = strtolower(pathinfo($fileData['original_file_name'], PATHINFO_EXTENSION));
            
            $html .= '<div class="file-item file-row ' . ($fileExists ? '' : 'missing-file') . '" data-level="' . $level . '">';
            $html .= '<div class="item-row">';
            $html .= '<div class="item-name">';
            $html .= '<span class="expand-icon"></span>';
            $html .= '<i class="fas ' . getFileIconClass($fileData['original_file_name']) . ' file-icon"></i>';
            $html .= '<span class="name-text">' . htmlspecialchars($fileData['original_file_name']) . '</span>';
            $html .= '</div>';
            $html .= '<div class="item-size">' . getFileSize($filePath) . '</div>';
            $html .= '<div class="item-date">' . getLastModified($filePath) . '</div>';
            $html .= '<div class="item-type">' . strtoupper($extension) . '</div>';
            $html .= '<div class="item-actions">';
            
            if ($fileExists) {
                $html .= '<a href="' . $downloadLink . '" class="action-btn download-btn" title="Download" download>';
                $html .= '<i class="fas fa-download"></i>';
                $html .= '</a>';
            } else {
                $html .= '<span class="action-btn disabled" title="File not found">';
                $html .= '<i class="fas fa-exclamation-triangle"></i>';
                $html .= '</span>';
            }
            
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
    }
    
    return $html;
}

// Definisi fungsi multilingual jika tidak ada
if (!function_exists('__')) {
    function __($key) {
        $translations = [
            'semester_data' => 'Data Semester',
            'select_semester' => 'Pilih semester untuk melihat mata kuliah dan materi',
            'semester_specific' => 'Data Semester %d',
            'courses_materials' => 'Mata kuliah dan materi untuk Semester %d',
            'no_courses' => 'Tidak ada mata kuliah yang ditemukan',
            'no_courses_yet' => 'Belum ada mata kuliah atau materi yang tersedia untuk Semester %d.',
        ];
        return $translations[$key] ?? $key;
    }
}

if (!function_exists('__f')) {
    function __f($key, ...$args) {
        $text = __($key);
        return sprintf($text, ...$args);
    }
}
?>

<style>
:root {
    --bg-primary: #1a1a1a;
    --bg-secondary: #2d2d2d;
    --bg-tertiary: #3d3d3d;
    --text-primary: #ffffff;
    --text-secondary: #b0b0b0;
    --text-muted: #888888;
    --border-color: #404040;
    --hover-bg: #404040;
    --accent-color: #0084ff;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --folder-color: #ffd700;
}

body {
    background-color: var(--bg-primary);
    color: var(--text-primary);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.semester-header {
    background: transparent (135deg,rgb(168, 32, 32) 0%, #1a1a1a 100%);
    padding: 2rem 0;
    border-bottom: 0px solid var(--border-color);
}

.semester-header h1 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    font-size: 2.5rem;
    font-weight: 700;
}

.semester-header p {
    color: var(--text-secondary);
    font-size: 1.1rem;
    margin-bottom: 0;
}

.semester-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 1.5rem;
}

.semester-tab {
    padding: 8px 16px;
    background-color: var(--bg-secondary);
    color: var(--text-secondary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
    font-size: 0.9rem;
}

.semester-tab:hover {
    background-color: var(--hover-bg);
    color: var(--text-primary);
    border-color: var(--accent-color);
}

.semester-tab.active {
    background-color: var(--accent-color);
    color: white;
    border-color: var(--accent-color);
}

.semester-count {
    position: absolute;
    top: -6px;
    right: -6px;
    background-color: var(--danger-color);
    color: white;
    font-size: 10px;
    min-width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.semester-content {
    padding: 2rem 0;
    min-height: 60vh;
}

.file-manager {
    background-color: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 2rem;
}

.file-manager-header {
    background-color: var(--bg-tertiary);
    padding: 12px 16px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 10px;
}

.file-manager-header h3 {
    margin: 0;
    color: var(--text-primary);
    font-size: 1.2rem;
    font-weight: 600;
}

.file-manager-header .course-info {
    color: var(--text-muted);
    font-size: 0.9rem;
}

.file-table-header {
    display: grid;
    grid-template-columns: 1fr 100px 180px 80px 80px;
    gap: 16px;
    padding: 12px 16px;
    background-color: var(--bg-tertiary);
    border-bottom: 1px solid var(--border-color);
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.file-list {
    max-height: 500px;
    overflow-y: auto;
}

.file-item {
    border-bottom: 1px solid var(--border-color);
    transition: background-color 0.2s ease;
}

.file-item:hover {
    background-color: var(--hover-bg);
}

.file-item.missing-file {
    background-color: rgba(220, 53, 69, 0.1);
}

.file-item.missing-file:hover {
    background-color: rgba(220, 53, 69, 0.2);
}

.item-row {
    display: grid;
    grid-template-columns: 1fr 100px 180px 80px 80px;
    gap: 16px;
    padding: 12px 16px;
    align-items: center;
}

.item-name {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-primary);
    font-size: 0.9rem;
}

.expand-icon {
    width: 16px;
    text-align: center;
    cursor: pointer;
    color: var(--text-muted);
    transition: transform 0.3s ease;
}

.folder-item.expanded .expand-icon {
    transform: rotate(90deg);
}

.folder-icon {
    color: var(--folder-color);
    font-size: 1rem;
}

.file-icon {
    color: var(--text-secondary);
    font-size: 1rem;
}

.file-icon.fa-file-pdf { color: #dc3545; }
.file-icon.fa-file-word { color: #2b579a; }
.file-icon.fa-file-excel { color: #217346; }
.file-icon.fa-file-powerpoint { color: #d24726; }
.file-icon.fa-file-archive { color: #6c757d; }
.file-icon.fa-file-image { color: #17a2b8; }

.name-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.item-size,
.item-date,
.item-type {
    font-size: 0.85rem;
    color: var(--text-secondary);
}

.item-actions {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
}

.action-btn {
    padding: 4px 8px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    background-color: var(--bg-secondary);
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.8rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 4px;
}

.action-btn:hover {
    background-color: var(--accent-color);
    color: white;
    border-color: var(--accent-color);
}

.action-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background-color: var(--bg-tertiary);
}

.action-btn.disabled:hover {
    background-color: var(--bg-tertiary);
    color: var(--text-muted);
    border-color: var(--border-color);
}

.folder-content {
    display: none;
    border-left: 2px solid var(--border-color);
    margin-left: 24px;
}

.folder-item.expanded .folder-content {
    display: block;
}

.no-data {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--text-secondary);
}

.no-data-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    color: var(--text-muted);
}

.no-data h3 {
    margin-bottom: 1rem;
    color: var(--text-primary);
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.breadcrumb {
    background-color: var(--bg-secondary);
    padding: 12px 16px;
    border-bottom: 1px solid var(--border-color);
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.breadcrumb a {
    color: var(--accent-color);
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.course-description {
    background-color: var(--bg-tertiary);
    padding: 12px 16px;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-secondary);
    font-size: 0.9rem;
    line-height: 1.5;
}

/* Custom scrollbar */
.file-list::-webkit-scrollbar {
    width: 8px;
}

.file-list::-webkit-scrollbar-track {
    background: var(--bg-secondary);
}

.file-list::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 4px;
}

.file-list::-webkit-scrollbar-thumb:hover {
    background: var(--text-muted);
}

@media (max-width: 768px) {
    .item-row,
    .file-table-header {
        grid-template-columns: 1fr 60px 40px;
    }
    
    .item-date,
    .item-type {
        display: none;
    }
}
</style>

<!-- Semester Header -->
<section class="semester-header">
    <div class="container">
        <h1><?= $semester > 0 ? 'Semester ' . $semester : 'Data Semester' ?></h1>
        <p><?= $semester > 0 ? 'File dan materi untuk Semester ' . $semester : 'Pilih semester untuk melihat file dan materi' ?></p>
        
        <div class="semester-tabs">
            <?php for ($i = 1; $i <= 8; $i++): ?>
                <a href="<?= BASE_URL ?>/pages/semester.php?semester=<?= $i ?>" class="semester-tab <?= $semester == $i ? 'active' : '' ?>">
                    Semester <?= $i ?>
                    <?php if (isset($semesterCounts[$i]) && $semesterCounts[$i] > 0): ?>
                        <span class="semester-count"><?= $semesterCounts[$i] ?></span>
                    <?php endif; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
</section>

<!-- Semester Content -->
<section class="semester-content">
    <div class="container">
        <?php if ($semester > 0): ?>
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $courseName => $files): ?>
                    <div class="file-manager">
                        <div class="file-manager-header">
                            <i class="fas fa-folder folder-icon"></i>
                            <h3><?= htmlspecialchars($courseName) ?></h3>
                            <span class="course-info"><?= count($files) ?> file(s)</span>
                        </div>
                        
                        <?php if (!empty($files[0]['deskripsi'])): ?>
                            <div class="course-description">
                                <?= nl2br(htmlspecialchars($files[0]['deskripsi'])) ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="file-table-header">
                            <div>Name</div>
                            <div>Size</div>
                            <div>Last Modified</div>
                            <div>Type</div>
                            <div>Actions</div>
                        </div>
                        
                        <div class="file-list">
                            <?php
                            $fileTree = buildFileTree($files);
                            echo renderFileTree($fileTree);
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-data">
                    <div class="no-data-icon"><i class="fas fa-folder-open"></i></div>
                    <h3>Tidak Ada Data</h3>
                    <p>Belum ada file atau materi yang tersedia untuk Semester <?= $semester ?>.</p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-data">
                <div class="no-data-icon"><i class="fas fa-graduation-cap"></i></div>
                <h3>Pilih Semester</h3>
                <p>Silakan pilih semester di atas untuk melihat file dan materi yang tersedia.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Folder expand/collapse functionality
    const expandIcons = document.querySelectorAll('.expand-icon');
    expandIcons.forEach(icon => {
        icon.addEventListener('click', function(e) {
            e.preventDefault();
            const folderItem = this.closest('.folder-item');
            if (folderItem) {
                folderItem.classList.toggle('expanded');
            }
        });
    });

    // Handle disabled download buttons
    const disabledButtons = document.querySelectorAll('.action-btn.disabled');
    disabledButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            alert('File tidak ditemukan atau sudah dihapus');
        });
    });

    // Console log for debugging
    console.log('Semester page loaded successfully');
    console.log('Found courses:', <?= json_encode(array_keys($courses)) ?>);
});
</script>

<?php 
require_once '../templates/footer.php'; 
?>