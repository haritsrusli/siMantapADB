<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InsertDefaultPengaturan extends Migration
{
    public function up()
    {
        // Cek apakah sudah ada data pengaturan
        $count = $this->db->table('pengaturan')->countAllResults();
        
        // Jika belum ada data, masukkan data default
        if ($count == 0) {
            $data = [
                'lokasi_latitude' => '-6.2088',
                'lokasi_longitude' => '106.8456',
                'radius_meter' => 50,
                'jam_masuk_senin' => '07:00:00',
                'jam_pulang_senin' => '15:00:00',
                'jam_masuk_selasa' => '07:00:00',
                'jam_pulang_selasa' => '15:00:00',
                'jam_masuk_rabu' => '07:00:00',
                'jam_pulang_rabu' => '15:00:00',
                'jam_masuk_kamis' => '07:00:00',
                'jam_pulang_kamis' => '15:00:00',
                'jam_masuk_jumat' => '07:00:00',
                'jam_pulang_jumat' => '15:00:00',
            ];
            
            $this->db->table('pengaturan')->insert($data);
        }
    }

    public function down()
    {
        // Hapus data default jika perlu
        // Kita tidak menghapus data pengaturan yang sudah ada
    }
}