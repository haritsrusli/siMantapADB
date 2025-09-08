<?php
// Test script untuk memeriksa fungsi model
require_once '../vendor/autoload.php';

// Load CodeIgniter
$paths = new \Config\Paths();
require_once '../app/Config/Constants.php';
require_once '../vendor/codeigniter4/framework/system/bootstrap.php';

use App\Models\AbsensiManual;

try {
    // Buat instance model
    $absensiManualModel = new AbsensiManual();
    
    // Panggil fungsi getAbsensiManualWithSiswa
    $filters = [
        'tanggal' => date('Y-m-d')
    ];
    
    $result = $absensiManualModel->getAbsensiManualWithSiswa($filters);
    $data = $result->findAll();
    
    echo "Data yang diambil dari model:\n";
    echo str_repeat("-", 40) . "\n";
    print_r($data);
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
