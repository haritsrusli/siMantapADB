<?php

namespace App\Controllers;

use App\Models\IzinKeluar;
use App\Models\IzinKeluarBersama;
use App\Models\IzinKeluarPenugasan;
use App\Models\User;
use App\Models\UserRole;
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

        // Get filter parameters
        $jenisIzin = $this->request->getGet('jenis_izin');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');

        $data = [
            'jenis_izin_filter' => $jenisIzin,
            'status_filter' => $status,
            'search_filter' => $search
        ];
        
        $model->select('izin_keluar.*, siswa.nama_lengkap as nama_siswa, gp.nama_lengkap as nama_guru_piket')
              ->join('users as siswa', 'siswa.id = izin_keluar.siswa_id', 'left')
              ->join('users as gp', 'gp.id = izin_keluar.guru_piket_id', 'left');

        // Apply filters for all roles
        if ($jenisIzin) {
            $model->where('izin_keluar.jenis_izin', $jenisIzin);
        }
        
        if ($status) {
            $model->where('izin_keluar.status', $status);
        }
        
        if ($search) {
            $model->like('siswa.nama_lengkap', $search);
        }

        switch ($role) {
            case 'admin':
                // Add pagination for admin role (10 items per page)
                $data['izin_requests'] = $model->paginate(10, 'izin_requests');
                $data['pager'] = $model->pager;
                break;
            case 'siswa':
                $data['izin_requests'] = $model->where('siswa_id', $userId)->findAll();
                break;
            default: // Handles all teacher roles
                $userModel = new User(); // Ensure user model is available

                // Base query builder
                $builder = $model->groupStart();

                // 1. Requests directly assigned to this user as Guru Kelas
                $builder->orGroupStart()
                        ->where('status', 'diproses_guru_kelas')
                        ->where('guru_kelas_id', $userId)
                        ->groupEnd();

                // 2. Requests directly assigned to this user as Wali Kelas
                $builder->orGroupStart()
                        ->where('status', 'diproses_wali_kelas')
                        ->where('wali_kelas_id', $userId)
                        ->groupEnd();

                // 3. Requests for roles assigned via penugasan or user_roles
                $assignedRoles = $penugasanModel->where('user_id', $userId)->findColumn('role') ?? [];
                $additionalRoles = $userModel->getUserRoles($userId) ?? [];
                $allRoles = array_unique(array_merge($assignedRoles, $additionalRoles));

                $statusesToQuery = [];
                if (!empty($allRoles)) {
                    foreach ($allRoles as $roleName) {
                        // These are handled by direct ID checks above
                        if ($roleName === 'guru_kelas' || $roleName === 'wali_kelas') {
                            continue;
                        }
                        $statusesToQuery[] = 'diproses_' . $roleName;
                    }
                }

                if (!empty($statusesToQuery)) {
                    $builder->orWhereIn('status', $statusesToQuery);
                }

                $data['izin_requests'] = $builder->groupEnd()->findAll();
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

        // Determine at which stage the rejection happened, if applicable
        if ($data['izin']['status'] === 'ditolak') {
            $izinData = $data['izin'];
            $penolak_id = $izinData['penolak_id'];
            $rejected_stage = null;

            if ($penolak_id == $izinData['guru_kelas_id']) {
                $rejected_stage = 'diproses_guru_kelas';
            } elseif ($penolak_id == $izinData['wali_kelas_id']) {
                $rejected_stage = 'diproses_wali_kelas';
            } elseif ($penolak_id == $izinData['wakil_kurikulum_id']) {
                $rejected_stage = 'diproses_wakil_kesiswaan'; // Note: column name is still wakil_kurikulum_id
            } elseif ($penolak_id == $izinData['guru_piket_id']) {
                $rejected_stage = 'diproses_guru_piket';
            }
            $data['izin']['rejected_at_stage'] = $rejected_stage;
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
            'jam_keluar' => null, // Jam keluar akan diisi oleh admin
            'jam_kembali' => null,
            'status'     => 'diajukan' // Initial status
        ];

        // Add validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            'jenis_izin' => 'required',
            'alasan' => 'required|min_length[5]'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', implode(', ', $validation->getErrors()));
        }
        
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
        log_message('debug', 'IzinKeluarController@update: Request received for ID ' . $id);
        log_message('debug', 'IzinKeluarController@update: Session User ID: ' . $this->session->get('user_id') . ', Role: ' . $this->session->get('role'));

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
        $jam_keluar = $this->request->getVar('jam_keluar');

        $userId = $this->session->get('user_id');

        // --- Permission Check ---
        $hasPermission = false;
        $status = $izin['status'];
        $userRole = $this->session->get('role');

        log_message('debug', 'IzinKeluarController@update: Current Izin Status: ' . $status . ', Action: ' . $action);

        // Check if user has specific role assignment for this stage
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
            } else {
                // Check if user has this role in user_roles table
                $userModel = new User();
                if ($userModel->userHasRole($userId, $requiredRole)) {
                    $hasPermission = true;
                }
            }
        }

        

        // Additional check: If user is a teacher with multiple roles, check if they have the required role
        if (!$hasPermission) {
            $userModel = new User();
            $allUserRoles = $userModel->getAllUserRoles($userId);
            $requiredRole = str_replace('diproses_', '', $status);
            
            if (in_array($requiredRole, $allUserRoles)) {
                $hasPermission = true;
            }
        }

        log_message('debug', 'IzinKeluarController@update: Has Permission: ' . ($hasPermission ? 'true' : 'false'));

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
                    $newData['status'] = 'diproses_wali_kelas';
                    break;
                case 'diproses_wali_kelas':
                    $newData['status'] = 'diproses_wakil_kesiswaan';
                    break;
                case 'diproses_wakil_kesiswaan':
                    $newData['status'] = 'diproses_guru_piket';
                    break;
                case 'diproses_guru_piket':
                    if (empty($jam_kembali)) {
                        // This might need to be handled differently, e.g., a modal in frontend
                        // For now, we assume it might be optional or set at a different stage.
                        // return $this->fail('Jam kembali harus diisi oleh Guru Piket.');
                    }
                    $newData['status'] = 'disetujui';
                    $newData['jam_kembali'] = $jam_kembali ?: $izin['jam_kembali']; // Preserve existing if not provided
                    break;
                default:
                    return $this->fail(['status' => 'error', 'message' => 'Status izin tidak valid untuk persetujuan.']);
            }
        } else {
            return $this->fail(['status' => 'error', 'message' => 'Aksi tidak valid.']);
        }

        log_message('debug', 'IzinKeluarController@update: New Data for Update: ' . json_encode($newData));

        $updateResult = $model->update($id, $newData);
        log_message('debug', 'IzinKeluarController@update: Model Update Result: ' . ($updateResult ? 'true' : 'false'));
        log_message('debug', 'IzinKeluarController@update: Model Errors: ' . json_encode($model->errors()));

        if ($updateResult) {
            return $this->respond(['status' => 'success', 'message' => 'Status izin berhasil diperbarui.']);
        } else {
            $errors = $model->errors();
            $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Gagal memperbarui status izin. Silakan coba lagi.';
            return $this->fail(['status' => 'error', 'message' => $errorMessage]);
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
        $userRoleModel = new \App\Models\UserRole();
        $userModel = new User(); // Load the User model

        $data['izin'] = $izinModel
            ->select('izin_keluar.*, users.nama_lengkap as nama_siswa')
            ->join('users', 'users.id = izin_keluar.siswa_id')
            ->find($id);
            
        if (!$data['izin']) {
            return $this->failNotFound('Permintaan izin tidak ditemukan.');
        }

        // Izinkan akses selama status belum 'disetujui' atau 'ditolak'
        if ($data['izin']['status'] === 'disetujui' || $data['izin']['status'] === 'ditolak') {
            return $this->failNotFound('Permintaan izin tidak dapat ditugaskan ulang karena sudah selesai atau ditolak.');
        }

        // Get user lists
        $data['guru_mapel_list'] = $userModel->where('role', 'guru')->findAll(); // All users with main role 'guru'
        $data['wali_kelas_list'] = $userRoleModel->getUsersByRole('wali_kelas');
        $data['wakil_kesiswaan_list'] = $userRoleModel->getUsersByRole('wakil_kesiswaan');
        $data['guru_piket_list'] = $userRoleModel->getUsersByRole('guru_piket');

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

        if (!$izin) {
            return redirect()->back()->with('error', 'Permintaan izin tidak ditemukan.');
        }

        // Allow assignment/re-assignment only for requests that are not yet finished or rejected.
        if (in_array($izin['status'], ['disetujui', 'ditolak'])) {
            return redirect()->back()->with('error', 'Izin yang sudah disetujui atau ditolak tidak dapat diubah penugasannya.');
        }

        // Get data from form
        $jamKeluar = $this->request->getPost('jam_keluar');
        $jamKembali = $this->request->getPost('jam_kembali');
        $guruKelasId = $this->request->getPost('guru_kelas_id');
        $waliKelasId = $this->request->getPost('wali_kelas_id'); // Get wali_kelas_id
        $wakilKurikulumId = $this->request->getPost('wakil_kurikulum_id');
        $guruPiketId = $this->request->getPost('guru_piket_id');

        // --- Validation ---
        $validationErrors = [];
        if (empty($jamKeluar)) {
            $validationErrors[] = 'Jam Keluar harus diisi.';
        }
        if (empty($guruKelasId)) {
            $validationErrors[] = 'Guru Kelas harus dipilih.';
        }
        if (empty($waliKelasId)) { // Validate wali_kelas_id
            $validationErrors[] = 'Wali Kelas harus dipilih.';
        }
        if (empty($wakilKurikulumId)) {
            $validationErrors[] = 'Wakil Kurikulum harus dipilih.';
        }
        if (empty($guruPiketId)) {
            $validationErrors[] = 'Guru Piket harus dipilih.';
        }

        if (!empty($validationErrors)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $validationErrors));
        }

        // Validate time format
        $jamKeluar = trim($jamKeluar);
        if (strtotime($jamKeluar) === false) {
            return redirect()->back()->withInput()->with('error', 'Format Jam Keluar tidak valid.');
        }
        $jamKeluar = date('H:i', strtotime($jamKeluar));

        if (!empty($jamKembali)) {
            $jamKembali = trim($jamKembali);
            if (strtotime($jamKembali) === false) {
                return redirect()->back()->withInput()->with('error', 'Format Jam Kembali tidak valid.');
            }
            $jamKembali = date('H:i', strtotime($jamKembali));
        }
        // --- End Validation ---

        // Prepare data for update
        $data = [
            'jam_keluar'         => $jamKeluar,
            'jam_kembali'        => $jamKembali ?: null,
            'guru_kelas_id'      => $guruKelasId,
            'wali_kelas_id'      => $waliKelasId, // Add to data array
            'wakil_kurikulum_id' => $wakilKurikulumId,
            'guru_piket_id'      => $guruPiketId,
        ];

        // Only change status if it's the very first assignment
        if ($izin['status'] === 'diajukan') {
            $data['status'] = 'diproses_guru_kelas'; // Start the workflow
        }
        // Otherwise, just update the assignments without changing the current workflow step.

        if ($izinModel->update($id, $data)) {
            return redirect()->to('/admin/izin-keluar/' . $id . '/assign')->with('success', 'Penugasan izin berhasil diperbarui.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data penugasan.');
        }
    }

    public function assignPenugasan($id_izin = null)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failForbidden('You are not allowed to perform this action.');
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
            return redirect()->back()->with('error', 'Staf tersebut sudah memiliki peran yang sama.');
        }

        $data = [
            'user_id' => $userId,
            'role'    => $role,
        ];

        if ($penugasanModel->save($data)) {
            return redirect()->back()->with('success', 'Penugasan berhasil ditambahkan.');
        } else {
            return redirect()->back()->with('error', 'Gagal menambahkan penugasan.');
        }
    }

    public function unassignPenugasan($id_izin = null, $id_penugasan = null)
    {
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return $this->failForbidden('You are not allowed to perform this action.');
        }

        $penugasanModel = new IzinKeluarPenugasan();

        // Check if assignment exists
        $assignment = $penugasanModel->find($id_penugasan);
        if (!$assignment) {
            return redirect()->back()->with('error', 'Tugas tidak ditemukan.');
        }

        if ($penugasanModel->delete($id_penugasan)) {
            return redirect()->back()->with('success', 'Tugas berhasil dihapus.');
        } else {
            return redirect()->back()->with('error', 'Gagal menghapus tugas.');
        }
    }

    /**
     * Delete the designated resource
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->failForbidden('You are not allowed to perform this action.');
        }

        $model = new IzinKeluar();
        $izin = $model->find($id);

        if (!$izin) {
            return $this->failNotFound('Leave request not found');
        }

        $role = $this->session->get('role');
        $userId = $this->session->get('user_id');

        // Check if user is authorized to delete
        if ($role === 'siswa') {
            // Siswa hanya bisa menghapus izin mereka sendiri
            if ($izin['siswa_id'] != $userId) {
                return $this->failForbidden('You are not allowed to delete this request.');
            }

            // Siswa tidak bisa menghapus jika izin sudah disetujui sepenuhnya
            if ($izin['status'] === 'disetujui') {
                return redirect()->back()->with('error', 'Tidak bisa menghapus permintaan izin karena sudah disetujui semua pihak.');
            }
        } else if ($role !== 'admin') {
            return $this->failForbidden('You are not allowed to perform this action.');
        }

        if ($model->delete($id)) {
            if ($role === 'siswa') {
                return redirect()->to('/izin-keluar')->with('success', 'Permintaan izin berhasil dihapus.');
            }
            return $this->respondDeleted(['id' => $id], 'Leave request deleted successfully.');
        }

        if ($role === 'siswa') {
            return redirect()->back()->with('error', 'Gagal menghapus permintaan izin.');
        }
        return $this->fail('Failed to delete leave request.');
    }

    public function reset($id = null)
    {
        // Authorization check
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') !== 'admin') {
            return redirect()->to('/auth')->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini.');
        }

        $model = new IzinKeluar();
        $izin = $model->find($id);

        if (!$izin) {
            return redirect()->back()->with('error', 'Permintaan izin tidak ditemukan.');
        }

        // Only rejected requests can be reset
        if ($izin['status'] !== 'ditolak') {
            return redirect()->back()->with('error', 'Hanya permintaan yang ditolak yang dapat dibuka kembali.');
        }

        $resetData = [
            'status' => 'diajukan',
            'guru_kelas_id' => null,
            'wali_kelas_id' => null,
            'wakil_kurikulum_id' => null,
            'guru_piket_id' => null,
            'penolak_id' => null,
            'catatan_penolakan' => null,
            'jam_keluar' => null,
            'jam_kembali' => null,
        ];

        if ($model->update($id, $resetData)) {
            return redirect()->to('/izin-keluar')->with('success', 'Pengajuan izin berhasil dibuka kembali dan direset ke status "Diajukan".');
        } else {
            return redirect()->back()->with('error', 'Gagal mereset pengajuan izin.');
        }
    }
}