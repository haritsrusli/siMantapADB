<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>

<style>
    .pagination .page-link {
        color: #007bff;
    }
    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
    }
</style>
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="bi bi-calendar-check"></i> Rekap Harian Absensi
            </h2>
            <p class="lead">Lihat rekap absensi harian berdasarkan rentang waktu dan kelas.</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel"></i> Filter Rekap Absensi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                        </a>
                    </div>
                    
                    <form action="<?= base_url('admin/rekap-harian') ?>" method="get" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= esc($start_date ?? date('Y-m-01')) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= esc($end_date ?? date('Y-m-t')) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="id_kelas" class="form-label">Pilih Kelas</label>
                                <select class="form-select" id="id_kelas" name="id_kelas" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php if (!empty($kelas)): ?>
                                        <?php foreach ($kelas as $k): ?>
                                            <option value="<?= $k['id'] ?>" <?= ($id_kelas ?? '') == $k['id'] ? 'selected' : '' ?>>
                                                <?= $k['nama_kelas'] ?> (<?= $k['tingkat'] ?> - <?= $k['jurusan'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                        <!-- Hidden input to reset page to 1 when filtering -->
                        <input type="hidden" name="page" value="1">
                    </form>

                    <?php if (isset($rekap_harian) && !empty($rekap_harian)): ?>
                        <div class="table-responsive mt-4">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Kelas</th>
                                        <th>Total Siswa</th>
                                        <th>Hadir</th>
                                        <th>Izin</th>
                                        <th>Sakit</th>
                                        <th>Alpha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rekap_harian as $rekap): ?>
                                        <tr>
                                            <td><?= date('d-m-Y', strtotime($rekap['tanggal'])) ?></td>
                                            <td><?= esc($rekap['nama_kelas']) ?></td>
                                            <td><?= esc($rekap['total_siswa']) ?></td>
                                            <td><?= esc($rekap['hadir']) ?></td>
                                            <td><?= esc($rekap['izin']) ?></td>
                                            <td><?= esc($rekap['sakit']) ?></td>
                                            <td><?= esc($rekap['alpha']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            
                            <!-- Pagination Controls -->
                            <?php if (isset($pager) && $pager['totalPages'] > 1): ?>
                            <nav aria-label="Navigasi halaman">
                                <ul class="pagination justify-content-center">
                                    <!-- Previous Button -->
                                    <?php if ($pager['currentPage'] > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= base_url('admin/rekap-harian') ?>?start_date=<?= esc($pager['start_date']) ?>&end_date=<?= esc($pager['end_date']) ?>&id_kelas=<?= esc($pager['id_kelas']) ?>&page=<?= $pager['currentPage'] - 1 ?>" aria-label="Sebelumnya">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled">
                                            <span class="page-link" aria-hidden="true">&laquo;</span>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <!-- Page Numbers -->
                                    <?php 
                                    $start = max(1, $pager['currentPage'] - 2);
                                    $end = min($pager['totalPages'], $pager['currentPage'] + 2);
                                    
                                    // Show first page if not in range
                                    if ($start > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= base_url('admin/rekap-harian') ?>?start_date=<?= esc($pager['start_date']) ?>&end_date=<?= esc($pager['end_date']) ?>&id_kelas=<?= esc($pager['id_kelas']) ?>&page=1">1</a>
                                        </li>
                                        <?php if ($start > 2): ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = $start; $i <= $end; $i++): ?>
                                        <li class="page-item <?= $i == $pager['currentPage'] ? 'active' : '' ?>">
                                            <a class="page-link" href="<?= base_url('admin/rekap-harian') ?>?start_date=<?= esc($pager['start_date']) ?>&end_date=<?= esc($pager['end_date']) ?>&id_kelas=<?= esc($pager['id_kelas']) ?>&page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <!-- Show last page if not in range -->
                                    <?php if ($end < $pager['totalPages']): ?>
                                        <?php if ($end < $pager['totalPages'] - 1): ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        <?php endif; ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= base_url('admin/rekap-harian') ?>?start_date=<?= esc($pager['start_date']) ?>&end_date=<?= esc($pager['end_date']) ?>&id_kelas=<?= esc($pager['id_kelas']) ?>&page=<?= $pager['totalPages'] ?>"><?= $pager['totalPages'] ?></a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <!-- Next Button -->
                                    <?php if ($pager['currentPage'] < $pager['totalPages']): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= base_url('admin/rekap-harian') ?>?start_date=<?= esc($pager['start_date']) ?>&end_date=<?= esc($pager['end_date']) ?>&id_kelas=<?= esc($pager['id_kelas']) ?>&page=<?= $pager['currentPage'] + 1 ?>" aria-label="Berikutnya">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled">
                                            <span class="page-link" aria-hidden="true">&raquo;</span>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                            
                            <div class="text-center text-muted mt-2">
                                Menampilkan <?= count($rekap_harian) ?> dari <?= $pager['totalRecords'] ?> data 
                                (Halaman <?= $pager['currentPage'] ?> dari <?= $pager['totalPages'] ?>)
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php elseif (isset($rekap_harian)): ?>
                        <div class="alert alert-info text-center mt-4" role="alert">
                            <i class="bi bi-info-circle"></i> Tidak ada data rekap harian untuk filter yang dipilih.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
