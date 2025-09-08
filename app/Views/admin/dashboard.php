<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="bi bi-speedometer2"></i> Dashboard
            </h2>
            <p class="lead">Selamat datang di dashboard administrasi sistem presensi.</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card border-success shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">
                                <i class="bi bi-people text-success"></i> Total Siswa
                            </h5>
                            <h2><?= $total_siswa ?></h2>
                            <p class="text-muted mb-0">Jumlah siswa terdaftar</p>
                        </div>
                        <i class="bi bi-people-fill" style="font-size: 3rem; color: rgba(25, 135, 84, 0.2);"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card border-primary shadow h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">
                                <i class="bi bi-calendar-check text-primary"></i> Hadir Hari Ini
                            </h5>
                            <h2><?= $hadir ?></h2>
                            <p class="text-muted mb-0">Jumlah siswa yang sudah presensi</p>
                            <?php if(isset($persentase_hadir)): ?>
                                <p class="mb-0"><small>Persentase kehadiran: <?= $persentase_hadir ?>%</small></p>
                            <?php endif; ?>
                        </div>
                        <i class="bi bi-calendar-check-fill" style="font-size: 3rem; color: rgba(13, 110, 253, 0.2);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Akses Cepat Dipindahkan ke Bagian Bawah -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning"></i> Akses Cepat
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Baris Pertama -->
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('admin/pengaturan-presensi') ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-geo-alt"></i> Pengaturan Presensi
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('admin/user') ?>" class="btn btn-outline-success w-100">
                                <i class="bi bi-people"></i> Manajemen User
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('admin/kelas') ?>" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-book"></i> Manajemen Kelas
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('admin/libur-nasional') ?>" class="btn btn-outline-danger w-100">
                                <i class="bi bi-calendar-heart"></i> Libur Nasional
                            </a>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <!-- Baris Kedua -->
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('admin/input-presensi-harian') ?>" class="btn btn-outline-warning w-100">
                                <i class="bi bi-calendar-plus"></i> Input Presensi Harian
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('laporan') ?>" class="btn btn-outline-info w-100">
                                <i class="bi bi-file-earmark-bar-graph"></i> Rekap Presensi Harian
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('admin/user-roles') ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-person-badge"></i> User Roles
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('izin-keluar') ?>" class="btn btn-outline-dark w-100">
                                <i class="bi bi-calendar-check"></i> Manajemen Izin Keluar
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('admin/penugasan-izin') ?>" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-person-check-fill"></i> Penugasan Izin
                            </a>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>