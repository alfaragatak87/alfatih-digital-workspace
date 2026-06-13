<?php
ob_start();
$pageTitle = "Pengaturan Tema";
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';

$availableThemes = [
    'default' => ['name' => 'Default (Biru Gelap)', 'image' => 'theme-default.jpg'],
    'dark-purple' => ['name' => 'Ungu Gelap', 'image' => 'theme-purple.jpg'],
    'dark-green' => ['name' => 'Hijau Gelap', 'image' => 'theme-green.jpg'],
    'dark-orange' => ['name' => 'Oranye Gelap', 'image' => 'theme-orange.jpg'],
    'dark-red' => ['name' => 'Merah Gelap', 'image' => 'theme-red.jpg']
];

// perubahan dimulai: Memperbaiki struktur logika yang error
if (isset($_GET['action']) && $_GET['action'] === 'set_theme' && isset($_GET['theme'])) {
    $selectedTheme = $_GET['theme'];
    if (array_key_exists($selectedTheme, $availableThemes)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO pengaturan (kunci, nilai) VALUES (:kunci, :nilai) ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)");
            $stmt->execute(['kunci' => 'theme', 'nilai' => $selectedTheme]);
            setAlert('success', 'Tema "' . $availableThemes[$selectedTheme]['name'] . '" berhasil diaktifkan.');
        } catch (PDOException $e) {
            setAlert('error', 'Gagal menyimpan tema: ' . $e->getMessage());
        }
    } else {
        setAlert('error', 'Tema tidak valid.');
    }
    header('Location: ' . BASE_URL . '/admin/kelola_tema.php');
    exit;
}
// perubahan selesai

require_once 'templates/header.php';

// Simpan semua pengaturan kustomisasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_customizations'])) {
    $settingsToUpdate = [
        'theme_color_type' => $_POST['theme_color_type'] ?? 'solid',
        'theme_color_solid' => $_POST['theme_color_solid'] ?? '#00e5ff',
        'theme_color_gradient_start' => $_POST['theme_color_gradient_start'] ?? '#00e5ff',
        'theme_color_gradient_end' => $_POST['theme_color_gradient_end'] ?? '#7c4dff',
        'theme_color_gradient_angle' => $_POST['theme_color_gradient_angle'] ?? '90',
        'enable_particles' => isset($_POST['enable_particles']) ? 1 : 0,
        'animation_speed' => $_POST['animation_speed'] ?? 'normal'
    ];

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO pengaturan (kunci, nilai) VALUES (:kunci, :nilai) ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)");
        foreach ($settingsToUpdate as $key => $value) {
            $stmt->execute(['kunci' => $key, 'nilai' => $value]);
        }
        $pdo->commit();
        setAlert('success', 'Kustomisasi tema berhasil diperbarui!');
    } catch (PDOException $e) {
        $pdo->rollBack();
        setAlert('error', 'Error: ' . $e->getMessage());
    }
    header('Location: ' . BASE_URL . '/admin/kelola_tema.php');
    exit;
}

// Ambil semua pengaturan dari DB
$defaultSettings = [
    'theme' => 'default', 'theme_color_type' => 'solid', 'theme_color_solid' => '#00e5ff',
    'theme_color_gradient_start' => '#00e5ff', 'theme_color_gradient_end' => '#7c4dff',
    'theme_color_gradient_angle' => '90', 'enable_particles' => 1, 'animation_speed' => 'normal'
];
try {
    $stmt = $pdo->query("SELECT kunci, nilai FROM pengaturan");
    $dbSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $currentSettings = array_merge($defaultSettings, $dbSettings);
} catch (Exception $e) {
    $currentSettings = $defaultSettings;
}
?>

