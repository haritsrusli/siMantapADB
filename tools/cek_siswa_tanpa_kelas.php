<?php
// Test script untuk memeriksa siswa tanpa kelas
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
    
    // Periksa siswa yang tidak memiliki kelas
    $stmt = $pdo->query("
        SELECT u.id, u.nama_lengkap, u.username, u.id_kelas
        FROM users u
        LEFT JOIN kelas k ON u.id_kelas = k.id
        WHERE u.role = 'siswa' AND (u.id_kelas IS NULL OR k.id IS NULL)
    ");
    
    echo "Siswa yang tidak memiliki kelas yang valid:\n";
    echo str_repeat("-", 40) . "\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    
    // Periksa record absensi manual untuk user tanpa kelas
    $stmt = $pdo->query("
        SELECT am.*, u.nama_lengkap, u.username
        FROM absensi_manual am
        JOIN users u ON am.user_id = u.id
        LEFT JOIN kelas k ON u.id_kelas = k.id
        WHERE u.id_kelas IS NULL OR k.id IS NULL
    ");
    
    echo "\nRecord absensi manual untuk user tanpa kelas:\n";
    echo str_repeat("-", 40) . "\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>