<?php
// Load .env file
$env = parse_ini_file('.env');

// Database configuration
$host = $env['database.default.hostname'] ?? 'localhost';
$dbname = $env['database.default.database'] ?? 'simantap';
$username = $env['database.default.username'] ?? 'root';
$password = $env['database.default.password'] ?? '';

try {
    // Create connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Add 'terlambat' column to absensi table
    $sql = "ALTER TABLE absensi ADD COLUMN terlambat TINYINT(1) DEFAULT 0 AFTER foto_selfie";
    $pdo->exec($sql);
    
    echo "Kolom 'terlambat' berhasil ditambahkan ke tabel absensi.\n";
    
    // Verify the column was added
    $stmt = $pdo->query("DESCRIBE absensi");
    echo "\nStruktur tabel absensi saat ini:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
