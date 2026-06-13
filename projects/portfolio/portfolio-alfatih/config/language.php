
<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default language
$defaultLanguage = 'id'; // Indonesia

// Try to get language from settings table
try {
    global $pdo;
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT nilai FROM pengaturan WHERE kunci = 'default_language'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $defaultLanguage = $result['nilai'];
        }
    }
} catch (Exception $e) {
    // Silently fail, use default language
}

// Get language from URL parameter or session
if (isset($_GET['lang']) && in_array($_GET['lang'], ['id', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
} elseif (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = $defaultLanguage;
}

$lang = $_SESSION['lang'];

// Load language files
$translations = [];

// Base translations (English)
$translations['en'] = [
    // Navigation
    'home' => 'Home',
    'about' => 'About',
    'projects' => 'Projects',
    'cv' => 'CV',
    'semester' => 'Semester',
    'blog' => 'Blog',
    'contact' => 'Contact',
    'testimonials' => 'Testimonials',
    'login' => 'Login',
    
    // Hero Section
    'hero_subtitle' => 'Building exceptional digital experiences with clean and efficient code.',
    'view_projects' => 'View Projects',
    'contact_me' => 'Contact Me',
    
    // Project Section
    'latest_projects' => 'Latest Projects',
    'latest_projects_subtitle' => 'Check out some of my recent work',
    'view_project' => 'View Project',
    'view_all_projects' => 'View All Projects',
    'no_projects' => 'No projects to display yet.',
    
    // Article Section
    'latest_articles' => 'Latest Articles',
    'latest_articles_subtitle' => 'Insights and thoughts on web development and technology',
    'read_more' => 'Read More',
    'no_articles' => 'No articles to display yet.',
    'view_all_articles' => 'View All Articles',
    
    // About Page
    'about_me' => 'About Me',
    'about_subtitle' => 'Get to know me better',
    'my_skills' => 'My Skills',
    'my_skills_subtitle' => 'Technologies & tools I work with',
    'frontend_development' => 'Frontend Development',
    'backend_development' => 'Backend Development',
    'design_marketing' => 'Design & Marketing',
    'education_journey' => 'Education Journey',
    'my_interests' => 'My Interests',
    'interests_subtitle' => 'Things I love beyond coding',
    'work_together' => 'Interested in working together?',
    'work_together_subtitle' => 'Let\'s bring your ideas to life! I\'m always open to discussing new projects, creative ideas or opportunities to be part of your vision.',
    'get_in_touch' => 'Get In Touch',
    
    // CV Page
    'curriculum_vitae' => 'Curriculum Vitae',
    'cv_subtitle' => 'My professional journey and qualifications',
    'download_cv' => 'Download CV',
    'cv_not_available' => 'CV file not available yet.',
    'personal_information' => 'Personal Information',
    'full_name' => 'Full Name',
    'email' => 'Email',
    'phone' => 'Phone',
    'location' => 'Location',
    'current_status' => 'Current Status',
    'education' => 'Education',
    'present' => 'Present',
    
    // Contact Page
    'contact_heading' => 'Contact Me',
    'contact_subtitle' => 'Get in touch for collaboration or inquiries',
    'your_name' => 'Your Name',
    'your_email' => 'Your Email',
    'your_subject' => 'Subject',
    'your_message' => 'Your Message',
    'send_message' => 'Send Message',
    
    // Testimonials Page
    'client_testimonials' => 'Client Testimonials',
    'testimonials_subtitle' => 'What people say about working with me',
    'share_experience' => 'Share Your Experience',
    'experience_subtitle' => 'Your feedback helps me improve and grow',
    'your_position' => 'Your Position',
    'company_organization' => 'Company/Organization',
    'your_testimonial' => 'Your Testimonial',
    'your_photo' => 'Your Photo',
    'photo_note' => 'Upload a profile picture (optional). Maximum size: 2MB',
    'submit_testimonial' => 'Submit Testimonial',
    'no_testimonials' => 'No testimonials yet',
    'no_testimonials_subtitle' => 'Check back soon for client testimonials and reviews.',
    
    // Semester Page
    'semester_data' => 'Semester Data',
    'select_semester' => 'Select a semester to view courses and materials',
    'semester_specific' => 'Semester %d Data',
    'courses_materials' => 'Courses and materials for Semester %d',
    'no_courses' => 'No courses found',
    'no_courses_yet' => 'There are no courses or materials available for Semester %d yet.',
    'download_material' => 'Download Material',
    
    // Footer
    'connect_with_me' => 'Connect With Me',
    'navigation' => 'Navigation',
    'all_rights_reserved' => 'All Rights Reserved.'
];

// Indonesian translations
$translations['id'] = [
    // Navigation
    'home' => 'Beranda',
    'about' => 'Tentang',
    'projects' => 'Proyek',
    'cv' => 'CV',
    'semester' => 'Semester',
    'blog' => 'Blog',
    'contact' => 'Kontak',
    'testimonials' => 'Testimoni',
    'login' => 'Masuk',
    
    // Hero Section
    'hero_subtitle' => 'Membangun pengalaman digital yang luar biasa dengan kode yang bersih dan efisien.',
    'view_projects' => 'Lihat Proyek',
    'contact_me' => 'Hubungi Saya',
    
    // Project Section
    'latest_projects' => 'Proyek Terbaru',
    'latest_projects_subtitle' => 'Lihat beberapa karya terbaru saya',
    'view_project' => 'Lihat Proyek',
    'view_all_projects' => 'Lihat Semua Proyek',
    'no_projects' => 'Belum ada proyek untuk ditampilkan.',
    
    // Article Section
    'latest_articles' => 'Artikel Terbaru',
    'latest_articles_subtitle' => 'Wawasan dan pemikiran tentang pengembangan web dan teknologi',
    'read_more' => 'Baca Selengkapnya',
    'no_articles' => 'Belum ada artikel untuk ditampilkan.',
    'view_all_articles' => 'Lihat Semua Artikel',
    
    // About Page
    'about_me' => 'Tentang Saya',
    'about_subtitle' => 'Kenali saya lebih jauh',
    'my_skills' => 'Keahlian Saya',
    'my_skills_subtitle' => 'Teknologi & alat yang saya kuasai',
    'frontend_development' => 'Pengembangan Frontend',
    'backend_development' => 'Pengembangan Backend',
    'design_marketing' => 'Desain & Pemasaran',
    'education_journey' => 'Perjalanan Pendidikan',
    'my_interests' => 'Minat Saya',
    'interests_subtitle' => 'Hal-hal yang saya sukai selain coding',
    'work_together' => 'Tertarik untuk bekerja sama?',
    'work_together_subtitle' => 'Mari wujudkan ide Anda! Saya selalu terbuka untuk membahas proyek baru, ide kreatif, atau kesempatan untuk menjadi bagian dari visi Anda.',
    'get_in_touch' => 'Hubungi Saya',
    
    // CV Page
    'curriculum_vitae' => 'Curriculum Vitae',
    'cv_subtitle' => 'Perjalanan profesional dan kualifikasi saya',
    'download_cv' => 'Unduh CV',
    'cv_not_available' => 'File CV belum tersedia.',
    'personal_information' => 'Informasi Pribadi',
    'full_name' => 'Nama Lengkap',
    'email' => 'Email',
    'phone' => 'Telepon',
    'location' => 'Lokasi',
    'current_status' => 'Status Saat Ini',
    'education' => 'Pendidikan',
    'present' => 'Sekarang',
    
    // Contact Page
    'contact_heading' => 'Hubungi Saya',
    'contact_subtitle' => 'Hubungi untuk kolaborasi atau pertanyaan',
    'your_name' => 'Nama Anda',
    'your_email' => 'Email Anda',
    'your_subject' => 'Subjek',
    'your_message' => 'Pesan Anda',
    'send_message' => 'Kirim Pesan',
    
    // Testimonials Page
    'client_testimonials' => 'Testimoni Klien',
    'testimonials_subtitle' => 'Apa kata orang tentang bekerja dengan saya',
    'share_experience' => 'Bagikan Pengalaman Anda',
    'experience_subtitle' => 'Umpan balik Anda membantu saya meningkatkan dan berkembang',
    'your_position' => 'Posisi Anda',
    'company_organization' => 'Perusahaan/Organisasi',
    'your_testimonial' => 'Testimoni Anda',
    'your_photo' => 'Foto Anda',
    'photo_note' => 'Unggah foto profil (opsional). Ukuran maksimum: 2MB',
    'submit_testimonial' => 'Kirim Testimoni',
    'no_testimonials' => 'Belum ada testimoni',
    'no_testimonials_subtitle' => 'Kunjungi kembali segera untuk testimoni dan ulasan klien.',
    
    // Semester Page
    'semester_data' => 'Data Semester',
    'select_semester' => 'Pilih semester untuk melihat mata kuliah dan materi',
    'semester_specific' => 'Data Semester %d',
    'courses_materials' => 'Mata kuliah dan materi untuk Semester %d',
    'no_courses' => 'Tidak ada mata kuliah yang ditemukan',
    'no_courses_yet' => 'Belum ada mata kuliah atau materi yang tersedia untuk Semester %d.',
    'download_material' => 'Unduh Materi',
    
    // Footer
    'connect_with_me' => 'Terhubung Dengan Saya',
    'navigation' => 'Navigasi',
    'all_rights_reserved' => 'Seluruh Hak Cipta.'
];

// Function to translate text
function __($key) {
    global $translations, $lang;
    
    if (isset($translations[$lang][$key])) {
        return $translations[$lang][$key];
    } elseif (isset($translations['en'][$key])) {
        return $translations['en'][$key];
    }
    
    return $key;
}

// Function to translate text with printf format
function __f($key, ...$args) {
    $text = __($key);
    return sprintf($text, ...$args);
}
?>