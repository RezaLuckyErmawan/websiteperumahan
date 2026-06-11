<?php

use App\Controllers\UserController;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');

$routes->get('/dashboard', 'DashboardController::index');
$routes->get('/', 'DashboardController::landing');


$routes->get('/data-rumah', 'DatarumahController::datarumah');
$routes->get('/form-tambahdata-rumah', 'DatarumahController::create');
$routes->post('/data-rumah/store', 'DatarumahController::store');
$routes->get('/data-rumah/edit/(:num)', 'DatarumahController::edit/$1');
$routes->post('/data-rumah/update/(:num)', 'DatarumahController::update/$1'); 
$routes->delete('/data-rumah/delete/(:num)', 'DatarumahController::delete/$1');
$routes->get('/data-rumah/json', 'DatarumahController::json');


$routes->get('/data-bahan', 'DatabahanController::databahan');
$routes->get('/data-bahan/json', 'DatabahanController::json');
$routes->get('/data-bahan/edit/(:num)', 'DatabahanController::getBahan/$1');
$routes->post('/data-bahan/store', 'DatabahanController::store');
$routes->post('/data-bahan/update/(:num)', 'DatabahanController::update/$1');
$routes->delete('/data-bahan/delete/(:num)', 'DatabahanController::delete/$1');



$routes->get('/data-pembelian-bahan', 'PembelianbahanController::datapembelian');
$routes->get('/data-pembelian-bahan/json', 'PembelianbahanController::json');
$routes->get('/data-pembelian-bahan/edit/(:num)', 'PembelianbahanController::edit/$1');
$routes->post('/data-pembelian-bahan/store', 'PembelianbahanController::store');
$routes->post('/data-pembelian-bahan/update/(:num)', 'PembelianbahanController::update/$1');
$routes->delete('/data-pembelian-bahan/delete/(:num)', 'PembelianbahanController::delete/$1');



$routes->get('/detail-pembelian-bahan', 'DetailPembelianbahanController::detailpembelian');
$routes->get('/detail-pembelian/json', 'DetailPembelianbahanController::json');
$routes->get('/form-tambahdata-detailpembelian', 'DetailPembelianbahanController::create');
$routes->post('/detail-pembelian/store', 'DetailPembelianbahanController::store');
$routes->get('/detail-pembelian/edit/(:num)', 'DetailPembelianbahanController::edit/$1');
$routes->post('/detail-pembelian/update/(:num)', 'DetailPembelianbahanController::update/$1');
$routes->get('/detail-pembelian-bahan/delete/(:num)', 'DetailPembelianbahanController::delete/$1');


$routes->get('/data-bahan-pembangunan', 'BahanPembangunanRumahController::bahanpembangunanrumah');
$routes->get('/data-bahan-pembangunan/json', 'BahanPembangunanRumahController::json');
$routes->get('/bahan-pembangunan/rumah/(:num)', 'BahanPembangunanRumahController::show/$1');
$routes->get('/form-tambahdata-bahanpembangunanrumah', 'BahanPembangunanRumahController::create');
$routes->post('/bahan-pembangunan/store', 'BahanPembangunanRumahController::store');
$routes->get('/bahan-pembangunan/edit/(:num)', 'BahanPembangunanRumahController::edit/$1');
$routes->post('/bahan-pembangunan/update/(:num)', 'BahanPembangunanRumahController::update/$1');
$routes->get('/bahan-pembangunan/rumah-detail/(:num)', 'BahanPembangunanRumahController::lihatBahan/$1');

$routes->get('/bahan-pembangunan/rumah/delete/(:num)', 'BahanPembangunanRumahController::delete/$1');


