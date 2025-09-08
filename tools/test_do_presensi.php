<?php
// Test script for doPresensi function
require_once '../vendor/autoload.php';
require_once '../app/Controllers/Siswa.php';

// Mock session
$_SESSION['isLoggedIn'] = true;
$_SESSION['role'] = 'siswa';
$_SESSION['user_id'] = 1;

// Mock request data
class MockRequest {
    public function getJSON() {
        return (object)[
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'foto_selfie' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg=='
        ];
    }
}

// Mock response
class MockResponse {
    private $data = [];
    
    public function setJSON($data) {
        $this->data = $data;
        return $this;
    }
    
    public function getData() {
        return $this->data;
    }
}

// Test doPresensi function
try {
    $siswaController = new \App\Controllers\Siswa();
    
    // Mock request and response
    $siswaController->request = new MockRequest();
    $siswaController->response = new MockResponse();
    
    // Call doPresensi method
    $result = $siswaController->doPresensi();
    
    // Get response data
    $responseData = $siswaController->response->getData();
    
    echo "Hasil presensi:\n";
    print_r($responseData);
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>
