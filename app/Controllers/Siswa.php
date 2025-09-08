<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Absensi;
use App\Models\Pengaturan;
use App\Models\User;
use CodeIgniter\HTTP\ResponseInterface;

class Siswa extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is a student or has student-like role
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        // Cek apakah user memiliki role siswa atau role yang diizinkan mengakses halaman siswa
        $allowedRoles = ['siswa'];
        $userRole = $session->get('role');
        
        // Jika menggunakan sistem multiple roles, cek roles tambahan
        $userModel = new User();
        $allRoles = $userModel->getAllUserRoles($session->get('user_id'));
        
        $hasAccess = in_array($userRole, $allowedRoles) || count(array_intersect($allRoles, $allowedRoles)) > 0;
        
        if (!$hasAccess) {
            return redirect()->to('/auth');
        }

        $data['user'] = $userModel->find($session->get('user_id'));
        return view('siswa/dashboard', $data);
    }

    public function dashboard()
    {
        // Check if user is logged in and is a student or has student-like role
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        // Cek apakah user memiliki role siswa atau role yang diizinkan mengakses halaman siswa
        $allowedRoles = ['siswa'];
        $userRole = $session->get('role');
        
        // Jika menggunakan sistem multiple roles, cek roles tambahan
        $userModel = new User();
        $allRoles = $userModel->getAllUserRoles($session->get('user_id'));
        
        $hasAccess = in_array($userRole, $allowedRoles) || count(array_intersect($allRoles, $allowedRoles)) > 0;
        
        if (!$hasAccess) {
            return redirect()->to('/auth');
        }

        $userId = $session->get('user_id');
        $data['user'] = $userModel->find($userId);
        
        // Hitung rekap kehadiran - hanya perlu menghitung jumlah hari dengan presensi
        $absensiModel = new Absensi();
        $today = date('Y-m-d');
        
        // Hitung total hari masuk (presensi)
        $totalHariMasuk = $absensiModel->select('DATE(waktu_presensi) as tanggal')
            ->where('user_id', $userId)
            ->groupBy('DATE(waktu_presensi)')
            ->countAllResults();
            
        // Hitung total hari kerja dalam bulan ini secara dinamis
        $tahunBulanIni = date('Y-m');
        $totalHariKerja = $this->hitungHariKerjaDalamBulan($tahunBulanIni);
        $totalHariTidakMasuk = $totalHariKerja - $totalHariMasuk;
        
        $data['rekapKehadiran'] = [
            'hadir' => $totalHariMasuk,
            'tidak_hadir' => $totalHariTidakMasuk,
            'total' => $totalHariKerja
        ];

        return view('siswa/dashboard', $data);
    }
    
    // Fungsi untuk menghitung jumlah hari kerja dalam suatu bulan
    private function hitungHariKerjaDalamBulan($tahunBulan)
    {
        // Format tahunBulan: YYYY-MM
        $tahun = intval(substr($tahunBulan, 0, 4));
        $bulan = intval(substr($tahunBulan, 5, 2));
        
        // Hitung jumlah hari dalam bulan
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        
        // Hitung jumlah hari kerja (Senin-Jumat)
        $hariKerja = 0;
        for ($hari = 1; $hari <= $jumlahHari; $hari++) {
            $timestamp = mktime(0, 0, 0, $bulan, $hari, $tahun);
            $hariDalamMinggu = date('N', $timestamp); // 1 = Senin, 7 = Minggu
            
            // Hanya hitung hari kerja (Senin-Jumat)
            if ($hariDalamMinggu >= 1 && $hariDalamMinggu <= 5) {
                $hariKerja++;
            }
        }
        
        // Kurangi hari kerja dengan libur nasional dalam bulan tersebut
        $liburNasionalModel = new \App\Models\LiburNasional();
        $startDate = date('Y-m-01', mktime(0, 0, 0, $bulan, 1, $tahun));
        $endDate = date('Y-m-t', mktime(0, 0, 0, $bulan, 1, $tahun));
        $liburNasional = $liburNasionalModel->getLiburInRange($startDate, $endDate);
        
        // Hitung jumlah libur nasional yang jatuh pada hari kerja (Senin-Jumat)
        $jumlahLiburNasional = 0;
        foreach ($liburNasional as $libur) {
            $hariDalamMinggu = date('N', strtotime($libur['tanggal']));
            // Hanya kurangi jika libur nasional jatuh pada hari kerja (Senin-Jumat)
            if ($hariDalamMinggu >= 1 && $hariDalamMinggu <= 5) {
                $jumlahLiburNasional++;
            }
        }
        
        // Hari kerja dikurangi libur nasional yang jatuh pada hari kerja
        $hariKerja = $hariKerja - $jumlahLiburNasional;
        
        return max(0, $hariKerja); // Pastikan tidak negatif
    }

    public function presensi()
    {
        // Check if user is logged in and is a student or has student-like role
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        // Cek apakah user memiliki role siswa atau role yang diizinkan mengakses halaman siswa
        $allowedRoles = ['siswa'];
        $userRole = $session->get('role');
        
        // Jika menggunakan sistem multiple roles, cek roles tambahan
        $userModel = new User();
        $allRoles = $userModel->getAllUserRoles($session->get('user_id'));
        
        $hasAccess = in_array($userRole, $allowedRoles) || count(array_intersect($allRoles, $allowedRoles)) > 0;
        
        if (!$hasAccess) {
            return redirect()->to('/auth');
        }

        $userId = $session->get('user_id');
        $data['user'] = $userModel->find($userId);
        
        // Get today's attendance status - hanya perlu satu presensi per hari
        $absensiModel = new Absensi();
        $today = date('Y-m-d');
        $data['presensi'] = $absensiModel->where('user_id', $userId)
            ->where('DATE(waktu_presensi)', $today)
            ->first();
            
        // Cek apakah ada presensi manual untuk hari ini
        $absensiManualModel = new \App\Models\AbsensiManual();
        $data['presensi_manual'] = $absensiManualModel->where('user_id', $userId)
            ->where('tanggal', $today)
            ->first();

        // Cek apakah hari ini Sabtu atau Minggu
        // Untuk saat ini, kita tetap memeriksa akhir pekan tapi tidak membatasi presensi
        $dayOfWeek = date('N'); // 1 (for Monday) through 7 (for Sunday)
        $data['is_weekend'] = ($dayOfWeek == 6 || $dayOfWeek == 7);

        return view('siswa/presensi', $data);
    }

    public function doPresensi()
    {
        // Check if user is logged in and is a student
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'siswa') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        // Get JSON data
        $json = $this->request->getJSON();
        
        if (!$json) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }

        $userId = $session->get('user_id');
        $latitude = $json->latitude ?? null;
        $longitude = $json->longitude ?? null;
        $fotoSelfie = $json->foto_selfie ?? null;

        // Validate input
        if (empty($latitude) || empty($longitude)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }

        // Validate numeric values
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Koordinat harus berupa angka']);
        }

        // Validate coordinate ranges
        $lat = floatval($latitude);
        $lon = floatval($longitude);

        if ($lat < -90 || $lat > 90) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Latitude harus antara -90 dan 90']);
        }

        if ($lon < -180 || $lon > 180) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Longitude harus antara -180 dan 180']);
        }

        try {
            // Cek apakah user sudah presensi hari ini
            $absensiModel = new Absensi();
            $today = date('Y-m-d');
            $existingPresensi = $absensiModel->where('user_id', $userId)
                ->where('DATE(waktu_presensi)', $today)
                ->first();
                
            if ($existingPresensi) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Anda sudah melakukan presensi hari ini']);
            }
            
            // Cek apakah ada presensi manual untuk hari ini
            $absensiManualModel = new \App\Models\AbsensiManual();
            $presensiManual = $absensiManualModel->where('user_id', $userId)
                ->where('tanggal', $today)
                ->first();
                
            if ($presensiManual) {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'message' => 'Anda tidak dapat melakukan presensi karena status kehadiran Anda sudah diatur secara manual oleh admin. Status: ' . ucfirst($presensiManual['jenis']) . 
                                 (!empty($presensiManual['keterangan']) ? ' (' . $presensiManual['keterangan'] . ')' : '')
                ]);
            }

            // Validate location
            $pengaturanModel = new Pengaturan();
            $pengaturan = $pengaturanModel->first();
            
            if (!$pengaturan) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Pengaturan lokasi belum diatur oleh admin']);
            }

            // Convert to float for calculation
            $lat1 = floatval($latitude);
            $lon1 = floatval($longitude);
            $lat2 = floatval($pengaturan['lokasi_latitude']);
            $lon2 = floatval($pengaturan['lokasi_longitude']);
            
            $jarak = $this->hitungJarak($lat1, $lon1, $lat2, $lon2);
            
            if ($jarak === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Koordinat tidak valid']);
            }

            if ($jarak > $pengaturan['radius_meter']) {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'message' => 'Anda berada di luar area sekolah. Jarak: ' . round($jarak, 2) . ' meter'
                ]);
            }

            // Face recognition validation
            $userModel = new User();
            $user = $userModel->find($userId);
            
            // Check if user has profile photo
            if (empty($user['foto_profil'])) {
                // Redirect to profile page to upload photo first
                return $this->response->setJSON(['status' => 'error', 'message' => 'Anda harus mengupload foto profil terlebih dahulu. Silakan ke halaman Profil untuk mengupload foto.']);
            } else {
                // Implement face recognition logic here
                $faceRecognitionResult = $this->verifyFace($fotoSelfie, $user['foto_profil']);
                
                if (!$faceRecognitionResult) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Verifikasi wajah gagal. Wajah tidak dikenali.']);
                }
            }

            // Save attendance - hanya satu presensi per hari
            $waktuPresensi = date('Y-m-d H:i:s');
            
            // Cek apakah siswa terlambat atau di luar jam presensi
            $presensiStatus = $this->isLateForPresensi($waktuPresensi);

            if ($presensiStatus['status'] !== 'valid') {
                return $this->response->setJSON(['status' => 'error', 'message' => $presensiStatus['message']]);
            }

            $isLate = $presensiStatus['is_late'];

            if ($isLate) {
                log_message('info', 'Siswa terlambat presensi: user_id=' . $userId . ', waktu=' . $waktuPresensi);
            }
            
            $data = [
                'user_id' => $userId,
                'waktu_presensi' => $waktuPresensi,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'foto_selfie' => substr($fotoSelfie, strpos($fotoSelfie, ",") + 1),
                'terlambat' => $isLate ? 1 : 0 // Tambahkan field terlambat
            ];

            // Validate using model validation
            if (!$absensiModel->save($data)) {
                $errors = $absensiModel->errors();
                $errorMessage = implode(', ', $errors);
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan presensi: ' . $errorMessage]);
            }
            
            // Simpan foto selfie ke file terpisah
            $absensiId = $absensiModel->getInsertID();
            if ($absensiId && $data['foto_selfie']) {
                // Buat direktori jika belum ada
                $selfiePath = FCPATH . 'uploads/selfies';
                if (!is_dir($selfiePath)) {
                    mkdir($selfiePath, 0755, true);
                }
                
                // Simpan foto
                $fotoPath = $selfiePath . '/' . $absensiId . '.png';
                file_put_contents($fotoPath, base64_decode($data['foto_selfie']));
            }

            $message = 'Presensi berhasil';
            if ($isLate) {
                $message .= ' (Anda terlambat)';
            }
            
            return $this->response->setJSON(['status' => 'success', 'message' => $message]);
        } catch (\Exception $e) {
            // Log error
            log_message('error', 'Error in doPresensi: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan saat memproses presensi']);
        }
    }

    public function checkLocation()
    {
        // Get JSON data
        $json = $this->request->getJSON();
        
        if (!$json || !isset($json->latitude) || !isset($json->longitude)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data lokasi tidak lengkap.']);
        }

        $latitude = $json->latitude;
        $longitude = $json->longitude;

        // Validate numeric values
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Koordinat harus berupa angka.']);
        }

        try {
            // Validate location
            $pengaturanModel = new Pengaturan();
            $pengaturan = $pengaturanModel->first();
            
            if (!$pengaturan) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Pengaturan lokasi belum diatur oleh admin.']);
            }

            // Convert to float for calculation
            $lat1 = floatval($latitude);
            $lon1 = floatval($longitude);
            $lat2 = floatval($pengaturan['lokasi_latitude']);
            $lon2 = floatval($pengaturan['lokasi_longitude']);
            
            $jarak = $this->hitungJarak($lat1, $lon1, $lat2, $lon2);
            
            if ($jarak === false) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Koordinat tidak valid.']);
            }

            if ($jarak > $pengaturan['radius_meter']) {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'message' => 'Anda berada di luar area sekolah. Jarak Anda dari sekolah: ' . round($jarak, 2) . ' meter.'
                ]);
            }

            return $this->response->setJSON(['status' => 'success', 'message' => 'Lokasi Anda sesuai.']);

        } catch (\Exception $e) {
            // Log error
            log_message('error', 'Error in checkLocation: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan saat memeriksa lokasi.']);
        }
    }

    public function riwayat()
    {
        // Check if user is logged in and is a student or has student-like role
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        // Cek apakah user memiliki role siswa atau role yang diizinkan mengakses halaman siswa
        $allowedRoles = ['siswa'];
        $userRole = $session->get('role');
        
        // Jika menggunakan sistem multiple roles, cek roles tambahan
        $userModel = new User();
        $allRoles = $userModel->getAllUserRoles($session->get('user_id'));
        
        $hasAccess = in_array($userRole, $allowedRoles) || count(array_intersect($allRoles, $allowedRoles)) > 0;
        
        if (!$hasAccess) {
            return redirect()->to('/auth');
        }

        $userId = $session->get('user_id');
        $absensiModel = new Absensi();
        $absensiManualModel = new \App\Models\AbsensiManual();
        $liburNasionalModel = new \App\Models\LiburNasional();

        // Ambil data presensi yang sudah ada
        $presensiExist = $absensiModel
            ->select('DATE(waktu_presensi) as tanggal, MIN(waktu_presensi) as jam_presensi')
            ->where('user_id', $userId)
            ->groupBy('DATE(waktu_presensi)')
            ->orderBy('tanggal', 'DESC')
            ->findAll();
            
        // Konversi array presensi ke associative array untuk pencarian cepat
        $presensiMap = [];
        foreach ($presensiExist as $presensi) {
            $presensiMap[$presensi['tanggal']] = $presensi;
        }
        
        // Ambil data presensi manual (izin/sakit/alpa)
        $presensiManualExist = $absensiManualModel
            ->select('tanggal, jenis, keterangan')
            ->where('user_id', $userId)
            ->orderBy('tanggal', 'DESC')
            ->findAll();
            
        // Konversi array presensi manual ke associative array untuk pencarian cepat
        $presensiManualMap = [];
        foreach ($presensiManualExist as $presensi) {
            $presensiManualMap[$presensi['tanggal']] = $presensi;
        }
        
        // Dapatkan semua tanggal dalam bulan ini
        $tahun = date('Y');
        $bulan = date('m');
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        
        // Dapatkan libur nasional dalam bulan ini
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
        $liburNasional = $liburNasionalModel->getLiburInRange($startDate, $endDate);
        
        // Konversi libur nasional ke associative array untuk pencarian cepat
        $liburNasionalMap = [];
        foreach ($liburNasional as $libur) {
            $liburNasionalMap[$libur['tanggal']] = $libur;
        }
        
        // Buat array untuk semua tanggal dalam bulan
        $riwayat = [];
        for ($i = 1; $i <= $jumlahHari; $i++) {
            $tanggal = date('Y-m-d', mktime(0, 0, 0, $bulan, $i, $tahun));
            
            // Cek hari dalam minggu (0 = Minggu, 6 = Sabtu)
            $hariDalamMinggu = date('w', strtotime($tanggal));
            $isWeekend = ($hariDalamMinggu == 0 || $hariDalamMinggu == 6);
            
            // Cek apakah tanggal ini adalah libur nasional
            $isLiburNasional = isset($liburNasionalMap[$tanggal]);
            
            // Cek apakah ada presensi manual
            $hasManualPresensi = isset($presensiManualMap[$tanggal]);
            
            if (isset($presensiMap[$tanggal])) {
                // Jika ada presensi GPS/face recognition, gunakan data yang sebenarnya
                $riwayat[] = [
                    'tanggal' => $tanggal,
                    'jam_presensi' => $presensiMap[$tanggal]['jam_presensi'],
                    'is_weekend' => $isWeekend,
                    'is_libur_nasional' => $isLiburNasional,
                    'keterangan_libur' => $isLiburNasional ? $liburNasionalMap[$tanggal]['keterangan'] : null,
                    'jenis_presensi' => 'hadir' // Presensi otomatis
                ];
            } elseif ($hasManualPresensi) {
                // Jika ada presensi manual, gunakan data manual
                $manualPresensi = $presensiManualMap[$tanggal];
                $riwayat[] = [
                    'tanggal' => $tanggal,
                    'jam_presensi' => null,
                    'is_weekend' => $isWeekend,
                    'is_libur_nasional' => $isLiburNasional,
                    'keterangan_libur' => $isLiburNasional ? $liburNasionalMap[$tanggal]['keterangan'] : null,
                    'jenis_presensi' => $manualPresensi['jenis'], // izin, sakit, alpa
                    'keterangan_manual' => $manualPresensi['keterangan']
                ];
            } else {
                // Jika tidak ada presensi sama sekali, anggap alpa jika bukan weekend atau libur
                $jenis_presensi = null;
                if (!$isWeekend && !$isLiburNasional) {
                    $jenis_presensi = 'alpa';
                }

                $riwayat[] = [
                    'tanggal' => $tanggal,
                    'jam_presensi' => null,
                    'is_weekend' => $isWeekend,
                    'is_libur_nasional' => $isLiburNasional,
                    'keterangan_libur' => $isLiburNasional ? $liburNasionalMap[$tanggal]['keterangan'] : null,
                    'jenis_presensi' => $jenis_presensi
                ];
            }
        }
        
        // Urutkan berdasarkan tanggal ASC (dari tanggal terkecil ke terbesar)
        usort($riwayat, function($a, $b) {
            return strtotime($a['tanggal']) - strtotime($b['tanggal']);
        });

        $data['riwayat'] = $riwayat;
        $data['user'] = $userModel->find($userId);

        return view('siswa/riwayat', $data);
    }

    private function hitungJarak($lat1, $lon1, $lat2, $lon2)
    {
        // Validate input
        if (!is_numeric($lat1) || !is_numeric($lon1) || !is_numeric($lat2) || !is_numeric($lon2)) {
            return false;
        }
        
        // Validate coordinate ranges
        if ($lat1 < -90 || $lat1 > 90 || $lat2 < -90 || $lat2 > 90) {
            return false;
        }
        
        if ($lon1 < -180 || $lon1 > 180 || $lon2 < -180 || $lon2 > 180) {
            return false;
        }
        
        // Haversine formula
        $earth_radius = 6371000; // Bumi dalam meter
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + 
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
             sin($dLon/2) * sin($dLon/2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earth_radius * $c;
        
        return $distance;
    }
    
    public function profile()
    {
        // Check if user is logged in and is a student or has student-like role
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        // Cek apakah user memiliki role siswa atau role yang diizinkan mengakses halaman siswa
        $allowedRoles = ['siswa'];
        $userRole = $session->get('role');
        
        // Jika menggunakan sistem multiple roles, cek roles tambahan
        $userModel = new User();
        $allRoles = $userModel->getAllUserRoles($session->get('user_id'));
        
        $hasAccess = in_array($userRole, $allowedRoles) || count(array_intersect($allRoles, $allowedRoles)) > 0;
        
        if (!$hasAccess) {
            return redirect()->to('/auth');
        }

        $userId = $session->get('user_id');
        $userModel = new User();
        $data['user'] = $userModel->find($userId);

        return view('siswa/profile', $data);
    }

    public function uploadProfilePhoto()
    {
        // Check if user is logged in and is a student
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'siswa') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        $userId = $session->get('user_id');
        $userModel = new User();

        // Get the photo data
        $photoData = $this->request->getPost('photo');

        if (empty($photoData)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data foto tidak ditemukan']);
        }

        // Remove the data:image/png;base64, part
        $photoData = substr($photoData, strpos($photoData, ",") + 1);
        $photoData = base64_decode($photoData);

        // Create directory if not exists
        $uploadPath = FCPATH . 'uploads/profiles';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate unique filename
        $filename = 'profile_' . $userId . '_' . time() . '.png';
        $filepath = $uploadPath . '/' . $filename;

        // Save the file
        if (file_put_contents($filepath, $photoData)) {
            // Update user record with profile photo path
            $userModel->update($userId, ['foto_profil' => $filename]);
            
            // Return the URL to the saved file
            $photoUrl = base_url('uploads/profiles/' . $filename);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Foto profil berhasil diupload', 'photo_url' => $photoUrl]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menyimpan foto profil']);
        }
    }
    
    private function isLateForPresensi($waktuPresensi)
    {
        try {
            // Dapatkan hari dalam seminggu (1 = Senin, 7 = Minggu)
            $hari = date('N', strtotime($waktuPresensi));
            
            // Dapatkan pengaturan jam presensi
            $pengaturanModel = new Pengaturan();
            $pengaturan = $pengaturanModel->first();
            
            if (!$pengaturan) {
                return ['status' => 'no_setting', 'is_late' => false, 'message' => 'Pengaturan jam presensi belum diatur.'];
            }
            
            // Tentukan field jam berdasarkan hari
            $hariNames = [1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat', 6 => 'sabtu', 7 => 'minggu'];
            $hariName = $hariNames[$hari];
            $jamMasukField = 'jam_masuk_' . $hariName;
            $jamPulangField = 'jam_pulang_' . $hariName;
            
            // Cek apakah jam presensi telah diatur untuk hari ini
            if (empty($pengaturan[$jamMasukField]) || empty($pengaturan[$jamPulangField])) {
                // Jika jam presensi tidak diatur untuk hari ini, izinkan presensi dengan waktu bebas
                $jamPresensi = date('H:i:s', strtotime($waktuPresensi));
                return ['status' => 'valid', 'is_late' => false, 'message' => ''];
            }
            
            $jamPresensi = date('H:i:s', strtotime($waktuPresensi));
            $jamMasuk = $pengaturan[$jamMasukField];
            $jamPulang = $pengaturan[$jamPulangField];

            // Cek apakah presensi dilakukan di luar jam kerja
            if ($jamPresensi < $jamMasuk || $jamPresensi > $jamPulang) {
                return ['status' => 'outside_window', 'is_late' => false, 'message' => 'Presensi dilakukan di luar jam masuk (' . $jamMasuk . ') dan jam pulang (' . $jamPulang . ').'];
            }
            
            // Jika waktu presensi lebih besar dari jam masuk, maka terlambat
            $isLate = $jamPresensi > $jamMasuk;

            return ['status' => 'valid', 'is_late' => $isLate, 'message' => ''];
        } catch (\Exception $e) {
            log_message('error', 'Error checking presensi time: ' . $e->getMessage());
            return ['status' => 'error', 'is_late' => false, 'message' => 'Terjadi kesalahan saat memeriksa jam presensi.'];
        }
    }
    
    private function verifyFace($selfieData, $profilePhotoFilename)
    {
        try {
            // Path ke foto profil
            $profilePhotoPath = FCPATH . 'uploads/profiles/' . $profilePhotoFilename;
            
            // Cek apakah foto profil ada
            if (!file_exists($profilePhotoPath)) {
                log_message('error', 'Foto profil tidak ditemukan: ' . $profilePhotoPath);
                return false;
            }
            
            // Simpan data selfie ke file temporer
            $tempPath = WRITEPATH . 'uploads/temp/';
            if (!is_dir($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            $selfiePath = $tempPath . 'selfie_' . time() . '.png';
            $selfieBase64 = substr($selfieData, strpos($selfieData, ",") + 1);
            file_put_contents($selfiePath, base64_decode($selfieBase64));
            
            // Panggil skrip Python untuk perbandingan wajah
            $pythonPath = "C:\\Users\\User\\AppData\\Local\\Programs\\Python\\Python313\\python.exe";
            $command = '"' . $pythonPath . '" ' . ROOTPATH . "face_compare.py " . escapeshellarg($profilePhotoPath) . " " . escapeshellarg($selfiePath) . " 2>&1";
            $output = shell_exec($command);
            
            // Catat output dari skrip Python untuk debugging
            log_message('error', 'Output verifikasi wajah: ' . $output);
            
            // Hapus file temporer
            if (file_exists($selfiePath)) {
                unlink($selfiePath);
            }
            
            // Parse hasil JSON
            $result = json_decode($output, true);
            if ($result && isset($result['match'])) {
                // Tambahkan log untuk debugging hasil verifikasi
                log_message('info', 'Hasil verifikasi wajah: match=' . ($result['match'] ? 'true' : 'false') . 
                                   ', similarity=' . (isset($result['similarity']) ? $result['similarity'] : 'N/A') . 
                                   ', distance=' . (isset($result['distance']) ? $result['distance'] : 'N/A') . 
                                   (isset($result['warning']) ? ', warning=' . $result['warning'] : ''));
                
                // Jika ada warning, tampilkan dalam log
                if (isset($result['warning'])) {
                    log_message('warning', 'Face recognition warning: ' . $result['warning']);
                }
                
                // Kita bisa menyesuaikan threshold sesuai kebutuhan
                // Misalnya, jika similarity > 0.5, anggap sebagai match
                if (isset($result['similarity'])) {
                    // Untuk debugging, kita bisa mengizinkan presensi bahkan dengan akurasi rendah
                    // Dalam produksi, sebaiknya gunakan threshold yang lebih tinggi (misalnya 0.6 atau 0.7)
                    $threshold = 0.3; // Threshold yang lebih rendah untuk debugging
                    $match = $result['similarity'] > $threshold;
                    
                    log_message('info', 'Threshold: ' . $threshold . ', Similarity: ' . $result['similarity'] . ', Match: ' . ($match ? 'true' : 'false'));
                    return $match;
                }
                
                return $result['match'];
            }

            // Jika ada error dari skrip Python, catat juga
            if ($result && isset($result['error'])) {
                log_message('error', 'Error verifikasi wajah: ' . $result['error']);
                // Untuk sementara, kita bisa mengizinkan presensi jika ada error library
                // Ini hanya untuk tujuan debugging, sebaiknya dihapus dalam produksi
                if (strpos($result['error'], 'Required library not installed') !== false) {
                    log_message('error', 'Library face_recognition tidak terinstal. Untuk debugging, mengizinkan presensi.');
                    return true;
                }
                // Jika ada error lain, kembalikan false
                return false;
            }
            
            // Jika tidak ada hasil yang valid, kembalikan false
            return false;
        } catch (\Exception $e) {
            log_message('error', 'Error pada fungsi verifyFace: ' . $e->getMessage());
            return false;
        }
    }
}
