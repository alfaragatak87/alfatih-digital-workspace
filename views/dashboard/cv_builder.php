<?php
/**
 * views/dashboard/cv_builder.php — VERSI 2.0 (Universal)
 * ==========================================================
 * Perombakan total CV Builder:
 *  - 6 Tab (Identitas, Sosial Media, Pendidikan, Pengalaman,
 *    Keahlian + Bahasa, Portfolio)
 *  - Skill Autocomplete dari 50+ preset, bukan input bebas
 *  - Multi Social Media — array dinamis, pilih platform
 *  - Profesi relevan untuk semua kalangan
 *  - Desain card-based yang intuitif & mobile-first
 *  - Completion score real-time
 * ==========================================================
 */
if (!defined('SITE_URL')) exit;

$active_tab = $_GET['tab'] ?? 'identitas';

// Baca dari profile_data dengan struktur v2 (lihat schema)
$ident_d  = $profile_data['identitas']   ?? [];
$sosmed_d = $profile_data['media_sosial'] ?? [];
$edu_d    = $profile_data['pendidikan']  ?? [];
$exp_d    = $profile_data['pengalaman']  ?? [];
// Skill v2: dua array terpisah
$skill_umum_d   = $profile_data['skill_umum']   ?? $profile_data['keahlian'] ?? [];
$skill_teknis_d = $profile_data['skill_teknis'] ?? [];
$bahasa_d       = $profile_data['bahasa']        ?? [];
$porto_d        = $profile_data['portfolio']     ?? [];
$sertif_d       = $profile_data['sertifikasi']   ?? [];

// Hitung completion score
$cv_checks = [
    'foto'        => !empty($foto_profil) && $foto_profil !== 'default.png',
    'identitas'   => !empty($ident_d['nama_lengkap']),
    'sosmed'      => !empty($sosmed_d),
    'pendidikan'  => !empty($edu_d),
    'pengalaman'  => !empty($exp_d),
    'keahlian'    => !empty($skill_umum_d) || !empty($skill_teknis_d),
    'portfolio'   => !empty($porto_d),
];
$cv_pct = (int) round(array_sum($cv_checks) / count($cv_checks) * 100);

// Platform sosmed beserta meta
$platform_list = [
    ['key'=>'linkedin',  'label'=>'LinkedIn',  'icon'=>'fa-brands fa-linkedin',  'warna'=>'#0077b5', 'ph'=>'linkedin.com/in/username'],
    ['key'=>'instagram', 'label'=>'Instagram', 'icon'=>'fa-brands fa-instagram', 'warna'=>'#e1306c', 'ph'=>'instagram.com/username'],
    ['key'=>'github',    'label'=>'GitHub',    'icon'=>'fa-brands fa-github',    'warna'=>'#24292e', 'ph'=>'github.com/username'],
    ['key'=>'tiktok',    'label'=>'TikTok',    'icon'=>'fa-brands fa-tiktok',    'warna'=>'#010101', 'ph'=>'tiktok.com/@username'],
    ['key'=>'youtube',   'label'=>'YouTube',   'icon'=>'fa-brands fa-youtube',   'warna'=>'#ff0000', 'ph'=>'youtube.com/@channel'],
    ['key'=>'twitter',   'label'=>'X / Twitter','icon'=>'fa-brands fa-x-twitter','warna'=>'#000000', 'ph'=>'x.com/username'],
    ['key'=>'facebook',  'label'=>'Facebook',  'icon'=>'fa-brands fa-facebook',  'warna'=>'#1877f2', 'ph'=>'facebook.com/username'],
    ['key'=>'behance',   'label'=>'Behance',   'icon'=>'fa-brands fa-behance',   'warna'=>'#1769ff', 'ph'=>'behance.net/username'],
    ['key'=>'dribbble',  'label'=>'Dribbble',  'icon'=>'fa-brands fa-dribbble',  'warna'=>'#ea4c89', 'ph'=>'dribbble.com/username'],
    ['key'=>'whatsapp',  'label'=>'WhatsApp',  'icon'=>'fa-brands fa-whatsapp',  'warna'=>'#25d366', 'ph'=>'+62 812 xxxx xxxx'],
    ['key'=>'telegram',  'label'=>'Telegram',  'icon'=>'fa-brands fa-telegram',  'warna'=>'#229ed9', 'ph'=>'t.me/username'],
    ['key'=>'discord',   'label'=>'Discord',   'icon'=>'fa-brands fa-discord',   'warna'=>'#5865f2', 'ph'=>'Username#0000'],
    ['key'=>'website',   'label'=>'Website',   'icon'=>'fa-solid fa-globe',      'warna'=>'#6b7280', 'ph'=>'https://website.com'],
    ['key'=>'lainnya',   'label'=>'Lainnya',   'icon'=>'fa-solid fa-link',       'warna'=>'#9ca3af', 'ph'=>'https://...'],
];
$platform_map = array_column($platform_list, null, 'key');

// Tab definitions
$tabs = [
    ['id'=>'identitas',  'icon'=>'fa-user',            'label'=>'Identitas',    'done'=>$cv_checks['identitas']],
    ['id'=>'sosmed',     'icon'=>'fa-share-nodes',     'label'=>'Media Sosial', 'done'=>$cv_checks['sosmed']],
    ['id'=>'pendidikan', 'icon'=>'fa-graduation-cap',  'label'=>'Pendidikan',   'done'=>$cv_checks['pendidikan']],
    ['id'=>'pengalaman', 'icon'=>'fa-briefcase',       'label'=>'Pengalaman',   'done'=>$cv_checks['pengalaman']],
    ['id'=>'keahlian',   'icon'=>'fa-bolt',            'label'=>'Keahlian',     'done'=>$cv_checks['keahlian']],
    ['id'=>'portfolio',  'icon'=>'fa-diagram-project', 'label'=>'Portfolio',    'done'=>$cv_checks['portfolio']],
];
?>

<!-- ── PAGE HEADER ─────────────────────────────────────────── -->
<div class="page-header">
    <div class="page-header-left">
        <div class="page-eyebrow">CV &amp; Portfolio</div>
        <h1 class="page-title">Profile Builder</h1>
        <div style="margin-top:6px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <?php if (!empty($ident_d['nama_sebutan'])) { ?>
                <span style="font-size:.82rem;color:var(--text-muted);">
                    Dipanggil: <strong style="color:var(--text-main);"><?= h($ident_d['nama_sebutan']) ?></strong>
                </span>
            <?php } ?>
            <?php if (!empty($ident_d['profesi'])) { ?>
                <span class="profesi-badge"><?= h($ident_d['profesi']) ?></span>
            <?php } ?>
        </div>
    </div>
    <div class="page-actions">
        <a href="<?= h($portfolio_url) ?>" target="_blank" class="btn-ghost">
            <i class="fa-solid fa-globe"></i> Lihat Portfolio
        </a>
    </div>
</div>

<!-- ── CV COMPLETION BAR ───────────────────────────────────── -->
<div class="cv-completion-bar-wrap">
    <div class="cv-completion-inner">
        <div class="cv-completion-info">
            <span class="cv-completion-label">Kelengkapan Profil</span>
            <span class="cv-completion-pct" id="cvPct"><?= $cv_pct ?>%</span>
        </div>
        <div class="cv-completion-track">
            <div class="cv-completion-fill" style="width:<?= $cv_pct ?>%"></div>
        </div>
        <div class="cv-completion-checks">
            <?php foreach ($cv_checks as $key => $done) {
                $labels = ['foto'=>'Foto','identitas'=>'Identitas','sosmed'=>'Sosial','pendidikan'=>'Pendidikan','pengalaman'=>'Pengalaman','keahlian'=>'Keahlian','portfolio'=>'Portfolio'];
            ?>
            <span class="cv-check-item <?= $done ? 'done' : '' ?>">
                <i class="fa-solid <?= $done ? 'fa-circle-check' : 'fa-circle' ?>"></i>
                <?= $labels[$key] ?? $key ?>
            </span>
            <?php } ?>
        </div>
    </div>
</div>

<!-- ── PORTFOLIO LINK ──────────────────────────────────────── -->
<div class="profile-inner">
<div class="portfolio-link-box">
    <i class="fa-solid fa-link" style="color:var(--text-muted);flex-shrink:0;padding:0 16px;"></i>
    <input type="text" value="<?= h($portfolio_url) ?>" id="portfolioLinkInput" readonly>
    <button class="copy-btn" onclick="copyPortfolioLink()"><i class="fa-solid fa-copy"></i> Salin</button>
    <a href="<?= h($portfolio_url) ?>" target="_blank" class="copy-btn">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka
    </a>
</div>

<!-- ── TAB NAVIGATION ─────────────────────────────────────── -->
<div class="tab-nav cv2-tab-nav">
    <?php foreach ($tabs as $t) { ?>
    <button class="tab-btn cv2-tab-btn <?= $active_tab === $t['id'] ? 'active' : '' ?>"
            onclick="switchTab('<?= $t['id'] ?>')">
        <i class="fa-solid <?= $t['icon'] ?>"></i>
        <?= $t['label'] ?>
        <?php if ($t['done']) { ?><span class="tab-done-dot"></span><?php } ?>
    </button>
    <?php } ?>
</div>

<!-- ══════════════════════════════════════════════════════════
     TAB 1: IDENTITAS
     ══════════════════════════════════════════════════════════ -->
