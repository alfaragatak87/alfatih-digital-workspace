<?php
require_once '../config/koneksi.php';

// Get project by ID for AJAX call
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM proyek WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $project = $stmt->fetch();
        
        if ($project) {
            echo json_encode([
                'success' => true,
                'project' => $project
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Project not found'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No project ID provided'
    ]);
}
?>