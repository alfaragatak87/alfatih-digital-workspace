<?php
require_once '../config/constants.php';
require_once '../includes/helpers.php';
require_once '../config/koneksi.php';

// Start session and check if logged in
startSession();

if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if color is provided
if (!isset($_POST['color'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Color not provided']);
    exit;
}

$color = $_POST['color'];

// Validate color (hex format)
if (!preg_match('/^#[a-f0-9]{6}$/i', $color)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid color format']);
    exit;
}

try {
    // Check if setting exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pengaturan WHERE kunci = 'theme_color'");
    $stmt->execute();
    $exists = $stmt->fetchColumn() > 0;
    
    if ($exists) {
        // Update existing setting
        $stmt = $pdo->prepare("UPDATE pengaturan SET nilai = :nilai WHERE kunci = 'theme_color'");
    } else {
        // Insert new setting
        $stmt = $pdo->prepare("INSERT INTO pengaturan (kunci, nilai, deskripsi) VALUES ('theme_color', :nilai, 'Warna utama tema website')");
    }
    
    $stmt->bindParam(':nilai', $color);
    $stmt->execute();
    
    // Log activity
    if (function_exists('logActivity')) {
        logActivity('Changed theme color to ' . $color);
    }
    
    echo json_encode(['success' => true, 'message' => 'Theme color saved successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>