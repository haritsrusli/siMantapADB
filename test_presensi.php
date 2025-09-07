<?php
// Test script for presensi functionality
require_once 'vendor/autoload.php';

// Load environment variables
$env = @parse_ini_file('.env');

// Database configuration
$host = $env['database.default.hostname'] ?? 'localhost';
$dbname = $env['database.default.database'] ?? 'simantap';
$username = $env['database.default.username'] ?? 'root';
$password = $env['database.default.password'] ?? '';

try {
    // Create connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if absensi table has the 'terlambat' column
    $stmt = $pdo->query("SHOW COLUMNS FROM absensi LIKE 'terlambat'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        echo "Field 'terlambat' tersedia di tabel absensi.\n";
    } else {
        echo "Field 'terlambat' tidak ditemukan di tabel absensi.\n";
        exit(1);
    }
    
    // Test face comparison script
    $pythonPath = "C:\\Users\\User\\AppData\\Local\\Programs\\Python\\Python313\\python.exe";
    
    // Check if Python executable exists
    if (file_exists($pythonPath)) {
        echo "Python executable ditemukan.\n";
    } else {
        echo "Python executable tidak ditemukan di path yang ditentukan.\n";
    }
    
    // Create a simple text file as test image
    $testImage1 = __DIR__ . "\\public\\uploads\\profiles\\test1.txt";
    $testImage2 = __DIR__ . "\\public\\uploads\\profiles\\test2.txt";
    
    // Create test directory if not exists
    $uploadPath = __DIR__ . "\\public\\uploads\\profiles";
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    
    // Create dummy test files if they don't exist
    if (!file_exists($testImage1)) {
        file_put_contents($testImage1, "Test file 1");
        echo "Test file 1 dibuat.\n";
    }
    
    if (!file_exists($testImage2)) {
        file_put_contents($testImage2, "Test file 2");
        echo "Test file 2 dibuat.\n";
    }
    
    // Run face comparison
    $command = '"' . $pythonPath . '" ' . __DIR__ . "\\face_compare.py " . escapeshellarg($testImage1) . " " . escapeshellarg($testImage2) . " 2>&1";
    $output = shell_exec($command);
    
    echo "Output face comparison:\n" . $output . "\n";
    
    // Parse result
    $result = json_decode($output, true);
    if ($result) {
        if (isset($result['error'])) {
            echo "Error dalam face comparison: " . $result['error'] . "\n";
        } else {
            echo "Face comparison berhasil.\n";
            echo "Match: " . ($result['match'] ? 'Ya' : 'Tidak') . "\n";
            if (isset($result['similarity'])) {
                echo "Similarity: " . $result['similarity'] . "\n";
            }
            if (isset($result['warning'])) {
                echo "Warning: " . $result['warning'] . "\n";
            }
        }
    } else {
        echo "Gagal memparse hasil face comparison.\n";
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>