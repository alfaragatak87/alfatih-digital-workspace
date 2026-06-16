<?php if (!defined('SITE_URL')) exit; 

// +------------------------------------------------------------------------------+
// ¦  FILE: tampilan/dasbor/pembuat_cv.php                                        ¦
// ¦                                                                              ¦
// ¦  DESKRIPSI:                                                                  ¦
// ¦  Antarmuka Builder (Pembuat) CV dan Profil Profesional. Berisi form panjang  ¦
// ¦  untuk mengisi data Identitas, Ringkasan, Pendidikan, Pengalaman, dan Skill. ¦
// ¦                                                                              ¦
// ¦  KONEKSI & RELASI:                                                           ¦
// ¦  - Di-include oleh index.php pada rute page=profile.                     ¦
// ¦  - Mengirim data (Submit) ke ksi_profil.php melalui index.php dengan    ¦
// ¦    action 'save_profile_data' untuk dibungkus ke format JSON.                ¦
// ¦                                                                              ¦
// ¦  BARIS KODE PENTING:                                                         ¦
// ¦  - Blok Input Array (Pendidikan/Skill) : Memungkinkan user untuk menambah    ¦
// ¦    kolom input dinamis (Multiple rows) via JavaScript Vanilla.               ¦
// +------------------------------------------------------------------------------+
$active_tab = $_GET['tab'] ?? 'identitas';
$ident_d = $profile_data['identitas'] ?? [];
$edu_d   = $profile_data['pendidikan'] ?? [];
$exp_d   = $profile_data['pengalaman'] ?? [];
$skill_d = $profile_data['keahlian'] ?? [];
$porto_d = $profile_data['portfolio'] ?? [];
?>
<style>
/* â•â• CV BUILDER DARK THEME â•â• */
.pb-wrapper {
    display: flex;
    gap: 24px;
    color: #e2e8f0;
    font-family: var(--f-body);
}
/* SIDEBAR */
.pb-sidebar {
    width: 250px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.pb-tab {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    border-radius: 12px;
    color: #94a3b8;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    background: transparent;
    border: none;
    text-align: left;
    font-size: 0.95rem;
    width: 100%;
}
.pb-tab.active {
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(167, 139, 250, 0.2));
    color: #fff;
    box-shadow: 0 4px 20px rgba(139, 92, 246, 0.15);
}
.pb-tab:hover:not(.active) {
    background: rgba(255,255,255,0.03);
    color: #e2e8f0;
}
/* CONTENT */
.pb-content {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: 24px;
    min-width: 0;
}
/* HEADER CARD */
.pb-header-card {
    background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%);
    border: 1px solid rgba(139, 92, 246, 0.2);
    border-radius: 24px;
    padding: 32px 40px;
    display: flex;
    align-items: center;
    gap: 32px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255,255,255,0.1);
}
.pb-header-card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -10%;
    width: 60%;
    height: 200%;
    background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 0%, transparent 70%);
    z-index: 0;
    transform: rotate(15deg);
    pointer-events: none;
}
.pb-avatar {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 0 0 4px #1c1e29, 0 0 0 8px rgba(139, 92, 246, 0.5), 0 12px 24px rgba(0,0,0,0.5);
    position: relative;
    z-index: 1;
}
.pb-header-info { 
    flex-grow: 1; 
    position: relative;
    z-index: 1;
}
.pb-eyebrow {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #a78bfa;
    margin-bottom: 6px;
    display: block;
    font-weight: 600;
}
.pb-name-row {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 6px;
}
.pb-name {
    font-size: 1.8rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0;
    letter-spacing: -0.5px;
    text-shadow: 0 2px 12px rgba(0,0,0,0.4);
}
.badge-profesi {
    background: linear-gradient(135deg, #8b5cf6, #6d28d9);
    color: #fff;
    font-size: 0.75rem;
    padding: 6px 12px;
    border-radius: 8px;
    font-weight: 700;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}
.pb-nickname { font-size: 0.95rem; color: #cbd5e1; font-style: italic;}
.portfolio-link-box {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(15, 23, 42, 0.6);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 8px 16px;
    margin-top: 16px;
    width: fit-content;
    color: #94a3b8;
    font-size: 0.85rem;
    backdrop-filter: blur(8px);
}
.portfolio-link-box button {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: #e2e8f0;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
}
.portfolio-link-box button:hover { 
    background: rgba(139, 92, 246, 0.2); 
    border-color: rgba(139, 92, 246, 0.5);
    color: #fff;
    transform: translateY(-1px);
}

/* FORMS */
.pb-form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 16px;
}
.pb-form-group label {
    font-size: 0.85rem;
    color: #94a3b8;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
}
.pb-form-group label i { color: #8b5cf6; font-size: 0.9em; }
.pb-input {
    background: #15161f;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 8px;
    padding: 12px 16px;
    color: #fff;
    font-size: 0.95rem;
    transition: all 0.2s;
    width: 100%;
    box-sizing: border-box;
}
.pb-input:focus {
    border-color: #8b5cf6;
    box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2);
    outline: none;
}
select.pb-input option { background: #1c1e29; color: #fff; }
.pb-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

/* IDENTITAS MASONRY */
.pb-cards-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    grid-template-areas:
        "primary alamat"
        "contact alamat";
    gap: 24px;
}
.pb-card {
    background: #1c1e29;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 20px;
    padding: 24px;
}
.pb-card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #fff;
    margin-bottom: 20px;
}
.area-primary { grid-area: primary; }
.area-contact { grid-area: contact; }
.area-alamat { grid-area: alamat; }

