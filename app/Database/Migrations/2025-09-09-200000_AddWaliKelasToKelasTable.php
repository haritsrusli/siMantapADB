<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWaliKelasToKelasTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('kelas', [
            'wali_kelas_user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'jurusan', // Or any other suitable column
            ],
        ]);

        // Add foreign key constraint
        $this->forge->addForeignKey('wali_kelas_user_id', 'users', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        // Drop foreign key first
        $this->forge->dropForeignKey('kelas', 'kelas_wali_kelas_user_id_foreign');
        // Then drop the column
        $this->forge->dropColumn('kelas', 'wali_kelas_user_id');
    }
}
