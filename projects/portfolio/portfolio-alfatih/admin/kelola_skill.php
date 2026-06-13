<?php
ob_start();
$pageTitle = "Skills Management";
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';
require_once 'templates/header.php';

// Create skills table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `skills` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `category` VARCHAR(100) NOT NULL,
        `level` INT NOT NULL DEFAULT 70,
        `icon` VARCHAR(255),
        `display_order` INT DEFAULT 0,
        `is_active` TINYINT(1) DEFAULT 1
    )");
} catch (PDOException $e) {
    setAlert('error', 'Error creating skills table: ' . $e->getMessage());
}

// Process action
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Delete skill
if ($action === 'delete' && $id > 0) {
    try {
        // Get skill icon
        $stmt = $pdo->prepare("SELECT icon FROM skills WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $skill = $stmt->fetch();
        
        // Delete icon file if exists
        if ($skill && !empty($skill['icon']) && file_exists('../uploads/skills/' . $skill['icon'])) {
            unlink('../uploads/skills/' . $skill['icon']);
        }
        
        // Delete skill from database
        $stmt = $pdo->prepare("DELETE FROM skills WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        setAlert('success', 'Skill deleted successfully');
    } catch (PDOException $e) {
        setAlert('error', 'Error: ' . $e->getMessage());
    }
    header('Location: ' . BASE_URL . '/admin/kelola_skill.php');
    exit;
}

// Toggle active status
if ($action === 'toggle' && $id > 0) {
    try {
        // Get current status
        $stmt = $pdo->prepare("SELECT is_active FROM skills WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $skill = $stmt->fetch();
        
        if ($skill) {
            // Toggle status
            $newStatus = $skill['is_active'] ? 0 : 1;
            
            $stmt = $pdo->prepare("UPDATE skills SET is_active = :status WHERE id = :id");
            $stmt->bindParam(':status', $newStatus);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            setAlert('success', 'Skill status updated successfully');
        }
    } catch (PDOException $e) {
        setAlert('error', 'Error: ' . $e->getMessage());
    }
    header('Location: ' . BASE_URL . '/admin/kelola_skill.php');
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $level = (int)($_POST['level'] ?? 70);
    $display_order = (int)($_POST['display_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $skill_id = isset($_POST['skill_id']) ? (int)$_POST['skill_id'] : 0;
    
    // Validate inputs
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Skill name is required';
    }
    
    if (empty($category)) {
        $errors[] = 'Category is required';
    }
    
    if ($level < 0 || $level > 100) {
        $errors[] = 'Level must be between 0 and 100';
    }
    
    // If no errors, process form
    if (empty($errors)) {
        try {
            // CEK DUPLIKAT SEBELUM INSERT
            if ($skill_id === 0) { // Hanya cek saat menambah baru
                $stmt = $pdo->prepare("SELECT id FROM skills WHERE name = :name AND category = :category");
                $stmt->execute(['name' => $name, 'category' => $category]);
                if ($stmt->fetch()) {
                    setAlert('error', 'Skill "' . htmlspecialchars($name) . '" already exists in category "' . htmlspecialchars($category) . '".');
                    header('Location: ' . BASE_URL . '/admin/kelola_skill.php');
                    exit;
                }
            }

            // Upload icon if provided
            $icon = '';
            
            if ($skill_id > 0) {
                // Get current icon if editing
                $stmt = $pdo->prepare("SELECT icon FROM skills WHERE id = :id");
                $stmt->bindParam(':id', $skill_id);
                $stmt->execute();
                $currentSkill = $stmt->fetch();
                $icon = $currentSkill['icon'] ?? '';
            }
            
            if (!empty($_FILES['icon']['name'])) {
                $uploadDir = '../uploads/skills/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileExtension = pathinfo($_FILES['icon']['name'], PATHINFO_EXTENSION);
                $newFileName = 'skill_' . time() . '.' . $fileExtension;
                $targetPath = $uploadDir . $newFileName;
                
                if (move_uploaded_file($_FILES['icon']['tmp_name'], $targetPath)) {
                    // Delete old icon if exists and updating
                    if ($skill_id > 0 && !empty($icon) && file_exists($uploadDir . $icon)) {
                        unlink($uploadDir . $icon);
                    }
                    $icon = $newFileName;
                } else {
                    setAlert('error', 'Failed to upload icon');
                    header('Location: ' . BASE_URL . '/admin/kelola_skill.php');
                    exit;
                }
            }
            
            if ($skill_id > 0) {
                // Update existing skill
                $sql = "UPDATE skills SET name = :name, category = :category, level = :level, display_order = :display_order, is_active = :is_active";
                if (!empty($icon)) {
                    $sql .= ", icon = :icon";
                }
                $sql .= " WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $skill_id);
            } else {
                // Add new skill
                $stmt = $pdo->prepare("INSERT INTO skills (name, category, level, icon, display_order, is_active) VALUES (:name, :category, :level, :icon, :display_order, :is_active)");
            }
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':level', $level);
            $stmt->bindParam(':display_order', $display_order);
            $stmt->bindParam(':is_active', $is_active);
            
            if ($skill_id === 0 || !empty($icon)) {
                $stmt->bindParam(':icon', $icon);
            }
            
            $stmt->execute();
            
            setAlert('success', ($skill_id > 0 ? 'Skill updated' : 'Skill added') . ' successfully');
        } catch (PDOException $e) {
            setAlert('error', 'Error: ' . $e->getMessage());
        }
    } else {
        setAlert('error', implode('<br>', $errors));
    }
    
    header('Location: ' . BASE_URL . '/admin/kelola_skill.php');
    exit;
}

// Get skill data if editing
$editSkill = null;
if ($action === 'edit' && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $editSkill = $stmt->fetch();
    
    if (!$editSkill) {
        setAlert('error', 'Skill not found');
        header('Location: ' . BASE_URL . '/admin/kelola_skill.php');
        exit;
    }
}

// Get all skills for listing
$stmt = $pdo->query("SELECT * FROM skills ORDER BY category, display_order, name");
$skills = $stmt->fetchAll();

// BUAT DAFTAR SKILL YANG SUDAH ADA UNTUK PENGECEKAN
$existingSkills = [];
foreach ($skills as $skill) {
    // Kunci unik dibuat dari nama skill dan kategori (lowercase)
    $key = strtolower($skill['name']) . '|' . strtolower($skill['category']);
    $existingSkills[$key] = true;
}

// Group skills by category
$skillsByCategory = [];
foreach ($skills as $skill) {
    $skillsByCategory[$skill['category']][] = $skill;
}

// Define preset skills for quick selection
$presetSkills = [
    'Frontend' => ['HTML', 'CSS', 'JavaScript', 'React', 'Vue.js', 'Angular', 'Bootstrap', 'Tailwind CSS', 'jQuery', 'TypeScript'],
    'Backend' => ['PHP', 'Node.js', 'Python', 'Java', 'C#', 'Ruby', 'Go', 'Laravel', 'Express.js', 'Django', 'Spring Boot', 'ASP.NET'],
    'Database' => ['MySQL', 'PostgreSQL', 'MongoDB', 'SQLite', 'Oracle', 'SQL Server', 'Redis', 'Firebase'],
    'Mobile' => ['React Native', 'Flutter', 'Android (Java)', 'iOS (Swift)', 'Xamarin', 'Kotlin', 'Ionic'],
    'DevOps' => ['Docker', 'Kubernetes', 'AWS', 'Azure', 'Google Cloud', 'CI/CD', 'Jenkins', 'Git', 'GitHub Actions'],
    'Design' => ['Figma', 'Adobe XD', 'Photoshop', 'Illustrator', 'Sketch', 'InVision', 'UI/UX Design', 'Responsive Design'],
    'Other' => ['SEO', 'Digital Marketing', 'Content Writing', 'Project Management', 'Agile', 'Scrum', 'Technical Writing']
];
?>

<div class="admin-content-container">
    <div class="admin-breadcrumb">
        <div class="breadcrumb-container">
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="breadcrumb-item"><i class="fas fa-home"></i> Dashboard</a>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
            <span class="breadcrumb-item">Settings</span>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
            <span class="breadcrumb-item active"><?= $pageTitle ?></span>
        </div>
        <div class="breadcrumb-actions">
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>
    
    <div class="admin-content-header">
        <h1>
            <i class="fas fa-code"></i> 
            <?= ($action === 'add' || $action === 'edit') ? ($action === 'add' ? 'Add New Skill' : 'Edit Skill') : 'Skills Management' ?>
        </h1>
        <p><?= ($action === 'add' || $action === 'edit') ? 'Enter skill details below' : 'Manage your professional skills' ?></p>
    </div>
    
    <?php if ($action === 'add' || $action === 'edit'): ?>
        <div class="admin-form-container">
            <form action="" method="POST" enctype="multipart/form-data">
                <?php if ($action === 'edit'): ?>
                    <input type="hidden" name="skill_id" value="<?= $editSkill['id'] ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Skill Name*</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($editSkill['name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category*</label>
                        <input type="text" id="category" name="category" class="form-control" value="<?= htmlspecialchars($editSkill['category'] ?? '') ?>" list="category-list" required>
                        <datalist id="category-list">
                            <option value="Frontend Development">
                            <option value="Backend Development">
                            <option value="Database">
                            <option value="Mobile Development">
                            <option value="DevOps">
                            <option value="Design & Marketing">
                            <option value="Other">
                        </datalist>
                    </div>
                    
                    <div class="form-group">
                        <label for="level">Skill Level (0-100)*</label>
                        <div class="range-slider">
                            <input type="range" id="level" name="level" min="0" max="100" value="<?= $editSkill['level'] ?? 70 ?>" class="range-input" oninput="document.getElementById('level-value').textContent = this.value + '%'">
                            <span id="level-value"><?= $editSkill['level'] ?? 70 ?>%</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="display_order">Display Order</label>
                        <input type="number" id="display_order" name="display_order" class="form-control" value="<?= $editSkill['display_order'] ?? 0 ?>">
                        <p class="form-text">Lower numbers display first. Use for sorting.</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="icon">Skill Icon</label>
                    <div class="custom-file">
                        <input type="file" id="icon" name="icon" class="custom-file-input" accept="image/*">
                        <label class="custom-file-label" for="icon">Choose file</label>
                    </div>
                    <p class="form-text">Recommended size: 50x50 pixels. (PNG, SVG)</p>
                    
                    <?php if ($action === 'edit' && !empty($editSkill['icon'])): ?>
                        <div class="image-preview">
                            <img src="<?= BASE_URL ?>/uploads/skills/<?= htmlspecialchars($editSkill['icon']) ?>" alt="<?= htmlspecialchars($editSkill['name']) ?>">
                        </div>
                        <p class="form-text">Leave empty to keep current icon</p>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" value="1" <?= (isset($editSkill['is_active']) && $editSkill['is_active'] == 1) || !isset($editSkill['is_active']) ? 'checked' : '' ?>>
                        <label for="is_active">Active (Display this skill on your portfolio)</label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= ($action === 'edit') ? 'Update' : 'Save' ?> Skill</button>
                    <a href="<?= BASE_URL ?>/admin/kelola_skill.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Cancel</a>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="admin-card preset-skills-card">
            <div class="card-header">
                <h2><i class="fas fa-magic"></i> Quick Add Preset Skills</h2>
            </div>
            <div class="card-body">
                <div class="preset-skills-container">
                    <p>Select a category to view preset skills:</p>
                    <div class="preset-categories">
                        <?php foreach ($presetSkills as $category => $skillsList): ?>
                            <button class="preset-category-btn" data-category="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></button>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="preset-skills-list">
                        <?php foreach ($presetSkills as $category => $presetCategorySkills): ?>
                            <div class="preset-category" id="preset-<?= strtolower(str_replace(' ', '-', $category)) ?>" style="display: none;">
                                <h3><?= htmlspecialchars($category) ?> Skills</h3>
                                <div class="preset-skills-grid">
                                    <?php foreach ($presetCategorySkills as $skillName): ?>
                                        <?php
                                        // Cek apakah skill sudah ada
                                        $presetKey = strtolower($skillName) . '|' . strtolower($category);
                                        $isAdded = isset($existingSkills[$presetKey]);
                                        ?>
                                        <div class="preset-skill">
                                            <form action="" method="POST" onsubmit="return <?= $isAdded ? 'false' : 'true' ?>;">
                                                <input type="hidden" name="name" value="<?= htmlspecialchars($skillName) ?>">
                                                <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                                                <input type="hidden" name="level" value="80">
                                                <input type="hidden" name="is_active" value="1">
                                                <button type="submit" class="preset-skill-btn" <?= $isAdded ? 'disabled' : '' ?>>
                                                    <i class="fas <?= $isAdded ? 'fa-check' : 'fa-plus' ?>"></i> <?= htmlspecialchars($skillName) ?>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="admin-actions">
            <a href="<?= BASE_URL ?>/admin/kelola_skill.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Skill</a>
        </div>
        
        <?php if (count($skillsByCategory) > 0): ?>
            <?php foreach ($skillsByCategory as $category => $categorySkills): ?>
                <div class="admin-card">
                    <div class="card-header">
                        <h2><?= htmlspecialchars($category) ?></h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Icon</th>
                                        <th>Name</th>
                                        <th>Level</th>
                                        <th>Display Order</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categorySkills as $skill): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($skill['icon'])): ?>
                                                    <div class="skill-icon">
                                                        <img src="<?= BASE_URL ?>/uploads/skills/<?= htmlspecialchars($skill['icon']) ?>" alt="<?= htmlspecialchars($skill['name']) ?>">
                                                    </div>
                                                <?php else: ?>
                                                    <div class="skill-icon skill-icon-placeholder">
                                                        <i class="fas fa-code"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($skill['name']) ?></td>
                                            <td>
                                                <div class="skill-level">
                                                    <div class="skill-progress">
                                                        <div class="progress" style="width: <?= $skill['level'] ?>%"></div>
                                                    </div>
                                                    <span class="skill-level-text"><?= $skill['level'] ?>%</span>
                                                </div>
                                            </td>
                                            <td><?= $skill['display_order'] ?></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/admin/kelola_skill.php?action=toggle&id=<?= $skill['id'] ?>" class="status-toggle <?= $skill['is_active'] ? 'active' : 'inactive' ?>">
                                                    <span class="status-indicator"></span>
                                                    <span class="status-text"><?= $skill['is_active'] ? 'Active' : 'Inactive' ?></span>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/admin/kelola_skill.php?action=edit&id=<?= $skill['id'] ?>" class="action-btn edit-btn" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>/admin/kelola_skill.php?action=delete&id=<?= $skill['id'] ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this skill?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-code"></i>
                <h3>No skills found</h3>
                <p>Start by adding your professional skills to showcase on your portfolio.</p>
                <a href="<?= BASE_URL ?>/admin/kelola_skill.php?action=add" class="btn btn-primary mt-3"><i class="fas fa-plus"></i> Add Your First Skill</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
    .range-slider {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .range-input {
        flex: 1;
        height: 8px;
        -webkit-appearance: none;
        background-color: var(--dark-surface);
        border-radius: 4px;
    }
    
    .range-input::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--primary-color);
        cursor: pointer;
    }
    
    .range-input::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--primary-color);
        cursor: pointer;
        border: none;
    }
    
    #level-value {
        font-weight: 500;
        color: var(--primary-color);
        min-width: 45px;
        text-align: right;
    }
    
    .status-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 5px 10px;
        border-radius: 30px;
        font-size: 0.85rem;
        background-color: var(--dark-surface);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .status-toggle.active { background-color: rgba(76, 175, 80, 0.1); }
    .status-toggle.inactive { background-color: rgba(158, 158, 158, 0.1); }
    
    .status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    
    .status-toggle.active .status-indicator { background-color: var(--success-color); }
    .status-toggle.inactive .status-indicator { background-color: #9e9e9e; }
    
    .status-toggle.active .status-text { color: var(--success-color); }
    .status-toggle.inactive .status-text { color: var(--text-muted); }
    
    .status-toggle:hover { transform: translateY(-2px); }
    
    .preset-skills-card { margin-bottom: 30px; }
    
    .preset-categories {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin: 15px 0;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .preset-category-btn {
        padding: 8px 15px;
        background-color: var(--dark-surface);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 30px;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }
    
    .preset-category-btn:hover, .preset-category-btn.active {
        background-color: rgba(0, 229, 255, 0.1);
        color: var(--primary-color);
        transform: translateY(-2px);
    }
    
    .preset-skills-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }
    
    .preset-skill-btn {
        width: 100%;
        padding: 10px;
        text-align: left;
        background-color: var(--dark-surface);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .preset-skill-btn:hover {
        background-color: rgba(0, 229, 255, 0.1);
        color: var(--primary-color);
        transform: translateY(-2px);
    }
    
    .preset-skill-btn i { font-size: 0.8rem; }
    
    .mt-3 { margin-top: 15px; }

    .skill-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--dark-surface);
        border-radius: 8px;
        overflow: hidden;
    }
    
    .skill-icon img {
        width: 30px;
        height: 30px;
        object-fit: contain;
    }
    
    .skill-icon-placeholder {
        color: var(--primary-color);
    }
    
    .skill-level {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        min-width: 150px;
    }
    
    .skill-progress {
        flex: 1;
        height: 8px;
        background-color: var(--dark-surface);
        border-radius: 4px;
        overflow: hidden;
        position: relative;
    }
    
    .skill-progress .progress {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        border-radius: 4px;
    }
    
    .skill-level-text {
        font-size: 0.9rem;
        color: var(--text-secondary);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preset skills category toggle
        const categoryBtns = document.querySelectorAll('.preset-category-btn');
        const presetCategories = document.querySelectorAll('.preset-category');
        
        // Show first category by default
        if (categoryBtns.length > 0 && presetCategories.length > 0) {
            const firstCategoryBtn = categoryBtns[0];
            const firstCategoryName = firstCategoryBtn.getAttribute('data-category');
            document.getElementById('preset-' + firstCategoryName.toLowerCase().replace(/ & /g, '-').replace(/ /g, '-')).style.display = 'block';
            firstCategoryBtn.classList.add('active');
        }
        
        categoryBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                
                // Hide all categories
                presetCategories.forEach(cat => {
                    cat.style.display = 'none';
                });
                
                // Remove active class from all buttons
                categoryBtns.forEach(catBtn => {
                    catBtn.classList.remove('active');
                });
                
                // Show selected category
                const categoryId = 'preset-' + category.toLowerCase().replace(/ & /g, '-').replace(/ /g, '-');
                document.getElementById(categoryId).style.display = 'block';
                this.classList.add('active');
            });
        });
    });
</script>

<?php
require_once 'templates/footer.php';
ob_end_flush();
?>