<?php
// Load .env file
$env = parse_ini_file('../.env');

// Database configuration
$host = $env['database.default.hostname'] ?? 'localhost';
$dbname = $env['database.default.database'] ?? 'simantap';
$username = $env['database.default.username'] ?? 'root';
$password = $env['database.default.password'] ?? '';

try {
    // Create connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if absensi table has data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM absensi");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Jumlah data dalam tabel absensi: " . $row['count'] . "\n";
    
    if ($row['count'] > 0) {
        // Show sample data
        $stmt = $pdo->query("SELECT * FROM absensi LIMIT 5");
        echo "\nContoh data:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
    } else {
        echo "Tabel absensi kosong.\n";
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