<div id="tab-identitas" class="tab-panel cv2-panel <?= $active_tab === 'identitas' ? 'active' : '' ?>">
    <form method="POST" id="form-identitas">
        <input type="hidden" name="action"       value="save_profile_data">
        <input type="hidden" name="csrf_token"   value="<?= h($csrf_token) ?>">
        <input type="hidden" name="profile_tab"  value="identitas">

        <!-- Foto Profil inline -->
        <div class="cv2-foto-section">
            <div class="cv2-foto-wrap" onclick="document.getElementById('fotoUploadCv').click()" title="Klik untuk ganti foto">
                <img src="<?= h($path_foto) ?>" alt="Foto" class="cv2-foto-img" id="cvFotoPreview">
                <div class="cv2-foto-overlay"><i class="fa-solid fa-camera"></i></div>
            </div>
            <div class="cv2-foto-info">
                <strong>Foto Profil</strong>
                <span>JPG, PNG, WebP · Maks 3MB</span>
                <button type="button" class="cv2-btn-foto" onclick="document.getElementById('fotoUploadCv').click()">
                    <i class="fa-solid fa-upload"></i> Ganti Foto
                </button>
            </div>
            <input type="file" id="fotoUploadCv" name="foto_profil" accept="image/*" style="display:none;"
                   onchange="cvPreviewFoto(this)">
        </div>

        <!-- Identitas Utama -->
        <div class="cv2-section-title"><i class="fa-solid fa-id-card"></i> Identitas Utama</div>
        <div class="cv2-grid">
            <div class="cv2-field">
                <label>Nama Panggilan <span class="cv2-hint-inline">Tampil di dashboard</span></label>
                <input type="text" name="pd_nama_sebutan"
                       value="<?= h($ident_d['nama_sebutan'] ?? '') ?>"
                       placeholder='cth: "Andi", "Bu Sari", "Pak Wahyu"'>
            </div>
            <div class="cv2-field">
                <label>Nama Lengkap (Resmi)</label>
                <input type="text" name="pd_nama"
                       value="<?= h($ident_d['nama_lengkap'] ?? '') ?>"
                       placeholder="Nama formal untuk portfolio publik">
            </div>
            <div class="cv2-field">
                <label>Profesi / Pekerjaan</label>
                <input type="text" name="pd_profesi"
                       list="cv2-profesi-list"
                       value="<?= h($ident_d['profesi'] ?? '') ?>"
                       placeholder="cth: Guru SD, Fotografer, Web Developer">
                <datalist id="cv2-profesi-list">
                    <option value="Guru SD / SMP / SMA">
                    <option value="Dosen / Pengajar">
                    <option value="Fotografer">
                    <option value="Videografer">
                    <option value="Desainer Grafis">
                    <option value="Konten Kreator">
                    <option value="Wirausaha / Pengusaha">
                    <option value="Karyawan Swasta">
                    <option value="ASN / PNS">
                    <option value="Dokter / Tenaga Medis">
                    <option value="Akuntan / Keuangan">
                    <option value="Marketing / Sales">
                    <option value="Web Developer">
                    <option value="Mobile Developer">
                    <option value="UI/UX Designer">
                    <option value="Data Analyst">
                    <option value="Mahasiswa">
                    <option value="Pelajar SMK / SMA">
                    <option value="Freelancer">
                    <option value="Konsultan">
                </datalist>
            </div>
            <div class="cv2-field">
                <label>Tagline / Motto Singkat</label>
                <input type="text" name="pd_tagline"
                       value="<?= h($ident_d['tagline'] ?? '') ?>"
                       placeholder="cth: Berkomitmen memberikan yang terbaik">
            </div>
        </div>

        <!-- Kontak -->
        <div class="cv2-section-title"><i class="fa-solid fa-phone"></i> Kontak &amp; Lokasi</div>
        <div class="cv2-grid">
            <div class="cv2-field">
                <label>Email</label>
                <input type="email" name="pd_email"
                       value="<?= h($ident_d['email'] ?? '') ?>"
                       placeholder="email@contoh.com">
            </div>
            <div class="cv2-field">
                <label>No. Telepon / WhatsApp</label>
                <input type="text" name="pd_phone"
                       value="<?= h($ident_d['phone'] ?? '') ?>"
                       placeholder="+62 812 xxxx xxxx">
            </div>
            <div class="cv2-field">
                <label>Kota / Lokasi</label>
                <input type="text" name="pd_location"
                       value="<?= h($ident_d['location'] ?? '') ?>"
                       placeholder="Bandung, Jawa Barat">
            </div>
            <div class="cv2-field">
                <label>Website Pribadi <span class="cv2-hint-inline">opsional</span></label>
                <input type="url" name="pd_website"
                       value="<?= h($ident_d['website'] ?? '') ?>"
                       placeholder="https://namaanda.com">
            </div>
        </div>

        <!-- Bio -->
        <div class="cv2-section-title"><i class="fa-solid fa-align-left"></i> Bio / Tentang Saya</div>
        <div class="cv2-field cv2-full">
            <label>Deskripsi Diri <span class="cv2-hint-inline">Tampil di halaman portfolio publik</span></label>
            <textarea name="pd_summary" rows="5"
                      placeholder="Ceritakan tentang dirimu, pengalaman, dan apa yang kamu kerjakan. Tulis dengan bahasa yang natural — ini bukan formal CV kertas."><?= h($ident_d['summary'] ?? '') ?></textarea>
        </div>

        <!-- Visibility toggle -->
        <div class="cv2-visibility-toggle">
            <label class="cv2-toggle-wrap">
                <input type="checkbox" name="pd_tampil_publik" id="pd_tampil_publik" value="1"
                       <?= !empty($ident_d['tampil_publik']) ? 'checked' : '' ?>>
                <span class="cv2-toggle-switch"></span>
            </label>
            <div class="cv2-toggle-info">
                <strong>Tampil di Direktori Publik</strong>
                <span>Profilmu akan muncul di halaman utama website untuk dilihat semua orang</span>
            </div>
        </div>

        <div class="cv2-save-row">
            <button type="button" onclick="submitProfileForm('form-identitas','Identitas')" class="cv2-btn-save">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Identitas
            </button>
        </div>
    </form>
</div>

<!-- ══════════════════════════════════════════════════════════
     TAB 2: MEDIA SOSIAL
     ══════════════════════════════════════════════════════════ -->
