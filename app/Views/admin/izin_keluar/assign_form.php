<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">
                    <i class="bi bi-person-plus-fill"></i> Manajemen Izin #<?= $izin['id'] ?>
                </h2>
                <p class="text-muted mb-0">Tugaskan staf dan atur waktu untuk permintaan izin keluar siswa.</p>
            </div>
            <a href="<?= base_url('izin-keluar') ?>" class="btn btn-light">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Kolom Kiri: Detail Izin -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-text"></i> Detail Permintaan Izin
                </h5>
            </div>
            <div class="card-body">
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

                <div class="mb-0">
                    <label class="form-label text-muted small mb-1">Alasan</label>
                    <div class="border rounded p-3 bg-light">
                        <?= esc($izin['alasan']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Form Penugasan -->
    <div class="col-lg-7">
        <form action="<?= base_url('admin/izin-keluar/' . $izin['id'] . '/assign') ?>" method="post" id="assignForm">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-people"></i> Penugasan & Pengaturan Izin
                    </h5>
                </div>
                <div class="card-body">
                    <?= csrf_field() ?>
                    
                    <div class="alert alert-info border-0">
                        Pilih staf yang akan bertanggung jawab untuk setiap tahap persetujuan izin ini.
                    </div>

                    <!-- Dropdown Penugasan -->
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="guru_kelas_id" class="form-label">Guru Kelas <span class="text-danger">*</span></label>
                            <select name="guru_kelas_id" id="guru_kelas_id" class="form-select select-role" required>
                                <option value="">-- Pilih Guru Kelas --</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>" <?= set_select('guru_kelas_id', $teacher['id'], ($izin['guru_kelas_id'] == $teacher['id'])) ?>>
                                        <?= esc($teacher['nama_lengkap']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="wakil_kurikulum_id" class="form-label">Wakil Kurikulum <span class="text-danger">*</span></label>
                            <select name="wakil_kurikulum_id" id="wakil_kurikulum_id" class="form-select select-role" required>
                                <option value="">-- Pilih Wakil Kurikulum --</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>" <?= set_select('wakil_kurikulum_id', $teacher['id'], ($izin['wakil_kurikulum_id'] == $teacher['id'])) ?>>
                                        <?= esc($teacher['nama_lengkap']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="guru_piket_id" class="form-label">Guru Piket <span class="text-danger">*</span></label>
                            <select name="guru_piket_id" id="guru_piket_id" class="form-select select-role" required>
                                <option value="">-- Pilih Guru Piket --</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>" <?= set_select('guru_piket_id', $teacher['id'], ($izin['guru_piket_id'] == $teacher['id'])) ?>>
                                        <?= esc($teacher['nama_lengkap']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Input Waktu -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jam_keluar" class="form-label">Jam Keluar <span class="text-danger">*</span></label>
                            <input type="time" name="jam_keluar" id="jam_keluar" class="form-control" value="<?= set_value('jam_keluar', $izin['jam_keluar'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jam_kembali" class="form-label">Jam Kembali</label>
                            <input type="time" name="jam_kembali" id="jam_kembali" class="form-control" value="<?= set_value('jam_kembali', $izin['jam_kembali'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // This script is no longer strictly necessary as the form now uses standard `required` attributes,
    // but it can be kept for better UX if desired. For now, we rely on backend validation.
});
</script>
<?= $this->endSection() ?>