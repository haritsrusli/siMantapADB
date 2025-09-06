<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePengaturanTable extends Migration
{
    public function up()
    {
        // Modify columns to ensure correct data types
        $this->forge->modifyColumn('pengaturan', [
            'lokasi_latitude' => [
                'type' => 'DECIMAL',
                'constraint' => '10,8',
                'null' => false,
            ],
            'lokasi_longitude' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
                'null' => false,
            ],
            'radius_meter' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
        ]);
    }

    public function down()
    {
        // Revert changes if needed
    }
}