/* SPLIT PANE */
.pb-split-pane {
    display: flex;
    gap: 24px;
    height: 650px;
}
.pb-split-left {
    width: 380px;
    background: #1c1e29;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.pb-split-left-header {
    padding: 20px 24px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.pb-split-left-title { font-size: 1.1rem; font-weight: 600; color: #fff; }
.pb-btn-add {
    background: rgba(16, 185, 129, 0.15);
    color: #10b981;
    border: none;
    border-radius: 6px;
    padding: 6px 12px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}
.pb-btn-add:hover { background: #10b981; color: #fff; }
.pb-split-list {
    flex-grow: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.pb-list-item {
    background: transparent;
    border: 1px solid transparent;
    border-radius: 12px;
    padding: 16px;
    cursor: pointer;
    transition: all 0.2s;
}
.pb-list-item:hover:not(.active) { background: rgba(255,255,255,0.03); }
.pb-list-item.active {
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.15), rgba(167, 139, 250, 0.15));
    border: 1px solid rgba(139, 92, 246, 0.3);
}
.pb-list-item-title {
    color: #fff;
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.pb-list-item-sub { color: #94a3b8; font-size: 0.85rem; }
.pb-list-item-actions {
    display: flex;
    gap: 8px;
    margin-top: 12px;
}
.pb-btn-action {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
}
.pb-btn-action.edit { background: rgba(16, 185, 129, 0.15); color: #10b981; }
.pb-btn-action.danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
.pb-split-right {
    flex-grow: 1;
    background: #1c1e29;
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 20px;
    padding: 32px;
    overflow-y: auto;
}

/* SAVE BAR */
.pb-save-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    background: rgba(28, 30, 41, 0.85); /* Glassmorphism */
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 16px;
    box-shadow: 0 -4px 32px rgba(0,0,0,0.3);
    position: sticky;
    bottom: 24px;
    z-index: 100;
    margin-top: 32px;
}
.btn-save-changes {
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
    border: none;
    padding: 12px 28px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3);
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    animation: pulseGlow 2s infinite;
}
.btn-save-changes:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}
@keyframes pulseGlow {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
.btn-cancel {
    background: rgba(255,255,255,0.05);
    color: #e2e8f0;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}
/* RESPONSIVE DESIGN (MOBILE) */
@media screen and (max-width: 992px) {
    .pb-wrapper {
        flex-direction: column;
    }
    .pb-sidebar {
        width: 100%;
        flex-direction: row;
        overflow-x: auto;
        padding-bottom: 8px;
    }
    .pb-tab {
        white-space: nowrap;
        width: auto;
        padding: 10px 16px;
    }
    .pb-cards-grid {
        grid-template-columns: 1fr;
        grid-template-areas:
            "primary"
            "contact"
            "alamat";
    }
    .pb-split-pane {
        flex-direction: column;
        height: auto;
    }
    .pb-split-left {
        width: 100%;
        max-height: 400px;
    }
    .pb-split-right {
        width: 100%;
        padding: 20px;
        box-sizing: border-box;
    }
    .pb-form-row {
        grid-template-columns: 1fr;
    }
    .pb-header-card {
        flex-direction: column;
        text-align: center;
        padding: 24px 20px;
        gap: 20px;
    }
    .pb-name {
        font-size: 1.4rem;
    }
    .pb-name-row {
        justify-content: center;
        flex-wrap: wrap;
        gap: 8px;
    }
    .portfolio-link-box {
        margin: 16px auto 0;
        max-width: 100%;
        box-sizing: border-box;
    }
    .portfolio-link-box span {
        max-width: 140px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .pb-save-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
        padding: 12px 16px;
        bottom: 12px;
        border-radius: 12px;
    }
    .pb-save-bar > label {
        align-items: flex-start;
        text-align: left;
        font-size: 0.8rem;
        line-height: 1.4;
    }
    .btn-save-changes {
        width: 100%;
        justify-content: center;
        padding: 10px;
    }
}
</style>

<div class="pb-wrapper">
    <!-- SIDEBAR -->
    <div class="pb-sidebar">
        <button class="pb-tab <?= $active_tab==='identitas'?'active':'' ?>" onclick="switchPbTab('identitas', this)"><i class="fa-solid fa-user"></i> Identitas</button>
        <button class="pb-tab <?= $active_tab==='pendidikan'?'active':'' ?>" onclick="switchPbTab('pendidikan', this)"><i class="fa-solid fa-graduation-cap"></i> Pendidikan</button>
        <button class="pb-tab <?= $active_tab==='pengalaman'?'active':'' ?>" onclick="switchPbTab('pengalaman', this)"><i class="fa-solid fa-briefcase"></i> Pengalaman</button>
        <button class="pb-tab <?= $active_tab==='keahlian'?'active':'' ?>" onclick="switchPbTab('keahlian', this)"><i class="fa-solid fa-code"></i> Keahlian</button>
    </div>

    <!-- MAIN CONTENT -->
    <div class="pb-content">
        <!-- HEADER CARD -->
        <div class="pb-header-card">
            <img src="<?= h($path_foto) ?>" alt="Avatar" class="pb-avatar" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($nama_lengkap) ?>&background=1a1a1a&color=ffffff&bold=true&size=400'">
            <div class="pb-header-info">
                <span class="pb-eyebrow">Profile Builder</span>
                <div class="pb-name-row">
                    <h2 class="pb-name"><?= h($nama_lengkap) ?></h2>
                    <span class="badge-profesi"><?= h($ident_d['profesi'] ?? 'PROFESI') ?></span>
                </div>
                <span class="pb-nickname"><?= h($ident_d['nama_sebutan'] ?? 'Nickname') ?></span>
                <div class="portfolio-link-box">
                    <span><?= h($portfolio_url) ?></span>
                    <button onclick="navigator.clipboard.writeText('<?= h($portfolio_url) ?>')"><i class="fa-solid fa-copy"></i> Copy</button>
                    <a href="<?= h($portfolio_url) ?>" target="_blank" style="text-decoration:none;"><button><i class="fa-solid fa-eye"></i> View</button></a>
                </div>
            </div>
        </div>

        <form method="POST" id="formProfile">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="action" value="save_profile_data">
            <input type="hidden" name="profile_tab" id="profileTabInput" value="<?= h($active_tab) ?>">

            <!-- TAB: IDENTITAS -->
            <div id="tab-identitas" class="pb-tab-content" style="<?= $active_tab!=='identitas'?'display:none;':'' ?>">
                <div class="pb-cards-grid">
                    <!-- Area: Primary -->
                    <div class="pb-card area-primary">
                        <div class="pb-card-title">Primary Identity</div>
                        <div class="pb-form-row">
                            <div class="pb-form-group">
                                <label><i class="fa-regular fa-user"></i> Nama Panggilan</label>
                                <input type="text" class="pb-input" name="pd_nama_sebutan" value="<?= h($ident_d['nama_sebutan']??'') ?>">
                            </div>
                            <div class="pb-form-group">
                                <label><i class="fa-regular fa-id-badge"></i> Nama Lengkap</label>
                                <input type="text" class="pb-input" name="pd_nama" value="<?= h($nama_lengkap) ?>" readonly style="opacity:0.7;cursor:not-allowed;">
                            </div>
                        </div>
                        <div class="pb-form-group">
                            <label><i class="fa-solid fa-briefcase"></i> Profesi (Optional)</label>
<?php
$currentProfesi = $ident_d['profesi'] ?? '';
$predefined = ['Web Developer', 'Desainer Grafis', 'Guru', 'Pelajar', 'Mahasiswa', 'Pegawai Bank', 'Wiraswasta', 'Freelancer'];
$isCustom = ($currentProfesi !== '' && !in_array($currentProfesi, $predefined));
?>
                            <select class="pb-input" id="profesiSelect" name="<?= $isCustom ? '' : 'pd_profesi' ?>" onchange="toggleProfesi(this.value)">
                                <option value="">Pilih Profesi</option>
                                <?php foreach($predefined as $p): ?>
                                    <option value="<?= $p ?>" <?= $currentProfesi === $p ? 'selected' : '' ?>><?= $p ?></option>
                                <?php endforeach; ?>
                                <option value="Lainnya" <?= $isCustom ? 'selected' : '' ?>>Lainnya (Tulis sendiri...)</option>
                            </select>
                            <input type="text" class="pb-input" id="profesiInput" name="<?= $isCustom ? 'pd_profesi' : '' ?>" value="<?= $isCustom ? h($currentProfesi) : '' ?>" placeholder="Tulis profesi Anda..." style="margin-top:8px; <?= $isCustom ? '' : 'display:none;' ?>">
                        </div>
                        <div class="pb-form-group">
                            <label><i class="fa-solid fa-tag"></i> Tagline</label>
                            <input type="text" class="pb-input" name="pd_tagline" value="<?= h($ident_d['tagline']??'') ?>">
                        </div>
                    </div>

                    <!-- Area: Contact -->
                    <div class="pb-card area-contact">
                        <div class="pb-card-title">Contact & Sosial Link</div>
                        <div class="pb-form-row">
                            <div class="pb-form-group">
                                <label><i class="fa-brands fa-github"></i> GitHub URL</label>
                                <input type="url" class="pb-input" name="pd_github" value="<?= h($ident_d['github']??'') ?>">
                            </div>
                            <div class="pb-form-group">
                                <label><i class="fa-brands fa-linkedin"></i> LinkedIn URL</label>
                                <input type="url" class="pb-input" name="pd_linkedin" value="<?= h($ident_d['linkedin']??'') ?>">
                            </div>
                        </div>
                        <div class="pb-form-row">
                            <div class="pb-form-group">
                                <label><i class="fa-brands fa-instagram"></i> Instagram URL</label>
                                <input type="url" class="pb-input" name="pd_instagram" value="<?= h($ident_d['instagram']??'') ?>">
                            </div>
                            <div class="pb-form-group">
                                <label><i class="fa-brands fa-whatsapp"></i> No. Telepon / WhatsApp</label>
                                <input type="text" class="pb-input" name="pd_phone" value="<?= h($ident_d['phone']??'') ?>">
                            </div>
                        </div>
                        <div class="pb-form-row">
                            <div class="pb-form-group">
                                <label><i class="fa-regular fa-envelope"></i> Email</label>
                                <input type="email" class="pb-input" name="pd_email" value="<?= h($ident_d['email']??'') ?>">
                            </div>
                            <div class="pb-form-group">
                                <label><i class="fa-solid fa-globe"></i> Website URL</label>
                                <input type="url" class="pb-input" name="pd_website" value="<?= h($ident_d['website']??'') ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Area: Alamat -->
                    <div class="pb-card area-alamat">
                        <div class="pb-card-title">Alamat</div>
                        <div class="pb-form-row">
                            <div class="pb-form-group">
                                <label><i class="fa-solid fa-map-location-dot"></i> Provinsi</label>
                                <select class="pb-input" id="selProvinsi" onchange="loadWilayah('kabupaten', this)">
                                    <option value="">Memuat...</option>
                                </select>
                                <input type="hidden" name="pd_provinsi" id="valProvinsi" value="<?= h($ident_d['provinsi']??'') ?>">
                            </div>
                            <div class="pb-form-group">
                                <label><i class="fa-solid fa-city"></i> Kabupaten</label>
                                <select class="pb-input" id="selKabupaten" onchange="loadWilayah('kecamatan', this)" disabled>
                                    <option value="">Pilih Provinsi Dahulu</option>
                                </select>
                                <input type="hidden" name="pd_kabupaten" id="valKabupaten" value="<?= h($ident_d['kabupaten']??'') ?>">
                            </div>
                        </div>
                        <div class="pb-form-row">
                            <div class="pb-form-group">
                                <label><i class="fa-solid fa-map"></i> Kecamatan</label>
                                <select class="pb-input" id="selKecamatan" onchange="loadWilayah('desa', this)" disabled>
                                    <option value="">Pilih Kabupaten Dahulu</option>
                                </select>
                                <input type="hidden" name="pd_kecamatan" id="valKecamatan" value="<?= h($ident_d['kecamatan']??'') ?>">
                            </div>
                            <div class="pb-form-group">
                                <label><i class="fa-solid fa-house"></i> Desa / Kelurahan</label>
                                <select class="pb-input" id="selDesa" onchange="updateVal('desa', this)" disabled>
                                    <option value="">Pilih Kecamatan Dahulu</option>
                                </select>
                                <input type="hidden" name="pd_desa" id="valDesa" value="<?= h($ident_d['desa']??'') ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: PENDIDIKAN -->
            <div id="tab-pendidikan" class="pb-tab-content" style="<?= $active_tab!=='pendidikan'?'display:none;':'' ?>">
                <div class="pb-split-pane">
                    <div class="pb-split-left">
                        <div class="pb-split-left-header">
                            <span class="pb-split-left-title">Daftar Pendidikan</span>
                            <button type="button" class="pb-btn-add" onclick="addSplitItem('edu')">+ Tambah Riwayat</button>
                        </div>
                        <div class="pb-split-list" id="edu-list">
                            <?php foreach($edu_d as $i => $ed){ ?>
                            <div class="dyn-item pb-list-item <?= $i===0?'active':'' ?>" onclick="activateSplitItem(this, 'edu')">
                                <div class="pb-list-item-content">
                                    <div class="pb-list-item-title"><i class="fa-solid fa-graduation-cap"></i> <?= h($ed['institusi']??'') ?></div>
                                    <div class="pb-list-item-sub"><?= h($ed['gelar']??'') ?></div>
                                    <div class="pb-list-item-sub" style="font-size:0.75rem;margin-top:2px;opacity:0.7;"><span class="v-mulai"><?= h($ed['tahun_mulai']??'') ?></span> - <span class="v-selesai"><?= h($ed['tahun_selesai']??'') ?></span> <?= !empty($ed['is_current'])?'(Saat Ini)':'' ?></div>
                                </div>
                                <div class="pb-list-item-actions">
                                    <button type="button" class="pb-btn-action edit">Edit</button>
                                    <button type="button" class="pb-btn-action danger" onclick="event.stopPropagation();this.closest('.dyn-item').remove()">Hapus</button>
                                </div>
                                <div class="dyn-body" style="display:none;">
                                    <input type="hidden" class="fi-institusi" name="edu_institusi[]" value="<?= h($ed['institusi']??'') ?>">
                                    <input type="hidden" class="fi-gelar" name="edu_gelar[]" value="<?= h($ed['gelar']??'') ?>">
                                    <input type="hidden" class="fi-mulai" name="edu_mulai[]" value="<?= h($ed['tahun_mulai']??'') ?>">
                                    <input type="hidden" class="fi-selesai" name="edu_selesai[]" value="<?= h($ed['tahun_selesai']??'') ?>">
                                    <input type="hidden" class="fi-current" name="edu_is_current_tmp[]" value="<?= !empty($ed['is_current'])?'1':'0' ?>">
                                    <input type="checkbox" style="display:none;" name="edu_is_current[<?= $i ?>]" <?= !empty($ed['is_current'])?'checked':'' ?>>
                                    <input type="hidden" class="fi-ipk" name="edu_ipk[]" value="<?= h($ed['ipk_nilai']??'') ?>">
                                    <textarea class="fi-desc" name="edu_desc[]"><?= h($ed['deskripsi']??'') ?></textarea>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="pb-split-right">
                        <div class="pb-card-title">Detail Pendidikan</div>
                        <div class="pb-form-group">
                            <label>Nama Institusi / Universitas</label>
                            <input type="text" class="pb-input" id="active_edu_institusi" oninput="syncActiveEdu()">
                        </div>
                        <div class="pb-form-group">
                            <label>Gelar / Jurusan</label>
                            <input type="text" class="pb-input" id="active_edu_gelar" oninput="syncActiveEdu()">
                        </div>
                        <div class="pb-form-row" style="align-items:flex-end;">
                            <div class="pb-form-group">
                                <label>Tahun Mulai</label>
                                <input type="text" class="pb-input" id="active_edu_mulai" oninput="syncActiveEdu()">
                            </div>
                            <div class="pb-form-group" style="flex-grow:1;">
                                <label>Tahun Selesai</label>
                                <div style="display:flex;gap:12px;align-items:center;">
                                    <input type="text" class="pb-input" id="active_edu_selesai" style="flex-grow:1;" oninput="syncActiveEdu()">
                                    <label style="margin:0;color:#e2e8f0;cursor:pointer;"><input type="checkbox" id="active_edu_current" onchange="syncActiveEdu()"> Saat Ini</label>
                                </div>
                            </div>
                        </div>
                        <div class="pb-form-group">
                            <label>IPK / Nilai</label>
                            <input type="text" class="pb-input" id="active_edu_ipk" oninput="syncActiveEdu()">
                        </div>
                        <div class="pb-form-group">
                            <label>Deskripsi / Pencapaian</label>
                            <textarea class="pb-input" id="active_edu_desc" rows="4" oninput="syncActiveEdu()"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: PENGALAMAN -->
            <div id="tab-pengalaman" class="pb-tab-content" style="<?= $active_tab!=='pengalaman'?'display:none;':'' ?>">
                <div class="pb-split-pane">
                    <div class="pb-split-left">
                        <div class="pb-split-left-header">
                            <span class="pb-split-left-title">Daftar Pengalaman</span>
                            <button type="button" class="pb-btn-add" onclick="addSplitItem('exp')">+ Tambah Pengalaman</button>
                        </div>
                        <div class="pb-split-list" id="exp-list">
                            <?php foreach($exp_d as $i => $ex){ ?>
                            <div class="dyn-item pb-list-item <?= $i===0?'active':'' ?>" onclick="activateSplitItem(this, 'exp')">
                                <div class="pb-list-item-content">
                                    <div class="pb-list-item-title"><i class="fa-solid fa-briefcase"></i> <?= h($ex['perusahaan']??'') ?></div>
                                    <div class="pb-list-item-sub">Posisi: <span class="v-jabatan"><?= h($ex['jabatan']??'') ?></span></div>
                                    <div class="pb-list-item-sub" style="font-size:0.75rem;margin-top:2px;opacity:0.7;"><span class="v-mulai"><?= h($ex['tahun_mulai']??'') ?></span> - <span class="v-selesai"><?= h($ex['tahun_selesai']??'') ?></span> <?= !empty($ex['is_current'])?'(Saat Ini)':'' ?></div>
                                </div>
                                <div class="pb-list-item-actions">
                                    <button type="button" class="pb-btn-action edit">Edit</button>
                                    <button type="button" class="pb-btn-action danger" onclick="event.stopPropagation();this.closest('.dyn-item').remove()">Hapus</button>
                                </div>
                                <div class="dyn-body" style="display:none;">
                                    <input type="hidden" class="fi-jabatan" name="exp_jabatan[]" value="<?= h($ex['jabatan']??'') ?>">
                                    <input type="hidden" class="fi-perusahaan" name="exp_perusahaan[]" value="<?= h($ex['perusahaan']??'') ?>">
                                    <input type="hidden" class="fi-mulai" name="exp_mulai[]" value="<?= h($ex['tahun_mulai']??'') ?>">
                                    <input type="hidden" class="fi-selesai" name="exp_selesai[]" value="<?= h($ex['tahun_selesai']??'') ?>">
                                    <input type="hidden" class="fi-current" name="exp_is_current_tmp[]" value="<?= !empty($ex['is_current'])?'1':'0' ?>">
                                    <input type="checkbox" style="display:none;" name="exp_is_current[<?= $i ?>]" <?= !empty($ex['is_current'])?'checked':'' ?>>
                                    <textarea class="fi-desc" name="exp_desc[]"><?= h($ex['deskripsi']??'') ?></textarea>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="pb-split-right">
                        <div class="pb-card-title">Detail Pengalaman Kerja</div>
                        <div class="pb-form-group">
                            <label>Nama Perusahaan / Organisasi</label>
                            <input type="text" class="pb-input" id="active_exp_perusahaan" oninput="syncActiveExp()">
                        </div>
                        <div class="pb-form-group">
                            <label>Posisi / Jabatan</label>
                            <input type="text" class="pb-input" id="active_exp_jabatan" oninput="syncActiveExp()">
                        </div>
                        <div class="pb-form-row" style="align-items:flex-end;">
                            <div class="pb-form-group">
                                <label>Tahun Mulai</label>
                                <input type="text" class="pb-input" id="active_exp_mulai" oninput="syncActiveExp()">
                            </div>
                            <div class="pb-form-group" style="flex-grow:1;">
                                <label>Tahun Selesai</label>
                                <div style="display:flex;gap:12px;align-items:center;">
                                    <input type="text" class="pb-input" id="active_exp_selesai" style="flex-grow:1;" oninput="syncActiveExp()">
                                    <label style="margin:0;color:#e2e8f0;cursor:pointer;"><input type="checkbox" id="active_exp_current" onchange="syncActiveExp()"> Saat Ini</label>
                                </div>
                            </div>
                        </div>
                        <div class="pb-form-group">
                            <label>Deskripsi / Tanggung Jawab</label>
                            <textarea class="pb-input" id="active_exp_desc" rows="4" oninput="syncActiveExp()"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB: KEAHLIAN -->
            <div id="tab-keahlian" class="pb-tab-content" style="<?= $active_tab!=='keahlian'?'display:none;':'' ?>">
                <div class="pb-split-pane">
                    <div class="pb-split-left">
                        <div class="pb-split-left-header">
                            <span class="pb-split-left-title">Daftar Keahlian</span>
                            <button type="button" class="pb-btn-add" onclick="addSplitItem('skill')">+ Tambah Keahlian</button>
                        </div>
                        <div class="pb-split-list" id="skill-list">
                            <?php foreach($skill_d as $i => $sk){ 
                                $ic = !empty($sk['logo_icon']) ? $sk['logo_icon'] : 'fa-solid fa-code';
                            ?>
                            <div class="dyn-item pb-list-item <?= $i===0?'active':'' ?>" onclick="activateSplitItem(this, 'skill')">
                                <div class="pb-list-item-content">
                                    <div class="pb-list-item-title"><i class="<?= h($ic) ?>"></i> <?= h($sk['nama']??'') ?></div>
                                    <div class="pb-list-item-sub">Kategori: <?= h($sk['kategori']??'Lainnya') ?></div>
                                </div>
                                <div class="pb-list-item-actions">
                                    <button type="button" class="pb-btn-action edit">Edit</button>
                                    <button type="button" class="pb-btn-action danger" onclick="event.stopPropagation();this.closest('.dyn-item').remove()">Hapus</button>
                                </div>
                                <div class="dyn-body" style="display:none;">
                                    <input type="hidden" class="fi-nama" name="skill_nama[]" value="<?= h($sk['nama']??'') ?>">
                                    <input type="hidden" class="fi-kategori" name="skill_kategori[]" value="<?= h($sk['kategori']??'') ?>">
                                    <input type="hidden" class="fi-level" name="skill_level[]" value="<?= h($sk['level']??'Menengah') ?>">
                                    <input type="hidden" class="fi-logo" name="skill_logo[]" value="<?= h($sk['logo_icon']??'') ?>">
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="pb-split-right">
                        <div class="pb-card-title">Detail Keahlian</div>
                        <div class="pb-form-row">
                            <div class="pb-form-group">
                                <label>Nama Keahlian / Software</label>
                                <input type="text" class="pb-input" id="active_skill_nama" oninput="syncActiveSkill()">
                            </div>
                            <div class="pb-form-group">
                                <label>Icon Logo (opsional, cth: fa-brands fa-python)</label>
                                <input type="text" class="pb-input" id="active_skill_logo" oninput="syncActiveSkill()">
                            </div>
                        </div>
                        <div class="pb-form-row">
                            <div class="pb-form-group">
                                <label>Tingkat Keahlian</label>
                                <select class="pb-input" id="active_skill_level" onchange="syncActiveSkill()">
                                    <option value="Ahli">Ahli (Expert)</option>
                                    <option value="Menengah">Menengah (Intermediate)</option>
                                    <option value="Pemula">Pemula (Beginner)</option>
                                </select>
                            </div>
                            <div class="pb-form-group">
                                <label>Kategori Keahlian</label>
                                <select class="pb-input" id="active_skill_kategori" onchange="syncActiveSkill()">
                                    <option value="Desain UI/UX">Desain UI/UX</option>
                                    <option value="Pemrograman Web">Pemrograman Web</option>
                                    <option value="Tools & Software">Tools & Software</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER BAR -->
            <div class="pb-save-bar">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;color:#e2e8f0;font-weight:500;">
                    <input type="checkbox" name="pd_tampil_publik" value="1" <?= (!empty($ident_d['tampil_publik'])&&$ident_d['tampil_publik']==1)?'checked':'' ?>>
                    Tampilkan Profil di Halaman Depan Publik (Talent Directory)
                </label>
                <div style="display:flex;gap:16px;">
                    <button type="submit" class="btn-save-changes"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="pb-card" style="margin-top: 32px; border-left: 4px solid #6366f1;">
    <div class="pb-card-header">
        <h2 class="pb-card-title"><i class="fa-solid fa-bolt" style="color: #6366f1;"></i> Aktivitas Log</h2>
    </div>
    <div class="pb-card-body">
        <?php if(empty($recent_activity)){?>
        <div class="empty-activity" style="padding: 32px; text-align: center; color: #94a3b8;"><i class="fa-solid fa-mug-hot" style="font-size:2rem;margin-bottom:12px;opacity:0.5;"></i><br>Belum ada aktivitas.</div>
        <?php }else{?>
        <div class="timeline-list" style="display:flex;flex-direction:column;gap:16px;">
            <?php foreach($recent_activity as $ra){
                $al=strtolower($ra['action']??'');
                $bc=str_contains($al,'login')?'login':(str_contains($al,'upload')?'upload':(str_contains($al,'delete')?'delete':'other'));
                $ico=str_contains($al,'login')?'fa-right-to-bracket':(str_contains($al,'upload')?'fa-cloud-arrow-up':(str_contains($al,'delete')?'fa-trash':'fa-clock'));
                
                $ico_col = '#94a3b8'; $bg_col = 'rgba(255,255,255,0.05)';
                if(str_contains($al,'login')) { $ico_col = '#10b981'; $bg_col = 'rgba(16,185,129,0.1)'; }
                elseif(str_contains($al,'upload')) { $ico_col = '#3b82f6'; $bg_col = 'rgba(59,130,246,0.1)'; }
                elseif(str_contains($al,'delete')) { $ico_col = '#ef4444'; $bg_col = 'rgba(239,68,68,0.1)'; }
                
                echo "<div class='timeline-item' style='display:flex;align-items:flex-start;gap:16px;padding:16px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);border-radius:12px;'>";
                echo "<div class='tl-icon' style='width:40px;height:40px;border-radius:50%;background:$bg_col;color:$ico_col;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;'><i class='fa-solid $ico'></i></div>";
                echo "<div class='tl-content' style='flex:1;'>";
                echo "<div class='tl-title' style='color:#fff;font-weight:600;font-size:0.95rem;margin-bottom:4px;'>".h($ra['action'])."</div>";
                echo "<div class='tl-desc' style='color:#cbd5e1;font-size:0.85rem;margin-bottom:8px;'>".h($ra['details']??'-')."</div>";
                echo "<div class='tl-meta' style='color:#64748b;font-size:0.75rem;display:flex;gap:12px;'><span title='Waktu'><i class='fa-regular fa-clock'></i> ".date('d M Y, H:i',strtotime($ra['created_at']))."</span> <span title='IP Address'><i class='fa-solid fa-network-wired'></i> ".h($ra['ip_address']??'-')."</span></div>";
                echo "</div></div>";
            }?>
        </div>
        <?php }?>
    </div>
</div>

<script>
function switchPbTab(tabId, btn) {
    document.querySelectorAll('.pb-tab-content').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + tabId).style.display = 'block';
    document.querySelectorAll('.pb-tab').forEach(el => el.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('profileTabInput').value = tabId;
}

function activateSplitItem(item, type) {
    const list = item.parentElement;
    list.querySelectorAll('.pb-list-item').forEach(el => el.classList.remove('active'));
    item.classList.add('active');
    
    if(type === 'edu') {
        document.getElementById('active_edu_institusi').value = item.querySelector('.fi-institusi').value;
        document.getElementById('active_edu_gelar').value = item.querySelector('.fi-gelar').value;
        document.getElementById('active_edu_mulai').value = item.querySelector('.fi-mulai').value;
        document.getElementById('active_edu_selesai').value = item.querySelector('.fi-selesai').value;
        document.getElementById('active_edu_current').checked = (item.querySelector('.fi-current').value === '1');
        document.getElementById('active_edu_ipk').value = item.querySelector('.fi-ipk').value;
        document.getElementById('active_edu_desc').value = item.querySelector('.fi-desc').value;
    } else if(type === 'exp') {
        document.getElementById('active_exp_jabatan').value = item.querySelector('.fi-jabatan').value;
        document.getElementById('active_exp_perusahaan').value = item.querySelector('.fi-perusahaan').value;
        document.getElementById('active_exp_mulai').value = item.querySelector('.fi-mulai').value;
        document.getElementById('active_exp_selesai').value = item.querySelector('.fi-selesai').value;
        document.getElementById('active_exp_current').checked = (item.querySelector('.fi-current').value === '1');
        document.getElementById('active_exp_desc').value = item.querySelector('.fi-desc').value;
    } else if(type === 'skill') {
        document.getElementById('active_skill_nama').value = item.querySelector('.fi-nama').value;
        document.getElementById('active_skill_logo').value = item.querySelector('.fi-logo').value;
        
        let kat = item.querySelector('.fi-kategori').value;
        let dKat = document.getElementById('active_skill_kategori');
        if([...dKat.options].some(o => o.value === kat)) dKat.value = kat; else dKat.value = 'Lainnya';
        
        let lv = item.querySelector('.fi-level').value || 'Menengah';
        let dLv = document.getElementById('active_skill_level');
        dLv.value = lv;
    }
}

function syncActiveEdu() {
    const act = document.querySelector('#edu-list .pb-list-item.active');
    if(!act) return;
    act.querySelector('.fi-institusi').value = document.getElementById('active_edu_institusi').value;
    act.querySelector('.fi-gelar').value = document.getElementById('active_edu_gelar').value;
    act.querySelector('.fi-mulai').value = document.getElementById('active_edu_mulai').value;
    act.querySelector('.fi-selesai').value = document.getElementById('active_edu_selesai').value;
    act.querySelector('.fi-ipk').value = document.getElementById('active_edu_ipk').value;
    act.querySelector('.fi-desc').value = document.getElementById('active_edu_desc').value;
    
    let isCurr = document.getElementById('active_edu_current').checked;
    act.querySelector('.fi-current').value = isCurr ? '1' : '0';
    let cb = act.querySelector('input[type="checkbox"]');
    if(cb) cb.checked = isCurr;
    
    act.querySelector('.pb-list-item-title').innerHTML = '<i class="fa-solid fa-graduation-cap"></i> ' + (document.getElementById('active_edu_institusi').value || 'Institusi Baru');
    act.querySelector('.pb-list-item-sub').innerText = document.getElementById('active_edu_gelar').value || '-';
    act.querySelector('.v-mulai').innerText = document.getElementById('active_edu_mulai').value || '-';
    act.querySelector('.v-selesai').innerText = document.getElementById('active_edu_selesai').value || '-';
}

function syncActiveExp() {
    const act = document.querySelector('#exp-list .pb-list-item.active');
    if(!act) return;
    act.querySelector('.fi-jabatan').value = document.getElementById('active_exp_jabatan').value;
    act.querySelector('.fi-perusahaan').value = document.getElementById('active_exp_perusahaan').value;
    act.querySelector('.fi-mulai').value = document.getElementById('active_exp_mulai').value;
    act.querySelector('.fi-selesai').value = document.getElementById('active_exp_selesai').value;
    act.querySelector('.fi-desc').value = document.getElementById('active_exp_desc').value;
    
    let isCurr = document.getElementById('active_exp_current').checked;
    act.querySelector('.fi-current').value = isCurr ? '1' : '0';
    let cb = act.querySelector('input[type="checkbox"]');
    if(cb) cb.checked = isCurr;
    
    act.querySelector('.pb-list-item-title').innerHTML = '<i class="fa-solid fa-briefcase"></i> ' + (document.getElementById('active_exp_perusahaan').value || 'Perusahaan Baru');
    act.querySelector('.v-jabatan').innerText = document.getElementById('active_exp_jabatan').value || '-';
    act.querySelector('.v-mulai').innerText = document.getElementById('active_exp_mulai').value || '-';
    act.querySelector('.v-selesai').innerText = document.getElementById('active_exp_selesai').value || '-';
}

function syncActiveSkill() {
    const act = document.querySelector('#skill-list .pb-list-item.active');
    if(!act) return;
    act.querySelector('.fi-nama').value = document.getElementById('active_skill_nama').value;
    act.querySelector('.fi-kategori').value = document.getElementById('active_skill_kategori').value;
    act.querySelector('.fi-level').value = document.getElementById('active_skill_level').value;
    act.querySelector('.fi-logo').value = document.getElementById('active_skill_logo').value;
    
    let ic = document.getElementById('active_skill_logo').value || 'fa-solid fa-code';
    act.querySelector('.pb-list-item-title').innerHTML = `<i class="${ic}"></i> ` + (document.getElementById('active_skill_nama').value || 'Keahlian Baru');
    act.querySelector('.pb-list-item-sub').innerText = 'Kategori: ' + (document.getElementById('active_skill_kategori').value || 'Lainnya');
}

let edu_counter = 1000;
function addSplitItem(type) {
    const list = document.getElementById(type + '-list');
    const div = document.createElement('div');
    div.className = 'dyn-item pb-list-item active';
    div.setAttribute('onclick', `activateSplitItem(this, '${type}')`);
    list.querySelectorAll('.pb-list-item').forEach(el => el.classList.remove('active'));
    
    if(type === 'edu') {
        edu_counter++;
        div.innerHTML = `
            <div class="pb-list-item-content">
                <div class="pb-list-item-title"><i class="fa-solid fa-graduation-cap"></i> Institusi Baru</div>
                <div class="pb-list-item-sub">-</div>
                <div class="pb-list-item-sub" style="font-size:0.75rem;margin-top:2px;opacity:0.7;"><span class="v-mulai">-</span> - <span class="v-selesai">-</span></div>
            </div>
            <div class="pb-list-item-actions">
                <button type="button" class="pb-btn-action edit">Edit</button>
                <button type="button" class="pb-btn-action danger" onclick="event.stopPropagation();this.closest('.dyn-item').remove()">Hapus</button>
            </div>
            <div class="dyn-body" style="display:none;">
                <input type="hidden" class="fi-institusi" name="edu_institusi[]" value="">
                <input type="hidden" class="fi-gelar" name="edu_gelar[]" value="">
                <input type="hidden" class="fi-mulai" name="edu_mulai[]" value="">
                <input type="hidden" class="fi-selesai" name="edu_selesai[]" value="">
                <input type="hidden" class="fi-current" name="edu_is_current_tmp[]" value="0">
                <input type="checkbox" style="display:none;" name="edu_is_current[${edu_counter}]">
                <input type="hidden" class="fi-ipk" name="edu_ipk[]" value="">
                <textarea class="fi-desc" name="edu_desc[]"></textarea>
            </div>
        `;
    } else if(type === 'exp') {
        edu_counter++;
        div.innerHTML = `
            <div class="pb-list-item-content">
                <div class="pb-list-item-title"><i class="fa-solid fa-briefcase"></i> Perusahaan Baru</div>
                <div class="pb-list-item-sub">Posisi: <span class="v-jabatan">-</span></div>
                <div class="pb-list-item-sub" style="font-size:0.75rem;margin-top:2px;opacity:0.7;"><span class="v-mulai">-</span> - <span class="v-selesai">-</span></div>
            </div>
            <div class="pb-list-item-actions">
                <button type="button" class="pb-btn-action edit">Edit</button>
                <button type="button" class="pb-btn-action danger" onclick="event.stopPropagation();this.closest('.dyn-item').remove()">Hapus</button>
            </div>
            <div class="dyn-body" style="display:none;">
                <input type="hidden" class="fi-jabatan" name="exp_jabatan[]" value="">
                <input type="hidden" class="fi-perusahaan" name="exp_perusahaan[]" value="">
                <input type="hidden" class="fi-mulai" name="exp_mulai[]" value="">
                <input type="hidden" class="fi-selesai" name="exp_selesai[]" value="">
                <input type="hidden" class="fi-current" name="exp_is_current_tmp[]" value="0">
                <input type="checkbox" style="display:none;" name="exp_is_current[${edu_counter}]">
                <textarea class="fi-desc" name="exp_desc[]"></textarea>
            </div>
        `;
    } else if(type === 'skill') {
        div.innerHTML = `
            <div class="pb-list-item-content">
                <div class="pb-list-item-title"><i class="fa-solid fa-code"></i> Keahlian Baru</div>
                <div class="pb-list-item-sub">Kategori: Lainnya</div>
            </div>
            <div class="pb-list-item-actions">
                <button type="button" class="pb-btn-action edit">Edit</button>
                <button type="button" class="pb-btn-action danger" onclick="event.stopPropagation();this.closest('.dyn-item').remove()">Hapus</button>
            </div>
            <div class="dyn-body" style="display:none;">
                <input type="hidden" class="fi-nama" name="skill_nama[]" value="">
                <input type="hidden" class="fi-kategori" name="skill_kategori[]" value="Lainnya">
                <input type="hidden" class="fi-level" name="skill_level[]" value="Menengah">
                <input type="hidden" class="fi-logo" name="skill_logo[]" value="fa-solid fa-code">
            </div>
        `;
    }
    
    list.appendChild(div);
    activateSplitItem(div, type);
}

document.addEventListener('DOMContentLoaded', () => {
    let firstEdu = document.querySelector('#edu-list .pb-list-item.active');
    if(firstEdu) activateSplitItem(firstEdu, 'edu');
    
    let firstExp = document.querySelector('#exp-list .pb-list-item.active');
    if(firstExp) activateSplitItem(firstExp, 'exp');
    
    let firstSkill = document.querySelector('#skill-list .pb-list-item.active');
    if(firstSkill) activateSplitItem(firstSkill, 'skill');
});

function toggleProfesi(val) {
    const sel = document.getElementById('profesiSelect');
    const inp = document.getElementById('profesiInput');
    if(val === 'Lainnya') {
        sel.removeAttribute('name');
        inp.setAttribute('name', 'pd_profesi');
        inp.style.display = 'block';
        inp.focus();
    } else {
        inp.removeAttribute('name');
        sel.setAttribute('name', 'pd_profesi');
        inp.style.display = 'none';
        inp.value = '';
    }
}
const wilApi = 'https://www.emsifa.com/api-wilayah-indonesia/api/';

async function fetchWilayah(type, id) {
    let url = '';
    if(type === 'provinsi') url = wilApi + 'provinces.json';
    else if(type === 'kabupaten') url = wilApi + `regencies/${id}.json`;
    else if(type === 'kecamatan') url = wilApi + `districts/${id}.json`;
    else if(type === 'desa') url = wilApi + `villages/${id}.json`;
    
    try {
        const r = await fetch(url);
        return await r.json();
    } catch(e) {
        console.error(e);
        return [];
    }
}

async function populateSelect(selectId, type, parentId, savedValue) {
    const sel = document.getElementById(selectId);
    sel.innerHTML = '<option value="">Memuat...</option>';
    sel.disabled = true;
    
    const data = await fetchWilayah(type, parentId);
    sel.innerHTML = '<option value="">Pilih ' + type.charAt(0).toUpperCase() + type.slice(1) + '</option>';
    
    let matchedId = null;
    data.forEach(item => {
        const opt = document.createElement('option');
        opt.value = item.id;
        opt.dataset.name = item.name;
        opt.textContent = item.name;
        if(savedValue && item.name.toUpperCase() === savedValue.toUpperCase()) {
            opt.selected = true;
            matchedId = item.id;
        }
        sel.appendChild(opt);
    });
    sel.disabled = false;
    return matchedId;
}

function updateVal(type, selEl) {
    const valEl = document.getElementById('val' + type.charAt(0).toUpperCase() + type.slice(1));
    if(selEl.selectedIndex > 0) {
        valEl.value = selEl.options[selEl.selectedIndex].dataset.name;
    } else {
        valEl.value = '';
    }
}

async function loadWilayah(type, parentSel) {
    const parentNameType = parentSel.id.replace('sel', '').toLowerCase(); 
    updateVal(parentNameType, parentSel);
    
    const parentId = parentSel.value;
    
    if(type === 'kabupaten') {
        document.getElementById('selKecamatan').innerHTML = '<option value="">Pilih Kabupaten Dahulu</option>';
        document.getElementById('selKecamatan').disabled = true;
        document.getElementById('selDesa').innerHTML = '<option value="">Pilih Kecamatan Dahulu</option>';
        document.getElementById('selDesa').disabled = true;
        document.getElementById('valKabupaten').value = '';
        document.getElementById('valKecamatan').value = '';
        document.getElementById('valDesa').value = '';
        if(parentId) await populateSelect('selKabupaten', 'kabupaten', parentId, '');
        else {
            document.getElementById('selKabupaten').innerHTML = '<option value="">Pilih Provinsi Dahulu</option>';
        }
    } else if(type === 'kecamatan') {
        document.getElementById('selDesa').innerHTML = '<option value="">Pilih Kecamatan Dahulu</option>';
        document.getElementById('selDesa').disabled = true;
        document.getElementById('valKecamatan').value = '';
        document.getElementById('valDesa').value = '';
        if(parentId) await populateSelect('selKecamatan', 'kecamatan', parentId, '');
        else {
            document.getElementById('selKecamatan').innerHTML = '<option value="">Pilih Kabupaten Dahulu</option>';
        }
    } else if(type === 'desa') {
        document.getElementById('valDesa').value = '';
        if(parentId) await populateSelect('selDesa', 'desa', parentId, '');
        else {
            document.getElementById('selDesa').innerHTML = '<option value="">Pilih Kecamatan Dahulu</option>';
        }
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    const sProv = document.getElementById('valProvinsi').value;
    const sKab = document.getElementById('valKabupaten').value;
    const sKec = document.getElementById('valKecamatan').value;
    const sDesa = document.getElementById('valDesa').value;
    
    const provId = await populateSelect('selProvinsi', 'provinsi', null, sProv);
    if(provId) {
        const kabId = await populateSelect('selKabupaten', 'kabupaten', provId, sKab);
        if(kabId) {
            const kecId = await populateSelect('selKecamatan', 'kecamatan', kabId, sKec);
            if(kecId) {
                await populateSelect('selDesa', 'desa', kecId, sDesa);
            }
        }
    }
});
</script>
