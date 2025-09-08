Konsep Aplikasi Izin Keluar Siswa Saat Jam KBM
Aplikasi ini merupakan modul lanjutan dari sistem presensi yang bertujuan untuk mendigitalisasi proses pengajuan izin keluar lingkungan sekolah oleh siswa saat jam Kegiatan Belajar Mengajar (KBM) sedang berlangsung. Sistem ini memastikan setiap izin melalui alur persetujuan berjenjang yang jelas dan tercatat secara digital.

1. Fitur Utama Aplikasi
Aplikasi akan memiliki beberapa role hak akses: Siswa, Guru (sebagai pemberi izin), dan Admin.

Fitur untuk role Siswa:
Halaman Utama: Menampilkan status izin yang sedang diajukan atau riwayat izin sebelumnya.

Formulir Pengajuan Izin:

Siswa mengisi formulir sederhana yang berisi kolom "Alasan Izin Keluar".

Tombol "Ajukan Izin" untuk mengirim permintaan.

Pelacakan Status Izin:

Siswa dapat memantau secara real-time status pengajuan izinnya.

Contoh status: "Menunggu Persetujuan Guru Kelas", "Menunggu Persetujuan Wali Kelas", "Disetujui", "Ditolak".

Notifikasi & Surat Izin Digital:

Menerima notifikasi ketika izin sudah disetujui sepenuhnya atau ditolak.

Jika disetujui, siswa mendapatkan "Surat Izin Digital" yang berisi nama, kelas, alasan, dan batas waktu kembali ke sekolah yang bisa ditunjukkan ke petugas keamanan.

Fitur untuk role Guru (Pemberi Izin):
Dashboard Persetujuan: Menampilkan daftar permintaan izin dari siswa yang memerlukan persetujuannya.

Detail Permintaan: Guru dapat melihat detail pengajuan, termasuk nama siswa dan alasan yang diberikan.

Aksi Persetujuan:

Tombol "Setujui" untuk meneruskan permintaan ke jenjang persetujuan berikutnya.

Tombol "Tolak" untuk membatalkan pengajuan izin. Guru bisa menambahkan catatan penolakan.

Fitur Khusus Guru Piket:

Sebagai jenjang terakhir, saat menyetujui, Guru Piket wajib mengisi kolom "Jam Kembali ke Sekolah" untuk siswa yang bersangkutan.

Fitur untuk role Admin:
Dashboard Monitoring: Melihat rekapitulasi semua aktivitas izin yang terjadi (diajukan, disetujui, ditolak).

Manajemen Penugasan Peran:

Admin memiliki halaman khusus untuk menunjuk atau mengganti nama-nama guru yang bertugas sebagai:

Guru Kelas

Wali Kelas

Wakil Kurikulum

Guru Piket

Fitur ini memastikan alur persetujuan selalu mengarah ke orang yang tepat.

Nama-nama diambil dari manajemen user

Laporan Izin:

Melihat dan mengunduh riwayat data izin siswa, yang dapat difilter berdasarkan tanggal, kelas, atau status.

2. Alur Kerja Aplikasi (Workflow)
Alur Pengajuan oleh Siswa:
Siswa login ke aplikasi.

Siswa membuka menu "Izin Keluar" dan mengisi alasan, mengisi jam pulang dan jam kembali, jika bersama teman bisa memilih nama siswa yang akan bersama keluar saat jam KBM, lalu klik "Ajukan".

Sistem secara otomatis mengirimkan notifikasi ke Admin

Lalu admin memberikan tugas kepada Guru kelas yang ditunjuk, sistem secara otomatis mengirimkan notifikasi ke Guru yang ditunjuk

Siswa memantau status persetujuan melalui aplikasi.

Jika pengajuan disetujui oleh semua pihak, siswa akan menerima notifikasi "Izin Disetujui" beserta jam wajib kembali.

Alur Persetujuan Berjenjang:
Permintaan masuk ke Admin, admin memberikan tugas kepada Guru kelas yang ditunjuk.

Permintaan masuk ke Guru Kelas yang ditunjuk. Jika disetujui, notifikasi lanjut ke Wali Kelas.

Wali Kelas menyetujui, notifikasi lanjut ke Wakil Kurikulum.

Wakil Kurikulum menyetujui, notifikasi lanjut ke Guru Piket.

Guru Piket sebagai penentu akhir, menyetujui permintaan dan menetapkan jam kembali. Status izin berubah menjadi "Disetujui".

Jika ada salah satu pihak yang menolak, alur langsung berhenti dan status izin berubah menjadi "Ditolak".

3. Skema Database (MySQL)
Database ini dapat diintegrasikan dengan database aplikasi presensi sebelumnya.