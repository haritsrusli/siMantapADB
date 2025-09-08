<?php
// Debug script untuk memeriksa data absensi yang diterima view
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
    
    // Jalankan query yang sama dengan yang ada di model
    $stmt = $pdo->prepare("
        SELECT 
            absensi_manual.id, 
            absensi_manual.user_id, 
            absensi_manual.tanggal, 
            absensi_manual.jenis as status_kehadiran, 
            absensi_manual.keterangan, 
            absensi_manual.created_at, 
            absensi_manual.disetujui_oleh, 
            absensi_manual.tanggal_disetujui, 
            users.nama_lengkap as nama_siswa, 
            users.username as nis, 
            kelas.nama_kelas, 
            kelas.tingkat, 
            kelas.jurusan
        FROM absensi_manual
        JOIN users ON users.id = absensi_manual.user_id
        LEFT JOIN kelas ON kelas.id = users.id_kelas
        ORDER BY absensi_manual.tanggal DESC, absensi_manual.created_at DESC
        LIMIT 5
    ");
    $stmt->execute();
    
    echo "Data yang diambil dengan query dari model:\n";
    echo str_repeat("-", 50) . "\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
        echo "Keys: " . implode(', ', array_keys($row)) . "\n";
        echo str_repeat("-", 30) . "\n";
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>
