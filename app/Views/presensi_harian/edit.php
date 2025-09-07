<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Presensi Harian</h1>
    </div>

    <!-- Edit Absensi Manual Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Data Absensi</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= base_url('presensi-harian/update/' . $absensi['id']) ?>">
                <?= csrf_field() ?>
                <div class="form-group row">
                    <label for="nama_siswa" class="col-sm-2 col-form-label">Nama Siswa</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="nama_siswa" value="<?= esc($siswa['nama_lengkap']) ?>" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="nis" class="col-sm-2 col-form-label">NIS</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="nis" value="<?= esc($siswa['username']) ?>" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="kelas" class="col-sm-2 col-form-label">Kelas</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="kelas" 
                               value="<?= esc($kelas ? $kelas['tingkat'] . ' ' . $kelas['jurusan'] . ' ' . $kelas['nama_kelas'] : 'Tidak ada kelas') ?>" 
                               readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="tanggal" class="col-sm-2 col-form-label">Tanggal</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="tanggal" name="tanggal" 
                               value="<?= esc($absensi['tanggal']) ?>" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="jenis" class="col-sm-2 col-form-label">Jenis Absensi</label>
                    <div class="col-sm-10">
                        <select class="form-control" id="jenis" name="jenis" required>
                            <option value="hadir" <?= ($absensi['jenis'] == 'hadir') ? 'selected' : '' ?>>Hadir</option>
                            <option value="izin" <?= ($absensi['jenis'] == 'izin') ? 'selected' : '' ?>>Izin</option>
                            <option value="sakit" <?= ($absensi['jenis'] == 'sakit') ? 'selected' : '' ?>>Sakit</option>
                            <option value="alpa" <?= ($absensi['jenis'] == 'alpa') ? 'selected' : '' ?>>Alpa</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="keterangan" class="col-sm-2 col-form-label">Keterangan</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Keterangan..."><?= esc($absensi['keterangan']) ?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                        <a href="<?= base_url('presensi-harian') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>