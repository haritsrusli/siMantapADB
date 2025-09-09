<?php

namespace App\Models;

use CodeIgniter\Model;

class IzinKeluarPenugasan extends Model
{
    protected $table            = 'izin_keluar_penugasan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'role',
    ];

    // Dates
    protected $useTimestamps = false;

    // Validation
    protected $validationRules      = [
        'user_id' => 'required|integer',
        'role'    => 'required|in_list[guru_kelas,wakil_kurikulum,guru_piket]',
    ];
    protected $validationMessages   = [];
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
}