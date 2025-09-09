<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AllowNullTimesInIzinKeluar extends Migration
{
    public function up()
    {
        $fields = [
            'jam_keluar' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_kembali' => [
                'type' => 'TIME',
                'null' => true,
            ],
        ];

        $this->forge->modifyColumn('izin_keluar', $fields);
    }

    public function down()
    {
        // Revert the changes, if needed
        $fields = [
            'jam_keluar' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'jam_kembali' => [
                'type' => 'TIME',
                'null' => true, // Keep jam_kembali nullable as it might be empty even on revert
            ],
        ];

        $this->forge->modifyColumn('izin_keluar', $fields);
    }
}