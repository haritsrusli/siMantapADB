<?php
// Test script untuk memeriksa data absensi_manual
require_once '../vendor/autoload.php';

// Database configuration
$host = 'localhost';
$dbname = 'simantap';
$username = 'root';
$password = '';

try {
    // Create connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Periksa semua data dalam tabel absensi_manual
    $stmt = $pdo->query("SELECT * FROM absensi_manual LIMIT 10");
    echo "Data dalam tabel absensi_manual:\n";
    echo str_repeat("-", 50) . "\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
        echo str_repeat("-", 30) . "\n";
    }
    
    // Periksa struktur tabel absensi_manual
    $stmt = $pdo->query("DESCRIBE absensi_manual");
    echo "\nStruktur tabel absensi_manual:\n";
    echo str_repeat("-", 30) . "\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
