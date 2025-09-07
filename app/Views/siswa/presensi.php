<?= $this->extend('siswa/template') ?>

<?= $this->section('content') ?>

    <div id="status-container">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4"><i class="bi bi-calendar-check"></i> Halaman Presensi</h2>
                <p class="lead">Lihat status presensi Anda hari ini atau lakukan presensi.</p>

                <?php if(isset($is_weekend) && $is_weekend): ?>
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i> Hari ini adalah hari Sabtu atau Minggu.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Status Presensi Hari Ini -->
        <div class="row mt-2">
            <div class="col-md-12 mb-4">
                <div class="card border-success shadow h-100">
                    <div class="card-body text-center d-flex flex-column justify-content-between">
                        <div>
                            <i class="bi bi-calendar-check text-success" style="font-size: 3rem;"></i>
                            <h5 class="card-title mt-3">Presensi Harian</h5>
                        </div>
                        <div>
                            <?php if($presensi): ?>
                                <p class="text-success mb-1"><i class="bi bi-check-circle-fill"></i> Anda sudah presensi hari ini</p>
                                <p class="text-muted"><small><?= date('d M Y H:i:s', strtotime($presensi['waktu_presensi'])) ?></small></p>
                            <?php else: ?>
                                <p class="text-muted"><i class="bi bi-x-circle"></i> Anda belum presensi hari ini.</p>
                                <button id="show-presensi-btn" class="btn btn-success"><i class="bi bi-camera"></i> Lakukan Presensi</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-clock-history"></i> Riwayat Presensi</h5>
                        <p>Lihat riwayat presensi Anda secara lengkap.</p>
                        <a href="<?= base_url('siswa/riwayat') ?>" class="btn btn-outline-primary"><i class="bi bi-eye"></i> Lihat Riwayat</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <div id="presensi-form-container" style="display: none;">
        <div class="row">
            <div class="col-12">
                <h2 id="form-title" class="mb-4"></h2>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Pastikan lokasi Anda berada di dalam area sekolah dan wajah terlihat jelas.
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="bi bi-geo-alt"></i> Langkah 1: Verifikasi Lokasi</h5></div>
                    <div class="card-body">
                        <div id="location-status">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
                                <p class="mt-2">Mendeteksi lokasi...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card shadow" id="camera-card" style="display: none;">
                    <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="bi bi-camera"></i> Langkah 2: Ambil Foto Selfie</h5></div>
                    <div class="card-body">
                        <div class="text-center">
                            <video id="video" width="100%" height="200" autoplay class="border rounded"></video>
                            <canvas id="canvas" style="display:none;"></canvas>
                            <div id="photo-preview" style="display:none;">
                                <img id="photo" src="" alt="Selfie" class="img-fluid border rounded">
                            </div>
                            <button id="capture-btn" class="btn btn-success mt-2"><i class="bi bi-camera"></i> Ambil Foto</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-body text-center">
                        <button id="presensi-btn" class="btn btn-primary btn-lg" disabled><i class="bi bi-calendar-check"></i> Kirim Presensi</button>
                        <button id="cancel-btn" class="btn btn-secondary btn-lg ms-2"><i class="bi bi-x"></i> Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script>
        // Elements
        const statusContainer = document.getElementById('status-container');
        const presensiFormContainer = document.getElementById('presensi-form-container');
        const showPresensiBtn = document.getElementById('show-presensi-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const formTitle = document.getElementById('form-title');

        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const photo = document.getElementById('photo');
        const captureBtn = document.getElementById('capture-btn');
        const presensiBtn = document.getElementById('presensi-btn');
        const locationStatus = document.getElementById('location-status');
        const photoPreview = document.getElementById('photo-preview');
        const cameraCard = document.getElementById('camera-card');
        
        // Variables
        let latitude = null;
        let longitude = null;
        let stream = null;

        function showPresensiForm() {
            statusContainer.style.display = 'none';
            presensiFormContainer.style.display = 'block';
            formTitle.innerHTML = `<i class="bi bi-camera"></i> Form Presensi Harian`;
            getLocation();
        }

        function hidePresensiForm() {
            statusContainer.style.display = 'block';
            presensiFormContainer.style.display = 'none';
            
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            resetFormState();
        }

        if (showPresensiBtn) {
            showPresensiBtn.addEventListener('click', showPresensiForm);
        }

        cancelBtn.addEventListener('click', hidePresensiForm);
        
        function getLocation() {
            locationStatus.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Mendeteksi lokasi...</p></div>';
            cameraCard.style.display = 'none';
            presensiBtn.disabled = true;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else {
                locationStatus.innerHTML = "<div class='text-center'><i class='bi bi-x-circle text-danger' style='font-size: 2rem;'></i><p class='text-danger mt-2'>Geolocation tidak didukung oleh browser ini.</p></div>";
            }
        }
        
        function showPosition(position) {
            latitude = position.coords.latitude;
            longitude = position.coords.longitude;

            locationStatus.innerHTML = `<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Memverifikasi lokasi...</p></div>`;

            fetch('<?= base_url("siswa/check-location") ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
                body: JSON.stringify({ latitude: latitude, longitude: longitude })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    locationStatus.innerHTML = `<div class='text-center'><i class='bi bi-check-circle text-success' style='font-size: 2rem;'></i><p class='text-success mt-2'>Lokasi Anda sesuai. Silakan ambil foto.</p><p class='mb-1'><small>Lat: ${latitude.toFixed(6)}, Lon: ${longitude.toFixed(6)}</small></p></div>`;
                    cameraCard.style.display = 'block';
                    startCamera();
                } else {
                    locationStatus.innerHTML = `<div class='text-center'><i class='bi bi-x-circle text-danger' style='font-size: 2rem;'></i><p class='text-danger mt-2'>${data.message}</p><button id="retry-location-btn" class="btn btn-warning mt-2">Coba Lagi</button></div>`;
                    document.getElementById('retry-location-btn').addEventListener('click', getLocation);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                locationStatus.innerHTML = "<div class='text-center'><i class='bi bi-wifi-off text-danger' style='font-size: 2rem;'></i><p class='text-danger mt-2'>Gagal terhubung ke server untuk verifikasi lokasi.</p></div>";
            });
        }
        
        function showError(error) {
            cameraCard.style.display = 'none';
            presensiBtn.disabled = true;
            let message = '';
            switch(error.code) {
                case error.PERMISSION_DENIED: message = "Izin lokasi ditolak. Aktifkan lokasi untuk bisa presensi."; break;
                case error.POSITION_UNAVAILABLE: message = "Informasi lokasi tidak tersedia."; break;
                case error.TIMEOUT: message = "Waktu permintaan lokasi habis."; break;
                default: message = "Terjadi kesalahan yang tidak diketahui."; break;
            }
            locationStatus.innerHTML = `<div class='text-center'><i class='bi bi-shield-x text-danger' style='font-size: 2rem;'></i><p class='text-danger mt-2'>${message}</p></div>`;
        }
        
        function startCamera() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(s => {
                    stream = s;
                    video.srcObject = stream;
                })
                .catch(err => {
                    console.log("Error accessing camera: " + err);
                    cameraCard.querySelector('.card-body').innerHTML = `<div class='text-center'><i class='bi bi-camera-video-off text-danger' style='font-size: 2rem;'></i><p class='text-danger mt-2'>Gagal mengakses kamera. Pastikan izin kamera telah diberikan.</p></div>`;
                });
        }
        
        captureBtn.addEventListener('click', function() {
            if (captureBtn.textContent.includes('Ambil Foto')) {
                const context = canvas.getContext('2d');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                photo.setAttribute('src', canvas.toDataURL('image/png'));
                photoPreview.style.display = 'block';
                video.style.display = 'none';
                
                captureBtn.textContent = 'Ambil Ulang';
                captureBtn.classList.replace('btn-success', 'btn-warning');
                presensiBtn.disabled = false;
            } else {
                photoPreview.style.display = 'none';
                video.style.display = 'block';
                captureBtn.textContent = 'Ambil Foto';
                captureBtn.classList.replace('btn-warning', 'btn-success');
                presensiBtn.disabled = true;
            }
        });
        
        presensiBtn.addEventListener('click', function() {
            if (!latitude || !longitude) {
                showNotification('Lokasi belum terdeteksi.', 'danger');
                return;
            }
            if (!photo.src || photo.src === window.location.href) {
                showNotification('Silakan ambil foto selfie.', 'danger');
                return;
            }
            
            presensiBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
            presensiBtn.disabled = true;
            
            fetch('<?= base_url("siswa/do-presensi") ?>', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
                body: JSON.stringify({
                    latitude: latitude,
                    longitude: longitude,
                    foto_selfie: photo.src
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    let message = 'Presensi berhasil!';
                    if (data.message && data.message.includes('terlambat')) {
                        message += ' (Anda terlambat)';
                    }
                    showNotification(message, 'success');
                    setTimeout(() => window.location.reload(), 2000);
                } else {
                    showNotification('Presensi gagal: ' + data.message, 'danger');
                    presensiBtn.innerHTML = '<i class="bi bi-calendar-check"></i> Kirim Presensi';
                    presensiBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat mengirim data.', 'danger');
                presensiBtn.innerHTML = '<i class="bi bi-calendar-check"></i> Kirim Presensi';
                presensiBtn.disabled = false;
            });
        });

        function resetFormState() {
            locationStatus.innerHTML = '';
            cameraCard.style.display = 'none';
            video.style.display = 'block';
            photoPreview.style.display = 'none';
            photo.setAttribute('src', '');
            captureBtn.textContent = 'Ambil Foto';
            captureBtn.classList.replace('btn-warning', 'btn-success');
            presensiBtn.disabled = true;
            presensiBtn.innerHTML = '<i class="bi bi-calendar-check"></i> Kirim Presensi';
        }
        
        function showNotification(message, type) {
            const id = 'notif-' + Date.now();
            const notification = document.createElement('div');
            notification.id = id;
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
            notification.style.zIndex = '9999';
            notification.innerHTML = `<i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle'}"></i> ${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;
            document.body.appendChild(notification);
            setTimeout(() => document.getElementById(id)?.remove(), 5000);
        }
    </script>
<?= $this->endSection() ?>
