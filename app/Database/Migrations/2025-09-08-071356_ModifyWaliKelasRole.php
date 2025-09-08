<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyWaliKelasRole extends Migration
{
    public function up()
    {
        $fields = [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['guru_kelas', 'wakil_kurikulum', 'guru_piket'],
            ],
        ];
        $this->forge->modifyColumn('izin_keluar_penugasan', $fields);
    }

    public function down()
    {
        $fields = [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['guru_kelas', 'wali_kelas', 'wakil_kurikulum', 'guru_piket'],
            ],
        ];
        $this->forge->modifyColumn('izin_keluar_penugasan', $fields);
    }
}