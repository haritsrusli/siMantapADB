<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'username',
        'password',
        'nama_lengkap',
        'role',
        'id_kelas', // Untuk relasi dengan kelas jika role adalah wali_kelas
        'foto_profil',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'username' => 'required|alpha_numeric_space|min_length[3]|max_length[100]',
        'password' => 'required|min_length[6]|max_length[255]',
        'nama_lengkap' => 'required|min_length[3]|max_length[150]',
        'role' => 'required|in_list[admin,siswa,guru,wali_kelas,guru_piket]', // Menambahkan guru_piket sebagai role baru
    ];
    protected $validationMessages   = [
        'username' => [
            'required' => 'Username harus diisi',
            'alpha_numeric_space' => 'Username hanya boleh mengandung huruf, angka, dan spasi',
            'min_length' => 'Username minimal 3 karakter',
            'max_length' => 'Username maksimal 100 karakter',
        ],
        'password' => [
            'required' => 'Password harus diisi',
            'min_length' => 'Password minimal 6 karakter',
            'max_length' => 'Password maksimal 255 karakter',
        ],
        'nama_lengkap' => [
            'required' => 'Nama lengkap harus diisi',
            'min_length' => 'Nama lengkap minimal 3 karakter',
            'max_length' => 'Nama lengkap maksimal 150 karakter',
        ],
        'role' => [
            'required' => 'Role harus dipilih',
            'in_list' => 'Role hanya boleh admin, siswa, guru, wali_kelas, atau guru_piket',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
    // Hash password before insert or update
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        
        return $data;
    }
    
    // Mendapatkan semua walikelas dengan kelas yang diampu
    public function getAllWaliKelasWithKelas()
    {
        return $this->select('users.*, kelas.nama_kelas, kelas.tingkat, kelas.jurusan')
            ->join('kelas', 'kelas.id = users.id_kelas', 'left')
            ->where('users.role', 'wali_kelas')
            ->findAll();
    }
    
    // Mendapatkan walikelas berdasarkan id kelas
    public function getWaliKelasByIdKelas($idKelas)
    {
        return $this->select('users.*, kelas.nama_kelas, kelas.tingkat, kelas.jurusan')
            ->join('kelas', 'kelas.id = users.id_kelas', 'left')
            ->where('users.role', 'wali_kelas')
            ->where('users.id_kelas', $idKelas)
            ->first();
    }
    
    /**
     * Mendapatkan semua role untuk user tertentu
     */
    public function getUserRoles($userId)
    {
        $userRoleModel = new \App\Models\UserRole();
        return $userRoleModel->getRoleNamesByUserId($userId);
    }
    
    /**
     * Mengecek apakah user memiliki role tertentu
     */
    public function userHasRole($userId, $role)
    {
        $userRoleModel = new \App\Models\UserRole();
        return $userRoleModel->userHasRole($userId, $role);
    }
    
    /**
     * Mendapatkan role utama user (dari kolom role) dan role tambahan
     */
    public function getAllUserRoles($userId)
    {
        $roles = [$this->select('role')->where('id', $userId)->first()['role']];
        
        $userRoleModel = new \App\Models\UserRole();
        $additionalRoles = $userRoleModel->getRoleNamesByUserId($userId);
        
        return array_unique(array_merge($roles, $additionalRoles));
    }
}
