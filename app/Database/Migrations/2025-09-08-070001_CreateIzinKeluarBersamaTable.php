<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIzinKeluarBersamaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'izin_keluar_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'siswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('izin_keluar_id', 'izin_keluar', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('siswa_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('izin_keluar_bersama');
    }

    public function down()
    {
        $this->forge->dropTable('izin_keluar_bersama');
    }
}