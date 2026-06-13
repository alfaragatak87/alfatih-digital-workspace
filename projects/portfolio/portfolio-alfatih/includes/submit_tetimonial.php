<?php
require_once '../config/constants.php';
require_once '../config/koneksi.php';
require_once '../includes/helpers.php';

// Check if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $nama = trim($_POST['nama'] ?? '');
    $posisi = trim($_POST['posisi'] ?? '');
    $perusahaan = trim($_POST['perusahaan'] ?? '');
    $testimonial = trim($_POST['testimonial'] ?? '');
    $rating = (int)($_POST['rating'] ?? 5);
    
    // Validate data
    $errors = [];
    
    if (empty($nama)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($testimonial)) {
        $errors[] = 'Testimonial is required';
    }
    
    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Rating must be between 1 and 5';
    }
    
    // If no errors, process form
    if (empty($errors)) {
        // Upload photo if provided
        $foto = '';
        if (!empty($_FILES['foto']['name'])) {
            $uploadDir = '../uploads/testimonials/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExtension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $newFileName = 'testimonial_' . time() . '.' . $fileExtension;
            $targetPath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath)) {
                $foto = $newFileName;
            } else {
                setAlert('error', 'Failed to upload photo');
                header('Location: ' . BASE_URL . '/pages/testimonials.php');
                exit;
            }
        }
        
        try {
            // Insert testimonial into database (inactive by default until approved)
            $stmt = $pdo->prepare("INSERT INTO testimonials (nama, posisi, perusahaan, testimonial, foto, rating, aktif) VALUES (:nama, :posisi, :perusahaan, :testimonial, :foto, :rating, 0)");
            $stmt->bindParam(':nama', $nama);
            $stmt->bindParam(':posisi', $posisi);
            $stmt->bindParam(':perusahaan', $perusahaan);
            $stmt->bindParam(':testimonial', $testimonial);
            $stmt->bindParam(':foto', $foto);
            $stmt->bindParam(':rating', $rating);
            $stmt->execute();
            
            setAlert('success', 'Thank you for your testimonial! It will be reviewed and published soon.');
        } catch (PDOException $e) {
            setAlert('error', 'Error: ' . $e->getMessage());
        }
    } else {
        // Set error message
        setAlert('error', implode('<br>', $errors));
    }
    
    // Redirect back to testimonials page
    header('Location: ' . BASE_URL . '/pages/testimonials.php');
    exit;
} else {
    // If not POST request, redirect to testimonials page
    header('Location: ' . BASE_URL . '/pages/testimonials.php');
    exit;
}