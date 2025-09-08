<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIzinKeluarTable extends Migration
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
            'siswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jenis_izin' => [
                'type'       => 'ENUM',
                'constraint' => ['sakit', 'keluarga', 'lainnya'],
                'default'    => 'lainnya',
            ],
            'alasan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'jam_keluar' => [
                'type' => 'TIME',
            ],
            'jam_kembali' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['diajukan', 'diproses_guru_kelas', 'diproses_wali_kelas', 'diproses_wakil_kurikulum', 'diproses_guru_piket', 'disetujui', 'ditolak'],
                'default'    => 'diajukan',
            ],
            'guru_kelas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'wali_kelas_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'wakil_kurikulum_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'guru_piket_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'penolak_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'catatan_penolakan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('siswa_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('guru_kelas_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('wali_kelas_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('wakil_kurikulum_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('guru_piket_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('penolak_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('izin_keluar');
    }

    public function down()
    {
        $this->forge->dropTable('izin_keluar');
    }
}