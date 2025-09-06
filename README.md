# Sistem Presensi Siswa SMK
## Berbasis GPS, Selfie, dan Face Recognition dengan CodeIgniter

Aplikasi ini dirancang untuk meningkatkan akurasi, kedisiplinan, dan meminimalisir kecurangan dalam proses presensi siswa. Dengan memanfaatkan teknologi GPS, pengambilan foto selfie, dan verifikasi wajah, sistem ini memastikan bahwa siswa melakukan presensi di lokasi yang benar dan oleh orang yang bersangkutan.

## Fitur Utama

### Untuk Siswa:
- Login & Autentikasi menggunakan NIS
- Validasi lokasi GPS berdasarkan koordinat sekolah
- Presensi dengan selfie menggunakan kamera depan
- Verifikasi wajah (Face Recognition)
- Riwayat presensi

### Untuk Admin:
- Dashboard monitoring presensi real-time
- Manajemen lokasi sekolah (koordinat & radius)
- Manajemen data siswa
- Monitoring & laporan presensi
- Ekspor laporan (Excel/PDF)

## Teknologi yang Digunakan

- **Backend**: CodeIgniter 4 (PHP)
- **Database**: MySQL
- **Frontend**: Bootstrap 5, JavaScript (ES6)
- **APIs**: HTML5 Geolocation API, MediaDevices API
- **Face Recognition**: face_recognition Python library (opsional)

## Instalasi

### Prasyarat
- PHP 8.1 atau lebih tinggi
- MySQL
- Composer
- Python 3.6+ (untuk face recognition)
- Node.js & npm (opsional, untuk development)

### Langkah Instalasi

1. Clone repository:
   ```bash
   git clone <repository-url>
   cd simantapadb
   ```

2. Install dependensi PHP:
   ```bash
   composer install
   ```

3. Konfigurasi database:
   - Copy `.env.example` ke `.env`
   - Sesuaikan konfigurasi database di `.env`

4. Jalankan migrasi database:
   ```bash
   php spark migrate
   ```

5. Jalankan seeder untuk data awal:
   ```bash
   php spark db:seed UserSeeder
   php spark db:seed SettingSeeder
   ```

6. Untuk face recognition (opsional):
   - Install Python dependencies:
     ```bash
     pip install -r requirements.txt
     ```
   - Pastikan Python bisa diakses dari command line

7. Jalankan aplikasi:
   ```bash
   php spark serve
   ```

## Penggunaan

### Login Awal
- **Admin**: username: `admin`, password: `admin123`
- **Siswa**: NIS: `12345`, password: `12345`

### Fitur Face Recognition

Aplikasi menggunakan library `face_recognition` Python untuk verifikasi wajah. Untuk mengaktifkan fitur ini:

1. Pastikan Python dan pip terinstal
2. Install dependencies:
   ```bash
   pip install -r requirements.txt
   ```
3. Sistem akan secara otomatis menggunakan face recognition saat presensi

### Konfigurasi Lokasi Sekolah

1. Login sebagai admin
2. Buka menu "Pengaturan Lokasi"
3. Atur koordinat sekolah dan radius toleransi
4. Gunakan peta interaktif atau tombol "Gunakan Lokasi Saya"

## Struktur Database

### Tabel `users`
Menyimpan data pengguna (siswa & admin)

### Tabel `absensi`
Mencatat transaksi presensi

### Tabel `pengaturan`
Menyimpan konfigurasi lokasi sekolah

## Pengembangan Lanjutan

### Menambahkan Fitur Baru
1. Buat controller baru: `php spark make:controller NamaController`
2. Buat model: `php spark make:model NamaModel`
3. Buat migration: `php spark make:migration NamaMigration`
4. Tambahkan route di `app/Config/Routes.php`

### Custom Face Recognition
Implementasi face recognition saat ini menggunakan simulasi. Untuk mengganti dengan implementasi nyata:

1. Buka `app/Controllers/Siswa.php`
2. Temukan fungsi `verifyFace()`
3. Ikuti contoh implementasi yang diberikan dalam komentar

## Troubleshooting

### Error Koneksi Database
- Pastikan MySQL berjalan
- Periksa konfigurasi database di `.env`

### Error Face Recognition
- Pastikan Python terinstal
- Periksa path ke file `face_compare.py`
- Pastikan library `face_recognition` terinstal

### Error Lokasi GPS
- Pastikan browser mengizinkan akses lokasi
- Periksa pengaturan lokasi di perangkat

## Lisensi

Aplikasi ini dibuat untuk keperluan pendidikan dan dapat dikembangkan lebih lanjut sesuai kebutuhan.

## Kontribusi

Kontribusi dalam bentuk issue reporting dan pull requests sangat diterima.