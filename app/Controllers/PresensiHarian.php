<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Absensi;
use App\Models\AbsensiManual;
use App\Models\Kelas;
use App\Models\User;

class PresensiHarian extends BaseController
{
    // Enable CSRF protection
    protected $helpers = ['form'];
    
    public function index()
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $kelasModel = new Kelas();
        $userModel = new User();
        $absensiManualModel = new AbsensiManual();
        
        // Get filter parameters from request
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');
        $tingkat = $this->request->getGet('tingkat');
        $jurusan = $this->request->getGet('jurusan');
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 20;

        $data['kelas'] = $kelasModel->findAll();
        $data['siswa'] = [];
        $data['absensi_records'] = [];
        $data['pager'] = [
            'totalRecords' => 0,
            'totalPages' => 0,
            'currentPage' => 1,
            'perPage' => $perPage
        ];

        // Only process if at least one filter is provided
        if (!empty($tingkat) || !empty($jurusan)) {
            // Build filters for absensi manual records
            $filters = [
                'tanggal' => $tanggal,
                'tingkat' => $tingkat,
                'jurusan' => $jurusan
            ];
            
            // Get absensi manual records with student info
            $recordsQuery = $absensiManualModel->getAbsensiManualWithSiswa($filters);
            
            // Get total records for pagination
            $totalRecords = $recordsQuery->countAllResults(false);
            $totalPages = ceil($totalRecords / $perPage);
            $page = max(1, min($page, $totalPages));
            
            // Get records for current page
            $offset = ($page - 1) * $perPage;
            $data['absensi_records'] = $recordsQuery->limit($perPage, $offset)->findAll();
            
            // Debug: Log records count
            log_message('debug', 'Total records in database: ' . $totalRecords);
            log_message('debug', 'Absensi records count (current page): ' . count($data['absensi_records']));
            log_message('debug', 'Absensi records data: ' . print_r($data['absensi_records'], true));
            
            // Pass pagination data
            $data['pager'] = [
                'totalRecords' => $totalRecords,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'perPage' => $perPage
            ];
            
            // Get ALL students based on filters (regardless of whether they have records or not)
            $builder = $userModel->where('role', 'siswa');
            
            if (!empty($tingkat)) {
                $builder->join('kelas', 'kelas.id = users.id_kelas')
                        ->where('kelas.tingkat', $tingkat);
            }
            
            if (!empty($jurusan)) {
                if (empty($tingkat)) {
                    $builder->join('kelas', 'kelas.id = users.id_kelas');
                }
                $builder->where('kelas.jurusan', $jurusan);
            }
            
            $students = $builder->orderBy('users.nama_lengkap', 'ASC')->findAll();
                                         
            // Filter students who don't have absensi record yet for this date
            // AND who haven't done GPS/face recognition presensi
            $data['siswa'] = [];
            foreach ($students as $student) {
                // Debug: Log student data
                log_message('debug', 'Checking student: ' . $student['nama_lengkap'] . ' (ID: ' . $student['id'] . ')');
                
                // Check if student already has manual absensi record
                $existingManualRecord = $absensiManualModel->getAbsensiByTanggalAndUser($tanggal, $student['id']);
                log_message('debug', 'Existing manual record for ' . $student['nama_lengkap'] . ': ' . ($existingManualRecord ? 'Yes' : 'No'));
                
                if (!$existingManualRecord) {
                    // Check if student has done GPS/face recognition presensi
                    $absensiModel = new Absensi();
                    $existingGPSPresensi = $absensiModel->where('user_id', $student['id'])
                                                        ->where('DATE(waktu_presensi)', $tanggal)
                                                        ->first();
                    log_message('debug', 'Existing GPS record for ' . $student['nama_lengkap'] . ': ' . ($existingGPSPresensi ? 'Yes' : 'No'));
                    
                    // Only add to list if student hasn't done GPS presensi either
                    if (!$existingGPSPresensi) {
                        $data['siswa'][] = $student;
                        log_message('debug', 'Adding student to list: ' . $student['nama_lengkap']);
                    } else {
                        log_message('debug', 'Skipping student (has GPS record): ' . $student['nama_lengkap']);
                    }
                } else {
                    log_message('debug', 'Skipping student (has manual record): ' . $student['nama_lengkap']);
                }
            }
            
            // Debug: Log final student list
            log_message('debug', 'Final student list count: ' . count($data['siswa']));
            foreach ($data['siswa'] as $student) {
                log_message('debug', 'Final list student: ' . $student['nama_lengkap'] . ' (ID: ' . $student['id'] . ')');
            }
        }

