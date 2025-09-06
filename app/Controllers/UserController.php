<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\Kelas;

class UserController extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        $data['users'] = $userModel->findAll();
        
        // Mendapatkan data kelas untuk ditampilkan
        $kelasModel = new Kelas();
        $data['kelas'] = $kelasModel->findAll();

        return view('admin/manajemen_user', $data);
    }
    
    public function tambah()
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }
        
        // Mendapatkan data kelas untuk dropdown
        $kelasModel = new Kelas();
        $data['kelas'] = $kelasModel->findAll();

        return view('admin/tambah_user', $data);
    }
    
    public function simpan()
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
        
        // Untuk siswa, gunakan NIS sebagai username
        // Untuk guru/wali kelas, gunakan NIP sebagai username
        $username = '';
        if ($role === 'siswa') {
            $username = $this->request->getPost('nis');
        } else {
            $username = $this->request->getPost('nip');
        }
        
        $data = [
            'username' => $username,
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
            return redirect()->back()->with('error', 'Gagal menyimpan data user: ' . $errorMessage);
        }

        return redirect()->to('/admin/user')->with('success', 'Data user berhasil ditambahkan');
    }
    
    public function edit($id)
    {
        // Check if user is logged in and is admin
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        $data['user'] = $userModel->find($id);

        if (!$data['user']) {
            return redirect()->to('/admin/user')->with('error', 'Data user tidak ditemukan');
        }
        
        // Mendapatkan data kelas untuk dropdown
        $kelasModel = new Kelas();
        $data['kelas'] = $kelasModel->findAll();

        return view('admin/edit_user', $data);
    }
    
    public function update($id)
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
        
        // Untuk siswa, gunakan NIS sebagai username
        // Untuk guru/wali kelas, gunakan NIP sebagai username
        $username = '';
        if ($role === 'siswa') {
            $username = $this->request->getPost('nis');
        } else {
            $username = $this->request->getPost('nip');
        }
        
        $data = [
            'username' => $username,
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
            return redirect()->back()->with('error', 'Gagal mengupdate data user: ' . $errorMessage);
        }

        return redirect()->to('/admin/user')->with('success', 'Data user berhasil diupdate');
    }
    
    public function hapus($id)
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
            return redirect()->to('/admin/user')->with('error', 'Data user tidak ditemukan');
        }
        
        // Prevent deleting admin users
        if ($user['role'] === 'admin') {
            return redirect()->to('/admin/user')->with('error', 'Tidak dapat menghapus akun admin');
        }

        try {
            if ($userModel->delete($id)) {
                return redirect()->to('/admin/user')->with('success', 'Data user berhasil dihapus');
            } else {
                return redirect()->to('/admin/user')->with('error', 'Gagal menghapus data user');
            }
        } catch (\Exception $e) {
            // Log error
            log_message('error', 'Error deleting user: ' . $e->getMessage());
            return redirect()->to('/admin/user')->with('error', 'Terjadi kesalahan saat menghapus data user');
        }
    }
}