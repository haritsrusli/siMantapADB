<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">
            <i class="bi bi-file-earmark-bar-graph"></i> Rekap Kehadiran
        </h2>
        <p class="lead">Rekapitulasi kehadiran siswa berdasarkan kelas dan periode waktu.</p>
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
                <form action="<?= base_url('laporan/rekap-kehadiran') ?>" method="GET">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="bulan" class="form-label">Bulan</label>
                            <select class="form-select" id="bulan" name="bulan" required>
                                <option value="">Pilih Bulan</option>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= sprintf('%02d', $i) ?>" <?= (isset($bulan) && $bulan == sprintf('%02d', $i)) ? 'selected' : '' ?>>
                                        <?= date('F', mktime(0, 0, 0, $i, 10)) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="tahun" class="form-label">Tahun</label>
                            <select class="form-select" id="tahun" name="tahun" required>
                                <option value="">Pilih Tahun</option>
                                <?php 
                                $currentYear = date('Y');
                                for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++): ?>
                                    <option value="<?= $i ?>" <?= (isset($tahun) && $tahun == $i) ? 'selected' : '' ?>>
                                        <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
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
                                <a href="<?= base_url('laporan/rekap-kehadiran-detail?bulan=' . $bulan . '&tahun=' . $tahun . '&id_kelas=' . $id_kelas) ?>" 
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
                    <i class="bi bi-table"></i> Data Rekap Kehadiran
                </h5>
                <div>
                    <small class="text-muted">
                        Periode: <?= date('F Y', strtotime($tahun . '-' . $bulan . '-01')) ?>
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
                                    <a href="<?= base_url('laporan/detail-siswa/' . $r['user_id'] . '?bulan=' . $bulan . '&tahun=' . $tahun) ?>" 
                                       class="btn btn-sm btn-outline-primary">
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
            <i class="bi bi-info-circle"></i> Tidak ada data rekap kehadiran untuk kelas yang dipilih.
        </div>
    </div>
</div>
<?php endif; ?>

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
</script>
<?= $this->endSection() ?>