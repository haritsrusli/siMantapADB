<?php

namespace App\Models;

use CodeIgniter\Model;

class Pengaturan extends Model
{
    protected $table            = 'pengaturan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'lokasi_latitude',
        'lokasi_longitude',
        'radius_meter',
        'lokasi_locked',
        'google_maps_api_key',
        'jam_masuk_senin',
        'jam_pulang_senin',
        'jam_masuk_selasa',
        'jam_pulang_selasa',
        'jam_masuk_rabu',
        'jam_pulang_rabu',
        'jam_masuk_kamis',
        'jam_pulang_kamis',
        'jam_masuk_jumat',
        'jam_pulang_jumat',
        'jam_masuk_sabtu',
        'jam_pulang_sabtu',
        'jam_masuk_minggu',
        'jam_pulang_minggu',
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
        'lokasi_latitude' => 'required|decimal|greater_than_equal_to[-90]|less_than_equal_to[90]',
        'lokasi_longitude' => 'required|decimal|greater_than_equal_to[-180]|less_than_equal_to[180]',
        'radius_meter' => 'required|integer|greater_than[0]',
    ];
    protected $validationMessages   = [
        'lokasi_latitude' => [
            'required' => 'Latitude harus diisi',
            'decimal' => 'Latitude harus berupa angka desimal',
            'greater_than_equal_to' => 'Latitude harus antara -90 dan 90',
            'less_than_equal_to' => 'Latitude harus antara -90 dan 90',
        ],
        'lokasi_longitude' => [
            'required' => 'Longitude harus diisi',
            'decimal' => 'Longitude harus berupa angka desimal',
            'greater_than_equal_to' => 'Longitude harus antara -180 dan 180',
            'less_than_equal_to' => 'Longitude harus antara -180 dan 180',
        ],
        'radius_meter' => [
            'required' => 'Radius harus diisi',
            'integer' => 'Radius harus berupa angka bulat',
            'greater_than' => 'Radius harus lebih besar dari 0',
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
