<?php
ob_start();
$pageTitle = "Change Password";
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';
require_once 'templates/header.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate inputs
    $errors = [];
    
    if (empty($current_password)) {
        $errors[] = 'Current password is required';
    }
    
    if (empty($new_password)) {
        $errors[] = 'New password is required';
    } elseif (strlen($new_password) < 6) {
        $errors[] = 'New password must be at least 6 characters long';
    }
    
    if ($new_password !== $confirm_password) {
        $errors[] = 'New password and confirmation do not match';
    }
    
    // If no errors, process password change
    if (empty($errors)) {
        try {
            // Get current admin data
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE id = :id");
            $stmt->bindParam(':id', $_SESSION['admin_id']);
            $stmt->execute();
            $admin = $stmt->fetch();
            
            // Verify current password
            if ($admin && password_verify($current_password, $admin['password'])) {
                // Hash the new password
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update password in database
                $stmt = $pdo->prepare("UPDATE admin SET password = :password WHERE id = :id");
                $stmt->bindParam(':password', $password_hash);
                $stmt->bindParam(':id', $_SESSION['admin_id']);
                $stmt->execute();
                
                setAlert('success', 'Password changed successfully. Please log in again.');
                // Destroy session and redirect to login
                session_destroy();
                header('Location: ' . BASE_URL . '/admin/login.php');
                exit;
            } else {
                setAlert('error', 'Current password is incorrect');
            }
        } catch (PDOException $e) {
            setAlert('error', 'Error: ' . $e->getMessage());
        }
    } else {
        setAlert('error', implode('<br>', $errors));
    }
    
    // Redirect back to the same page to show errors
    header('Location: ' . BASE_URL . '/admin/change_password.php');
    exit;
}
?>

<div class="admin-content-container">
    <div class="admin-breadcrumb">
        <div class="breadcrumb-container">
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="breadcrumb-item"><i class="fas fa-home"></i> Dashboard</a>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
            <span class="breadcrumb-item active"><?= htmlspecialchars($pageTitle) ?></span>
        </div>
        <div class="breadcrumb-actions">
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>
    
    <div class="admin-content-header">
        <h1><i class="fas fa-key"></i> Change Password</h1>
        <p>Update your admin account password</p>
    </div>
    
    <div class="admin-form-container">
        <form action="" method="POST">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required>
                <p class="form-text">Password must be at least 6 characters long.</p>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Change Password</button>
                <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
    
    <div class="admin-card">
        <div class="card-header">
            <h2>Password Security Tips</h2>
        </div>
        <div class="card-body">
            <div class="security-tips">
                <div class="tip-item">
                    <div class="tip-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="tip-content">
                        <h3>Use Strong Passwords</h3>
                        <p>Create passwords that are at least 8 characters long with a mix of uppercase and lowercase letters, numbers, and special characters.</p>
                    </div>
                </div>
                
                <div class="tip-item">
                    <div class="tip-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <div class="tip-content">
                        <h3>Change Regularly</h3>
                        <p>Update your password regularly (every 3-6 months) to maintain security.</p>
                    </div>
                </div>
                
                <div class="tip-item">
                    <div class="tip-icon">
                        <i class="fas fa-user-secret"></i>
                    </div>
                    <div class="tip-content">
                        <h3>Keep It Private</h3>
                        <p>Never share your admin password with anyone else or store it in unsecured locations.</p>
                    </div>
                </div>
                
                <div class="tip-item">
                    <div class="tip-icon">
                        <i class="fas fa-copy"></i>
                    </div>
                    <div class="tip-content">
                        <h3>Avoid Reusing Passwords</h3>
                        <p>Don't use the same password for multiple accounts or websites.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .security-tips {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .tip-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 15px;
        background-color: var(--dark-surface);
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .tip-item:hover {
        transform: translateY(-5px);
    }
    
    .tip-icon {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(0, 229, 255, 0.1), rgba(124, 77, 255, 0.1));
        border-radius: 10px;
        font-size: 1.5rem;
        color: var(--primary-color);
        flex-shrink: 0;
    }
    
    .tip-content h3 {
        font-size: 1rem;
        margin-bottom: 8px;
        color: var(--text-primary);
    }
    
    .tip-content p {
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-bottom: 0;
    }
</style>

<?php
require_once 'templates/footer.php';
ob_end_flush();
?>
