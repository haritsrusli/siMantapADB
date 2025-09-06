<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>

    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="bi bi-people"></i> Manajemen User Roles
            </h2>
            <p class="lead">Kelola roles untuk setiap user dalam sistem.</p>
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
                            <i class="bi bi-table"></i> Daftar User dan Roles
                        </h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Username</th>
                                    <th>Nama Lengkap</th>
                                    <th>Role Utama</th>
                                    <th>Roles Tambahan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <?php 
                                        $userModel = new \App\Models\User();
                                        $allRoles = $userModel->getAllUserRoles($user['id']);
                                        $additionalRoles = array_diff($allRoles, [$user['role']]);
                                    ?>
                                    <tr>
                                        <td><?= $user['username'] ?></td>
                                        <td><?= $user['nama_lengkap'] ?></td>
                                        <td>
                                            <span class="badge bg-primary"><?= ucfirst(str_replace('_', ' ', $user['role'])) ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($additionalRoles)): ?>
                                                <?php foreach ($additionalRoles as $role): ?>
                                                    <span class="badge bg-secondary"><?= ucfirst(str_replace('_', ' ', $role)) ?></span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('admin/user-roles/edit/' . $user['id']) ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil"></i> Edit Roles
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>