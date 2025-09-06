<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJamPresensiSabtuMingguToPengaturanTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pengaturan', [
            'jam_masuk_sabtu' => [
                'type' => 'TIME',
                'default' => '07:00:00',
            ],
            'jam_pulang_sabtu' => [
                'type' => 'TIME',
                'default' => '12:00:00',
            ],
            'jam_masuk_minggu' => [
                'type' => 'TIME',
                'default' => '00:00:00',
            ],
            'jam_pulang_minggu' => [
                'type' => 'TIME',
                'default' => '00:00:00',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pengaturan', ['jam_masuk_sabtu', 'jam_pulang_sabtu', 'jam_masuk_minggu', 'jam_pulang_minggu']);
    }
}