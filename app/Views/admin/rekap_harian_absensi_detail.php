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
                                <th>Tidak Hadir</th>
                                <th>Total Hari Kerja</th>
                                <th>Persentase</th>
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

<?= $this->endSection() ?>