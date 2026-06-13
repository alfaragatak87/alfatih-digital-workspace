<?php
/**
 * views/components/onboarding_modal.php
 * ================================================================
 * MODAL ONBOARDING — Wizard 4 Langkah untuk User Baru
 *
 * Dipanggil dari index.php HANYA jika: $data_user['is_onboarded'] == 0
 *
 * Langkah:
 *   Step 1 – Sambutan & pilih profesi/kategori pekerjaan
 *   Step 2 – Upload foto profil + nama sebutan
 *   Step 3 – Isi keahlian utama (pilih dari preset, cepat)
 *   Step 4 – Selesai + CTA ke dashboard
 *
 * Variabel PHP yang dibutuhkan dari index.php:
 *   $csrf_token, $nama_lengkap, $username, $path_foto
 * ================================================================
 */
?>

<!-- ── ONBOARDING OVERLAY ──────────────────────────────────── -->
<div id="onboardingOverlay" class="ob-overlay" role="dialog" aria-modal="true" aria-label="Selamat datang di Workspace">

    <!-- Backdrop blur layer -->
    <div class="ob-backdrop"></div>

    <!-- Modal card -->
    <div class="ob-card" id="obCard">

        <!-- Progress bar top -->
        <div class="ob-progress-track">
            <div class="ob-progress-fill" id="obProgressFill"></div>
        </div>

        <!-- Step counter -->
        <div class="ob-step-counter" id="obStepCounter">
            <span class="ob-step-dot active" data-step="1"></span>
            <span class="ob-step-dot" data-step="2"></span>
            <span class="ob-step-dot" data-step="3"></span>
            <span class="ob-step-dot" data-step="4"></span>
        </div>

        <!-- ══════════════════════════════════════════════════
             STEP 1 — SAMBUTAN & PILIH PROFESI
             ══════════════════════════════════════════════════ -->
        <div class="ob-step active" id="ob-step-1">
            <div class="ob-step-icon">👋</div>
            <h2 class="ob-title">Halo, <span class="ob-name"><?= h(explode(' ', $nama_lengkap)[0]) ?></span>!</h2>
            <p class="ob-desc">
                Selamat datang di <strong>Alfatih Workspace</strong>.<br>
                Kami akan bantu kamu setup profil dalam <strong>2 menit</strong>.
                <br><br>
                Pertama — kamu lebih tepat disebut sebagai?
            </p>

            <!-- Grid profesi -->
            <div class="ob-profesi-grid" id="obProfesiGrid">
                <?php
                $profesi_list = [
                    ['key'=>'it',         'emoji'=>'💻', 'label'=>'IT / Developer',     'sub'=>'Web, Mobile, Data'],
                    ['key'=>'kreatif',    'emoji'=>'🎨', 'label'=>'Kreatif & Desain',    'sub'=>'Fotografer, Designer, Konten'],
                    ['key'=>'pendidikan', 'emoji'=>'📚', 'label'=>'Pendidikan',           'sub'=>'Guru, Dosen, Trainer'],
                    ['key'=>'bisnis',     'emoji'=>'💼', 'label'=>'Bisnis & Usaha',       'sub'=>'Wiraswasta, Marketing, Sales'],
                    ['key'=>'kesehatan',  'emoji'=>'🏥', 'label'=>'Kesehatan',            'sub'=>'Dokter, Perawat, Apoteker'],
                    ['key'=>'hukum',      'emoji'=>'⚖️', 'label'=>'Hukum & Pemerintahan','sub'=>'Pengacara, ASN, Notaris'],
                    ['key'=>'teknik',     'emoji'=>'⚙️', 'label'=>'Teknik & Produksi',   'sub'=>'Sipil, Mesin, Listrik'],
                    ['key'=>'lainnya',    'emoji'=>'✨', 'label'=>'Lainnya',              'sub'=>'Apapun bidangmu'],
                ];
                foreach($profesi_list as $p): ?>
                <button class="ob-profesi-btn" data-key="<?= $p['key'] ?>" onclick="obSelectProfesi(this)">
                    <span class="ob-profesi-emoji"><?= $p['emoji'] ?></span>
                    <span class="ob-profesi-label"><?= h($p['label']) ?></span>
                    <span class="ob-profesi-sub"><?= h($p['sub']) ?></span>
                </button>
                <?php endforeach; ?>
            </div>

            <!-- Profesi bebas (custom) -->
            <div class="ob-custom-profesi" id="obCustomProfesiWrap" style="display:none;">
                <input
                    type="text"
                    id="obCustomProfesiInput"
                    class="ob-input"
                    placeholder="Ketik profesimu, mis: Fotografer Pernikahan"
                    maxlength="80"
                >
            </div>

            <input type="hidden" id="obSelectedProfesiKey" value="">

            <button class="ob-btn-next" id="obStep1Next" onclick="obGoStep(2)" disabled>
                Lanjut <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>


        <!-- ══════════════════════════════════════════════════
             STEP 2 — FOTO PROFIL & NAMA PANGGILAN
             ══════════════════════════════════════════════════ -->
        <div class="ob-step" id="ob-step-2">
            <div class="ob-step-icon">🪪</div>
            <h2 class="ob-title">Perkenalkan dirimu</h2>
            <p class="ob-desc">Foto profil dan nama panggilan membuat profilmu lebih personal dan mudah dikenali.</p>

            <!-- Foto profil upload -->
            <div class="ob-foto-wrap">
                <div class="ob-foto-preview" id="obFotoPreview" onclick="document.getElementById('obFotoInput').click()">
                    <img id="obFotoImg" src="<?= h($path_foto) ?>" alt="Foto Profil">
                    <div class="ob-foto-overlay">
                        <i class="fa-solid fa-camera"></i>
                        <span>Ganti foto</span>
                    </div>
                </div>
                <input type="file" id="obFotoInput" accept="image/*" style="display:none;" onchange="obPreviewFoto(this)">
                <p class="ob-foto-hint">JPG, PNG, WebP &mdash; maks. 3 MB</p>
            </div>

            <!-- Nama panggilan -->
            <div class="ob-field">
                <label class="ob-label">Nama Panggilanmu <span class="ob-required">*</span></label>
                <input
                    type="text"
                    id="obNamaSebutan"
                    class="ob-input"
                    placeholder='cth: "Andi", "Bu Sari", "Kang Wahyu"'
                    maxlength="50"
                    oninput="obCheckStep2()"
                >
                <span class="ob-hint">Nama ini akan tampil di greeting dashboard</span>
            </div>

            <!-- Tagline singkat -->
            <div class="ob-field">
                <label class="ob-label">Tagline Singkat <span class="ob-optional">(opsional)</span></label>
                <input
                    type="text"
                    id="obTagline"
                    class="ob-input"
                    placeholder='cth: "Senang berbagi ilmu" atau "Siap membantu bisnis Anda"'
                    maxlength="100"
                >
            </div>

            <div class="ob-nav">
                <button class="ob-btn-back" onclick="obGoStep(1)">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </button>
                <button class="ob-btn-next" id="obStep2Next" onclick="obGoStep(3)" disabled>
                    Lanjut <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>


        <!-- ══════════════════════════════════════════════════
             STEP 3 — PILIH KEAHLIAN UTAMA (Quick Pick)
             ══════════════════════════════════════════════════ -->
        <div class="ob-step" id="ob-step-3">
            <div class="ob-step-icon">⚡</div>
            <h2 class="ob-title">Apa keahlian utamamu?</h2>
            <p class="ob-desc">Pilih beberapa yang paling relevan. Kamu bisa tambah lebih banyak nanti di CV Builder.</p>

            <!-- Search skill -->
            <div class="ob-skill-search-wrap">
                <i class="fa-solid fa-magnifying-glass ob-skill-search-icon"></i>
                <input
                    type="text"
                    id="obSkillSearch"
                    class="ob-skill-search"
                    placeholder="Cari skill, mis: Excel, Photoshop, Laravel..."
                    oninput="obFilterSkills(this.value)"
                    autocomplete="off"
                >
            </div>

            <!-- Selected pills -->
            <div class="ob-selected-skills" id="obSelectedSkills">
                <span class="ob-selected-label">Dipilih:</span>
                <div id="obSelectedSkillPills" class="ob-skill-pills"></div>
            </div>

            <!-- Skill chips grid (dinamis via JS dari data preset) -->
            <div class="ob-skill-grid" id="obSkillGrid">
                <!-- Di-render oleh JS: obRenderSkillGrid() -->
            </div>

            <div class="ob-nav">
                <button class="ob-btn-back" onclick="obGoStep(2)">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </button>
                <button class="ob-btn-next" onclick="obGoStep(4)">
                    <?php /* Step 3 bisa di-skip */ ?>
                    Lanjut <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>


        <!-- ══════════════════════════════════════════════════
             STEP 4 — SELESAI & SIMPAN
             ══════════════════════════════════════════════════ -->
        <div class="ob-step" id="ob-step-4">
            <div class="ob-finish-animation" id="obFinishAnim">
                <div class="ob-finish-circle">
                    <i class="fa-solid fa-check"></i>
                </div>
            </div>
            <h2 class="ob-title">Semua siap! 🎉</h2>
            <p class="ob-desc">
                Profil dasarmu sudah terbentuk.<br>
                Yuk mulai eksplorasi Workspace!
            </p>

            <!-- Summary card -->
            <div class="ob-summary-card" id="obSummaryCard">
                <div class="ob-summary-foto">
                    <img id="obSummaryImg" src="<?= h($path_foto) ?>" alt="">
                </div>
                <div class="ob-summary-info">
                    <div class="ob-summary-name" id="obSummaryName"><?= h(explode(' ',$nama_lengkap)[0]) ?></div>
                    <div class="ob-summary-profesi" id="obSummaryProfesi">&mdash;</div>
                    <div class="ob-summary-skills" id="obSummarySkills"></div>
                </div>
            </div>

            <!-- CTA buttons -->
            <div class="ob-finish-actions">
                <button class="ob-btn-finish ob-btn-primary" id="obFinishBtn" onclick="obSubmitAndFinish()">
                    <i class="fa-solid fa-rocket"></i> Mulai Gunakan Workspace
                </button>
                <a href="index.php?page=profile" class="ob-btn-finish ob-btn-ghost" onclick="obMarkDone()">
                    <i class="fa-solid fa-id-card"></i> Lengkapi CV Dulu
                </a>
            </div>
        </div>

    </div><!-- end .ob-card -->
