<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">
            <i class="bi bi-file-earmark-bar-graph"></i> Rekap Presensi Harian
        </h2>
        <p class="lead">Rekapitulasi presensi harian siswa berdasarkan kelas dan rentang tanggal.</p>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-funnel"></i> Filter Data
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                    </a>
                </div>
                <form action="<?= base_url('laporan/rekap-harian') ?>" method="GET">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="<?= isset($start_date) ? $start_date : date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="<?= isset($end_date) ? $end_date : date('Y-m-d') ?>" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="id_kelas" class="form-label">Kelas</label>
                            <select class="form-select" id="id_kelas" name="id_kelas" required>
                                <option value="">Pilih Kelas</option>
                                <?php foreach ($kelas as $k): ?>
                                    <option value="<?= $k['id'] ?>" <?= (isset($id_kelas) && $id_kelas == $k['id']) ? 'selected' : '' ?>>
                                        <?= $k['nama_kelas'] ?> (<?= $k['tingkat'] ?> - <?= $k['jurusan'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Tampilkan Rekap
                            </button>
                            <?php if (isset($id_kelas)): ?>
                                <a href="<?= base_url('laporan/rekap-harian-detail?start_date=' . $start_date . '&end_date=' . $end_date . '&id_kelas=' . $id_kelas) ?>" 
                                   class="btn btn-success" target="_blank">
                                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (isset($rekap) && !empty($rekap)): ?>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-table"></i> Data Rekap Presensi harian
                </h5>
                <div>
                    <small class="text-muted">
                        Periode: <?= date('d M Y', strtotime($start_date)) ?> - <?= date('d M Y', strtotime($end_date)) ?>
                    </small>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="dataTable">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Hadir</th>
                                <th>Tidak Hadir</th>
                                <th>Total Hari Kerja</th>
                                <th>Persentase</th>
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
                                <td><?= $r['tidak_hadir'] ?></td>
                                <td><?= $r['total_hari_kerja'] ?></td>
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
                                </td>
                                <td>
                                    <a href="javascript:void(0)" 
                                       class="btn btn-sm btn-outline-primary"
                                       onclick="showDetail('<?= $r['nama'] ?>', <?= htmlspecialchars(json_encode($r['detail_kehadiran'])) ?>)">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php elseif (isset($rekap)): ?>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Tidak ada data rekap presensi harian untuk kelas yang dipilih.
        </div>
    </div>
</div>
<?php endif; ?>

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
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            },
            "order": [[ 0, "asc" ]],
            "pageLength": 25
        });
    });
    
    function showDetail(siswaName, detailKehadiran) {
        $('#siswaName').text('Siswa: ' + siswaName);
        var tableBody = $('#detailTableBody');
        tableBody.empty();
        
        // Convert object to array and sort by date
        var dates = Object.keys(detailKehadiran).sort();
        
        dates.forEach(function(date) {
            var status = detailKehadiran[date];
            var statusClass = status === 'Hadir' ? 'text-success' : 'text-danger';
            var statusIcon = status === 'Hadir' ? '✔' : '✘';
            
            var row = '<tr>' +
                '<td>' + formatDate(date) + '</td>' +
                '<td class="' + statusClass + '">' + statusIcon + ' ' + status + '</td>' +
                '</tr>';
            tableBody.append(row);
        });
        
        $('#detailModal').modal('show');
    }
    
    function formatDate(dateString) {
        var date = new Date(dateString);
        var options = { day: '2-digit', month: 'short', year: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }
</script>
<?= $this->endSection() ?>