<style>
    .theme-customizer-grid { display: grid; grid-template-columns: 3fr 2fr; gap: 25px; }
    .theme-selection-list { display: flex; flex-direction: column; gap: 15px; }
    .theme-card-link { text-decoration: none; }
    .theme-card { display: flex; align-items: center; gap: 15px; padding: 10px; border-radius: 12px; background-color: var(--dark-surface); border: 2px solid transparent; transition: all 0.3s ease; }
    .theme-card.active { border-color: var(--primary-border-color); background-color: rgba(0, 229, 255, 0.1); }
    .theme-card:hover:not(.active) { background-color: var(--dark-card); transform: translateX(5px); }
    .theme-image-sm { width: 80px; height: 50px; border-radius: 8px; background-size: cover; background-position: center; flex-shrink: 0; }
    .theme-info-sm h4 { margin: 0; font-size: 1rem; color: var(--text-primary); }
    .color-mode-selector { display: flex; gap: 10px; margin-bottom: 20px; }
    .gradient-controls { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; align-items: center; }
    .angle-control label { display: block; margin-bottom: 5px; }
    .angle-control input[type="range"] { width: 100%; }
    .live-preview { margin-top: 20px; padding: 20px; border-radius: 12px; border: 1px dashed rgba(255,255,255,0.1); }
    .live-preview h4 { margin-top: 0; }
    .preview-btn { padding: 12px 25px; border-radius: 8px; font-weight: 500; color: #0a0a1a; text-align: center; }
    a.theme-card-link { text-decoration: none; display: block; }
    @media (max-width: 992px) { .theme-customizer-grid { grid-template-columns: 1fr; } }
</style>

<div class="admin-content-container">
    <div class="admin-breadcrumb">
        <div class="breadcrumb-container">
            <a href="<?= BASE_URL ?>/admin/dashboard.php" class="breadcrumb-item"><i class="fas fa-home"></i> Dashboard</a>
            <span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>
            <span class="breadcrumb-item active"><?= htmlspecialchars($pageTitle) ?></span>
        </div>
    </div>

    <div class="admin-content-header">
        <h1><i class="fas fa-paint-brush"></i> Pengaturan Tema</h1>
        <p>Sesuaikan tampilan dan nuansa website portofolio Anda secara bebas.</p>
    </div>

    <form action="kelola_tema.php" method="POST" class="admin-form-container">
        <input type="hidden" name="save_customizations" value="1">
        <div class="theme-customizer-grid">
            
            <div class="left-column">
                <div class="admin-card">
                    <div class="card-header"><h2><i class="fas fa-palette"></i> 1. Pilih Tema Dasar</h2></div>
                    <div class="card-body">
                        <p class="form-text">Klik pada salah satu tema di bawah untuk mengaktifkannya secara langsung.</p>
                        <div class="theme-selection-list">
                            <?php foreach ($availableThemes as $themeKey => $theme): ?>
                                <a href="?action=set_theme&theme=<?= $themeKey ?>" class="theme-card-link" title="Aktifkan tema <?= htmlspecialchars($theme['name']) ?>">
                                    <div class="theme-card <?= $currentSettings['theme'] === $themeKey ? 'active' : '' ?>">
                                        <div class="theme-image-sm" style="background-image: url('<?= BASE_URL ?>/assets/img/themes/<?= htmlspecialchars($theme['image']) ?>')"></div>
                                        <div class="theme-info-sm"><h4><?= htmlspecialchars($theme['name']) ?></h4></div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="admin-card">
                    <div class="card-header"><h2><i class="fas fa-film"></i> 3. Animasi & Efek</h2></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="enable_particles">Efek Latar Belakang</label>
                            <div class="toggle-switch">
                                <input type="checkbox" id="enable_particles" name="enable_particles" class="toggle-input" <?= $currentSettings['enable_particles'] ? 'checked' : '' ?> value="1">
                                <label for="enable_particles" class="toggle-label"></label>
                                <span class="toggle-text"><?= $currentSettings['enable_particles'] ? 'Aktif' : 'Nonaktif' ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Kecepatan Animasi</label>
                            <div class="radio-group">
                                <?php foreach(['slow' => 'Lambat', 'normal' => 'Normal', 'fast' => 'Cepat', 'none' => 'Tidak Ada'] as $speedKey => $speedValue): ?>
                                <div class="radio-item">
                                    <input type="radio" id="speed_<?= $speedKey ?>" name="animation_speed" value="<?= $speedKey ?>" <?= $currentSettings['animation_speed'] === $speedKey ? 'checked' : '' ?>>
                                    <label for="speed_<?= $speedKey ?>"><?= ucfirst($speedValue) ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="right-column">
                <div class="admin-card">
                    <div class="card-header"><h2><i class="fas fa-fill-drip"></i> 2. Kustomisasi Warna</h2></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Mode Warna</label>
                            <div class="radio-group color-mode-selector">
                                <div class="radio-item">
                                    <input type="radio" id="mode_solid" name="theme_color_type" value="solid" <?= $currentSettings['theme_color_type'] === 'solid' ? 'checked' : '' ?>>
                                    <label for="mode_solid">Solid</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" id="mode_gradient" name="theme_color_type" value="gradient" <?= $currentSettings['theme_color_type'] === 'gradient' ? 'checked' : '' ?>>
                                    <label for="mode_gradient">Gradien</label>
                                </div>
                            </div>
                        </div>

                        <div id="solid_controls" class="form-group">
                            <label for="theme_color_solid">Pilih Warna</label>
                            <input type="color" id="theme_color_solid" name="theme_color_solid" value="<?= htmlspecialchars($currentSettings['theme_color_solid']) ?>" class="form-control">
                        </div>

                        <div id="gradient_controls" class="form-group">
                            <div class="gradient-controls">
                                <div class="form-group">
                                    <label for="theme_color_gradient_start">Warna Awal</label>
                                    <input type="color" id="theme_color_gradient_start" name="theme_color_gradient_start" value="<?= htmlspecialchars($currentSettings['theme_color_gradient_start']) ?>" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="theme_color_gradient_end">Warna Akhir</label>
                                    <input type="color" id="theme_color_gradient_end" name="theme_color_gradient_end" value="<?= htmlspecialchars($currentSettings['theme_color_gradient_end']) ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group angle-control">
                                <label for="theme_color_gradient_angle">Arah Gradien: <span id="angle_value"><?= $currentSettings['theme_color_gradient_angle'] ?></span>°</label>
                                <input type="range" id="theme_color_gradient_angle" name="theme_color_gradient_angle" min="0" max="360" value="<?= $currentSettings['theme_color_gradient_angle'] ?>" class="form-control">
                            </div>
                        </div>

                        <div class="live-preview">
                            <h4>Pratinjau Tombol</h4>
                            <div id="previewBtn" class="preview-btn">Contoh Tombol</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions" style="justify-content: center; margin-top: 10px;">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Simpan Kustomisasi</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modeSolid = document.getElementById('mode_solid');
    const modeGradient = document.getElementById('mode_gradient');
    const solidControls = document.getElementById('solid_controls');
    const gradientControls = document.getElementById('gradient_controls');
    const solidColorInput = document.getElementById('theme_color_solid');
    const gradStartInput = document.getElementById('theme_color_gradient_start');
    const gradEndInput = document.getElementById('theme_color_gradient_end');
    const gradAngleInput = document.getElementById('theme_color_gradient_angle');
    const angleValueSpan = document.getElementById('angle_value');
    const previewBtn = document.getElementById('previewBtn');

    function toggleColorControls() {
        if (modeSolid.checked) {
            solidControls.style.display = 'block';
            gradientControls.style.display = 'none';
        } else {
            solidControls.style.display = 'none';
            gradientControls.style.display = 'block';
        }
        updatePreview();
    }

    function updatePreview() {
        if (modeSolid.checked) {
            previewBtn.style.background = solidColorInput.value;
        } else {
            const angle = gradAngleInput.value;
            const startColor = gradStartInput.value;
            const endColor = gradEndInput.value;
            previewBtn.style.background = `linear-gradient(${angle}deg, ${startColor}, ${endColor})`;
            angleValueSpan.textContent = angle;
        }
    }

    modeSolid.addEventListener('change', toggleColorControls);
    modeGradient.addEventListener('change', toggleColorControls);
    solidColorInput.addEventListener('input', updatePreview);
    gradStartInput.addEventListener('input', updatePreview);
    gradEndInput.addEventListener('input', updatePreview);
    gradAngleInput.addEventListener('input', updatePreview);

    const toggleInput = document.getElementById('enable_particles');
    if (toggleInput) {
        const toggleText = toggleInput.parentElement.querySelector('.toggle-text');
        toggleInput.addEventListener('change', function() {
            toggleText.textContent = this.checked ? 'Aktif' : 'Nonaktif';
        });
    }

    toggleColorControls();
});
</script>

<?php
require_once 'templates/footer.php';
ob_end_flush();
?>