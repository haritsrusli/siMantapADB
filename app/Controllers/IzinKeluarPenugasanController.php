<?php

namespace App\Controllers;

use App\Models\IzinKeluarPenugasan;
use App\Models\User;
use App\Models\Kelas; // Tambahkan ini

class IzinKeluarPenugasanController extends BaseController
{
    public function __construct()
    {
        $this->session = session();
        helper(['form', 'url']);
    }

    public function index()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $userModel = new User();
        $penugasanModel = new IzinKeluarPenugasan();

        // Get all teachers that can be assigned (guru and admin)
        $data['teachers'] = $userModel->whereIn('role', ['guru', 'admin'])->findAll();

        // Get current assignments with user names
        $data['assignments'] = $penugasanModel
            ->select('izin_keluar_penugasan.*, users.nama_lengkap')
            ->join('users', 'users.id = izin_keluar_penugasan.user_id')
            ->findAll();

        // Group assignments by role for easy display in the view
        $data['grouped_assignments'] = [];
        foreach ($data['assignments'] as $assignment) {
            $data['grouped_assignments'][$assignment['role']][] = $assignment;
        }

        // Get automatic Wali Kelas from the User table
        $data['auto_walikelas'] = $userModel->where('role', 'wali_kelas')->findAll();

        $data['available_roles'] = ['guru_kelas', 'wakil_kurikulum', 'guru_piket'];

        return view('admin/penugasan_izin/index', $data);
    }

    public function assign()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $penugasanModel = new IzinKeluarPenugasan();

        $userId = $this->request->getPost('user_id');
        $role = $this->request->getPost('role');

        // Basic validation
        if (empty($userId) || empty($role)) {
            return redirect()->back()->with('error', 'User dan Role harus dipilih.');
        }

        // Validate role
        $availableRoles = ['guru_kelas', 'wakil_kurikulum', 'guru_piket'];
        if (!in_array($role, $availableRoles)) {
            return redirect()->back()->with('error', 'Role tidak valid.');
        }

        // Check if user exists and has appropriate role
        $userModel = new User();
        $user = $userModel->find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        // Check if user has appropriate role (guru or admin)
        if (!in_array($user['role'], ['guru', 'admin'])) {
            return redirect()->back()->with('error', 'Hanya Guru atau Admin yang dapat ditugaskan untuk peran ini.');
        }

        // Check if assignment already exists
        $existing = $penugasanModel->where('user_id', $userId)->where('role', $role)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Guru tersebut sudah memiliki peran yang sama.');
        }

        $data = [
            'user_id' => $userId,
            'role'    => $role,
        ];

        if ($penugasanModel->save($data)) {
            return redirect()->back()->with('success', 'Peran berhasil ditugaskan.');
        } else {
            return redirect()->back()->with('error', 'Gagal menugaskan peran.');
        }
    }

    public function unassign($id = null)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to('/auth');
        }

        $penugasanModel = new IzinKeluarPenugasan();

        // Check if assignment exists
        $assignment = $penugasanModel->find($id);
        if (!$assignment) {
            return redirect()->back()->with('error', 'Tugas tidak ditemukan.');
        }

        if ($penugasanModel->delete($id)) {
            return redirect()->back()->with('success', 'Tugas berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus tugas.');
        }
    }
}