<?php
$pageTitle = "Testimonials";
require_once '../config/koneksi.php';
require_once '../templates/header.php';

// Get active testimonials
$stmt = $pdo->query("SELECT * FROM testimonials WHERE aktif = 1 ORDER BY id DESC");
$testimonials = $stmt->fetchAll();
?>

<!-- Testimonials Hero Section -->
<section class="testimonials-hero section-padding">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h1>Client Testimonials</h1>
            <p>What people say about working with me</p>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section section-padding">
    <div class="container">
        <?php if (count($testimonials) > 0): ?>
            <div class="testimonials-grid">
                <?php foreach ($testimonials as $index => $testimonial): ?>
                    <div class="testimonial-card" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="testimonial-content">
                            <div class="testimonial-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star <?= ($i <= $testimonial['rating']) ? 'star-filled' : 'star-empty' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            
                            <div class="testimonial-text">
                                <?= $testimonial['testimonial'] ?>
                            </div>
                            
                            <div class="testimonial-author">
                                <div class="testimonial-author-img">
                                    <?php if (!empty($testimonial['foto'])): ?>
                                        <img src="<?= BASE_URL ?>/uploads/testimonials/<?= $testimonial['foto'] ?>" alt="<?= $testimonial['nama'] ?>">
                                    <?php else: ?>
                                        <img src="<?= BASE_URL ?>/assets/img/default-avatar.png" alt="<?= $testimonial['nama'] ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="testimonial-author-info">
                                    <h4><?= $testimonial['nama'] ?></h4>
                                    <p><?= $testimonial['posisi'] ?><?= !empty($testimonial['perusahaan']) ? ', ' . $testimonial['perusahaan'] : '' ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-testimonials" data-aos="fade-up">
                <div class="no-data-icon">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <h3>No testimonials yet</h3>
                <p>Check back soon for client testimonials and reviews.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Form Submit Testimonial -->
<section class="submit-testimonial section-padding">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2>Share Your Experience</h2>
            <p>Your feedback helps me improve and grow</p>
        </div>
        
        <div class="testimonial-form-container" data-aos="fade-up">
            <form action="<?= BASE_URL ?>/includes/submit_testimonial.php" method="POST" enctype="multipart/form-data" id="testimonialForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nama">Your Name*</label>
                        <input type="text" id="nama" name="nama" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="posisi">Your Position</label>
                        <input type="text" id="posisi" name="posisi" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="perusahaan">Company/Organization</label>
                        <input type="text" id="perusahaan" name="perusahaan" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="rating">Rating*</label>
                        <div class="rating-input">
                            <i class="fas fa-star rating-star" data-rating="1"></i>
                            <i class="fas fa-star rating-star" data-rating="2"></i>
                            <i class="fas fa-star rating-star" data-rating="3"></i>
                            <i class="fas fa-star rating-star" data-rating="4"></i>
                            <i class="fas fa-star rating-star" data-rating="5"></i>
                            <input type="hidden" name="rating" id="rating" value="5" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="testimonial">Your Testimonial*</label>
                    <textarea id="testimonial" name="testimonial" class="form-control" rows="5" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="foto">Your Photo</label>
                    <div class="custom-file">
                        <input type="file" id="foto" name="foto" class="custom-file-input" accept="image/*">
                        <label class="custom-file-label" for="foto">Choose file</label>
                    </div>
                    <small class="form-text">Upload a profile picture (optional). Maximum size: 2MB</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Submit Testimonial</button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- JavaScript for Star Rating -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingStars = document.querySelectorAll('.rating-star');
    const ratingInput = document.getElementById('rating');
    
    // Set initial stars
    updateStars(5);
    
    // Add click event to stars
    ratingStars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            ratingInput.value = rating;
            updateStars(rating);
        });
    });
    
    function updateStars(rating) {
        ratingStars.forEach(star => {
            const starRating = parseInt(star.getAttribute('data-rating'));
            if (starRating <= rating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }
});
</script>

<?php require_once '../templates/footer.php'; ?>