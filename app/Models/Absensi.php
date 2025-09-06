<?php

namespace App\Models;

use CodeIgniter\Model;

class Absensi extends Model
{
    protected $table            = 'absensi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'waktu_presensi',
        'latitude',
        'longitude',
        'foto_selfie',
        'terlambat'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'user_id' => 'required|integer',
        'waktu_presensi' => 'required|valid_date',
        'latitude' => 'required|decimal|greater_than_equal_to[-90]|less_than_equal_to[90]',
        'longitude' => 'required|decimal|greater_than_equal_to[-180]|less_than_equal_to[180]',
    ];
    protected $validationMessages   = [
        'user_id' => [
            'required' => 'User ID harus diisi',
            'integer' => 'User ID harus berupa angka bulat',
        ],
        'waktu_presensi' => [
            'required' => 'Waktu presensi harus diisi',
            'valid_date' => 'Format waktu presensi tidak valid',
        ],
        'latitude' => [
            'required' => 'Latitude harus diisi',
            'decimal' => 'Latitude harus berupa angka desimal',
            'greater_than_equal_to' => 'Latitude harus antara -90 dan 90',
            'less_than_equal_to' => 'Latitude harus antara -90 dan 90',
        ],
        'longitude' => [
            'required' => 'Longitude harus diisi',
            'decimal' => 'Longitude harus berupa angka desimal',
            'greater_than_equal_to' => 'Longitude harus antara -180 dan 180',
            'less_than_equal_to' => 'Longitude harus antara -180 dan 180',
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
}
