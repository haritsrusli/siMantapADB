<?= $this->extend('siswa/template') ?>

<?= $this->section('content') ?>

    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="bi bi-person"></i> Profil Siswa
            </h2>
            <p class="lead">Kelola informasi profil Anda.</p>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person"></i> Profil Siswa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <?php if(!empty($user['foto_profil'])): ?>
                                <img src="<?= base_url('uploads/profiles/' . $user['foto_profil']) ?>" alt="Foto Profil" class="img-fluid rounded-circle mb-3" style="width: 200px; height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 200px; height: 200px; margin: 0 auto;">
                                    <i class="bi bi-person" style="font-size: 8rem; color: #6c757d;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div id="upload-message" class="mt-3"></div>

                            <div id="upload-options">
                                <button id="selfie-btn" class="btn btn-primary">
                                    <i class="bi bi-camera-video"></i> Ambil Foto Selfie
                                </button>
                                <button id="upload-file-btn" class="btn btn-secondary">
                                    <i class="bi bi-upload"></i> Upload dari File
                                </button>
                            </div>
                            <input type="file" id="file-input" accept="image/*" style="display: none;">
                            
                            <div id="camera-container" class="mt-3" style="display: none;">
                                <video id="video" width="100%" height="200" autoplay class="border rounded"></video>
                                <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
                                <div class="mt-2">
                                    <button id="capture-btn" class="btn btn-success">
                                        <i class="bi bi-camera"></i> Ambil Foto
                                    </button>
                                    <button id="cancel-btn" class="btn btn-secondary">
                                        <i class="bi bi-x"></i> Batal
                                    </button>
                                </div>
                            </div>
                            
                            <div id="preview-container" class="mt-3" style="display: none;">
                                <img id="preview" src="" alt="Preview" class="img-fluid rounded-circle mb-2" style="width: 200px; height: 200px; object-fit: cover;">
                                <div>
                                    <button id="save-btn" class="btn btn-success">
                                        <i class="bi bi-save"></i> Simpan Foto
                                    </button>
                                    <button id="retake-btn" class="btn btn-warning">
                                        <i class="bi bi-arrow-repeat"></i> Ulangi
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-card-text"></i> NIS
                                </label>
                                <input type="text" class="form-control" value="<?= $user['username'] ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-person"></i> Nama Lengkap
                                </label>
                                <input type="text" class="form-control" value="<?= $user['nama_lengkap'] ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-shield"></i> Role
                                </label>
                                <input type="text" class="form-control" value="<?= ucfirst($user['role']) ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-calendar"></i> Tanggal Daftar
                                </label>
                                <input type="text" class="form-control" value="<?= date('d M Y H:i:s', strtotime($user['created_at'])) ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script>
        // Elements
        const uploadOptions = document.getElementById('upload-options');
        const selfieBtn = document.getElementById('selfie-btn');
        const uploadFileBtn = document.getElementById('upload-file-btn');
        const fileInput = document.getElementById('file-input');
        const cameraContainer = document.getElementById('camera-container');
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureBtn = document.getElementById('capture-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const previewContainer = document.getElementById('preview-container');
        const preview = document.getElementById('preview');
        const saveBtn = document.getElementById('save-btn');
        const retakeBtn = document.getElementById('retake-btn');
        const uploadMessage = document.getElementById('upload-message');
        
        // Variables
        let stream = null;
        let capturedImageData = null;
        
        // Selfie button click
        selfieBtn.addEventListener('click', function() {
            uploadOptions.style.display = 'none';
            cameraContainer.style.display = 'block';
            startCamera();
        });

        // Upload file button click
        uploadFileBtn.addEventListener('click', function() {
            fileInput.click();
        });

        // File input change
        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    capturedImageData = e.target.result;
                    preview.src = capturedImageData;
                    uploadOptions.style.display = 'none';
                    previewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Cancel button click
        cancelBtn.addEventListener('click', function() {
            stopCamera();
            cameraContainer.style.display = 'none';
            uploadOptions.style.display = 'block';
        });
        
        // Retake button click
        retakeBtn.addEventListener('click', function() {
            previewContainer.style.display = 'none';
            capturedImageData = null;
            fileInput.value = ''; // Reset file input
            uploadOptions.style.display = 'block';
        });
        
        // Access camera
        function startCamera() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(s) {
                    stream = s;
                    video.srcObject = stream;
                })
                .catch(function(err) {
                    console.log("Error accessing camera: " + err);
                    uploadMessage.innerHTML = '<div class="alert alert-danger">Gagal mengakses kamera. Pastikan izin kamera telah diberikan.</div>';
                    cancelBtn.click();
                });
        }
        
        // Stop camera
        function stopCamera() {
            if (stream) {
                const tracks = stream.getTracks();
                tracks.forEach(track => track.stop());
                stream = null;
            }
        }
        
        // Capture photo
        captureBtn.addEventListener('click', function() {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Get image data
            capturedImageData = canvas.toDataURL('image/png');
            preview.src = capturedImageData;
            
            // Show preview and hide camera
            stopCamera();
            cameraContainer.style.display = 'none';
            previewContainer.style.display = 'block';
        });
        
        // Save photo
        saveBtn.addEventListener('click', function() {
            if (!capturedImageData) {
                uploadMessage.innerHTML = '<div class="alert alert-warning">Tidak ada foto untuk disimpan.</div>';
                return;
            }
            
            // Show loading
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            saveBtn.disabled = true;
            uploadMessage.innerHTML = '';

            // Send to server
            fetch('<?= base_url("siswa/upload-profile-photo") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'photo=' + encodeURIComponent(capturedImageData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    uploadMessage.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    setTimeout(() => location.reload(), 2000);
                } else {
                    uploadMessage.innerHTML = `<div class="alert alert-danger">Gagal menyimpan foto: ${data.message}</div>`;
                    saveBtn.innerHTML = '<i class="bi bi-save"></i> Simpan Foto';
                    saveBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                uploadMessage.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat menyimpan foto.</div>';
                saveBtn.innerHTML = '<i class="bi bi-save"></i> Simpan Foto';
                saveBtn.disabled = false;
            });
        });
    </script>
<?= $this->endSection() ?>