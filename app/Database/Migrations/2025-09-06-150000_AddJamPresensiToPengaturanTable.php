<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJamPresensiToPengaturanTable extends Migration
{
    public function up()
    {
        // Add columns for presensi hours
        $this->forge->addColumn('pengaturan', [
            'jam_masuk_senin' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_pulang_senin' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_masuk_selasa' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_pulang_selasa' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_masuk_rabu' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_pulang_rabu' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_masuk_kamis' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_pulang_kamis' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_masuk_jumat' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'jam_pulang_jumat' => [
                'type' => 'TIME',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        // Remove columns
        $this->forge->dropColumn('pengaturan', [
            'jam_masuk_senin',
            'jam_pulang_senin',
            'jam_masuk_selasa',
            'jam_pulang_selasa',
            'jam_masuk_rabu',
            'jam_pulang_rabu',
            'jam_masuk_kamis',
            'jam_pulang_kamis',
            'jam_masuk_jumat',
            'jam_pulang_jumat',
        ]);
    }
}