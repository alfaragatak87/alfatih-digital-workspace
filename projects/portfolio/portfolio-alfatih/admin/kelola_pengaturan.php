<?php
ob_start();
$pageTitle = "Pengaturan";
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';
require_once 'templates/header.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'setting_') === 0) {
                $settingKey = substr($key, 8);
                $stmt = $pdo->prepare("INSERT INTO pengaturan (kunci, nilai) VALUES (:kunci, :nilai) ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)");
                $stmt->execute([':kunci' => $settingKey, 'nilai' => trim($value)]);
            }
        }

        $pdo->commit();
        setAlert('success', 'Pengaturan berhasil diperbarui');

    } catch (PDOException $e) {
        $pdo->rollBack();
        setAlert('error', 'Error: ' . $e->getMessage());
    }

    header('Location: ' . BASE_URL . '/admin/kelola_pengaturan.php');
    exit;
}

$stmt = $pdo->query("SELECT kunci, nilai FROM pengaturan");
$settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

function get_setting($key) {
    global $settings;
    return htmlspecialchars($settings[$key] ?? '');
}
?>

<div class="admin-content-container">
    <div class="admin-breadcrumb">
        <div class="breadcrumb-container">
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="breadcrumb-item"><i class="fas fa-home"></i> Dashboard</a>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
            <span class="breadcrumb-item active"><?= $pageTitle ?></span>
        </div>
        <div class="breadcrumb-actions">
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-outline btn-sm"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
            <a href="<?= BASE_URL ?>" target="_blank" class="btn btn-primary btn-sm"><i class="fas fa-external-link-alt"></i> Lihat Situs</a>
        </div>
    </div>
    
    <?php
    // perubahan dimulai
    ?>
    <div class="admin-content-header">
        <h1><i class="fas fa-cog"></i> Pengaturan</h1>
        <p>Konfigurasi pengaturan website Anda</p>
    </div>

    <div class="admin-form-container">
        <div class="setting-tabs">
            <button class="tab-link active" onclick="openTab(event, 'general')"><i class="fas fa-sliders-h"></i> Umum</button>
             <button class="tab-link" onclick="openTab(event, 'social')"><i class="fas fa-share-alt"></i> Sosial & Kontak</button>
            <button class="tab-link" onclick="openTab(event, 'seo')"><i class="fas fa-chart-line"></i> SEO</button>
        </div>

        <form action="" method="POST">
            <div id="general" class="tab-content active">
                <h3>Pengaturan Umum</h3>
                 <div class="form-group">
                    <label for="setting_site_title">Judul Situs</label>
                    <input type="text" id="setting_site_title" name="setting_site_title" class="form-control" value="<?= get_setting('site_title') ?>">
                    <p class="form-text">Judul utama website Anda, ditampilkan di tab browser.</p>
                </div>
                <div class="form-group">
                    <label for="setting_tagline">Slogan / Subjudul</label>
                    <input type="text" id="setting_tagline" name="setting_tagline" class="form-control" value="<?= get_setting('tagline') ?>">
                    <p class="form-text">Frasa singkat dan menarik yang mendeskripsikan situs Anda.</p>
                </div>
                 <div class="form-group">
                    <label for="setting_default_language">Bahasa Default</label>
                     <select id="setting_default_language" name="setting_default_language" class="form-control">
                        <option value="id" <?= get_setting('default_language') == 'id' ? 'selected' : '' ?>>Indonesia</option>
                        <option value="en" <?= get_setting('default_language') == 'en' ? 'selected' : '' ?>>Inggris</option>
                    </select>
                     <p class="form-text">Bahasa default untuk halaman publik.</p>
                </div>
            </div>

            <div id="social" class="tab-content">
                <h3>Media Sosial & Kontak</h3>
                 <div class="form-group">
                    <label for="setting_email">Email Publik</label>
                    <input type="email" id="setting_email" name="setting_email" class="form-control" value="<?= get_setting('email') ?>">
                </div>
                 <div class="form-group">
                    <label for="setting_whatsapp">Nomor WhatsApp</label>
                    <input type="text" id="setting_whatsapp" name="setting_whatsapp" class="form-control" value="<?= get_setting('whatsapp') ?>">
                </div>
                <div class="form-group">
                    <label for="setting_github_url">URL GitHub</label>
                    <input type="url" id="setting_github_url" name="setting_github_url" class="form-control" value="<?= get_setting('github_url') ?>">
                </div>
                <div class="form-group">
                    <label for="setting_linkedin_url">URL LinkedIn</label>
                    <input type="url" id="setting_linkedin_url" name="setting_linkedin_url" class="form-control" value="<?= get_setting('linkedin_url') ?>">
                </div>
                 <div class="form-group">
                    <label for="setting_instagram_url">URL Instagram</label>
                    <input type="url" id="setting_instagram_url" name="setting_instagram_url" class="form-control" value="<?= get_setting('instagram_url') ?>">
                </div>
            </div>

            <div id="seo" class="tab-content">
                <h3>Optimisasi Mesin Pencari (SEO)</h3>
                <div class="form-group">
                    <label for="setting_meta_description">Deskripsi Meta</label>
                    <textarea id="setting_meta_description" name="setting_meta_description" class="form-control" rows="3"><?= get_setting('meta_description') ?></textarea>
                    <p class="form-text">Ringkasan singkat situs Anda untuk mesin pencari (sekitar 155-160 karakter).</p>
                 </div>
                <div class="form-group">
                    <label for="setting_meta_keywords">Kata Kunci Meta</label>
                    <input type="text" id="setting_meta_keywords" name="setting_meta_keywords" class="form-control" value="<?= get_setting('meta_keywords') ?>">
                    <p class="form-text">Kata kunci terkait situs Anda, dipisahkan koma (contoh: web developer, portofolio, php).</p>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Semua Pengaturan</button>
            </div>
        </form>
    </div>
    <?php
    // perubahan selesai
    ?>
</div>

<script>
function openTab(evt, tabName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tab-content");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
    tabcontent[i].classList.remove("active");
  }
  tablinks = document.getElementsByClassName("tab-link");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(tabName).style.display = "block";
  document.getElementById(tabName).classList.add("active");
  evt.currentTarget.className += " active";
}

// Show the first tab by default
document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('.tab-link').click();
});
</script>

<?php
require_once 'templates/footer.php';
ob_end_flush();
?>