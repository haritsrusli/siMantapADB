<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Presensi Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-form {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            margin: auto;
            text-align: center;
        }
        .school-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
        }
        .app-name {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 2rem;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 1rem;
        }
        .btn-login {
            background-color: #ff8c00;
            border: none;
            padding: 10px;
            border-radius: 10px;
            color: white;
            font-weight: bold;
        }
        .btn-login:hover {
            background-color: #ffa500;
        }
    </style>
</head>
<body class="bg-light">
    <div class="login-form">
        <img src="<?= base_url('assets/img/logoadb.png') ?>" alt="Logo Sekolah" class="school-logo" width="80" height="80">
        
        <h1 class="app-name">Sistem Presensi Siswa</h1>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <form action="<?= base_url('auth/login') ?>" method="post">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" name="username" required placeholder="Masukkan username">
                <label for="username">Username</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" required placeholder="Masukkan password">
                <label for="password">Password</label>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-login btn-lg">Login</button>
            </div>
        </form>
        
        <p class="mt-5 mb-3 text-muted">© <?= date('Y') ?> Sistem Presensi Siswa - SMK</p>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>