<?php

namespace Modules\Statistik\Config;

$routes->group('statistik', ['namespace' => 'Modules\Statistik\Controllers'], static function ($routes) {
    $routes->get('/', 'StatistikController::index');
    $routes->get('get-data', 'StatistikController::getDataStatistik');
});