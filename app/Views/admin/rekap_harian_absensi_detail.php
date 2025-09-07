<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">
            <i class="bi bi-file-earmark-bar-graph"></i> Detail Rekap Presensi harian
        </h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('laporan') ?>">Rekap Presensi harian</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-table"></i> Detail Rekap Presensi harian
                </h5>
                <div>
                    <small class="text-muted">
                        Periode: <?= date('d M Y', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?>
                    </small>
                </div>
                <a href="<?= base_url('laporan') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <?php if (isset($rekap) && !empty($rekap)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Hadir</th>
                                <th>Izin</th>
                                <th>Sakit</th>
                                <th>Alpa</th>
                                <th>Persentase Kehadiran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($rekap as $r): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $r['nis'] ?></td>
                                <td><?= $r['nama'] ?></td>
                                <td><?= $r['hadir'] ?></td>
                                <td><?= $r['izin'] ?></td>
                                <td><?= $r['sakit'] ?></td>
                                <td><?= $r['alpa'] ?></td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar <?= $r['persentase'] >= 75 ? 'bg-success' : ($r['persentase'] >= 50 ? 'bg-warning' : 'bg-danger') ?>" 
                                             role="progressbar" 
                                             style="width: <?= $r['persentase'] ?>%" 
                                             aria-valuenow="<?= $r['persentase'] ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            <?= $r['persentase'] ?>%
                                        </div>
                                    </div>
                                    <small class="text-muted">(<?= $r['hadir'] ?> dari <?= $r['total_hari_kerja'] ?> hari kerja)</small>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="showDetail('<?= addslashes($r['nama']) ?>', '<?= base64_encode(json_encode($r['detail_kehadiran'])) ?>')">
                                        <i class="bi bi-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <button class="btn btn-success" onclick="window.print()">
                        <i class="bi bi-printer"></i> Cetak Laporan
                    </button>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Tidak ada data rekap presensi harian untuk kelas yang dipilih.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal for detail kehadiran -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Kehadiran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 id="siswaName"></h6>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function showDetail(siswaName, encodedDetailKehadiran) {
        // Decode base64 data
        var decodedData = atob(encodedDetailKehadiran);
        var detailKehadiran = JSON.parse(decodedData);
        
        $('#siswaName').text('Siswa: ' + siswaName);
        var tableBody = $('#detailTableBody');
        tableBody.empty();
        
        // Convert object to array and sort by date
        var dates = Object.keys(detailKehadiran).sort();
        
        dates.forEach(function(date) {
            var status = detailKehadiran[date];
            var statusClass = '';
            var statusIcon = '';
            
            switch(status.toLowerCase()) {
                case 'hadir':
                    statusClass = 'text-success';
                    statusIcon = '✔';
                    break;
                case 'izin':
                    statusClass = 'text-warning';
                    statusIcon = 'Ⓘ';
                    break;
                case 'sakit':
                    statusClass = 'text-danger';
                    statusIcon = 'Ⓢ';
                    break;
                case 'alpa':
                case 'tak hadir':
                    statusClass = 'text-dark';
                    statusIcon = 'Ⓐ';
                    break;
                default:
                    statusClass = 'text-secondary';
                    statusIcon = '?';
            }
            
            var row = '<tr>' +
                '<td>' + formatDate(date) + '</td>' +
                '<td class="' + statusClass + '">' + statusIcon + ' ' + status + '</td>' +
                '</tr>';
            tableBody.append(row);
        });
        
        // Show the modal using Bootstrap 5 method
        var modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
    }
    
    function formatDate(dateString) {
        var date = new Date(dateString);
        var options = { day: '2-digit', month: 'short', year: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }
</script>
<?= $this->endSection() ?>