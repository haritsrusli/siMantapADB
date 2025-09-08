<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model Pengaturan
 * 
 * Digunakan untuk mengelola pengaturan sistem seperti lokasi, radius, dan jam presensi.
 * 
 * Perubahan penting:
 * - Mengganti aturan validasi 'valid_time' dengan 'regex_match[/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/]'
 *   karena 'valid_time' bukan aturan validasi bawaan CodeIgniter 4.
 *   Format waktu yang digunakan adalah HH:MM (24-jam format).
 */
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
        'lokasi_locked' => 'permit_empty|integer|in_list[0,1]',
        // Jam presensi rules - validasi dilakukan secara manual di controller
        'jam_masuk_senin' => 'permit_empty',
        'jam_pulang_senin' => 'permit_empty',
        'jam_masuk_selasa' => 'permit_empty',
        'jam_pulang_selasa' => 'permit_empty',
        'jam_masuk_rabu' => 'permit_empty',
        'jam_pulang_rabu' => 'permit_empty',
        'jam_masuk_kamis' => 'permit_empty',
        'jam_pulang_kamis' => 'permit_empty',
        'jam_masuk_jumat' => 'permit_empty',
        'jam_pulang_jumat' => 'permit_empty',
        'jam_masuk_sabtu' => 'permit_empty',
        'jam_pulang_sabtu' => 'permit_empty',
        'jam_masuk_minggu' => 'permit_empty',
        'jam_pulang_minggu' => 'permit_empty',
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
        'lokasi_locked' => [
            'integer' => 'Status kunci lokasi harus berupa angka bulat',
            'in_list' => 'Status kunci lokasi harus 0 atau 1',
        ],
        // Jam presensi messages
        'jam_masuk_senin' => [
            'regex_match' => 'Format jam masuk Senin tidak valid (HH:MM)',
        ],
        'jam_pulang_senin' => [
            'regex_match' => 'Format jam pulang Senin tidak valid (HH:MM)',
        ],
        'jam_masuk_selasa' => [
            'regex_match' => 'Format jam masuk Selasa tidak valid (HH:MM)',
        ],
        'jam_pulang_selasa' => [
            'regex_match' => 'Format jam pulang Selasa tidak valid (HH:MM)',
        ],
        'jam_masuk_rabu' => [
            'regex_match' => 'Format jam masuk Rabu tidak valid (HH:MM)',
        ],
        'jam_pulang_rabu' => [
            'regex_match' => 'Format jam pulang Rabu tidak valid (HH:MM)',
        ],
        'jam_masuk_kamis' => [
            'regex_match' => 'Format jam masuk Kamis tidak valid (HH:MM)',
        ],
        'jam_pulang_kamis' => [
            'regex_match' => 'Format jam pulang Kamis tidak valid (HH:MM)',
        ],
        'jam_masuk_jumat' => [
            'regex_match' => 'Format jam masuk Jumat tidak valid (HH:MM)',
        ],
        'jam_pulang_jumat' => [
            'regex_match' => 'Format jam pulang Jumat tidak valid (HH:MM)',
        ],
        'jam_masuk_sabtu' => [
            'regex_match' => 'Format jam masuk Sabtu tidak valid (HH:MM)',
        ],
        'jam_pulang_sabtu' => [
            'regex_match' => 'Format jam pulang Sabtu tidak valid (HH:MM)',
        ],
        'jam_masuk_minggu' => [
            'regex_match' => 'Format jam masuk Minggu tidak valid (HH:MM)',
        ],
        'jam_pulang_minggu' => [
            'regex_match' => 'Format jam pulang Minggu tidak valid (HH:MM)',
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
