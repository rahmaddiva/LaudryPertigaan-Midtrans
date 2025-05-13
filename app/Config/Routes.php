<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
// routes dashboard
$routes->get('/dashboard', 'DashboardController::index');


// routes pelanggan
$routes->get('/pelanggan', 'PelangganController::index');
$routes->post('/proses_pelanggan', 'PelangganController::proses');
$routes->post('/update_pelanggan/(:any)', 'PelangganController::update/$1');
$routes->get('/delete_pelanggan/(:any)', 'PelangganController::delete/$1');

// routes layanan laundry
$routes->get('/layanan_laundry', 'LayananLaundryController::index');
$routes->post('/proses_layanan_laundry', 'LayananLaundryController::proses');
$routes->post('/update_layanan_laundry/(:any)', 'LayananLaundryController::update/$1');
$routes->get('/delete_layanan_laundry/(:any)', 'LayananLaundryController::delete/$1');

// routes Transaksi
$routes->get('/transaksi', 'TransaksiController::index');
$routes->post('/proses_transaksi', 'TransaksiController::proses');
$routes->post('update_transaksi/(:any)', 'TransaksiController::update/$1');
$routes->get('/transaksi/bayar/(:num)', 'TransaksiController::bayar/$1');
$routes->post('/midtrans/callback', 'TransaksiController::callback');
$routes->get('/transaksi/sukses', 'TransaksiController::sukses');
$routes->get('/delete_transaksi/(:any)', 'TransaksiController::delete/$1');


