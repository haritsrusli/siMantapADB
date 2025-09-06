<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Kelas;
use App\Models\User;

class KelasController extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $kelasModel = new Kelas();
        $data['kelas'] = $kelasModel->getKelasWithWalikelas();
        
        // Mendapatkan semua user dengan role wali_kelas untuk dropdown
        $userModel = new User();
        $data['walikelas'] = $userModel->where('role', 'wali_kelas')->findAll();

        return view('admin/manajemen_kelas', $data);
    }
    
    public function tambah()
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
    
    public function simpan()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $kelasModel = new Kelas();
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
    
    public function edit($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $kelasModel = new Kelas();
        $data['kelas'] = $kelasModel->find($id);

        if (!$data['kelas']) {
            return redirect()->to('/admin/kelas')->with('error', 'Data kelas tidak ditemukan');
        }
        
        // Mendapatkan semua user dengan role wali_kelas untuk dropdown
        $userModel = new User();
        $data['walikelas'] = $userModel->where('role', 'wali_kelas')->findAll();

        return view('admin/edit_kelas', $data);
    }
    
    public function update($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $kelasModel = new Kelas();
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
    
    public function hapus($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $kelasModel = new Kelas();
        
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
    
    // Method untuk mendapatkan daftar kelas dalam format JSON (untuk AJAX)
    public function getKelasList()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        $kelasModel = new Kelas();
        $kelas = $kelasModel->findAll();
        
        return $this->response->setJSON(['status' => 'success', 'data' => $kelas]);
    }
    
    // Method untuk menetapkan walikelas ke kelas
    public function setWalikelas($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $kelasModel = new Kelas();
        $userModel = new User();
        
        // Cek apakah kelas ada
        $kelas = $kelasModel->find($id);
        if (!$kelas) {
            return redirect()->to('/admin/kelas')->with('error', 'Data kelas tidak ditemukan');
        }
        
        // Mendapatkan ID user walikelas dari POST
        $idUserWalikelas = $this->request->getPost('id_user_walikelas');
        
        // Validasi input
        if (!$idUserWalikelas) {
            return redirect()->back()->with('error', 'Harap pilih walikelas');
        }
        
        // Cek apakah user walikelas ada dan memiliki role wali_kelas
        $walikelas = $userModel->find($idUserWalikelas);
        if (!$walikelas || $walikelas['role'] !== 'wali_kelas') {
            return redirect()->back()->with('error', 'User yang dipilih bukan walikelas');
        }
        
        // Update id_kelas pada user walikelas
        if ($userModel->update($idUserWalikelas, ['id_kelas' => $id])) {
            return redirect()->to('/admin/kelas')->with('success', 'Walikelas berhasil ditetapkan untuk kelas ' . $kelas['nama_kelas']);
        } else {
            return redirect()->back()->with('error', 'Gagal menetapkan walikelas');
        }
    }
}