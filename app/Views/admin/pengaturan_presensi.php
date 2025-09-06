<?= $this->extend('admin/template') ?>

<?= $this->section('content') ?>

    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="bi bi-geo-alt"></i> Pengaturan Presensi
            </h2>
            <p class="lead">Atur lokasi utama sekolah, radius toleransi, dan jam presensi.</p>
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
            
            <?php if(!isset($pengaturan)): ?>
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle"></i> Menggunakan pengaturan default. Silakan sesuaikan pengaturan sesuai kebutuhan sekolah Anda.
                </div>
            <?php endif; ?>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
            
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-geo"></i> Pengaturan Lokasi & Radius
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="<?= base_url('admin/simpan-pengaturan-presensi') ?>" method="post">
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">
                                        <i class="bi bi-geo-alt"></i> Latitude
                                    </label>
                                    <input type="text" class="form-control" id="latitude" name="latitude" 
                                           value="<?= isset($pengaturan) ? $pengaturan['lokasi_latitude'] : '' ?>" required>
                                    <div class="form-text">Contoh: -6.2088 (antara -90 dan 90)</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">
                                        <i class="bi bi-geo-alt"></i> Longitude
                                    </label>
                                    <input type="text" class="form-control" id="longitude" name="longitude" 
                                           value="<?= isset($pengaturan) ? $pengaturan['lokasi_longitude'] : '' ?>" required>
                                    <div class="form-text">Contoh: 106.8456 (antara -180 dan 180)</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="radius" class="form-label">
                                        <i class="bi bi-rulers"></i> Radius Toleransi (meter)
                                    </label>
                                    <input type="number" class="form-control" id="radius" name="radius" min="1" max="1000" 
                                           value="<?= isset($pengaturan) ? $pengaturan['radius_meter'] : '50' ?>" required>
                                    <div class="form-text">Radius maksimal 1000 meter</div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-info" id="getCurrentLocation">
                                        <i class="bi bi-compass"></i> Gunakan Lokasi Saat Ini
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Simpan Pengaturan
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="col-md-6">
                            <div id="map" style="height: 400px; border: 1px solid #ddd; border-radius: 5px;"></div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i> Klik pada peta untuk mengatur lokasi sekolah
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history"></i> Jam Presensi Harian
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/simpan-jam-presensi') ?>" method="post">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Hari</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Pulang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $hariIndonesia = [
                                        'senin' => 'Senin',
                                        'selasa' => 'Selasa', 
                                        'rabu' => 'Rabu',
                                        'kamis' => 'Kamis',
                                        'jumat' => 'Jumat',
                                        'sabtu' => 'Sabtu',
                                        'minggu' => 'Minggu'
                                    ];
                                    
                                    foreach($hariIndonesia as $hari_en => $hari_id): 
                                        $jam_masuk_field = 'jam_masuk_' . $hari_en;
                                        $jam_pulang_field = 'jam_pulang_' . $hari_en;
                                    ?>
                                        <tr>
                                            <td><?= $hari_id ?></td>
                                            <td>
                                                <input type="time" class="form-control" name="<?= $jam_masuk_field ?>" 
                                                       value="<?= isset($pengaturan) && !empty($pengaturan[$jam_masuk_field]) ? $pengaturan[$jam_masuk_field] : '' ?>">
                                            </td>
                                            <td>
                                                <input type="time" class="form-control" name="<?= $jam_pulang_field ?>" 
                                                       value="<?= isset($pengaturan) && !empty($pengaturan[$jam_pulang_field]) ? $pengaturan[$jam_pulang_field] : '' ?>">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Jam Presensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Inisialisasi peta
    var map = L.map('map').setView([<?= isset($pengaturan) ? $pengaturan['lokasi_latitude'] : '-6.2088' ?>, <?= isset($pengaturan) ? $pengaturan['lokasi_longitude'] : '106.8456' ?>], 15);
    
    // Tambahkan tile layer OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Marker untuk lokasi sekolah
    var schoolMarker = L.marker([<?= isset($pengaturan) ? $pengaturan['lokasi_latitude'] : '-6.2088' ?>, <?= isset($pengaturan) ? $pengaturan['lokasi_longitude'] : '106.8456' ?>], {
        draggable: true,
        title: 'Lokasi Sekolah'
    }).addTo(map)
      .bindPopup('Lokasi Sekolah<br>Klik dan geser untuk memindahkan')
      .openPopup();
    
    // Event listener untuk marker drag
    schoolMarker.on('dragend', function(event) {
        var marker = event.target;
        var position = marker.getLatLng();
        document.getElementById('latitude').value = position.lat.toFixed(8);
        document.getElementById('longitude').value = position.lng.toFixed(8);
    });
    
    // Event listener untuk klik pada peta
    map.on('click', function(event) {
        var lat = event.latlng.lat.toFixed(8);
        var lng = event.latlng.lng.toFixed(8);
        
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        
        schoolMarker.setLatLng([lat, lng]);
    });
    
    // Tombol untuk mendapatkan lokasi saat ini
    document.getElementById('getCurrentLocation').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude.toFixed(8);
                var lng = position.coords.longitude.toFixed(8);
                
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                
                schoolMarker.setLatLng([lat, lng]);
                map.setView([lat, lng], 15);
                
                alert('Lokasi berhasil diperbarui');
            }, function(error) {
                alert('Gagal mendapatkan lokasi saat ini: ' + error.message);
            });
        } else {
            alert('Geolocation tidak didukung oleh browser ini.');
        }
    });
</script>
<?= $this->endSection() ?>