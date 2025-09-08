<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">
            <i class="bi bi-person-check-fill"></i> Manajemen Penugasan Izin Keluar
        </h2>
        <p class="lead">Tunjuk guru yang bertanggung jawab untuk setiap tahap persetujuan izin keluar.</p>
    </div>
</div>

<div class="row mt-4">
    <!-- Kolom untuk menugaskan peran -->
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle"></i> Tugaskan Peran
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/penugasan-izin/assign') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Pilih Guru</label>
                        <select name="user_id" id="user_id" class="form-control" required>
                            <option value="">-- Pilih Guru --</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>"><?= $teacher['nama_lengkap'] ?> (<?= $teacher['username'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Sebagai</label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="">-- Pilih Peran --</option>
                            <?php foreach ($available_roles as $role): ?>
                                <option value="<?= $role ?>"><?= ucwords(str_replace('_', ' ', $role)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Tugaskan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom untuk melihat peran yang sudah ditugaskan -->
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-list-check"></i> Daftar Penugasan Saat Ini
                </h5>
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

                    <div class="alert alert-info">Daftar Wali Kelas diambil secara otomatis dari data Manajemen Kelas.</div>

                    <div class="mb-4">
                        <h6>Wali Kelas (Otomatis)</h6>
                        <ul class="list-group">
                            <?php if (!empty($auto_walikelas)): ?>
                                <?php foreach ($auto_walikelas as $walas): ?>
                                    <li class="list-group-item">
                                        <span>
                                            <i class="bi bi-person-badge"></i>
                                            <?= $walas['nama_lengkap'] ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item text-muted">Tidak ada Wali Kelas yang terdaftar.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <hr>

                <?php foreach ($available_roles as $role): ?>
                    <div class="mb-4">
                        <h6><?= ucwords(str_replace('_', ' ', $role)) ?></h6>
                        <ul class="list-group">
                            <?php if (!empty($grouped_assignments[$role])): ?>
                                <?php foreach ($grouped_assignments[$role] as $assignment): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="bi bi-person"></i>
                                            <?= $assignment['nama_lengkap'] ?>
                                        </span>
                                        <a href="<?= base_url('admin/penugasan-izin/unassign/' . $assignment['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin ingin menghapus tugas ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item text-muted">Belum ada guru yang ditugaskan.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
