<?php
require_once '../config/constants.php';
require_once '../includes/helpers.php';
require_once '../config/koneksi.php';

// Atur header sebagai JSON
header('Content-Type: application/json');

// Mulai sesi dan periksa login
startSession();
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

// Periksa apakah warna dikirim
if (!isset($_POST['color'])) {
    echo json_encode(['success' => false, 'message' => 'No color provided']);
    exit;
}

$color = $_POST['color'];

// Validasi format warna hex
if (!preg_match('/^#[a-fA-F0-9]{6}$/', $color)) {
    echo json_encode(['success' => false, 'message' => 'Invalid color format']);
    exit;
}

try {
    // Gunakan query UPSERT (INSERT ... ON DUPLICATE KEY UPDATE) yang lebih efisien
    // Ini akan INSERT jika 'theme_color' belum ada, atau UPDATE jika sudah ada.
    $stmt = $pdo->prepare(
        "INSERT INTO pengaturan (kunci, nilai, deskripsi) 
         VALUES ('theme_color', :nilai, 'Theme primary color') 
         ON DUPLICATE KEY UPDATE nilai = :nilai"
    );
    
    $stmt->bindParam(':nilai', $color);
    $stmt->execute();
    
    // Kirim respons sukses
    echo json_encode(['success' => true, 'message' => 'Theme color updated successfully']);

} catch (PDOException $e) {
    // Kirim respons error jika ada masalah database
    http_response_code(500); // Set status kode error server
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
