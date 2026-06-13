<?php
include '../koneksi.php';

$sql = "SELECT * FROM ucapan ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $tanggal_format = date('d M Y H:i', strtotime($row['tanggal']));
        
        echo '<div style="background:rgba(255,255,255,0.05); padding:15px; margin-bottom:15px; border-radius:10px; text-align:left; border-left:4px solid var(--warna-gold);">';
        echo '<h4 style="color:var(--warna-gold); margin-bottom:5px; font-family:\'Cinzel\', serif;">' . htmlspecialchars($row['nama']) . ' <span style="font-size:0.7rem; color:#fff; background:var(--warna-sogan); padding:3px 8px; border-radius:10px; margin-left:10px; border:1px solid var(--warna-gold);">' . htmlspecialchars($row['kehadiran']) . '</span></h4>';
        echo '<p style="font-size:0.9rem; margin-bottom:10px; font-family:\'Lora\', serif;">' . htmlspecialchars($row['pesan']) . '</p>';
        echo '<small style="color:rgba(249, 246, 240, 0.5); font-size:0.75rem;"><i class="far fa-clock"></i> ' . $tanggal_format . '</small>';
        echo '</div>';
    }
} else {
    echo '<p style="color: rgba(249, 246, 240, 0.7); font-style: italic;">Belum ada ucapan. Jadilah yang pertama memberikan doa restu!</p>';
}
?>