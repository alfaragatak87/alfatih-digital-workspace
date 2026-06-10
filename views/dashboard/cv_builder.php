<?php if (!defined('SITE_URL')) exit; 
$active_tab = $_GET['tab'] ?? 'identitas';
$ident_d = $profile_data['identitas'] ?? [];
$edu_d   = $profile_data['pendidikan'] ?? [];
$exp_d   = $profile_data['pengalaman'] ?? [];
$skill_d = $profile_data['keahlian'] ?? [];
$porto_d = $profile_data['portfolio'] ?? [];
?>
<div class="page-header">
    <div class="page-header-left">
        <div class="page-eyebrow">CV &amp; Portfolio</div>
        <h1 class="page-title">Profile Builder</h1>
        <?php if(!empty($ident_d['profesi']) || !empty($ident_d['nama_sebutan'])){?>
        <div style="margin-top:6px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <?php if(!empty($ident_d['nama_sebutan'])){?><span style="font-size:.82rem;color:var(--text-muted);">Dipanggil: <strong style="color:var(--text-main);"><?= h($ident_d['nama_sebutan']) ?></strong></span><?php }?>
            <?php if(!empty($ident_d['profesi'])){?><span class="profesi-badge"><?= h($ident_d['profesi']) ?></span><?php }?>
        </div>
        <?php }?>
    </div>
    <div class="page-actions">
        <a href="<?= h($portfolio_url) ?>" target="_blank" class="btn-ghost"><i class="fa-solid fa-globe"></i> Lihat Portfolio</a>
    </div>
</div>

