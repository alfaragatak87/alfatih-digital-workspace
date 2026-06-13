
document.addEventListener('DOMContentLoaded', function() {

    // Initialize AOS (Animate on Scroll)
    AOS.init({
        duration: 800,
        easing: 'ease',
        once: true,
        offset: 100
    });

    // Initialize Particles.js for background
    if (document.getElementById('particles-js')) {
        particlesJS('particles-js', {
            "particles": {
                "number": {
                    "value": 80,
                    "density": {
                        "enable": true,
                        "value_area": 800
                    }
                },
                "color": {
                    "value": "#00e5ff"
                },
                "shape": {
                    "type": "circle"
                },
                "opacity": {
                    "value": 0.5,
                    "random": true,
                    "anim": {
                        "enable": true,
                        "speed": 1,
                        "opacity_min": 0.1,
                        "sync": false
                    }
                },
                "size": {
                    "value": 3,
                    "random": true,
                    "anim": {
                        "enable": true,
                        "speed": 2,
                        "size_min": 0.1,
                        "sync": false
                    }
                },
                "line_linked": {
                    "enable": true,
                    "distance": 150,
                    "color": "#00e5ff",
                    "opacity": 0.2,
                    "width": 1
                },
                "move": {
                    "enable": true,
                    "speed": 1,
                    "direction": "none",
                    "random": true,
                    "straight": false,
                    "out_mode": "out",
                    "bounce": false
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": {
                        "enable": true,
                        "mode": "bubble"
                    },
                    "onclick": {
                        "enable": true,
                        "mode": "push"
                    },
                    "resize": true
                },
                "modes": {
                    "bubble": {
                        "distance": 200,
                        "size": 5,
                        "duration": 2,
                        "opacity": 0.8
                    },
                    "push": {
                        "particles_nb": 4
                    }
                }
            },
            "retina_detect": true
        });
    }

    // =================================================================
    // KODE KURSOR GABUNGAN (PUNYAMU + CLAUDE) YANG SUDAH DIPERBAIKI
    // =================================================================
    const cursor = document.querySelector('.cursor-dot');
    const cursorOutline = document.querySelector('.cursor-dot-outline');

    if (cursor && cursorOutline) {
        if (window.innerWidth > 768) {
            document.body.style.cursor = 'none';

            // Sembunyikan kursor di awal agar tidak muncul di pojok kiri atas
            cursor.style.opacity = '0';
            cursorOutline.style.opacity = '0';

            let mouseX = 0;
            let mouseY = 0;
            let outlineX = 0;
            let outlineY = 0;
            let mouseMoved = false; // Penanda untuk cek apakah mouse sudah bergerak

            document.addEventListener('mousemove', e => {
                if (!mouseMoved) {
                    // Saat mouse bergerak pertama kali, tampilkan kursor
                    cursor.style.opacity = '1';
                    cursorOutline.style.opacity = '1';
                    mouseMoved = true;
                }
                mouseX = e.clientX;
                mouseY = e.clientY;
            });

            // Fungsi animasi agar pergerakan lebih halus
            const animateCursor = () => {
                // Animasikan titik kursor utama
                cursor.style.left = mouseX + 'px';
                cursor.style.top = mouseY + 'px';

                // Animasikan outline dengan sedikit delay (efek 'lerp' untuk kehalusan)
                const speed = 0.1;
                outlineX += (mouseX - outlineX) * speed;
                outlineY += (mouseY - outlineY) * speed;
                cursorOutline.style.left = outlineX + 'px';
                cursorOutline.style.top = outlineY + 'px';

                requestAnimationFrame(animateCursor);
            };
            requestAnimationFrame(animateCursor);


            // Sembunyikan kursor saat keluar dari window
            document.addEventListener('mouseleave', () => {
                cursor.style.opacity = '0';
                cursorOutline.style.opacity = '0';
            });

            // Tampilkan lagi saat masuk window
            document.addEventListener('mouseenter', () => {
                if (mouseMoved) {
                    cursor.style.opacity = '1';
                    cursorOutline.style.opacity = '1';
                }
            });

            // Efek hover pada elemen interaktif (menggabungkan logika dari kedua versi)
            const interactiveElements = document.querySelectorAll('a, button, .btn, input, textarea, select, .language-btn, .language-dropdown-content a, .filter-btn, .nav-link, .admin-btn, .social-icon, .close-modal');

            interactiveElements.forEach(el => {
                el.addEventListener('mouseenter', () => {
                    cursor.style.transform = 'translate(-50%, -50%) scale(0.5)';
                    cursorOutline.style.transform = 'translate(-50%, -50%) scale(1.5)';
                    cursorOutline.style.backgroundColor = 'rgba(0, 229, 255, 0.1)';
                });

                el.addEventListener('mouseleave', () => {
                    cursor.style.transform = 'translate(-50%, -50%) scale(1)';
                    cursorOutline.style.transform = 'translate(-50%, -50%) scale(1)';
                    cursorOutline.style.backgroundColor = 'rgba(0, 229, 255, 0.2)';
                });
            });

            // Efek klik
            document.addEventListener('mousedown', () => {
                cursor.style.transform = 'translate(-50%, -50%) scale(0.7)';
                cursorOutline.style.transform = 'translate(-50%, -50%) scale(1.5)';
            });

            document.addEventListener('mouseup', () => {
                cursor.style.transform = 'translate(-50%, -50%) scale(1)';
                cursorOutline.style.transform = 'translate(-50%, -50%) scale(1)';
            });

        } else {
            // Nonaktifkan kursor khusus di perangkat mobile
            cursor.style.display = 'none';
            cursorOutline.style.display = 'none';
        }
    }
    // =================================================================
    // AKHIR DARI KODE KURSOR
    // =================================================================


    // Typed.js initialization for hero section
    if (document.getElementById('typed-text')) {
        let typedStrings = ['Web Developer', 'UI/UX Designer', 'Mahasiswa Informatika', 'Problem Solver'];

        // Check if jobTitles variable is defined (from index.php)
        if (typeof jobTitles !== 'undefined') {
            typedStrings = jobTitles;
        }

        new Typed('#typed-text', {
            strings: typedStrings,
            typeSpeed: 70,
            backSpeed: 50,
            backDelay: 2000,
            loop: true
        });
    }

    // Mobile Menu Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.nav-menu');

    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            menuToggle.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
    }

    // Close mobile menu when clicking a nav link
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (navMenu.classList.contains('active')) {
                menuToggle.click();
            }
        });
    });

    // Back to Top Button
    const backToTopButton = document.getElementById('backToTop');

    if (backToTopButton) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });

        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Navbar scroll effect
    const header = document.querySelector('header');

    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    // Project Filtering
    const filterButtons = document.querySelectorAll('.filter-btn');
    const projectCards = document.querySelectorAll('.project-card');

    if (filterButtons.length > 0 && projectCards.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const filter = this.getAttribute('data-filter');

                projectCards.forEach(card => {
                    card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    if (filter === 'all' || card.getAttribute('data-category') === filter) {
                        card.style.display = 'block';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 50);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            card.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });
    }

    // Form validation
    const contactForm = document.getElementById('contactForm');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            let isValid = true;
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const subject = document.getElementById('subject');
            const message = document.getElementById('message');

            if (name.value.trim() === '') { isValid = false; highlightError(name); } else { removeError(name); }
            if (email.value.trim() === '' || !isValidEmail(email.value)) { isValid = false; highlightError(email); } else { removeError(email); }
            if (subject.value.trim() === '') { isValid = false; highlightError(subject); } else { removeError(subject); }
            if (message.value.trim() === '') { isValid = false; highlightError(message); } else { removeError(message); }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    function highlightError(element) { element.style.borderColor = 'var(--error-color)'; }
    function removeError(element) { element.style.borderColor = ''; }
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                window.scrollTo({
                    top: target.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Project Details Modal
    const viewDetailsButtons = document.querySelectorAll('.view-details');
    const modal = document.getElementById('projectModal');

    if (viewDetailsButtons.length > 0 && modal) {
        const closeBtn = document.querySelector('.close-modal');
        const projectDetails = document.getElementById('projectDetails');

        viewDetailsButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const projectId = this.getAttribute('data-id');

                fetch(`${window.location.origin}/portfolio-alfatih/includes/get_project.php?id=${projectId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const project = data.project;
                            projectDetails.innerHTML = `
                                <div class="modal-header">
                                    <h2>${project.judul}</h2>
                                    <span class="project-category">${project.kategori}</span>
                                </div>
                                <div class="modal-body">
                                    <div class="modal-image">
                                        <img src="${window.location.origin}/portfolio-alfatih/uploads/projects/${project.gambar_proyek}" alt="${project.judul}">
                                    </div>
                                    <div class="modal-text">
                                        <h3>Project Description</h3>
                                        <p>${project.deskripsi}</p>
                                        <p class="project-date"><i class="far fa-calendar"></i> ${new Date(project.tanggal_dibuat).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
                                        ${project.link_proyek ? `<a href="${project.link_proyek}" class="btn btn-primary" target="_blank">View Live Project</a>` : ''}
                                    </div>
                                </div>`;
                            modal.style.display = 'block';
                            document.body.style.overflow = 'hidden';
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

        const closeModal = () => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        };

        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    // =================================================================
    // FITUR BARU DARI KODE CLAUDE YANG SUDAH DIGABUNGKAN
    // =================================================================

    // Rating system for testimonials
    const ratingStars = document.querySelectorAll('.rating-star');
    const ratingInput = document.getElementById('rating');

    if (ratingStars.length > 0 && ratingInput) {
        const updateStars = (rating) => {
            ratingStars.forEach(star => {
                const starRating = parseInt(star.getAttribute('data-rating'));
                star.classList.toggle('active', starRating <= rating);
            });
        };

        ratingStars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                ratingInput.value = rating;
                updateStars(rating);
            });
        });
        
        // Set initial stars
        updateStars(parseInt(ratingInput.value) || 5);
    }

    // Theme switcher
    const themeSwitcher = document.getElementById('themeSwitcher');
    if (themeSwitcher) {
        themeSwitcher.addEventListener('change', function() {
            const themeValue = this.value;
            document.body.className = ''; // Reset theme classes
            document.body.classList.add('theme-' + themeValue);
            
            // Note: Fungsi saveThemePreference perlu diimplementasikan
            // Contoh: localStorage.setItem('theme', themeValue);
            if (typeof saveThemePreference === 'function') {
                saveThemePreference(themeValue);
            }
        });
    }

    // File input preview
    const fileInputs = document.querySelectorAll('.custom-file-input');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const fileName = this.files[0]?.name || 'Choose file';
            const fileLabel = this.nextElementSibling;
            
            if (fileLabel) {
                fileLabel.textContent = fileName;
            }
            
            const previewContainer = this.closest('.form-group')?.querySelector('.image-preview');
            if (previewContainer && this.files[0] && this.files[0].type.startsWith('image/')) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // Skill selector in admin
    const skillSelector = document.getElementById('skillSelector');
    const selectedSkillsContainer = document.getElementById('selectedSkills');

    if (skillSelector && selectedSkillsContainer) {
        const addSkill = (skillValue, skillText) => {
             if (skillValue && !document.querySelector(`input[name="skills[]"][value="${skillValue}"]`)) {
                const skillItem = document.createElement('div');
                skillItem.className = 'selected-skill';
                skillItem.innerHTML = `
                    <span>${skillText}</span>
                    <input type="hidden" name="skills[]" value="${skillValue}">
                    <button type="button" class="remove-skill"><i class="fas fa-times"></i></button>
                `;
                selectedSkillsContainer.appendChild(skillItem);
            }
        };

        skillSelector.addEventListener('change', function() {
            addSkill(this.value, this.options[this.selectedIndex].text);
            this.value = ''; // Reset selector
        });

        selectedSkillsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-skill')) {
                e.target.closest('.selected-skill').remove();
            }
        });
    }

    // Course assignment type toggle
    const assignmentTypeSelector = document.getElementById('assignment_type');
    const customTypeField = document.getElementById('custom_type_field');

    if (assignmentTypeSelector && customTypeField) {
        assignmentTypeSelector.addEventListener('change', function() {
            customTypeField.style.display = (this.value === 'custom') ? 'block' : 'none';
        });
        // Trigger on page load
        customTypeField.style.display = (assignmentTypeSelector.value === 'custom') ? 'block' : 'none';
    }

});
