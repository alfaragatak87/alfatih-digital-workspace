<?php
// ============================================================
// ALFATIH DIGITAL WORKSPACE — core/helpers.php
// Fokus: Kumpulan fungsi utilitas sistem murni (tanpa DB).
// Berisi: h(), formatBytes(), getFileIcon(), getPreviewType()
// ============================================================

/**
 * Sanitasi output HTML untuk mencegah XSS.
 * Gunakan ini setiap kali menampilkan data user ke HTML.
 *
 * @param  string|null $str  String yang akan di-escape.
 * @return string            String yang aman untuk ditampilkan di HTML.
 */
function h(?string $str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Mengkonversi ukuran bytes menjadi format yang mudah dibaca manusia.
 * Contoh: 1048576 → "1 MB"
 *
 * @param  int    $bytes  Ukuran dalam bytes.
 * @return string         Representasi ukuran yang terbaca (e.g., "2.5 MB").
 */
function formatBytes(int $bytes): string
{
    if ($bytes <= 0) return '0 B';
    $k     = 1024;
    $sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i     = (int) floor(log(max($bytes, 1)) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

/**
 * Mengembalikan ikon Font Awesome dan warna yang sesuai
 * berdasarkan ekstensi file.
 *
 * @param  string $filename  Nama file lengkap (dengan ekstensi).
 * @return array             Array berisi [string $fa_icon_class, string $hex_color].
 *                           Contoh: ['fa-file-pdf', '#dc2626']
 */
function getFileIcon(string $filename): array
{
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    $map = [
        // Dokumen
        'pdf'  => ['fa-file-pdf',        '#dc2626'],
        'doc'  => ['fa-file-word',       '#2563eb'],
        'docx' => ['fa-file-word',       '#2563eb'],
        // Spreadsheet
        'xls'  => ['fa-file-excel',      '#16a34a'],
        'xlsx' => ['fa-file-excel',      '#16a34a'],
        'csv'  => ['fa-file-excel',      '#16a34a'],
        // Presentasi
        'ppt'  => ['fa-file-powerpoint', '#ea580c'],
        'pptx' => ['fa-file-powerpoint', '#ea580c'],
        // Gambar
        'jpg'  => ['fa-file-image',      '#0891b2'],
        'jpeg' => ['fa-file-image',      '#0891b2'],
        'png'  => ['fa-file-image',      '#0891b2'],
        'gif'  => ['fa-file-image',      '#0891b2'],
        'webp' => ['fa-file-image',      '#0891b2'],
        'svg'  => ['fa-file-image',      '#0891b2'],
        // Video
        'mp4'  => ['fa-file-video',      '#7c3aed'],
        'avi'  => ['fa-file-video',      '#7c3aed'],
        // Audio
        'mp3'  => ['fa-file-audio',      '#db2777'],
        'wav'  => ['fa-file-audio',      '#db2777'],
        // Arsip
        'zip'  => ['fa-file-zipper',     '#b45309'],
        'rar'  => ['fa-file-zipper',     '#b45309'],
        // Teks & Log
        'txt'  => ['fa-file-lines',      '#475569'],
        'log'  => ['fa-file-lines',      '#475569'],
        // Kode
        'html' => ['fa-file-code',       '#be185d'],
        'css'  => ['fa-file-code',       '#be185d'],
        'js'   => ['fa-file-code',       '#be185d'],
        'php'  => ['fa-file-code',       '#be185d'],
    ];

    return $map[$ext] ?? ['fa-file', '#94a3b8'];
}

/**
 * Menentukan tipe preview yang tepat berdasarkan ekstensi file.
 * Digunakan oleh right sidebar untuk memutuskan bagaimana
 * menampilkan preview cepat sebuah file.
 *
 * @param  string $filename  Nama file lengkap (dengan ekstensi).
 * @return string            Salah satu dari: 'image', 'pdf', 'video', 'audio', 'text', 'none'.
 */
function getPreviewType(string $filename): string
{
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'])) {
        return 'image';
    }
    if ($ext === 'pdf') {
        return 'pdf';
    }
    if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'])) {
        return 'video';
    }
    if (in_array($ext, ['mp3', 'wav', 'ogg', 'flac', 'aac'])) {
        return 'audio';
    }
    if (in_array($ext, ['txt', 'log', 'csv', 'json', 'xml', 'html', 'css', 'js', 'php', 'md'])) {
        return 'text';
    }

    return 'none';
}
