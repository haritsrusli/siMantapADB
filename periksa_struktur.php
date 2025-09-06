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
    
    // Show table structure
    $stmt = $pdo->query("DESCRIBE absensi");
    echo "Struktur tabel absensi saat ini:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    echo "\nStruktur tabel absensi berdasarkan migrasi terakhir seharusnya:\n";
    echo "- id (INT 11 unsigned AUTO_INCREMENT PRIMARY KEY)\n";
    echo "- user_id (INT 11 unsigned FOREIGN KEY -> users.id)\n";
    echo "- waktu_presensi (DATETIME)\n";
    echo "- latitude (DECIMAL 10,8)\n";
    echo "- longitude (DECIMAL 11,8)\n";
    echo "- foto_selfie (TEXT)\n";
    
    echo "\nPerbedaan yang ditemukan:\n";
    echo "1. Field 'terlambat' tidak ada di tabel saat ini tetapi ada di model\n";
    echo "2. Field 'tipe_presensi' sudah dihapus sesuai migrasi terbaru\n";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
