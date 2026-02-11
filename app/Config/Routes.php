<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setAutoRoute(false);

$routes->get('/', 'Auth::index');

$routes->post('/login/auth', 'Auth::loginProcess');
$routes->get('/logout', 'Auth::logout');

$routes->group('admin', ['filter' => 'auth'], function ($routes) {
    $routes->get('users', 'Admin\Users::index');
    $routes->get('users/create', 'Admin\Users::create');
    $routes->post('users/store', 'Admin\Users::store');
    $routes->get('users/edit/(:num)', 'Admin\Users::edit/$1');
    $routes->post('users/update/(:num)', 'Admin\Users::update/$1');
    $routes->get('users/delete/(:num)', 'Admin\Users::delete/$1');
});

$routes->group('apoteker', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Apoteker\Apoteker::index');
    $routes->get('detail/(:num)', 'Apoteker\Apoteker::detail/$1');
    $routes->post('pickup/(:num)', 'Apoteker\Apoteker::pickup/$1');
});

$routes->group('kasir', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Cashier\Kasir::index');
    $routes->get('detail/(:num)', 'Cashier\Kasir::detail/$1');
    $routes->post('bayar/(:num)', 'Cashier\Kasir::bayar/$1');
    $routes->post('prosesBayar', 'Cashier\Kasir::prosesBayar');
});

$routes->group('dokter', ['filter' => 'auth'], function ($routes) {
    $routes->get('dashboard', 'Doctor\DoctorController::index');
    $routes->get('/', 'Doctor\DoctorController::index');
    $routes->get('examine/(:num)', 'Doctor\DoctorController::examine/$1');
    $routes->post('submitExamination', 'Doctor\DoctorController::submitExamination');
});

$routes->group('pasien', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Pasien\Pasien::index');
    $routes->get('booking', 'Pasien\Pasien::booking');
    $routes->post('booking/store', 'Pasien\Pasien::store');
    $routes->get('riwayat', 'Pasien\Pasien::riwayat');
    $routes->get('antrian', 'Pasien\Pasien::antrian');
    $routes->get(
        'detail-pemeriksaan/(:num)',
        'Pasien\Pasien::detail_pemeriksaan/$1'
    );
});


$routes->get('register', 'Pasien\Register::index');

$routes->group('pendaftaran', function ($routes) {
    $routes->get('/', 'Pendaftaran\Pendaftaran::index');
    $routes->get('pasien', 'Pendaftaran\Pendaftaran::pasien');
    $routes->get('antrian', 'Pendaftaran\Pendaftaran::antrian');
    $routes->get('konfirmasi/(:num)', 'Pendaftaran\Pendaftaran::konfirmasi/$1');
});
