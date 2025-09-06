<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKelasTable extends Migration
{
    public function up()
    {
        // Membuat tabel kelas
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_kelas' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'tingkat' => [
                'type' => 'ENUM',
                'constraint' => ['X', 'XI', 'XII'],
            ],
            'jurusan' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'tahun_ajaran' => [
                'type' => 'VARCHAR',
                'constraint' => '9', // Format: 2024/2025
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('kelas');
    }

    public function down()
    {
        $this->forge->dropTable('kelas');
    }
}