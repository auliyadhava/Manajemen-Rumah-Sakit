<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setAutoRoute(false);

$routes->get('/', 'Auth::index');

$routes->post('/login/auth', 'Auth::loginProcess');
$routes->get('/logout', 'Auth::logout');

$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('users', 'Admin\Users::index');
    $routes->get('users/create', 'Admin\Users::create');
    $routes->post('users/store', 'Admin\Users::store');
    $routes->get('users/edit/(:num)', 'Admin\Users::edit/$1');
    $routes->post('users/update/(:num)', 'Admin\Users::update/$1');
    $routes->get('users/delete/(:num)', 'Admin\Users::delete/$1');
});

$routes->group('apoteker', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Apoteker::index');
    $routes->get('detail/(:num)', 'Apoteker::detail/$1');
    $routes->post('pickup/(:num)', 'Apoteker::pickup/$1');
});

$routes->group('kasir', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Kasir::index');
    $routes->get('detail/(:num)', 'Kasir::detail/$1');
    $routes->post('bayar/(:num)', 'Kasir::bayar/$1');
});


$routes->get('/pendaftaran', 'Pendaftaran::index');
$routes->get('/pendaftaran/pasien', 'Pendaftaran::pasien');
$routes->get('/pendaftaran/antrian', 'Pendaftaran::antrian');

