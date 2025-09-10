<?= $this->extend('admin/template') ?>

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

<div class="row mt-4 justify-content-center">
    <div class="col-md-2 col-4 mb-4 text-center">
        <a href="<?= base_url('izin-keluar') ?>" class="text-decoration-none text-dark">
            <div class="py-3">
                <i class="bi bi-box-arrow-right text-success" style="font-size: 3.5rem;"></i>
                <h6 class="mt-2">Izin Keluar</h6>
            </div>
        </a>
    </div>
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