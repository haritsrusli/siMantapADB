<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGoogleMapsApiKeyToPengaturanTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pengaturan', [
            'google_maps_api_key' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'default' => null,
                'after' => 'radius_meter'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pengaturan', 'google_maps_api_key');
    }
}