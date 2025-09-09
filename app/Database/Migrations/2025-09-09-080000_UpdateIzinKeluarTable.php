<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateIzinKeluarTable extends Migration
{
    public function up()
    {
        // Add validation for jam_keluar and jam_kembali columns
        $this->forge->modifyColumn('izin_keluar', [
            'jam_keluar' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_kembali' => [
                'type' => 'TIME',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        // Revert changes if needed
        $this->forge->modifyColumn('izin_keluar', [
            'jam_keluar' => [
                'type' => 'TIME',
            ],
            'jam_kembali' => [
                'type' => 'TIME',
                'null' => true,
            ],
        ]);
    }
}