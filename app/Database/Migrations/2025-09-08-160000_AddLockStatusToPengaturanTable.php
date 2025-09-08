<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLockStatusToPengaturanTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pengaturan', [
            'lokasi_locked' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 0,
                'after' => 'radius_meter',
                'comment' => '0 = unlocked, 1 = locked'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pengaturan', 'lokasi_locked');
    }
}