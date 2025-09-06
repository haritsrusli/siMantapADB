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
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?= base_url('admin/user/update/' . $user['id']) ?>" id="editUserForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">
                                    <i class="bi bi-person-lines-fill"></i> Role
                                </label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Pilih Role</option>
                                    <option value="siswa" <?= $user['role'] == 'siswa' ? 'selected' : '' ?>>Siswa</option>
                                    <option value="guru" <?= $user['role'] == 'guru' ? 'selected' : '' ?>>Guru</option>
                                    <option value="wali_kelas" <?= $user['role'] == 'wali_kelas' ? 'selected' : '' ?>>Wali Kelas</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3" id="usernameField" style="<?= ($user['role'] == 'siswa' || $user['role'] == 'guru' || $user['role'] == 'wali_kelas') ? 'display: block;' : 'display: none;' ?>">
                                <label for="username" class="form-label">
                                    <i class="bi bi-person-badge"></i> Username
                                </label>
                                <input type="text" class="form-control" id="username" name="username" value="<?= $user['username'] ?>" placeholder="Username untuk login">
                            </div>
                            
                            <div class="col-md-6 mb-3" id="nisField" style="<?= $user['role'] == 'siswa' ? 'display: block;' : 'display: none;' ?>">
                                <label for="nis" class="form-label">
                                    <i class="bi bi-person-badge"></i> NIS
                                </label>
                                <input type="text" class="form-control" id="nis" name="nis" value="<?= $user['role'] == 'siswa' ? $user['username'] : '' ?>" placeholder="Nomor Induk Siswa">
                            </div>
                            
                            <div class="col-md-6 mb-3" id="nipField" style="<?= ($user['role'] == 'guru' || $user['role'] == 'wali_kelas') ? 'display: block;' : 'display: none;' ?>">
                                <label for="nip" class="form-label">
                                    <i class="bi bi-person-badge"></i> NIP
                                </label>
                                <input type="text" class="form-control" id="nip" name="nip" value="<?= ($user['role'] == 'guru' || $user['role'] == 'wali_kelas') ? $user['username'] : '' ?>" placeholder="Nomor Induk Pegawai">
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
                            
                            <div class="col-md-12 mb-3" id="kelasField" style="<?= $user['role'] == 'wali_kelas' ? 'display: block;' : 'display: none;' ?>">
                                <label for="id_kelas" class="form-label">
                                    <i class="bi bi-book"></i> Kelas yang Diampu
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
    // Menampilkan/menyembunyikan field berdasarkan pilihan role
    document.getElementById('role').addEventListener('change', function() {
        var usernameField = document.getElementById('usernameField');
        var nisField = document.getElementById('nisField');
        var nipField = document.getElementById('nipField');
        var kelasField = document.getElementById('kelasField');
        
        // Sembunyikan semua field terlebih dahulu
        usernameField.style.display = 'none';
        nisField.style.display = 'none';
        nipField.style.display = 'none';
        kelasField.style.display = 'none';
        
        // Reset nilai field
        document.getElementById('username').value = '';
        document.getElementById('nis').value = '';
        document.getElementById('nip').value = '';
        document.getElementById('id_kelas').value = '';
        
        // Tampilkan field sesuai role
        if (this.value === 'siswa') {
            usernameField.style.display = 'block';
            nisField.style.display = 'block';
        } else if (this.value === 'guru' || this.value === 'wali_kelas') {
            usernameField.style.display = 'block';
            nipField.style.display = 'block';
            if (this.value === 'wali_kelas') {
                kelasField.style.display = 'block';
            }
        }
    });
    
    // Inisialisasi tampilan field saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        var roleSelect = document.getElementById('role');
        var usernameField = document.getElementById('usernameField');
        var nisField = document.getElementById('nisField');
        var nipField = document.getElementById('nipField');
        var kelasField = document.getElementById('kelasField');
        
        if (roleSelect.value === 'siswa') {
            usernameField.style.display = 'block';
            nisField.style.display = 'block';
        } else if (roleSelect.value === 'guru' || roleSelect.value === 'wali_kelas') {
            usernameField.style.display = 'block';
            nipField.style.display = 'block';
            if (roleSelect.value === 'wali_kelas') {
                kelasField.style.display = 'block';
            }
        }
    });
    </script>
<?= $this->endSection() ?>