<?php
// Test script untuk memeriksa perbaikan presensi manual
require_once 'vendor/autoload.php';

// Database configuration
$host = 'localhost';
$dbname = 'simantap';
$username = 'root';
$password = '';

try {
    // Create connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Periksa struktur tabel absensi_manual setelah perbaikan
    $stmt = $pdo->query("DESCRIBE absensi_manual");
    echo "Struktur tabel absensi_manual setelah perbaikan:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    // Periksa apakah ada data dengan jenis 'hadir'
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM absensi_manual WHERE jenis = ?");
    $stmt->execute(['hadir']);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "\nJumlah data dengan jenis 'hadir': " . $row['count'] . "\n";
    
    echo "\nPerbaikan telah selesai. Silakan uji aplikasi Anda.\n";
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
