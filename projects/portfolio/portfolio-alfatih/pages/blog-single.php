
<?php
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';

// Get article by slug
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    header('Location: ' . BASE_URL . '/pages/blog.php');
    exit;
}

$stmt = $pdo->prepare("SELECT a.*, k.nama_kategori 
                      FROM artikel a 
                      JOIN kategori k ON a.id_kategori = k.id 
                      WHERE a.slug = :slug");
$stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
$stmt->execute();
$article = $stmt->fetch();

if (!$article) {
    header('Location: ' . BASE_URL . '/pages/blog.php');
    exit;
}

// Get related articles (same category, excluding current)
$stmt = $pdo->prepare("SELECT a.*, k.nama_kategori 
                      FROM artikel a 
                      JOIN kategori k ON a.id_kategori = k.id 
                      WHERE a.id_kategori = :category_id AND a.id != :article_id 
                      ORDER BY a.tanggal_dibuat DESC LIMIT 3");
$stmt->bindParam(':category_id', $article['id_kategori'], PDO::PARAM_INT);
$stmt->bindParam(':article_id', $article['id'], PDO::PARAM_INT);
$stmt->execute();
$relatedArticles = $stmt->fetchAll();

$pageTitle = $article['judul'];
require_once '../templates/header.php';
?>

<!-- Blog Single Article -->
<section class="blog-single section-padding">
    <div class="container">
        <!-- Article Header -->
        <div class="article-header" data-aos="fade-up">
            <h1><?= $article['judul'] ?></h1>
            <div class="article-meta">
                <span><i class="fas fa-folder"></i> <?= $article['nama_kategori'] ?></span>
                <span><i class="far fa-calendar"></i> <?= date('d M Y', strtotime($article['tanggal_dibuat'])) ?></span>
            </div>
        </div>
        
        <!-- Featured Image -->
        <div class="article-featured-image" data-aos="fade-up">
            <img src="<?= BASE_URL ?>/uploads/articles/<?= $article['gambar_unggulan'] ?>" alt="<?= $article['judul'] ?>">
        </div>
        
        <!-- Article Content -->
        <div class="article-content" data-aos="fade-up">
            <?= nl2br($article['konten']) ?>
        </div>
        
        <!-- Related Articles -->
        <?php if (count($relatedArticles) > 0): ?>
        <div class="related-articles" data-aos="fade-up">
            <h3>Related Articles</h3>
            <div class="articles-grid">
                <?php foreach ($relatedArticles as $relatedArticle): ?>
                    <div class="article-card">
                        <div class="article-img">
                            <img src="<?= BASE_URL ?>/uploads/articles/<?= $relatedArticle['gambar_unggulan'] ?>" alt="<?= $relatedArticle['judul'] ?>">
                            <span class="article-category"><?= $relatedArticle['nama_kategori'] ?></span>
                        </div>
                        <div class="article-info">
                            <h3><?= $relatedArticle['judul'] ?></h3>
                            <p><?= truncateText($relatedArticle['konten'], 100) ?></p>
                            <div class="article-meta">
                                <span><i class="far fa-calendar"></i> <?= date('d M Y', strtotime($relatedArticle['tanggal_dibuat'])) ?></span>
                                <a href="<?= BASE_URL ?>/pages/blog-single.php?slug=<?= $relatedArticle['slug'] ?>" class="read-more">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Back to Blog -->
        <div class="back-to-blog" data-aos="fade-up">
            <a href="<?= BASE_URL ?>/pages/blog.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Blog</a>
        </div>
    </div>
</section>

<?php require_once '../templates/footer.php'; ?>