<div id="tab-sosmed" class="tab-panel cv2-panel <?= $active_tab === 'sosmed' ? 'active' : '' ?>">
    <form method="POST" id="form-sosmed">
        <input type="hidden" name="action"      value="save_profile_data">
        <input type="hidden" name="csrf_token"  value="<?= h($csrf_token) ?>">
        <input type="hidden" name="profile_tab" value="sosmed">

        <p class="cv2-desc">Tambahkan akun media sosialmu. Akan tampil sebagai tombol di halaman portfolio.</p>

        <!-- Platform picker -->
        <div class="cv2-platform-picker">
            <div class="cv2-section-title"><i class="fa-solid fa-plus-circle"></i> Tambah Platform</div>
            <div class="cv2-platform-grid" id="cvPlatformGrid">
                <?php foreach ($platform_list as $pl) {
                    $already = array_filter($sosmed_d, fn($s) => ($s['platform'] ?? '') === $pl['key']);
                    $active_cls = !empty($already) ? 'cv2-plat-btn-added' : '';
                ?>
                <button type="button"
                        class="cv2-plat-btn <?= $active_cls ?>"
                        data-key="<?= $pl['key'] ?>"
                        data-label="<?= h($pl['label']) ?>"
                        data-icon="<?= h($pl['icon']) ?>"
                        data-warna="<?= h($pl['warna']) ?>"
                        data-ph="<?= h($pl['ph']) ?>"
                        onclick="cvAddSosmed(this)">
                    <i class="<?= $pl['icon'] ?>" style="color:<?= $pl['warna'] ?>;font-size:1.2rem;"></i>
                    <span><?= h($pl['label']) ?></span>
                    <?php if (!empty($already)) { ?><i class="fa-solid fa-check cv2-plat-check"></i><?php } ?>
                </button>
                <?php } ?>
            </div>
        </div>

        <!-- List sosmed yang sudah ditambahkan -->
        <div class="cv2-section-title" style="margin-top:24px;">
            <i class="fa-solid fa-list"></i> Sosial Media Tersimpan
        </div>
        <div class="cv2-sosmed-list" id="cvSosmedList">
            <?php if (empty($sosmed_d)) { ?>
            <div class="cv2-empty-hint" id="cvSosmedEmpty">
                <i class="fa-regular fa-face-smile"></i>
                Belum ada. Klik platform di atas untuk menambahkan.
            </div>
            <?php } else {
                foreach ($sosmed_d as $i => $sm) {
                    $pl_meta = $platform_map[$sm['platform'] ?? ''] ?? $platform_map['lainnya'];
                    $ic = $sm['icon'] ?? $pl_meta['icon'];
                    $cl = $sm['warna'] ?? $pl_meta['warna'];
            ?>
            <div class="cv2-sosmed-item" data-idx="<?= $i ?>">
                <div class="cv2-sosmed-icon" style="background:<?= h($cl) ?>20;color:<?= h($cl) ?>">
                    <i class="<?= h($ic) ?>"></i>
                </div>
                <div class="cv2-sosmed-fields">
                    <input type="hidden" name="sosmed_platform[]" value="<?= h($sm['platform'] ?? '') ?>">
                    <input type="hidden" name="sosmed_icon[]"     value="<?= h($ic) ?>">
                    <input type="hidden" name="sosmed_warna[]"    value="<?= h($cl) ?>">
                    <span class="cv2-sosmed-label"><?= h($sm['label'] ?? $pl_meta['label']) ?></span>
                    <input type="text" name="sosmed_url[]"
                           value="<?= h($sm['url'] ?? '') ?>"
                           placeholder="<?= h($pl_meta['ph']) ?>"
                           class="cv2-sosmed-url-input">
                </div>
                <button type="button" class="cv2-sosmed-remove" onclick="this.closest('.cv2-sosmed-item').remove();cvCheckSosmedEmpty()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <?php } } ?>
        </div>

        <div class="cv2-save-row">
            <button type="button" onclick="submitProfileForm('form-sosmed','Media Sosial')" class="cv2-btn-save">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Media Sosial
            </button>
        </div>
    </form>
</div>

<!-- ══════════════════════════════════════════════════════════
     TAB 3: PENDIDIKAN
     ══════════════════════════════════════════════════════════ -->
<div id="tab-pendidikan" class="tab-panel cv2-panel <?= $active_tab === 'pendidikan' ? 'active' : '' ?>">
    <form method="POST" id="form-pendidikan">
        <input type="hidden" name="action"      value="save_profile_data">
        <input type="hidden" name="csrf_token"  value="<?= h($csrf_token) ?>">
        <input type="hidden" name="profile_tab" value="pendidikan">

        <p class="cv2-desc">Isi riwayat pendidikanmu dari yang terbaru. Bisa SD, SMP, SMA, SMK, kuliah, atau kursus.</p>

        <div class="dyn-list" id="edu-list">
            <?php foreach ($edu_d as $i => $e) {
                $pt = !empty($e['institusi']) ? $e['institusi'] : "Pendidikan " . ($i + 1);
                $ps = $e['gelar'] ?? '';
            ?>
            <div class="dyn-item">
                <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
                    <h4>
                        <i class="fa-solid fa-graduation-cap"></i>
                        <?= h($pt) ?>
                        <?php if ($ps) { ?><span class="dyn-preview"> &mdash; <?= h($ps) ?></span><?php } ?>
                    </h4>
                    <div class="dyn-item-header-btns">
                        <button type="button" class="btn-remove-dyn"
                                onclick="event.stopPropagation();this.closest('.dyn-item').remove()">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                        <i class="fa-solid fa-chevron-down dyn-chevron"></i>
                    </div>
                </div>
                <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
                    <?php foreach ([
                        ['edu_institusi[]', 'Nama Sekolah / Universitas / Kursus', $e['institusi'] ?? ''],
                        ['edu_gelar[]',     'Gelar / Jenjang (SD/SMP/SMA/S1/dll)', $e['gelar'] ?? ''],
                        ['edu_bidang[]',    'Jurusan / Bidang Studi', $e['bidang'] ?? ''],
                        ['edu_mulai[]',     'Tahun Mulai', $e['tahun_mulai'] ?? ''],
                        ['edu_selesai[]',   'Tahun Selesai (atau: Sekarang)', $e['tahun_selesai'] ?? ''],
                    ] as [$fn, $fl, $fv]) { ?>
                    <div class="dyn-field">
                        <label><?= $fl ?></label>
                        <input type="text" name="<?= $fn ?>" value="<?= h($fv) ?>" placeholder="<?= $fl ?>">
                    </div>
                    <?php } ?>
                    <div class="dyn-field full-width">
                        <label>Catatan / Prestasi (opsional)</label>
                        <textarea name="edu_desc[]" rows="2"
                                  placeholder="cth: Lulus dengan predikat cumlaude, aktif di OSIS, dll."><?= h($e['deskripsi'] ?? '') ?></textarea>
                    </div>
                </div></div></div>
            </div>
            <?php } ?>
        </div>

        <button type="button" class="btn-add-dyn" onclick="addEduItem()">
            <i class="fa-solid fa-plus"></i> Tambah Riwayat Pendidikan
        </button>
        <div class="cv2-save-row">
            <button type="button" onclick="submitProfileForm('form-pendidikan','Pendidikan')" class="cv2-btn-save">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Pendidikan
            </button>
        </div>
    </form>
</div>

<!-- ══════════════════════════════════════════════════════════
     TAB 4: PENGALAMAN
     ══════════════════════════════════════════════════════════ -->
<div id="tab-pengalaman" class="tab-panel cv2-panel <?= $active_tab === 'pengalaman' ? 'active' : '' ?>">
    <form method="POST" id="form-pengalaman">
        <input type="hidden" name="action"      value="save_profile_data">
        <input type="hidden" name="csrf_token"  value="<?= h($csrf_token) ?>">
        <input type="hidden" name="profile_tab" value="pengalaman">

        <p class="cv2-desc">Pengalaman kerja, magang, organisasi, relawan — apapun yang pernah kamu ikuti.</p>

        <div class="dyn-list" id="exp-list">
            <?php foreach ($exp_d as $i => $e) {
                $pt = !empty($e['jabatan']) ? $e['jabatan'] : "Pengalaman " . ($i + 1);
                $ps = $e['perusahaan'] ?? '';
            ?>
            <div class="dyn-item">
                <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
                    <h4>
                        <i class="fa-solid fa-briefcase"></i>
                        <?= h($pt) ?>
                        <?php if ($ps) { ?><span class="dyn-preview"> @ <?= h($ps) ?></span><?php } ?>
                    </h4>
                    <div class="dyn-item-header-btns">
                        <button type="button" class="btn-remove-dyn"
                                onclick="event.stopPropagation();this.closest('.dyn-item').remove()">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                        <i class="fa-solid fa-chevron-down dyn-chevron"></i>
                    </div>
                </div>
                <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
                    <div class="dyn-field">
                        <label>Jabatan / Posisi</label>
                        <input type="text" name="exp_jabatan[]"
                               value="<?= h($e['jabatan'] ?? '') ?>"
                               placeholder="cth: Guru Kelas, Staff Marketing, Fotografer Freelance">
                    </div>
                    <div class="dyn-field">
                        <label>Instansi / Perusahaan / Organisasi</label>
                        <input type="text" name="exp_perusahaan[]"
                               value="<?= h($e['perusahaan'] ?? '') ?>"
                               placeholder="cth: SD Negeri 1 Jakarta, PT Maju Bersama">
                    </div>
                    <div class="dyn-field">
                        <label>Periode</label>
                        <input type="text" name="exp_periode[]"
                               value="<?= h($e['periode'] ?? '') ?>"
                               placeholder="cth: 2020 — 2023 atau Jan 2022 — Sekarang">
                    </div>
                    <div class="dyn-field">
                        <label>Jenis Pekerjaan <span style="color:#9ca3af;font-size:.8em;">(opsional)</span></label>
                        <select name="exp_tipe[]">
                            <?php $tipe_now = $e['tipe'] ?? ''; ?>
                            <?php foreach ([''=>'Pilih tipe...','fulltime'=>'Full-time','parttime'=>'Part-time','freelance'=>'Freelance','magang'=>'Magang','relawan'=>'Relawan','organisasi'=>'Organisasi','lainnya'=>'Lainnya'] as $v => $l) { ?>
                            <option value="<?= $v ?>" <?= $tipe_now === $v ? 'selected' : '' ?>><?= $l ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="dyn-field full-width">
                        <label>Deskripsi Singkat</label>
                        <textarea name="exp_desc[]" rows="3"
                                  placeholder="Apa yang kamu kerjakan? Apa pencapaianmu?"><?= h($e['deskripsi'] ?? '') ?></textarea>
                    </div>
                </div></div></div>
            </div>
            <?php } ?>
        </div>

        <button type="button" class="btn-add-dyn" onclick="addExpItem()">
            <i class="fa-solid fa-plus"></i> Tambah Pengalaman
        </button>
        <div class="cv2-save-row">
            <button type="button" onclick="submitProfileForm('form-pengalaman','Pengalaman')" class="cv2-btn-save">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Pengalaman
            </button>
        </div>
    </form>
</div>

<!-- ══════════════════════════════════════════════════════════
     TAB 5: KEAHLIAN & BAHASA
     ══════════════════════════════════════════════════════════ -->
<div id="tab-keahlian" class="tab-panel cv2-panel <?= $active_tab === 'keahlian' ? 'active' : '' ?>">
    <form method="POST" id="form-keahlian">
        <input type="hidden" name="action"      value="save_profile_data">
        <input type="hidden" name="csrf_token"  value="<?= h($csrf_token) ?>">
        <input type="hidden" name="profile_tab" value="keahlian">

        <!-- Skill Autocomplete Search -->
        <div class="cv2-skill-search-section">
            <div class="cv2-section-title"><i class="fa-solid fa-magnifying-glass"></i> Cari &amp; Tambah Keahlian</div>
            <p class="cv2-desc">Ketik nama skill atau software yang kamu kuasai. Sistem akan menyarankan pilihan yang tepat.</p>
            <div class="cv2-skill-autocomplete-wrap">
                <i class="fa-solid fa-magnifying-glass cv2-skill-search-icon"></i>
                <input type="text" id="cvSkillSearchInput" class="cv2-skill-search-input"
                       placeholder="Cari: Excel, Photoshop, Laravel, Figma, Mengajar..."
                       oninput="cvSkillSearch(this.value)"
                       autocomplete="off">
                <div id="cvSkillDropdown" class="cv2-skill-dropdown" style="display:none;"></div>
            </div>
        </div>

        <!-- Skill Umum (non-IT) -->
        <div class="cv2-section-title" style="margin-top:20px;">
            <i class="fa-solid fa-star"></i> Keahlian Umum
            <span class="cv2-section-count" id="skillUmumCount"><?= count($skill_umum_d) ?></span>
        </div>
        <p class="cv2-desc-sm">Software, tools, dan kemampuan profesional non-teknis</p>
        <div class="dyn-list" id="skill-umum-list">
            <?php foreach ($skill_umum_d as $i => $sk) { ?>
            <div class="dyn-item cv2-skill-item">
                <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
                    <h4>
                        <span class="cv2-skill-emoji"><?= $sk['icon'] ?? '⭐' ?></span>
                        <?= h($sk['nama'] ?? '') ?>
                        <span class="dyn-preview cv2-skill-cat-badge">
                            <?= h($sk['kategori'] ?? '') ?>
                        </span>
                    </h4>
                    <div class="dyn-item-header-btns">
                        <span class="cv2-skill-level-badge"><?= (int)($sk['level'] ?? 70) ?>%</span>
                        <button type="button" class="btn-remove-dyn"
                                onclick="event.stopPropagation();this.closest('.dyn-item').remove();cvUpdateSkillCount('umum')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        <i class="fa-solid fa-chevron-down dyn-chevron"></i>
                    </div>
                </div>
                <div class="dyn-body"><div class="dyn-body-inner">
                    <input type="hidden" name="skill_umum_nama[]"     value="<?= h($sk['nama'] ?? '') ?>">
                    <input type="hidden" name="skill_umum_kategori[]" value="<?= h($sk['kategori'] ?? '') ?>">
                    <input type="hidden" name="skill_umum_icon[]"     value="<?= h($sk['icon'] ?? '⭐') ?>">
                    <input type="hidden" name="skill_umum_warna[]"    value="<?= h($sk['warna'] ?? '#6b7280') ?>">
                    <div class="cv2-skill-level-wrap">
                        <label>
                            Tingkat Kemampuan:
                            <strong id="slu_<?= $i ?>"><?= (int)($sk['level'] ?? 70) ?>%</strong>
                        </label>
                        <div class="cv2-level-slider-row">
                            <span class="cv2-level-label-sm">Pemula</span>
                            <input type="range" name="skill_umum_level[]"
                                   min="10" max="100" step="5"
                                   value="<?= (int)($sk['level'] ?? 70) ?>"
                                   oninput="document.getElementById('slu_<?= $i ?>').textContent=this.value+'%';this.style.background=cvSliderBg(this.value)">
                            <span class="cv2-level-label-sm">Ahli</span>
                        </div>
                    </div>
                </div></div>
            </div>
            <?php } ?>
        </div>

        <!-- Skill Teknis (IT) -->
        <div class="cv2-section-title" style="margin-top:28px;">
            <i class="fa-solid fa-code"></i> Keahlian Teknis (IT)
            <span class="cv2-section-count" id="skillTeknisCount"><?= count($skill_teknis_d) ?></span>
        </div>
        <p class="cv2-desc-sm">Bahasa pemrograman, framework, tools IT. Kosongkan jika tidak relevan.</p>
        <div class="dyn-list" id="skill-teknis-list">
            <?php foreach ($skill_teknis_d as $i => $sk) { ?>
            <div class="dyn-item cv2-skill-item">
                <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
                    <h4>
                        <span class="cv2-skill-emoji"><?= $sk['icon'] ?? '💻' ?></span>
                        <?= h($sk['nama'] ?? '') ?>
                        <span class="dyn-preview cv2-skill-cat-badge cv2-skill-it-badge">
                            <?= h($sk['kategori'] ?? '') ?>
                        </span>
                    </h4>
                    <div class="dyn-item-header-btns">
                        <span class="cv2-skill-level-badge"><?= (int)($sk['level'] ?? 70) ?>%</span>
                        <button type="button" class="btn-remove-dyn"
                                onclick="event.stopPropagation();this.closest('.dyn-item').remove();cvUpdateSkillCount('teknis')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        <i class="fa-solid fa-chevron-down dyn-chevron"></i>
                    </div>
                </div>
                <div class="dyn-body"><div class="dyn-body-inner">
                    <input type="hidden" name="skill_teknis_nama[]"     value="<?= h($sk['nama'] ?? '') ?>">
                    <input type="hidden" name="skill_teknis_kategori[]" value="<?= h($sk['kategori'] ?? '') ?>">
                    <input type="hidden" name="skill_teknis_icon[]"     value="<?= h($sk['icon'] ?? '💻') ?>">
                    <input type="hidden" name="skill_teknis_warna[]"    value="<?= h($sk['warna'] ?? '#6b7280') ?>">
                    <div class="cv2-skill-level-wrap">
                        <label>
                            Tingkat Kemampuan:
                            <strong id="slt_<?= $i ?>"><?= (int)($sk['level'] ?? 70) ?>%</strong>
                        </label>
                        <div class="cv2-level-slider-row">
                            <span class="cv2-level-label-sm">Pemula</span>
                            <input type="range" name="skill_teknis_level[]"
                                   min="10" max="100" step="5"
                                   value="<?= (int)($sk['level'] ?? 70) ?>"
                                   oninput="document.getElementById('slt_<?= $i ?>').textContent=this.value+'%';this.style.background=cvSliderBg(this.value)">
                            <span class="cv2-level-label-sm">Ahli</span>
                        </div>
                    </div>
                </div></div>
            </div>
            <?php } ?>
        </div>

        <!-- Bahasa yang dikuasai -->
        <div class="cv2-section-title" style="margin-top:28px;">
            <i class="fa-solid fa-language"></i> Bahasa yang Dikuasai
        </div>
        <div class="cv2-bahasa-grid" id="cvBahasaGrid">
            <?php
            $default_langs = ['Bahasa Indonesia', 'Bahasa Inggris', 'Bahasa Arab', 'Bahasa Jepang', 'Bahasa Mandarin', 'Bahasa Jawa', 'Bahasa Sunda', 'Bahasa Belanda', 'Lainnya'];
            $saved_langs = array_column($bahasa_d, null, 'nama');
            foreach ($default_langs as $lang) {
                $saved = $saved_langs[$lang] ?? null;
                $checked = !empty($saved);
                $level = $saved['level'] ?? '';
            ?>
            <div class="cv2-bahasa-item <?= $checked ? 'checked' : '' ?>" onclick="cvToggleBahasa(this)">
                <input type="checkbox" name="bahasa_nama[]" value="<?= h($lang) ?>"
                       <?= $checked ? 'checked' : '' ?> style="display:none;">
                <div class="cv2-bahasa-check"><i class="fa-solid fa-check"></i></div>
                <span class="cv2-bahasa-name"><?= h($lang) ?></span>
                <select name="bahasa_level[]" class="cv2-bahasa-level" onclick="event.stopPropagation()"
                        <?= !$checked ? 'disabled' : '' ?>>
                    <option value="">Pilih level</option>
                    <option value="native"       <?= $level==='native'?'selected':'' ?>>Bahasa Ibu</option>
                    <option value="fluent"       <?= $level==='fluent'?'selected':'' ?>>Lancar</option>
                    <option value="intermediate" <?= $level==='intermediate'?'selected':'' ?>>Menengah</option>
                    <option value="beginner"     <?= $level==='beginner'?'selected':'' ?>>Dasar</option>
                </select>
            </div>
            <?php } ?>
        </div>

        <div class="cv2-save-row">
            <button type="button" onclick="submitProfileForm('form-keahlian','Keahlian')" class="cv2-btn-save">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Keahlian &amp; Bahasa
            </button>
        </div>
    </form>
</div>

<!-- ══════════════════════════════════════════════════════════
     TAB 6: PORTFOLIO & SERTIFIKASI
     ══════════════════════════════════════════════════════════ -->
<div id="tab-portfolio" class="tab-panel cv2-panel <?= $active_tab === 'portfolio' ? 'active' : '' ?>">
    <form method="POST" id="form-portfolio">
        <input type="hidden" name="action"      value="save_profile_data">
        <input type="hidden" name="csrf_token"  value="<?= h($csrf_token) ?>">
        <input type="hidden" name="profile_tab" value="portfolio">

        <!-- Portfolio Proyek -->
        <div class="cv2-section-title"><i class="fa-solid fa-diagram-project"></i> Proyek / Karya</div>
        <p class="cv2-desc">Apapun yang pernah kamu buat: website, foto, kerajinan, event, penelitian, dll.</p>

        <div class="dyn-list" id="porto-list">
            <?php foreach ($porto_d as $i => $p) {
                $pp = !empty($p['nama']) ? $p['nama'] : "Proyek " . ($i + 1);
            ?>
            <div class="dyn-item">
                <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
                    <h4>
                        <i class="fa-solid fa-folder-open"></i> <?= h($pp) ?>
                        <?php if (!empty($p['kategori'])) { ?>
                        <span class="dyn-preview"> · <?= h($p['kategori']) ?></span>
                        <?php } ?>
                    </h4>
                    <div class="dyn-item-header-btns">
                        <button type="button" class="btn-remove-dyn"
                                onclick="event.stopPropagation();this.closest('.dyn-item').remove()">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                        <i class="fa-solid fa-chevron-down dyn-chevron"></i>
                    </div>
                </div>
                <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
                    <div class="dyn-field">
                        <label>Nama Proyek / Karya</label>
                        <input type="text" name="porto_nama[]"
                               value="<?= h($p['nama'] ?? '') ?>"
                               placeholder="cth: Website Sekolah, Album Foto Wedding, Laporan PKL">
                    </div>
                    <div class="dyn-field">
                        <label>Kategori <span style="color:#9ca3af;font-size:.8em;">(opsional)</span></label>
                        <input type="text" name="porto_kategori[]"
                               list="porto-kategori-list"
                               value="<?= h($p['kategori'] ?? '') ?>"
                               placeholder="cth: Web, Fotografi, Event">
                        <datalist id="porto-kategori-list">
                            <option value="Web Development"><option value="Mobile App">
                            <option value="Fotografi"><option value="Videografi">
                            <option value="Desain Grafis"><option value="Penelitian">
                            <option value="Event Organizer"><option value="Wirausaha">
                            <option value="Pendidikan"><option value="Lainnya">
                        </datalist>
                    </div>
                    <div class="dyn-field">
                        <label>Link / URL <span style="color:#9ca3af;font-size:.8em;">(opsional)</span></label>
                        <input type="url" name="porto_url[]"
                               value="<?= h($p['url'] ?? '') ?>"
                               placeholder="https://...">
                    </div>
                    <div class="dyn-field">
                        <label>Tools / Teknologi yang Dipakai</label>
                        <input type="text" name="porto_tech[]"
                               value="<?= h($p['tech'] ?? '') ?>"
                               placeholder="cth: Canva, Camera Sony, WordPress, Laravel">
                    </div>
                    <div class="dyn-field full-width">
                        <label>Deskripsi Singkat</label>
                        <textarea name="porto_desc[]" rows="3"
                                  placeholder="Ceritakan proyekmu: apa itu, tujuannya, dan dampaknya."><?= h($p['deskripsi'] ?? '') ?></textarea>
                    </div>
                </div></div></div>
            </div>
            <?php } ?>
        </div>

        <button type="button" class="btn-add-dyn" onclick="addPortoItem()">
            <i class="fa-solid fa-plus"></i> Tambah Proyek / Karya
        </button>

        <!-- Sertifikasi -->
        <div class="cv2-section-title" style="margin-top:36px;">
            <i class="fa-solid fa-certificate"></i> Sertifikasi &amp; Penghargaan
        </div>
        <p class="cv2-desc">Sertifikat kursus, pelatihan, penghargaan, atau lisensi profesional.</p>

        <div class="dyn-list" id="sertif-list">
            <?php foreach ($sertif_d as $i => $s) { ?>
            <div class="dyn-item">
                <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
                    <h4>
                        <i class="fa-solid fa-award"></i>
                        <?= h($s['nama'] ?? "Sertifikasi " . ($i + 1)) ?>
                        <?php if (!empty($s['penerbit'])) { ?>
                        <span class="dyn-preview"> · <?= h($s['penerbit']) ?></span>
                        <?php } ?>
                    </h4>
                    <div class="dyn-item-header-btns">
                        <button type="button" class="btn-remove-dyn"
                                onclick="event.stopPropagation();this.closest('.dyn-item').remove()">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                        <i class="fa-solid fa-chevron-down dyn-chevron"></i>
                    </div>
                </div>
                <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
                    <div class="dyn-field">
                        <label>Nama Sertifikat / Penghargaan</label>
                        <input type="text" name="sertif_nama[]"
                               value="<?= h($s['nama'] ?? '') ?>"
                               placeholder="cth: Google Analytics Certificate, Sertifikat TOEFL">
                    </div>
                    <div class="dyn-field">
                        <label>Penerbit / Lembaga</label>
                        <input type="text" name="sertif_penerbit[]"
                               value="<?= h($s['penerbit'] ?? '') ?>"
                               placeholder="cth: Google, Coursera, Kemendikbud">
                    </div>
                    <div class="dyn-field">
                        <label>Tahun</label>
                        <input type="text" name="sertif_tahun[]"
                               value="<?= h($s['tahun'] ?? '') ?>"
                               placeholder="2023">
                    </div>
                    <div class="dyn-field">
                        <label>Link Verifikasi <span style="color:#9ca3af;font-size:.8em;">(opsional)</span></label>
                        <input type="url" name="sertif_url[]"
                               value="<?= h($s['url'] ?? '') ?>"
                               placeholder="https://...">
                    </div>
                </div></div></div>
            </div>
            <?php } ?>
        </div>

        <button type="button" class="btn-add-dyn" onclick="addSertifItem()">
            <i class="fa-solid fa-plus"></i> Tambah Sertifikasi
        </button>

        <div class="cv2-save-row">
            <button type="button" onclick="submitProfileForm('form-portfolio','Portfolio')" class="cv2-btn-save">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Portfolio &amp; Sertifikasi
            </button>
        </div>
    </form>
</div>

</div><!-- end .profile-inner -->


<!-- ════════════════════════════════════════════════════════════
     CSS — CV Builder v2
     ════════════════════════════════════════════════════════════ -->
<style>
/* ── Completion Bar ──────────────────────────────────────────── */
.cv-completion-bar-wrap {
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    padding: 16px 32px;
}
.cv-completion-inner { max-width: 900px; }
.cv-completion-info {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 8px;
}
.cv-completion-label {
    font-size: .68rem; font-weight: 700;
    letter-spacing: .8px; text-transform: uppercase;
    color: var(--text-muted);
}
.cv-completion-pct {
    font-size: .88rem; font-weight: 800;
    font-family: var(--f-display);
}
.cv-completion-track {
    height: 6px; background: var(--surface-3);
    border-radius: 6px; overflow: hidden;
    margin-bottom: 12px;
}
.cv-completion-fill {
    height: 100%;
    background: linear-gradient(90deg, #0a0a0a, #444);
    border-radius: 6px;
    transition: width .8s var(--ease-out-expo);
}
.cv-completion-checks {
    display: flex; flex-wrap: wrap; gap: 8px;
}
.cv-check-item {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .7rem; font-weight: 600;
    padding: 3px 10px;
    border: 1px solid var(--border-md);
    border-radius: 20px;
    color: var(--text-muted);
    transition: all .2s;
}
.cv-check-item.done {
    background: var(--success-bg);
    border-color: #86efac;
    color: var(--success);
}
.cv-check-item.done i { color: var(--success); }

/* ── Tab nav v2 ──────────────────────────────────────────────── */
.cv2-tab-nav {
    overflow-x: auto;
    display: flex;
    gap: 0;
    border-bottom: 2px solid var(--border);
    margin-bottom: 0;
    padding: 0 8px;
    scrollbar-width: none;
    background: var(--surface);
}
.cv2-tab-nav::-webkit-scrollbar { display: none; }
.cv2-tab-btn {
    padding: 14px 20px;
    font-size: .78rem; font-weight: 700;
    letter-spacing: .2px;
    display: flex; align-items: center; gap: 7px;
    position: relative; white-space: nowrap;
    border: none; background: transparent;
    color: var(--text-muted); cursor: pointer;
    font-family: var(--f-body);
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    transition: color .2s, border-color .2s;
}
.cv2-tab-btn:hover { color: var(--text-main); }
.cv2-tab-btn.active {
    color: var(--text-main);
    border-bottom-color: var(--ink);
    font-weight: 700;
}
.tab-done-dot {
    width: 7px; height: 7px;
    background: var(--success);
    border-radius: 50%;
    display: inline-block;
    margin-left: 2px;
}

/* ── Panel ────────────────────────────────────────────────────── */
.cv2-panel { padding: 28px 32px; animation: fadeUp .3s var(--ease-out-expo); }
.cv2-section-title {
    font-size: .65rem; font-weight: 800;
    letter-spacing: 1.5px; text-transform: uppercase;
    color: var(--text-muted);
    display: flex; align-items: center; gap: 8px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border);
    margin: 24px 0 16px;
}
.cv2-section-title:first-child { margin-top: 0; }
.cv2-section-count {
    background: var(--surface-3);
    color: var(--text-muted);
    border-radius: 20px;
    padding: 1px 8px;
    font-size: .65rem;
    font-weight: 700;
}
.cv2-desc {
    font-size: .85rem; color: var(--text-muted);
    line-height: 1.6; margin-bottom: 20px;
}
.cv2-desc-sm {
    font-size: .78rem; color: var(--text-muted);
    margin: -8px 0 14px;
}
.cv2-hint-inline {
    font-size: .68rem; color: var(--text-muted);
    font-weight: 400; text-transform: none;
    letter-spacing: 0;
}

/* ── Grid & Fields ───────────────────────────────────────────── */
.cv2-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 18px;
    margin-bottom: 8px;
}
.cv2-field { display: flex; flex-direction: column; gap: 5px; }
.cv2-field.cv2-full { grid-column: 1 / -1; }
.cv2-field label {
    font-size: .65rem; font-weight: 700;
    letter-spacing: .5px; text-transform: uppercase;
    color: var(--text-muted);
    display: flex; align-items: center; gap: 6px;
}
.cv2-field input, .cv2-field textarea, .cv2-field select {
    width: 100%; padding: 10px 12px;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    color: var(--text-main);
    font-family: var(--f-body); font-size: .88rem;
    outline: none;
    transition: border-color .2s, box-shadow .2s, background .2s;
}
.cv2-field input:focus, .cv2-field textarea:focus, .cv2-field select:focus {
    border-color: var(--border-dark);
    background: var(--surface);
    box-shadow: var(--glow-sm);
}
.cv2-field textarea { resize: vertical; min-height: 88px; }

/* ── Foto Profil inline ──────────────────────────────────────── */
.cv2-foto-section {
    display: flex; align-items: center; gap: 20px;
    padding: 20px;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    margin-bottom: 24px;
}
.cv2-foto-wrap {
    width: 80px; height: 80px; border-radius: 50%;
    overflow: hidden; position: relative; cursor: pointer;
    flex-shrink: 0; border: 2.5px solid var(--border-md);
    transition: border-color .2s, transform .2s;
}
.cv2-foto-wrap:hover { border-color: var(--ink); transform: scale(1.04); }
.cv2-foto-img { width: 100%; height: 100%; object-fit: cover; display: block; }
.cv2-foto-overlay {
    position: absolute; inset: 0;
    background: rgba(0,0,0,.4);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity .2s;
    color: #fff; font-size: 1.2rem;
}
.cv2-foto-wrap:hover .cv2-foto-overlay { opacity: 1; }
.cv2-foto-info {
    display: flex; flex-direction: column; gap: 4px;
}
.cv2-foto-info strong { font-size: .9rem; font-weight: 700; }
.cv2-foto-info span { font-size: .75rem; color: var(--text-muted); }
.cv2-btn-foto {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px;
    background: var(--ink); color: #fff; border: none;
    border-radius: var(--radius-sm); font-size: .72rem;
    font-weight: 700; cursor: pointer; font-family: var(--f-body);
    width: fit-content; margin-top: 4px;
    transition: background .2s;
}
.cv2-btn-foto:hover { background: #333; }

/* ── Visibility Toggle ───────────────────────────────────────── */
.cv2-visibility-toggle {
    display: flex; align-items: flex-start; gap: 14px;
    padding: 16px 18px;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    margin: 20px 0 8px; cursor: pointer;
}
.cv2-toggle-wrap { flex-shrink: 0; }
.cv2-toggle-switch {
    width: 44px; height: 24px;
    background: #d1d5db; border-radius: 12px;
    display: block; position: relative;
    transition: background .25s;
    cursor: pointer;
}
.cv2-toggle-switch::after {
    content: '';
    position: absolute; top: 3px; left: 3px;
    width: 18px; height: 18px;
    background: #fff; border-radius: 50%;
    box-shadow: 0 1px 4px rgba(0,0,0,.15);
    transition: left .25s var(--ease-out-expo);
}
input[type=checkbox]:checked + .cv2-toggle-switch { background: var(--success); }
input[type=checkbox]:checked + .cv2-toggle-switch::after { left: 23px; }
.cv2-toggle-info strong { font-size: .85rem; font-weight: 700; display: block; margin-bottom: 2px; }
.cv2-toggle-info span { font-size: .78rem; color: var(--text-muted); }

/* ── Save button ─────────────────────────────────────────────── */
.cv2-save-row { margin-top: 24px; }
.cv2-btn-save {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 28px;
    background: var(--ink); color: #fff; border: none;
    border-radius: var(--radius-sm);
    font-size: .78rem; font-weight: 700;
    letter-spacing: .3px; text-transform: uppercase;
    cursor: pointer; font-family: var(--f-body);
    transition: all .2s; box-shadow: var(--shadow-sm);
}
.cv2-btn-save:hover { background: #222; box-shadow: var(--shadow-md); transform: translateY(-1px); }
.cv2-btn-save:active { transform: scale(.97); }

/* ── Platform Picker (Sosmed) ────────────────────────────────── */
.cv2-platform-grid {
    display: flex; flex-wrap: wrap; gap: 8px;
    margin-top: 12px;
}
.cv2-plat-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 8px 14px;
    border: 1.5px solid var(--border-md);
    border-radius: 50px;
    font-size: .78rem; font-weight: 600;
    cursor: pointer; background: var(--surface);
    color: var(--text-main); font-family: var(--f-body);
    transition: all .2s var(--ease-out-expo);
    white-space: nowrap;
}
.cv2-plat-btn:hover { border-color: var(--ink); background: var(--surface-2); transform: translateY(-1px); }
.cv2-plat-btn-added { border-color: var(--success) !important; background: var(--success-bg) !important; color: var(--success); }
.cv2-plat-check { color: var(--success); font-size: .7rem; }

/* ── Sosmed List ─────────────────────────────────────────────── */
.cv2-sosmed-list { display: flex; flex-direction: column; gap: 10px; margin-top: 12px; }
.cv2-sosmed-item {
    display: flex; align-items: center; gap: 12px;
    padding: 12px 16px;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    animation: fadeUp .25s var(--ease-out-expo);
}
.cv2-sosmed-icon {
    width: 40px; height: 40px; border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.cv2-sosmed-fields { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 4px; }
.cv2-sosmed-label { font-size: .68rem; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; color: var(--text-muted); }
.cv2-sosmed-url-input {
    width: 100%; padding: 8px 10px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: .86rem; font-family: var(--f-body);
    background: var(--surface-2); color: var(--text-main);
    outline: none; transition: border-color .2s;
}
.cv2-sosmed-url-input:focus { border-color: var(--border-dark); }
.cv2-sosmed-remove {
    width: 30px; height: 30px; border-radius: var(--radius-sm);
    background: none; border: none; cursor: pointer;
    color: var(--text-muted); font-size: .85rem;
    display: flex; align-items: center; justify-content: center;
    transition: all .2s; flex-shrink: 0;
}
.cv2-sosmed-remove:hover { background: var(--danger-bg); color: var(--danger); }
.cv2-empty-hint {
    display: flex; align-items: center; gap: 10px;
    padding: 20px; border: 1.5px dashed var(--border-md);
    border-radius: var(--radius-md);
    font-size: .83rem; color: var(--text-muted);
}

/* ── Skill Autocomplete ──────────────────────────────────────── */
.cv2-skill-search-section { margin-bottom: 8px; }
.cv2-skill-autocomplete-wrap {
    position: relative;
}
.cv2-skill-search-icon {
    position: absolute; left: 14px; top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted); font-size: .85rem; pointer-events: none;
}
.cv2-skill-search-input {
    width: 100%; padding: 12px 16px 12px 40px;
    background: var(--surface-2);
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: .9rem; font-family: var(--f-body);
    color: var(--text-main); outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.cv2-skill-search-input:focus {
    border-color: var(--border-dark);
    box-shadow: var(--glow-sm);
    background: var(--surface);
}
.cv2-skill-dropdown {
    position: absolute; top: calc(100% + 6px); left: 0; right: 0;
    background: var(--surface);
    border: 1.5px solid var(--border-md);
    border-radius: var(--radius-md);
    z-index: 200; box-shadow: var(--shadow-xl);
    max-height: 280px; overflow-y: auto;
    animation: scaleIn .15s var(--ease-out-expo);
}
.cv2-skill-dd-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 14px; cursor: pointer;
    border-bottom: 1px solid var(--border);
    transition: background .15s; font-size: .85rem;
}
.cv2-skill-dd-item:last-child { border-bottom: none; }
.cv2-skill-dd-item:hover { background: var(--surface-3); }
.cv2-skill-dd-emoji { font-size: 1.1rem; width: 24px; text-align: center; flex-shrink: 0; }
.cv2-skill-dd-name { font-weight: 600; flex: 1; }
.cv2-skill-dd-cat {
    font-size: .65rem; font-weight: 700; padding: 2px 8px;
    border-radius: 20px; background: var(--surface-3);
    color: var(--text-muted); white-space: nowrap;
}
.cv2-skill-dd-it { background: var(--blue-bg); color: var(--blue); }
.cv2-skill-dd-add {
    width: 28px; height: 28px; border-radius: var(--radius-sm);
    background: var(--ink); color: #fff; border: none;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; font-size: .85rem; flex-shrink: 0;
    transition: background .2s;
}
.cv2-skill-dd-add:hover { background: #333; }

/* ── Skill items ─────────────────────────────────────────────── */
.cv2-skill-emoji { font-size: 1rem; margin-right: 4px; }
.cv2-skill-cat-badge {
    font-size: .62rem; font-weight: 700;
    padding: 2px 8px; border-radius: 20px;
    background: var(--surface-3); color: var(--text-muted);
}
.cv2-skill-it-badge { background: var(--blue-bg); color: var(--blue); }
.cv2-skill-level-badge {
    font-size: .7rem; font-weight: 800;
    padding: 2px 8px; border-radius: 20px;
    background: rgba(255,255,255,.15); color: rgba(255,255,255,.8);
}
.cv2-skill-level-wrap { padding: 0 2px; }
.cv2-skill-level-wrap label {
    font-size: .72rem; color: rgba(255,255,255,.7);
    display: block; margin-bottom: 8px;
}
.cv2-skill-level-wrap label strong { color: #fff; font-size: .85rem; }
.cv2-level-slider-row { display: flex; align-items: center; gap: 10px; }
.cv2-level-label-sm { font-size: .62rem; color: rgba(255,255,255,.5); white-space: nowrap; font-weight: 600; }
.cv2-level-slider-row input[type=range] {
    flex: 1; accent-color: #fff; height: 4px;
    cursor: pointer; border-radius: 4px;
    background: rgba(255,255,255,.2);
}

/* ── Bahasa ──────────────────────────────────────────────────── */
.cv2-bahasa-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px;
    margin-bottom: 8px;
}
.cv2-bahasa-item {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 14px;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    cursor: pointer; transition: all .2s;
}
.cv2-bahasa-item:hover { border-color: var(--border-md); background: var(--surface-2); }
.cv2-bahasa-item.checked { border-color: var(--ink); background: var(--surface-2); }
.cv2-bahasa-check {
    width: 22px; height: 22px;
    border: 1.5px solid var(--border-md); border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: .7rem; color: transparent;
    transition: all .2s; background: var(--surface-3);
}
.cv2-bahasa-item.checked .cv2-bahasa-check {
    background: var(--ink); border-color: var(--ink); color: #fff;
}
.cv2-bahasa-name { font-size: .83rem; font-weight: 600; flex: 1; }
.cv2-bahasa-level {
    padding: 4px 6px; font-size: .7rem;
    border: 1px solid var(--border-md); border-radius: var(--radius-sm);
    background: var(--surface); font-family: var(--f-body);
    max-width: 110px; outline: none;
}
.cv2-bahasa-level:disabled { opacity: .3; pointer-events: none; }

/* ── Mobile ──────────────────────────────────────────────────── */
@media (max-width: 768px) {
    .cv2-panel { padding: 16px 20px; }
    .cv2-grid { grid-template-columns: 1fr; }
    .cv2-tab-btn { padding: 12px 14px; font-size: .73rem; }
    .cv2-foto-section { flex-direction: column; align-items: flex-start; gap: 12px; }
    .cv-completion-bar-wrap { padding: 14px 20px; }
    .cv2-bahasa-grid { grid-template-columns: 1fr; }
    .cv2-skill-search-input { font-size: 16px !important; }
    .cv2-field input, .cv2-field textarea, .cv2-field select { font-size: 16px !important; }
}
</style>


<!-- ════════════════════════════════════════════════════════════
     JAVASCRIPT — CV Builder v2
     ════════════════════════════════════════════════════════════ -->
<script>
/* ─────────────────────────────────────────────────────────────
   SKILL PRESET DATA (inline, sama seperti di onboarding)
   Dapat di-extend dari PHP: cvSkillDB = <?= json_encode(array_merge($skill_umum_d, $skill_teknis_d)) ?> || cvSkillDB;
   ───────────────────────────────────────────────────────────── */
const cvSkillDB = [
    // Microsoft Office
    {n:'Microsoft Word',        k:'Microsoft Office',   t:'umum',   e:'📝', w:'#2563eb'},
    {n:'Microsoft Excel',       k:'Microsoft Office',   t:'umum',   e:'📊', w:'#16a34a'},
    {n:'Microsoft PowerPoint',  k:'Microsoft Office',   t:'umum',   e:'📽️', w:'#ea580c'},
    {n:'Microsoft Outlook',     k:'Microsoft Office',   t:'umum',   e:'📧', w:'#0891b2'},
    {n:'Microsoft Teams',       k:'Microsoft Office',   t:'umum',   e:'💬', w:'#7c3aed'},
    {n:'Microsoft Access',      k:'Microsoft Office',   t:'umum',   e:'🗄️', w:'#dc2626'},
    {n:'Microsoft OneNote',     k:'Microsoft Office',   t:'umum',   e:'📒', w:'#7c3aed'},
    // Google
    {n:'Google Docs',           k:'Google Workspace',   t:'umum',   e:'📄', w:'#1a73e8'},
    {n:'Google Sheets',         k:'Google Workspace',   t:'umum',   e:'📈', w:'#34a853'},
    {n:'Google Slides',         k:'Google Workspace',   t:'umum',   e:'🖥️', w:'#fbbc04'},
    {n:'Google Meet',           k:'Google Workspace',   t:'umum',   e:'📹', w:'#00ac47'},
    {n:'Google Analytics',      k:'Google Workspace',   t:'umum',   e:'📊', w:'#e37400'},
    // Desain
    {n:'Adobe Photoshop',       k:'Desain Grafis',      t:'umum',   e:'🎨', w:'#31a8ff'},
    {n:'Adobe Illustrator',     k:'Desain Grafis',      t:'umum',   e:'✏️', w:'#ff9a00'},
    {n:'Adobe InDesign',        k:'Desain Grafis',      t:'umum',   e:'📰', w:'#ff3366'},
    {n:'Adobe XD',              k:'UI/UX',              t:'teknis', e:'🖌️', w:'#ff61f6'},
    {n:'Canva',                 k:'Desain Grafis',      t:'umum',   e:'🖌️', w:'#00c4cc'},
    {n:'CorelDRAW',             k:'Desain Grafis',      t:'umum',   e:'🖍️', w:'#1db954'},
    {n:'Figma',                 k:'UI/UX',              t:'teknis', e:'🎭', w:'#f24e1e'},
    {n:'Sketch',                k:'UI/UX',              t:'teknis', e:'💎', w:'#f7b500'},
    // Foto & Video
    {n:'Adobe Lightroom',       k:'Foto & Video',       t:'umum',   e:'📸', w:'#31a8ff'},
    {n:'Adobe Premiere Pro',    k:'Foto & Video',       t:'umum',   e:'🎬', w:'#9999ff'},
    {n:'Adobe After Effects',   k:'Foto & Video',       t:'umum',   e:'✨', w:'#9999ff'},
    {n:'DaVinci Resolve',       k:'Foto & Video',       t:'umum',   e:'🎥', w:'#ff6b00'},
    {n:'CapCut',                k:'Foto & Video',       t:'umum',   e:'📲', w:'#010101'},
    {n:'OBS Studio',            k:'Foto & Video',       t:'umum',   e:'📡', w:'#302e31'},
    // Keuangan
    {n:'Accurate Accounting',   k:'Keuangan',           t:'umum',   e:'💰', w:'#16a34a'},
    {n:'Jurnal (Mekari)',        k:'Keuangan',           t:'umum',   e:'📓', w:'#4f46e5'},
    {n:'MYOB',                  k:'Keuangan',           t:'umum',   e:'📒', w:'#16a34a'},
    {n:'Zahir Accounting',      k:'Keuangan',           t:'umum',   e:'💹', w:'#0891b2'},
    // Komunikasi
    {n:'Public Speaking',       k:'Komunikasi',         t:'umum',   e:'🎤', w:'#dc2626'},
    {n:'Copywriting',           k:'Komunikasi',         t:'umum',   e:'✍️', w:'#7c3aed'},
    {n:'Content Writing',       k:'Komunikasi',         t:'umum',   e:'📝', w:'#0891b2'},
    {n:'Presentasi',            k:'Komunikasi',         t:'umum',   e:'📊', w:'#ea580c'},
    {n:'Negosiasi',             k:'Komunikasi',         t:'umum',   e:'🤝', w:'#16a34a'},
    // Manajemen
    {n:'Project Management',    k:'Manajemen',          t:'umum',   e:'📋', w:'#6b7280'},
    {n:'Kepemimpinan',          k:'Manajemen',          t:'umum',   e:'👑', w:'#b45309'},
    {n:'Analisis Data',         k:'Manajemen',          t:'umum',   e:'📉', w:'#0891b2'},
    {n:'Customer Service',      k:'Manajemen',          t:'umum',   e:'🎧', w:'#16a34a'},
    // Pendidikan
    {n:'Membuat Materi Ajar',   k:'Pendidikan',         t:'umum',   e:'📚', w:'#b45309'},
    {n:'E-learning',            k:'Pendidikan',         t:'umum',   e:'💻', w:'#0891b2'},
    {n:'Moodle',                k:'Pendidikan',         t:'umum',   e:'🎓', w:'#ea580c'},
    // Frontend
    {n:'HTML / CSS',            k:'Frontend',           t:'teknis', e:'🌐', w:'#e34f26'},
    {n:'JavaScript',            k:'Frontend',           t:'teknis', e:'⚡', w:'#f7df1e'},
    {n:'TypeScript',            k:'Frontend',           t:'teknis', e:'🔷', w:'#3178c6'},
    {n:'React',                 k:'Frontend',           t:'teknis', e:'⚛️', w:'#61dafb'},
    {n:'Vue.js',                k:'Frontend',           t:'teknis', e:'🟢', w:'#4fc08d'},
    {n:'Next.js',               k:'Frontend',           t:'teknis', e:'▲', w:'#000000'},
    {n:'Tailwind CSS',          k:'Frontend',           t:'teknis', e:'🎨', w:'#38bdf8'},
    {n:'Bootstrap',             k:'Frontend',           t:'teknis', e:'💜', w:'#7952b3'},
    // Backend
    {n:'PHP',                   k:'Backend',            t:'teknis', e:'🐘', w:'#8892bf'},
    {n:'Laravel',               k:'Backend',            t:'teknis', e:'🔴', w:'#ff2d20'},
    {n:'Node.js',               k:'Backend',            t:'teknis', e:'🟩', w:'#339933'},
    {n:'Express.js',            k:'Backend',            t:'teknis', e:'🚂', w:'#000000'},
    {n:'Python',                k:'Backend',            t:'teknis', e:'🐍', w:'#3776ab'},
    {n:'Django',                k:'Backend',            t:'teknis', e:'🟩', w:'#092e20'},
    {n:'Go (Golang)',           k:'Backend',            t:'teknis', e:'🔵', w:'#00add8'},
    {n:'Rust',                  k:'Backend',            t:'teknis', e:'🦀', w:'#ce422b'},
    {n:'Java',                  k:'Backend',            t:'teknis', e:'☕', w:'#007396'},
    {n:'Spring Boot',           k:'Backend',            t:'teknis', e:'🌱', w:'#6db33f'},
    // Database
    {n:'MySQL',                 k:'Database',           t:'teknis', e:'🐬', w:'#4479a1'},
    {n:'PostgreSQL',            k:'Database',           t:'teknis', e:'🐘', w:'#336791'},
    {n:'MongoDB',               k:'Database',           t:'teknis', e:'🍃', w:'#47a248'},
    {n:'Redis',                 k:'Database',           t:'teknis', e:'🔴', w:'#dc382d'},
    {n:'SQLite',                k:'Database',           t:'teknis', e:'🗄️', w:'#0f80cc'},
    // Mobile
    {n:'Flutter',               k:'Mobile',             t:'teknis', e:'📱', w:'#54c5f8'},
    {n:'React Native',          k:'Mobile',             t:'teknis', e:'📲', w:'#61dafb'},
    {n:'Android (Kotlin)',      k:'Mobile',             t:'teknis', e:'🤖', w:'#7f52ff'},
    {n:'iOS (Swift)',           k:'Mobile',             t:'teknis', e:'🍎', w:'#fa7343'},
    // DevOps
    {n:'Docker',                k:'DevOps / CI-CD',     t:'teknis', e:'🐳', w:'#2496ed'},
    {n:'Kubernetes',            k:'DevOps / CI-CD',     t:'teknis', e:'⚙️', w:'#326ce5'},
    {n:'GitHub Actions',        k:'DevOps / CI-CD',     t:'teknis', e:'⚙️', w:'#24292e'},
    {n:'Jenkins',               k:'DevOps / CI-CD',     t:'teknis', e:'🔧', w:'#d24939'},
    {n:'Linux',                 k:'Infrastruktur',      t:'teknis', e:'🐧', w:'#fcc624'},
    {n:'Nginx',                 k:'Infrastruktur',      t:'teknis', e:'🟩', w:'#009639'},
    // Cloud
    {n:'AWS',                   k:'Cloud',              t:'teknis', e:'☁️', w:'#ff9900'},
    {n:'Google Cloud (GCP)',    k:'Cloud',              t:'teknis', e:'☁️', w:'#4285f4'},
    {n:'Firebase',              k:'Cloud',              t:'teknis', e:'🔥', w:'#ffca28'},
    {n:'Vercel',                k:'Cloud',              t:'teknis', e:'▲', w:'#000000'},
    {n:'Heroku',                k:'Cloud',              t:'teknis', e:'💜', w:'#430098'},
    // Data & AI
    {n:'Python (Data)',         k:'Data & AI',          t:'teknis', e:'🐍', w:'#3776ab'},
    {n:'TensorFlow',            k:'Data & AI',          t:'teknis', e:'🧠', w:'#ff6f00'},
    {n:'SQL Analytics',         k:'Data & AI',          t:'teknis', e:'📊', w:'#4479a1'},
    {n:'Tableau',               k:'Data & AI',          t:'teknis', e:'📉', w:'#e97627'},
    {n:'Power BI',              k:'Data & AI',          t:'teknis', e:'📊', w:'#f2c811'},
];

/* ─────────────────────────────────────────────────────────────
   SKILL AUTOCOMPLETE
   ───────────────────────────────────────────────────────────── */
const IT_CATS = ['Frontend','Backend','Database','Mobile','DevOps / CI-CD','Cloud','Infrastruktur','Data & AI','UI/UX'];

function cvSkillSearch(query) {
    const dd = document.getElementById('cvSkillDropdown');
    if (!query.trim()) { dd.style.display = 'none'; return; }
    const q = query.toLowerCase();
    const results = cvSkillDB.filter(s =>
        s.n.toLowerCase().includes(q) || s.k.toLowerCase().includes(q)
    ).slice(0, 10);
    if (results.length === 0) {
        // Offer adding custom skill
        dd.innerHTML = `<div class="cv2-skill-dd-item" onclick="cvAddCustomSkill('${query.replace(/'/g,"\\'")}')">
            <span class="cv2-skill-dd-emoji">✨</span>
            <span class="cv2-skill-dd-name">Tambah "<strong>${query}</strong>" sebagai skill kustom</span>
            <button class="cv2-skill-dd-add" type="button"><i class="fa-solid fa-plus"></i></button>
        </div>`;
    } else {
        dd.innerHTML = results.map(s => {
            const isIT = IT_CATS.includes(s.k);
            return `<div class="cv2-skill-dd-item" onclick="cvAddSkillFromSearch('${s.n.replace(/'/g,"\\'")}','${s.k.replace(/'/g,"\\'")}','${s.t}','${s.e}','${s.w}')">
                <span class="cv2-skill-dd-emoji">${s.e}</span>
                <span class="cv2-skill-dd-name">${s.n}</span>
                <span class="cv2-skill-dd-cat ${isIT ? 'cv2-skill-dd-it' : ''}">${s.k}</span>
                <button class="cv2-skill-dd-add" type="button"><i class="fa-solid fa-plus"></i></button>
            </div>`;
        }).join('');
    }
    dd.style.display = 'block';
}

function cvAddSkillFromSearch(nama, kat, tipe, emoji, warna) {
    const listId = (tipe === 'teknis') ? 'skill-teknis-list' : 'skill-umum-list';
    const counterType = (tipe === 'teknis') ? 'teknis' : 'umum';
    const prefix = (tipe === 'teknis') ? 'skill_teknis' : 'skill_umum';
    const idx = Date.now();
    _cvAddSkillItem(listId, nama, kat, tipe, emoji, warna, prefix, idx);
    cvUpdateSkillCount(counterType);
    document.getElementById('cvSkillSearchInput').value = '';
    document.getElementById('cvSkillDropdown').style.display = 'none';
    showToast(`<i class="fa-solid fa-plus-circle"></i> ${nama} ditambahkan!`);
}

function cvAddCustomSkill(nama) {
    _cvAddSkillItem('skill-umum-list', nama, 'Lainnya', 'umum', '✨', '#6b7280', 'skill_umum', Date.now());
    cvUpdateSkillCount('umum');
    document.getElementById('cvSkillSearchInput').value = '';
    document.getElementById('cvSkillDropdown').style.display = 'none';
}

function _cvAddSkillItem(listId, nama, kat, tipe, emoji, warna, prefix, idx) {
    const list  = document.getElementById(listId);
    const isIT  = IT_CATS.includes(kat);
    const div   = document.createElement('div');
    div.className = 'dyn-item cv2-skill-item is-open';
    div.innerHTML = `
        <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
            <h4>
                <span class="cv2-skill-emoji">${emoji}</span> ${nama}
                <span class="dyn-preview cv2-skill-cat-badge ${isIT ? 'cv2-skill-it-badge' : ''}">${kat}</span>
            </h4>
            <div class="dyn-item-header-btns">
                <span class="cv2-skill-level-badge" id="slv_badge_${idx}">70%</span>
                <button type="button" class="btn-remove-dyn"
                    onclick="event.stopPropagation();this.closest('.dyn-item').remove();cvUpdateSkillCount('${tipe === 'teknis' ? 'teknis' : 'umum'}')">
                    <i class="fa-solid fa-trash"></i>
                </button>
                <i class="fa-solid fa-chevron-down dyn-chevron"></i>
            </div>
        </div>
        <div class="dyn-body"><div class="dyn-body-inner">
            <input type="hidden" name="${prefix}_nama[]"     value="${nama}">
            <input type="hidden" name="${prefix}_kategori[]" value="${kat}">
            <input type="hidden" name="${prefix}_icon[]"     value="${emoji}">
            <input type="hidden" name="${prefix}_warna[]"    value="${warna}">
            <div class="cv2-skill-level-wrap">
                <label>Tingkat Kemampuan: <strong id="slv_${idx}">70%</strong></label>
                <div class="cv2-level-slider-row">
                    <span class="cv2-level-label-sm">Pemula</span>
                    <input type="range" name="${prefix}_level[]"
                           min="10" max="100" step="5" value="70"
                           oninput="document.getElementById('slv_${idx}').textContent=this.value+'%';document.getElementById('slv_badge_${idx}').textContent=this.value+'%'">
                    <span class="cv2-level-label-sm">Ahli</span>
                </div>
            </div>
        </div></div>`;
    list.appendChild(div);
}

function cvUpdateSkillCount(type) {
    const id   = type === 'teknis' ? 'skill-teknis-list' : 'skill-umum-list';
    const cid  = type === 'teknis' ? 'skillTeknisCount' : 'skillUmumCount';
    const cnt  = document.querySelectorAll(`#${id} .dyn-item`).length;
    const el   = document.getElementById(cid);
    if (el) el.textContent = cnt;
}

// Close dropdown on outside click
document.addEventListener('click', e => {
    if (!e.target.closest('.cv2-skill-autocomplete-wrap')) {
        const dd = document.getElementById('cvSkillDropdown');
        if (dd) dd.style.display = 'none';
    }
});

// Slider background gradient helper
function cvSliderBg(val) {
    const pct = ((val - 10) / 90) * 100;
    return `linear-gradient(to right,rgba(255,255,255,.7) 0%,rgba(255,255,255,.7) ${pct}%,rgba(255,255,255,.2) ${pct}%)`;
}

/* ─────────────────────────────────────────────────────────────
   SOSIAL MEDIA
   ───────────────────────────────────────────────────────────── */
function cvAddSosmed(btn) {
    const key   = btn.dataset.key;
    const label = btn.dataset.label;
    const icon  = btn.dataset.icon;
    const warna = btn.dataset.warna;
    const ph    = btn.dataset.ph;

    // Cegah duplikat
    const existing = document.querySelector(`#cvSosmedList [data-platform="${key}"]`);
    if (existing) {
        existing.querySelector('.cv2-sosmed-url-input').focus();
        return;
    }

    document.getElementById('cvSosmedEmpty')?.remove();
    const list = document.getElementById('cvSosmedList');
    const div  = document.createElement('div');
    div.className = 'cv2-sosmed-item';
    div.dataset.platform = key;
    div.innerHTML = `
        <div class="cv2-sosmed-icon" style="background:${warna}20;color:${warna}">
            <i class="${icon}"></i>
        </div>
        <div class="cv2-sosmed-fields">
            <input type="hidden" name="sosmed_platform[]" value="${key}">
            <input type="hidden" name="sosmed_icon[]"     value="${icon}">
            <input type="hidden" name="sosmed_warna[]"    value="${warna}">
            <span class="cv2-sosmed-label">${label}</span>
            <input type="text" name="sosmed_url[]" placeholder="${ph}" class="cv2-sosmed-url-input">
        </div>
        <button type="button" class="cv2-sosmed-remove"
                onclick="this.closest('.cv2-sosmed-item').remove();cvCheckSosmedEmpty();cvUpdatePlatformBtn('${key}',false)">
            <i class="fa-solid fa-xmark"></i>
        </button>`;
    list.appendChild(div);
    div.querySelector('input[type=text]').focus();

    // Update tombol platform
    cvUpdatePlatformBtn(key, true);
}

function cvUpdatePlatformBtn(key, added) {
    const btn = document.querySelector(`#cvPlatformGrid [data-key="${key}"]`);
    if (!btn) return;
    if (added) {
        btn.classList.add('cv2-plat-btn-added');
        if (!btn.querySelector('.cv2-plat-check')) {
            btn.insertAdjacentHTML('beforeend', '<i class="fa-solid fa-check cv2-plat-check"></i>');
        }
    } else {
        btn.classList.remove('cv2-plat-btn-added');
        btn.querySelector('.cv2-plat-check')?.remove();
    }
}

function cvCheckSosmedEmpty() {
    const list = document.getElementById('cvSosmedList');
    if (!list.querySelector('.cv2-sosmed-item')) {
        list.innerHTML = `<div class="cv2-empty-hint" id="cvSosmedEmpty">
            <i class="fa-regular fa-face-smile"></i>
            Belum ada. Klik platform di atas untuk menambahkan.
        </div>`;
    }
}

/* ─────────────────────────────────────────────────────────────
   BAHASA TOGGLE
   ───────────────────────────────────────────────────────────── */
function cvToggleBahasa(item) {
    const cb  = item.querySelector('input[type=checkbox]');
    const sel = item.querySelector('select');
    cb.checked = !cb.checked;
    item.classList.toggle('checked', cb.checked);
    sel.disabled = !cb.checked;
}

/* ─────────────────────────────────────────────────────────────
   FOTO PROFIL PREVIEW
   ───────────────────────────────────────────────────────────── */
function cvPreviewFoto(input) {
    if (!input.files[0]) return;
    if (input.files[0].size > 3 * 1024 * 1024) {
        alert('Ukuran foto terlalu besar (maks 3 MB)');
        return;
    }
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('cvFotoPreview').src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}

/* ─────────────────────────────────────────────────────────────
   TAMBAH ITEM PORTFOLIO & SERTIFIKASI (override app.js default)
   ───────────────────────────────────────────────────────────── */
function addPortoItem() {
    const list = document.getElementById('porto-list');
    const i    = Date.now();
    const div  = document.createElement('div');
    div.className = 'dyn-item is-open';
    div.innerHTML = `
        <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
            <h4><i class="fa-solid fa-folder-open"></i> Proyek Baru <span class="dyn-preview"> &mdash; isi data di bawah</span></h4>
            <div class="dyn-item-header-btns">
                <button type="button" class="btn-remove-dyn" onclick="event.stopPropagation();this.closest('.dyn-item').remove()"><i class="fa-solid fa-trash"></i></button>
                <i class="fa-solid fa-chevron-down dyn-chevron"></i>
            </div>
        </div>
        <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
            <div class="dyn-field"><label>Nama Proyek</label><input type="text" name="porto_nama[]" placeholder="cth: Website UMKM, Album Foto Wisuda"></div>
            <div class="dyn-field"><label>Kategori</label><input type="text" name="porto_kategori[]" list="porto-kategori-list" placeholder="Web, Fotografi, Event..."></div>
            <div class="dyn-field"><label>Link URL (opsional)</label><input type="url" name="porto_url[]" placeholder="https://..."></div>
            <div class="dyn-field"><label>Tools yang Dipakai</label><input type="text" name="porto_tech[]" placeholder="cth: Canva, Laravel, Kamera Sony"></div>
            <div class="dyn-field full-width"><label>Deskripsi</label><textarea name="porto_desc[]" rows="3" placeholder="Ceritakan proyekmu..."></textarea></div>
        </div></div></div>`;
    list.appendChild(div);
    div.querySelector('input')?.focus();
}

function addSertifItem() {
    const list = document.getElementById('sertif-list');
    const div  = document.createElement('div');
    div.className = 'dyn-item is-open';
    div.innerHTML = `
        <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
            <h4><i class="fa-solid fa-award"></i> Sertifikasi Baru</h4>
            <div class="dyn-item-header-btns">
                <button type="button" class="btn-remove-dyn" onclick="event.stopPropagation();this.closest('.dyn-item').remove()"><i class="fa-solid fa-trash"></i></button>
                <i class="fa-solid fa-chevron-down dyn-chevron"></i>
            </div>
        </div>
        <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
            <div class="dyn-field"><label>Nama Sertifikat</label><input type="text" name="sertif_nama[]" placeholder="cth: Sertifikat Pelatihan K3"></div>
            <div class="dyn-field"><label>Penerbit</label><input type="text" name="sertif_penerbit[]" placeholder="cth: Google, Kemendikbud, BNSP"></div>
            <div class="dyn-field"><label>Tahun</label><input type="text" name="sertif_tahun[]" placeholder="2024"></div>
            <div class="dyn-field"><label>Link Verifikasi</label><input type="url" name="sertif_url[]" placeholder="https://..."></div>
        </div></div></div>`;
    list.appendChild(div);
    div.querySelector('input')?.focus();
}
</script>