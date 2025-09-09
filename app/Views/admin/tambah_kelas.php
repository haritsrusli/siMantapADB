<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="bi bi-book"></i> Tambah Kelas
            </h2>
            <p class="lead">Tambah data kelas baru.</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus"></i> Form Tambah Kelas
                    </h5>
                </div>
                <div class="card-body">
                    
                    <form method="post" action="<?= base_url('admin/kelas/simpan') ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_kelas" class="form-label">
                                    <i class="bi bi-book"></i> Nama Kelas
                                </label>
                                <input type="text" class="form-control" id="nama_kelas" name="nama_kelas" placeholder="Contoh: X RPL 1" required>
                                <div class="form-text">Masukkan nama kelas lengkap dengan tingkat dan jurusan</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tingkat" class="form-label">
                                    <i class="bi bi-collection"></i> Tingkat
                                </label>
                                <select class="form-select" id="tingkat" name="tingkat" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="X">X (Sepuluh)</option>
                                    <option value="XI">XI (Sebelas)</option>
                                    <option value="XII">XII (Dua Belas)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="jurusan" class="form-label">
                                    <i class="bi bi-mortarboard"></i> Jurusan
                                </label>
                                <input type="text" class="form-control" id="jurusan" name="jurusan" placeholder="Contoh: Rekayasa Perangkat Lunak" required>
                                <div class="form-text">Masukkan nama jurusan lengkap</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tahun_ajaran" class="form-label">
                                    <i class="bi bi-calendar"></i> Tahun Ajaran
                                </label>
                                <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" placeholder="Contoh: 2025/2026">
                                <div class="form-text">Opsional, format: 2025/2026</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="wali_kelas_user_id" class="form-label">
                                    <i class="bi bi-person-badge"></i> Wali Kelas
                                </label>
                                <select class="form-select" id="wali_kelas_user_id" name="wali_kelas_user_id">
                                    <option value="">-- Pilih Wali Kelas --</option>
                                    <?php if(!empty($walikelas_list)): ?>
                                        <?php foreach($walikelas_list as $walikelas): ?>
                                            <option value="<?= $walikelas['id'] ?>"><?= esc($walikelas['nama_lengkap']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="form-text">Opsional, pilih guru yang akan menjadi wali kelas</div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/kelas') ?>" class="btn btn-secondary">
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