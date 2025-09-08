<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIzinKeluarPenugasanTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['guru_kelas', 'wali_kelas', 'wakil_kurikulum', 'guru_piket'],
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('izin_keluar_penugasan');
    }

    public function down()
    {
        $this->forge->dropTable('izin_keluar_penugasan');
    }
}