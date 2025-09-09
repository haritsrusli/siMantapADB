<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWakilKesiswaanStatus extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('izin_keluar', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['diajukan', 'diproses_guru_kelas', 'diproses_wali_kelas', 'diproses_wakil_kurikulum', 'diproses_wakil_kesiswaan', 'diproses_guru_piket', 'disetujui', 'ditolak'],
                'default'    => 'diajukan',
            ],
        ]);
    }

    public function down()
    {
        // Revert the ENUM definition
        $this->forge->modifyColumn('izin_keluar', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['diajukan', 'diproses_guru_kelas', 'diproses_wali_kelas', 'diproses_wakil_kurikulum', 'diproses_guru_piket', 'disetujui', 'ditolak'],
                'default'    => 'diajukan',
            ],
        ]);
    }
}
