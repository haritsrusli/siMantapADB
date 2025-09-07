<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveDokumenPendukungFromAbsensiManual extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('absensi_manual', 'dokumen_pendukung');
    }

    public function down()
    {
        $this->forge->addColumn('absensi_manual', [
            'dokumen_pendukung' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);
    }
}
