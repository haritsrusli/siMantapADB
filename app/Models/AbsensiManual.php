<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiManual extends Model
{
    protected $table            = 'absensi_manual';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'tanggal',
        'jenis',
        'keterangan',
        'disetujui_oleh',
        'tanggal_disetujui',
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
        'user_id' => 'required|integer',
        'tanggal' => 'required|valid_date',
        'jenis' => 'required|in_list[izin,sakit,alpa]',
        'keterangan' => 'permit_empty|string|max_length[500]',
    ];
    protected $validationMessages   = [
        'user_id' => [
            'required' => 'User ID harus diisi',
            'integer' => 'User ID harus berupa angka bulat',
        ],
        'tanggal' => [
            'required' => 'Tanggal harus diisi',
            'valid_date' => 'Format tanggal tidak valid',
        ],
        'jenis' => [
            'required' => 'Jenis absensi harus dipilih',
            'in_list' => 'Jenis absensi hanya boleh izin, sakit, atau alpa',
        ],
    ];
    protected $skipValidation       = false; // Aktifkan kembali validasi
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
     * Mendapatkan data absensi manual dengan informasi siswa
     */
    public function getAbsensiManualWithSiswa($filters = [])
    {
        $builder = $this->select('absensi_manual.id, absensi_manual.user_id, absensi_manual.tanggal, absensi_manual.jenis as status_kehadiran, absensi_manual.keterangan, absensi_manual.created_at, absensi_manual.disetujui_oleh, absensi_manual.tanggal_disetujui, users.nama_lengkap as nama_siswa, users.username as nis, kelas.nama_kelas, kelas.tingkat, kelas.jurusan')
                         ->join('users', 'users.id = absensi_manual.user_id')
                         ->join('kelas', 'kelas.id = users.id_kelas', 'left');
                         
        // Apply filters if provided
        if (!empty($filters['tanggal'])) {
            $builder->where('absensi_manual.tanggal', $filters['tanggal']);
        }
        
        if (!empty($filters['jenis'])) {
            $builder->where('absensi_manual.jenis', $filters['jenis']);
        }
        
        if (!empty($filters['tingkat'])) {
            $builder->where('kelas.tingkat', $filters['tingkat']);
        }
        
        if (!empty($filters['jurusan'])) {
            $builder->where('kelas.jurusan', $filters['jurusan']);
        }
        
        if (!empty($filters['search'])) {
            $builder->groupStart()
                    ->like('users.nama_lengkap', $filters['search'])
                    ->orLike('users.username', $filters['search'])
                    ->groupEnd();
        }
        
        return $builder->orderBy('absensi_manual.tanggal', 'DESC')
                       ->orderBy('absensi_manual.created_at', 'DESC');
    }
    
    /**
     * Mendapatkan data absensi manual berdasarkan tanggal dan user
     */
    public function getAbsensiByTanggalAndUser($tanggal, $userId)
    {
        return $this->where('tanggal', $tanggal)
                    ->where('user_id', $userId)
                    ->first();
    }
}