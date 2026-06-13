<?php
ob_start();
$pageTitle = "Backup System";
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';
require_once 'templates/header.php';

// Check if logged in
if (!isset($_SESSION['admin_id'])) {
    setAlert('error', 'Please login to access the admin panel');
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

// Check permissions
$stmt = $pdo->prepare("SELECT a.*, r.permissions 
                      FROM admin a 
                      LEFT JOIN admin_roles r ON a.role_id = r.id 
                      WHERE a.id = :id");
$stmt->bindParam(':id', $_SESSION['admin_id']);
$stmt->execute();
$admin = $stmt->fetch();

$permissions = json_decode($admin['permissions'] ?? '{"all":true}', true);
if (!($permissions['all'] ?? false) && !($permissions['settings'] ?? false)) {
    setAlert('error', 'You do not have permission to access this page');
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
    exit;
}

// Process backup creation
if (isset($_POST['create_backup'])) {
    // Create backup directory if it doesn't exist
    $backupDir = '../backups/';
    if (!file_exists($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    // Generate backup filename
    $timestamp = date('Y-m-d_H-i-s');
    $filename = 'backup_' . $timestamp . '.sql';
    $filepath = $backupDir . $filename;
    
    // Get backup notes
    $notes = trim($_POST['notes'] ?? '');
    
    // Backup configuration
    $dbHost = DB_HOST;
    $dbPort = DB_PORT;
    $dbUser = DB_USER;
    $dbPass = DB_PASS;
    $dbName = DB_NAME;
    
    // Create backup
    try {
        // Check if mysqldump is available
        $mysqldumpPath = null;
        
        // Try to find mysqldump location on different systems
        $possiblePaths = [
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/usr/local/mysql/bin/mysqldump',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp\\bin\\mysql\\mysql5.7.26\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe'
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $mysqldumpPath = $path;
                break;
            }
        }
        
        if ($mysqldumpPath) {
            // Use mysqldump command
            $command = sprintf('"%s" --host=%s --port=%s --user=%s --password=%s %s > %s',
                $mysqldumpPath, $dbHost, $dbPort, $dbUser, $dbPass, $dbName, $filepath
            );
            
            // Execute command
            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);
            
            if ($returnVar !== 0) {
                throw new Exception("Error executing mysqldump command");
            }
        } else {
            // Fallback to PHP backup method
            $backup = createMySQLBackup($pdo, $dbName);
            file_put_contents($filepath, $backup);
        }
        
        // Get file size
        $filesize = filesize($filepath);
        
        // Insert backup record into database
        $stmt = $pdo->prepare("INSERT INTO system_backups (filename, size, notes, created_by) VALUES (:filename, :size, :notes, :created_by)");
        $stmt->bindParam(':filename', $filename);
        $stmt->bindParam(':size', $filesize);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':created_by', $_SESSION['admin_id']);
        $stmt->execute();
        
        // Log activity
        logActivity('Created database backup: ' . $filename);
        
        setAlert('success', 'Backup created successfully');
    } catch (Exception $e) {
        setAlert('error', 'Backup failed: ' . $e->getMessage());
    }
    
    header('Location: ' . BASE_URL . '/admin/backup.php');
    exit;
}

// Process backup deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $backupId = (int)$_GET['id'];
    
    // Get backup info
    $stmt = $pdo->prepare("SELECT filename FROM system_backups WHERE id = :id");
    $stmt->bindParam(':id', $backupId);
    $stmt->execute();
    $backup = $stmt->fetch();
    
    if ($backup) {
        // Delete file
        $filepath = '../backups/' . $backup['filename'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM system_backups WHERE id = :id");
        $stmt->bindParam(':id', $backupId);
        $stmt->execute();
        
        // Log activity
        logActivity('Deleted backup: ' . $backup['filename']);
        
        setAlert('success', 'Backup deleted successfully');
    } else {
        setAlert('error', 'Backup not found');
    }
    
    header('Location: ' . BASE_URL . '/admin/backup.php');
    exit;
}

// Process backup restoration
if (isset($_GET['action']) && $_GET['action'] === 'restore' && isset($_GET['id'])) {
    $backupId = (int)$_GET['id'];
    
    // Get backup info
    $stmt = $pdo->prepare("SELECT filename FROM system_backups WHERE id = :id");
    $stmt->bindParam(':id', $backupId);
    $stmt->execute();
    $backup = $stmt->fetch();
    
    if ($backup) {
        $filepath = '../backups/' . $backup['filename'];
        
        if (file_exists($filepath)) {
            try {
                // Read backup file
                $sql = file_get_contents($filepath);
                
                // Execute SQL commands
                $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
                $pdo->exec($sql);
                $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
                
                // Log activity
                logActivity('Restored database from backup: ' . $backup['filename']);
                
                setAlert('success', 'Database restored successfully');
            } catch (PDOException $e) {
                setAlert('error', 'Restore failed: ' . $e->getMessage());
            }
        } else {
            setAlert('error', 'Backup file not found');
        }
    } else {
        setAlert('error', 'Backup not found');
    }
    
    header('Location: ' . BASE_URL . '/admin/backup.php');
    exit;
}

// Process backup download
if (isset($_GET['action']) && $_GET['action'] === 'download' && isset($_GET['id'])) {
    $backupId = (int)$_GET['id'];
    
    // Get backup info
    $stmt = $pdo->prepare("SELECT filename FROM system_backups WHERE id = :id");
    $stmt->bindParam(':id', $backupId);
    $stmt->execute();
    $backup = $stmt->fetch();
    
    if ($backup) {
        $filepath = '../backups/' . $backup['filename'];
        
        if (file_exists($filepath)) {
            // Log activity
            logActivity('Downloaded backup: ' . $backup['filename']);
            
            // Download file
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $backup['filename'] . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            ob_clean();
            flush();
            readfile($filepath);
            exit;
        } else {
            setAlert('error', 'Backup file not found');
        }
    } else {
        setAlert('error', 'Backup not found');
    }
    
    header('Location: ' . BASE_URL . '/admin/backup.php');
    exit;
}

// Get all backups
$stmt = $pdo->query("SELECT b.*, a.username 
                    FROM system_backups b 
                    LEFT JOIN admin a ON b.created_by = a.id 
                    ORDER BY b.backup_date DESC");
$backups = $stmt->fetchAll();
?>

<div class="admin-content-container">
    <div class="admin-breadcrumb">
        <div class="breadcrumb-container">
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="breadcrumb-item"><i class="fas fa-home"></i> Dashboard</a>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
            <span class="breadcrumb-item active"><?= $pageTitle ?></span>
        </div>
        <div class="breadcrumb-actions">
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>

    <div class="admin-content-header">
        <h1><i class="fas fa-database"></i> Backup System</h1>
        <p>Create, manage, and restore database backups</p>
    </div>
    
    <!-- Create Backup -->
    <div class="admin-card mb-4">
        <div class="card-header">
            <h2><i class="fas fa-plus"></i> Create New Backup</h2>
        </div>
        <div class="card-body">
            <form action="" method="POST">
                <input type="hidden" name="create_backup" value="1">
                
                <div class="form-group">
                    <label for="notes">Backup Notes (Optional)</label>
                    <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Enter notes about this backup..."></textarea>
                </div>
                
                <div class="backup-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Creating a backup may take some time depending on the size of your database. Do not close the page during this process.</p>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-database"></i> Create Backup
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Backup List -->
    <div class="admin-card">
        <div class="card-header">
            <h2><i class="fas fa-history"></i> Backup History</h2>
        </div>
        <div class="card-body">
            <?php if (count($backups) > 0): ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Size</th>
                                <th>Created By</th>
                                <th>Date</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($backups as $backup): ?>
                                <tr>
                                    <td><?= htmlspecialchars($backup['filename']) ?></td>
                                    <td><?= formatFileSize($backup['size']) ?></td>
                                    <td><?= htmlspecialchars($backup['username']) ?></td>
                                    <td><?= date('d M Y H:i', strtotime($backup['backup_date'])) ?></td>
                                    <td><?= htmlspecialchars($backup['notes'] ?: 'No notes') ?></td>
                                    <td>
                                        <a href="?action=download&id=<?= $backup['id'] ?>" class="action-btn" title="Download Backup">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="?action=restore&id=<?= $backup['id'] ?>" class="action-btn restore-btn" title="Restore Database" onclick="return confirm('Are you sure you want to restore the database from this backup? This will overwrite all current data.')">
                                            <i class="fas fa-undo"></i>
                                        </a>
                                        <a href="?action=delete&id=<?= $backup['id'] ?>" class="action-btn delete-btn" title="Delete Backup" onclick="return confirm('Are you sure you want to delete this backup?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-database"></i>
                    <h3>No backups found</h3>
                    <p>Create your first backup to protect your data</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Helper function to create MySQL backup using PHP
function createMySQLBackup($pdo, $dbName) {
    $output = "-- Database Backup\n";
    $output .= "-- Generated on " . date('Y-m-d H:i:s') . "\n";
    $output .= "-- Database: " . $dbName . "\n\n";
    
    // Get all tables
    $tables = [];
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    // Process each table
    foreach ($tables as $table) {
        $output .= "-- Table structure for table `$table`\n";
        $output .= "DROP TABLE IF EXISTS `$table`;\n";
        
        // Get create table statement
        $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $output .= $row[1] . ";\n\n";
        
        // Get table data
        $stmt = $pdo->query("SELECT * FROM `$table`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($rows) > 0) {
            $output .= "-- Dumping data for table `$table`\n";
            
            // Get column names
            $columnNames = array_keys($rows[0]);
            $columnList = "`" . implode("`, `", $columnNames) . "`";
            
            // Create insert statements
            foreach ($rows as $row) {
                $values = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = "NULL";
                    } else {
                        $values[] = $pdo->quote($value);
                    }
                }
                
                $valueList = implode(", ", $values);
                $output .= "INSERT INTO `$table` ($columnList) VALUES ($valueList);\n";
            }
            
            $output .= "\n";
        }
    }
    
    return $output;
}

// Helper function to format file size
function formatFileSize($size) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    
    while ($size >= 1024 && $i < count($units) - 1) {
        $size /= 1024;
        $i++;
    }
    
    return round($size, 2) . ' ' . $units[$i];
}



require_once 'templates/footer.php';
ob_end_flush();
?>