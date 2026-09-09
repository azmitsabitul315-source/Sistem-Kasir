<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Halaman default langsung ke Kasir
$routes->get('/', 'Kasir::index');

// Kasir
$routes->get('kasir', 'Kasir::index');
$routes->post('kasir/proses', 'Kasir::proses');

// Menu (CRUD)
$routes->get('menu', 'Menu::index');
$routes->get('menu/create', 'Menu::create');
$routes->post('menu/store', 'Menu::store');
$routes->get('menu/edit/(:num)', 'Menu::edit/$1');
$routes->post('menu/update/(:num)', 'Menu::update/$1');
$routes->post('menu/toggle-status/(:num)', 'Menu::toggleStatus/$1');

// Histori
$routes->get('histori', 'Histori::index');
$routes->get('histori/detail/(:num)', 'Histori::detail/$1');
$routes->post('histori/void/(:num)', 'Histori::void/$1');

// Laporan
$routes->get('laporan', 'Laporan::harian');
$routes->get('laporan/harian', 'Laporan::harian');
$routes->get('laporan/bulanan', 'Laporan::bulanan');

// Nota (cetak)
$routes->get('nota/cetak/(:num)', 'Nota::cetak/$1');

// Pengaturan
$routes->get('setting', 'Setting::index');
$routes->post('setting/update', 'Setting::update');
