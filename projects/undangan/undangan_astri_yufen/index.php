<?php
include 'koneksi.php';
$nama_tamu = isset($_GET['to']) ? htmlspecialchars($_GET['to']) : "Tamu Undangan";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Undangan Pernikahan Astri & Yufen</title>
    
    <!-- PREVIEW LINK UNTUK WHATSAPP / SOSMED -->
    <!-- PREVIEW LINK UNTUK WHATSAPP -->
    <meta property="og:title" content="Undangan Pernikahan Astri & Yufen">
    <meta property="og:description" content="Tanpa mengurangi rasa hormat, kami mengundang Bapak/Ibu/Saudara/i untuk hadir di hari bahagia kami.">
    <meta property="og:image" content="https://alfamuhammad.my.id/image/preview-undangan.jpg">
    <meta property="og:url" content="https://alfamuhammad.my.id/">
    <meta property="og:type" content="website">
    
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,600;1,400&family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600&family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link rel="stylesheet" href="css/menikah.css">
</head>
<body class="no-scroll">

    <!-- EFEK GUNUNGAN MEMBELAH SAAT NAVIGASI DIKLIK -->
    <div id="global-gate" class="gate-hidden">
        <img src="image/gunungan.png" class="global-gate-img global-gate-left" alt="gate">
        <img src="image/gunungan.png" class="global-gate-img global-gate-right" alt="gate">
    </div>

    <!-- PEMBUNGKUS UTAMA LAYAR HP -->
    <div id="mobile-wrapper">
        
        <audio id="backsound" loop>
            <source src="audio/gending.mp3" type="audio/mpeg">
        </audio>

        <!-- ================= 1. COVER DEPAN ================= -->
        <section id="cover" class="cover-wrapper">
            <div id="particles-container"></div>
            
            <img src="image/gunungan.png" class="gunungan-cover gate-left" alt="Gunungan Kiri">
            <img src="image/gunungan.png" class="gunungan-cover gate-right" alt="Gunungan Kanan">
            
            <div class="cover-content text-center">
                <h3 class="judul-jawa gold-text">Pawiwahan Ageng</h3>
                <h1 class="nama-cover mb-4">Astri & Yufen</h1>
                
                <div class="frame-cover-utama mx-auto">
                    <img src="image/karakter-jawa.png" alt="Astri Yufen" class="cover-img animate-float">
                </div>

                <div class="kepada-yth box-kaca-dark mx-auto mt-4">
                    <p class="teks-kecil">Katur Dumateng Bpk/Ibu/Sdr/i:</p>
                    <h2 class="nama-tamu"><?php echo $nama_tamu; ?></h2>
                    <p class="teks-sangat-kecil">*Mohon maaf bila ada kesalahan penulisan nama/gelar</p>
                </div>
                
                <div class="w-100 d-flex-center mt-4">
                    <button id="btn-buka" class="btn-solid-gold">
                        <i class="fas fa-envelope-open-text"></i> BUKA UNDANGAN
                    </button>
                </div>
            </div>
        </section>

        <!-- ================= NAVIGASI BAWAH ================= -->
        <nav class="bottom-nav" id="bottom-nav" style="display: none;">
            <ul>
                <li><a href="#mempelai" class="nav-link" data-target="mempelai"><i class="fas fa-heart"></i><span>Mempelai</span></a></li>
                <li><a href="#acara" class="nav-link" data-target="acara"><i class="fas fa-calendar-alt"></i><span>Acara</span></a></li>
                <li><a href="#lovestory" class="nav-link" data-target="lovestory"><i class="fas fa-book-open"></i><span>Cerita</span></a></li>
                <li><a href="#gift" class="nav-link" data-target="gift"><i class="fas fa-gift"></i><span>Hadiah</span></a></li>
                <li><a href="#rsvp" class="nav-link" data-target="rsvp"><i class="fas fa-envelope"></i><span>Ucapan</span></a></li>
            </ul>
        </nav>

        <!-- ================= KONTEN UTAMA ================= -->
        <main id="konten-utama" style="display: none;">
            
            <!-- 2. PEMBUKA & AYAT SUCI (SUDAH DIPERBAIKI ESTETIKANYA) -->
            <section id="mukadimah" class="section-dark pattern-bg text-center pb-3 pt-5">
                <div class="container mx-auto" data-aos="fade-up">
                    
                    <!-- Bismillah diberi class khusus agar bisa diedit ukurannya -->
                    <img src="image/bismillah.png" alt="Bismillah" class="bismillah-img pulse-slow mx-auto d-block">
                    
                    <!-- Salam diganti Font Jawa -->
                    <h2 class="judul-jawa gold-text salam-pembuka">Assalamu'alaikum Warahmatullahi Wabarakatuh</h2>
                    
                    <p class="teks-formal mt-3">Maha Suci Allah yang telah menciptakan makhluk-Nya berpasang-pasangan. Dengan memohon rahmat dan ridho Allah Subhanahu Wa Ta'ala, kami bermaksud menyelenggarakan acara pernikahan putra-putri kami.</p>
                    
                    <div class="ayat-suci mt-4 box-kaca-light mx-auto">
                        <p class="teks-arab mb-3">وَمِنْ اٰيٰتِهٖٓ اَنْ خَلَقَ لَكُمْ مِّنْ اَنْفُسِكُمْ اَزْوَاجًا لِّتَسْكُنُوْٓا اِلَيْهَا وَجَعَلَ بَيْنَكُمْ مَّوَدَّةً وَّرَحْمَةً ۗ اِنَّ فِيْ ذٰلِكَ لَاٰيٰتٍ لِّقَوْمٍ يَّتَفَكَّرُوْنَ</p>
                        <p class="teks-arti">"Dan di antara tanda-tanda (kebesaran)-Nya ialah Dia menciptakan pasangan-pasangan untukmu dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya, dan Dia menjadikan di antaramu rasa kasih dan sayang. Sungguh, pada yang demikian itu benar-benar terdapat tanda-tanda (kebesaran Allah) bagi kaum yang berpikir."</p>
                        <p class="teks-surat mt-2 fw-bold">(QS. Ar-Rum: 21)</p>
                    </div>
                </div>
            </section>

            <!-- 3. PROFIL MEMPELAI -->
            <section id="mempelai" class="section-darker pattern-bg text-center pt-5">
                <div class="container mx-auto">
                    <h2 class="judul-jawa gold-text mb-5" data-aos="zoom-in">Sang Mempelai</h2>
                    
                    <div class="mempelai-box mx-auto" data-aos="fade-up">
                        <div class="frame-foto-mempelai mx-auto">
                            <img src="image/karakter_wanita.png" alt="Astri" class="img-mempelai animate-float">
                        </div>
                        <h1 class="nama-elegan gold-text mt-4">Astri Rizki Amalia</h1>
                        <p class="teks-formal mt-2">Putri Pertama dari Keluarga<br><span class="fw-bold gold-text">Bpk. Achmad Zainuri & Ibu Sukarti</span></p>
                        <a href="https://www.instagram.com/astrirzk_?" target="_blank" class="btn-medsos mt-3"><i class="fab fa-instagram"></i> @astrirzk_</a>
                    </div>
                    
                    <h2 class="ampersand heart-beat my-5">&</h2>
                    
                    <div class="mempelai-box mx-auto mb-4" data-aos="fade-up">
                        <div class="frame-foto-mempelai mx-auto">
                            <img src="image/karakter_laki-laki.png" alt="Yufen" class="img-mempelai animate-float-delay">
                        </div>
                        <h1 class="nama-elegan gold-text mt-4">Yufen Barkanis</h1>
                        <p class="teks-formal mt-2">Putra Kedua dari Keluarga<br><span class="fw-bold gold-text">Bpk. Kanis Laman Barkanis & Ibu Erny Rif'atin</span></p>
                        <a href="https://www.instagram.com/ypnnn.b" target="_blank" class="btn-medsos mt-3"><i class="fab fa-instagram"></i> @ypnnn.b</a>
                    </div>
                </div>
            </section>

            <!-- 4. LOVE STORY -->
            <section id="lovestory" class="section-dark text-center">
                <div class="container mx-auto">
                    <h2 class="judul-jawa gold-text mb-3" data-aos="fade-up">Perjalanan Cinta</h2>
                    <p class="teks-formal mb-5 opacity-80" data-aos="fade-up">Tidak ada yang kebetulan, semua sudah digariskan oleh Sang Maha Pencipta.</p>
                    
                    <div class="timeline-mobile mx-auto mt-4 text-left">
                        <div class="timeline-item-m" data-aos="fade-up">
                            <div class="dot-m"></div>
                            <div class="content-m box-kaca-dark">
                                <h3 class="judul-timeline gold-text">Awal Bertemu</h3>
                                <p class="teks-formal">Pertemuan pertama kami yang sederhana namun membekas di hati, memulai lembaran cerita baru.</p>
                            </div>
                        </div>
                        <div class="timeline-item-m" data-aos="fade-up">
                            <div class="dot-m"></div>
                            <div class="content-m box-kaca-dark">
                                <h3 class="judul-timeline gold-text">Khitbah (Lamaran)</h3>
                                <p class="teks-formal">Dengan restu kedua orang tua, kami melangkah ke jenjang yang lebih serius untuk menyatukan niat baik.</p>
                            </div>
                        </div>
                        <div class="timeline-item-m" data-aos="fade-up">
                            <div class="dot-m"></div>
                            <div class="content-m box-kaca-dark">
                                <h3 class="judul-timeline gold-text">Pernikahan</h3>
                                <p class="teks-formal">5 Juni 2026, hari dimana kami berikrar suci untuk mengarungi bahtera rumah tangga bersama.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 5. ACARA -->
            <section id="acara" class="section-darker pattern-bg text-center">
                <div class="container mx-auto">
                    <h2 class="judul-jawa gold-text mb-2" data-aos="zoom-in">Rangkaian Acara</h2>
                    <p class="teks-formal mb-4">InsyaAllah akan diselenggarakan pada:</p>
                    
                    <div id="hitungmundur" class="box-countdown mx-auto mb-5" data-aos="fade-up"></div>

                    <!-- JATIROTO -->
                    <div class="card-acara box-kaca-light mx-auto mb-4" data-aos="fade-up">
                        <h3 class="judul-acara gold-text mb-3">Akad & Resepsi 1</h3>
                        <div class="tanggal-box d-flex-center">
                            <p class="hari">JUMAT</p>
                            <p class="angka-tgl mx-3">05</p>
                            <p class="bulan">JUNI<br>2026</p>
                        </div>
                        <hr class="garis-emas my-4">
                        <div class="info-waktu-lokasi text-left">
                            <p><i class="far fa-clock gold-text w-icon"></i> Pukul 10.00 WIB - Selesai</p>
                            <p class="mt-3"><i class="fas fa-map-marker-alt gold-text w-icon"></i> <b>Lumajang</b><br><span class="teks-formal">Belakang Rs Jatiroto, Jalan Dusun Krajan, RT.5/RW.6, Kec. Jatiroto.</span></p>
                        </div>
                        <div class="map-responsive mt-3">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15797.688!2d113.3888!3d-8.1599!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zOMKwMDknMzUuNiJTIDExM8KwMjMnMTkuNyJF!5e0!3m2!1sen!2sid!4v1700000000000" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                        <a href="https://maps.app.goo.gl/CHua7noibK8o3FTd9" target="_blank" class="btn-solid-gold w-100 mt-4"><i class="fas fa-map-marked-alt"></i> Buka Petunjuk Arah</a>
                    </div>

                    <!-- SIDOARJO -->
                    <div class="card-acara box-kaca-light mx-auto" data-aos="fade-up">
                        <h3 class="judul-acara gold-text mb-3">Resepsi 2</h3>
                        <div class="tanggal-box d-flex-center">
                            <p class="hari">MINGGU</p>
                            <p class="angka-tgl mx-3">07</p>
                            <p class="bulan">JUNI<br>2026</p>
                        </div>
                        <hr class="garis-emas my-4">
                        <div class="info-waktu-lokasi text-left">
                            <p><i class="far fa-clock gold-text w-icon"></i> Sore - Selesai</p>
                            <p class="mt-3"><i class="fas fa-map-marker-alt gold-text w-icon"></i> <b>Sidoarjo</b><br><span class="teks-formal">Jl. Gajah Magersari Gang II No. 29, RT.12/RW.4, Magersari.</span></p>
                        </div>
                        <div class="map-responsive mt-3">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3956.12!2d112.7123!3d-7.4123!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zN8KwMjQnNDQuMyJTIDExMsKwNDInNDQuMyJF!5e0!3m2!1sen!2sid!4v1700000000000" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                        <a href="https://maps.app.goo.gl/g2u55g35JtvJ8CeD8" target="_blank" class="btn-solid-gold w-100 mt-4"><i class="fas fa-map-marked-alt"></i> Buka Petunjuk Arah</a>
                    </div>
                </div>
            </section>

            <!-- 6. GIFT -->
            <section id="gift" class="section-dark text-center pb-5">
                <div class="container mx-auto" data-aos="zoom-in">
                    <h2 class="judul-jawa gold-text mb-3">Wedding Gift</h2>
                    <p class="teks-formal mb-4 opacity-80">Doa restu Bapak/Ibu/Saudara/i merupakan karunia terindah bagi kami. Namun, apabila bermaksud memberikan tanda kasih, dapat melalui:</p>
                    
                    <div class="box-kaca-dark mx-auto mb-4 text-center">
                        <i class="fas fa-credit-card fa-3x gold-text mb-3 animate-bounce"></i>
                        <h3 class="judul-timeline">Transfer Bank BCA</h3>
                        <p class="rek-num gold-text mt-2" style="font-size: 2rem; font-weight: bold; letter-spacing: 2px;">4290977181</p>
                        <p class="teks-formal fw-bold mt-1">A.N ASTRI RIZKI AMALIA</p>
                        <button class="btn-gold-outline btn-copy mt-4 w-100" data-clipboard-text="4290977181"><i class="far fa-copy"></i> Salin Nomor Rekening</button>
                    </div>

                    <div class="box-kaca-dark mx-auto text-left">
                        <h3 class="judul-timeline gold-text text-center mb-4"><i class="fas fa-gift"></i> Kirim Kado Fisik</h3>
                        
                        <p class="gold-text fw-bold mb-1"><i class="fas fa-map-pin"></i> Alamat Astri:</p>
                        <p class="teks-formal mb-3">Belakang Rs Jatiroto, Jalan Dusun Krajan, RT.5/RW.6, Kec. Jatiroto, Kab. Lumajang.</p>
                        
                        <p class="gold-text fw-bold mb-1"><i class="fas fa-map-pin"></i> Alamat Yufen:</p>
                        <p class="teks-formal">Jalan Gajah Magersari Gang II No. 29, RT.12/RW.4, Magersari, Kab. Sidoarjo.</p>
                    </div>
                </div>
            </section>

            <!-- 7. RSVP & PENUTUP -->
            <section id="rsvp" class="section-darker pattern-bg text-center pb-5">
                <div class="container mx-auto" data-aos="fade-up">
                    <h2 class="judul-jawa gold-text mb-3">Buku Tamu</h2>
                    <p class="teks-formal mb-4">Silakan tinggalkan pesan dan konfirmasi kehadiran Anda pada kolom di bawah ini.</p>
                    
                    <div class="guestbook-container box-kaca-light mx-auto">
                        <form id="form-ucapan">
                            <div class="input-group-modern">
                                <input type="text" name="nama" placeholder="Nama Lengkap Anda" required class="input-form-premium text-center">
                            </div>
                            <div class="input-group-modern">
                                <select name="kehadiran" required class="input-form-premium text-center" style="text-align-last: center;">
                                    <option value="" disabled selected>-- Konfirmasi Kehadiran --</option>
                                    <option value="Hadir">InsyaAllah Hadir</option>
                                    <option value="Tidak Hadir">Maaf, Tidak Bisa Hadir</option>
                                </select>
                            </div>
                            <div class="input-group-modern">
                                <textarea name="pesan" placeholder="Tulis doa & ucapan untuk kedua mempelai..." required class="input-form-premium" style="height: 100px; resize:none;"></textarea>
                            </div>
                            <button type="submit" class="btn-solid-gold w-100 mt-2"><i class="fas fa-paper-plane"></i> Kirim Ucapan</button>
                        </form>
                        
                        <a href="https://wa.me/6285942842455?text=Halo%20Mbak%20Astri,%20saya%20sudah%20menerima%20undangan%20pernikahannya.%20InsyaAllah%20saya%20akan%20hadir." target="_blank" class="btn-wa-solid mt-3">
                            <i class="fab fa-whatsapp"></i> Konfirmasi via WhatsApp
                        </a>
                        
                        <hr class="garis-emas my-4">
                        <h3 class="judul-timeline gold-text text-left mb-3">Daftar Doa & Ucapan</h3>
                        <div class="list-ucapan-scroll" id="list-ucapan" style="text-align: left; max-height: 350px; overflow-y: auto;"></div>
                    </div>
                </div>
                
                <div class="hormat-kami mt-5 pb-5 text-center" data-aos="fade-up">
                    <p class="teks-formal mx-auto" style="max-width: 85%;">Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir untuk memberikan doa restu kepada kedua mempelai.</p>
                    <p class="teks-formal mt-3 mb-4">Wassalamu'alaikum Warahmatullahi Wabarakatuh</p>
                    
                    <p class="teks-kecil mb-1 opacity-80">Hormat kami yang berbahagia,</p>
                    <p class="teks-formal fw-bold gold-text mb-1">Kel. Bpk. Achmad Zainuri & Ibu Sukarti</p>
                    <p class="teks-formal fw-bold gold-text mb-4">Kel. Bpk. Kanis Laman Barkanis & Ibu Erny Rif'atin</p>
                    
                    <img src="image/gunungan.png" alt="gunungan" style="width: 70px; opacity:0.3; margin-bottom:15px;">
                    <h1 class="nama-cover gold-text">Astri & Yufen</h1>
                </div>
                
                <div style="height: 90px;"></div>
            </section>
            
            <div id="music-controller" class="music-active"><i class="fas fa-music"></i></div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="js/clipboard.min.js"></script>
    <script src="js/jquery.countdown.js"></script>
    <script src="js/menikah.js"></script>
</body>
</html>