<div class="profile-inner">
    <div class="portfolio-link-box">
        <i class="fa-solid fa-link" style="color:var(--text-muted);flex-shrink:0;padding:0 16px;"></i>
        <input type="text" value="<?= h($portfolio_url) ?>" id="portfolioLinkInput" readonly>
        <button class="copy-btn" onclick="copyPortfolioLink()"><i class="fa-solid fa-copy"></i> Salin</button>
        <a href="<?= h($portfolio_url) ?>" target="_blank" class="copy-btn"><i class="fa-solid fa-arrow-up-right-from-square"></i> Buka</a>
    </div>
    <div class="tab-nav">
        <?php foreach([['identitas','fa-user','Identitas'],['pendidikan','fa-graduation-cap','Pendidikan'],['pengalaman','fa-briefcase','Pengalaman'],['keahlian','fa-code','Keahlian & Portfolio']] as [$tid,$tico,$tlbl]){?>
        <button class="tab-btn <?= $active_tab===$tid?'active':'' ?>" onclick="switchTab('<?= $tid ?>')"><i class="fa-solid <?= $tico ?>"></i> <?= $tlbl ?></button>
        <?php }?>
    </div>

    <div id="tab-identitas" class="tab-panel <?= $active_tab==='identitas'?'active':'' ?>">
        <form method="POST" id="form-identitas">
            <input type="hidden" name="action" value="save_profile_data">
            <input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>">
            <input type="hidden" name="profile_tab" value="identitas">
            <div class="ident-section-title">Identitas Utama</div>
            <div class="profile-form-grid">
                <div class="profile-form-field"><label>Nama Panggilan</label><input type="text" name="pd_nama_sebutan" value="<?= h($ident_d['nama_sebutan']??'') ?>" placeholder='cth: Tuan Muda, Alfatih'></div>
                <div class="profile-form-field"><label>Nama Lengkap (Resmi)</label><input type="text" name="pd_nama" value="<?= h($ident_d['nama_lengkap']??'') ?>" placeholder="Nama formal untuk portfolio publik"></div>
                <div class="profile-form-field">
                    <label>Profesi (opsional)</label>
                    <input type="text" name="pd_profesi" list="profesi-list" value="<?= h($ident_d['profesi']??'') ?>" placeholder="Web Developer, Pelajar SMK, dll">
                    <datalist id="profesi-list">
                        <option value="Web Developer"><option value="UI/UX Designer"><option value="Full Stack Developer">
                        <option value="Frontend Developer"><option value="Backend Developer"><option value="Mobile Developer">
                        <option value="Pelajar SMK"><option value="Mahasiswa"><option value="Karyawan Swasta">
                    </datalist>
                </div>
                <div class="profile-form-field"><label>Tagline / Motto</label><input type="text" name="pd_tagline" value="<?= h($ident_d['tagline']??'') ?>" placeholder="Kalimat yang menggambarkan Anda..."></div>
            </div>
            <div class="ident-section-title">Kontak &amp; Lokasi</div>
            <div class="profile-form-grid">
                <div class="profile-form-field"><label>Email</label><input type="email" name="pd_email" value="<?= h($ident_d['email']??'') ?>" placeholder="email@contoh.com"></div>
                <div class="profile-form-field"><label>No. Telepon</label><input type="text" name="pd_phone" value="<?= h($ident_d['phone']??'') ?>" placeholder="+62 812 xxxx xxxx"></div>
                <div class="profile-form-field"><label>Lokasi</label><input type="text" name="pd_location" value="<?= h($ident_d['location']??'') ?>" placeholder="Jakarta, Indonesia"></div>
            </div>
            <div class="ident-section-title">Tautan Sosial &amp; Web</div>
            <div class="profile-form-grid">
                <div class="profile-form-field"><label>GitHub URL</label><input type="url" name="pd_github" value="<?= h($ident_d['github']??'') ?>" placeholder="https://github.com/username"></div>
                <div class="profile-form-field"><label>LinkedIn URL</label><input type="url" name="pd_linkedin" value="<?= h($ident_d['linkedin']??'') ?>" placeholder="https://linkedin.com/in/username"></div>
                <div class="profile-form-field"><label>Instagram URL</label><input type="url" name="pd_instagram" value="<?= h($ident_d['instagram']??'') ?>" placeholder="https://instagram.com/username"></div>
                <div class="profile-form-field"><label>Website URL</label><input type="url" name="pd_website" value="<?= h($ident_d['website']??'') ?>" placeholder="https://yourwebsite.com"></div>
            </div>
            <div class="ident-section-title">Bio / Summary</div>
            <div class="profile-form-field">
                <label>Deskripsi Diri</label>
                <textarea name="pd_summary" rows="5" placeholder="Tulis bio singkat tentang diri Anda..."><?= h($ident_d['summary']??'') ?></textarea>
            </div>
            <div class="tampil-toggle">
                <input type="checkbox" name="pd_tampil_publik" id="pd_tampil_publik" value="1" <?= !empty($ident_d['tampil_publik'])?'checked':'' ?>>
                <label for="pd_tampil_publik"><i class="fa-solid fa-users" style="margin-right:6px;color:var(--text-muted);"></i>Tampilkan Profil di Halaman Depan Publik (Talent Directory)</label>
            </div>
            <button type="button" onclick="submitProfileForm('form-identitas','Identitas')" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Identitas</button>
        </form>
    </div>

    <div id="tab-pendidikan" class="tab-panel <?= $active_tab==='pendidikan'?'active':'' ?>">
        <form method="POST" id="form-pendidikan">
            <input type="hidden" name="action" value="save_profile_data"><input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>"><input type="hidden" name="profile_tab" value="pendidikan">
            <div class="dyn-list" id="edu-list">
                <?php foreach($edu_d as $i => $e) { $pt=!empty($e['institusi'])?$e['institusi']:"Pendidikan ".($i+1); $ps=!empty($e['gelar'])?$e['gelar']:''; ?>
                <div class="dyn-item">
                    <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
                        <h4><i class="fa-solid fa-graduation-cap"></i> <?= h($pt) ?><?php if($ps){?><span class="dyn-preview"> &mdash; <?= h($ps) ?></span><?php }?></h4>
                        <div class="dyn-item-header-btns"><button type="button" class="btn-remove-dyn" onclick="event.stopPropagation();this.closest('.dyn-item').remove()"><i class="fa-solid fa-trash"></i> Hapus</button><i class="fa-solid fa-chevron-down dyn-chevron"></i></div>
                    </div>
                    <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
                        <?php foreach([['edu_institusi[]','text','Nama Institusi',$e['institusi']??''],['edu_gelar[]','text','Gelar / Jenjang',$e['gelar']??''],['edu_bidang[]','text','Bidang Studi',$e['bidang']??''],['edu_mulai[]','text','Tahun Mulai',$e['tahun_mulai']??''],['edu_selesai[]','text','Tahun Selesai',$e['tahun_selesai']??'']] as [$fn,$ft,$fl,$fv]){?>
                        <div class="dyn-field"><label><?= $fl ?></label><input type="<?= $ft ?>" name="<?= $fn ?>" value="<?= h($fv) ?>" placeholder="<?= $fl ?>"></div>
                        <?php }?>
                        <div class="dyn-field full-width"><label>Deskripsi</label><textarea name="edu_desc[]" rows="3" placeholder="Prestasi, kegiatan..."><?= h($e['deskripsi']??'') ?></textarea></div>
                    </div></div></div>
                </div>
                <?php } ?>
            </div>
            <button type="button" class="btn-add-dyn" onclick="addEduItem()"><i class="fa-solid fa-plus"></i> Tambah Riwayat Pendidikan</button>
            <button type="button" onclick="submitProfileForm('form-pendidikan','Pendidikan')" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Pendidikan</button>
        </form>
    </div>

    <div id="tab-pengalaman" class="tab-panel <?= $active_tab==='pengalaman'?'active':'' ?>">
        <form method="POST" id="form-pengalaman">
            <input type="hidden" name="action" value="save_profile_data"><input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>"><input type="hidden" name="profile_tab" value="pengalaman">
            <div class="dyn-list" id="exp-list">
                <?php foreach($exp_d as $i => $e) { $pt=!empty($e['jabatan'])?$e['jabatan']:"Pengalaman ".($i+1); $ps=!empty($e['perusahaan'])?$e['perusahaan']:''; ?>
                <div class="dyn-item">
                    <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
                        <h4><i class="fa-solid fa-briefcase"></i> <?= h($pt) ?><?php if($ps){?><span class="dyn-preview"> @ <?= h($ps) ?></span><?php }?></h4>
                        <div class="dyn-item-header-btns"><button type="button" class="btn-remove-dyn" onclick="event.stopPropagation();this.closest('.dyn-item').remove()"><i class="fa-solid fa-trash"></i> Hapus</button><i class="fa-solid fa-chevron-down dyn-chevron"></i></div>
                    </div>
                    <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
                        <?php foreach([['exp_jabatan[]','Jabatan / Posisi',$e['jabatan']??''],['exp_perusahaan[]','Perusahaan / Organisasi',$e['perusahaan']??''],['exp_periode[]','Periode (cth: 2022 — 2024)',$e['periode']??'']] as [$fn,$fl,$fv]){?>
                        <div class="dyn-field"><label><?= $fl ?></label><input type="text" name="<?= $fn ?>" value="<?= h($fv) ?>" placeholder="<?= $fl ?>"></div>
                        <?php }?>
                        <div class="dyn-field full-width"><label>Deskripsi Tugas</label><textarea name="exp_desc[]" rows="3"><?= h($e['deskripsi']??'') ?></textarea></div>
                    </div></div></div>
                </div>
                <?php } ?>
            </div>
            <button type="button" class="btn-add-dyn" onclick="addExpItem()"><i class="fa-solid fa-plus"></i> Tambah Pengalaman Kerja</button>
            <button type="button" onclick="submitProfileForm('form-pengalaman','Pengalaman')" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Pengalaman</button>
        </form>
    </div>

    <div id="tab-keahlian" class="tab-panel <?= $active_tab==='keahlian'?'active':'' ?>">
        <form method="POST" id="form-keahlian">
            <input type="hidden" name="action" value="save_profile_data"><input type="hidden" name="csrf_token" value="<?= h($csrf_token) ?>"><input type="hidden" name="profile_tab" value="keahlian">
            
            <div class="ident-section-title">Keahlian (Skills)</div>
            <div class="dyn-list" id="skill-list">
                <?php foreach($skill_d as $i => $sk) { $ps=!empty($sk['nama'])?$sk['nama']:"Skill ".($i+1); ?>
                <div class="dyn-item">
                    <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
                        <h4><i class="fa-solid fa-star"></i> <?= h($ps) ?><span class="dyn-preview"><?= !empty($sk['kategori'])?' &middot; '.h($sk['kategori']):'' ?> <strong><?= (int)($sk['level']??70) ?>%</strong></span></h4>
                        <div class="dyn-item-header-btns"><button type="button" class="btn-remove-dyn" onclick="event.stopPropagation();this.closest('.dyn-item').remove()"><i class="fa-solid fa-trash"></i> Hapus</button><i class="fa-solid fa-chevron-down dyn-chevron"></i></div>
                    </div>
                    <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
                        <div class="dyn-field"><label>Nama Keahlian</label><input type="text" name="skill_nama[]" value="<?= h($sk['nama']??'') ?>" placeholder="PHP, JavaScript, Figma..."></div>
                        <div class="dyn-field"><label>Kategori</label><input type="text" name="skill_kategori[]" value="<?= h($sk['kategori']??'') ?>" placeholder="Frontend, Backend, Design..."></div>
                        <div class="dyn-field full-width">
                            <label>Level: <span id="slv_<?= $i ?>" style="font-weight:700;"><?= (int)($sk['level']??70) ?>%</span></label>
                            <div class="skill-slider-wrap">
                                <input type="range" name="skill_level[]" min="10" max="100" step="5" value="<?= (int)($sk['level']??70) ?>" oninput="document.getElementById('slv_<?= $i ?>').textContent=this.value+'%'">
                                <span style="font-size:.82rem;font-weight:700;min-width:40px;text-align:right;"><?= (int)($sk['level']??70) ?>%</span>
                            </div>
                        </div>
                    </div></div></div>
                </div>
                <?php } ?>
            </div>
            <button type="button" class="btn-add-dyn" onclick="addSkillItem()"><i class="fa-solid fa-plus"></i> Tambah Keahlian</button>
            
            <div class="ident-section-title" style="margin-top:32px;">Portfolio Proyek</div>
            <div class="dyn-list" id="porto-list">
                <?php foreach($porto_d as $i => $p) { $pp=!empty($p['nama'])?$p['nama']:"Proyek ".($i+1); ?>
                <div class="dyn-item">
                    <div class="dyn-item-header" onclick="toggleAccordion(this.closest('.dyn-item'))">
                        <h4><i class="fa-solid fa-diagram-project"></i> <?= h($pp) ?><?php if(!empty($p['tech'])){?><span class="dyn-preview"> &middot; <?= h(mb_strimwidth($p['tech'],0,36,'...')) ?></span><?php }?></h4>
                        <div class="dyn-item-header-btns"><button type="button" class="btn-remove-dyn" onclick="event.stopPropagation();this.closest('.dyn-item').remove()"><i class="fa-solid fa-trash"></i> Hapus</button><i class="fa-solid fa-chevron-down dyn-chevron"></i></div>
                    </div>
                    <div class="dyn-body"><div class="dyn-body-inner"><div class="dyn-body-grid">
                        <?php foreach([['porto_nama[]','Nama Proyek',$p['nama']??''],['porto_url[]','URL / Link Proyek',$p['url']??''],['porto_tech[]','Teknologi (pisah koma)',$p['tech']??'']] as [$fn,$fl,$fv]){?>
                        <div class="dyn-field"><label><?= $fl ?></label><input type="text" name="<?= $fn ?>" value="<?= h($fv) ?>" placeholder="<?= $fl ?>"></div>
                        <?php }?>
                        <div class="dyn-field full-width"><label>Deskripsi Proyek</label><textarea name="porto_desc[]" rows="3"><?= h($p['deskripsi']??'') ?></textarea></div>
                    </div></div></div>
                </div>
                <?php } ?>
            </div>
            <button type="button" class="btn-add-dyn" onclick="addPortoItem()"><i class="fa-solid fa-plus"></i> Tambah Proyek</button>
            
            <button type="button" onclick="submitProfileForm('form-keahlian','Keahlian &amp; Portfolio')" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Simpan Semua</button>
        </form>
    </div>
</div>