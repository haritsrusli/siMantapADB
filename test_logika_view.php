<?php
// Test script untuk meniru logika view
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
    ],
    [
        'status_kehadiran' => null,
        'keterangan' => ''
    ]
];

echo "Menguji logika penampilan jenis absensi seperti di view:\n";
echo str_repeat("=", 50) . "\n";

foreach ($data_sample as $index => $record) {
    echo "Data " . ($index + 1) . ":\n";
    echo "status_kehadiran: ";
    var_dump($record['status_kehadiran']);
    
    $jenis = $record['status_kehadiran'] ?? '';
    echo "jenis (setelah ?? ''): ";
    var_dump($jenis);
    
    echo "empty(jenis): ";
    var_dump(empty($jenis));
    
    if (!empty($jenis)) {
        echo "Masuk ke switch dengan nilai: '$jenis'\n";
        switch ($jenis) {
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
        echo "Tidak masuk ke switch, menampilkan Hadir default\n";
        echo "Hasil: <span class=\"badge badge-success\">Hadir</span>\n";
    }
    echo str_repeat("-", 30) . "\n";
}
?>
