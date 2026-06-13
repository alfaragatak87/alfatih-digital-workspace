<?php
ob_start();
$pageTitle = "Notifications";
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';
require_once 'templates/header.php';

// Check if logged in
if (!isset($_SESSION['admin_id'])) {
    setAlert('error', 'Please login to access the admin panel');
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

// Process action to mark as read
if (isset($_GET['action']) && $_GET['action'] === 'mark_read' && isset($_GET['id'])) {
    $notificationId = (int)$_GET['id'];
    
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id AND admin_id = :admin_id");
    $stmt->bindParam(':id', $notificationId);
    $stmt->bindParam(':admin_id', $_SESSION['admin_id']);
    $stmt->execute();
    
    setAlert('success', 'Notification marked as read');
    header('Location: ' . BASE_URL . '/admin/notifications.php');
    exit;
}

// Process action to mark all as read
if (isset($_GET['action']) && $_GET['action'] === 'mark_all_read') {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE admin_id = :admin_id");
    $stmt->bindParam(':admin_id', $_SESSION['admin_id']);
    $stmt->execute();
    
    setAlert('success', 'All notifications marked as read');
    header('Location: ' . BASE_URL . '/admin/notifications.php');
    exit;
}

// Process action to delete notification
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $notificationId = (int)$_GET['id'];
    
    $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = :id AND admin_id = :admin_id");
    $stmt->bindParam(':id', $notificationId);
    $stmt->bindParam(':admin_id', $_SESSION['admin_id']);
    $stmt->execute();
    
    setAlert('success', 'Notification deleted');
    header('Location: ' . BASE_URL . '/admin/notifications.php');
    exit;
}

// Process action to delete all notifications
if (isset($_GET['action']) && $_GET['action'] === 'delete_all') {
    $stmt = $pdo->prepare("DELETE FROM notifications WHERE admin_id = :admin_id");
    $stmt->bindParam(':admin_id', $_SESSION['admin_id']);
    $stmt->execute();
    
    setAlert('success', 'All notifications deleted');
    header('Location: ' . BASE_URL . '/admin/notifications.php');
    exit;
}

// Get total count for pagination
$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE admin_id = :admin_id");
$stmt->bindParam(':admin_id', $_SESSION['admin_id']);
$stmt->execute();
$totalItems = $stmt->fetchColumn();

// Set up pagination
$itemsPerPage = 20;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalPages = ceil($totalItems / $itemsPerPage);
$offset = ($currentPage - 1) * $itemsPerPage;

// Get filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Get notifications
$sql = "SELECT * FROM notifications WHERE admin_id = :admin_id";

if ($filter === 'unread') {
    $sql .= " AND is_read = 0";
} elseif ($filter === 'read') {
    $sql .= " AND is_read = 1";
}

$sql .= " ORDER BY created_at DESC LIMIT :offset, :limit";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':admin_id', $_SESSION['admin_id']);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->execute();

$notifications = $stmt->fetchAll();

