Konsep Aplikasi Presensi Siswa SMK
Berbasis GPS, Selfie, dan Face Recognition dengan CodeIgniter
Aplikasi ini dirancang untuk meningkatkan akurasi, kedisiplinan, dan meminimalisir kecurangan dalam proses presensi siswa. Dengan memanfaatkan teknologi GPS, pengambilan foto selfie, dan verifikasi wajah, sistem ini memastikan bahwa siswa melakukan presensi di lokasi yang benar dan oleh orang yang bersangkutan.

1. Fitur Utama Aplikasi
Aplikasi akan memiliki dua hak akses utama: Siswa dan Admin (bisa guru atau staf TU).

Fitur untuk Siswa:
Login & Autentikasi: Siswa login menggunakan NIS (Nomor Induk Siswa) dan password yang telah didaftarkan.

Validasi Lokasi GPS:

Aplikasi secara otomatis mendeteksi koordinat GPS siswa saat ini.

Sistem akan membandingkan lokasi siswa dengan titik koordinat sekolah yang telah diatur oleh Admin.

Memberikan notifikasi apakah siswa berada di dalam atau di luar radius area sekolah yang diizinkan.

Presensi dengan Selfie:

Tombol "Presensi Masuk/Pulang" hanya aktif jika siswa berada dalam radius lokasi yang valid.

Saat tombol ditekan, kamera depan akan aktif untuk mengambil foto selfie.

Verifikasi Wajah (Face Recognition):

Setelah selfie diambil, sistem akan membandingkan wajah pada foto selfie dengan foto profil siswa yang tersimpan di database.

Jika wajah cocok, presensi dianggap berhasil. Jika tidak, presensi akan ditolak.

Riwayat Presensi: Siswa dapat melihat rekapitulasi dan detail riwayat kehadiran mereka sendiri (jam masuk, jam pulang, dan foto).

Fitur untuk Admin:
Dashboard Admin: Menampilkan ringkasan data presensi hari ini secara real-time.

Manajemen Lokasi Sekolah:

Fitur untuk mengatur titik koordinat utama sekolah (Latitude & Longitude).

Mengatur radius toleransi (misalnya, 50 meter dari titik utama) sebagai area presensi yang sah.

Manajemen Data Siswa:

Menambah, mengubah, dan menghapus data siswa.

Mengunggah foto profil utama siswa yang akan dijadikan acuan untuk proses face recognition.

Monitoring & Laporan Presensi:

Melihat daftar presensi seluruh siswa secara detail (lengkap dengan waktu, status, dan foto selfie).

Memfilter data presensi berdasarkan kelas, tanggal, atau nama siswa.

Mengekspor laporan presensi (misalnya ke format Excel atau PDF).

2. Alur Kerja Aplikasi (Workflow)
Alur Siswa:
Siswa membuka aplikasi dan mengaktifkan GPS di ponsel.

Siswa melakukan Login.

Di halaman utama, aplikasi menampilkan status lokasi: "Anda berada di area sekolah" atau "Anda di luar area sekolah".

Jika lokasi sesuai, tombol "Lakukan Presensi" akan aktif.

Siswa menekan tombol tersebut, dan kamera depan terbuka.

Siswa mengambil foto Selfie.

Aplikasi mengirim data (ID siswa, waktu, koordinat, dan foto selfie) ke server.

Server melakukan Face Recognition: membandingkan selfie dengan foto di database.

Jika cocok: Presensi dicatat sebagai "Berhasil".

Jika tidak cocok: Aplikasi menampilkan pesan "Wajah tidak dikenali, coba lagi.".

Alur Admin (via Web):
Admin membuka halaman web aplikasi dan melakukan Login.

Masuk ke menu "Pengaturan Lokasi".

Menentukan titik koordinat sekolah di peta atau dengan menginput Latitude/Longitude secara manual.

Menyimpan pengaturan radius.

Admin dapat memantau data presensi yang masuk melalui menu "Laporan Presensi".

