<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAbsensiTable extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('absensi', [
            'foto_selfie' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('absensi', [
            'foto_selfie' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
        ]);
    }
}
