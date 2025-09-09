<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameWakilKurikulumRole extends Migration
{
    public function up()
    {
        // 1. Update the role name in the user_roles table
        $this->db->table('user_roles')
                 ->where('role', 'wakil_kurikulum')
                 ->update(['role' => 'wakil_kesiswaan']);

        // 2. Update the status in the izin_keluar table
        $this->db->table('izin_keluar')
                 ->where('status', 'diproses_wakil_kurikulum')
                 ->update(['status' => 'diproses_wakil_kesiswaan']);

        // 3. Modify the ENUM definition in the izin_keluar table
        $this->forge->modifyColumn('izin_keluar', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['diajukan', 'diproses_guru_kelas', 'diproses_wali_kelas', 'diproses_wakil_kesiswaan', 'diproses_guru_piket', 'disetujui', 'ditolak'],
                'default'    => 'diajukan',
            ],
        ]);
    }

    public function down()
    {
        // Revert the changes
        // 1. Update the role name back
        $this->db->table('user_roles')
                 ->where('role', 'wakil_kesiswaan')
                 ->update(['role' => 'wakil_kurikulum']);

        // 2. Update the status back
        $this->db->table('izin_keluar')
                 ->where('status', 'diproses_wakil_kesiswaan')
                 ->update(['status' => 'diproses_wakil_kurikulum']);

        // 3. Modify the ENUM definition back
        $this->forge->modifyColumn('izin_keluar', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['diajukan', 'diproses_guru_kelas', 'diproses_wali_kelas', 'diproses_wakil_kurikulum', 'diproses_guru_piket', 'disetujui', 'ditolak'],
                'default'    => 'diajukan',
            ],
        ]);
    }
}
