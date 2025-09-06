<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserRolesTable extends Migration
{
    public function up()
    {
        // Membuat tabel user_roles untuk menyimpan multiple roles per user
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['admin', 'siswa', 'guru', 'wali_kelas', 'guru_piket'],
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_roles');
        
        // Menambahkan role 'guru_piket' ke constraint role di tabel users (untuk backward compatibility)
        $this->db->query("ALTER TABLE `users` MODIFY `role` ENUM('admin', 'siswa', 'guru', 'wali_kelas', 'guru_piket')");
    }

    public function down()
    {
        $this->forge->dropTable('user_roles');
    }
}