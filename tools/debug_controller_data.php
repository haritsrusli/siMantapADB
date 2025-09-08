<?php
// Test script untuk memeriksa pengambilan data seperti di controller
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
    
    // Simulasi cara controller mengambil data
    $tanggal = date('Y-m-d');
    $tingkat = '';
    $jurusan = '';
    
    // Query seperti di controller
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
            JOIN users ON users.id = absensi_manual.user_id
            LEFT JOIN kelas ON kelas.id = users.id_kelas
            WHERE absensi_manual.tanggal = ?
            ORDER BY absensi_manual.tanggal DESC, absensi_manual.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tanggal]);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Data yang diambil dengan query seperti di controller:\n";
    echo "Tanggal filter: " . $tanggal . "\n";
    echo str_repeat("-", 50) . "\n";
    
    if (empty($results)) {
        echo "Tidak ada data ditemukan.\n";
    } else {
        foreach ($results as $index => $row) {
            echo "Record " . ($index + 1) . ":\n";
            foreach ($row as $key => $value) {
                echo "  $key: " . (is_null($value) ? 'NULL' : $value) . "\n";
            }
            echo str_repeat("-", 30) . "\n";
        }
    }
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>