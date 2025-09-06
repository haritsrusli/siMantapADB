<?= $this->extend('siswa/template') ?>

<?= $this->section('content') ?>

    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="bi bi-clock-history"></i> Riwayat Presensi
            </h2>
            <p class="lead">Berikut adalah riwayat presensi Anda.</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-table"></i> Data Presensi
                        </h5>
                        <span class="badge bg-primary"><?= count($riwayat) ?> Hari</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-start mb-3">
                        <a href="<?= base_url('siswa/dashboard') ?>" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Hari</th>
                                    <th>Tanggal</th>
                                    <th>Jam Presensi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $hariIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                foreach ($riwayat as $item): ?>
                                    <tr>
                                        <td><?= $hariIndonesia[date('w', strtotime($item['tanggal']))] ?></td>
                                        <td><?= date('d M Y', strtotime($item['tanggal'])) ?></td>
                                        <td>
                                            <?php if ($item['jam_presensi']): ?>
                                                <?= date('H:i:s', strtotime($item['jam_presensi'])) ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($item['is_libur_nasional']): ?>
                                                <span class="badge bg-info">Libur Nasional</span>
                                                <?php if ($item['keterangan_libur']): ?>
                                                    <small class="d-block"><?= $item['keterangan_libur'] ?></small>
                                                <?php endif; ?>
                                            <?php elseif ($item['is_weekend']): ?>
                                                <span class="badge bg-secondary">Libur Akhir Pekan</span>
                                            <?php elseif ($item['jam_presensi']): ?>
                                                <span class="badge bg-success">Hadir</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Tidak Hadir</span>
                                            <?php endif; ?>
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

<?= $this->endSection() ?>