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
    
    // Show tables
    $stmt = $pdo->query("SHOW TABLES");
    echo "Tables in database:\n";
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "- " . $row[0] . "\n";
    }
    
    // Check if absensi table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'absensi'");
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo "\nTabel absensi ditemukan.\n";
        
        // Show table structure
        $stmt = $pdo->query("DESCRIBE absensi");
        echo "\nStruktur tabel absensi:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "\nTabel absensi tidak ditemukan.\n";
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
