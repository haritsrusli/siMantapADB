<?php
// Test script untuk memeriksa apakah data jenis muncul dengan benar
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
    
    // Jalankan query yang sama seperti di model
    $sql = "SELECT 
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
            LEFT JOIN users ON users.id = absensi_manual.user_id
            LEFT JOIN kelas ON kelas.id = users.id_kelas
            ORDER BY absensi_manual.tanggal DESC, absensi_manual.created_at DESC
            LIMIT 5";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Data yang diambil dengan query dari model (dengan LEFT JOIN):\n";
    echo str_repeat("-", 60) . "\n";
    
    foreach ($results as $index => $row) {
        echo "Record " . ($index + 1) . ":\n";
        echo "  ID: " . $row['id'] . "\n";
        echo "  User ID: " . $row['user_id'] . "\n";
        echo "  Tanggal: " . $row['tanggal'] . "\n";
        echo "  Status Kehadiran (alias dari jenis): '" . $row['status_kehadiran'] . "'\n";
        echo "  Jenis (dari tabel): '" . $row['status_kehadiran'] . "'\n";
        echo "  Nama Siswa: " . $row['nama_siswa'] . "\n";
        echo "  NIS: " . $row['nis'] . "\n";
        echo str_repeat("-", 30) . "\n";
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>