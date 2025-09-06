<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="bi bi-book"></i> Edit Kelas
            </h2>
            <p class="lead">Edit data kelas.</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil"></i> Form Edit Kelas
                    </h5>
                </div>
                <div class="card-body">
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?= base_url('admin/kelas/update/' . $kelas['id']) ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_kelas" class="form-label">
                                    <i class="bi bi-book"></i> Nama Kelas
                                </label>
                                <input type="text" class="form-control" id="nama_kelas" name="nama_kelas" value="<?= $kelas['nama_kelas'] ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tingkat" class="form-label">
                                    <i class="bi bi-collection"></i> Tingkat
                                </label>
                                <select class="form-select" id="tingkat" name="tingkat" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="X" <?= $kelas['tingkat'] == 'X' ? 'selected' : '' ?>>X (Sepuluh)</option>
                                    <option value="XI" <?= $kelas['tingkat'] == 'XI' ? 'selected' : '' ?>>XI (Sebelas)</option>
                                    <option value="XII" <?= $kelas['tingkat'] == 'XII' ? 'selected' : '' ?>>XII (Dua Belas)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="jurusan" class="form-label">
                                    <i class="bi bi-mortarboard"></i> Jurusan
                                </label>
                                <input type="text" class="form-control" id="jurusan" name="jurusan" value="<?= $kelas['jurusan'] ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tahun_ajaran" class="form-label">
                                    <i class="bi bi-calendar"></i> Tahun Ajaran
                                </label>
                                <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" value="<?= $kelas['tahun_ajaran'] ?>">
                                <div class="form-text">Opsional, format: 2025/2026</div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/kelas') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>