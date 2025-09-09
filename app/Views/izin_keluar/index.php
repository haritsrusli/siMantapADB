<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">
                    <i class="bi bi-calendar-check"></i> Manajemen Izin Keluar
                </h2>
                <p class="text-muted mb-0">Daftar permintaan izin keluar siswa</p>
            </div>
            <?php if (session()->get('role') === 'siswa'): ?>
                <a href="<?= base_url('izin-keluar/new') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Buat Permintaan Baru
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="bi bi-list-task"></i> Daftar Permintaan Izin
                    </h5>
                </div>
                
                <!-- Filter Form -->
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="jenis_izin" class="form-label">Jenis Izin</label>
                        <select name="jenis_izin" id="jenis_izin" class="form-select">
                            <option value="">Semua Jenis</option>
                            <option value="sakit" <?= (isset($jenis_izin_filter) && $jenis_izin_filter == 'sakit') ? 'selected' : '' ?>>Sakit</option>
                            <option value="keluarga" <?= (isset($jenis_izin_filter) && $jenis_izin_filter == 'keluarga') ? 'selected' : '' ?>>Keluarga</option>
                            <option value="lainnya" <?= (isset($jenis_izin_filter) && $jenis_izin_filter == 'lainnya') ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="diajukan" <?= (isset($status_filter) && $status_filter == 'diajukan') ? 'selected' : '' ?>>Diajukan</option>
                            <option value="diproses_guru_kelas" <?= (isset($status_filter) && $status_filter == 'diproses_guru_kelas') ? 'selected' : '' ?>>Diproses Guru Mapel</option>
                            <option value="diproses_wali_kelas" <?= (isset($status_filter) && $status_filter == 'diproses_wali_kelas') ? 'selected' : '' ?>>Diproses Wali Kelas</option>
                            <option value="diproses_wakil_kesiswaan" <?= (isset($status_filter) && $status_filter == 'diproses_wakil_kesiswaan') ? 'selected' : '' ?>>Diproses Wakil Kesiswaan</option>
                            <option value="diproses_guru_piket" <?= (isset($status_filter) && $status_filter == 'diproses_guru_piket') ? 'selected' : '' ?>>Diproses Guru Piket</option>
                            <option value="disetujui" <?= (isset($status_filter) && $status_filter == 'disetujui') ? 'selected' : '' ?>>Disetujui</option>
                            <option value="ditolak" <?= (isset($status_filter) && $status_filter == 'ditolak') ? 'selected' : '' ?>>Ditolak</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="search" class="form-label">Cari Nama Siswa</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" id="search" class="form-control" placeholder="Masukkan nama siswa..." value="<?= isset($search_filter) ? esc($search_filter) : '' ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="btn-group" role="group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                            <a href="<?= base_url('izin-keluar') ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="izinTable">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">ID</th>
                                <th width="15%">Nama Siswa</th>
                                <th width="10%">Jenis Izin</th>
                                <th width="10%">Jam Keluar</th>
                                <th width="10%">Jam Kembali</th>
                                <th width="15%">Status</th>
                                <th width="15%">Tgl Diajukan</th>
                                <th width="10%" class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($izin_requests)): ?>
                                <?php foreach ($izin_requests as $req): ?>
                                    <tr data-status="<?= $req['status'] ?>">
                                        <td><?= $req['id'] ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <i class="bi bi-person-circle text-primary"></i>
                                                </div>
                                                <div><?= esc($req['nama_siswa']) ?></div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                                $jenis_badge = 'bg-info';
                                                if ($req['jenis_izin'] === 'sakit') $jenis_badge = 'bg-danger';
                                                if ($req['jenis_izin'] === 'keluarga') $jenis_badge = 'bg-warning';
                                            ?>
                                            <span class="badge <?= $jenis_badge ?>"><?= esc(ucfirst($req['jenis_izin'])) ?></span>
                                        </td>
                                        <td><?= esc($req['jam_keluar']) ?></td>
                                        <td><?= esc($req['jam_kembali']) ?></td>
                                        <td>
                                            <?php 
                                                $status = $req['status'];
                                                $currentUserRole = session()->get('role');
                                                $status_class = 'bg-secondary';

                                                // More descriptive status texts for all roles
                                                $friendly_map = [
                                                    'diajukan' => 'Diajukan',
                                                    'diproses_guru_kelas' => 'Proses Guru Mapel',
                                                    'diproses_wali_kelas' => 'Proses Wali Kelas',
                                                    'diproses_wakil_kesiswaan' => 'Proses Wakil Kesiswaan',
                                                    'diproses_guru_piket' => 'Proses Guru Piket',
                                                    'disetujui' => 'Disetujui',
                                                    'ditolak' => 'Ditolak'
                                                ];
                                                $status_text = $friendly_map[$status] ?? ucwords(str_replace('_', ' ', $status));

                                                if ($status === 'disetujui') {
                                                    $status_class = 'bg-success';
                                                    // For admin, show the name of the final approver (picket teacher)
                                                    if (!empty($req['nama_guru_piket']) && $currentUserRole === 'admin') {
                                                        $status_text = esc($req['nama_guru_piket']);
                                                    }
                                                } elseif ($status === 'ditolak') {
                                                    $status_class = 'bg-danger';
                                                } elseif ($status === 'diajukan') {
                                                    $status_class = 'bg-primary';
                                                } elseif (str_starts_with($status, 'diproses_')) {
                                                    $status_class = 'bg-warning text-dark';
                                                }
                                            ?>
                                            <span class="badge <?= $status_class ?>"><?= $status_text ?></span>
                                        </td>
                                        <td><?= date('d M Y H:i', strtotime($req['created_at'])) ?></td>
                                        <td class="text-end">
                                            <?php $role = session()->get('role'); ?>
                                            
                                            <!-- Admin Action -->
                                            <?php if ($role === 'admin'): ?>
                                                <?php if ($req['status'] !== 'disetujui' && $req['status'] !== 'ditolak'): ?>
                                                    <a href="<?= base_url('admin/izin-keluar/' . $req['id'] . '/assign') ?>" class="btn btn-sm btn-outline-primary" title="Tugaskan/Ubah Penugasan">
                                                        <i class="bi bi-person-plus"></i>
                                                    </a>
                                                <?php elseif ($req['status'] === 'ditolak'): ?>
                                                    <a href="<?= base_url('admin/izin-keluar/reset/' . $req['id']) ?>" class="btn btn-sm btn-outline-warning" title="Buka Kembali Pengajuan" onclick="return confirm('Anda yakin ingin membuka kembali pengajuan ini? Status akan kembali ke Diajukan dan data persetujuan sebelumnya akan dihapus.')">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </a>
                                                <?php elseif ($req['status'] === 'disetujui'): ?>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled title="Tidak dapat dibuka kembali karena sudah disetujui">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <!-- Teacher Actions -->
                                            <?php if (str_starts_with($req['status'], 'diproses_') && $role !== 'admin' && $role !== 'siswa'): ?>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-success" onclick="handleApproval(<?= $req['id'] ?>, 'approve', this)" title="Setujui">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                    <button class="btn btn-danger" onclick="toggleRejectionForm(<?= $req['id'] ?>)" title="Tolak">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Student Actions -->
                                            <?php if ($role === 'siswa' && $req['siswa_id'] == session()->get('user_id')): ?>
                                                <?php 
                                                // Cek apakah izin sudah disetujui oleh semua pihak
                                                $isFullyApproved = ($req['status'] === 'disetujui');
                                                ?>
                                                <?php if (!$isFullyApproved): ?>
                                                    <a href="<?= base_url('izin-keluar/remove/' . $req['id']) ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Anda yakin ingin menghapus permintaan izin ini?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled title="Tidak bisa dihapus karena sudah disetujui semua pihak">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <a href="<?= base_url('izin-keluar/' . $req['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr id="rejection-form-<?= $req['id'] ?>" style="display: none;" class="rejection-row">
                                        <td colspan="8" class="p-0">
                                            <div class="p-3 bg-light border-top">
                                                <form onsubmit="submitRejection(event, <?= $req['id'] ?>)">
                                                    <div class="mb-2">
                                                        <label for="catatan_penolakan_<?= $req['id'] ?>" class="form-label fw-bold">Alasan Penolakan</label>
                                                        <textarea id="catatan_penolakan_<?= $req['id'] ?>" name="catatan_penolakan" class="form-control" rows="3" required placeholder="Masukkan alasan mengapa permintaan ini ditolak..."></textarea>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <button type="button" class="btn btn-sm btn-secondary me-2" onclick="toggleRejectionForm(<?= $req['id'] ?>)">Batal</button>
                                                        <button type="submit" class="btn btn-sm btn-danger">Kirim Penolakan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                            <h6 class="mb-0">Tidak ada permintaan izin</h6>
                                            <p class="mb-0">Belum ada permintaan izin keluar yang diajukan</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if (!empty($izin_requests)): ?>
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan <?= count($izin_requests) ?> dari <?= count($izin_requests) ?> permintaan
                        </div>
                        <?php if (isset($pager) && $pager): ?>
                            <div class="d-flex justify-content-center flex-grow-1 me-3">
                                <?php 
                                // Preserve filter parameters in pagination links
                                $queryParams = [];
                                if (!empty($jenis_izin_filter)) $queryParams['jenis_izin'] = $jenis_izin_filter;
                                if (!empty($status_filter)) $queryParams['status'] = $status_filter;
                                if (!empty($search_filter)) $queryParams['search'] = $search_filter;
                                
                                if (!empty($queryParams)) {
                                    // Add query parameters to each pagination link
                                    $pager->setPath(current_url());
                                    echo $pager->only($queryParams)->links('izin_requests', 'simple_pagination');
                                } else {
                                    echo $pager->links('izin_requests', 'simple_pagination');
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function toggleRejectionForm(id) {
    document.querySelectorAll('.rejection-row').forEach(row => {
        if (row.id !== `rejection-form-${id}`) {
            row.style.display = 'none';
        }
    });
    const formRow = document.getElementById(`rejection-form-${id}`);
    if (formRow) {
        formRow.style.display = formRow.style.display === 'none' ? 'table-row' : 'none';
    }
}

function submitRejection(event, id) {
    event.preventDefault();
    const form = event.target;
    const textarea = form.querySelector('textarea[name="catatan_penolakan"]');
    const reason = textarea.value;
    const submitButton = form.querySelector('button[type="submit"]');

    if (!reason.trim()) {
        showNotification('Alasan penolakan tidak boleh kosong.', 'error');
        textarea.focus();
        return;
    }

    let url = `<?= base_url('izin-keluar/') ?>${id}`;
    let data = new FormData();
    data.append('_method', 'PUT');
    data.append('action', 'reject');
    data.append('catatan_penolakan', reason);

    const originalButtonContent = submitButton.innerHTML;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Mengirim...';
    submitButton.disabled = true;

    fetch(url, {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
        }
    })
    .then(response => {
        if (!response.ok) {
            // Try to parse error response, but fallback if it's not JSON
            return response.json().catch(() => {
                throw new Error(`Server responded with status: ${response.status}`)
            }).then(errData => {
                throw errData; // Throw parsed error data
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            showNotification(data.message || 'Permintaan berhasil ditolak.', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            // Handle structured errors from the backend
            const errorMsg = data.message || 'Terjadi kesalahan tidak diketahui.';
            showNotification(errorMsg, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const errorMsg = error.message || 'Tidak dapat memproses permintaan ke server.';
        showNotification(errorMsg, 'error');
    })
    .finally(() => {
        submitButton.innerHTML = originalButtonContent;
        submitButton.disabled = false;
    });
}

function handleApproval(id, action, element) {
    let url = `<?= base_url('izin-keluar/') ?>${id}`;
    let data = new FormData();
    data.append('_method', 'PUT');
    data.append('action', action);

    const currentStatus = element.closest('tr').dataset.status;

    if (action === 'approve' && currentStatus === 'diproses_guru_piket') {
        const returnTime = prompt('Masukkan jam kembali siswa (format HH:MM):');
        if (returnTime === null) return; // User cancelled
        if (!returnTime.match(/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/)) {
            showNotification('Format jam tidak valid. Gunakan format HH:MM.', 'error');
            return;
        }
        data.append('jam_kembali', returnTime);
    }

    const originalContent = element.innerHTML;
    element.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
    element.disabled = true;

    fetch(url, {
        method: 'POST',
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
        }
    })
    .then(response => {
         if (!response.ok) {
            return response.json().catch(() => {
                throw new Error(`Server responded with status: ${response.status}`)
            }).then(errData => {
                throw errData; // Throw parsed error data
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            showNotification(data.message || 'Aksi berhasil.', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            const errorMsg = data.message || 'Terjadi kesalahan tidak diketahui.';
            showNotification(errorMsg, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const errorMsg = error.message || 'Tidak dapat memproses permintaan ke server.';
        showNotification(errorMsg, 'error');
    })
    .finally(() => {
        element.innerHTML = originalContent;
        element.disabled = false;
    });
}
</script>
<?= $this->endSection() ?>