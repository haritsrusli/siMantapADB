<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\Kelas;
use App\Models\UserRole;

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
        $kelasModel = new Kelas();

        // Get search and filter parameters from request
        $search = $this->request->getGet('search');
        $role = $this->request->getGet('role');
        $id_kelas = $this->request->getGet('id_kelas');

        // Start building the query
        $userModelBuilder = $userModel->select('users.*, kelas.nama_kelas as wali_kelas_nama_kelas')
                                             ->join('kelas', 'kelas.wali_kelas_user_id = users.id', 'left');

        // Apply search filter
        if (!empty($search)) {
            $userModelBuilder = $userModelBuilder->groupStart()
                           ->like('username', $search)
                           ->orLike('nama_lengkap', $search)
                           ->groupEnd();
        }

        // Apply role filter
        if (!empty($role)) {
            $userModelBuilder = $userModelBuilder->where('role', $role);
        }

        // Apply class filter
        if (!empty($id_kelas)) {
            $userModelBuilder = $userModelBuilder->where('id_kelas', $id_kelas);
        }

        $data['users'] = $userModelBuilder->paginate(10, 'group1');
        $data['pager'] = $userModel->pager;
        
        log_message('debug', 'UserController@index: Users data passed to view: ' . json_encode($data['users']));

        // Mendapatkan data kelas untuk ditampilkan
        $data['kelas'] = $kelasModel->findAll();

        // Pass filter values back to the view to retain selections
        $data['search'] = $search;
        $data['role'] = $role;
        $data['id_kelas'] = $id_kelas;

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

        if ($role === 'wali_kelas') {
            // Check if the class is already assigned to another wali_kelas
            $existingWaliKelas = $userModel->where('id_kelas', $id_kelas)
                                           ->where('role', 'wali_kelas')
                                           ->first();
            if ($existingWaliKelas) {
                return redirect()->back()->with('error', 'Kelas ini sudah memiliki wali kelas.')->withInput();
            }
        }
        
        // Jika role bukan wali_kelas atau siswa, maka id_kelas tidak diperlukan
        if ($role !== 'wali_kelas' && $role !== 'siswa') {
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
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/user')->with('error', 'Data user tidak ditemukan');
        }
        
        // Mendapatkan data dari form
        $role = $this->request->getPost('role');
        $id_kelas = $this->request->getPost('id_kelas');

        if ($role === 'wali_kelas') {
            // Check if the class is already assigned to another wali_kelas
            $existingWaliKelas = $userModel->where('id_kelas', $id_kelas)
                                           ->where('role', 'wali_kelas')
                                           ->where('id !=', $id) // Exclude the current user
                                           ->first();
            if ($existingWaliKelas) {
                return redirect()->back()->with('error', 'Kelas ini sudah memiliki wali kelas.')->withInput();
            }
        }
        
        // Jika role bukan wali_kelas atau siswa, maka id_kelas tidak diperlukan
        if ($role !== 'wali_kelas' && $role !== 'siswa') {
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
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'role' => $role,
            'id_kelas' => $id_kelas,
        ];

        // Check if username has changed
        if ($username !== $user['username']) {
            $data['username'] = $username;
        }

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
