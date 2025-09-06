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
            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
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
                        <a href="<?= base_url('admin/user-roles') ?>" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
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
                                    <?php if ($role !== $user['role']): // Jangan tampilkan role utama ?>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="roles[]" value="<?= $role ?>" id="role_<?= $role ?>" <?= in_array($role, $userRoles) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="role_<?= $role ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $role)) ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endif; ?>
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
        const userId = <?= $user['id'] ?>;
        
        // Kirim data ke server
        fetch('<?= base_url('admin/user-roles/update/' . $user['id']) ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Tampilkan pesan sukses
                const alert = document.createElement('div');
                alert.className = 'alert alert-success alert-dismissible fade show';
                alert.innerHTML = `
                    <i class="bi bi-check-circle"></i> ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                document.querySelector('.col-md-12').insertBefore(alert, document.querySelector('.card'));
                
                // Reload halaman setelah 2 detik
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                // Tampilkan pesan error
                const alert = document.createElement('div');
                alert.className = 'alert alert-danger alert-dismissible fade show';
                alert.innerHTML = `
                    <i class="bi bi-exclamation-triangle"></i> ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                document.querySelector('.col-md-12').insertBefore(alert, document.querySelector('.card'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Tampilkan pesan error
            const alert = document.createElement('div');
            alert.className = 'alert alert-danger alert-dismissible fade show';
            alert.innerHTML = `
                <i class="bi bi-exclamation-triangle"></i> Terjadi kesalahan saat menyimpan roles
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            document.querySelector('.col-md-12').insertBefore(alert, document.querySelector('.card'));
        });
    });
</script>
<?= $this->endSection() ?>