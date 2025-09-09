<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="bi bi-people"></i> Manajemen User
            </h2>
            <p class="lead">Kelola semua user (siswa, guru, wali kelas).</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-table"></i> Data User
                        </h5>
                        <a href="<?= base_url('admin/user/tambah') ?>" class="btn btn-primary">
                            <i class="bi bi-plus"></i> Tambah User
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                        </a>
                    </div>
                    
                    <form action="<?= base_url('admin/user') ?>" method="get" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label visually-hidden">Cari NIP/NIS/Nama</label>
                                <input type="text" class="form-control" id="search" name="search" placeholder="Cari NIP/NIS/Nama..." value="<?= esc($search ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="role" class="form-label visually-hidden">Filter Role</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="">Semua Role</option>
                                    <option value="admin" <?= ($role ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="siswa" <?= ($role ?? '') == 'siswa' ? 'selected' : '' ?>>Siswa</option>
                                    <option value="guru" <?= ($role ?? '') == 'guru' ? 'selected' : '' ?>>Guru</option>
                                    <option value="wali_kelas" <?= ($role ?? '') == 'wali_kelas' ? 'selected' : '' ?>>Wali Kelas</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="id_kelas" class="form-label visually-hidden">Filter Kelas</label>
                                <select class="form-select" id="id_kelas" name="id_kelas">
                                    <option value="">Semua Kelas</option>
                                    <?php if (!empty($kelas)): ?>
                                        <?php foreach ($kelas as $k): ?>
                                            <option value="<?= $k['id'] ?>" <?= ($id_kelas ?? '') == $k['id'] ? 'selected' : '' ?>>
                                                <?= $k['nama_kelas'] ?> (<?= $k['tingkat'] ?> - <?= $k['jurusan'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>NIP/NIS</th>
                                    <th>Nama Lengkap</th>
                                    <th>Role</th>
                                    <th>Kelas (jika wali kelas)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($users)):
                                    $no = 1;
                                    if (isset($pager)) {
                                        $no = (($pager->getCurrentPage('group1') - 1) * $pager->getPerPage('group1')) + 1;
                                    }
                                    foreach($users as $row):
                                 ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <i class="bi bi-card-text"></i> 
                                            <?= $row['username'] ?>
                                        </td>
                                        <td>
                                            <i class="bi bi-person"></i> <?= $row['nama_lengkap'] ?>
                                        </td>
                                        <td>
                                            <?php if($row['role'] == 'admin'): ?>
                                                <span class="badge bg-primary">Admin</span>
                                            <?php elseif($row['role'] == 'siswa'): ?>
                                                <span class="badge bg-success">Siswa</span>
                                            <?php elseif($row['role'] == 'guru'): ?>
                                                <span class="badge bg-info">Guru</span>
                                            <?php elseif($row['role'] == 'wali_kelas'): ?>
                                                <span class="badge bg-warning text-dark">Wali Kelas</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['role'] === 'guru' && !empty($row['wali_kelas_nama_kelas'])): ?>
                                                <i class="bi bi-book"></i> <?= esc($row['wali_kelas_nama_kelas']) ?>
                                            <?php elseif ($row['role'] === 'siswa' && !empty($row['id_kelas'])): ?>
                                                <?php 
                                                // Find class name for student based on id_kelas
                                                $student_class_name = '-';
                                                if (!empty($kelas)) {
                                                    foreach($kelas as $k) {
                                                        if($k['id'] == $row['id_kelas']) {
                                                            $student_class_name = $k['nama_kelas'];
                                                            break;
                                                        }
                                                    }
                                                }
                                                echo esc($student_class_name);
                                                ?>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($row['role'] != 'admin'): ?>
                                                <a href="<?= base_url('admin/user/edit/' . $row['id']) ?>" class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <a href="<?= base_url('admin/user/hapus/' . $row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Tidak bisa diubah</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2">Belum ada data user</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center">
                            <?= $pager->links('group1', 'simple_pagination') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
