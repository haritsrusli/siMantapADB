<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Admin - Sistem Presensi Siswa' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('admin/dashboard') ?>">
                Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= (current_url() == base_url('admin/dashboard')) ? 'active' : '' ?>" href="<?= base_url('admin/dashboard') ?>">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(current_url(), base_url('admin/user')) !== false) ? 'active' : '' ?>" href="<?= base_url('admin/user') ?>">
                            <i class="bi bi-people"></i> Manajemen User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(current_url(), base_url('admin/kelas')) !== false) ? 'active' : '' ?>" href="<?= base_url('admin/kelas') ?>">
                            <i class="bi bi-building"></i> Manajemen Kelas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(current_url(), base_url('admin/libur-nasional')) !== false) ? 'active' : '' ?>" href="<?= base_url('admin/libur-nasional') ?>">
                            <i class="bi bi-calendar-event"></i> Libur Nasional
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(current_url(), base_url('admin/pengaturan-presensi')) !== false) ? 'active' : '' ?>" href="<?= base_url('admin/pengaturan-presensi') ?>">
                            <i class="bi bi-geo-alt"></i> Pengaturan Presensi
                        </a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <a class="btn btn-outline-light btn-sm" href="<?= base_url('auth/logout') ?>">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <!-- Akses Cepat -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-lightning"></i> Akses Cepat
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="<?= base_url('admin/user-roles') ?>" class="btn btn-outline-primary">
                                <i class="bi bi-person-badge"></i> User Roles
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-white text-center py-3 mt-5 border-top">
        <div class="container-fluid">
            <small class="text-muted">© <?= date('Y') ?> Sistem Presensi Siswa - SMK</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>