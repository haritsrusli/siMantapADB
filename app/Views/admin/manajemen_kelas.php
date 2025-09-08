<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="bi bi-book"></i> Manajemen Kelas
            </h2>
            <p class="lead">Kelola data kelas dan wali kelas.</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-table"></i> Data Kelas
                        </h5>
                        <a href="<?= base_url('admin/kelas/tambah') ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus"></i> Tambah Kelas
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
                                    <th>Nama Kelas</th>
                                    <th>Tingkat</th>
                                    <th>Jurusan</th>
                                    <th>Tahun Ajaran</th>
                                    <th>Wali Kelas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($kelas)):
                                    $no = ($pager->getCurrentPage() - 1) * $pager->getPerPage() + 1; foreach($kelas as $row):
                                 ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <i class="bi bi-book"></i> <?= $row['nama_kelas'] ?>
                                        </td>
                                        <td>
                                            <?php if($row['tingkat'] == 'X'): ?>
                                                <span class="badge bg-primary">X (Sepuluh)</span>
                                            <?php elseif($row['tingkat'] == 'XI'): ?>
                                                <span class="badge bg-success">XI (Sebelas)</span>
                                            <?php elseif($row['tingkat'] == 'XII'): ?>
                                                <span class="badge bg-warning">XII (Dua Belas)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <i class="bi bi-mortarboard"></i> <?= $row['jurusan'] ?>
                                        </td>
                                        <td>
                                            <?php if($row['tahun_ajaran']): ?>
                                                <i class="bi bi-calendar"></i> <?= $row['tahun_ajaran'] ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(!empty($row['nama_walikelas'])): ?>
                                                <i class="bi bi-person-badge"></i> <?= $row['nama_walikelas'] ?> (<?= $row['nip_walikelas'] ?>)
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('admin/kelas/edit/' . $row['id']) ?>" class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="<?= base_url('admin/kelas/hapus/' . $row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus kelas ini?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Belum ada data kelas</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if($pager): ?>
                        <div class="d-flex justify-content-center mt-4">
                            <?= $pager->links('default', 'simple_pagination') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>