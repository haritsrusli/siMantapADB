<?= $this->extend('admin/template') // Assuming a generic template, might need adjustment ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-text"></i> Detail Permintaan Izin #<?= $izin['id'] ?>
                </h5>
                <a href="<?= base_url('izin-keluar') ?>" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
            <div class="card-body">
                
                <h4>Informasi Utama</h4>
                <ul class="list-group mb-4">
                    <li class="list-group-item"><strong>Siswa:</strong> <?= esc($izin['nama_siswa']) ?></li>
                    <li class="list-group-item"><strong>Jenis Izin:</strong> <?= esc($izin['jenis_izin']) ?></li>
                    <li class="list-group-item"><strong>Alasan:</strong> <?= esc($izin['alasan']) ?></li>
                    <li class="list-group-item"><strong>Jam Keluar:</strong> <?= esc($izin['jam_keluar']) ?></li>
                    <li class="list-group-item"><strong>Jam Kembali:</strong> <?= esc($izin['jam_kembali'] ?? '-') ?></li>
                    <li class="list-group-item"><strong>Status:</strong> 
                        <?php 
                            $status_class = 'bg-secondary';
                            if ($izin['status'] === 'disetujui') $status_class = 'bg-success';
                            if ($izin['status'] === 'ditolak') $status_class = 'bg-danger';
                        ?>
                        <span class="badge <?= $status_class ?>"><?= ucwords(str_replace('_', ' ', $izin['status'])) ?></span>
                    </li>
                </ul>

                <?php if (!empty($bersama)): ?>
                <h4>Izin Bersama Dengan:</h4>
                <ul class="list-group mb-4">
                    <?php foreach ($bersama as $rekan): ?>
                        <li class="list-group-item"><?= esc($rekan['nama_lengkap']) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>

                <h4>Riwayat Persetujuan</h4>
                <ul class="list-group">
                    <li class="list-group-item">1. Permintaan diajukan oleh siswa.</li>
                    <li class="list-group-item">2. Ditugaskan ke Guru Kelas: <strong><?= esc($izin['nama_guru_kelas'] ?? '-') ?></strong></li>
                    <li class="list-group-item">3. Disetujui Wali Kelas: <strong><?= esc($izin['nama_wali_kelas'] ?? '-') ?></strong></li>
                    <li class="list-group-item">4. Disetujui Wakil Kurikulum: <strong><?= esc($izin['nama_wakil_kurikulum'] ?? '-') ?></strong></li>
                    <li class="list-group-item">5. Disetujui Guru Piket: <strong><?= esc($izin['nama_guru_piket'] ?? '-') ?></strong></li>
                </ul>

                <?php if ($izin['status'] === 'ditolak'): ?>
                    <div class="alert alert-danger mt-4">
                        <strong>Ditolak oleh:</strong> <?= esc($izin['nama_penolak']) ?><br>
                        <strong>Alasan:</strong> <?= esc($izin['catatan_penolakan']) ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
