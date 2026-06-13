<?php
require_once '../config/constants.php';
require_once '../includes/helpers.php';
require_once '../config/koneksi.php';

// Start session and check if logged in
startSession();

if (!isset($_SESSION['admin_id'])) {
    setAlert('error', 'Please login to access the admin panel');
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

// Update last login time
$stmt = $pdo->prepare("UPDATE admin SET last_login = NOW() WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['admin_id']);
$stmt->execute();

// Get admin info
$stmt = $pdo->prepare("SELECT a.*, r.role_name, r.permissions 
                      FROM admin a 
                      LEFT JOIN admin_roles r ON a.role_id = r.id 
                      WHERE a.id = :id");
$stmt->bindParam(':id', $_SESSION['admin_id']);
$stmt->execute();
$admin = $stmt->fetch();

// Parse permissions
$permissions = json_decode($admin['permissions'] ?? '{"all":true}', true);

// Check for unread notifications
$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE admin_id = :admin_id AND is_read = 0");
$stmt->bindParam(':admin_id', $_SESSION['admin_id']);
$stmt->execute();
$unreadNotifications = $stmt->fetchColumn();

// Get basic statistics
$statsProyek = $pdo->query("SELECT COUNT(*) FROM proyek")->fetchColumn();
$statsArtikel = $pdo->query("SELECT COUNT(*) FROM artikel")->fetchColumn();
$statsDokumen = $pdo->query("SELECT COUNT(*) FROM dokumen")->fetchColumn();
$statsMessages = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$statsTestimonials = $pdo->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();
$statsMedia = $pdo->query("SELECT COUNT(*) FROM media_library")->fetchColumn();

// Get latest content for each type
$stmt = $pdo->query("SELECT 'project' as type, id, judul as title, tanggal_dibuat as date FROM proyek ORDER BY tanggal_dibuat DESC LIMIT 5");
$latestProjects = $stmt->fetchAll();

$stmt = $pdo->query("SELECT 'article' as type, a.id, a.judul as title, a.tanggal_dibuat as date, k.nama_kategori as category 
                     FROM artikel a 
                     JOIN kategori k ON a.id_kategori = k.id 
                     ORDER BY a.tanggal_dibuat DESC LIMIT 5");
$latestArticles = $stmt->fetchAll();

// Combine and sort by date
$latestContent = array_merge($latestProjects, $latestArticles);
usort($latestContent, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});
$latestContent = array_slice($latestContent, 0, 5);

// Get latest messages
$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
$latestMessages = $stmt->fetchAll();

// Get latest testimonials
$stmt = $pdo->query("SELECT * FROM testimonials ORDER BY tanggal_dibuat DESC LIMIT 5");
$latestTestimonials = $stmt->fetchAll();

// Get recent login history
$stmt = $pdo->prepare("SELECT * FROM admin_logs WHERE admin_id = :admin_id AND action LIKE 'Logged in%' ORDER BY created_at DESC LIMIT 5");
$stmt->bindParam(':admin_id', $_SESSION['admin_id']);
$stmt->execute();
$loginHistory = $stmt->fetchAll();

// Get visitor analytics data for the past 30 days
$thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
$stmt = $pdo->prepare("
    SELECT DATE(visit_date) as date, COUNT(*) as count
    FROM website_analytics
    WHERE visit_date >= :start_date
    GROUP BY DATE(visit_date)
    ORDER BY date ASC
");
$stmt->bindParam(':start_date', $thirtyDaysAgo);
$stmt->execute();
$visitorData = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Fill in any missing dates
$dateRange = [];
$currentDate = new DateTime($thirtyDaysAgo);
$endDate = new DateTime();

while ($currentDate <= $endDate) {
    $dateStr = $currentDate->format('Y-m-d');
    $dateRange[$dateStr] = $visitorData[$dateStr] ?? 0;
    $currentDate->modify('+1 day');
}

// Format for chart
$dates = array_keys($dateRange);
$counts = array_values($dateRange);

// Get most viewed pages
$stmt = $pdo->query("
    SELECT page_url, page_title, COUNT(*) as view_count
    FROM website_analytics
    GROUP BY page_url
    ORDER BY view_count DESC
    LIMIT 5
");
$popularPages = $stmt->fetchAll();

// Get visitor by device
$stmt = $pdo->query("
    SELECT visitor_device, COUNT(*) as count
    FROM website_analytics
    GROUP BY visitor_device
    ORDER BY count DESC
");
$deviceStats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Get visitor by country
$stmt = $pdo->query("
    SELECT visitor_country, COUNT(*) as count
    FROM website_analytics
    GROUP BY visitor_country
    ORDER BY count DESC
    LIMIT 10
");
$countryStats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Page title
$pageTitle = "Dashboard";
include '../admin/templates/header.php';
?>

<div class="admin-content-container">
    <div class="admin-breadcrumb">
        <div class="breadcrumb-container">
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="breadcrumb-item"><i class="fas fa-home"></i> Dashboard</a>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
            <span class="breadcrumb-item active"><?= $pageTitle ?></span>
        </div>
        <div class="breadcrumb-actions">
            <a href="<?= BASE_URL ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fas fa-external-link-alt"></i> View Site</a>
        </div>
    </div>

    <div class="admin-content-header">
        <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
        <p>Welcome back, <?= htmlspecialchars($admin['username']) ?>! Here's an overview of your website.</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="stats-container">
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="stats-info">
                <h3>Projects</h3>
                <p><?= $statsProyek ?></p>
            </div>
        </div>
        
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-newspaper"></i>
            </div>
            <div class="stats-info">
                <h3>Articles</h3>
                <p><?= $statsArtikel ?></p>
            </div>
        </div>
        
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stats-info">
                <h3>Documents</h3>
                <p><?= $statsDokumen ?></p>
            </div>
        </div>
        
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-photo-video"></i>
            </div>
            <div class="stats-info">
                <h3>Media Files</h3>
                <p><?= $statsMedia ?></p>
            </div>
        </div>
        
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stats-info">
                <h3>Messages</h3>
                <p><?= $statsMessages ?></p>
            </div>
        </div>
        
        <div class="stats-card">
            <div class="stats-icon">
                <i class="fas fa-comment-dots"></i>
            </div>
            <div class="stats-info">
                <h3>Testimonials</h3>
                <p><?= $statsTestimonials ?></p>
            </div>
        </div>
    </div>
    
    <!-- Analytics Dashboard -->
    <div class="admin-grid">
        <div class="admin-main">
            <!-- Visitor Chart -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h2><i class="fas fa-chart-line"></i> Website Traffic (Last 30 Days)</h2>
                </div>
                <div class="card-body">
                    <canvas id="visitorChart" height="300"></canvas>
                </div>
            </div>
            
            <!-- Latest Content -->
            <div class="admin-card">
                <div class="card-header">
                    <h2><i class="fas fa-clock"></i> Recent Content</h2>
                </div>
                <div class="card-body">
                    <?php if (count($latestContent) > 0): ?>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Title</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($latestContent as $content): ?>
                                        <tr>
                                            <td>
                                                <?php if ($content['type'] === 'project'): ?>
                                                    <span class="badge badge-primary">Project</span>
                                                <?php else: ?>
                                                    <span class="badge badge-info">Article</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($content['title']) ?></td>
                                            <td><?= date('d M Y', strtotime($content['date'])) ?></td>
                                            <td>
                                                <?php if ($content['type'] === 'project'): ?>
                                                    <a href="<?= BASE_URL ?>/admin/kelola_proyek.php?action=edit&id=<?= $content['id'] ?>" class="action-btn edit-btn" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= BASE_URL ?>/admin/kelola_artikel.php?action=edit&id=<?= $content['id'] ?>" class="action-btn edit-btn" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?= BASE_URL ?>/<?= $content['type'] === 'project' ? 'pages/projects.php' : 'pages/blog-single.php?slug=' . $content['slug'] ?>" class="action-btn view-btn" title="View" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="no-data">No content available yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="admin-sidebar-right">
            <!-- Popular Pages -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h2><i class="fas fa-star"></i> Popular Pages</h2>
                </div>
                <div class="card-body">
                    <?php if (count($popularPages) > 0): ?>
                        <ul class="popular-pages-list">
                            <?php foreach ($popularPages as $page): ?>
                                <li>
                                    <div class="page-info">
                                        <h4><?= htmlspecialchars($page['page_title'] ?? basename($page['page_url'])) ?></h4>
                                        <small><?= htmlspecialchars($page['page_url']) ?></small>
                                    </div>
                                    <div class="page-views">
                                        <span class="view-count"><?= $page['view_count'] ?></span>
                                        <small>views</small>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-data">No analytics data available yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Visitor by Device -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h2><i class="fas fa-mobile-alt"></i> Visitors by Device</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($deviceStats)): ?>
                        <canvas id="deviceChart" height="200"></canvas>
                    <?php else: ?>
                        <p class="no-data">No device data available yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Visitor by Country -->
            <div class="admin-card mb-4">
                <div class="card-header">
                    <h2><i class="fas fa-globe"></i> Top Countries</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($countryStats)): ?>
                        <ul class="country-list">
                            <?php foreach ($countryStats as $country => $count): ?>
                                <li>
                                    <div class="country-name">
                                        <?= htmlspecialchars($country ?: 'Unknown') ?>
                                    </div>
                                    <div class="country-count">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?= min(100, ($count / max($countryStats) * 100)) ?>%"></div>
                                        </div>
                                        <span class="count-value"><?= $count ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-data">No country data available yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Login History -->
            <div class="admin-card">
                <div class="card-header">
                    <h2><i class="fas fa-history"></i> Recent Logins</h2>
                </div>
                <div class="card-body">
                    <?php if (count($loginHistory) > 0): ?>
                        <ul class="login-history-list">
                            <?php foreach ($loginHistory as $login): ?>
                                <li>
                                    <i class="fas fa-sign-in-alt"></i>
                                    <div class="login-info">
                                        <span class="login-date"><?= date('d M Y, H:i', strtotime($login['created_at'])) ?></span>
                                        <span class="login-ip"><?= htmlspecialchars($login['ip_address']) ?></span>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-data">No login history available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="quick-actions-grid">
            <a href="<?= BASE_URL ?>/admin/kelola_proyek.php?action=add" class="quick-action-btn">
                <i class="fas fa-plus-circle"></i>
                <span>Add New Project</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/kelola_artikel.php?action=add" class="quick-action-btn">
                <i class="fas fa-plus-circle"></i>
                <span>Add New Article</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/media_library.php" class="quick-action-btn">
                <i class="fas fa-photo-video"></i>
                <span>Media Library</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/backup.php" class="quick-action-btn">
                <i class="fas fa-database"></i>
                <span>Backup System</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/kelola_testimonial.php" class="quick-action-btn">
                <i class="fas fa-comment-dots"></i>
                <span>Manage Testimonials</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/notifications.php" class="quick-action-btn">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
                <?php if ($unreadNotifications > 0): ?>
                    <span class="count-badge"><?= $unreadNotifications ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
    
    <!-- Latest Messages -->
    <div class="latest-messages">
        <div class="section-header">
            <h2><i class="fas fa-envelope"></i> Latest Messages</h2>
            <a href="<?= BASE_URL ?>/admin/kelola_pesan.php" class="view-all-btn">View All</a>
        </div>
        
        <?php if (count($latestMessages) > 0): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latestMessages as $message): ?>
                    <tr class="<?= $message['status'] === 'unread' ? 'unread-row' : '' ?>">
                        <td><?= htmlspecialchars($message['name']) ?></td>
                        <td><?= htmlspecialchars($message['email']) ?></td>
                        <td><?= htmlspecialchars($message['subject']) ?></td>
                        <td><?= date('d M Y H:i', strtotime($message['created_at'])) ?></td>
                        <td>
                            <span class="status-badge status-<?= $message['status'] ?>">
                                <?= ucfirst($message['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= BASE_URL ?>/admin/kelola_pesan.php?action=view&id=<?= $message['id'] ?>" class="action-btn view-btn" title="View Message">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/admin/kelola_pesan.php?action=delete&id=<?= $message['id'] ?>" class="action-btn delete-btn" title="Delete Message" onclick="return confirm('Are you sure you want to delete this message?')">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <p class="no-data">No messages to display yet.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Visitor Chart
const visitorCtx = document.getElementById('visitorChart').getContext('2d');
const visitorChart = new Chart(visitorCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_map(function($date) { return date('d M', strtotime($date)); }, $dates)) ?>,
        datasets: [{
            label: 'Visitors',
            data: <?= json_encode($counts) ?>,
            backgroundColor: 'rgba(0, 229, 255, 0.2)',
            borderColor: 'rgba(0, 229, 255, 1)',
            borderWidth: 2,
            tension: 0.4,
            pointBackgroundColor: 'rgba(0, 229, 255, 1)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                mode: 'index',
                intersect: false,
                backgroundColor: 'rgba(18, 18, 37, 0.9)',
                titleColor: '#fff',
                bodyColor: '#b3b3b3',
                borderColor: 'rgba(0, 229, 255, 0.3)',
                borderWidth: 1,
                padding: 12,
                titleFont: {
                    size: 14,
                    weight: 'bold'
                },
                bodyFont: {
                    size: 13
                },
                displayColors: false
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#757575'
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(255, 255, 255, 0.05)'
                },
                ticks: {
                    precision: 0,
                    color: '#757575'
                }
            }
        }
    }
});

// Device Chart
<?php if (!empty($deviceStats)): ?>
const deviceCtx = document.getElementById('deviceChart').getContext('2d');
const deviceChart = new Chart(deviceCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_keys($deviceStats)) ?>,
        datasets: [{
            data: <?= json_encode(array_values($deviceStats)) ?>,
            backgroundColor: [
                'rgba(0, 229, 255, 0.8)',
                'rgba(124, 77, 255, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(244, 67, 54, 0.8)',
                'rgba(76, 175, 80, 0.8)'
            ],
            borderColor: [
                'rgba(0, 229, 255, 1)',
                'rgba(124, 77, 255, 1)',
                'rgba(255, 193, 7, 1)',
                'rgba(244, 67, 54, 1)',
                'rgba(76, 175, 80, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#b3b3b3',
                    padding: 15,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(18, 18, 37, 0.9)',
               titleColor: '#fff',
               bodyColor: '#b3b3b3',
               borderColor: 'rgba(0, 229, 255, 0.3)',
               borderWidth: 1,
               padding: 12,
               titleFont: {
                   size: 14,
                   weight: 'bold'
               },
               bodyFont: {
                   size: 13
               }
           }
       },
       cutout: '70%'
   }
});
<?php endif; ?>
</script>

<?php include '../admin/templates/footer.php'; ?>