3. Skema Database (MySQL)
Berikut adalah desain database sederhana untuk menunjang aplikasi ini.

Tabel 1: users
Menyimpan data pengguna, baik siswa maupun admin.

Nama Kolom, Tipe Data, Keterangan

id, INT (PK), ID Unik Pengguna (Auto Increment)

username, VARCHAR(100), NIS untuk siswa, username untuk admin (Unik)

password, VARCHAR(255), Password yang sudah di-hash

nama_lengkap, VARCHAR(150), Nama lengkap pengguna

role, ENUM('admin', 'siswa'), Peran pengguna

foto_profil, VARCHAR(255), Path/URL ke foto acuan untuk face recognition

created_at, TIMESTAMP, Waktu pembuatan akun

<br>

Tabel 2: absensi
Mencatat setiap transaksi presensi yang dilakukan siswa.

Nama Kolom, Tipe Data, Keterangan

id, INT (PK), ID Unik Presensi (Auto Increment)

user_id, INT (FK), Merujuk ke users.id

waktu_presensi, DATETIME, Waktu saat siswa melakukan presensi

tipe_presensi, ENUM('masuk', 'pulang'), Jenis presensi (masuk atau pulang)

latitude, DECIMAL(10, 8), Koordinat Latitude saat presensi

longitude, DECIMAL(11, 8), Koordinat Longitude saat presensi

foto_selfie, VARCHAR(255), Path/URL ke foto selfie saat presensi

<br>

Tabel 3: pengaturan
Menyimpan konfigurasi aplikasi, terutama lokasi sekolah.

Nama Kolom, Tipe Data, Keterangan

id, INT (PK), Cukup 1 baris data saja untuk pengaturan

lokasi_latitude, DECIMAL(10, 8), Titik Latitude utama sekolah

lokasi_longitude, DECIMAL(11, 8), Titik Longitude utama sekolah

radius_meter, INT, Jarak toleransi dalam satuan meter

4. Rekomendasi Teknis & Desain (WebView & Web)
Untuk memastikan pengalaman pengguna yang baik di WebView Android dan browser web, pendekatan berikut sangat disarankan:

Desain Responsif (Mobile-First):

Prioritaskan desain antarmuka (UI) untuk layar ponsel. Gunakan CSS Framework seperti Bootstrap atau Tailwind CSS untuk memudahkan pembuatan layout yang responsif.

Pastikan semua elemen (tombol, teks, gambar) dapat menyesuaikan ukurannya secara otomatis dari layar kecil (ponsel) hingga besar (desktop).

Antarmuka yang Sederhana dan Jelas:

Tombol Besar: Buat tombol, terutama tombol presensi, cukup besar dan mudah ditekan (tappable) di layar sentuh.

Navigasi Minimalis: Untuk tampilan siswa, gunakan navigasi sederhana seperti menu bottom bar (bar navigasi di bawah) yang menampilkan menu utama seperti "Home" dan "Riwayat".

Loading Cepat: Optimalkan ukuran gambar dan aset lainnya agar aplikasi terasa ringan dan cepat dimuat, yang sangat penting untuk pengalaman di WebView.

Akses Fitur Perangkat Keras (GPS & Kamera):

Aplikasi web modern di browser (seperti Chrome di Android) sudah dapat meminta akses GPS dan kamera secara langsung menggunakan HTML5 Geolocation API dan MediaDevices API (getUserMedia).

Ini menyederhanakan proses karena Anda tidak memerlukan "jembatan" (JavaScript Bridge) yang kompleks antara web dan kode native Android. Cukup pastikan WebView yang digunakan di aplikasi Android Anda memiliki izin untuk mengakses fitur-fitur ini.

Feedback yang Jelas untuk Pengguna:

Saat aplikasi memproses data (misalnya, memverifikasi lokasi atau wajah), tampilkan indikator loading (seperti spinner).

Gunakan notifikasi atau toast message yang non-intrusif untuk memberitahu hasil tindakan, seperti "Presensi Berhasil" atau "Lokasi Anda di Luar Jangkauan".