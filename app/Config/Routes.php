<?php

use CodeIgniter\Router\RouteCollection;

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




