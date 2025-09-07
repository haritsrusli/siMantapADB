<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAbsensiManualTable extends Migration
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
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'jenis' => [
                'type' => 'ENUM',
                'constraint' => ['izin', 'sakit'],
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'dokumen_pendukung' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'disetujui_oleh' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'tanggal_disetujui' => [
                'type' => 'DATETIME',
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
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('disetujui_oleh', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('absensi_manual');
    }

    public function down()
    {
        $this->forge->dropTable('absensi_manual');
    }
}