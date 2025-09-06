<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="bi bi-calendar-heart"></i> Manajemen Libur Nasional
            </h2>
            <p class="lead">Kelola data libur nasional dan cuti bersama.</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-table"></i> Data Libur Nasional
                        </h5>
                        <a href="<?= base_url('admin/libur-nasional/tambah') ?>" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Tambah Libur Nasional
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                        </a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Keterangan</th>
                                    <th>Jenis Libur</th>
                                    <th>Tahun</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($libur_nasional)): ?>
                                    <?php $no = 1; foreach($libur_nasional as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td>
                                                <i class="bi bi-calendar"></i> <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                            </td>
                                            <td><?= $row['keterangan'] ?></td>
                                            <td>
                                                <?php if($row['jenis_libur'] == 'nasional'): ?>
                                                    <span class="badge bg-danger">Nasional</span>
                                                <?php elseif($row['jenis_libur'] == 'daerah'): ?>
                                                    <span class="badge bg-warning text-dark">Daerah</span>
                                                <?php else: ?>
                                                    <span class="badge bg-info">Khusus</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $row['tahun'] ?></td>
                                            <td>
                                                <a href="<?= base_url('admin/libur-nasional/edit/' . $row['id']) ?>" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <a href="<?= base_url('admin/libur-nasional/hapus/' . $row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus libur nasional ini?')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Belum ada data libur nasional</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>