// Get counts for filters
$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE admin_id = :admin_id AND is_read = 0");
$stmt->bindParam(':admin_id', $_SESSION['admin_id']);
$stmt->execute();
$unreadCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE admin_id = :admin_id AND is_read = 1");
$stmt->bindParam(':admin_id', $_SESSION['admin_id']);
$stmt->execute();
$readCount = $stmt->fetchColumn();
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
        <h1><i class="fas fa-bell"></i> Notifications</h1>
        <p>View and manage your notifications</p>
    </div>
    
    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <a href="?filter=all" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>">
            All <span class="count">(<?= $totalItems ?>)</span>
        </a>
        <a href="?filter=unread" class="filter-tab <?= $filter === 'unread' ? 'active' : '' ?>">
            Unread <span class="count">(<?= $unreadCount ?>)</span>
        </a>
        <a href="?filter=read" class="filter-tab <?= $filter === 'read' ? 'active' : '' ?>">
            Read <span class="count">(<?= $readCount ?>)</span>
        </a>
    </div>
    
    <!-- Notification Actions -->
    <div class="admin-actions">
        <?php if ($unreadCount > 0): ?>
        <a href="?action=mark_all_read" class="btn btn-info btn-sm">
            <i class="fas fa-check-double"></i> Mark All as Read
        </a>
        <?php endif; ?>
        
        <?php if ($totalItems > 0): ?>
        <a href="?action=delete_all" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete all notifications?')">
            <i class="fas fa-trash"></i> Delete All
        </a>
        <?php endif; ?>
    </div>
    
    <!-- Notifications List -->
    <div class="admin-card">
        <div class="card-body">
            <?php if (count($notifications) > 0): ?>
                <div class="notifications-list">
                    <?php foreach ($notifications as $notification): ?>
                        <div class="notification-item <?= $notification['is_read'] ? 'read' : 'unread' ?>">
                            <div class="notification-icon notification-<?= $notification['type'] ?>">
                                <?php if ($notification['type'] === 'info'): ?>
                                    <i class="fas fa-info-circle"></i>
                                <?php elseif ($notification['type'] === 'success'): ?>
                                    <i class="fas fa-check-circle"></i>
                                <?php elseif ($notification['type'] === 'warning'): ?>
                                    <i class="fas fa-exclamation-triangle"></i>
                                <?php elseif ($notification['type'] === 'error'): ?>
                                    <i class="fas fa-times-circle"></i>
                                <?php endif; ?>
                            </div>
                            
                            <div class="notification-content">
                                <h3 class="notification-title"><?= htmlspecialchars($notification['title']) ?></h3>
                                <p class="notification-message"><?= htmlspecialchars($notification['message']) ?></p>
                                <div class="notification-meta">
                                    <span class="notification-time">
                                        <i class="far fa-clock"></i> <?= time_elapsed_string($notification['created_at']) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="notification-actions">
                                <?php if (!$notification['is_read']): ?>
                                <a href="?action=mark_read&id=<?= $notification['id'] ?>" class="notification-action" title="Mark as Read">
                                    <i class="fas fa-check"></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($notification['link']): ?>
                                <a href="<?= $notification['link'] ?>" class="notification-action" title="View Details">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <?php endif; ?>
                                
                                <a href="?action=delete&id=<?= $notification['id'] ?>" class="notification-action" title="Delete" onclick="return confirm('Are you sure you want to delete this notification?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <a href="?page=<?= $currentPage - 1 ?>&filter=<?= $filter ?>" class="pagination-btn">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        <?php endif; ?>
                        
                        <div class="pagination-pages">
                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <a href="?page=<?= $i ?>&filter=<?= $filter ?>" class="pagination-page <?= $i === $currentPage ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                        
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?page=<?= $currentPage + 1 ?>&filter=<?= $filter ?>" class="pagination-btn">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-data">
                    <i class="fas fa-bell-slash"></i>
                    <h3>No notifications found</h3>
                    <p>You don't have any notifications right now</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.notifications-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 20px;
    border-radius: 10px;
    background-color: var(--dark-surface);
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.notification-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.notification-item.unread {
    background-color: rgba(0, 229, 255, 0.05);
    border-left: 4px solid var(--primary-color);
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.notification-info {
    background-color: rgba(33, 150, 243, 0.1);
    color: var(--info-color);
}

.notification-success {
    background-color: rgba(76, 175, 80, 0.1);
    color: var(--success-color);
}

.notification-warning {
    background-color: rgba(255, 152, 0, 0.1);
    color: var(--warning-color);
}

.notification-error {
    background-color: rgba(244, 67, 54, 0.1);
    color: var(--error-color);
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-size: 1.1rem;
    margin-bottom: 5px;
}

.notification-message {
    color: var(--text-secondary);
    margin-bottom: 10px;
}

.notification-meta {
    display: flex;
    align-items: center;
    gap: 15px;
}

.notification-time {
    font-size: 0.9rem;
    color: var(--text-muted);
}

.notification-time i {
    margin-right: 5px;
}

.notification-actions {
    display: flex;
    gap: 10px;
}

.notification-action {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(255, 255, 255, 0.05);
    color: var(--text-secondary);
    transition: all 0.3s ease;
}

.notification-action:hover {
    background-color: var(--primary-color);
    color: var(--dark-bg);
    transform: translateY(-3px);
}
</style>

<?php
// Helper function to format time elapsed
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    $string = array();
    
    if ($diff->y > 0) { 
        $string[] = $diff->y . ' ' . ($diff->y > 1 ? 'tahun' : 'tahun'); 
    }
    if ($diff->m > 0) { 
        $string[] = $diff->m . ' ' . ($diff->m > 1 ? 'bulan' : 'bulan'); 
    }
    // Kita akan menghitung minggu secara manual
    $weeks = floor($diff->d / 7);
    $remaining_days = $diff->d % 7;
    
    if ($weeks > 0) { 
        $string[] = $weeks . ' ' . ($weeks > 1 ? 'minggu' : 'minggu'); 
    }
    if ($remaining_days > 0) { 
        $string[] = $remaining_days . ' ' . ($remaining_days > 1 ? 'hari' : 'hari'); 
    }
    if ($diff->h > 0) { 
        $string[] = $diff->h . ' ' . ($diff->h > 1 ? 'jam' : 'jam'); 
    }
    if ($diff->i > 0) { 
        $string[] = $diff->i . ' ' . ($diff->i > 1 ? 'menit' : 'menit'); 
    }
    if ($diff->s > 0) { 
        $string[] = $diff->s . ' ' . ($diff->s > 1 ? 'detik' : 'detik'); 
    }
    
    if (empty($string)) {
        return 'baru saja';
    }
    
    if (!$full) {
        $string = array_slice($string, 0, 1);
    }
    
    return implode(', ', $string) . ' yang lalu';
}

require_once 'templates/footer.php';
ob_end_flush();
?>