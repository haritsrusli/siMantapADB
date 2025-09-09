<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUserRolesEnum extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('user_roles', [
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['admin', 'siswa', 'guru', 'wali_kelas', 'guru_piket', 'wakil_kurikulum', 'wakil_kesiswaan', 'ketua_kelas', 'sekretaris'],
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('user_roles', [
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['admin', 'siswa', 'guru', 'wali_kelas', 'guru_piket'],
            ],
        ]);
    }
}
