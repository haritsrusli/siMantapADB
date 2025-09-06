<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run()
    {
        // Insert default location settings
        $settingData = [
            'lokasi_latitude' => '-6.2088',
            'lokasi_longitude' => '106.8456',
            'radius_meter' => 50
        ];
        
        // Check if setting already exists
        $existing = $this->db->table('pengaturan')->countAllResults();
        if ($existing == 0) {
            $this->db->table('pengaturan')->insert($settingData);
        }
    }
}
