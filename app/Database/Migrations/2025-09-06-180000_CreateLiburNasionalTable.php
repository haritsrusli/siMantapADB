<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLiburNasionalTable extends Migration
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
            'tanggal' => [
                'type' => 'DATE',
            ],
            'keterangan' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'jenis_libur' => [
                'type' => 'ENUM',
                'constraint' => ['nasional', 'daerah', 'khusus'],
                'default' => 'nasional',
            ],
            'tahun' => [
                'type' => 'YEAR',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('tanggal');
        $this->forge->addKey('tahun');
        $this->forge->createTable('libur_nasional');
    }

    public function down()
    {
        $this->forge->dropTable('libur_nasional');
    }
}