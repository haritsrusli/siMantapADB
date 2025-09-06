<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\User;
use App\Models\UserRole;
use CodeIgniter\HTTP\ResponseInterface;

class UserRoleController extends BaseController
{
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        $users = $userModel->findAll();

        $data['users'] = $users;
        $data['title'] = 'Manajemen User Roles';
        return view('admin/user_roles/index', $data);
    }

    public function edit($userId)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        $userRoleModel = new UserRole();
        
        $user = $userModel->find($userId);
        if (!$user) {
            return redirect()->to('/admin/user-roles')->with('error', 'User tidak ditemukan');
        }

        // Mendapatkan semua role untuk user ini
        $userRoles = $userRoleModel->getRoleNamesByUserId($userId);

        $data['user'] = $user;
        $data['userRoles'] = $userRoles;
        $data['availableRoles'] = ['admin', 'siswa', 'guru', 'wali_kelas', 'guru_piket'];
        $data['title'] = 'Edit User Roles';

        return view('admin/user_roles/edit', $data);
    }

    public function update($userId)
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        $userModel = new User();
        $userRoleModel = new UserRole();
        
        $user = $userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User tidak ditemukan']);
        }

        // Mendapatkan role dari request
        $roles = $this->request->getPost('roles');
        
        if (!is_array($roles)) {
            $roles = [];
        }

        // Validasi role
        $availableRoles = ['admin', 'siswa', 'guru', 'wali_kelas', 'guru_piket'];
        foreach ($roles as $role) {
            if (!in_array($role, $availableRoles)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Role tidak valid: ' . $role]);
            }
        }

        // Jika tidak ada role yang dipilih, set minimal satu role
        if (empty($roles)) {
            // Gunakan role default berdasarkan role utama user
            $roles = [$user['role']];
        }

        // Set roles untuk user
        try {
            $userRoleModel->setRolesForUser($userId, $roles);
            
            // Update role utama user jika perlu
            $primaryRole = $roles[0]; // Gunakan role pertama sebagai role utama
            if ($user['role'] !== $primaryRole) {
                $userModel->update($userId, ['role' => $primaryRole]);
            }
            
            return $this->response->setJSON(['status' => 'success', 'message' => 'Roles berhasil diperbarui']);
        } catch (\Exception $e) {
            log_message('error', 'Error updating user roles: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui roles']);
        }
    }

    public function addRole()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        $userRoleModel = new UserRole();
        
        $userId = $this->request->getPost('user_id');
        $role = $this->request->getPost('role');
        
        if (!$userId || !$role) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }

        // Validasi role
        $availableRoles = ['admin', 'siswa', 'guru', 'wali_kelas', 'guru_piket'];
        if (!in_array($role, $availableRoles)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Role tidak valid']);
        }

        try {
            $userRoleModel->addRoleToUser($userId, $role);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Role berhasil ditambahkan']);
        } catch (\Exception $e) {
            log_message('error', 'Error adding user role: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menambahkan role']);
        }
    }

    public function removeRole()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        $userRoleModel = new UserRole();
        
        $userId = $this->request->getPost('user_id');
        $role = $this->request->getPost('role');
        
        if (!$userId || !$role) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }

        try {
            $userRoleModel->removeRoleFromUser($userId, $role);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Role berhasil dihapus']);
        } catch (\Exception $e) {
            log_message('error', 'Error removing user role: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus role']);
        }
    }
}