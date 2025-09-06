<?php

namespace App\Models;

use CodeIgniter\Model;

class LiburNasional extends Model
{
    protected $table            = 'libur_nasional';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tanggal',
        'keterangan',
        'jenis_libur',
        'tahun'
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
        'tanggal' => 'required|valid_date',
        'keterangan' => 'required|max_length[255]',
        'jenis_libur' => 'required|in_list[nasional,daerah,khusus]',
        'tahun' => 'required|integer'
    ];
    protected $validationMessages   = [
        'tanggal' => [
            'required' => 'Tanggal harus diisi',
            'valid_date' => 'Format tanggal tidak valid'
        ],
        'keterangan' => [
            'required' => 'Keterangan harus diisi',
            'max_length' => 'Keterangan maksimal 255 karakter'
        ],
        'jenis_libur' => [
            'required' => 'Jenis libur harus dipilih',
            'in_list' => 'Jenis libur hanya boleh nasional, daerah, atau khusus'
        ],
        'tahun' => [
            'required' => 'Tahun harus diisi',
            'integer' => 'Tahun harus berupa angka'
        ]
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
    
    // Mendapatkan libur nasional berdasarkan tanggal
    public function getLiburByTanggal($tanggal)
    {
        return $this->where('tanggal', $tanggal)->first();
    }
    
    // Mendapatkan semua libur nasional dalam rentang tanggal
    public function getLiburInRange($startDate, $endDate)
    {
        return $this->where('tanggal >=', $startDate)
                   ->where('tanggal <=', $endDate)
                   ->findAll();
    }
}