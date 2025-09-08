<?= $this->extend('admin/template') // Assuming a generic template, might need adjustment ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">
            <i class="bi bi-calendar-check"></i> Modul Izin Keluar
        </h2>
        <p class="lead">Daftar permintaan izin keluar siswa.</p>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Permintaan</h5>
                <?php if (session()->get('role') === 'siswa'): ?>
                    <a href="<?= base_url('izin-keluar/new') ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> Buat Permintaan Baru
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nama Siswa</th>
                                <th>Jenis Izin</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th>Tgl Diajukan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($izin_requests)): ?>
                                <?php foreach ($izin_requests as $req): ?>
                                    <tr>
                                        <td><?= $req['id'] ?></td>
                                        <td><?= esc($req['nama_siswa']) ?></td>
                                        <td><span class="badge bg-info"><?= esc($req['jenis_izin']) ?></span></td>
                                        <td><?= esc($req['alasan']) ?></td>
                                        <td>
                                            <span class="badge bg-secondary"><?= ucwords(str_replace('_', ' ', $req['status'])) ?></span>
                                        </td>
                                        <td><?= date('d-m-Y H:i', strtotime($req['created_at'])) ?></td>
                                        <td>
                                            <?php $role = session()->get('role'); ?>
                                            
                                            <!-- Admin Action -->
                                            <?php if ($role === 'admin' && $req['status'] === 'diajukan'): ?>
                                                <a href="<?= base_url('admin/izin-keluar/' . $req['id'] . '/assign') ?>" class="btn btn-sm btn-info">Tugaskan Guru</a>
                                            <?php endif; ?>

                                            <!-- Teacher Actions -->
                                            <?php if (str_starts_with($req['status'], 'diproses_') && $role !== 'admin' && $role !== 'siswa'): ?>
                                                <button class="btn btn-sm btn-success" onclick="handleApproval(<?= $req['id'] ?>, 'approve')">Setuju</button>
                                                <button class="btn btn-sm btn-danger" onclick="handleApproval(<?= $req['id'] ?>, 'reject')">Tolak</button>
                                            <?php endif; ?>

                                            <a href="<?= base_url('izin-keluar/' . $req['id']) ?>" class="btn btn-sm btn-outline-secondary">Detail</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada permintaan izin.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Basic JS for handling approvals -->
<script>
function handleApproval(id, action) {
    let url = `<?= base_url('izin-keluar/') ?>${id}`;
    let data = new FormData();
    data.append('_method', 'PUT'); // Method spoofing for RESTful update
    data.append('action', action);

    if (action === 'reject') {
        const reason = prompt('Silakan masukkan alasan penolakan:');
        if (reason === null) return; // User cancelled
        data.append('catatan_penolakan', reason);
    }

    // Special case for final approval
    const status = '<?= $req["status"] ?? "" ?>';
    if (action === 'approve' && status === 'diproses_guru_piket') {
        const returnTime = prompt('Masukkan jam kembali siswa (format HH:MM):');
        if (!returnTime) return; // User cancelled or left empty
        data.append('jam_kembali', returnTime);
    }

    fetch(url, {
        method: 'POST', // POST because forms don't support PUT directly
        body: data,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status && data.status === 200) {
            alert('Aksi berhasil.');
            location.reload();
        } else {
            alert('Terjadi kesalahan: ' + (data.messages.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Tidak dapat memproses permintaan.');
    });
}
</script>

<?= $this->endSection() ?>
