<?php

namespace App\Models;

use CodeIgniter\Model;

class UserRole extends Model
{
    protected $table            = 'user_roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'role',
        'created_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    // Validation
    protected $validationRules      = [
        'user_id' => 'required|is_natural_no_zero',
        'role' => 'required|in_list[admin,siswa,guru,wali_kelas,guru_piket,wakil_kurikulum,wakil_kesiswaan]',
    ];
    protected $validationMessages   = [
        'user_id' => [
            'required' => 'User ID harus diisi',
            'is_natural_no_zero' => 'User ID harus berupa angka positif',
        ],
        'role' => [
            'required' => 'Role harus dipilih',
            'in_list' => 'Role hanya boleh admin, siswa, guru, wali_kelas, guru_piket, wakil_kurikulum, atau wakil_kesiswaan',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
    /**
     * Mendapatkan semua role untuk user tertentu
     */
    public function getRolesByUserId($userId)
    {
        return $this->where('user_id', $userId)->findAll();
    }
    
    /**
     * Mendapatkan role dalam bentuk array untuk user tertentu
     */
    public function getRoleNamesByUserId($userId)
    {
        $roles = $this->select('role')->where('user_id', $userId)->findAll();
        return array_column($roles, 'role');
    }
    
    /**
     * Menetapkan role untuk user (menghapus role lama dan menambahkan role baru)
     */
    public function setRolesForUser($userId, array $roles): bool
    {
        $this->db->transStart();

        // Hapus role lama
        $this->where('user_id', $userId)->delete();

        // Tambahkan role baru
        if (!empty($roles)) {
            foreach ($roles as $role) {
                $this->insert([
                    'user_id' => $userId,
                    'role'    => $role,
                ]);
            }
        }

        return $this->db->transComplete();
    }
    
    /**
     * Menambahkan role untuk user
     */
    public function addRoleToUser($userId, $role)
    {
        $existing = $this->where(['user_id' => $userId, 'role' => $role])->first();
        if (!$existing) {
            return $this->insert([
                'user_id' => $userId,
                'role' => $role,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        return true;
    }
    
    /**
     * Menghapus role dari user
     */
    public function removeRoleFromUser($userId, $role)
    {
        return $this->where(['user_id' => $userId, 'role' => $role])->delete();
    }
    
    /**
     * Mengecek apakah user memiliki role tertentu
     */
    public function userHasRole($userId, $role)
    {
        return $this->where(['user_id' => $userId, 'role' => $role])->countAllResults() > 0;
    }
    
    /**
     * Mendapatkan semua user dengan role tertentu
     */
    public function getUsersByRole($role)
    {
        log_message('debug', 'UserRole@getUsersByRole: Attempting to get users for role: ' . $role);
        $result = $this->select('users.*, user_roles.role')
            ->join('users', 'users.id = user_roles.user_id')
            ->where('user_roles.role', $role)
            ->findAll();
        log_message('debug', 'UserRole@getUsersByRole: Result for ' . $role . ': ' . json_encode($result));
        return $result;
    }
}