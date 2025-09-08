<?php

namespace App\Controllers;

use App\Models\IzinKeluar;
use App\Models\IzinKeluarBersama;
use App\Models\IzinKeluarPenugasan;
use App\Models\User;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;

class IzinKeluarController extends ResourceController
{
    use ResponseTrait;

    protected $session;

    public function __construct()
    {
        $this->session = session();
        helper(['form', 'url']);
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $role = $this->session->get('role');
        $userId = $this->session->get('user_id');
        $model = new IzinKeluar();
        $penugasanModel = new IzinKeluarPenugasan();

        $data = [];
        $model->select('izin_keluar.*, siswa.nama_lengkap as nama_siswa')
              ->join('users as siswa', 'siswa.id = izin_keluar.siswa_id');

        switch ($role) {
            case 'admin':
                $data['izin_requests'] = $model->findAll();
                break;
            case 'siswa':
                $data['izin_requests'] = $model->where('siswa_id', $userId)->findAll();
                break;
            default: // Handles all teacher roles
                // Find which approval roles this user is assigned to
                $assignedRoles = $penugasanModel->where('user_id', $userId)->findColumn('role');
                
                $statusesToQuery = [];
                if ($assignedRoles) {
                    foreach ($assignedRoles as $assignedRole) {
                        // Map the assigned role to a status
                        $statusesToQuery[] = 'diproses_' . $assignedRole;
                    }
                }

                // Also check for requests directly assigned to this user as guru_kelas
                $data['izin_requests'] = $model->groupStart()
                                                ->whereIn('status', $statusesToQuery)
                                                ->orWhere('guru_kelas_id', $userId)
                                            ->groupEnd()
                                            ->findAll();
                break;
        }

        // This will be the main view for the module
        return view('izin_keluar/index', $data);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/auth');
        }

        $model = new IzinKeluar();
        $data['izin'] = $model
            ->select('izin_keluar.*, siswa.nama_lengkap as nama_siswa, gk.nama_lengkap as nama_guru_kelas, wk.nama_lengkap as nama_wali_kelas, wku.nama_lengkap as nama_wakil_kurikulum, gp.nama_lengkap as nama_guru_piket, p.nama_lengkap as nama_penolak')
            ->join('users as siswa', 'siswa.id = izin_keluar.siswa_id', 'left')
            ->join('users as gk', 'gk.id = izin_keluar.guru_kelas_id', 'left')
            ->join('users as wk', 'wk.id = izin_keluar.wali_kelas_id', 'left')
            ->join('users as wku', 'wku.id = izin_keluar.wakil_kurikulum_id', 'left')
            ->join('users as gp', 'gp.id = izin_keluar.guru_piket_id', 'left')
            ->join('users as p', 'p.id = izin_keluar.penolak_id', 'left')
            ->find($id);

        if (!$data['izin']) {
            return $this->failNotFound('Permintaan izin tidak ditemukan');
        }

        // Add authorization check later if needed

        $bersamaModel = new IzinKeluarBersama();
        $data['bersama'] = $bersamaModel
            ->select('users.nama_lengkap')
            ->join('users', 'users.id = izin_keluar_bersama.siswa_id')
            ->where('izin_keluar_id', $id)
            ->findAll();

