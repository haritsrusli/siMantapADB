<?= $this->extend('admin/template') // Assuming a generic template, might need adjustment ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle"></i> Formulir Pengajuan Izin Keluar
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('izin-keluar') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">Jenis Izin</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="jenis_izin" id="sakit" value="sakit" required>
                            <label class="form-check-label" for="sakit">Sakit</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="jenis_izin" id="keluarga" value="keluarga" required>
                            <label class="form-check-label" for="keluarga">Keluarga</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="jenis_izin" id="lainnya" value="lainnya" required>
                            <label class="form-check-label" for="lainnya">Lainnya</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alasan" class="form-label">Alasan Lengkap</label>
                        <textarea name="alasan" id="alasan" class="form-control" rows="3" required></textarea>
                    </div>

                    

                    <div class="mb-3">
                        <label for="bersama_siswa_ids" class="form-label">Izin Bersama (opsional)</label>
                        <select name="bersama_siswa_ids[]" id="bersama_siswa_ids" class="form-control" multiple>
                            <option value="">-- Pilih Siswa --</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= $student['id'] ?>"><?= $student['nama_lengkap'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Tahan Ctrl (atau Cmd di Mac) untuk memilih lebih dari satu siswa.</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('izin-keluar') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Ajukan Permintaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
