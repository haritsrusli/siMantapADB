<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="bi bi-people"></i> Tambah User
            </h2>
            <p class="lead">Tambah user baru (siswa, guru, wali kelas).</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-plus"></i> Form Tambah User
                    </h5>
                </div>
                <div class="card-body">
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?= base_url('admin/user/simpan') ?>" id="tambahUserForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">
                                    <i class="bi bi-person-lines-fill"></i> Role
                                </label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Pilih Role</option>
                                    <option value="siswa">Siswa</option>
                                    <option value="guru">Guru</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3" id="nisField" style="display: none;">
                                <label for="nis" class="form-label">
                                    <i class="bi bi-person-badge"></i> NIS
                                </label>
                                <input type="text" class="form-control" id="nis" name="nis" placeholder="Nomor Induk Siswa">
                            </div>
                            
                            <div class="col-md-6 mb-3" id="nipField" style="display: none;">
                                <label for="nip" class="form-label">
                                    <i class="bi bi-person-badge"></i> NIP
                                </label>
                                <input type="text" class="form-control" id="nip" name="nip" placeholder="Nomor Induk Pegawai">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nama_lengkap" class="form-label">
                                    <i class="bi bi-person"></i> Nama Lengkap
                                </label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock"></i> Password
                                </label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="col-md-12 mb-3" id="kelasField" style="display: none;">
                                <label for="id_kelas" class="form-label">
                                    <i class="bi bi-book"></i> Kelas (Hanya untuk Siswa)
                                </label>
                                <select class="form-select" id="id_kelas" name="id_kelas">
                                    <option value="">Pilih Kelas</option>
                                    <?php if(!empty($kelas)): ?>
                                        <?php foreach($kelas as $row): ?>
                                            <option value="<?= $row['id'] ?>"><?= $row['nama_kelas'] ?> (<?= $row['tingkat'] ?> - <?= $row['jurusan'] ?>)</option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/user') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Menampilkan/menyembunyikan field berdasarkan pilihan role
    document.getElementById('role').addEventListener('change', function() {
        var nisField = document.getElementById('nisField');
        var nipField = document.getElementById('nipField');
        var kelasField = document.getElementById('kelasField');
        var nisInput = document.getElementById('nis');
        var nipInput = document.getElementById('nip');
        
        // Sembunyikan semua field terlebih dahulu dan hapus atribut required
        nisField.style.display = 'none';
        nisInput.removeAttribute('required');
        
        nipField.style.display = 'none';
        nipInput.removeAttribute('required');

        kelasField.style.display = 'none';
        
        // Reset nilai field
        nisInput.value = '';
        nipInput.value = '';
        document.getElementById('id_kelas').value = '';
        
        // Tampilkan field sesuai role dan tambahkan atribut required
        if (this.value === 'siswa') {
            nisField.style.display = 'block';
            nisInput.setAttribute('required', 'required');
            kelasField.style.display = 'block';
        } else if (this.value === 'guru') {
            nipField.style.display = 'block';
            nipInput.setAttribute('required', 'required');
        }
    });
    </script>
<?= $this->endSection() ?>