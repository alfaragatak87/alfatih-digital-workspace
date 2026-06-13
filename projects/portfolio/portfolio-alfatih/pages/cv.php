<?php
$pageTitle = "Curriculum Vitae";
require_once '../config/koneksi.php';
require_once '../templates/header.php';

// Get profile data
$stmt = $pdo->query("SELECT * FROM profil WHERE id = 1");
$profile = $stmt->fetch();

// Get education history
$stmt = $pdo->query("SELECT * FROM pendidikan ORDER BY tahun_mulai DESC");
$education = $stmt->fetchAll();

// Get CV file
$stmt = $pdo->query("SELECT * FROM dokumen WHERE kategori = 'CV' ORDER BY id DESC LIMIT 1");
$cv = $stmt->fetch();
?>

<!-- CV Header Section -->
<section class="cv-header section-padding">
    <div class="container">
        <div class="section-header" data-aos="fade-down">
        <h1>Curriculum Vitae</h1>
            <p>My journey and qualifications</p>
        </div>
        
        <div class="cv-actions" data-aos="fade-up">
            <?php if ($cv && !empty($cv['file'])): ?>
            <a href="<?= BASE_URL ?>/uploads/documents/<?= $cv['file'] ?>" class="btn btn-primary btn-lg" download>
                <i class="fas fa-download"></i> Download CV
            </a>
            <?php else: ?>
            <p class="no-data">CV file not available yet.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Personal Info Section -->
<section class="cv-personal section-padding">
    <div class="container">
        <div class="cv-card" data-aos="fade-up">
            <div class="cv-card-header">
                <h2><i class="fas fa-user"></i> Personal Information</h2>
            </div>
            <div class="cv-card-body">
                <div class="personal-info-grid">
                    <div class="personal-info-item">
                        <h3>Full Name</h3>
                        <p><?= OWNER_NAME ?></p>
                    </div>
                    <div class="personal-info-item">
                        <h3>Email</h3>
                        <p><?= OWNER_EMAIL ?></p>
                    </div>
                    <div class="personal-info-item">
                        <h3>Phone</h3>
                        <p><?= OWNER_WHATSAPP ?></p>
                    </div>
                    <div class="personal-info-item">
                        <h3>GitHub</h3>
                        <p><a href="<?= OWNER_GITHUB ?>" target="_blank"><?= OWNER_GITHUB ?></a></p>
                    </div>
                    <div class="personal-info-item">
                        <h3>Location</h3>
                        <p><?= $profile['location'] ?? 'Lumajang, East Java, Indonesia' ?></p>
                    </div>
                    <div class="personal-info-item">
                        <h3>Current Status</h3>
                        <p><?= $profile['current_status'] ?? 'Student' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Education Section -->
<section class="cv-education section-padding">
    <div class="container">
        <div class="cv-card" data-aos="fade-up">
            <div class="cv-card-header">
                <h2><i class="fas fa-graduation-cap"></i> Education</h2>
            </div>
            <div class="cv-card-body">
                <div class="timeline">
                    <?php if (count($education) > 0): ?>
                        <?php foreach ($education as $edu): ?>
                            <div class="timeline-item" data-aos="fade-up">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <div class="timeline-date"><?= $edu['tahun_mulai'] ?> - <?= $edu['tahun_selesai'] ?: 'Present' ?></div>
                                    <h3><?= $edu['gelar'] ?></h3>
                                    <h4><?= $edu['institusi'] ?></h4>
                                    <p><?= $edu['deskripsi'] ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">2023 - Present</div>
                                <h3>S1 Informatika</h3>
                                <h4>ITB Widya Gama Lumajang</h4>
                                <p>Currently in 3rd semester, focusing on web development, algorithms, and database management.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">2019 - 2022</div>
                                <h3>Teknik Komputer & Jaringan</h3>
                                <h4>SMK Miftahul Islam Kunir</h4>
                                <p>Learned computer networking, hardware troubleshooting, and basic programming.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">2016 - 2019</div>
                                <h3>MTs</h3>
                                <h4>MTs Salafiyah Al-Yasiny</h4>
                                <p>Junior high school education with additional Islamic studies.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">2010 - 2016</div>
                                <h3>MI</h3>
                                <h4>MI Salafiyah Al-Yasiny</h4>
                                <p>Primary education with basic computer literacy courses.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Skills Section -->
<section class="cv-skills section-padding">
    <div class="container">
        <div class="cv-card" data-aos="fade-up">
            <div class="cv-card-header">
                <h2><i class="fas fa-code"></i> Technical Skills</h2>
            </div>
            <div class="cv-card-body">
                <div class="skills-grid">
                    <div class="skill-category">
                        <h3>Frontend Development</h3>
                        <div class="skill-items">
                            <div class="skill-item">
                                <span>HTML5</span>
                                <div class="skill-progress">
                                    <div class="progress" style="width: 95%;"></div>
                                </div>
                            </div>
                            <div class="skill-item">
                                <span>CSS3</span>
                                <div class="skill-progress">
                                    <div class="progress" style="width: 90%;"></div>
                                </div>
                            </div>
                            <div class="skill-item">
                                <span>JavaScript</span>
                                <div class="skill-progress">
                                    <div class="progress" style="width: 85%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="skill-category">
                        <h3>Backend Development</h3>
                        <div class="skill-items">
                            <div class="skill-item">
                                <span>PHP</span>
                                <div class="skill-progress">
                                    <div class="progress" style="width: 90%;"></div>
                                </div>
                            </div>
                            <div class="skill-item">
                                <span>MySQL</span>
                                <div class="skill-progress">
                                    <div class="progress" style="width: 85%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="skill-category">
                        <h3>Design & Marketing</h3>
                        <div class="skill-items">
                            <div class="skill-item">
                                <span>UI/UX (Figma)</span>
                                <div class="skill-progress">
                                    <div class="progress" style="width: 80%;"></div>
                                </div>
                            </div>
                            <div class="skill-item">
                                <span>Digital Marketing</span>
                                <div class="skill-progress">
                                    <div class="progress" style="width: 85%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once '../templates/footer.php'; ?>