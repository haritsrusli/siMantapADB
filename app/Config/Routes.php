<?php

use CodeIgniter\Router\RouteCollection;

// Izin Keluar Admin Assignment routes
$routes->group('admin', static function ($routes) {
    $routes->get('izin-keluar/(:num)/assign', 'IzinKeluarController::assignForm/$1');
    $routes->post('izin-keluar/(:num)/assign', 'IzinKeluarController::assignAction/$1');
    $routes->get('izin-keluar/reset/(:num)', 'IzinKeluarController::reset/$1');
    // Route for penugasan from assign form
    $routes->post('izin-keluar/(:num)/assign-penugasan', 'IzinKeluarController::assignPenugasan/$1');
    $routes->get('izin-keluar/(:num)/unassign-penugasan/(:num)', 'IzinKeluarController::unassignPenugasan/$1/$2');
});

// Izin Keluar Penugasan routes
$routes->group('admin', static function ($routes) {
    $routes->get('penugasan-izin', 'IzinKeluarPenugasanController::index');
    $routes->post('penugasan-izin/assign', 'IzinKeluarPenugasanController::assign');
    $routes->get('penugasan-izin/unassign/(:num)', 'IzinKeluarPenugasanController::unassign/$1');
});

// Izin Keluar routes
$routes->get('izin-keluar/remove/(:num)', 'IzinKeluarController::delete/$1');
$routes->resource('izin-keluar', ['controller' => 'IzinKeluarController']);

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::index');

// Auth routes
$routes->get('/auth', 'Auth::index');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/logout', 'Auth::logout');

// Siswa routes
$routes->get('/siswa/dashboard', 'Siswa::dashboard');
$routes->get('/siswa/presensi', 'Siswa::presensi');
$routes->post('/siswa/do-presensi', 'Siswa::doPresensi');
$routes->post('/siswa/check-location', 'Siswa::checkLocation');
$routes->get('/siswa/riwayat', 'Siswa::riwayat');
$routes->get('/siswa/profile', 'Siswa::profile');
$routes->post('/siswa/upload-profile-photo', 'Siswa::uploadProfilePhoto');

// Admin routes
$routes->get('/admin/dashboard', 'Admin::dashboard');
$routes->get('/admin/pengaturan-presensi', 'Admin::pengaturanPresensi');
$routes->post('/admin/simpan-pengaturan-presensi', 'Admin::simpanPengaturanPresensi');
$routes->post('/admin/simpan-jam-presensi', 'Admin::simpanJamPresensi');

// Admin - Manajemen User
$routes->get('/admin/user', 'UserController::index');
$routes->get('/admin/user/tambah', 'UserController::tambah');
$routes->post('/admin/user/simpan', 'UserController::simpan');
$routes->get('/admin/user/edit/(:num)', 'UserController::edit/$1');
$routes->post('/admin/user/update/(:num)', 'UserController::update/$1');
$routes->get('/admin/user/hapus/(:num)', 'UserController::hapus/$1');

// Admin - Manajemen Libur Nasional
$routes->get('/admin/libur-nasional', 'LiburNasionalController::index');
$routes->get('/admin/libur-nasional/tambah', 'LiburNasionalController::tambah');
$routes->post('/admin/libur-nasional/simpan', 'LiburNasionalController::simpan');
$routes->get('/admin/libur-nasional/edit/(:num)', 'LiburNasionalController::edit/$1');
$routes->post('/admin/libur-nasional/update/(:num)', 'LiburNasionalController::update/$1');
$routes->get('/admin/libur-nasional/hapus/(:num)', 'LiburNasionalController::hapus/$1');

// Admin - Manajemen Kelas
$routes->get('/admin/kelas', 'Admin::kelas');
$routes->get('/admin/kelas/tambah', 'Admin::tambahKelas');
$routes->post('/admin/kelas/simpan', 'Admin::simpanKelas');
$routes->get('/admin/kelas/edit/(:num)', 'Admin::editKelas/$1');
$routes->post('/admin/kelas/update/(:num)', 'Admin::updateKelas/$1');
$routes->get('/admin/kelas/hapus/(:num)', 'Admin::hapusKelas/$1');

// Admin - Manajemen User Roles
$routes->get('/admin/user-roles', 'Admin\UserRoleController::index');
$routes->get('/admin/user-roles/edit/(:num)', 'Admin\UserRoleController::edit/$1');
$routes->post('/admin/user-roles/update/(:num)', 'Admin\UserRoleController::update/$1');

// Admin - Rekap Harian
$routes->get('/admin/rekap-harian', 'Admin::rekapHarian');

// Admin - Input Presensi Harian
$routes->get('/admin/input-presensi-harian', 'Admin::inputPresensiHarian');
$routes->post('/admin/simpan-absensi-manual', 'Admin::simpanAbsensiManual');
$routes->post('/admin/simpan-absensi-manual-massal', 'Admin::simpanAbsensiManualMassal');
$routes->get('/admin/hapus-absensi-manual/(:num)', 'Admin::hapusAbsensiManual/$1');
$routes->get('/admin/edit-absensi-manual/(:num)', 'Admin::editAbsensiManual/$1');
$routes->post('/admin/update-absensi-manual/(:num)', 'Admin::updateAbsensiManual/$1');

// Admin - Lock/Unlock Lokasi
$routes->post('/admin/lock-lokasi', 'Admin::lockLokasi');
$routes->post('/admin/unlock-lokasi', 'Admin::unlockLokasi');

// Presensi Harian routes
$routes->get('/presensi-harian', 'PresensiHarian::index');
$routes->post('/presensi-harian/simpan', 'PresensiHarian::simpan');
$routes->post('/presensi-harian/simpan-massal', 'PresensiHarian::simpanMassal');
$routes->get('/presensi-harian/hapus/(:num)', 'PresensiHarian::hapus/$1');
$routes->get('/presensi-harian/edit/(:num)', 'PresensiHarian::edit/$1');
$routes->post('/presensi-harian/update/(:num)', 'PresensiHarian::update/$1');

// Laporan routes
$routes->get('/laporan', 'LaporanController::index');
$routes->get('/laporan/rekap-harian', 'LaporanController::rekapHarian');





