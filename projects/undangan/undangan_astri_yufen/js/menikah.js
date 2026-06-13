/* =========================================
   JS MOBILE-FIRST & ANIMASI PROFESIONAL
   Undangan Astri & Yufen
========================================= */

document.addEventListener("DOMContentLoaded", function() {
    const btnBuka = document.getElementById('btn-buka');
    const cover = document.getElementById('cover');
    const kontenUtama = document.getElementById('konten-utama');
    const bottomNav = document.getElementById('bottom-nav');
    const audio = document.getElementById('backsound');
    const musicIcon = document.querySelector('#music-controller i');

    // Elemen untuk Animasi Gunungan Navigasi (Global Gate)
    const globalGate = document.getElementById('global-gate');
    const globalLeft = document.querySelector('.global-gate-left');
    const globalRight = document.querySelector('.global-gate-right');
    
    let isPlaying = false;

    // 1. GENERATE DEBU EMAS (PARTICLES) DI COVER
    const particleContainer = document.getElementById("particles-container");
    if(particleContainer) {
        for (let i = 0; i < 35; i++) {
            let particle = document.createElement("div");
            particle.classList.add("particle");
            particle.style.left = Math.random() * 100 + "%";
            particle.style.animationDuration = (Math.random() * 3 + 3) + "s";
            particle.style.animationDelay = Math.random() * 2 + "s";
            particleContainer.appendChild(particle);
        }
    }

    // ==============================================================
    // FUNGSI KHUSUS: ANIMASI GUNUNGAN MEMBELAH SAAT NAVIGASI DIKLIK
    // ==============================================================
    function triggerGlobalGate() {
        if(!globalGate || !globalLeft || !globalRight) return;

        // Reset posisi Gunungan ke pas di Tengah Layar (Instan tanpa animasi)
        globalGate.classList.remove('gate-hidden');
        globalLeft.style.transition = 'none';
        globalRight.style.transition = 'none';

        globalLeft.style.transform = 'translateX(0) scale(1.1)';
        globalLeft.style.opacity = '1';

        globalRight.style.transform = 'translateX(0) scale(1.1) scaleX(-1)';
        globalRight.style.opacity = '1';

        // Beri jeda 50ms agar browser memproses letak di tengah, lalu gerakkan MEMBELAH KE PINGGIR
        setTimeout(() => {
            globalLeft.style.transition = 'transform 1s cubic-bezier(0.25, 0.46, 0.45, 0.94), opacity 0.8s';
            globalRight.style.transition = 'transform 1s cubic-bezier(0.25, 0.46, 0.45, 0.94), opacity 0.8s';

            globalLeft.style.transform = 'translateX(-100vw) rotate(-15deg)';
            globalLeft.style.opacity = '0';

            globalRight.style.transform = 'translateX(100vw) rotate(15deg) scaleX(-1)';
            globalRight.style.opacity = '0';
        }, 50);

        // Sembunyikan kontainer setelah animasi membelah selesai (1 detik)
        setTimeout(() => {
            globalGate.classList.add('gate-hidden');
        }, 1000);
    }

    // 2. ANIMASI BUKA UNDANGAN & THEATRICAL GATE DI DEPAN
    if (btnBuka) {
        btnBuka.addEventListener('click', function() {
            // Mainkan audio musik
            if (audio) {
                audio.play().then(() => {
                    isPlaying = true;
                    if(musicIcon) musicIcon.classList.add('fa-spin');
                }).catch(e => console.log("Autoplay musik diblokir browser."));
            }

            // Panggil Animasi Gunungan Membelah dari Tengah ke Pinggir di Halaman Depan
            const gateLeft = document.querySelector('.gate-left');
            const gateRight = document.querySelector('.gate-right');
            const coverContent = document.querySelector('.cover-content');

            if (gateLeft) gateLeft.classList.add('gate-open-left');
            if (gateRight) gateRight.classList.add('gate-open-right');
            if (coverContent) coverContent.style.opacity = '0';
            
            // Matikan background cover agar transparan saat membelah
            cover.style.background = 'transparent';
            cover.style.pointerEvents = 'none';

            // Tunggu animasi gunungan membelah selesai (1.5 detik), lalu tampilkan halaman inti
            setTimeout(() => {
                cover.style.display = 'none';
                kontenUtama.style.display = 'block';
                bottomNav.style.display = 'block';
                
                // Buka kunci scroll di layar utama agar halaman bisa discroll
                document.body.classList.remove('no-scroll');

                // Aktifkan animasi AOS untuk teks yang bermunculan
                if (typeof AOS !== 'undefined') {
                    AOS.init({ duration: 1000, once: true });
                    AOS.refresh(); 
                }

                // Load buku tamu otomatis
                if (typeof loadUcapan === 'function') loadUcapan(); 
            }, 1500); 
        });
    }

    // 3. MUSIK CONTROLLER
    const musicController = document.getElementById('music-controller');
    if (musicController && audio) {
        musicController.addEventListener('click', function() {
            if (isPlaying) { 
                audio.pause(); 
                if(musicIcon) musicIcon.classList.remove('fa-spin'); 
                isPlaying = false; 
            } else { 
                audio.play(); 
                if(musicIcon) musicIcon.classList.add('fa-spin'); 
                isPlaying = true; 
            }
        });
    }

    // 4. LOGIKA BOTTOM NAV (Scroll Presisi & Gunungan Membelah)
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('main section');

    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const targetSection = document.getElementById(targetId);
            
            if (targetSection) {
                // Munculkan efek Gunungan Membelah
                triggerGlobalGate();

                // Beri jeda sangat singkat agar tertutup gunungan, lalu scroll ke bagian yang dituju
                setTimeout(() => {
                    window.scrollTo({
                        top: targetSection.offsetTop,
                        behavior: 'smooth'
                    });
                }, 200);
            }
        });
    });

    // Deteksi letak scroll untuk mewarnai icon navigasi secara otomatis
    window.addEventListener("scroll", function() {
        let scrollY = window.scrollY || window.pageYOffset;
        
        sections.forEach(current => {
            const sectionHeight = current.offsetHeight;
            const sectionTop = current.offsetTop - 150; // Jarak offset agar pendeteksian lebih akurat
            const sectionId = current.getAttribute("id");
            
            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                document.querySelectorAll('.bottom-nav a').forEach(a => a.classList.remove('active'));
                const activeLink = document.querySelector('.bottom-nav a[data-target=' + sectionId + ']');
                if (activeLink) activeLink.classList.add('active');
            }
        });
    });
});

// 5. AJAX DATABASE UCAPAN & BUKU TAMU
$(document).ready(function() {
    window.loadUcapan = function() {
        $.ajax({ 
            url: 'process/ambil-ucapan.php', 
            type: 'GET', 
            success: function(res) { 
                $('#list-ucapan').html(res); 
            } 
        });
    }

    $('#form-ucapan').on('submit', function(e) {
        e.preventDefault();
        var btn = $(this).find('button[type="submit"]');
        var txt = btn.html();
        
        // Ganti tombol jadi loading saat diklik
        btn.html('<i class="fas fa-spinner fa-spin"></i> Mengirim...').prop('disabled', true);

        $.ajax({
            url: 'process/simpan-ucapan.php', 
            type: 'POST', 
            data: $(this).serialize(),
            success: function(res) {
                if(res.trim() === 'sukses') { 
                    $('#form-ucapan')[0].reset(); 
                    loadUcapan(); 
                } else { 
                    alert('Gagal tersambung ke server database.'); 
                }
            },
            error: function() {
                alert('Terjadi kesalahan. Pastikan server XAMPP / Database berjalan.');
            },
            complete: function() { 
                // Kembalikan tombol seperti semula
                btn.html(txt).prop('disabled', false); 
            }
        });
    });
});