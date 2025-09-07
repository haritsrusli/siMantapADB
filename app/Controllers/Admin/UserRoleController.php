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
        $data['availableRoles'] = ['admin', 'siswa', 'guru', 'wali_kelas', 'guru_piket', 'ketua_kelas', 'sekretaris'];
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
        $availableRoles = ['admin', 'siswa', 'guru', 'wali_kelas', 'guru_piket', 'ketua_kelas', 'sekretaris'];
        foreach ($roles as $role) {
            if (!in_array($role, $availableRoles)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Role tidak valid: ' . $role]);
            }
        }

        // Custom validation based on main role
        $mainRole = $user['role'];
        if ($mainRole === 'siswa') {
            foreach ($roles as $role) {
                if (!in_array($role, ['ketua_kelas', 'sekretaris'])) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Siswa hanya bisa memiliki role tambahan Ketua Kelas atau Sekretaris.']);
                }
            }
        }

        if ($mainRole === 'guru' || $mainRole === 'wali_kelas') {
            foreach ($roles as $role) {
                if (in_array($role, ['siswa', 'ketua_kelas', 'sekretaris'])) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Guru atau Wali Kelas tidak bisa memiliki role Siswa, Ketua Kelas, atau Sekretaris.']);
                }
            }
        }

        // Set roles untuk user
        try {
            $userRoleModel->setRolesForUser($userId, $roles);
            
            session()->setFlashdata('success', 'Roles berhasil diperbarui');
            return $this->response->setJSON(['status' => 'success', 'message' => 'Roles berhasil diperbarui']);
        } catch (\Exception $e) {
            log_message('error', 'Error updating user roles: ' . $e->getMessage());
            session()->setFlashdata('error', 'Gagal memperbarui roles');
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
        $availableRoles = ['admin', 'siswa', 'guru', 'wali_kelas', 'guru_piket', 'ketua_kelas', 'sekretaris'];
        if (!in_array($role, $availableRoles)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Role tidak valid']);
        }

        $userModel = new User();
        $user = $userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'User tidak ditemukan']);
        }

        // Custom validation based on main role
        $mainRole = $user['role'];
        if ($mainRole === 'siswa') {
            if (!in_array($role, ['ketua_kelas', 'sekretaris'])) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Siswa hanya bisa memiliki role tambahan Ketua Kelas atau Sekretaris.']);
            }
        }

        if ($mainRole === 'guru' || $mainRole === 'wali_kelas') {
            if (in_array($role, ['siswa', 'ketua_kelas', 'sekretaris'])) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Guru atau Wali Kelas tidak bisa memiliki role Siswa, Ketua Kelas, atau Sekretaris.']);
            }
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
