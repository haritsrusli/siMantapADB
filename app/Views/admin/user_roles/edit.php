<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>

    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="bi bi-person"></i> Edit User Roles
            </h2>
            <p class="lead">Edit roles untuk user: <strong><?= $user['nama_lengkap'] ?></strong> (<?= $user['username'] ?>)</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-person-badge"></i> Roles untuk <?= $user['nama_lengkap'] ?>
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    <form id="userRolesForm">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-shield"></i> Role Utama
                            </label>
                            <input type="text" class="form-control" value="<?= ucfirst(str_replace('_', ' ', $user['role'])) ?>" readonly>
                            <div class="form-text">Role utama tidak dapat diubah di sini. Untuk mengubah role utama, gunakan fitur edit user.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="bi bi-shield-check"></i> Roles Tambahan
                            </label>
                            <div class="row">
                                <?php foreach ($availableRoles as $role): ?>
                                    <?php
                                    // Skip if the role is the user's main role
                                    if ($role === $user['role']) {
                                        continue;
                                    }

                                    // Conditional logic for students
                                    if ($user['role'] === 'siswa') {
                                        if ($role !== 'ketua_kelas' && $role !== 'sekretaris') {
                                            continue; // Skip other roles for students
                                        }
                                    }

                                    // Conditional logic for teachers and homeroom teachers
                                    if ($user['role'] === 'guru' || $user['role'] === 'wali_kelas') {
                                        if (in_array($role, ['siswa', 'ketua_kelas', 'sekretaris'])) {
                                            continue; // Skip student-related roles for teachers
                                        }
                                    }
                                    ?>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="<?= $role ?>" id="role_<?= $role ?>" <?= in_array($role, $userRoles) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="role_<?= $role ?>">
                                                <?= ucfirst(str_replace('_', ' ', $role)) ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/user-roles') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Roles
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.getElementById('userRolesForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalButtonContent = submitButton.innerHTML;

        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Menyimpan...';
        submitButton.disabled = true;

        // Kirim data ke server
        fetch('<?= base_url('admin/user-roles/update/' . $user['id']) ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Gagal memperbarui roles.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan koneksi saat menyimpan roles.', 'error');
        })
        .finally(() => {
            submitButton.innerHTML = originalButtonContent;
            submitButton.disabled = false;
        });
    });
</script>
<?= $this->endSection() ?>