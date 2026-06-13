<?php
require_once 'config/constants.php';
require_once 'config/koneksi.php';

// Hanya dapat diakses dari localhost
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die('Akses ditolak');
}

// Generate hash untuk password 'admin123'
$password = 'admin123';
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Reset password untuk user admin
    $stmt = $pdo->prepare("UPDATE admin SET password = :password WHERE username = 'admin'");
    $stmt->bindParam(':password', $passwordHash);
    $stmt->execute();
    
    // Cek apakah berhasil
    if ($stmt->rowCount() > 0) {
        echo "Password berhasil diubah menjadi 'admin123'";
    } else {
        // Cek apakah user admin ada
        $stmt = $pdo->query("SELECT * FROM admin WHERE username = 'admin'");
        if ($stmt->rowCount() == 0) {
            // Buat user admin baru
            $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES ('admin', :password)");
            $stmt->bindParam(':password', $passwordHash);
            $stmt->execute();
            echo "User admin baru dibuat dengan password 'admin123'";
        } else {
            echo "Password sudah diubah sebelumnya";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>