</div><!-- end #onboardingOverlay -->


<!-- ── HIDDEN FORM (submit onboarding ke PHP) ─────────────── -->
<form id="obForm" method="POST" enctype="multipart/form-data" style="display:none;">
    <input type="hidden" name="action"         value="save_onboarding">
    <input type="hidden" name="csrf_token"     value="<?= $csrf_token ?>">
    <input type="hidden" name="ob_profesi_key" id="obFormProfesiKey">
    <input type="hidden" name="ob_profesi_custom" id="obFormProfesiCustom">
    <input type="hidden" name="ob_nama_sebutan"   id="obFormNamaSebutan">
    <input type="hidden" name="ob_tagline"        id="obFormTagline">
    <input type="hidden" name="ob_skills"         id="obFormSkills">   <!-- JSON string -->
    <!-- File upload disalin via JS ke FormData sebelum submit -->
</form>


<!-- ════════════════════════════════════════════════════════════
     CSS — Onboarding Modal
     ════════════════════════════════════════════════════════════ -->
<style>
/* ── Variables reuse dari dashboard.css ────────────────────── */
:root {
    --ob-radius: 24px;
    --ob-card-w: 560px;
    --ob-accent: #0a0a0a;
    --ob-accent-light: #f4f4f4;
    --ob-blue: #3b82f6;
    --ob-success: #22c55e;
    --ob-easing: cubic-bezier(.16,1,.3,1);
}

