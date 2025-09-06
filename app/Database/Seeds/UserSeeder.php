<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Insert admin user
        $userData = [
            [
                'username' => 'admin',
                'password' => 'admin123',
                'nama_lengkap' => 'Administrator',
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => '12345',
                'password' => '12345',
                'nama_lengkap' => 'Budi Santoso',
                'role' => 'siswa',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Using Query Builder
        $this->db->table('users')->insertBatch($userData);
        
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