        return view('izin_keluar/show', $data);
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'siswa') {
            return redirect()->to('/auth');
        }
        
        $userModel = new User();
        // Get other students for the 'accompanying friends' feature
        $data['students'] = $userModel->where('role', 'siswa')->where('id !=', $this->session->get('user_id'))->findAll();

        return view('izin_keluar/new', $data);
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'siswa') {
            return redirect()->to('/auth');
        }

        $model = new IzinKeluar();

        $data = [
            'siswa_id'   => $this->session->get('user_id'),
            'jenis_izin' => $this->request->getVar('jenis_izin'),
            'alasan'     => $this->request->getVar('alasan'),
            'jam_keluar' => $this->request->getVar('jam_keluar'),
            'status'     => 'diajukan' // Initial status
        ];

        // TODO: Add validation
        
        $izinId = $model->insert($data);

        if (!$izinId) {
            $errors = $model->errors() ? implode(', ', $model->errors()) : 'Gagal menyimpan data.';
            return redirect()->back()->withInput()->with('error', $errors);
        }

        // Handle accompanying students if any
        $bersamaSiswaIds = $this->request->getVar('bersama_siswa_ids');
        if (!empty($bersamaSiswaIds)) {
            $bersamaModel = new IzinKeluarBersama();
            foreach ($bersamaSiswaIds as $siswaId) {
                $bersamaModel->insert([
                    'izin_keluar_id' => $izinId,
                    'siswa_id'       => $siswaId
                ]);
            }
        }

        return redirect()->to('/izin-keluar')->with('success', 'Permintaan izin berhasil diajukan. Menunggu persetujuan Admin.');
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        // TODO: Implement edit form for admins/teachers if needed
        return $this->respond(['message' => 'This will be the form for editing a leave request.']);
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->failUnauthorized('Anda harus login.');
        }

        $model = new IzinKeluar();
        $izin = $model->find($id);

        if (!$izin) {
            return $this->failNotFound('Permintaan izin tidak ditemukan.');
        }

        $action = $this->request->getVar('action'); // 'approve' or 'reject'
        $catatan = $this->request->getVar('catatan_penolakan');
        $jam_kembali = $this->request->getVar('jam_kembali');

        $userId = $this->session->get('user_id');

        // --- Permission Check ---
        $hasPermission = false;
        $status = $izin['status'];

        if ($status === 'diproses_guru_kelas' && $izin['guru_kelas_id'] == $userId) {
            $hasPermission = true;
        } else if ($status === 'diproses_wali_kelas' && $izin['wali_kelas_id'] == $userId) {
            $hasPermission = true;
        } else {
            $penugasanModel = new IzinKeluarPenugasan();
            // Map status to role, e.g., 'diproses_wakil_kurikulum' -> 'wakil_kurikulum'
            $requiredRole = str_replace('diproses_', '', $status);
            $isAssigned = $penugasanModel->where('user_id', $userId)->where('role', $requiredRole)->first();
            if ($isAssigned) {
                $hasPermission = true;
            }
        }

        if (!$hasPermission) {
            return $this->failForbidden('Anda tidak memiliki izin untuk memproses tahap ini.');
        }
        // --- End Permission Check ---

        $newData = [];

        if ($action === 'reject') {
            $newData['status'] = 'ditolak';
            $newData['penolak_id'] = $userId;
            $newData['catatan_penolakan'] = $catatan;
        } else if ($action === 'approve') {
            switch ($status) {
                case 'diproses_guru_kelas':
                    $userModel = new User();
                    $siswa = $userModel->find($izin['siswa_id']);
                    if (!$siswa || !$siswa['id_kelas']) {
                        return $this->fail('Data kelas siswa tidak ditemukan, tidak dapat meneruskan ke Wali Kelas.');
                    }

                    $kelasModel = new \App\Models\Kelas();
                    $kelas = $kelasModel->find($siswa['id_kelas']);
                    if (!$kelas || !$kelas['id_walikelas']) {
                        return $this->fail('Data Wali Kelas untuk kelas ini tidak ditemukan.');
                    }

                    $newData['wali_kelas_id'] = $kelas['id_walikelas'];
                    $newData['status'] = 'diproses_wali_kelas';
                    break;
                case 'diproses_wali_kelas':
                    $newData['status'] = 'diproses_wakil_kurikulum';
                    break;
                case 'diproses_wakil_kurikulum':
                    $newData['status'] = 'diproses_guru_piket';
                    break;
                case 'diproses_guru_piket':
                    if (empty($jam_kembali)) {
                        return $this->fail('Jam kembali harus diisi oleh Guru Piket.');
                    }
                    $newData['status'] = 'disetujui';
                    $newData['jam_kembali'] = $jam_kembali;
                    break;
                default:
                    return $this->fail('Status izin tidak valid untuk persetujuan.');
            }
        } else {
            return $this->fail('Aksi tidak valid.');
        }

        if ($model->update($id, $newData)) {
            return $this->respondUpdated($newData, 'Status izin berhasil diperbarui.');
        } else {
            return $this->fail($model->errors());
        }
    }

    /**
     * Delete the designated resource
     *
     * @return mixed
     */
    public function assignForm($id = null)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failForbidden('You are not allowed to perform this action.');
        }

        $izinModel = new IzinKeluar();
        $penugasanModel = new IzinKeluarPenugasan();

        $data['izin'] = $izinModel->find($id);
        if (!$data['izin'] || $data['izin']['status'] !== 'diajukan') {
            return $this->failNotFound('Permintaan izin tidak valid atau sudah diproses.');
        }

        // Get available 'guru_kelas' from penugasan table
        $data['guru_kelas_list'] = $penugasanModel
            ->select('users.id, users.nama_lengkap')
            ->join('users', 'users.id = izin_keluar_penugasan.user_id')
            ->where('izin_keluar_penugasan.role', 'guru_kelas')
            ->findAll();

        // This will be a view file
        return view('admin/izin_keluar/assign_form', $data);
    }

    public function assignAction($id = null)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failForbidden('You are not allowed to perform this action.');
        }

        $izinModel = new IzinKeluar();
        $izin = $izinModel->find($id);

        if (!$izin || $izin['status'] !== 'diajukan') {
            return redirect()->back()->with('error', 'Permintaan izin tidak valid atau sudah diproses.');
        }

        $guruKelasId = $this->request->getPost('guru_kelas_id');
        if (empty($guruKelasId)) {
            return redirect()->back()->with('error', 'Guru Kelas harus dipilih.');
        }

        $data = [
            'guru_kelas_id' => $guruKelasId,
            'status' => 'diproses_guru_kelas' // Move to the next stage
        ];

        if ($izinModel->update($id, $data)) {
            // In a real app, a notification would be sent here
            return redirect()->to('/izin-keluar')->with('success', 'Guru Kelas berhasil ditugaskan.');
        } else {
            return redirect()->back()->with('error', 'Gagal menugaskan Guru Kelas.');
        }
    }

    /**
     * Delete the designated resource
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failForbidden('You are not allowed to perform this action.');
        }

        $model = new IzinKeluar();

        if (!$model->find($id)) {
            return $this->failNotFound('Leave request not found');
        }

        if ($model->delete($id)) {
            return $this->respondDeleted(['id' => $id], 'Leave request deleted successfully.');
        }

        return $this->fail('Failed to delete leave request.');
    }
}