<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-plus-fill"></i> Tugaskan Guru Kelas
                </h5>
            </div>
            <div class="card-body">
                <p>Anda akan menugaskan Guru Kelas untuk permintaan izin berikut:</p>
                <ul class="list-group mb-3">
                    <li class="list-group-item"><strong>Alasan:</strong> <?= esc($izin['alasan']) ?></li>
                    <li class="list-group-item"><strong>Jenis:</strong> <?= esc($izin['jenis_izin']) ?></li>
                    <li class="list-group-item"><strong>Jam Keluar:</strong> <?= esc($izin['jam_keluar']) ?></li>
                </ul>

                <form action="<?= base_url('admin/izin-keluar/' . $izin['id'] . '/assign') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="guru_kelas_id" class="form-label">Pilih Guru Kelas</label>
                        <select name="guru_kelas_id" id="guru_kelas_id" class="form-control" required>
                            <option value="">-- Pilih Guru --</option>
                            <?php if (!empty($guru_kelas_list)): ?>
                                <?php foreach ($guru_kelas_list as $guru): ?>
                                    <option value="<?= $guru['id'] ?>"><?= $guru['nama_lengkap'] ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Tidak ada Guru Kelas yang ditugaskan. Silakan atur di Manajemen Penugasan.</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="<?= base_url('izin-keluar') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Tugaskan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
