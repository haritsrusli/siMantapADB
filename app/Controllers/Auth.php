<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    public function index()
    {
        // Check if user is already logged in
        $session = session();
        if ($session->get('isLoggedIn')) {
            if ($session->get('role') === 'admin') {
                return redirect()->to('/admin/dashboard');
            } else {
                return redirect()->to('/siswa/dashboard');
            }
        }
        
        return view('auth/login');
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Validate input
        if (empty($username) || empty($password)) {
            return redirect()->back()->with('error', 'Username dan password harus diisi');
        }

        try {
            $userModel = new User();
            $user = $userModel->where('username', $username)->first();

            if ($user && password_verify($password, $user['password'])) {
                $session = session();
                $session->set([
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'isLoggedIn' => true,
                ]);

                $teacherRoles = ['guru', 'wali_kelas', 'wakil_kurikulum', 'guru_piket'];

                if ($user['role'] === 'admin') {
                    return redirect()->to('/admin/dashboard');
                } else if (in_array($user['role'], $teacherRoles)) {
                    return redirect()->to('/izin-keluar'); // Redirect teachers to the leave module
                } else if ($user['role'] === 'siswa') {
                    return redirect()->to('/siswa/dashboard');
                } else {
                    // Fallback for any other roles
                    return redirect()->to('/');
                }
            } else {
                return redirect()->back()->with('error', 'Username atau password salah');
            }
        } catch (\Exception $e) {
            // Log error
            log_message('error', 'Error in login: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat login');
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/auth');
    }
}
