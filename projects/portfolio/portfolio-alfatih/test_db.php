<?php
// Include database connection
require_once 'config/koneksi.php';

// Test the connection
try {
    $stmt = $pdo->query("SELECT 1");
    echo "<h2>Database connection successful!</h2>";
    
    // Check if the database has tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "<h3>Tables in database:</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . htmlspecialchars($table) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>No tables found in the database. Please run the SQL script to create tables.</p>";
    }
    
} catch (PDOException $e) {
    echo "<h2>Database connection failed!</h2>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    
    // Display configuration for debugging
    echo "<h3>Current Database Configuration:</h3>";
    echo "<ul>";
    echo "<li>Host: " . DB_HOST . "</li>";
    echo "<li>Database: " . DB_NAME . "</li>";
    echo "<li>User: " . DB_USER . "</li>";
    echo "<li>Password: " . (empty(DB_PASS) ? "(empty)" : "(set)") . "</li>";
    echo "</ul>";
}
?>