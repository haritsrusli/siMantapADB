<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">
                    <i class="bi bi-person-check-fill"></i> Manajemen Penugasan Izin Keluar
                </h2>
                <p class="text-muted mb-0">Tunjuk guru yang bertanggung jawab untuk setiap tahap persetujuan izin keluar</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="alert alert-info border-0 shadow-sm">
            <div class="d-flex">
                <div class="me-2">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <div>
                    <strong>Informasi:</strong> Wali Kelas ditentukan secara otomatis berdasarkan data kelas. 
                    Hanya Guru dan Admin yang dapat ditugaskan untuk peran Guru Kelas, Wakil Kurikulum, dan Guru Piket.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Form penugasan peran -->
    <div class="col-md-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle"></i> Tugaskan Peran Baru
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/penugasan-izin/assign') ?>" method="post" id="assignForm">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="user_id" class="form-label fw-bold">Pilih Guru <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="form-control form-select" required>
                            <option value="">-- Pilih Guru --</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>"><?= $teacher['nama_lengkap'] ?> (<?= $teacher['username'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Hanya Guru dan Admin yang dapat ditugaskan</div>
                    </div>
                    <div class="mb-4">
                        <label for="role" class="form-label fw-bold">Sebagai <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-control form-select" required>
                            <option value="">-- Pilih Peran --</option>
                            <?php foreach ($available_roles as $role): ?>
                                <option value="<?= $role ?>"><?= ucwords(str_replace('_', ' ', $role)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus"></i> Tugaskan Peran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar penugasan -->
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-list-check"></i> Daftar Penugasan Saat Ini
                </h5>
            </div>
            <div class="card-body">
                

                <!-- Wali Kelas Otomatis -->
                <div class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="bi bi-person-badge"></i> Wali Kelas (Otomatis)
                        </h5>
                        <span class="badge bg-info">Otomatis</span>
                    </div>
                    <?php if (!empty($auto_walikelas)): ?>
                        <div class="row">
                            <?php foreach ($auto_walikelas as $walas): ?>
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center p-2 bg-light rounded">
                                        <div class="me-2 text-info">
                                            <i class="bi bi-person-badge"></i>
                                        </div>
                                        <div class="small"><?= $walas['nama_lengkap'] ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 bg-light rounded">
                            <i class="bi bi-person-x display-6 text-muted mb-2"></i>
                            <p class="mb-0 text-muted">Tidak ada Wali Kelas yang terdaftar.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Penugasan Manual -->
                <?php foreach ($available_roles as $role): ?>
                    <div class="mb-4">
                        <h5 class="mb-3">
                            <i class="bi bi-person-check"></i> <?= ucwords(str_replace('_', ' ', $role)) ?>
                        </h5>
                        <?php if (!empty($grouped_assignments[$role])): ?>
                            <div class="row">
                                <?php foreach ($grouped_assignments[$role] as $assignment): ?>
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex justify-content-between align-items-center p-2 bg-white border rounded">
                                            <div class="d-flex align-items-center">
                                                <div class="me-2 text-primary">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                                <div class="small"><?= $assignment['nama_lengkap'] ?></div>
                                            </div>
                                            <a href="<?= base_url('admin/penugasan-izin/unassign/' . $assignment['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Anda yakin ingin menghapus tugas ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4 bg-light rounded">
                                <i class="bi bi-person-plus display-6 text-muted mb-2"></i>
                                <p class="mb-0 text-muted">Belum ada guru yang ditugaskan untuk peran ini.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Form validation
document.getElementById('assignForm').addEventListener('submit', function(e) {
    const user = document.getElementById('user_id').value;
    const role = document.getElementById('role').value;
    
    if (!user || !role) {
        e.preventDefault();
        showNotification('Silakan lengkapi semua field yang diperlukan.', 'error');
    }
});
</script>
<?= $this->endSection() ?>
