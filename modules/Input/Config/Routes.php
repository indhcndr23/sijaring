<?php

namespace Modules\Input\Config;

$routes->group('input', ['namespace' => 'Modules\Input\Controllers'], function ($routes) {

    // ================== DATA AWAL (SURVEI AUTO-SAVE OPD) ==================
    $routes->get('data-awal', 'DataAwalController::index');
    $routes->get('data-awal/detail/(:num)', 'DataAwalController::getDetailSurvei/$1');
    $routes->post('data-awal/save-info-opd', 'DataAwalController::saveInfoOpd');
    $routes->post('data-awal/save-barang', 'DataAwalController::saveBarang');
    $routes->post('data-awal/delete-barang/(:num)', 'DataAwalController::deleteBarang/$1');

    // ================== NON-OPD (TEMPAT WISATA, FASUM, DLL) ==================
    $routes->get('non-opd', 'NonOpdController::index');
    $routes->get('non-opd/data', 'NonOpdController::nonOpdData');
    $routes->post('non-opd/store', 'NonOpdController::nonOpdStore');
    $routes->get('non-opd/hapus/(:num)', 'NonOpdController::nonOpdHapus/$1');

    // ================== MASTER BARANG ==================
    $routes->get('barang', 'InputController::barang');
    $routes->get('barang/data', 'InputController::barangData');
    $routes->post('barang/store', 'InputController::barangStore');
    $routes->post('barang/hapus/(:num)', 'InputController::barangHapus/$1');

    // ================== KEPEMILIKAN ==================
    $routes->get('kepemilikan', 'InputController::kepemilikan');
    $routes->get('kepemilikan/data', 'InputController::kepemilikanData');
    $routes->post('kepemilikan/store', 'InputController::kepemilikanStore');
    $routes->post('kepemilikan/hapus/(:num)', 'InputController::kepemilikanHapus/$1');

    // ================== INDEX (DIMENSI & VARIABEL) ==================
    $routes->get('index', 'InputController::index');
    $routes->get('index/data', 'InputController::indexData');

    // API Dimensi & Variabel
    $routes->get('index/dimensi/list', 'InputController::dimensiList');
    $routes->get('index/dimensi/detail/(:num)', 'InputController::dimensiDetail/$1');
    $routes->post('index/dimensi/store', 'InputController::dimensiStore');

    $routes->get('index/variabel/detail/(:num)', 'InputController::variabelDetail/$1');
    $routes->post('index/variabel/store', 'InputController::variabelStore');

    // Sub variabel
    $routes->get('index/sub-variabel/list/(:num)', 'InputController::subVariabelList/$1');
    $routes->get('index/sub-variabel/detail/(:num)', 'InputController::subVariabelDetail/$1');
    $routes->post('index/sub-variabel/store', 'InputController::subVariabelStore');
    $routes->post('index/sub-variabel/hapus/(:num)', 'InputController::subVariabelHapus/$1');
});
