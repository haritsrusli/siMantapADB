<?php
// Test script untuk memeriksa notifikasi presensi manual
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
    
    // Periksa apakah ada data presensi manual
    $stmt = $pdo->query("SELECT * FROM absensi_manual WHERE tanggal = CURDATE() LIMIT 5");
    echo "Data presensi manual hari ini:\n";
    echo str_repeat("-", 50) . "\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
        echo str_repeat("-", 30) . "\n";
    }
    
    // Periksa apakah ada data presensi otomatis
    $stmt = $pdo->query("SELECT * FROM absensi WHERE DATE(waktu_presensi) = CURDATE() LIMIT 5");
    echo "\nData presensi otomatis hari ini:\n";
    echo str_repeat("-", 50) . "\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
        echo str_repeat("-", 30) . "\n";
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
