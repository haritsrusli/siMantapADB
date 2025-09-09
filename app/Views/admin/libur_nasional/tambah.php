<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="bi bi-calendar-plus"></i> Tambah Libur Nasional
            </h2>
            <p class="lead">Tambah data libur nasional baru.</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus"></i> Form Tambah Libur Nasional
                    </h5>
                </div>
                <div class="card-body">
                    
                    
                    <form method="post" action="<?= base_url('admin/libur-nasional/simpan') ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label">
                                    <i class="bi bi-calendar"></i> Tanggal
                                </label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="jenis_libur" class="form-label">
                                    <i class="bi bi-tag"></i> Jenis Libur
                                </label>
                                <select class="form-select" id="jenis_libur" name="jenis_libur" required>
                                    <option value="">Pilih Jenis Libur</option>
                                    <option value="nasional">Nasional</option>
                                    <option value="daerah">Daerah</option>
                                    <option value="khusus">Khusus/Cuti Bersama</option>
                                </select>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <label for="keterangan" class="form-label">
                                    <i class="bi bi-card-text"></i> Keterangan
                                </label>
                                <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Masukkan keterangan libur nasional" required>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/libur-nasional') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
