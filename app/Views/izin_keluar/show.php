<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">
                    <i class="bi bi-file-earmark-text"></i> Detail Permintaan Izin #<?= $izin['id'] ?>
                </h2>
                <p class="text-muted mb-0">Informasi lengkap permintaan izin keluar</p>
            </div>
            <a href="<?= base_url('izin-keluar') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle"></i> Informasi Utama
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Nama Siswa</label>
                            <div class="d-flex align-items-center">
                                <div class="me-2 text-primary">
                                    <i class="bi bi-person-circle fs-4"></i>
                                </div>
                                <h5 class="mb-0"><?= esc($izin['nama_siswa']) ?></h5>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Jenis Izin</label>
                            <?php 
                                $jenis_badge = 'bg-info';
                                if ($izin['jenis_izin'] === 'sakit') $jenis_badge = 'bg-danger';
                                if ($izin['jenis_izin'] === 'keluarga') $jenis_badge = 'bg-warning';
                            ?>
                            <div>
                                <span class="badge <?= $jenis_badge ?> fs-6"><?= esc(ucfirst($izin['jenis_izin'])) ?></span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Tanggal Pengajuan</label>
                            <div class="fw-bold"><?= date('d M Y H:i', strtotime($izin['created_at'])) ?></div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Jam Keluar</label>
                            <div class="fw-bold"><?= esc($izin['jam_keluar']) ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Jam Kembali</label>
                            <div class="fw-bold">
                                <?php if (empty($izin['jam_kembali']) && $izin['status'] === 'disetujui'): ?>
                                    <span class="text-muted">Siswa tidak kembali ke sekolah</span>
                                <?php elseif (!empty($izin['jam_kembali'])): ?>
                                    <?= esc($izin['jam_kembali']) ?>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Status</label>
                            <?php 
                                $status_map = [
                                    'diajukan' => 'Menunggu Diproses Admin',
                                    'diproses_guru_kelas' => 'Menunggu Persetujuan Guru Kelas',
                                    'diproses_wali_kelas' => 'Menunggu Persetujuan Wali Kelas',
                                    'diproses_wakil_kurikulum' => 'Menunggu Persetujuan Wakil Kesiswaan',
                                    'diproses_guru_piket' => 'Menunggu Persetujuan Guru Piket',
                                    'disetujui' => 'Disetujui',
                                    'ditolak' => 'Ditolak'
                                ];
                                $current_status = $izin['status'];
                                $status_text = $status_map[$current_status] ?? ucwords(str_replace('_', ' ', $current_status));

                                $status_class = 'bg-secondary';
                                if ($current_status === 'disetujui') {
                                    $status_class = 'bg-success';
                                } elseif ($current_status === 'ditolak') {
                                    $status_class = 'bg-danger';
                                } elseif (str_starts_with($current_status, 'diproses_')) {
                                    $status_class = 'bg-warning text-dark';
                                } elseif ($current_status === 'diajukan') {
                                    $status_class = 'bg-primary';
                                }
                            ?>
                            <div>
                                <span class="badge <?= $status_class ?> fs-6"><?= esc($status_text) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="mb-0">
                            <label class="form-label text-muted small mb-1">Alasan</label>
                            <div class="border rounded p-3 bg-light">
                                <?= esc($izin['alasan']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($bersama)): ?>
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-people"></i> Izin Bersama Dengan
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($bersama as $rekan): ?>
                    <div class="col-md-6 col-lg-4 mb-2">
                        <div class="d-flex align-items-center">
                            <div class="me-2 text-info">
                                <i class="bi bi-person-circle"></i>
                            </div>
                            <div><?= esc($rekan['nama_lengkap']) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm border-0 sticky-top" style="top: 1rem;">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-clipboard-check"></i> Riwayat Persetujuan
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php
                        $status = $izin['status'];
                        $statusOrder = ['diajukan', 'diproses_guru_kelas', 'diproses_wali_kelas', 'diproses_wakil_kesiswaan', 'diproses_guru_piket', 'disetujui'];
                        
                        // This is the status name of the step being rendered.
                        // The key is the step name, value is the status name in the DB.
                        $stepStatuses = [
                            'guru_mapel' => 'diproses_guru_kelas',
                            'wali_kelas' => 'diproses_wali_kelas',
                            'wakil_kesiswaan' => 'diproses_wakil_kesiswaan',
                            'guru_piket' => 'diproses_guru_piket',
                            'selesai' => 'disetujui'
                        ];

                        function render_approval_badge($step_status_name, $izin, $statusOrder) {
                            $current_status = $izin['status'];
                            $is_rejected = ($current_status === 'ditolak');
                            $rejected_stage = $izin['rejected_at_stage'] ?? null;

                            $step_index = array_search($step_status_name, $statusOrder);

                            if ($is_rejected) {
                                $rejected_stage_index = array_search($rejected_stage, $statusOrder);

                                if ($step_index < $rejected_stage_index) {
                                    return '<span class="badge bg-success rounded-pill">Selesai</span>';
                                } elseif ($step_index === $rejected_stage_index) {
                                    return '<span class="badge bg-danger rounded-pill">Ditolak</span>';
                                } else {
                                    return '<span class="badge bg-secondary rounded-pill">Tidak Diproses</span>';
                                }
                            } else {
                                $current_status_index = array_search($current_status, $statusOrder);
                                // Ketika status adalah 'disetujui', tahap 'diproses_guru_piket' juga harus menunjukkan "Selesai"
                                if ($current_status === 'disetujui' && $step_status_name === 'diproses_guru_piket') {
                                    return '<span class="badge bg-success rounded-pill">Selesai</span>';
                                } 
                                // Ketika status adalah 'disetujui', tahap 'disetujui' (tahap 6) juga harus menunjukkan "Selesai"
                                else if ($current_status === 'disetujui' && $step_status_name === 'disetujui') {
                                    return '<span class="badge bg-success rounded-pill">Selesai</span>';
                                } else if ($current_status_index > $step_index) {
                                    return '<span class="badge bg-success rounded-pill">Selesai</span>';
                                } else if ($current_status_index === $step_index) {
                                    return '<span class="badge bg-warning text-dark rounded-pill">Menunggu</span>';
                                } else {
                                    return '<span class="badge bg-secondary rounded-pill">Belum Diproses</span>';
                                }
                            }
                        }
                    ?>

                    <!-- 1. Diajukan -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold small">1. Permintaan diajukan</div>
                            <small class="text-muted">Oleh siswa</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">Selesai</span>
                    </div>
                    
                    <!-- 2. Guru Mapel -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold small">2. Persetujuan Guru Mapel</div>
                            <small class="text-muted"><?= esc($izin['nama_guru_kelas'] ?? 'Menunggu Penugasan') ?></small>
                        </div>
                        <?= render_approval_badge('diproses_guru_kelas', $izin, $statusOrder) ?>
                    </div>
                    
                    <!-- 3. Wali Kelas -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold small">3. Persetujuan Wali Kelas</div>
                            <small class="text-muted"><?= esc($izin['nama_wali_kelas'] ?? '-') ?></small>
                        </div>
                        <?= render_approval_badge('diproses_wali_kelas', $izin, $statusOrder) ?>
                    </div>
                    
                    <!-- 4. Wakil Kesiswaan -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold small">4. Persetujuan Wakil Kesiswaan</div>
                            <small class="text-muted"><?= esc($izin['nama_wakil_kurikulum'] ?? '-') ?></small>
                        </div>
                        <?= render_approval_badge('diproses_wakil_kesiswaan', $izin, $statusOrder) ?>
                    </div>
                    
                    <!-- 5. Guru Piket -->
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold small">5. Persetujuan Guru Piket</div>
                            <small class="text-muted"><?= esc($izin['nama_guru_piket'] ?? '-') ?></small>
                        </div>
                        <?= render_approval_badge('diproses_guru_piket', $izin, $statusOrder) ?>
                    </div>

                     <!-- 6. Selesai -->
                     <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold small">6. Selesai</div>
                            <small class="text-muted">Permintaan izin disetujui</small>
                        </div>
                        <?= render_approval_badge('disetujui', $izin, $statusOrder) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($izin['status'] === 'ditolak'): ?>
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-danger">
                    <i class="bi bi-exclamation-triangle"></i> Informasi Penolakan
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label text-muted small mb-1">Ditolak oleh</label>
                    <div><?= esc($izin['nama_penolak'] ?? 'Tidak diketahui') ?></div>
                </div>
                <div>
                    <label class="form-label text-muted small mb-1">Alasan Penolakan</label>
                    <div class="border rounded p-3 bg-light">
                        <?= esc($izin['catatan_penolakan'] ?? '-') ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