/* ── Overlay ────────────────────────────────────────────────── */
.ob-overlay {
    position: fixed;
    inset: 0;
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    animation: obFadeIn .3s ease both;
}
@keyframes obFadeIn { from { opacity:0; } to { opacity:1; } }

.ob-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.55);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

/* ── Card ────────────────────────────────────────────────────── */
.ob-card {
    position: relative;
    width: 100%;
    max-width: var(--ob-card-w);
    max-height: 92vh;
    overflow-y: auto;
    background: #fff;
    border-radius: var(--ob-radius);
    box-shadow: 0 32px 80px rgba(0,0,0,.25), 0 8px 24px rgba(0,0,0,.12);
    padding: 0 0 40px;
    animation: obCardIn .45s var(--ob-easing) both;
    scrollbar-width: none;
}
.ob-card::-webkit-scrollbar { display:none; }
@keyframes obCardIn {
    from { opacity:0; transform:scale(.95) translateY(20px); }
    to   { opacity:1; transform:none; }
}

/* ── Progress bar ────────────────────────────────────────────── */
.ob-progress-track {
    height: 4px;
    background: #f0f0f0;
    border-radius: var(--ob-radius) var(--ob-radius) 0 0;
    overflow: hidden;
}
.ob-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #0a0a0a, #444);
    width: 25%;
    transition: width .5s var(--ob-easing);
    border-radius: 0 4px 4px 0;
}

/* ── Step dots ───────────────────────────────────────────────── */
.ob-step-counter {
    display: flex;
    justify-content: center;
    gap: 8px;
    padding: 20px 0 4px;
}
.ob-step-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #e5e5e5;
    transition: background .3s, transform .3s;
    display: block;
}
.ob-step-dot.active {
    background: var(--ob-accent);
    transform: scale(1.25);
}
.ob-step-dot.done {
    background: #22c55e;
}

/* ── Step panels ─────────────────────────────────────────────── */
.ob-step {
    display: none;
    padding: 8px 40px 0;
    animation: obStepIn .35s var(--ob-easing) both;
    text-align: center;
}
.ob-step.active { display: block; }
@keyframes obStepIn {
    from { opacity:0; transform:translateY(14px); }
    to   { opacity:1; transform:none; }
}

.ob-step-icon {
    font-size: 3rem;
    margin-bottom: 14px;
    display: block;
    animation: obBounce .6s var(--ob-easing);
}
@keyframes obBounce {
    0%   { transform:scale(0) rotate(-20deg); }
    70%  { transform:scale(1.12) rotate(5deg); }
    100% { transform:scale(1) rotate(0); }
}

.ob-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.75rem;
    font-weight: 900;
    letter-spacing: -.5px;
    color: #0a0a0a;
    margin-bottom: 10px;
}
.ob-name { color: var(--ob-accent); }

.ob-desc {
    font-size: .9rem;
    color: #6b7280;
    line-height: 1.7;
    max-width: 420px;
    margin: 0 auto 24px;
}

