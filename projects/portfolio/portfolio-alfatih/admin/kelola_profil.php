<?php
// Buffer output to prevent "headers already sent" errors
ob_start();

$pageTitle = "Kelola Profil";
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';
require_once 'templates/header.php';

// Get profile data
$stmt = $pdo->query("SELECT * FROM profil WHERE id = 1");
$profile = $stmt->fetch();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $github = trim($_POST['github'] ?? '');
    $summary = trim($_POST['summary'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $current_status = trim($_POST['current_status'] ?? '');
    
    // Upload profile image if provided
    $profile_image = $profile['profile_image'] ?? '';
    if (!empty($_FILES['profile_image']['name'])) {
        $uploadDir = '../uploads/profile/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExtension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $newFileName = 'profile_' . time() . '.' . $fileExtension;
        $targetPath = $uploadDir . $newFileName;
        
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
            // Delete old image if exists
            if (!empty($profile_image) && file_exists($uploadDir . $profile_image)) {
                unlink($uploadDir . $profile_image);
            }
            $profile_image = $newFileName;
        } else {
            setAlert('error', 'Failed to upload profile image');
        }
    }
    
    try {
        // Check if profile exists
        if ($profile) {
            // Update existing profile
            $stmt = $pdo->prepare("UPDATE profil SET nama = :nama, email = :email, whatsapp = :whatsapp, github = :github, profile_image = :profile_image, summary = :summary, location = :location, current_status = :current_status WHERE id = 1");
        } else {
            // Insert new profile
            $stmt = $pdo->prepare("INSERT INTO profil (id, nama, email, whatsapp, github, profile_image, summary, location, current_status) VALUES (1, :nama, :email, :whatsapp, :github, :profile_image, :summary, :location, :current_status)");
        }
        
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':whatsapp', $whatsapp);
        $stmt->bindParam(':github', $github);
        $stmt->bindParam(':profile_image', $profile_image);
        $stmt->bindParam(':summary', $summary);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':current_status', $current_status);
        $stmt->execute();
        
        setAlert('success', 'Profile updated successfully');
        header('Location: ' . BASE_URL . '/admin/kelola_profil.php');
        exit;
    } catch (PDOException $e) {
        setAlert('error', 'Error: ' . $e->getMessage());
    }
}
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
        <h1><i class="fas fa-user"></i> Kelola Profil</h1>
        <p>Update your personal information</p>
    </div>
    
    <div class="admin-form-container">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" class="form-control" value="<?= $profile['nama'] ?? OWNER_NAME ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= $profile['email'] ?? OWNER_EMAIL ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="whatsapp">WhatsApp</label>
                    <input type="text" id="whatsapp" name="whatsapp" class="form-control" value="<?= $profile['whatsapp'] ?? OWNER_WHATSAPP ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="github">GitHub</label>
                    <input type="text" id="github" name="github" class="form-control" value="<?= $profile['github'] ?? OWNER_GITHUB ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-control" value="<?= $profile['location'] ?? 'Lumajang, East Java, Indonesia' ?>">
                </div>
                
                <div class="form-group">
                    <label for="current_status">Current Status</label>
                    <input type="text" id="current_status" name="current_status" class="form-control" value="<?= $profile['current_status'] ?? 'Student' ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="summary">Summary</label>
                <textarea id="summary" name="summary" class="form-control" rows="5"><?= $profile['summary'] ?? '' ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="profile_image">Profile Image</label>
                <div class="custom-file">
                    <input type="file" id="profile_image" name="profile_image" class="custom-file-input" accept="image/*">
                    <label class="custom-file-label" for="profile_image">Choose file</label>
                </div>
                <?php if (!empty($profile['profile_image'])): ?>
                <div class="image-preview">
                    <img src="<?= BASE_URL ?>/uploads/profile/<?= $profile['profile_image'] ?>" alt="Profile Image">
                </div>
                <?php endif; ?>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php 
require_once 'templates/footer.php';
// Flush the output buffer and send to browser
ob_end_flush();
?>