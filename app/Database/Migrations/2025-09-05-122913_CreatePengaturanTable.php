<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengaturanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'lokasi_latitude' => [
                'type' => 'DECIMAL',
                'constraint' => '10,8',
            ],
            'lokasi_longitude' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
            ],
            'radius_meter' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('pengaturan');
    }

    public function down()
    {
        $this->forge->dropTable('pengaturan');
    }
}
