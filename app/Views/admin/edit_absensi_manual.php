<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Absensi Manual</h1>
        <a href="<?= base_url('admin/input-presensi-harian') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Edit Absensi Manual Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Data Absensi</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= base_url('admin/update-absensi-manual/' . $absensi['id']) ?>">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_siswa">Nama Siswa:</label>
                            <input type="text" class="form-control" id="nama_siswa" value="<?= esc($siswa['nama_lengkap']) ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nis">NIS:</label>
                            <input type="text" class="form-control" id="nis" value="<?= esc($siswa['username']) ?>" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="kelas">Kelas:</label>
                            <input type="text" class="form-control" id="kelas" 
                                   value="<?= esc(($kelas) ? $kelas['tingkat'] . ' ' . $kelas['jurusan'] . ' ' . $kelas['nama_kelas'] : '-') ?>" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal">Tanggal:</label>
                            <input type="text" class="form-control" id="tanggal" value="<?= esc(date('d F Y', strtotime($absensi['tanggal']))) ?>" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jenis">Jenis Absensi:</label>
                            <select class="form-control" id="jenis" name="jenis" required>
                                <option value="">Pilih Jenis</option>
                                <option value="izin" <?= ($absensi['jenis'] == 'izin') ? 'selected' : '' ?>>Izin</option>
                                <option value="sakit" <?= ($absensi['jenis'] == 'sakit') ? 'selected' : '' ?>>Sakit</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="keterangan">Keterangan:</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan" 
                                   value="<?= esc($absensi['keterangan'] ?? '') ?>" placeholder="Keterangan...">
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Update Absensi
                </button>
                <a href="<?= base_url('admin/input-presensi-harian') ?>" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>