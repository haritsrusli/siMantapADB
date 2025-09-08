<?php
// Debug script sederhana untuk menguji logika penampilan jenis absensi
$data_sample = [
    [
        'status_kehadiran' => 'izin',
        'keterangan' => 'surat'
    ],
    [
        'status_kehadiran' => 'sakit',
        'keterangan' => 'demam'
    ],
    [
        'status_kehadiran' => '',
        'keterangan' => ''
    ]
];

echo "Menguji logika penampilan jenis absensi:\n";
echo str_repeat("-", 40) . "\n";

foreach ($data_sample as $index => $record) {
    echo "Data " . ($index + 1) . ":\n";
    echo "Status kehadiran: '" . $record['status_kehadiran'] . "'\n";
    
    if (!empty($record['status_kehadiran'])) {
        echo "Kondisi: !empty(status_kehadiran) = true\n";
        switch ($record['status_kehadiran']) {
            case 'sakit':
                echo "Hasil: <span class=\"badge badge-danger\">Sakit</span>\n";
                break;
            case 'izin':
                echo "Hasil: <span class=\"badge badge-warning\">Izin</span>\n";
                break;
            case 'alpa':
                echo "Hasil: <span class=\"badge badge-dark\">Alpa</span>\n";
                break;
            default:
                echo "Hasil: <span class=\"badge badge-success\">Hadir</span>\n";
                break;
        }
    } else {
        echo "Kondisi: !empty(status_kehadiran) = false\n";
        echo "Hasil: <span class=\"badge badge-success\">Hadir</span>\n";
    }
    echo str_repeat("-", 20) . "\n";
}
?>