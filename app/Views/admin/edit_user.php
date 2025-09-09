<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="bi bi-people"></i> Edit User
            </h2>
            <p class="lead">Edit data user.</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pencil"></i> Form Edit User
                    </h5>
                </div>
                <div class="card-body">
                    
                    
                    <form method="post" action="<?= base_url('admin/user/update/' . $user['id']) ?>" id="editUserForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">
                                    <i class="bi bi-person-lines-fill"></i> Role Utama
                                </label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Pilih Role</option>
                                    <option value="siswa" <?= $user['role'] == 'siswa' ? 'selected' : '' ?>>Siswa</option>
                                    <option value="guru" <?= $user['role'] == 'guru' ? 'selected' : '' ?>>Guru</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3" id="nisField" style="<?= $user['role'] == 'siswa' ? 'display: block;' : 'display: none;' ?>">
                                <label for="nis" class="form-label">
                                    <i class="bi bi-person-badge"></i> NIS
                                </label>
                                <input type="text" class="form-control" id="nis" name="nis" value="<?= $user['role'] == 'siswa' ? $user['username'] : '' ?>" placeholder="Nomor Induk Siswa" required>
                            </div>
                            
                            <div class="col-md-6 mb-3" id="nipField" style="<?= ($user['role'] == 'guru' || $user['role'] == 'wali_kelas') ? 'display: block;' : 'display: none;' ?>">
                                <label for="nip" class="form-label">
                                    <i class="bi bi-person-badge"></i> NIP
                                </label>
                                <input type="text" class="form-control" id="nip" name="nip" value="<?= ($user['role'] == 'guru' || $user['role'] == 'wali_kelas') ? $user['username'] : '' ?>" placeholder="Nomor Induk Pegawai" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nama_lengkap" class="form-label">
                                    <i class="bi bi-person"></i> Nama Lengkap
                                </label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= $user['nama_lengkap'] ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    <i class="bi bi-lock"></i> Password (Kosongkan jika tidak ingin mengubah)
                                </label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            
                            <div class="col-md-12 mb-3" id="kelasField" style="<?= ($user['role'] == 'wali_kelas' || $user['role'] == 'siswa') ? 'display: block;' : 'display: none;' ?>">
                                <label for="id_kelas" class="form-label">
                                    <i class="bi bi-book"></i> Kelas
                                </label>
                                <select class="form-select" id="id_kelas" name="id_kelas">
                                    <option value="">Pilih Kelas</option>
                                    <?php if(!empty($kelas)): ?>
                                        <?php foreach($kelas as $row): ?>
                                            <option value="<?= $row['id'] ?>" <?= $user['id_kelas'] == $row['id'] ? 'selected' : '' ?>><?= $row['nama_kelas'] ?> (<?= $row['tingkat'] ?> - <?= $row['jurusan'] ?>)</option>
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
                                <i class="bi bi-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleFields(roleValue) {
        var role = roleValue;
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
        
        // Tampilkan field sesuai role dan tambahkan atribut required
        if (role === 'siswa') {
            nisField.style.display = 'block';
            nisInput.setAttribute('required', 'required');
            kelasField.style.display = 'block';
        } else if (role === 'guru') {
            nipField.style.display = 'block';
            nipInput.setAttribute('required', 'required');
        }
        
        // Jika mengubah role dari walikelas ke role lain, hapus id_kelas
        if (role !== 'siswa') {
            document.getElementById('id_kelas').value = '';
        }
    }

    // Panggil fungsi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        toggleFields(document.getElementById('role').value);
    });

    // Panggil fungsi saat role diubah
    document.getElementById('role').addEventListener('change', function() {
        toggleFields(this.value);
    });
    </script>
<?= $this->endSection() ?>