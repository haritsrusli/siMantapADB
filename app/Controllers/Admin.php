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
        $data['kelas'] = $kelasModel->getKelasWithWalikelas();
        
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
            return redirect()->back()->with('error', 'Semua field harus diisi');
        }

        // Validate numeric values
        if (!is_numeric($latitude) || !is_numeric($longitude) || !is_numeric($radius)) {
            return redirect()->back()->with('error', 'Koordinat dan radius harus berupa angka');
        }

        // Validate coordinate ranges
        $lat = floatval($latitude);
        $lon = floatval($longitude);
        $rad = intval($radius);

        if ($lat < -90 || $lat > 90) {
            return redirect()->back()->with('error', 'Latitude harus antara -90 dan 90');
        }

        if ($lon < -180 || $lon > 180) {
            return redirect()->back()->with('error', 'Longitude harus antara -180 dan 180');
        }

        if ($rad <= 0) {
            return redirect()->back()->with('error', 'Radius harus lebih besar dari 0');
        }

        $pengaturanModel = new Pengaturan();
        $pengaturan = $pengaturanModel->first();

        $data = [
            'lokasi_latitude' => $lat,
            'lokasi_longitude' => $lon,
            'radius_meter' => $rad,
            'jam_masuk_senin' => $jamMasukSenin,
            'jam_pulang_senin' => $jamPulangSenin,
            'jam_masuk_selasa' => $jamMasukSelasa,
            'jam_pulang_selasa' => $jamPulangSelasa,
            'jam_masuk_rabu' => $jamMasukRabu,
            'jam_pulang_rabu' => $jamPulangRabu,
            'jam_masuk_kamis' => $jamMasukKamis,
            'jam_pulang_kamis' => $jamPulangKamis,
            'jam_masuk_jumat' => $jamMasukJumat,
            'jam_pulang_jumat' => $jamPulangJumat,
            'jam_masuk_sabtu' => $jamMasukSabtu,
            'jam_pulang_sabtu' => $jamPulangSabtu,
            'jam_masuk_minggu' => $jamMasukMinggu,
            'jam_pulang_minggu' => $jamPulangMinggu,
        ];

        try {
            if ($pengaturan) {
                // Update existing setting
                if ($pengaturanModel->update($pengaturan['id'], $data)) {
                    return redirect()->back()->with('success', 'Pengaturan lokasi berhasil diperbarui');
                } else {
                    return redirect()->back()->with('error', 'Gagal memperbarui pengaturan lokasi');
                }
            } else {
                // Create new setting
                if ($pengaturanModel->save($data)) {
                    return redirect()->back()->with('success', 'Pengaturan lokasi berhasil disimpan');
                } else {
                    return redirect()->back()->with('error', 'Gagal menyimpan pengaturan lokasi');
                }
            }
        } catch (\Exception $e) {
            // Log error
            log_message('error', 'Error saving pengaturan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan pengaturan lokasi');
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

        $data = [];
        // Tambahkan hari Sabtu dan Minggu
        $hariNames = ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'];

        foreach ($hariNames as $hari) {
            $data['jam_masuk_' . $hari] = $this->request->getPost('jam_masuk_' . $hari);
            $data['jam_pulang_' . $hari] = $this->request->getPost('jam_pulang_' . $hari);
        }

        try {
            if ($pengaturan) {
                // Update existing setting
                if ($pengaturanModel->update($pengaturan['id'], $data)) {
                    return redirect()->to('/admin/pengaturan-presensi')->with('success', 'Jam presensi berhasil diperbarui');
                } else {
                    return redirect()->to('/admin/pengaturan-presensi')->with('error', 'Gagal memperbarui jam presensi');
                }
            } else {
                // Create new setting if no existing one (though it should be created by pengaturanPresensi)
                if ($pengaturanModel->save($data)) {
                    return redirect()->to('/admin/pengaturan-presensi')->with('success', 'Jam presensi berhasil disimpan');
                } else {
                    return redirect()->to('/admin/pengaturan-presensi')->with('error', 'Gagal menyimpan jam presensi');
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Error saving jam presensi: ' . $e->getMessage());
            return redirect()->to('/admin/pengaturan-presensi')->with('error', 'Terjadi kesalahan saat menyimpan jam presensi');
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
        $data['siswa'] = $userModel->where('role', 'siswa')->findAll();

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
        $data['guru'] = $userModel->whereIn('role', ['guru', 'wali_kelas'])->findAll();
        
        // Mendapatkan data kelas untuk ditampilkan
        $kelasModel = new \App\Models\Kelas();
        $data['kelas'] = $kelasModel->findAll();

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

    
}
