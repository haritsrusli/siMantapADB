<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveTipePresensiColumn extends Migration
{
    public function up()
    {
        // Hapus kolom tipe_presensi karena sistem presensi sekarang hanya menggunakan sekali presensi per hari
        $this->forge->dropColumn('absensi', 'tipe_presensi');
    }

    public function down()
    {
        // Tambahkan kembali kolom tipe_presensi jika perlu rollback
        $this->forge->addColumn('absensi', [
            'tipe_presensi' => [
                'type' => 'ENUM',
                'constraint' => ['masuk', 'pulang'],
                'after' => 'waktu_presensi'
            ],
        ]);
    }
}