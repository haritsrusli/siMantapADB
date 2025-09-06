<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKelasAndRoleToUsersTable extends Migration
{
    public function up()
    {
        // Menambahkan kolom id_kelas dan memperbarui constraint role
        $this->forge->addColumn('users', [
            'id_kelas' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'role',
            ],
        ]);
        
        // Memperbarui constraint role untuk menambahkan 'guru' dan 'wali_kelas'
        $this->db->query("ALTER TABLE `users` MODIFY `role` ENUM('admin', 'siswa', 'guru', 'wali_kelas')");
        
        // Menambahkan foreign key untuk id_kelas
        $this->forge->addForeignKey('id_kelas', 'kelas', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        // Menghapus foreign key
        $this->forge->dropForeignKey('users', 'users_id_kelas_foreign');
        
        // Menghapus kolom id_kelas
        $this->forge->dropColumn('users', 'id_kelas');
        
        // Mengembalikan constraint role ke nilai semula
        $this->db->query("ALTER TABLE `users` MODIFY `role` ENUM('admin', 'siswa')");
    }
}