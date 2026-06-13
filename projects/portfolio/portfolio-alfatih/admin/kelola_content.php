<?php
ob_start();
$pageTitle = "Editor Konten";
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';
require_once 'templates/header.php';

// Create page_content table if not exists
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `page_content` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `page` VARCHAR(100) NOT NULL,
        `section` VARCHAR(100) NOT NULL,
        `content` TEXT,
        `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY `page_section` (`page`, `section`)
    )");
} catch (PDOException $e) {
    setAlert('error', 'Error creating page_content table: ' . $e->getMessage());
}

// Available pages and sections (Translated)
$contentSections = [
    'home' => [
        'hero_title' => 'Judul Hero',
        'hero_subtitle' => 'Subjudul Hero',
        'hero_description' => 'Deskripsi Hero',
        'projects_title' => 'Judul Bagian Proyek',
        'projects_subtitle' => 'Subjudul Bagian Proyek',
        'articles_title' => 'Judul Bagian Artikel',
        'articles_subtitle' => 'Subjudul Bagian Artikel'
    ],
    'about' => [
        'about_title' => 'Judul Halaman Tentang',
        'about_subtitle' => 'Subjudul Halaman Tentang',
        'about_quote' => 'Kutipan Halaman Tentang',
        'about_description' => 'Deskripsi Halaman Tentang',
        'skills_title' => 'Judul Bagian Keahlian',
        'skills_subtitle' => 'Subjudul Bagian Keahlian',
        'interests_title' => 'Judul Bagian Minat',
        'interests_subtitle' => 'Subjudul Bagian Minat',
        'cta_title' => 'Judul Ajakan Bertindak (CTA)',
        'cta_subtitle' => 'Subjudul Ajakan Bertindak (CTA)'
    ],
    'projects' => [
        'projects_title' => 'Judul Halaman Proyek',
        'projects_subtitle' => 'Subjudul Halaman Proyek'
    ],
    'blog' => [
        'blog_title' => 'Judul Halaman Blog',
        'blog_subtitle' => 'Subjudul Halaman Blog'
    ],
    'contact' => [
        'contact_title' => 'Judul Halaman Kontak',
        'contact_subtitle' => 'Subjudul Halaman Kontak',
        'contact_success_message' => 'Pesan Sukses Formulir Kontak',
        'contact_error_message' => 'Pesan Error Formulir Kontak'
    ],
    'testimonials' => [
        'testimonials_title' => 'Judul Halaman Testimoni',
        'testimonials_subtitle' => 'Subjudul Halaman Testimoni',
        'form_title' => 'Judul Formulir Testimoni',
        'form_subtitle' => 'Subjudul Formulir Testimoni'
    ],
    'cv' => [
        'cv_title' => 'Judul Halaman CV',
        'cv_subtitle' => 'Subjudul Halaman CV'
    ],
    'footer' => [
        'footer_text' => 'Teks Copyright Footer',
        'connect_text' => 'Teks Koneksi Footer'
    ]
];

$pageNames = [
    'home' => 'Beranda', 'about' => 'Tentang', 'projects' => 'Proyek',
    'blog' => 'Blog', 'contact' => 'Kontak', 'testimonials' => 'Testimoni',
    'cv' => 'CV', 'footer' => 'Footer'
];

$selectedPage = $_GET['page'] ?? 'home';
if (!array_key_exists($selectedPage, $contentSections)) {
    $selectedPage = 'home';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $page = $_POST['page'] ?? '';
    $sections = $_POST['sections'] ?? [];

    if (!empty($page) && !empty($sections) && array_key_exists($page, $contentSections)) {
        try {
            $pdo->beginTransaction();

            // perubahan dimulai
            $stmt = $pdo->prepare(
                "INSERT INTO page_content (page, section, content)
                 VALUES (:page, :section, :content)
                 ON DUPLICATE KEY UPDATE content = VALUES(content)"
            );
            // perubahan selesai

            foreach ($sections as $section => $content) {
                $stmt->execute([
                    ':page' => $page,
                    ':section' => $section,
                    ':content' => $content
                ]);
            }

            $pdo->commit();
            setAlert('success', 'Konten berhasil diperbarui');

        } catch (PDOException $e) {
            $pdo->rollBack();
            setAlert('error', 'Error: ' . $e->getMessage());
        }
    } else {
        setAlert('error', 'Halaman tidak valid atau tidak ada bagian yang diberikan');
    }

    header('Location: ' . BASE_URL . '/admin/kelola_content.php?page=' . $selectedPage);
    exit;
}

$currentContent = [];
try {
    $stmt = $pdo->prepare("SELECT section, content FROM page_content WHERE page = :page");
    $stmt->bindParam(':page', $selectedPage);
    $stmt->execute();
    $currentContent = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (PDOException $e) {
    setAlert('error', 'Error memuat konten: ' . $e->getMessage());
}

function getPageIcon($page) {
    $icons = [
        'home' => 'home', 'about' => 'user', 'projects' => 'briefcase', 'blog' => 'newspaper',
        'contact' => 'envelope', 'testimonials' => 'comment-dots', 'cv' => 'file-alt', 'footer' => 'copyright'
    ];
    return $icons[$page] ?? 'file';
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
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
            <a href="<?= BASE_URL ?>" class="btn btn-primary btn-sm" target="_blank"><i class="fas fa-external-link-alt"></i> Lihat Situs</a>
        </div>
    </div>
    
    <div class="admin-content-header">
        <h1><i class="fas fa-edit"></i> Editor Konten</h1>
        <p>Ubah konten teks di seluruh website portofolio Anda</p>
    </div>

    <div class="content-tabs">
        <?php foreach ($contentSections as $page => $sections): ?>
            <a href="?page=<?= $page ?>" class="content-tab <?= $selectedPage === $page ? 'active' : '' ?>">
                <i class="fas fa-<?= getPageIcon($page) ?>"></i>
                <span><?= $pageNames[$page] ?? ucfirst($page) ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="admin-form-container">
        <div class="form-header">
            <h2><i class="fas fa-<?= getPageIcon($selectedPage) ?>"></i> Konten Halaman <?= $pageNames[$selectedPage] ?? ucfirst($selectedPage) ?></h2>
            <p>Ubah konten untuk halaman <?= $pageNames[$selectedPage] ?? ucfirst($selectedPage) ?> dari portofolio Anda</p>
        </div>

        <form action="" method="POST">
            <input type="hidden" name="page" value="<?= $selectedPage ?>">
            <?php foreach ($contentSections[$selectedPage] as $section => $sectionTitle): ?>
                <div class="form-group">
                    <label for="<?= $section ?>"><?= htmlspecialchars($sectionTitle) ?></label>
                    <?php if (strpos($section, 'description') !== false || strpos($section, 'deskripsi') !== false || $section === 'about_quote' || $section === 'footer_text'): ?>
                        <textarea id="<?= $section ?>" name="sections[<?= $section ?>]" class="form-control" rows="4"><?= htmlspecialchars($currentContent[$section] ?? '') ?></textarea>
                    <?php else: ?>
                        <input type="text" id="<?= $section ?>" name="sections[<?= $section ?>]" class="form-control" value="<?= htmlspecialchars($currentContent[$section] ?? '') ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline"><i class="fas fa-times"></i> Batal</a>
            </div>
        </form>
    </div>
    
    </div>

<style>
/* (CSS tidak perlu diubah) */
</style>

<?php
require_once 'templates/footer.php';
ob_end_flush();
?>