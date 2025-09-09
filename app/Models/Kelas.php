<?php

namespace App\Models;

use CodeIgniter\Model;

class Kelas extends Model
{
    protected $table            = 'kelas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_kelas',
        'tingkat',
        'jurusan',
        'tahun_ajaran',
        'wali_kelas_user_id',
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
        'nama_kelas' => 'required|max_length[50]',
        'tingkat' => 'required|in_list[X,XI,XII]',
        'jurusan' => 'required|max_length[100]',
        'tahun_ajaran' => 'permit_empty|max_length[9]', // Format: 2024/2025
        'wali_kelas_user_id' => 'permit_empty|integer',
    ];
    protected $validationMessages   = [
        'nama_kelas' => [
            'required' => 'Nama kelas harus diisi',
            'max_length' => 'Nama kelas maksimal 50 karakter',
        ],
        'tingkat' => [
            'required' => 'Tingkat harus dipilih',
            'in_list' => 'Tingkat hanya boleh X, XI, atau XII',
        ],
        'jurusan' => [
            'required' => 'Jurusan harus diisi',
            'max_length' => 'Jurusan maksimal 100 karakter',
        ],
        'tahun_ajaran' => [
            'max_length' => 'Tahun ajaran maksimal 9 karakter (format: 2024/2025)',
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
    
    // Mendapatkan semua kelas dengan jumlah siswa
    public function getAllKelasWithJumlahSiswa()
    {
        return $this->select('kelas.*, COUNT(users.id) as jumlah_siswa')
            ->join('users', 'users.id_kelas = kelas.id', 'left')
            ->where('users.role', 'siswa')
            ->groupBy('kelas.id')
            ->orderBy('kelas.tingkat', 'ASC')
            ->orderBy('kelas.jurusan', 'ASC')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->findAll();
    }
    
    // Mendapatkan kelas berdasarkan tingkat
    public function getKelasByTingkat($tingkat)
    {
        return $this->where('tingkat', $tingkat)
            ->orderBy('jurusan', 'ASC')
            ->orderBy('nama_kelas', 'ASC')
            ->findAll();
    }
    
    // Mendapatkan kelas dengan walikelas
    public function getKelasWithWalikelas()
    {
        return $this->select('kelas.*, 
                             walikelas.nama_lengkap as nama_walikelas, 
                             walikelas.username as nip_walikelas')
            ->join('users walikelas', 'walikelas.id_kelas = kelas.id AND walikelas.role = "wali_kelas"', 'left')
            ->orderBy('kelas.tingkat', 'ASC')
            ->orderBy('kelas.jurusan', 'ASC')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->findAll();
    }
    
    // Mendapatkan kelas dengan walikelas berdasarkan ID kelas
    public function getKelasWithWalikelasById($id)
    {
        return $this->select('kelas.*, 
                             walikelas.nama_lengkap as nama_walikelas, 
                             walikelas.username as nip_walikelas')
            ->join('users walikelas', 'walikelas.id_kelas = kelas.id AND walikelas.role = "wali_kelas"', 'left')
            ->where('kelas.id', $id)
            ->first();
    }

    // Mendapatkan kelas dengan walikelas dengan paginasi
    public function getKelasWithWalikelasPaginated($perPage = 10)
    {
        return $this->select('kelas.*, 
                             walikelas.nama_lengkap as nama_walikelas, 
                             walikelas.username as nip_walikelas')
            ->join('users walikelas', 'walikelas.id_kelas = kelas.id AND walikelas.role = "wali_kelas"', 'left')
            ->orderBy('kelas.tingkat', 'ASC')
            ->orderBy('kelas.jurusan', 'ASC')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->paginate($perPage);
    }
}