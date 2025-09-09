<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="bi bi-calendar-check"></i> Edit Libur Nasional
            </h2>
            <p class="lead">Edit data libur nasional.</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil"></i> Form Edit Libur Nasional
                    </h5>
                </div>
                <div class="card-body">
                    
                    
                    <form method="post" action="<?= base_url('admin/libur-nasional/update/' . $libur_nasional['id']) ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label">
                                    <i class="bi bi-calendar"></i> Tanggal
                                </label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= $libur_nasional['tanggal'] ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="jenis_libur" class="form-label">
                                    <i class="bi bi-tag"></i> Jenis Libur
                                </label>
                                <select class="form-select" id="jenis_libur" name="jenis_libur" required>
                                    <option value="">Pilih Jenis Libur</option>
                                    <option value="nasional" <?= $libur_nasional['jenis_libur'] == 'nasional' ? 'selected' : '' ?>>Nasional</option>
                                    <option value="daerah" <?= $libur_nasional['jenis_libur'] == 'daerah' ? 'selected' : '' ?>>Daerah</option>
                                    <option value="khusus" <?= $libur_nasional['jenis_libur'] == 'khusus' ? 'selected' : '' ?>>Khusus/Cuti Bersama</option>
                                </select>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="keterangan" class="form-label">
                                    <i class="bi bi-card-text"></i> Keterangan
                                </label>
                                <input type="text" class="form-control" id="keterangan" name="keterangan" value="<?= $libur_nasional['keterangan'] ?>" placeholder="Masukkan keterangan libur nasional" required>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/libur-nasional') ?>" class="btn btn-secondary">
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
<?= $this->endSection() ?>
