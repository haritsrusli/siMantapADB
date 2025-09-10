<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User;

class Guru extends BaseController
{
    public function index()
    {
        // Check if user is logged in and is a teacher
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        // Check if user has a teacher role
        $teacherRoles = ['guru', 'wali_kelas', 'wakil_kurikulum', 'guru_piket'];
        $userRole = $session->get('role');
        
        if (!in_array($userRole, $teacherRoles)) {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        $data['user'] = $userModel->find($session->get('user_id'));

        return view('guru/dashboard', $data);
    }

    public function dashboard()
    {
        // Check if user is logged in and is a teacher
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        // Check if user has a teacher role
        $teacherRoles = ['guru', 'wali_kelas', 'wakil_kurikulum', 'guru_piket'];
        $userRole = $session->get('role');
        
        if (!in_array($userRole, $teacherRoles)) {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        $data['user'] = $userModel->find($session->get('user_id'));

        return view('guru/dashboard', $data);
    }
}