$routes->get('/data-customer', 'CustomerController::customer');
$routes->get('/data-customer/json', 'CustomerController::json');
$routes->get('/customer/create', 'CustomerController::create');
$routes->post('/customer/store', 'CustomerController::store');
$routes->get('/customer/edit/(:num)', 'CustomerController::edit/$1');
$routes->post('/customer/update/(:num)', 'CustomerController::update/$1');
$routes->get('/customer/delete/(:num)', 'CustomerController::delete/$1');
// $routes->get('/data-customer', 'DashboardController::datacustomer');

$routes->get('/pembelian-rumah', 'PembelianRumahController::pembelianrumah');
$routes->get('/pembelian-rumah/json', 'PembelianRumahController::json');
$routes->post('/pembelian-rumah/store', 'PembelianRumahController::store');
$routes->get('/pembelian-rumah/edit/(:num)', 'PembelianRumahController::edit/$1');
$routes->post('/pembelian-rumah/update/(:num)', 'PembelianRumahController::update/$1');
$routes->delete('pembelian-rumah/delete/(:num)', 'PembelianRumahController::delete/$1');

$routes->get('/chart-penjualan-rumah', 'PembelianRumahController::chartPenjualanRumah');
$routes->get('/chart-penjualan-rumah', 'PembelianRumahController::chartPenjualanRumah');

$routes->get('/pembayaran-rumah', 'PembayaranRumahController::pembayaranrumah');
$routes->get('/pembayaran-rumah/json', 'PembayaranRumahController::json');
$routes->get('/pembayaran-rumah/ringkasan/(:num)', 'PembayaranRumahController::ringkasan/$1');
$routes->get('/pembayaran-rumah/edit/(:num)', 'PembayaranRumahController::edit/$1');
$routes->post('/pembayaran-rumah/store', 'PembayaranRumahController::store');
$routes->post('/pembayaran-rumah/update/(:num)', 'PembayaranRumahController::update/$1');
$routes->post('/pembayaran-rumah/approve/(:num)', 'PembayaranRumahController::approve/$1');
$routes->delete('/pembayaran-rumah/delete/(:num)', 'PembayaranRumahController::delete/$1');

$routes->get('/progres-pembayaran-rumah', 'ProgresPembayaranRumahController::index');
$routes->get('/progres-pembayaran-rumah/json', 'ProgresPembayaranRumahController::json');


$routes->get('/login', 'LoginController::loginform');
$routes->post('/login/auth', 'LoginController::login');
$routes->get('/logout', 'LoginController::logout');

$routes->get('/data-user', 'UserController::user');
$routes->get('/data-user/json', 'UserController::json');
$routes->post('/user/store', 'UserController::store');
$routes->get('/user/edit/(:num)', 'UserController::edit/$1');
$routes->post('/user/update/(:num)', 'UserController::update/$1');
$routes->delete('/user/delete/(:num)', 'UserController::delete/$1');

$routes->get('/data-mandor', 'MandorController::mandor');
$routes->get('/data-mandor/json', 'MandorController::json');
$routes->post('/mandor/store', 'MandorController::store');
$routes->get('/mandor/edit/(:num)', 'MandorController::edit/$1');
$routes->post('/mandor/update/(:num)', 'MandorController::update/$1');
$routes->delete('/mandor/delete/(:num)', 'MandorController::delete/$1');

$routes->get('/data-spv', 'SpvController::spv');
$routes->get('/data-spv/json', 'SpvController::json');

$routes->get('/rab-rumah', 'RabRumahController::rabrumah');
$routes->get('/rab-rumah/json', 'RabRumahController::json');
$routes->get('/rab-rumah/edit/(:num)', 'RabRumahController::edit/$1');
$routes->post('/rab-rumah/store', 'RabRumahController::store');
$routes->post('/rab-rumah/update/(:num)', 'RabRumahController::update/$1');
$routes->delete('/rab-rumah/delete/(:num)', 'RabRumahController::delete/$1');
$routes->get('/rab-rumah/cetak/(:num)', 'RabRumahController::cetak/$1');


