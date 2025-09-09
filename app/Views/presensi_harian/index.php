<?= $this->extend('admin/template') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php helper('form'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Input Presensi Harian</h1>
        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
    
    <!-- Flash Messages -->
    

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?= base_url('presensi-harian') ?>">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tanggal">Tanggal:</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= esc($tanggal ?? date('Y-m-d')) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tingkat">Tingkat:</label>
                            <select class="form-control" id="tingkat" name="tingkat">
                                <option value="">Pilih Tingkat</option>
                                <option value="X" <?= (isset($tingkat) && $tingkat == 'X') ? 'selected' : '' ?>>X</option>
                                <option value="XI" <?= (isset($tingkat) && $tingkat == 'XI') ? 'selected' : '' ?>>XI</option>
                                <option value="XII" <?= (isset($tingkat) && $tingkat == 'XII') ? 'selected' : '' ?>>XII</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="jurusan">Jurusan:</label>
                            <select class="form-control" id="jurusan" name="jurusan">
                                <option value="">Pilih Jurusan</option>
                                <?php 
                                // Get unique jurusan from kelas data
                                $jurusan_list = [];
                                foreach ($kelas as $k) {
                                    if (!in_array($k['jurusan'], $jurusan_list)) {
                                        $jurusan_list[] = $k['jurusan'];
                                    }
                                }
                                sort($jurusan_list);
                                foreach ($jurusan_list as $j): ?>
                                    <option value="<?= esc($j) ?>" <?= (isset($jurusan) && $jurusan == $j) ? 'selected' : '' ?>>
                                        <?= esc($j) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12 text-right">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-filter"></i> Filter Data
                        </button>
                        <a href="<?= base_url('presensi-harian') ?>" class="btn btn-secondary">
                            <i class="fas fa-sync"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($tingkat) || isset($jurusan)): ?>
        <!-- Input Absensi Manual Section -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Input Absensi Manual</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($siswa)): ?>
                    <?= form_open('presensi-harian/simpan-massal', ['method' => 'post']) ?>
                        <?= csrf_field() ?>
                        <input type="hidden" name="tanggal" value="<?= esc($tanggal) ?>">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>NIS</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($siswa as $s): ?>
                                        <tr>
                                            <td><?= esc($s['nama_lengkap']) ?></td>
                                            <td><?= esc($s['username']) ?></td>
                                            <td>
                                                <input type="hidden" name="presensi[<?= $s['id'] ?>][user_id]" value="<?= $s['id'] ?>">
                                                <select class="form-control" name="presensi[<?= $s['id'] ?>][jenis]">
                                                    <option value="hadir">Hadir</option>
                                                    <option value="izin">Izin</option>
                                                    <option value="sakit">Sakit</option>
                                                    <option value="alpa">Alpa</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="presensi[<?= $s['id'] ?>][keterangan]" placeholder="Keterangan...">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Semua
                        </button>
                    <?= form_close() ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        Tidak ada siswa yang cocok dengan filter yang dipilih, atau semua siswa sudah memiliki data absensi untuk tanggal ini.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Daftar Absensi Hari Ini Section -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Absensi Tanggal <?= esc(date('d F Y', strtotime($tanggal))) ?></h6>
                <?php if (!empty($absensi_records)): ?>
                    <span class="badge badge-secondary"><?= count($absensi_records) ?> Data</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (!empty($absensi_records)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>NIS</th>
                                    <th>Kelas</th>
                                    <th>Jenis</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = (($pager['currentPage'] - 1) * $pager['perPage']) + 1; ?>
                                <?php foreach ($absensi_records as $record): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= esc($record['nama_siswa']) ?></td>
                                        <td><?= esc($record['nis']) ?></td>
                                        <td><?= esc($record['tingkat'] . ' ' . $record['jurusan'] . ' ' . $record['nama_kelas']) ?></td>
                                        <td>
                                            <?php
                                            $status_kehadiran = $record['status_kehadiran'] ?? '';
                                            if (!empty($status_kehadiran)) {
                                                switch ($status_kehadiran) {
                                                    case 'sakit':
                                                        echo '<span class="badge badge-danger">Sakit</span>';
                                                        break;
                                                    case 'izin':
                                                        echo '<span class="badge badge-warning">Izin</span>';
                                                        break;
                                                    case 'alpa':
                                                        echo '<span class="badge badge-dark">Alpa</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge badge-success">Hadir</span>';
                                                        break;
                                                }
                                            } else {
                                                echo '<span class="badge badge-success">Hadir</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><?= esc($record['keterangan'] ?? '-') ?></td>
                                        <td>
                                            <a href="<?= base_url('presensi-harian/edit/' . $record['id']) ?>" 
                                               class="btn btn-primary btn-sm mr-1">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="<?= base_url('presensi-harian/hapus/' . $record['id']) ?>" 
                                               class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Yakin ingin menghapus data absensi ini?')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($pager['totalPages'] > 1): ?>
                        <nav aria-label="Pagination">
                            <ul class="pagination justify-content-center">
                                <?php if ($pager['currentPage'] > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $pager['currentPage'] - 1])) ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $pager['totalPages']; $i++): ?>
                                    <li class="page-item <?= ($i == $pager['currentPage']) ? 'active' : '' ?>">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($pager['currentPage'] < $pager['totalPages']): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $pager['currentPage'] + 1])) ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        Belum ada data absensi manual untuk tanggal <?= esc(date('d F Y', strtotime($tanggal))) ?> dengan filter yang dipilih.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>