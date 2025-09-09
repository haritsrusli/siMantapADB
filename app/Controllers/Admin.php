<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Absensi;
use App\Models\Pengaturan;
use App\Models\User;
use CodeIgniter\HTTP\ResponseInterface;

class Admin extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        return view('admin/dashboard');
    }
    
    public function kelas()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $kelasModel = new \App\Models\Kelas();
        $data['kelas'] = $kelasModel->getKelasWithWalikelasPaginated(10);
        $data['pager'] = $kelasModel->pager;
        
        // Mendapatkan semua user dengan role wali_kelas untuk dropdown
        $userModel = new User();
        $data['walikelas'] = $userModel->where('role', 'wali_kelas')->findAll();

        return view('admin/manajemen_kelas', $data);
    }
    
    public function tambahKelas()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }
        
        // Mendapatkan semua user dengan role wali_kelas untuk dropdown
        $userModel = new User();
        $data['walikelas'] = $userModel->where('role', 'wali_kelas')->findAll();

        return view('admin/tambah_kelas', $data);
    }
    
    public function simpanKelas()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $kelasModel = new \App\Models\Kelas();
        $data = [
            'nama_kelas' => $this->request->getPost('nama_kelas'),
            'tingkat' => $this->request->getPost('tingkat'),
            'jurusan' => $this->request->getPost('jurusan'),
            'tahun_ajaran' => $this->request->getPost('tahun_ajaran'),
        ];

        // Validate using model validation
        if (!$kelasModel->save($data)) {
            $errors = $kelasModel->errors();
            $errorMessage = implode(', ', $errors);
            return redirect()->back()->with('error', 'Gagal menyimpan data kelas: ' . $errorMessage);
        }

        return redirect()->to('/admin/kelas')->with('success', 'Data kelas berhasil ditambahkan');
    }
    
    public function editKelas($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $kelasModel = new \App\Models\Kelas();
        $data['kelas'] = $kelasModel->find($id);

        if (!$data['kelas']) {
            return redirect()->to('/admin/kelas')->with('error', 'Data kelas tidak ditemukan');
        }
        
        // Mendapatkan semua user dengan role wali_kelas untuk dropdown
        $userModel = new User();
        $data['walikelas'] = $userModel->where('role', 'wali_kelas')->findAll();

        return view('admin/edit_kelas', $data);
    }
    
    public function updateKelas($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $kelasModel = new \App\Models\Kelas();
        $data = [
            'nama_kelas' => $this->request->getPost('nama_kelas'),
            'tingkat' => $this->request->getPost('tingkat'),
            'jurusan' => $this->request->getPost('jurusan'),
            'tahun_ajaran' => $this->request->getPost('tahun_ajaran'),
        ];

        // Validate using model validation
        if (!$kelasModel->update($id, $data)) {
            $errors = $kelasModel->errors();
            $errorMessage = implode(', ', $errors);
            return redirect()->back()->with('error', 'Gagal mengupdate data kelas: ' . $errorMessage);
        }

        return redirect()->to('/admin/kelas')->with('success', 'Data kelas berhasil diupdate');
    }
    
    public function hapusKelas($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $kelasModel = new \App\Models\Kelas();
        
        // Check if kelas exists
        $kelas = $kelasModel->find($id);
        if (!$kelas) {
            return redirect()->to('/admin/kelas')->with('error', 'Data kelas tidak ditemukan');
        }

        try {
            if ($kelasModel->delete($id)) {
                return redirect()->to('/admin/kelas')->with('success', 'Data kelas berhasil dihapus');
            } else {
                return redirect()->to('/admin/kelas')->with('error', 'Gagal menghapus data kelas');
            }
        } catch (\Exception $e) {
            // Log error
            log_message('error', 'Error deleting kelas: ' . $e->getMessage());
            return redirect()->to('/admin/kelas')->with('error', 'Terjadi kesalahan saat menghapus data kelas');
        }
    }

    public function dashboard()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        // Get today's attendance data - hanya menghitung jumlah user yang presensi hari ini
        $absensiModel = new Absensi();
        $today = date('Y-m-d');
        
        // Dapatkan presensi hari ini yang dikelompokkan per user
        $hadirData = $absensiModel->select('user_id')
            ->where('DATE(waktu_presensi)', $today)
            ->groupBy('user_id')
            ->findAll();
            
        $data['hadir'] = count($hadirData);

        // Get total students
        $userModel = new User();
        $data['total_siswa'] = $userModel->where('role', 'siswa')->countAllResults();
        
        // Hitung persentase kehadiran hari ini
        if ($data['total_siswa'] > 0) {
            $data['persentase_hadir'] = round(($data['hadir'] / $data['total_siswa']) * 100, 2);
        } else {
            $data['persentase_hadir'] = 0;
        }

        return view('admin/dashboard', $data);
    }

    public function pengaturanPresensi()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $pengaturanModel = new Pengaturan();
        $data['pengaturan'] = $pengaturanModel->first();
        
        // Jika belum ada data pengaturan, buat data default
        if (!$data['pengaturan']) {
            $defaultData = [
                'lokasi_latitude' => '-6.2088',
                'lokasi_longitude' => '106.8456',
                'radius_meter' => 50,
                'lokasi_locked' => 0,
                'google_maps_api_key' => '',
                'jam_masuk_senin' => '07:00:00',
                'jam_pulang_senin' => '15:00:00',
                'jam_masuk_selasa' => '07:00:00',
                'jam_pulang_selasa' => '15:00:00',
                'jam_masuk_rabu' => '07:00:00',
                'jam_pulang_rabu' => '15:00:00',
                'jam_masuk_kamis' => '07:00:00',
                'jam_pulang_kamis' => '15:00:00',
                'jam_masuk_jumat' => '07:00:00',
                'jam_pulang_jumat' => '15:00:00',
                'jam_masuk_sabtu' => null,
                'jam_pulang_sabtu' => null,
                'jam_masuk_minggu' => null,
                'jam_pulang_minggu' => null,
            ];
            
            if ($pengaturanModel->save($defaultData)) {
                $data['pengaturan'] = $pengaturanModel->first();
            } else {
                log_message('error', 'Failed to create default pengaturan: ' . json_encode($pengaturanModel->errors()));
            }
        }

        return view('admin/pengaturan_presensi', $data);
    }

    public function simpanPengaturanPresensi()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');
        $radius = $this->request->getPost('radius');
        $googleMapsApiKey = $this->request->getPost('google_maps_api_key');

        // Get jam presensi data
        $jamMasukSenin = $this->request->getPost('jam_masuk_senin');
        $jamPulangSenin = $this->request->getPost('jam_pulang_senin');
        $jamMasukSelasa = $this->request->getPost('jam_masuk_selasa');
        $jamPulangSelasa = $this->request->getPost('jam_pulang_selasa');
        $jamMasukRabu = $this->request->getPost('jam_masuk_rabu');
        $jamPulangRabu = $this->request->getPost('jam_pulang_rabu');
        $jamMasukKamis = $this->request->getPost('jam_masuk_kamis');
        $jamPulangKamis = $this->request->getPost('jam_pulang_kamis');
        $jamMasukJumat = $this->request->getPost('jam_masuk_jumat');
        $jamPulangJumat = $this->request->getPost('jam_pulang_jumat');
        $jamMasukSabtu = $this->request->getPost('jam_masuk_sabtu');
        $jamPulangSabtu = $this->request->getPost('jam_pulang_sabtu');
        $jamMasukMinggu = $this->request->getPost('jam_masuk_minggu');
        $jamPulangMinggu = $this->request->getPost('jam_pulang_minggu');

        // Validate input
        if (empty($latitude) || empty($longitude) || empty($radius)) {
            $errorMessage = 'Semua field harus diisi';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $errorMessage]);
            } else {
                return redirect()->back()->with('error', $errorMessage);
            }
        }

        // Validate numeric values
        if (!is_numeric($latitude) || !is_numeric($longitude) || !is_numeric($radius)) {
            $errorMessage = 'Koordinat dan radius harus berupa angka';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $errorMessage]);
            } else {
                return redirect()->back()->with('error', $errorMessage);
            }
        }

        // Validate coordinate ranges
        $lat = floatval($latitude);
        $lon = floatval($longitude);
        $rad = intval($radius);

        // Format coordinates to 8 decimal places for precision
        $lat = number_format($lat, 8, '.', '');
        $lon = number_format($lon, 8, '.', '');

        if ($lat < -90 || $lat > 90) {
            $errorMessage = 'Latitude harus antara -90 dan 90';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $errorMessage]);
            } else {
                return redirect()->back()->with('error', $errorMessage);
            }
        }

        if ($lon < -180 || $lon > 180) {
            $errorMessage = 'Longitude harus antara -180 dan 180';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $errorMessage]);
            } else {
                return redirect()->back()->with('error', $errorMessage);
            }
        }

        if ($rad <= 0) {
            $errorMessage = 'Radius harus lebih besar dari 0';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $errorMessage]);
            } else {
                return redirect()->back()->with('error', $errorMessage);
            }
        }

        $pengaturanModel = new Pengaturan();
        $pengaturan = $pengaturanModel->first();

        $data = [
            'lokasi_latitude' => $lat,
            'lokasi_longitude' => $lon,
            'radius_meter' => $rad,
            'lokasi_locked' => isset($pengaturan['lokasi_locked']) ? $pengaturan['lokasi_locked'] : 0,
            'google_maps_api_key' => $googleMapsApiKey,
            // Hanya update field jam presensi jika ada nilai yang dikirim
            'jam_masuk_senin' => !empty($jamMasukSenin) ? $jamMasukSenin : (isset($pengaturan['jam_masuk_senin']) ? $pengaturan['jam_masuk_senin'] : null),
            'jam_pulang_senin' => !empty($jamPulangSenin) ? $jamPulangSenin : (isset($pengaturan['jam_pulang_senin']) ? $pengaturan['jam_pulang_senin'] : null),
            'jam_masuk_selasa' => !empty($jamMasukSelasa) ? $jamMasukSelasa : (isset($pengaturan['jam_masuk_selasa']) ? $pengaturan['jam_masuk_selasa'] : null),
            'jam_pulang_selasa' => !empty($jamPulangSelasa) ? $jamPulangSelasa : (isset($pengaturan['jam_pulang_selasa']) ? $pengaturan['jam_pulang_selasa'] : null),
            'jam_masuk_rabu' => !empty($jamMasukRabu) ? $jamMasukRabu : (isset($pengaturan['jam_masuk_rabu']) ? $pengaturan['jam_masuk_rabu'] : null),
            'jam_pulang_rabu' => !empty($jamPulangRabu) ? $jamPulangRabu : (isset($pengaturan['jam_pulang_rabu']) ? $pengaturan['jam_pulang_rabu'] : null),
            'jam_masuk_kamis' => !empty($jamMasukKamis) ? $jamMasukKamis : (isset($pengaturan['jam_masuk_kamis']) ? $pengaturan['jam_masuk_kamis'] : null),
            'jam_pulang_kamis' => !empty($jamPulangKamis) ? $jamPulangKamis : (isset($pengaturan['jam_pulang_kamis']) ? $pengaturan['jam_pulang_kamis'] : null),
            'jam_masuk_jumat' => !empty($jamMasukJumat) ? $jamMasukJumat : (isset($pengaturan['jam_masuk_jumat']) ? $pengaturan['jam_masuk_jumat'] : null),
            'jam_pulang_jumat' => !empty($jamPulangJumat) ? $jamPulangJumat : (isset($pengaturan['jam_pulang_jumat']) ? $pengaturan['jam_pulang_jumat'] : null),
            'jam_masuk_sabtu' => !empty($jamMasukSabtu) ? $jamMasukSabtu : (isset($pengaturan['jam_masuk_sabtu']) ? $pengaturan['jam_masuk_sabtu'] : null),
            'jam_pulang_sabtu' => !empty($jamPulangSabtu) ? $jamPulangSabtu : (isset($pengaturan['jam_pulang_sabtu']) ? $pengaturan['jam_pulang_sabtu'] : null),
            'jam_masuk_minggu' => !empty($jamMasukMinggu) ? $jamMasukMinggu : (isset($pengaturan['jam_masuk_minggu']) ? $pengaturan['jam_masuk_minggu'] : null),
            'jam_pulang_minggu' => !empty($jamPulangMinggu) ? $jamPulangMinggu : (isset($pengaturan['jam_pulang_minggu']) ? $pengaturan['jam_pulang_minggu'] : null),
        ];

        try {
            if ($pengaturan) {
                // Update existing setting
                if ($pengaturanModel->update($pengaturan['id'], $data)) {
                    // Cek apakah request ini adalah AJAX request
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['status' => 'success', 'message' => 'Pengaturan lokasi berhasil diperbarui']);
                    } else {
                        return redirect()->back()->with('success', 'Pengaturan lokasi berhasil diperbarui');
                    }
                } else {
                    $errors = $pengaturanModel->errors();
                    log_message('error', 'Failed to update pengaturan: ' . json_encode($errors));
                    $errorMessage = !empty($errors) ? 'Gagal memperbarui pengaturan lokasi: ' . implode(', ', $errors) : 'Gagal memperbarui pengaturan lokasi';
                    // Cek apakah request ini adalah AJAX request
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['status' => 'error', 'message' => $errorMessage]);
                    } else {
                        return redirect()->back()->with('error', $errorMessage);
                    }
                }
            } else {
                // Create new setting
                if ($pengaturanModel->save($data)) {
                    // Cek apakah request ini adalah AJAX request
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['status' => 'success', 'message' => 'Pengaturan lokasi berhasil disimpan']);
                    } else {
                        return redirect()->back()->with('success', 'Pengaturan lokasi berhasil disimpan');
                    }
                } else {
                    $errors = $pengaturanModel->errors();
                    log_message('error', 'Failed to save pengaturan: ' . json_encode($errors));
                    $errorMessage = !empty($errors) ? 'Gagal menyimpan pengaturan lokasi: ' . implode(', ', $errors) : 'Gagal menyimpan pengaturan lokasi';
                    // Cek apakah request ini adalah AJAX request
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['status' => 'error', 'message' => $errorMessage]);
                    } else {
                        return redirect()->back()->with('error', $errorMessage);
                    }
                }
            }
        } catch (\Exception $e) {
            // Log error
            log_message('error', 'Error saving pengaturan: ' . $e->getMessage());
            $errorMessage = 'Terjadi kesalahan saat menyimpan pengaturan lokasi: ' . $e->getMessage();
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $errorMessage]);
            } else {
                return redirect()->back()->with('error', $errorMessage);
            }
        }
    }

    public function simpanJamPresensi()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $pengaturanModel = new Pengaturan();
        $pengaturan = $pengaturanModel->first();

        // Siapkan data untuk update - hanya field jam presensi yang diisi
        $data = [];
        $errors = [];
        $hariNames = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];

        // Log semua data POST untuk debugging
        log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));

        $post_data = $this->request->getPost();

        foreach ($hariNames as $hari) {
            $jam_masuk_key = 'jam_masuk_' . $hari;
            $jam_pulang_key = 'jam_pulang_' . $hari;
            
            // Untuk jam masuk
            if (array_key_exists($jam_masuk_key, $post_data)) {
                $jam_masuk = $post_data[$jam_masuk_key];
                
                // Log nilai yang diterima untuk setiap field
                log_message('debug', "Hari $hari - Jam masuk: '" . $jam_masuk . "' (type: " . gettype($jam_masuk) . ")");
                
                if ($jam_masuk === null || $jam_masuk === '') {
                    $data[$jam_masuk_key] = null;
                } else {
                    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $jam_masuk)) {
                        $errors[] = "Format jam masuk " . ucfirst($hari) . " tidak valid (HH:MM atau HH:MM:SS)";
                    } else {
                        $data[$jam_masuk_key] = $jam_masuk;
                    }
                }
            }
            
            // Untuk jam pulang
            if (array_key_exists($jam_pulang_key, $post_data)) {
                $jam_pulang = $post_data[$jam_pulang_key];
                
                // Log nilai yang diterima untuk setiap field
                log_message('debug', "Hari $hari - Jam pulang: '" . $jam_pulang . "' (type: " . gettype($jam_pulang) . ")");
                
                if ($jam_pulang === null || $jam_pulang === '') {
                    $data[$jam_pulang_key] = null;
                } else {
                    if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $jam_pulang)) {
                        $errors[] = "Format jam pulang " . ucfirst($hari) . " tidak valid (HH:MM atau HH:MM:SS)";
                    } else {
                        $data[$jam_pulang_key] = $jam_pulang;
                    }
                }
            }
        }

        // Jika ada error validasi, kembali dengan pesan error
        if (!empty($errors)) {
            log_message('error', 'Validation errors in jam presensi: ' . implode(', ', $errors));
            $errorMessage = 'Gagal memperbarui jam presensi: ' . implode(', ', $errors);
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $errorMessage]);
            } else {
                return redirect()->to('/admin/pengaturan-presensi')->with('error', $errorMessage);
            }
        }

        // Jika tidak ada data yang diisi, kembali dengan pesan
        if (empty($data)) {
            $warningMessage = 'Tidak ada data jam presensi yang diisi';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'warning', 'message' => $warningMessage]);
            } else {
                return redirect()->to('/admin/pengaturan-presensi')->with('warning', $warningMessage);
            }
        }

        // Log data yang akan dikirim untuk debugging
        log_message('debug', 'Data yang dikirim ke model: ' . json_encode($data));

        try {
            if ($pengaturan) {
                // Update existing setting - hanya update field jam presensi
                if ($pengaturanModel->update($pengaturan['id'], $data)) {
                    // Cek apakah request ini adalah AJAX request
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['status' => 'success', 'message' => 'Jam presensi berhasil diperbarui']);
                    } else {
                        return redirect()->to('/admin/pengaturan-presensi')->with('success', 'Jam presensi berhasil diperbarui');
                    }
                } else {
                    // Log error untuk debugging
                    $modelErrors = $pengaturanModel->errors();
                    log_message('error', 'Failed to update jam presensi: ' . json_encode($modelErrors));
                    $errorMessage = !empty($modelErrors) ? 'Gagal memperbarui jam presensi: ' . implode(', ', $modelErrors) : 'Gagal memperbarui jam presensi';
                    // Cek apakah request ini adalah AJAX request
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['status' => 'error', 'message' => $errorMessage]);
                    } else {
                        return redirect()->to('/admin/pengaturan-presensi')->with('error', $errorMessage);
                    }
                }
            } else {
                // Create new setting if no existing one (though it should be created by pengaturanPresensi)
                // Gabungkan dengan data default untuk lokasi
                $defaultData = [
                    'lokasi_latitude' => '-6.2088',
                    'lokasi_longitude' => '106.8456',
                    'radius_meter' => 50,
                    'lokasi_locked' => 0,
                    'google_maps_api_key' => '',
                ];
                
                $saveData = array_merge($defaultData, $data);
                
                if ($pengaturanModel->save($saveData)) {
                    // Cek apakah request ini adalah AJAX request
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['status' => 'success', 'message' => 'Jam presensi berhasil disimpan']);
                    } else {
                        return redirect()->to('/admin/pengaturan-presensi')->with('success', 'Jam presensi berhasil disimpan');
                    }
                } else {
                    // Log error untuk debugging
                    $modelErrors = $pengaturanModel->errors();
                    log_message('error', 'Failed to save jam presensi: ' . json_encode($modelErrors));
                    $errorMessage = !empty($modelErrors) ? 'Gagal menyimpan jam presensi: ' . implode(', ', $modelErrors) : 'Gagal menyimpan jam presensi';
                    // Cek apakah request ini adalah AJAX request
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['status' => 'error', 'message' => $errorMessage]);
                    } else {
                        return redirect()->to('/admin/pengaturan-presensi')->with('error', $errorMessage);
                    }
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Error saving jam presensi: ' . $e->getMessage());
            $errorMessage = 'Terjadi kesalahan saat menyimpan jam presensi: ' . $e->getMessage();
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => $errorMessage]);
            } else {
                return redirect()->to('/admin/pengaturan-presensi')->with('error', $errorMessage);
            }
        }
    }

    public function manajemenSiswa()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        $data['siswa'] = $userModel->where('role', 'siswa')->paginate(10);
        $data['pager'] = $userModel->pager;

        return view('admin/manajemen_siswa', $data);
    }
    
    public function manajemenGuru()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        $data['guru'] = $userModel->whereIn('role', ['guru', 'wali_kelas'])->paginate(10);
        $data['pager'] = $userModel->pager;
        
        // Mendapatkan data kelas untuk ditampilkan
        $kelasModel = new \App\Models\Kelas();
        $data['kelas'] = $kelasModel->findAll(); // This is for a dropdown, not paginated

        return view('admin/manajemen_guru', $data);
    }

    public function tambahSiswa()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        return view('admin/tambah_siswa');
    }
    
    public function tambahGuru()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }
        
        // Mendapatkan data kelas untuk dropdown
        $kelasModel = new \App\Models\Kelas();
        $data['kelas'] = $kelasModel->findAll();

        return view('admin/tambah_guru', $data);
    }

    public function simpanSiswa()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        $data = [
            'username' => $this->request->getPost('nis'),
            'password' => $this->request->getPost('password'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'role' => 'siswa',
            'foto_profil' => null // No profile photo by default
        ];

        // Validate using model validation
        if (!$userModel->save($data)) {
            $errors = $userModel->errors();
            $errorMessage = implode(', ', $errors);
            return redirect()->back()->with('error', 'Gagal menyimpan data siswa: ' . $errorMessage);
        }

        return redirect()->to('/admin/manajemen-siswa')->with('success', 'Data siswa berhasil ditambahkan');
    }
    
    public function simpanGuru()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        
        // Mendapatkan data dari form
        $role = $this->request->getPost('role');
        $id_kelas = $this->request->getPost('id_kelas');
        
        // Jika role bukan wali_kelas, maka id_kelas tidak diperlukan
        if ($role !== 'wali_kelas') {
            $id_kelas = null;
        }
        
        $data = [
            'username' => $this->request->getPost('nip'),
            'password' => $this->request->getPost('password'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'role' => $role,
            'id_kelas' => $id_kelas,
            'foto_profil' => null // No profile photo by default
        ];

        // Validate using model validation
        if (!$userModel->save($data)) {
            $errors = $userModel->errors();
            $errorMessage = implode(', ', $errors);
            return redirect()->back()->with('error', 'Gagal menyimpan data guru: ' . $errorMessage);
        }

        return redirect()->to('/admin/manajemen-guru')->with('success', 'Data guru berhasil ditambahkan');
    }
    
    public function editGuru($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        $data['guru'] = $userModel->find($id);

        if (!$data['guru']) {
            return redirect()->to('/admin/manajemen-guru')->with('error', 'Data guru tidak ditemukan');
        }
        
        // Mendapatkan data kelas untuk dropdown
        $kelasModel = new \App\Models\Kelas();
        $data['kelas'] = $kelasModel->findAll();

        return view('admin/edit_guru', $data);
    }
    
    public function updateGuru($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        
        // Mendapatkan data dari form
        $role = $this->request->getPost('role');
        $id_kelas = $this->request->getPost('id_kelas');
        
        // Jika role bukan wali_kelas, maka id_kelas tidak diperlukan
        if ($role !== 'wali_kelas') {
            $id_kelas = null;
        }
        
        $data = [
            'username' => $this->request->getPost('nip'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'role' => $role,
            'id_kelas' => $id_kelas,
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            // Validate password length
            if (strlen($password) < 6) {
                return redirect()->back()->with('error', 'Password minimal 6 karakter');
            }
            $data['password'] = $password;
        }

        // Validate using model validation
        if (!$userModel->update($id, $data)) {
            $errors = $userModel->errors();
            $errorMessage = implode(', ', $errors);
            return redirect()->back()->with('error', 'Gagal mengupdate data guru: ' . $errorMessage);
        }

        return redirect()->to('/admin/manajemen-guru')->with('success', 'Data guru berhasil diupdate');
    }
    
    public function hapusGuru($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        
        // Check if user exists
        $user = $userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/manajemen-guru')->with('error', 'Data guru tidak ditemukan');
        }
        
        // Prevent deleting admin users
        if ($user['role'] === 'admin') {
            return redirect()->to('/admin/manajemen-guru')->with('error', 'Tidak dapat menghapus akun admin');
        }

        try {
            if ($userModel->delete($id)) {
                return redirect()->to('/admin/manajemen-guru')->with('success', 'Data guru berhasil dihapus');
            } else {
                return redirect()->to('/admin/manajemen-guru')->with('error', 'Gagal menghapus data guru');
            }
        } catch (\Exception $e) {
            // Log error
            log_message('error', 'Error deleting user: ' . $e->getMessage());
            return redirect()->to('/admin/manajemen-guru')->with('error', 'Terjadi kesalahan saat menghapus data guru');
        }
    }

    public function editSiswa($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        $data['siswa'] = $userModel->find($id);

        if (!$data['siswa']) {
            return redirect()->to('/admin/manajemen-siswa')->with('error', 'Data siswa tidak ditemukan');
        }

        return view('admin/edit_siswa', $data);
    }

    public function updateSiswa($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        $data = [
            'username' => $this->request->getPost('nis'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            // Validate password length
            if (strlen($password) < 6) {
                return redirect()->back()->with('error', 'Password minimal 6 karakter');
            }
            $data['password'] = $password;
        }

        // Validate using model validation
        if (!$userModel->update($id, $data)) {
            $errors = $userModel->errors();
            $errorMessage = implode(', ', $errors);
            return redirect()->back()->with('error', 'Gagal mengupdate data siswa: ' . $errorMessage);
        }

        return redirect()->to('/admin/manajemen-siswa')->with('success', 'Data siswa berhasil diupdate');
    }

    public function hapusSiswa($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        
        // Check if user exists
        $user = $userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/manajemen-siswa')->with('error', 'Data siswa tidak ditemukan');
        }
        
        // Prevent deleting admin users
        if ($user['role'] === 'admin') {
            return redirect()->to('/admin/manajemen-siswa')->with('error', 'Tidak dapat menghapus akun admin');
        }

        try {
            if ($userModel->delete($id)) {
                return redirect()->to('/admin/manajemen-siswa')->with('success', 'Data siswa berhasil dihapus');
            } else {
                return redirect()->to('/admin/manajemen-siswa')->with('error', 'Gagal menghapus data siswa');
            }
        } catch (\Exception $e) {
            // Log error
            log_message('error', 'Error deleting user: ' . $e->getMessage());
            return redirect()->to('/admin/manajemen-siswa')->with('error', 'Terjadi kesalahan saat menghapus data siswa');
        }
    }

    public function rekapHarian()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $absensiModel = new Absensi();
        $absensiManualModel = new \App\Models\AbsensiManual(); // Tambahkan model absensi manual
        $kelasModel = new \App\Models\Kelas();
        $userModel = new User();

        // Get filter parameters from request
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');
        $id_kelas = $this->request->getGet('id_kelas');
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 36; // Maximum records per page

        $data['rekap_harian'] = [];
        $data['kelas'] = $kelasModel->findAll(); // Get all classes for the dropdown

        // Set default dates if not provided
        if (empty($start_date)) {
            $start_date = date('Y-m-01');
        }
        if (empty($end_date)) {
            $end_date = date('Y-m-t');
        }

        // Only process if class is selected
        if (!empty($id_kelas)) {
            // Get all students in the selected class
            $studentsInClass = $userModel->where('role', 'siswa')
                                         ->where('id_kelas', $id_kelas)
                                         ->findAll();
            $totalStudentsInClass = count($studentsInClass);

            // Loop through each date in the range
            $currentDate = new \DateTime($start_date);
            $endDateObj = new \DateTime($end_date);
            
            // Array to hold all records
            $allRecords = [];

            while ($currentDate <= $endDateObj) {
                $date = $currentDate->format('Y-m-d');
                
                // Get attendance for the current date and class (GPS/face recognition)
                $attendanceRecords = $absensiModel->select('user_id')
                                                  ->where('DATE(waktu_presensi)', $date)
                                                  ->whereIn('user_id', array_column($studentsInClass, 'id'))
                                                  ->findAll();

                // Get manual attendance for the current date and class (izin, sakit, alpa)
                $manualAttendanceRecords = $absensiManualModel->select('user_id, jenis')
                                                              ->where('tanggal', $date)
                                                              ->whereIn('user_id', array_column($studentsInClass, 'id'))
                                                              ->findAll();

                // Initialize counters
                $hadir = 0;
                $izin = 0;
                $sakit = 0;
                $alpha = 0;
                $presentStudents = []; // To track students who have at least one record

                // Process GPS/face recognition attendance
                foreach ($attendanceRecords as $record) {
                    if (!in_array($record['user_id'], $presentStudents)) {
                        $presentStudents[] = $record['user_id'];
                        $hadir++; // Count as present (hadir)
                    }
                }

                // Process manual attendance
                foreach ($manualAttendanceRecords as $record) {
                    if (!in_array($record['user_id'], $presentStudents)) {
                        $presentStudents[] = $record['user_id'];
                        // Count based on jenis
                        switch ($record['jenis']) {
                            case 'izin':
                                $izin++;
                                break;
                            case 'sakit':
                                $sakit++;
                                break;
                            case 'alpa':
                                $alpha++;
                                break;
                        }
                    }
                }

                // Calculate alpha: total students in class - students with any record
                $alpha += $totalStudentsInClass - count($presentStudents);

                $kelasInfo = $kelasModel->find($id_kelas);

                $allRecords[] = [
                    'tanggal' => $date,
                    'nama_kelas' => $kelasInfo['nama_kelas'] . ' (' . $kelasInfo['tingkat'] . ' - ' . $kelasInfo['jurusan'] . ')',
                    'total_siswa' => $totalStudentsInClass,
                    'hadir' => $hadir,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alpha' => $alpha,
                ];

                $currentDate->modify('+1 day');
            }
            
            // Calculate pagination
            $totalRecords = count($allRecords);
            $totalPages = ceil($totalRecords / $perPage);
            
            // Ensure page is within valid range
            $page = max(1, min($page, $totalPages));
            
            // Get records for current page
            $offset = ($page - 1) * $perPage;
            $data['rekap_harian'] = array_slice($allRecords, $offset, $perPage);
            
            // Pass pagination data to view
            $data['pager'] = [
                'totalRecords' => $totalRecords,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'perPage' => $perPage,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'id_kelas' => $id_kelas
            ];
        } else {
            // Initialize pager data even when no class is selected
            $data['pager'] = [
                'totalRecords' => 0,
                'totalPages' => 0,
                'currentPage' => 1,
                'perPage' => $perPage,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'id_kelas' => $id_kelas
            ];
        }

        // Pass filter values back to the view to retain selections
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['id_kelas'] = $id_kelas;

        return view('admin/rekap_harian', $data);
    }
    
    public function inputPresensiHarian()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $kelasModel = new \App\Models\Kelas();
        $userModel = new User();
        $absensiManualModel = new \App\Models\AbsensiManual();
        $absensiModel = new \App\Models\Absensi(); // Main attendance model
        
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
            // Get student IDs who have already done main attendance
            $attendedUserIdsMain = $absensiModel->where('DATE(waktu_presensi)', $tanggal)
                                               ->distinct()
                                               ->findColumn('user_id') ?? [];

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
            
            // Exclude students who have already done main attendance
            if (!empty($attendedUserIdsMain)) {
                $builder->whereNotIn('users.id', $attendedUserIdsMain);
            }

            $students = $builder->select('users.*, kelas.nama_kelas, kelas.tingkat, kelas.jurusan') // Select necessary fields
                                 ->orderBy('users.nama_lengkap', 'ASC')
                                 ->findAll();

            // Get all manual attendance for the given date and filters to determine who has not attended
            $allAbsensiManualRecords = $absensiManualModel->getAbsensiManualWithSiswa($filters)->findAll();
            $attendedUserIds = array_column($allAbsensiManualRecords, 'user_id');

            $unattendedStudents = [];
            foreach ($students as $student) {
                if (!in_array($student['id'], $attendedUserIds)) {
                    $unattendedStudents[] = $student;
                }
            }

            $data['siswa'] = $unattendedStudents;
            // $data['absensi_records'] is already populated with paginated data, so no need to overwrite.
        }

        // Pass filter values back to the view
        $data['tanggal'] = $tanggal;
        $data['tingkat'] = $tingkat;
        $data['jurusan'] = $jurusan;

        return view('admin/input_presensi_harian', $data);
    }
    
    public function simpanAbsensiManual()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $absensiManualModel = new \App\Models\AbsensiManual();
        
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
            return redirect()->back()->with('success', 'Data absensi berhasil disimpan.');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data absensi.');
        }
    }

    public function simpanAbsensiManualMassal()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $absensiManualModel = new \App\Models\AbsensiManual();
        
        // Get data from form
        $presensiData = $this->request->getPost('presensi');
        $tanggal = $this->request->getPost('tanggal');
        $tingkat = $this->request->getPost('tingkat'); // Get tingkat from form
        $jurusan = $this->request->getPost('jurusan'); // Get jurusan from form
        
        if (empty($presensiData) || !is_array($presensiData)) {
            return redirect()->back()->with('error', 'Tidak ada data presensi yang dikirim.');
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($presensiData as $userId => $data) {
            // Only process if a status is selected
            if (!empty($data['jenis'])) {
                // Check if record already exists
                $existingRecord = $absensiManualModel->getAbsensiByTanggalAndUser($tanggal, $userId);
                if ($existingRecord) {
                    continue;
                }

                $insertData = [
                    'user_id' => $userId,
                    'tanggal' => $tanggal,
                    'jenis' => $data['jenis'],
                    'keterangan' => $data['keterangan'],
                    'disetujui_oleh' => $session->get('user_id'),
                    'tanggal_disetujui' => date('Y-m-d H:i:s')
                ];

                if ($absensiManualModel->save($insertData)) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
            }
        }

        if ($successCount > 0) {
            session()->setFlashdata('success', "$successCount data absensi berhasil disimpan.");
        }
        if ($errorCount > 0) {
            session()->setFlashdata('error', "$errorCount data absensi gagal disimpan.");
        }

        // Construct the redirect URL with filter parameters
        $redirectUrl = base_url('admin/input-presensi-harian') . '?tanggal=' . $tanggal . '&tingkat=' . $tingkat . '&jurusan=' . $jurusan;

        return redirect()->to($redirectUrl);
    }
    
    public function hapusAbsensiManual($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $absensiManualModel = new \App\Models\AbsensiManual();
        
        // Check if record exists
        $record = $absensiManualModel->find($id);
        if (!$record) {
            return redirect()->back()->with('error', 'Data absensi tidak ditemukan.');
        }
        
        // Delete record
        if ($absensiManualModel->delete($id)) {
            return redirect()->back()->with('success', 'Data absensi berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus data absensi.');
        }
    }
    
    public function editAbsensiManual($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $absensiManualModel = new \App\Models\AbsensiManual();
        
        // Check if record exists
        $data['absensi'] = $absensiManualModel->find($id);
        if (!$data['absensi']) {
            return redirect()->to('/admin/input-presensi-harian')->with('error', 'Data absensi tidak ditemukan.');
        }
        
        // Get user info
        $userModel = new User();
        $data['siswa'] = $userModel->find($data['absensi']['user_id']);
        
        // Get class info
        $kelasModel = new \App\Models\Kelas();
        if ($data['siswa']['id_kelas']) {
            $data['kelas'] = $kelasModel->find($data['siswa']['id_kelas']);
        } else {
            $data['kelas'] = null;
        }

        return view('admin/edit_absensi_manual', $data);
    }
    
    public function updateAbsensiManual($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $absensiManualModel = new \App\Models\AbsensiManual();
        
        // Check if record exists
        $record = $absensiManualModel->find($id);
        if (!$record) {
            return redirect()->to('/admin/input-presensi-harian')->with('error', 'Data absensi tidak ditemukan.');
        }
        
        // Get data from form
        $jenis = $this->request->getPost('jenis');
        $keterangan = $this->request->getPost('keterangan');
        
        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'jenis' => 'required|in_list[izin,sakit,alpa]',
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
            return redirect()->to('/admin/input-presensi-harian')->with('success', 'Data absensi berhasil diupdate.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengupdate data absensi.');
        }
    }
    
    public function lockLokasi()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        try {
            $pengaturanModel = new Pengaturan();
            $pengaturan = $pengaturanModel->first();
            
            if (!$pengaturan) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Pengaturan tidak ditemukan']);
            }
            
            // Update lock status to locked (1)
            if ($pengaturanModel->update($pengaturan['id'], ['lokasi_locked' => 1])) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Lokasi berhasil dikunci']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal mengunci lokasi']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in lockLokasi: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan saat mengunci lokasi']);
        }
    }
    
    public function unlockLokasi()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        try {
            $pengaturanModel = new Pengaturan();
            $pengaturan = $pengaturanModel->first();
            
            if (!$pengaturan) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Pengaturan tidak ditemukan']);
            }
            
            // Update lock status to unlocked (0)
            if ($pengaturanModel->update($pengaturan['id'], ['lokasi_locked' => 0])) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Lokasi berhasil dibuka']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal membuka lokasi']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in unlockLokasi: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan saat membuka lokasi']);
        }
    }
}
