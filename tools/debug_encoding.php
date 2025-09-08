<?php
// Test script untuk memeriksa encoding dan karakter spesial
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
    
    // Periksa encoding
    $stmt = $pdo->query("SELECT @@character_set_database, @@collation_database");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Database charset: " . $row['@@character_set_database'] . "\n";
    echo "Database collation: " . $row['@@collation_database'] . "\n\n";
    
    // Periksa data absensi_manual dengan detail
    $stmt = $pdo->query("SELECT id, user_id, tanggal, jenis, HEX(jenis) as jenis_hex FROM absensi_manual LIMIT 5");
    echo "Data absensi_manual dengan detail encoding:\n";
    echo str_repeat("-", 50) . "\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id'] . "\n";
        echo "User ID: " . $row['user_id'] . "\n";
        echo "Tanggal: " . $row['tanggal'] . "\n";
        echo "Jenis: '" . $row['jenis'] . "'\n";
        echo "Jenis (HEX): " . $row['jenis_hex'] . "\n";
        echo "Length: " . strlen($row['jenis']) . "\n";
        echo str_repeat("-", 20) . "\n";
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
