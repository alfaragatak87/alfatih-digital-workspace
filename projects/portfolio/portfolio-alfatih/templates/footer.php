</main> <!-- Ini menutup tag <main> dari header.php -->

<!-- =================================================================
     PROFESSIONAL FOOTER SECTION
     ================================================================= -->
<style>
    /* Styling untuk Footer Baru yang Profesional */
    .footer-professional {
        background-color: #080815; /* Warna background sedikit lebih gelap dari body */
        color: var(--text-secondary);
        padding: 5rem 0 2rem;
        margin-top: 4rem;
        position: relative;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        overflow: hidden;
    }

    .footer-professional::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 300px;
        background: radial-gradient(circle at 50% 0, rgba(0, 229, 255, 0.08), transparent 70%);
        pointer-events: none;
        z-index: 0;
    }

    .footer-professional .container {
        position: relative;
        z-index: 1;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 3rem;
        margin-bottom: 4rem;
    }

    .footer-column h4 {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        position: relative;
        padding-bottom: 0.5rem;
    }

    .footer-column h4::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 2px;
        background: var(--primary-color);
    }

    .footer-about p {
        line-height: 1.7;
        margin-bottom: 1.5rem;
    }

    .footer-social-icons {
        display: flex;
        gap: 1rem;
    }

    .footer-social-icons .social-icon {
        width: 45px;
        height: 45px;
        font-size: 1.1rem;
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .footer-social-icons .social-icon:hover {
        background-color: var(--primary-color);
        color: var(--dark-bg);
        transform: translateY(-3px) scale(1.1);
        border-color: var(--primary-color);
    }

    .footer-links ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links ul li {
        margin-bottom: 0.8rem;
    }

    .footer-links ul li a {
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
        position: relative;
    }

    .footer-links ul li a::after {
        content: '';
        position: absolute;
        width: 0;
        height: 1px;
        display: block;
        margin-top: 2px;
        right: 0;
        background: var(--primary-color);
        transition: width 0.3s ease;
    }
    
    .footer-links ul li a:hover {
        color: var(--text-primary);
        transform: translateX(5px);
    }

    .footer-links ul li a:hover::after {
        width: 100%;
        left: 0;
        background: var(--primary-color);
    }

    .footer-contact .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.2rem;
    }

    .footer-contact .contact-item i {
        font-size: 1.1rem;
        color: var(--primary-color);
        margin-top: 5px;
    }
    
    .footer-contact .contact-item a {
        color: var(--text-secondary);
        transition: color 0.3s ease;
    }
    
    .footer-contact .contact-item a:hover {
        color: var(--primary-color);
    }

    .footer-bottom-professional {
        text-align: center;
        padding-top: 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .footer-bottom-professional p {
        margin: 0;
        color: var(--text-muted);
    }
</style>

<footer class="footer-professional">
    <div class="container">
        <div class="footer-grid">
            <!-- Kolom About -->
            <div class="footer-column footer-about">
                <h4><?= htmlspecialchars(OWNER_NAME) ?></h4>
                <p>
                    Seorang mahasiswa informatika yang antusias dalam menciptakan solusi digital yang fungsional dan menarik secara visual.
                </p>
                <div class="footer-social-icons">
                    <a href="<?= htmlspecialchars(OWNER_GITHUB) ?>" target="_blank" class="social-icon" title="GitHub"><i class="fab fa-github"></i></a>
                    <a href="https://www.instagram.com/alfamuhammad___/" target="_blank" class="social-icon" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="mailto:<?= htmlspecialchars(OWNER_EMAIL) ?>" class="social-icon" title="Email"><i class="fas fa-envelope"></i></a>
                    <a href="https://wa.me/<?= str_replace(['+', ' ', '-'], '', htmlspecialchars(OWNER_WHATSAPP)) ?>" target="_blank" class="social-icon" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            
            <!-- Kolom Kontak (DIPINDAHKAN KE SINI) -->
            <div class="footer-column footer-contact">
                <h4>Hubungi Saya</h4>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Lumajang, Jawa Timur, Indonesia</span>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <a href="https://wa.me/<?= str_replace(['+', ' ', '-'], '', htmlspecialchars(OWNER_WHATSAPP)) ?>"><?= htmlspecialchars(OWNER_WHATSAPP) ?></a>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <a href="mailto:<?= htmlspecialchars(OWNER_EMAIL) ?>"><?= htmlspecialchars(OWNER_EMAIL) ?></a>
                </div>
            </div>

            <!-- Kolom Quick Links -->
            <div class="footer-column footer-links">
                <h4><?= __('navigation') ?></h4>
                <ul>
                    <li><a href="<?= BASE_URL ?>/pages/about.php"><?= __('about') ?></a></li>
                    <li><a href="<?= BASE_URL ?>/pages/projects.php"><?= __('projects') ?></a></li>
                    <li><a href="<?= BASE_URL ?>/pages/blog.php"><?= __('blog') ?></a></li>
                    <li><a href="<?= BASE_URL ?>/pages/contact.php"><?= __('contact') ?></a></li>
                </ul>
            </div>

        </div>

        <div class="footer-bottom-professional">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(OWNER_NAME) ?>. Dibuat dengan <i class="fas fa-heart" style="color: #e25555;"></i> dan semangat ngoding.</p>
        </div>
    </div>
</footer>
    
<!-- JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typed.js/2.0.12/typed.min.js"></script>
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
