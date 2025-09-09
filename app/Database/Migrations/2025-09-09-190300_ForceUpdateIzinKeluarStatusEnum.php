<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ForceUpdateIzinKeluarStatusEnum extends Migration
{
    public function up()
    {
        $sql = "ALTER TABLE `izin_keluar` MODIFY `status` ENUM('diajukan','diproses_guru_kelas','diproses_wali_kelas','diproses_wakil_kurikulum','diproses_wakil_kesiswaan','diproses_guru_piket','disetujui','ditolak') NOT NULL DEFAULT 'diajukan'";
        $this->db->query($sql);
    }

    public function down()
    {
        // Revert to the state without 'diproses_wakil_kesiswaan'
        $sql = "ALTER TABLE `izin_keluar` MODIFY `status` ENUM('diajukan','diproses_guru_kelas','diproses_wali_kelas','diproses_wakil_kurikulum','diproses_guru_piket','disetujui','ditolak') NOT NULL DEFAULT 'diajukan'";
        $this->db->query($sql);
    }
}
