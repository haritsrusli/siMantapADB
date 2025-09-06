# Dokumentasi Multiple Roles

## Gambaran Umum
Sistem ini mendukung multiple roles untuk setiap user, memungkinkan satu user memiliki lebih dari satu peran dalam sistem. Misalnya, seorang guru dapat memiliki role sebagai walikelas dan guru piket sekaligus.

## Struktur Database
1. **Tabel `users`** - Menyimpan informasi user utama dengan role utama
2. **Tabel `user_roles`** - Tabel pivot untuk menyimpan multiple roles per user

## Role yang Tersedia
- `admin` - Administrator sistem
- `siswa` - Siswa
- `guru` - Guru
- `wali_kelas` - Wali kelas
- `guru_piket` - Guru piket

## Cara Menggunakan

### 1. Menambahkan Role Baru
Untuk menambahkan role baru:
1. Tambahkan role baru ke constraint ENUM di tabel `users` dan `user_roles`
2. Tambahkan role baru ke validasi di model `User` dan `UserRole`
3. Tambahkan role baru ke array `$availableRoles` di controller `UserRoleController`

### 2. Mengelola User Roles
Admin dapat mengelola roles untuk setiap user melalui menu "User Roles" di admin panel:
1. Buka menu "User Roles"
2. Klik tombol "Edit Roles" untuk user yang ingin diubah
3. Centang roles tambahan yang diinginkan
4. Klik "Simpan Roles"

### 3. Mengakses Roles dalam Aplikasi
Untuk memeriksa apakah user memiliki role tertentu:
```php
// Mendapatkan semua roles untuk user
$userModel = new \App\Models\User();
$allRoles = $userModel->getAllUserRoles($userId);

// Mengecek apakah user memiliki role tertentu
$hasRole = $userModel->userHasRole($userId, 'guru_piket');
```

## Contoh Penggunaan
Contoh user dengan multiple roles:
- Username: `guruA`
- Role utama: `guru`
- Roles tambahan: `wali_kelas`, `guru_piket`

User ini akan dapat mengakses fitur yang tersedia untuk ketiga role tersebut.

## Implementasi Teknis
1. **Model**: Model `User` dan `UserRole` telah diperbarui untuk mendukung multiple roles
2. **Controller**: Controller `UserRoleController` untuk mengelola roles
3. **View**: View untuk manajemen user roles di admin panel
4. **Migrasi**: Migrasi database untuk membuat tabel `user_roles`
5. **Routing**: Route baru untuk mengakses fitur manajemen user roles

## Pengujian
Setelah menjalankan migrasi, Anda dapat:
1. Membuat user baru dengan role `guru`
2. Menambahkan roles tambahan `wali_kelas` dan `guru_piket` melalui admin panel
3. Memverifikasi bahwa user dapat mengakses fitur sesuai dengan roles yang dimiliki