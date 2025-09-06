<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKelasRelationToUsersTable extends Migration
{
    public function up()
    {
        // Menambahkan kolom id_kelas di tabel users untuk relasi dengan walikelas
        $fields = [
            'id_kelas' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'role',
            ],
        ];
        
        $this->forge->addColumn('users', $fields);
        
        // Menambahkan foreign key untuk id_kelas
        $this->forge->addForeignKey('id_kelas', 'kelas', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        // Menghapus foreign key
        $this->forge->dropForeignKey('users', 'users_id_kelas_foreign');
        
        // Menghapus kolom id_kelas
        $this->forge->dropColumn('users', 'id_kelas');
    }
}