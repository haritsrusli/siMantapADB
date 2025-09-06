<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LiburNasionalSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Libur Nasional Tahun 2025
            [
                'tanggal' => '2025-01-01',
                'keterangan' => 'Tahun Baru',
                'jenis_libur' => 'nasional',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-02-09',
                'keterangan' => 'Tahun Baru Imlek',
                'jenis_libur' => 'nasional',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-03-03',
                'keterangan' => 'Hari Raya Nyepi',
                'jenis_libur' => 'nasional',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-04-18',
                'keterangan' => 'Wafat Isa Almasih',
                'jenis_libur' => 'nasional',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-05-01',
                'keterangan' => 'Hari Buruh Internasional',
                'jenis_libur' => 'nasional',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-05-29',
                'keterangan' => 'Kenaikan Isa Almasih',
                'jenis_libur' => 'nasional',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-06-01',
                'keterangan' => 'Hari Lahir Pancasila',
                'jenis_libur' => 'nasional',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-06-04',
                'keterangan' => 'Hari Raya Waisak',
                'jenis_libur' => 'nasional',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-06-17',
                'keterangan' => 'Idul Fitri',
                'jenis_libur' => 'nasional',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-06-18',
                'keterangan' => 'Idul Fitri',
                'jenis_libur' => 'nasional',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-08-17',
                'keterangan' => 'Hari Kemerdekaan RI',
                'jenis_libur' => 'nasional',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-08-29',
                'keterangan' => 'Idul Adha',
                'jenis_libur' => 'nasional',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-09-07',
                'keterangan' => 'Tahun Baru Islam',
                'jenis_libur' => 'nasional',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-11-03',
                'keterangan' => 'Maulid Nabi Muhammad SAW',
                'jenis_libur' => 'nasional',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-11-20',
                'keterangan' => 'Hari Guru Nasional',
                'jenis_libur' => 'nasional',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-12-25',
                'keterangan' => 'Hari Natal',
                'jenis_libur' => 'nasional',
                'tahun' => '2025'
            ],
            
            // Libur Bersama Cuti Bersama Tahun 2025
            [
                'tanggal' => '2025-04-17',
                'keterangan' => 'Cuti Bersama Wafat Isa Almasih',
                'jenis_libur' => 'khusus',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-06-16',
                'keterangan' => 'Cuti Bersama Idul Fitri',
                'jenis_libur' => 'khusus',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-06-19',
                'keterangan' => 'Cuti Bersama Idul Fitri',
                'jenis_libur' => 'khusus',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-06-20',
                'keterangan' => 'Cuti Bersama Idul Fitri',
                'jenis_libur' => 'khusus',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-08-18',
                'keterangan' => 'Cuti Bersama HUT RI',
                'jenis_libur' => 'khusus',
                'tahun' => '2025'
            ],
            [
                'tanggal' => '2025-12-26',
                'keterangan' => 'Cuti Bersama Hari Natal',
                'jenis_libur' => 'khusus',
                'tahun' => '2025'
            ],
        ];

        // Simple Queries
        foreach ($data as $libur) {
            $this->db->table('libur_nasional')->insert($libur);
        }
    }
}