/* ── Profesi grid ────────────────────────────────────────────── */
.ob-profesi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 20px;
    text-align: left;
}
.ob-profesi-btn {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
    padding: 14px 12px;
    background: #f9f9fb;
    border: 2px solid #e5e5e5;
    border-radius: 14px;
    cursor: pointer;
    transition: all .2s var(--ob-easing);
    font-family: 'Inter', sans-serif;
}
.ob-profesi-btn:hover {
    border-color: #d4d4d4;
    background: #f4f4f4;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
}
.ob-profesi-btn.selected {
    border-color: var(--ob-accent);
    background: var(--ob-accent);
    color: #fff;
    box-shadow: 0 6px 20px rgba(10,10,10,.2);
}
.ob-profesi-emoji {
    font-size: 1.5rem;
    line-height: 1;
    margin-bottom: 2px;
}
.ob-profesi-label {
    font-size: .78rem;
    font-weight: 700;
    letter-spacing: -.1px;
}
.ob-profesi-sub {
    font-size: .68rem;
    color: #9ca3af;
    line-height: 1.3;
}
.ob-profesi-btn.selected .ob-profesi-sub { color: rgba(255,255,255,.6); }

/* ── Buttons ─────────────────────────────────────────────────── */
.ob-btn-next {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 13px 32px;
    background: var(--ob-accent);
    color: #fff;
    border: none;
    border-radius: 50px;
    font-size: .84rem;
    font-weight: 700;
    letter-spacing: .3px;
    text-transform: uppercase;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    transition: all .25s var(--ob-easing);
    margin-top: 8px;
    box-shadow: 0 4px 16px rgba(10,10,10,.2);
    width: 100%;
    justify-content: center;
}
.ob-btn-next:hover:not(:disabled) {
    background: #222;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(10,10,10,.25);
}
.ob-btn-next:disabled {
    background: #d1d5db;
    color: #9ca3af;
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
}
.ob-nav {
    display: flex;
    gap: 12px;
    margin-top: 8px;
}
.ob-nav .ob-btn-next { flex: 1; margin-top: 0; }
.ob-btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 13px 20px;
    background: transparent;
    color: #6b7280;
    border: 2px solid #e5e5e5;
    border-radius: 50px;
    font-size: .84rem;
    font-weight: 700;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    transition: all .2s;
    white-space: nowrap;
}
.ob-btn-back:hover {
    border-color: #9ca3af;
    color: #0a0a0a;
}

