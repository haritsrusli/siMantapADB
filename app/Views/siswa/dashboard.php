<?= $this->extend('siswa/template') ?>

<?= $this->section('content') ?>

    <div class="row">
        <div class="col-md-12">
            <!-- Menampilkan hari, tanggal, dan waktu realtime -->
            <div class="text-center mb-4 py-3 bg-white rounded shadow-sm">
                <h2 id="currentDateTime" class="display-7 fw-bold text-primary mb-2" style="font-size: 1.8rem;"></h2>
                <p id="currentTime" class="lead fs-5 text-dark mb-0" style="font-size: 1.3rem;"></p>
            </div>
        </div>
    </div>

    <!-- Rekap Kehadiran -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Rekap Kehadiran</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="card border-success h-100">
                                <div class="card-body d-flex flex-column justify-content-center">
                                    <h3 class="card-title text-success"><?= isset($rekapKehadiran['hadir']) ? $rekapKehadiran['hadir'] : 0 ?></h3>
                                    <p class="card-text">Hari Hadir</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-danger h-100">
                                <div class="card-body d-flex flex-column justify-content-center">
                                    <h3 class="card-title text-danger"><?= isset($rekapKehadiran['tidak_hadir']) ? $rekapKehadiran['tidak_hadir'] : 0 ?></h3>
                                    <p class="card-text">Hari Tidak Hadir</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body d-flex flex-column justify-content-center">
                                    <h3 class="card-title text-primary"><?= isset($rekapKehadiran['total']) ? $rekapKehadiran['total'] : 0 ?></h3>
                                    <p class="card-text">Total Hari Kerja</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 justify-content-center">
        <div class="col-md-2 col-4 mb-4 text-center">
            <a href="<?= base_url('siswa/presensi') ?>" class="text-decoration-none text-dark">
                <div class="py-3">
                    <i class="bi bi-calendar-check text-primary" style="font-size: 3.5rem;"></i>
                    <h6 class="mt-2">Presensi</h6>
                </div>
            </a>
        </div>
        <div class="col-md-2 col-4 mb-4 text-center">
            <a href="<?= base_url('izin-keluar') ?>" class="text-decoration-none text-dark">
                <div class="py-3">
                    <i class="bi bi-box-arrow-right text-success" style="font-size: 3.5rem;"></i>
                    <h6 class="mt-2">Izin Keluar</h6>
                </div>
            </a>
        </div>
        <!-- Tambahkan ikon menu lainnya di sini di masa mendatang -->
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script>
        // Fungsi untuk memperbarui tanggal dan waktu realtime
        function updateDateTime() {
            const now = new Date();
            
            // Array nama hari dalam bahasa Indonesia
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            
            // Array nama bulan dalam bahasa Indonesia
            const months = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            
            // Mendapatkan komponen tanggal
            const dayName = days[now.getDay()];
            const date = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            
            // Mendapatkan komponen waktu
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            // Memperbarui elemen HTML dengan font yang lebih kecil
            document.getElementById('currentDateTime').textContent = `${dayName}, ${date} ${monthName} ${year}`;
            document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds}`;
        }
        
        // Memperbarui waktu setiap detik
        setInterval(updateDateTime, 1000);
        
        // Memanggil fungsi pertama kali saat halaman dimuat
        updateDateTime();
    </script>
<?= $this->endSection() ?>