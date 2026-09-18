<?php

$routes->group('dashboard', ['namespace' => 'Modules\Dashboard\Controllers'], function ($routes) {
    $routes->get('/', 'DashboardController::grafik');
    $routes->get('grafik', 'DashboardController::grafik', ['as' => 'dashboard.grafik']);
    $routes->get('tabel', 'DashboardController::tabel', ['as' => 'dashboard.tabel']);
    $routes->get('peta', 'DashboardController::peta', ['as' => 'dashboard.peta']);
    $routes->get('detail/(:num)', 'DashboardController::detail/$1', ['as' => 'dashboard.detail']);
    $routes->get('histori-index/(:num)', 'DashboardController::historiIndex/$1');
});