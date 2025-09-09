<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Kelas;
use App\Models\User;

class KelasController extends BaseController
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $kelasModel = new Kelas();
        
        // Fetch classes with wali_kelas information using the new column
        $kelas = $kelasModel->select('kelas.*, users.nama_lengkap as nama_walikelas, users.username as nip_walikelas')
                                    ->join('users', 'users.id = kelas.wali_kelas_user_id', 'left')
                                    ->paginate(10, 'kelas'); // Use paginate() and define a group name

        $data['kelas'] = $kelas;
        $data['pager'] = $kelasModel->pager; // Pass the pager object

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
        $userRoleModel = new \App\Models\UserRole();
        $data['walikelas_list'] = $userRoleModel->getUsersByRole('wali_kelas');
        log_message('debug', 'KelasController@tambah: walikelas_list passed to view: ' . json_encode($data['walikelas_list']));

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
            'wali_kelas_user_id' => $this->request->getPost('wali_kelas_user_id'), // Add this line
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
        $userRoleModel = new \App\Models\UserRole();
        $data['walikelas_list'] = $userRoleModel->getUsersByRole('wali_kelas');
        log_message('debug', 'KelasController@edit: walikelas_list passed to view: ' . json_encode($data['walikelas_list']));

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
            'wali_kelas_user_id' => $this->request->getPost('wali_kelas_user_id'), // Add this line
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
}