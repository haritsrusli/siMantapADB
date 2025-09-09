<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">
                    <i class="bi bi-plus-circle"></i> Formulir Pengajuan Izin Keluar
                </h2>
                <p class="text-muted mb-0">Ajukan permintaan izin keluar sekolah</p>
            </div>
            <a href="<?= base_url('izin-keluar') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-text"></i> Detail Pengajuan Izin
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('izin-keluar') ?>" method="post" id="izinForm">
                    <?= csrf_field() ?>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Jenis Izin</label>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <div class="form-check p-3 border rounded h-100">
                                    <input class="form-check-input" type="radio" name="jenis_izin" id="sakit" value="sakit" required>
                                    <label class="form-check-label d-flex align-items-center" for="sakit">
                                        <div class="me-2 text-danger">
                                            <i class="bi bi-emoji-frown fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Sakit</div>
                                            <div class="small text-muted">Izin karena kondisi kesehatan</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check p-3 border rounded h-100">
                                    <input class="form-check-input" type="radio" name="jenis_izin" id="keluarga" value="keluarga" required>
                                    <label class="form-check-label d-flex align-items-center" for="keluarga">
                                        <div class="me-2 text-warning">
                                            <i class="bi bi-people fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Keluarga</div>
                                            <div class="small text-muted">Kebutuhan keluarga mendesak</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check p-3 border rounded h-100">
                                    <input class="form-check-input" type="radio" name="jenis_izin" id="lainnya" value="lainnya" required>
                                    <label class="form-check-label d-flex align-items-center" for="lainnya">
                                        <div class="me-2 text-info">
                                            <i class="bi bi-question-circle fs-4"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Lainnya</div>
                                            <div class="small text-muted">Alasan lain yang spesifik</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="alasan" class="form-label fw-bold">Alasan Lengkap <span class="text-danger">*</span></label>
                        <textarea name="alasan" id="alasan" class="form-control" rows="4" placeholder="Jelaskan alasan Anda mengajukan izin keluar..." required></textarea>
                        <div class="form-text">Minimal 5 karakter</div>
                    </div>

                    <div class="mb-4">
                        <label for="bersama_siswa_ids" class="form-label fw-bold">Izin Bersama (opsional)</label>
                        <select name="bersama_siswa_ids[]" id="bersama_siswa_ids" class="form-control" multiple>
                            <option value="">-- Pilih Siswa --</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= $student['id'] ?>"><?= $student['nama_lengkap'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Tahan Ctrl (atau Cmd di Mac) untuk memilih lebih dari satu siswa.</div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('izin-keluar') ?>" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Ajukan Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm border-0 sticky-top" style="top: 1rem;">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle"></i> Informasi
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex mb-3">
                    <div class="me-3 text-primary">
                        <i class="bi bi-exclamation-circle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Proses Persetujuan</h6>
                        <p class="mb-0 small">Izin Anda akan melalui proses persetujuan berjenjang sebelum disetujui.</p>
                    </div>
                </div>
                
                <div class="d-flex mb-3">
                    <div class="me-3 text-success">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Waktu Respon</h6>
                        <p class="mb-0 small">Proses persetujuan memerlukan waktu, mohon bersabar.</p>
                    </div>
                </div>
                
                <div class="d-flex">
                    <div class="me-3 text-info">
                        <i class="bi bi-question-circle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Bantuan</h6>
                        <p class="mb-0 small">Jika ada pertanyaan, silakan hubungi admin atau wali kelas Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Form validation and submission
document.getElementById('izinForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Basic validation
    const jenisIzin = document.querySelector('input[name="jenis_izin"]:checked');
    const alasan = document.getElementById('alasan').value.trim();
    
    if (!jenisIzin) {
        alert('Silakan pilih jenis izin terlebih dahulu.');
        return;
    }
    
    if (alasan.length < 5) {
        alert('Alasan harus diisi minimal 5 karakter.');
        return;
    }
    
    // Submit form
    this.submit();
});
</script>
<?= $this->endSection() ?>
