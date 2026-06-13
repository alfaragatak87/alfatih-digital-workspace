<?php
ob_start();
$pageTitle = "Kelola Pesan";
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';
require_once 'templates/header.php';

// Process action
$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Mark as read
if ($action === 'read' && $id > 0) {
    try {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    } catch (PDOException $e) {
        setAlert('error', 'Error: ' . $e->getMessage());
    }
}

// Mark as archived
if ($action === 'archive' && $id > 0) {
    try {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'archived' WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        setAlert('success', 'Message archived successfully');
        header('Location: ' . BASE_URL . '/admin/kelola_pesan.php');
        exit;
    } catch (PDOException $e) {
        setAlert('error', 'Error: ' . $e->getMessage());
    }
}

// Delete message
if ($action === 'delete' && $id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        setAlert('success', 'Message deleted successfully');
        header('Location: ' . BASE_URL . '/admin/kelola_pesan.php');
        exit;
    } catch (PDOException $e) {
        setAlert('error', 'Error: ' . $e->getMessage());
    }
}

// Get message details if viewing
$viewMessage = null;
if (($action === 'view' || $action === 'read') && $id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $viewMessage = $stmt->fetch();
    
    if (!$viewMessage) {
        setAlert('error', 'Message not found');
        header('Location: ' . BASE_URL . '/admin/kelola_pesan.php');
        exit;
    }
    
    // Mark as read if viewing
    if ($action === 'view' && $viewMessage['status'] === 'unread') {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $viewMessage['status'] = 'read';
    }
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? 'all';

// Build query based on filters
$query = "SELECT * FROM contact_messages";
$params = [];

if ($statusFilter !== 'all') {
    $query .= " WHERE status = :status";
    $params[':status'] = $statusFilter;
}

$query .= " ORDER BY created_at DESC";

// Get messages
$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$messages = $stmt->fetchAll();

// Count messages by status
$stmt = $pdo->query("SELECT status, COUNT(*) as count FROM contact_messages GROUP BY status");
$statusCounts = [];
while ($row = $stmt->fetch()) {
    $statusCounts[$row['status']] = $row['count'];
}
$totalCount = array_sum($statusCounts);
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
        <h1><i class="fas fa-envelope"></i> Manage Messages</h1>
        <p>View and respond to messages from your contact form</p>
    </div>
    
    <?php if ($action === 'view' || $action === 'read'): ?>
        <!-- View Message -->
        <div class="admin-actions">
            <a href="<?= BASE_URL ?>/admin/kelola_pesan.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Messages</a>
            <a href="<?= BASE_URL ?>/admin/kelola_pesan.php?action=archive&id=<?= $viewMessage['id'] ?>" class="btn btn-secondary"><i class="fas fa-archive"></i> Archive</a>
            <a href="<?= BASE_URL ?>/admin/kelola_pesan.php?action=delete&id=<?= $viewMessage['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this message?')"><i class="fas fa-trash-alt"></i> Delete</a>
        </div>
        
        <div class="admin-card message-detail">
            <div class="card-header">
                <div class="message-header">
                    <h2><?= htmlspecialchars($viewMessage['subject']) ?></h2>
                    <span class="status-badge status-<?= $viewMessage['status'] ?>">
                        <?= ucfirst($viewMessage['status']) ?>
                    </span>
                </div>
                <div class="message-meta">
                    <div class="message-sender">
                        From: <strong><?= htmlspecialchars($viewMessage['name']) ?></strong> 
                        &lt;<?= htmlspecialchars($viewMessage['email']) ?>&gt;
                    </div>
                    <div class="message-date">
                        <?= date('d M Y H:i', strtotime($viewMessage['created_at'])) ?>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="message-content">
                    <?= nl2br(htmlspecialchars($viewMessage['message'])) ?>
                </div>
                
                <div class="message-actions">
                    <a href="mailto:<?= htmlspecialchars($viewMessage['email']) ?>?subject=Re: <?= htmlspecialchars($viewMessage['subject']) ?>" class="btn btn-primary">
                        <i class="fas fa-reply"></i> Reply via Email
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Messages List -->
        <div class="admin-filters">
            <div class="filter-tabs">
                <a href="<?= BASE_URL ?>/admin/kelola_pesan.php" class="filter-tab <?= $statusFilter === 'all' ? 'active' : '' ?>">
                    All <span class="count"><?= $totalCount ?? 0 ?></span>
                </a>
                <a href="<?= BASE_URL ?>/admin/kelola_pesan.php?status=unread" class="filter-tab <?= $statusFilter === 'unread' ? 'active' : '' ?>">
                    Unread <span class="count"><?= $statusCounts['unread'] ?? 0 ?></span>
                </a>
                <a href="<?= BASE_URL ?>/admin/kelola_pesan.php?status=read" class="filter-tab <?= $statusFilter === 'read' ? 'active' : '' ?>">
                    Read <span class="count"><?= $statusCounts['read'] ?? 0 ?></span>
                </a>
                <a href="<?= BASE_URL ?>/admin/kelola_pesan.php?status=archived" class="filter-tab <?= $statusFilter === 'archived' ? 'active' : '' ?>">
                    Archived <span class="count"><?= $statusCounts['archived'] ?? 0 ?></span>
                </a>
            </div>
        </div>
        
        <div class="admin-card">
            <div class="card-body">
                <?php if (count($messages) > 0): ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Sender</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $message): ?>
                                    <tr class="<?= $message['status'] === 'unread' ? 'unread-row' : '' ?>">
                                        <td><?= htmlspecialchars($message['name']) ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/admin/kelola_pesan.php?action=view&id=<?= $message['id'] ?>" class="message-link">
                                                <?= htmlspecialchars($message['subject']) ?>
                                            </a>
                                        </td>
                                        <td><?= date('d M Y H:i', strtotime($message['created_at'])) ?></td>
                                        <td>
                                            <span class="status-badge status-<?= $message['status'] ?>">
                                                <?= ucfirst($message['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= BASE_URL ?>/admin/kelola_pesan.php?action=view&id=<?= $message['id'] ?>" class="action-btn view-btn" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($message['status'] !== 'archived'): ?>
                                                <a href="<?= BASE_URL ?>/admin/kelola_pesan.php?action=archive&id=<?= $message['id'] ?>" class="action-btn archive-btn" title="Archive">
                                                    <i class="fas fa-archive"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= BASE_URL ?>/admin/kelola_pesan.php?action=delete&id=<?= $message['id'] ?>" class="action-btn delete-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this message?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="no-data">No messages found.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .filter-tabs {
        display: flex;
        margin-bottom: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .filter-tab {
        padding: 10px 20px;
        color: var(--text-secondary);
        border-bottom: 2px solid transparent;
        transition: all 0.3s ease;
    }
    
    .filter-tab:hover {
        color: var(--text-primary);
    }
    
    .filter-tab.active {
        color: var(--primary-color);
        border-bottom-color: var(--primary-color);
    }
    
    .filter-tab .count {
        display: inline-block;
        background-color: rgba(255, 255, 255, 0.1);
        color: var(--text-secondary);
        padding: 2px 6px;
        border-radius: 10px;
        font-size: 0.8rem;
        margin-left: 5px;
    }
    
    .message-detail .card-header {
        padding-bottom: 20px;
    }
    
    .message-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .message-meta {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        color: var(--text-secondary);
    }
    
    .message-content {
        background-color: var(--dark-surface);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        white-space: pre-line;
    }
    
    .unread-row {
        font-weight: 500;
        background-color: rgba(0, 229, 255, 0.05);
    }
    
    .message-link {
        color: var(--text-primary);
        text-decoration: none;
    }
    
    .message-link:hover {
        color: var(--primary-color);
        text-decoration: underline;
    }
    
    .status-unread {
        background-color: rgba(33, 150, 243, 0.1);
        color: #2196F3;
    }
    
    .status-read {
        background-color: rgba(76, 175, 80, 0.1);
        color: #4CAF50;
    }
    
    .status-archived {
        background-color: rgba(158, 158, 158, 0.1);
        color: #9E9E9E;
    }
    
    .archive-btn {
        background-color: rgba(158, 158, 158, 0.1);
        color: #9E9E9E;
    }
    
    .archive-btn:hover {
        background-color: #9E9E9E;
        color: white;
    }
</style>

<?php 
require_once 'templates/footer.php';
ob_end_flush();
?>