$routes->get('/rab-bahan', 'RabBahanController::rabbahan');
$routes->get('/rab-bahan/json', 'RabBahanController::json');
$routes->post('/rab-bahan/store', 'RabBahanController::store');
$routes->get('/rab-bahan/edit/(:num)', 'RabBahanController::edit/$1');
$routes->post('/rab-bahan/update/(:num)', 'RabBahanController::update/$1');
$routes->delete('/rab-bahan/delete/(:num)', 'RabBahanController::delete/$1');

$routes->get('/rab-pekerja', 'RabPekerjaController::rabpekerja');
$routes->get('/rab-pekerja/json', 'RabPekerjaController::json');
$routes->post('/rab-pekerja/store', 'RabPekerjaController::store');
$routes->get('/rab-pekerja/edit/(:num)', 'RabPekerjaController::edit/$1');
$routes->post('/rab-pekerja/update/(:num)', 'RabPekerjaController::update/$1');
$routes->delete('/rab-pekerja/delete/(:num)', 'RabPekerjaController::delete/$1');
$routes->get('rab-pekerja/get-kode-rumah/(:num)', 'RabPekerjaController::getKodeRumah/$1');


$routes->get('/realisasi-rumah', 'RealisasiRumahController::realisasirumah');
$routes->get('/realisasi-rumah/json', 'RealisasiRumahController::json');
$routes->post('/realisasi-rumah/store', 'RealisasiRumahController::store');
$routes->get('/realisasi-rumah/edit/(:num)', 'RealisasiRumahController::edit/$1');
$routes->post('/realisasi-rumah/update/(:num)', 'RealisasiRumahController::update/$1');
$routes->delete('/realisasi-rumah/delete/(:num)', 'RealisasiRumahController::delete/$1');
$routes->get('/realisasi-rumah/cetak/(:num)', 'RealisasiRumahController::cetak/$1');

$routes->get('/realisasi-pekerja', 'RealisasiPekerjaController::realisasipekerja');
$routes->get('/realisasi-pekerja/json', 'RealisasiPekerjaController::json');
$routes->post('/realisasi-pekerja/store', 'RealisasiPekerjaController::store');
$routes->get('/realisasi-pekerja/edit/(:num)', 'RealisasiPekerjaController::edit/$1');
$routes->post('/realisasi-pekerja/update/(:num)', 'RealisasiPekerjaController::update/$1');
$routes->delete('/realisasi-pekerja/delete/(:num)', 'RealisasiPekerjaController::delete/$1');

$routes->get('/realisasi-bahan', 'RealisasiBahanController::realisasibahan');
$routes->get('/realisasi-bahan/json', 'RealisasiBahanController::json');
$routes->post('/realisasi-bahan/store', 'RealisasiBahanController::store');
$routes->get('/realisasi-bahan/edit/(:num)', 'RealisasiBahanController::edit/$1');
$routes->post('/realisasi-bahan/update/(:num)', 'RealisasiBahanController::update/$1');
$routes->delete('/realisasi-bahan/delete/(:num)', 'RealisasiBahanController::delete/$1');


$routes->get('/pembatalan-transaksi', 'PembatalanTransaksiController::pembatalan');
$routes->get('/pembatalan-transaksi/json', 'PembatalanTransaksiController::json');


$routes->get('/pekerjaan-insidentil', 'PekerjaanInsidentilController::pekerjaaninsidentil');
$routes->get('/pekerjaan-insidentil/json', 'PekerjaanInsidentilController::json');
$routes->post('/pekerjaan-insidentil/store', 'PekerjaanInsidentilController::store');
$routes->get('/pekerjaan-insidentil/edit/(:num)', 'PekerjaanInsidentilController::edit/$1');
$routes->post('/pekerjaan-insidentil/update/(:num)', 'PekerjaanInsidentilController::update/$1');
$routes->delete('/pekerjaan-insidentil/delete/(:num)', 'PekerjaanInsidentilController::delete/$1');

$routes->get('/perumahan', 'DashboardController::index');
