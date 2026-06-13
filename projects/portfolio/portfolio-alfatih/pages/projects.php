<?php
$pageTitle = "Projects";
require_once '../config/koneksi.php';
require_once '../templates/header.php';

// Get all projects
$stmt = $pdo->query("SELECT * FROM proyek ORDER BY tanggal_dibuat DESC");
$projects = $stmt->fetchAll();

// Get all categories for filter
$stmt = $pdo->query("SELECT DISTINCT kategori FROM proyek ORDER BY kategori ASC");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!-- Projects Section -->
<section class="projects section-padding" id="projects">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h1>My Projects</h1>
            <p>Showcase of my work and experience</p>
        </div>
        
        <!-- Filter Buttons -->
        <div class="filter-buttons" data-aos="fade-up">
            <button class="filter-btn active" data-filter="all">All</button>
            <?php foreach ($categories as $category): ?>
                <button class="filter-btn" data-filter="<?= strtolower(str_replace(' ', '-', $category)) ?>"><?= $category ?></button>
            <?php endforeach; ?>
        </div>
        
        <!-- Projects Grid -->
        <div class="projects-grid">
            <?php if (count($projects) > 0): ?>
                <?php foreach ($projects as $index => $project): ?>
                    <div class="project-card" data-aos="fade-up" data-aos-delay="<?= ($index % 3) * 100 ?>" data-category="<?= strtolower(str_replace(' ', '-', $project['kategori'])) ?>">
                        <div class="project-img">
                            <img src="<?= BASE_URL ?>/uploads/projects/<?= $project['gambar_proyek'] ?>" alt="<?= $project['judul'] ?>">
                            <div class="project-overlay">
                                <div class="project-overlay-content">
                                    <h3><?= $project['judul'] ?></h3>
                                    <p><?= truncateText($project['deskripsi'], 100) ?></p>
                                    <div class="project-links">
                                        <?php if (!empty($project['link_proyek'])): ?>
                                            <a href="<?= $project['link_proyek'] ?>" class="btn-icon" target="_blank" title="View Live Project">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="#" class="btn-icon view-details" data-id="<?= $project['id'] ?>" title="View Details">
                                            <i class="fas fa-info-circle"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="project-info">
                            <span class="project-category"><?= $project['kategori'] ?></span>
                            <h3><?= $project['judul'] ?></h3>
                            <p class="project-date"><i class="far fa-calendar"></i> <?= date('d M Y', strtotime($project['tanggal_dibuat'])) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-data">No projects to display yet.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Project Details Modal -->
<div class="modal" id="projectModal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <div id="projectDetails"></div>
    </div>
</div>

<!-- Modal JavaScript for Projects -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get modal elements
    const modal = document.getElementById('projectModal');
    const closeBtn = document.querySelector('.close-modal');
    const projectDetails = document.getElementById('projectDetails');
    
    // View Details Button Click
    const viewDetailsButtons = document.querySelectorAll('.view-details');
    viewDetailsButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const projectId = this.getAttribute('data-id');
            
            // AJAX request to get project details
            fetch(`<?= BASE_URL ?>/includes/get_project.php?id=${projectId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const project = data.project;
                        let modalContent = `
                            <div class="modal-header">
                                <h2>${project.judul}</h2>
                                <span class="project-category">${project.kategori}</span>
                            </div>
                            <div class="modal-body">
                                <div class="modal-image">
                                    <img src="<?= BASE_URL ?>/uploads/projects/${project.gambar_proyek}" alt="${project.judul}">
                                </div>
                                <div class="modal-text">
                                    <h3>Project Description</h3>
                                    <p>${project.deskripsi}</p>
                                    <p class="project-date"><i class="far fa-calendar"></i> ${new Date(project.tanggal_dibuat).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
                                    ${project.link_proyek ? `<a href="${project.link_proyek}" class="btn btn-primary" target="_blank">View Live Project</a>` : ''}
                                </div>
                            </div>
                        `;
                        projectDetails.innerHTML = modalContent;
                        modal.style.display = 'block';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while fetching project details.');
                });
        });
    });
    
    // Close modal
    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});
</script>

<?php require_once '../templates/footer.php'; ?>