        // Pass filter values back to the view
        $data['tanggal'] = $tanggal;
        $data['tingkat'] = $tingkat;
        $data['jurusan'] = $jurusan;

        // Debug: Log final data
        log_message('debug', 'Final siswa count: ' . count($data['siswa']));
        log_message('debug', 'Final absensi_records count: ' . count($data['absensi_records']));

        return view('presensi_harian/index', $data);
    }
    
    public function simpan()
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $absensiManualModel = new AbsensiManual();
        
        // Get data from form
        $user_id = $this->request->getPost('user_id');
        $tanggal = $this->request->getPost('tanggal');
        $jenis = $this->request->getPost('jenis');
        $keterangan = $this->request->getPost('keterangan');
        
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'user_id' => 'required|integer',
            'tanggal' => 'required|valid_date',
            'jenis' => 'required|in_list[izin,sakit,alpa]',
            'keterangan' => 'permit_empty|string|max_length[500]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('error', 'Data tidak valid: ' . implode(', ', $validation->getErrors()));
        }
        
        // Check if record already exists
        $existingRecord = $absensiManualModel->getAbsensiByTanggalAndUser($tanggal, $user_id);
        if ($existingRecord) {
            return redirect()->back()->with('error', 'Data absensi untuk siswa ini pada tanggal tersebut sudah ada.');
        }
        
        // Prepare data
        $data = [
            'user_id' => $user_id,
            'tanggal' => $tanggal,
            'jenis' => $jenis,
            'keterangan' => $keterangan,
            'disetujui_oleh' => $session->get('user_id'),
            'tanggal_disetujui' => date('Y-m-d H:i:s')
        ];
        
        // Save record
        if ($absensiManualModel->save($data)) {
            return redirect()->to('/presensi-harian')->with('success', 'Data absensi berhasil disimpan.');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data absensi.');
        }
    }

    public function simpanMassal()
    {
        // Log incoming request
        log_message('debug', 'simpanMassal function called');
        log_message('debug', 'Request method: ' . $this->request->getMethod());
        log_message('debug', 'Is AJAX: ' . ($this->request->isAJAX() ? 'Yes' : 'No'));
        
        // Check if request method is POST
        if ($this->request->getMethod() !== 'post') {
            $errorMsg = 'Invalid request method: ' . $this->request->getMethod();
            log_message('error', $errorMsg);
            return redirect()->to('/presensi-harian')->with('error', 'Akses tidak sah.');
        }
        
        // Check CSRF
        $csrfToken = $this->request->getPost('csrf_test_name');
        log_message('debug', 'CSRF Token: ' . ($csrfToken ? 'Present' : 'Missing'));
        
        if (!$this->validate([])) {
            $errorMsg = 'CSRF validation failed';
            log_message('error', $errorMsg);
            return redirect()->to('/presensi-harian')->with('error', 'Terjadi kesalahan keamanan. Silakan coba lagi.');
        }

        // Check if user is logged in
        $session = session();
        if (!$session->get('isLoggedIn')) {
            log_message('debug', 'User not logged in, redirecting to auth');
            return redirect()->to('/auth');
        }

        // Debug: Log request method and data
        log_message('debug', 'Request method: ' . $this->request->getMethod());
        log_message('debug', 'All POST data: ' . print_r($this->request->getPost(), true));
        log_message('debug', 'Raw input: ' . $this->request->getRawInput());

        $absensiManualModel = new AbsensiManual();
        
        // Get data from form
        $presensiData = $this->request->getPost('presensi');
        $tanggal = $this->request->getPost('tanggal');
        
        // Debug: Log data yang diterima
        log_message('debug', 'Presensi Data: ' . print_r($presensiData, true));
        log_message('debug', 'Tanggal: ' . $tanggal);
        log_message('debug', 'Presensi Data Type: ' . gettype($presensiData));
        log_message('debug', 'Presensi Data Count: ' . (is_array($presensiData) ? count($presensiData) : 'Not an array'));
        
        // Validasi data
        if (empty($tanggal)) {
            $errorMsg = 'Tanggal harus diisi.';
            session()->setFlashdata('error', $errorMsg);
            log_message('error', $errorMsg);
            return redirect()->back()->withInput();
        }
        
        if (empty($presensiData) || !is_array($presensiData)) {
            $errorMsg = 'Tidak ada data presensi yang dikirim atau format data tidak valid. Data type: ' . gettype($presensiData);
            session()->setFlashdata('error', $errorMsg);
            log_message('error', $errorMsg);
            return redirect()->back()->withInput();
        }

        $successCount = 0;
        $errorCount = 0;
        $processedUsers = [];
        $failedUsers = [];

        // Proses semua data presensi
        log_message('debug', 'Starting to process ' . count($presensiData) . ' users');
        
        foreach ($presensiData as $userId => $data) {
            // Debug: Log data per user
            log_message('debug', 'Processing user ' . $userId . ': ' . print_r($data, true));
            
            // Validasi data user
            if (!is_numeric($userId)) {
                $errorMsg = 'Invalid user ID: ' . $userId;
                log_message('error', $errorMsg);
                $errorCount++;
                $failedUsers[] = ['user_id' => $userId, 'reason' => 'Invalid user ID'];
                $processedUsers[] = $userId;
                continue;
            }
            
            // Validasi data
            if (!is_array($data)) {
                $errorMsg = 'Invalid data format for user ID ' . $userId;
                log_message('error', $errorMsg);
                $errorCount++;
                $failedUsers[] = ['user_id' => $userId, 'reason' => 'Invalid data format'];
                $processedUsers[] = $userId;
                continue;
            }
            
            // Hanya proses jika status dipilih dan bukan 'hadir'
            if (!empty($data['jenis']) && $data['jenis'] != 'hadir') {
                // Cek apakah record sudah ada
                $existingRecord = $absensiManualModel->getAbsensiByTanggalAndUser($tanggal, $userId);
                if ($existingRecord) {
                    log_message('debug', 'Record already exists for user ' . $userId);
                    $processedUsers[] = $userId;
                    continue;
                }

                // Validasi jenis presensi
                $allowedJenis = ['izin', 'sakit', 'alpa'];
                if (!in_array($data['jenis'], $allowedJenis)) {
                    $errorMsg = 'Jenis presensi tidak valid untuk user ID ' . $userId . ': ' . $data['jenis'];
                    log_message('error', $errorMsg);
                    $errorCount++;
                    $failedUsers[] = ['user_id' => $userId, 'reason' => 'Invalid jenis: ' . $data['jenis']];
                    $processedUsers[] = $userId;
                    continue;
                }

                $insertData = [
                    'user_id' => (int)$userId,
                    'tanggal' => $tanggal,
                    'jenis' => $data['jenis'],
                    'keterangan' => isset($data['keterangan']) ? trim($data['keterangan']) : '',
                    'disetujui_oleh' => (int)$session->get('user_id'),
                    'tanggal_disetujui' => date('Y-m-d H:i:s')
                ];

                // Debug: Log data yang akan disimpan
                log_message('debug', 'Insert Data for user ' . $userId . ': ' . print_r($insertData, true));
                
                // Simpan data menggunakan model
                if ($absensiManualModel->save($insertData)) {
                    $successCount++;
                    log_message('debug', 'Successfully saved data for user ' . $userId);
                    $processedUsers[] = $userId;
                } else {
                    $errorCount++;
                    // Log error messages
                    $errors = $absensiManualModel->errors();
                    $errorMsg = 'Gagal menyimpan data absensi untuk user_id ' . $userId . ': ' . print_r($errors, true);
                    log_message('error', $errorMsg);
                    $failedUsers[] = ['user_id' => $userId, 'reason' => 'Model save failed', 'errors' => $errors];
                    $processedUsers[] = $userId;
                }
            } else {
                log_message('debug', 'Skipping user ' . $userId . ' - jenis: ' . ($data['jenis'] ?? 'empty'));
                $processedUsers[] = $userId;
            }
        }

        // Siapkan pesan notifikasi
        $notificationMessage = '';
        if ($successCount > 0) {
            $notificationMessage .= "$successCount data presensi berhasil disimpan. ";
        }
        if ($errorCount > 0) {
            $notificationMessage .= "$errorCount data presensi gagal disimpan. ";
        }
        if ($successCount == 0 && $errorCount == 0) {
            $notificationMessage = 'Tidak ada data yang perlu disimpan.';
        }

        // Log detail failed users
        if (!empty($failedUsers)) {
            log_message('debug', 'Failed users details: ' . print_r($failedUsers, true));
        }

        // Set flash data berdasarkan hasil
        if ($successCount > 0) {
            session()->setFlashdata('success', trim($notificationMessage));
            log_message('debug', 'Success notification: ' . trim($notificationMessage));
        } elseif ($errorCount > 0) {
            session()->setFlashdata('error', trim($notificationMessage));
            log_message('debug', 'Error notification: ' . trim($notificationMessage));
        } else {
            session()->setFlashdata('info', trim($notificationMessage));
            log_message('debug', 'Info notification: ' . trim($notificationMessage));
        }

        // Log informasi akhir
        log_message('debug', 'Total processed: ' . count($processedUsers) . ', Success: ' . $successCount . ', Error: ' . $errorCount);
        log_message('debug', 'Redirecting to /presensi-harian with filters: tanggal=' . $tanggal);

        // Redirect dengan mempertahankan filter
        $redirectUrl = '/presensi-harian?tanggal=' . urlencode($tanggal);
        if (!empty($this->request->getGet('tingkat'))) {
            $redirectUrl .= '&tingkat=' . urlencode($this->request->getGet('tingkat'));
        }
        if (!empty($this->request->getGet('jurusan'))) {
            $redirectUrl .= '&jurusan=' . urlencode($this->request->getGet('jurusan'));
        }
        
        log_message('debug', 'Final redirect URL: ' . $redirectUrl);
        return redirect()->to($redirectUrl);
    }
    
    public function hapus($id)
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $absensiManualModel = new AbsensiManual();
        
        // Check if record exists
        $record = $absensiManualModel->find($id);
        if (!$record) {
            return redirect()->back()->with('error', 'Data absensi tidak ditemukan.');
        }
        
        // Delete record
        if ($absensiManualModel->delete($id)) {
            return redirect()->to('/presensi-harian')->with('success', 'Data absensi berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus data absensi.');
        }
    }
    
    public function edit($id)
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $absensiManualModel = new AbsensiManual();
        
        // Check if record exists
        $data['absensi'] = $absensiManualModel->find($id);
        if (!$data['absensi']) {
            return redirect()->to('/presensi-harian')->with('error', 'Data absensi tidak ditemukan.');
        }
        
        // Get user info
        $userModel = new User();
        $data['siswa'] = $userModel->find($data['absensi']['user_id']);
        
        // Get class info
        $kelasModel = new Kelas();
        if ($data['siswa']['id_kelas']) {
            $data['kelas'] = $kelasModel->find($data['siswa']['id_kelas']);
        } else {
            $data['kelas'] = null;
        }

        return view('presensi_harian/edit', $data);
    }
    
    public function update($id)
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $absensiManualModel = new AbsensiManual();
        
        // Check if record exists
        $record = $absensiManualModel->find($id);
        if (!$record) {
            return redirect()->to('/presensi-harian')->with('error', 'Data absensi tidak ditemukan.');
        }
        
        // Get data from form
        $jenis = $this->request->getPost('jenis');
        $keterangan = $this->request->getPost('keterangan');
        
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'jenis' => 'required|in_list[izin,sakit,alpa,hadir]',
            'keterangan' => 'permit_empty|string|max_length[500]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('error', 'Data tidak valid: ' . implode(', ', $validation->getErrors()));
        }
        
        // Prepare data
        $data = [
            'jenis' => $jenis,
            'keterangan' => $keterangan,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Update record
        if ($absensiManualModel->update($id, $data)) {
            return redirect()->to('/presensi-harian')->with('success', 'Data absensi berhasil diupdate.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengupdate data absensi.');
        }
    }
}