/* ── Input fields ────────────────────────────────────────────── */
.ob-field {
    text-align: left;
    margin-bottom: 18px;
}
.ob-label {
    display: block;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .8px;
    text-transform: uppercase;
    color: #6b7280;
    margin-bottom: 6px;
}
.ob-required { color: #ef4444; }
.ob-optional { color: #d1d5db; font-weight: 400; text-transform: none; font-size: .75rem; }
.ob-input {
    width: 100%;
    padding: 12px 16px;
    background: #f9f9fb;
    border: 2px solid #e5e5e5;
    border-radius: 12px;
    color: #0a0a0a;
    font-size: .9rem;
    font-family: 'Inter', sans-serif;
    outline: none;
    transition: border-color .2s, background .2s, box-shadow .2s;
}
.ob-input:focus {
    border-color: #0a0a0a;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(10,10,10,.06);
}
.ob-input::placeholder { color: #c4c4c4; }
.ob-hint {
    font-size: .72rem;
    color: #9ca3af;
    margin-top: 4px;
    display: block;
}

/* ── Foto profil ─────────────────────────────────────────────── */
.ob-foto-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 24px;
}
.ob-foto-preview {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    overflow: hidden;
    position: relative;
    cursor: pointer;
    border: 3px solid #e5e5e5;
    transition: border-color .2s, transform .2s;
}
.ob-foto-preview:hover { border-color: #0a0a0a; transform: scale(1.04); }
.ob-foto-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.ob-foto-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.45);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    opacity: 0;
    transition: opacity .2s;
    color: #fff;
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
}
.ob-foto-overlay i { font-size: 1.4rem; }
.ob-foto-preview:hover .ob-foto-overlay { opacity: 1; }
.ob-foto-hint {
    font-size: .72rem;
    color: #9ca3af;
    margin-top: 8px;
}

/* ── Skill section ───────────────────────────────────────────── */
.ob-skill-search-wrap {
    position: relative;
    margin-bottom: 14px;
    text-align: left;
}
.ob-skill-search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: .85rem;
    pointer-events: none;
}
.ob-skill-search {
    width: 100%;
    padding: 11px 14px 11px 38px;
    background: #f9f9fb;
    border: 2px solid #e5e5e5;
    border-radius: 12px;
    font-size: .88rem;
    font-family: 'Inter', sans-serif;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.ob-skill-search:focus {
    border-color: #0a0a0a;
    box-shadow: 0 0 0 3px rgba(10,10,10,.06);
}
.ob-selected-skills {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 14px;
    min-height: 32px;
    text-align: left;
}
.ob-selected-label {
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .5px;
    text-transform: uppercase;
    color: #9ca3af;
    white-space: nowrap;
}
.ob-skill-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.ob-skill-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px 4px 12px;
    background: #0a0a0a;
    color: #fff;
    border-radius: 50px;
    font-size: .72rem;
    font-weight: 700;
    animation: obPillIn .2s var(--ob-easing);
}
@keyframes obPillIn { from {scale:.8;opacity:0} to {scale:1;opacity:1} }
.ob-skill-pill button {
    background: none;
    border: none;
    color: rgba(255,255,255,.7);
    cursor: pointer;
    font-size: .7rem;
    padding: 0 0 0 2px;
    line-height: 1;
    transition: color .15s;
}
.ob-skill-pill button:hover { color: #ef4444; }

.ob-skill-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    max-height: 240px;
    overflow-y: auto;
    padding: 2px 0 8px;
    text-align: left;
    scrollbar-width: thin;
}
.ob-skill-chip {
    padding: 7px 14px;
    border: 2px solid #e5e5e5;
    border-radius: 50px;
    font-size: .78rem;
    font-weight: 600;
    cursor: pointer;
    background: #fff;
    color: #374151;
    transition: all .2s var(--ob-easing);
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.ob-skill-chip:hover { border-color: #0a0a0a; background: #f9f9fb; }
.ob-skill-chip.selected {
    background: #0a0a0a;
    border-color: #0a0a0a;
    color: #fff;
}
.ob-skill-chip .ob-cat-badge {
    font-size: .6rem;
    padding: 2px 6px;
    background: rgba(255,255,255,.18);
    border-radius: 20px;
}

/* ── Step 4: Selesai ─────────────────────────────────────────── */
.ob-finish-animation {
    display: flex;
    justify-content: center;
    margin-bottom: 16px;
}
.ob-finish-circle {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #22c55e, #16a34a);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    color: #fff;
    animation: obCircleIn .6s var(--ob-easing) both;
    box-shadow: 0 8px 32px rgba(34,197,94,.35);
}
@keyframes obCircleIn {
    from { scale:.3; opacity:0; }
    70%  { scale:1.15; }
    100% { scale:1; opacity:1; }
}

.ob-summary-card {
    display: flex;
    align-items: center;
    gap: 16px;
    background: #f9f9fb;
    border: 1.5px solid #e5e5e5;
    border-radius: 16px;
    padding: 16px 20px;
    margin: 20px 0 24px;
    text-align: left;
}
.ob-summary-foto img {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e5e5e5;
}
.ob-summary-name {
    font-weight: 800;
    font-size: 1.05rem;
    font-family: 'Playfair Display', serif;
    margin-bottom: 2px;
}
.ob-summary-profesi {
    font-size: .78rem;
    color: #6b7280;
    margin-bottom: 6px;
}
.ob-summary-skills {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}
.ob-summary-skill-tag {
    font-size: .65rem;
    font-weight: 700;
    padding: 2px 8px;
    background: #f0f0f0;
    border-radius: 20px;
    color: #374151;
}

.ob-finish-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.ob-btn-finish {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 24px;
    border-radius: 50px;
    font-size: .84rem;
    font-weight: 700;
    letter-spacing: .3px;
    text-transform: uppercase;
    cursor: pointer;
    font-family: 'Inter', sans-serif;
    transition: all .25s var(--ob-easing);
    border: none;
    text-decoration: none;
}
.ob-btn-primary {
    background: linear-gradient(135deg, #0a0a0a, #1a1a2e);
    color: #fff;
    box-shadow: 0 6px 20px rgba(10,10,10,.25);
}
.ob-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(10,10,10,.3); }
.ob-btn-primary:active { transform: scale(.97); }
.ob-btn-ghost {
    background: transparent;
    color: #6b7280;
    border: 2px solid #e5e5e5;
}
.ob-btn-ghost:hover { border-color: #0a0a0a; color: #0a0a0a; }

/* Custom profesi input */
.ob-custom-profesi { margin-bottom: 16px; text-align:left; }

/* ── Responsive ──────────────────────────────────────────────── */
@media (max-width: 600px) {
    .ob-overlay { padding: 0; align-items: flex-end; }
    .ob-card {
        border-radius: var(--ob-radius) var(--ob-radius) 0 0;
        max-height: 92dvh;
        animation: obCardInMobile .4s var(--ob-easing) both;
    }
    @keyframes obCardInMobile {
        from { transform: translateY(100%); opacity:0; }
        to   { transform: none; opacity:1; }
    }
    .ob-step { padding: 8px 22px 0; }
    .ob-profesi-grid { grid-template-columns: repeat(2, 1fr); }
    .ob-title { font-size: 1.45rem; }
}
@media (max-width: 400px) {
    .ob-profesi-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
}
</style>


<!-- ════════════════════════════════════════════════════════════
     JAVASCRIPT — Onboarding Logic
     ════════════════════════════════════════════════════════════ -->
<script>
/* ─────────────────────────────────────────────────────────────
   DATA SKILL PRESET
   Digunakan untuk render chip skill di Step 3.
   Kamu bisa extend ini dari PHP: obSkillPresets = <?= json_encode($skill_presets_from_db ?? []) ?> || obSkillPresets;
   ───────────────────────────────────────────────────────────── */
const obSkillPresets = [
    // Microsoft Office
    { nama:'Microsoft Word',       kat:'Microsoft Office', icon:'📝', warna:'#2563eb' },
    { nama:'Microsoft Excel',      kat:'Microsoft Office', icon:'📊', warna:'#16a34a' },
    { nama:'Microsoft PowerPoint', kat:'Microsoft Office', icon:'📽️', warna:'#ea580c' },
    { nama:'Microsoft Outlook',    kat:'Microsoft Office', icon:'📧', warna:'#0891b2' },
    { nama:'Microsoft Teams',      kat:'Microsoft Office', icon:'💬', warna:'#7c3aed' },

    // Google Workspace
    { nama:'Google Docs',          kat:'Google Workspace', icon:'📄', warna:'#1a73e8' },
    { nama:'Google Sheets',        kat:'Google Workspace', icon:'📈', warna:'#34a853' },
    { nama:'Google Slides',        kat:'Google Workspace', icon:'🖥️', warna:'#fbbc04' },
    { nama:'Google Meet',          kat:'Google Workspace', icon:'📹', warna:'#00ac47' },

    // Desain Grafis
    { nama:'Adobe Photoshop',      kat:'Desain Grafis',    icon:'🎨', warna:'#31a8ff' },
    { nama:'Adobe Illustrator',    kat:'Desain Grafis',    icon:'✏️', warna:'#ff9a00' },
    { nama:'Canva',                kat:'Desain Grafis',    icon:'🖌️', warna:'#00c4cc' },
    { nama:'CorelDRAW',            kat:'Desain Grafis',    icon:'🖍️', warna:'#1db954' },
    { nama:'Adobe InDesign',       kat:'Desain Grafis',    icon:'📰', warna:'#ff3366' },

    // Fotografi & Video
    { nama:'Adobe Lightroom',      kat:'Foto & Video',     icon:'📸', warna:'#31a8ff' },
    { nama:'Adobe Premiere Pro',   kat:'Foto & Video',     icon:'🎬', warna:'#9999ff' },
    { nama:'DaVinci Resolve',      kat:'Foto & Video',     icon:'🎥', warna:'#ff6b00' },
    { nama:'CapCut',               kat:'Foto & Video',     icon:'📲', warna:'#000000' },

    // Akuntansi
    { nama:'Accurate Accounting',  kat:'Keuangan',         icon:'💰', warna:'#16a34a' },
    { nama:'Microsoft Excel (Akuntansi)', kat:'Keuangan',  icon:'📒', warna:'#16a34a' },
    { nama:'Jurnal (Mekari)',       kat:'Keuangan',         icon:'📓', warna:'#4f46e5' },

    // Komunikasi
    { nama:'Public Speaking',      kat:'Komunikasi',       icon:'🎤', warna:'#dc2626' },
    { nama:'Copywriting',          kat:'Komunikasi',       icon:'✍️', warna:'#7c3aed' },
    { nama:'Content Writing',      kat:'Komunikasi',       icon:'📝', warna:'#0891b2' },
    { nama:'Presentasi',           kat:'Komunikasi',       icon:'📊', warna:'#ea580c' },

    // Manajemen
    { nama:'Project Management',   kat:'Manajemen',        icon:'📋', warna:'#6b7280' },
    { nama:'Kepemimpinan',         kat:'Manajemen',        icon:'👑', warna:'#b45309' },
    { nama:'Analisis Data',        kat:'Manajemen',        icon:'📉', warna:'#0891b2' },

    // Frontend
    { nama:'HTML / CSS',           kat:'Frontend',         icon:'🌐', warna:'#e34f26' },
    { nama:'JavaScript',           kat:'Frontend',         icon:'⚡', warna:'#f7df1e' },
    { nama:'React',                kat:'Frontend',         icon:'⚛️', warna:'#61dafb' },
    { nama:'Vue.js',               kat:'Frontend',         icon:'🟢', warna:'#4fc08d' },
    { nama:'Next.js',              kat:'Frontend',         icon:'▲',  warna:'#000000' },
    { nama:'Tailwind CSS',         kat:'Frontend',         icon:'🎨', warna:'#38bdf8' },

    // Backend
    { nama:'PHP',                  kat:'Backend',          icon:'🐘', warna:'#8892bf' },
    { nama:'Laravel',              kat:'Backend',          icon:'🔴', warna:'#ff2d20' },
    { nama:'Node.js',              kat:'Backend',          icon:'🟩', warna:'#339933' },
    { nama:'Python',               kat:'Backend',          icon:'🐍', warna:'#3776ab' },
    { nama:'Express.js',           kat:'Backend',          icon:'🚂', warna:'#000000' },

    // Database
    { nama:'MySQL',                kat:'Database',         icon:'🐬', warna:'#4479a1' },
    { nama:'PostgreSQL',           kat:'Database',         icon:'🐘', warna:'#336791' },
    { nama:'MongoDB',              kat:'Database',         icon:'🍃', warna:'#47a248' },
    { nama:'Redis',                kat:'Database',         icon:'🔴', warna:'#dc382d' },

    // Mobile
    { nama:'Flutter',              kat:'Mobile',           icon:'📱', warna:'#54c5f8' },
    { nama:'React Native',         kat:'Mobile',           icon:'📲', warna:'#61dafb' },
    { nama:'Android (Kotlin)',     kat:'Mobile',           icon:'🤖', warna:'#7f52ff' },

    // UI/UX
    { nama:'Figma',                kat:'UI/UX',            icon:'🎭', warna:'#f24e1e' },
    { nama:'Adobe XD',             kat:'UI/UX',            icon:'🖌️', warna:'#ff61f6' },
    { nama:'Wireframing',          kat:'UI/UX',            icon:'📐', warna:'#6b7280' },

    // DevOps
    { nama:'Docker',               kat:'DevOps / CI-CD',   icon:'🐳', warna:'#2496ed' },
    { nama:'GitHub Actions',       kat:'DevOps / CI-CD',   icon:'⚙️', warna:'#24292e' },
    { nama:'Linux',                kat:'Infrastruktur',    icon:'🐧', warna:'#fcc624' },
    { nama:'AWS',                  kat:'Cloud',            icon:'☁️', warna:'#ff9900' },
    { nama:'Firebase',             kat:'Cloud',            icon:'🔥', warna:'#ffca28' },
];

/* ─────────────────────────────────────────────────────────────
   STATE
   ───────────────────────────────────────────────────────────── */
let obCurrentStep = 1;
let obSelectedProfesiKey = '';
let obSelectedSkills = [];  // array of { nama, kat }
let obFotoFile = null;

const OB_STEP_COUNT = 4;

/* ─────────────────────────────────────────────────────────────
   NAVIGASI ANTAR STEP
   ───────────────────────────────────────────────────────────── */
function obGoStep(n) {
    if (n < 1 || n > OB_STEP_COUNT) return;

    // Sembunyikan step lama, tampilkan yang baru
    document.getElementById(`ob-step-${obCurrentStep}`).classList.remove('active');
    obCurrentStep = n;
    document.getElementById(`ob-step-${n}`).classList.add('active');

    // Update progress bar
    document.getElementById('obProgressFill').style.width = (n / OB_STEP_COUNT * 100) + '%';

    // Update dots
    document.querySelectorAll('.ob-step-dot').forEach(dot => {
        const s = parseInt(dot.dataset.step);
        dot.classList.toggle('active', s === n);
        dot.classList.toggle('done',   s < n);
    });

    // Khusus step 4: update summary
    if (n === 4) obBuildSummary();

    // Scroll card ke atas
    document.getElementById('obCard').scrollTop = 0;
}

/* ─────────────────────────────────────────────────────────────
   STEP 1 — PILIH PROFESI
   ───────────────────────────────────────────────────────────── */
function obSelectProfesi(btn) {
    document.querySelectorAll('.ob-profesi-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    obSelectedProfesiKey = btn.dataset.key;

    // Tampilkan input custom jika pilih "Lainnya"
    const customWrap = document.getElementById('obCustomProfesiWrap');
    customWrap.style.display = (obSelectedProfesiKey === 'lainnya') ? 'block' : 'none';

    // Enable tombol next
    document.getElementById('obStep1Next').disabled = false;

    // Render ulang skill grid di step 3 berdasarkan profesi
    obRenderSkillGrid();
}

/* ─────────────────────────────────────────────────────────────
   STEP 2 — CEK INPUT WAJIB
   ───────────────────────────────────────────────────────────── */
function obCheckStep2() {
    const nama = document.getElementById('obNamaSebutan').value.trim();
    document.getElementById('obStep2Next').disabled = (nama.length < 2);
}

/* ─────────────────────────────────────────────────────────────
   STEP 2 — PREVIEW FOTO
   ───────────────────────────────────────────────────────────── */
function obPreviewFoto(input) {
    if (!input.files[0]) return;
    const file = input.files[0];
    if (file.size > 3 * 1024 * 1024) {
        alert('Ukuran foto terlalu besar (maks 3 MB)');
        return;
    }
    obFotoFile = file;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('obFotoImg').src     = e.target.result;
        document.getElementById('obSummaryImg').src  = e.target.result;
    };
    reader.readAsDataURL(file);
}

/* ─────────────────────────────────────────────────────────────
   STEP 3 — RENDER & FILTER SKILL GRID
   ───────────────────────────────────────────────────────────── */
// Prioritas skill berdasarkan profesi yang dipilih
const obProfesiSkillMap = {
    it:         ['Frontend','Backend','Database','Mobile','DevOps / CI-CD','Cloud','UI/UX'],
    kreatif:    ['Desain Grafis','Foto & Video','UI/UX','Komunikasi'],
    pendidikan: ['Microsoft Office','Google Workspace','Komunikasi','Manajemen'],
    bisnis:     ['Microsoft Office','Google Workspace','Keuangan','Manajemen','Komunikasi'],
    kesehatan:  ['Microsoft Office','Komunikasi','Manajemen'],
    hukum:      ['Microsoft Office','Komunikasi','Manajemen'],
    teknik:     ['Microsoft Office','Manajemen'],
    lainnya:    [],
};

function obRenderSkillGrid(filter = '') {
    const grid = document.getElementById('obSkillGrid');
    const priorityCats = obProfesiSkillMap[obSelectedProfesiKey] || [];
    const query = filter.toLowerCase().trim();

    // Sort: priority cats first, then rest
    const sorted = [...obSkillPresets].sort((a, b) => {
        const ai = priorityCats.indexOf(a.kat);
        const bi = priorityCats.indexOf(b.kat);
        if (ai === -1 && bi !== -1) return 1;
        if (ai !== -1 && bi === -1) return -1;
        if (ai !== bi) return ai - bi;
        return a.nama.localeCompare(b.nama);
    });

    const filtered = query
        ? sorted.filter(s => s.nama.toLowerCase().includes(query) || s.kat.toLowerCase().includes(query))
        : sorted;

    grid.innerHTML = filtered.map(s => {
        const isSelected = obSelectedSkills.some(x => x.nama === s.nama);
        return `<button
            class="ob-skill-chip ${isSelected ? 'selected' : ''}"
            onclick="obToggleSkill('${s.nama.replace(/'/g,"\\'")}', '${s.kat.replace(/'/g,"\\'")}', this)"
        >${s.icon} ${s.nama} <span class="ob-cat-badge">${s.kat}</span></button>`;
    }).join('');
}

function obFilterSkills(val) {
    obRenderSkillGrid(val);
}

function obToggleSkill(nama, kat, el) {
    const idx = obSelectedSkills.findIndex(s => s.nama === nama);
    if (idx === -1) {
        if (obSelectedSkills.length >= 12) return; // max 12
        obSelectedSkills.push({ nama, kat });
        el.classList.add('selected');
    } else {
        obSelectedSkills.splice(idx, 1);
        el.classList.remove('selected');
    }
    obRenderSelectedPills();
}

function obRenderSelectedPills() {
    const wrap = document.getElementById('obSelectedSkillPills');
    wrap.innerHTML = obSelectedSkills.map(s => `
        <span class="ob-skill-pill">
            ${s.nama}
            <button type="button" onclick="obRemoveSkill('${s.nama.replace(/'/g,"\\'")}')">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </span>
    `).join('');
}

function obRemoveSkill(nama) {
    obSelectedSkills = obSelectedSkills.filter(s => s.nama !== nama);
    obRenderSelectedPills();
    obRenderSkillGrid(document.getElementById('obSkillSearch').value);
}

/* ─────────────────────────────────────────────────────────────
   STEP 4 — BUILD SUMMARY
   ───────────────────────────────────────────────────────────── */
function obBuildSummary() {
    // Nama
    const nama = document.getElementById('obNamaSebutan').value.trim()
        || '<?= h(explode(' ',$nama_lengkap)[0]) ?>';
    document.getElementById('obSummaryName').textContent = nama;

    // Profesi
    const profesiBtn = document.querySelector('.ob-profesi-btn.selected');
    const profesiLabel = profesiBtn
        ? profesiBtn.querySelector('.ob-profesi-label').textContent
        : '—';
    document.getElementById('obSummaryProfesi').textContent = profesiLabel;

    // Skills
    const skillsEl = document.getElementById('obSummarySkills');
    skillsEl.innerHTML = obSelectedSkills.slice(0, 5).map(s =>
        `<span class="ob-summary-skill-tag">${s.nama}</span>`
    ).join('');
    if (obSelectedSkills.length > 5) {
        skillsEl.innerHTML += `<span class="ob-summary-skill-tag">+${obSelectedSkills.length - 5} lagi</span>`;
    }
}

/* ─────────────────────────────────────────────────────────────
   SUBMIT — Kirim data onboarding ke server
   ───────────────────────────────────────────────────────────── */
async function obSubmitAndFinish() {
    const btn = document.getElementById('obFinishBtn');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
    btn.disabled  = true;

    const fd = new FormData();
    fd.append('action',              'save_onboarding');
    fd.append('csrf_token',          '<?= $csrf_token ?>');
    fd.append('ob_profesi_key',      obSelectedProfesiKey);
    fd.append('ob_profesi_custom',   document.getElementById('obCustomProfesiInput').value.trim());
    fd.append('ob_nama_sebutan',     document.getElementById('obNamaSebutan').value.trim());
    fd.append('ob_tagline',          document.getElementById('obTagline').value.trim());
    fd.append('ob_skills',           JSON.stringify(obSelectedSkills));

    if (obFotoFile) {
        fd.append('foto_profil', obFotoFile, obFotoFile.name);
    }

    try {
        const res = await fetch('index.php', { method: 'POST', body: fd });
        // Sukses → tutup modal & reload halaman
        obMarkDone();
    } catch(e) {
        btn.innerHTML = '<i class="fa-solid fa-rocket"></i> Mulai Gunakan Workspace';
        btn.disabled = false;
        alert('Gagal menyimpan. Coba lagi.');
    }
}

function obMarkDone() {
    // Animasi keluar
    const overlay = document.getElementById('onboardingOverlay');
    overlay.style.transition = 'opacity .4s ease';
    overlay.style.opacity = '0';
    setTimeout(() => {
        overlay.remove();
        // Reload untuk apply perubahan profil
        window.location.href = 'index.php?page=beranda';
    }, 400);
}

/* ─────────────────────────────────────────────────────────────
   INIT
   ───────────────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    // Render skill grid awal (semua skill, tanpa filter profesi dulu)
    obRenderSkillGrid();
});
</script>
