<?php
$pageTitle = "Blog";
require_once '../config/koneksi.php';
require_once '../templates/header.php';

// Get all categories
$stmt = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
$categories = $stmt->fetchAll();

// Filter by category if requested
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$whereClause = $categoryFilter > 0 ? "WHERE a.id_kategori = :category" : "";

// Get articles
$query = "SELECT a.*, k.nama_kategori 
          FROM artikel a 
          JOIN kategori k ON a.id_kategori = k.id
          $whereClause
          ORDER BY a.tanggal_dibuat DESC";

$stmt = $pdo->prepare($query);
if ($categoryFilter > 0) {
    $stmt->bindParam(':category', $categoryFilter, PDO::PARAM_INT);
}
$stmt->execute();
$articles = $stmt->fetchAll();
?>

<!-- Blog Section -->
<section class="blog section-padding" id="blog">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h1>My Blog</h1>
            <p>Thoughts, tutorials, and insights on web development</p>
        </div>
        
        <!-- Categories Filter -->
        <div class="blog-categories" data-aos="fade-up">
            <ul>
                <li><a href="<?= BASE_URL ?>/pages/blog.php" class="<?= $categoryFilter == 0 ? 'active' : '' ?>">All</a></li>
                <?php foreach ($categories as $category): ?>
                    <li><a href="<?= BASE_URL ?>/pages/blog.php?category=<?= $category['id'] ?>" class="<?= $categoryFilter == $category['id'] ? 'active' : '' ?>"><?= $category['nama_kategori'] ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <!-- Articles Grid -->
        <div class="articles-grid">
            <?php if (count($articles) > 0): ?>
                <?php foreach ($articles as $index => $article): ?>
                    <div class="article-card" data-aos="fade-up" data-aos-delay="<?= ($index % 3) * 100 ?>">
                        <div class="article-img">
                            <img src="<?= BASE_URL ?>/uploads/articles/<?= $article['gambar_unggulan'] ?>" alt="<?= $article['judul'] ?>">
                            <span class="article-category"><?= $article['nama_kategori'] ?></span>
                        </div>
                        <div class="article-info">
                            <h3><?= $article['judul'] ?></h3>
                            <p><?= truncateText($article['konten'], 150) ?></p>
                            <div class="article-meta">
                                <span><i class="far fa-calendar"></i> <?= date('d M Y', strtotime($article['tanggal_dibuat'])) ?></span>
                                <a href="<?= BASE_URL ?>/pages/blog-single.php?slug=<?= $article['slug'] ?>" class="read-more">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-data">No articles to display in this category.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once '../templates/footer.php'; ?>