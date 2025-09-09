<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Admin - Sistem Presensi Siswa' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
    <style>
        .notification-container {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        .notification-toast {
            background-color: #333;
            color: #fff;
            padding: 1rem 1.5rem;
            border-radius: 0.25rem;
            margin-top: 0.5rem;
            box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.1);
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
            transform: translateY(20px);
        }
        .notification-toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        .notification-toast.success {
            background-color: var(--bs-success);
        }
        .notification-toast.error {
            background-color: var(--bs-danger);
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
        <div class="container">
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav w-100">
                    <li class="nav-item">
                        <a class="nav-link <?= (current_url() == base_url('admin/dashboard')) ? 'active' : '' ?>" href="<?= base_url('admin/dashboard') ?>">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item ms-lg-auto">
                        <span class="nav-link">
                            <i class="bi bi-person-circle"></i> Halo, <?= session()->get('username') ?? 'Pengguna' ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm" href="<?= base_url('auth/logout') ?>">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <div id="notification-container" class="notification-container"></div>

    <footer class="bg-white text-center py-3 mt-5 border-top">
        <div class="container-fluid">
            <small class="text-muted">© <?= date('Y') ?> Sistem Presensi Siswa - SMK</small>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showNotification(message, type = 'success') {
            const container = document.getElementById('notification-container');
            const toast = document.createElement('div');
            toast.className = `notification-toast ${type}`;
            toast.textContent = message;
            container.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);

            // Animate out and remove
            setTimeout(() => {
                toast.classList.remove('show');
                toast.addEventListener('transitionend', () => toast.remove());
            }, 5000);
        }
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>