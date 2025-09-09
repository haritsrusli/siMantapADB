<?php

namespace App\Models;

use CodeIgniter\Model;

class IzinKeluar extends Model
{
    protected $table            = 'izin_keluar';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'siswa_id',
        'jenis_izin',
        'alasan',
        'jam_keluar',
        'jam_kembali',
        'status',
        'guru_kelas_id',
        'wali_kelas_id',
        'wakil_kurikulum_id',
        'guru_piket_id',
        'penolak_id',
        'catatan_penolakan',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'siswa_id'          => 'permit_empty|integer',
        'jenis_izin'        => 'permit_empty|in_list[sakit,keluarga,lainnya]',
        'alasan'            => 'permit_empty|min_length[5]',
        'jam_keluar'        => 'permit_empty|regex_match[/^[0-2][0-9]:[0-5][0-9]$/]',
        'jam_kembali'       => 'permit_empty|regex_match[/^[0-2][0-9]:[0-5][0-9]$/]',
        'status'            => 'permit_empty|in_list[diajukan,diproses_guru_kelas,diproses_wali_kelas,diproses_wakil_kurikulum,diproses_wakil_kesiswaan,diproses_guru_piket,disetujui,ditolak]',
        'guru_kelas_id'     => 'permit_empty|integer',
        'wali_kelas_id'     => 'permit_empty|integer',
        'wakil_kurikulum_id' => 'permit_empty|integer',
        'guru_piket_id'     => 'permit_empty|integer',
        'penolak_id'        => 'permit_empty|integer',
        'catatan_penolakan' => 'permit_empty',
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