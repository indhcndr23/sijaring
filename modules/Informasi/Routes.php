<?php

$routes->get('/', 'InformasiController::index', [
    'as' => 'base.informasi',
    'filter' => 'role:*'
]);

$routes->get('data', 'InformasiController::getInformasiData', [
    'as' => 'data.informasi'
]);

$routes->get('sop/(:any)', 'InformasiController::openSop/$1', [
    'as' => 'sop.informasi'
]);

$routes->head('sop/(:any)', 'InformasiController::openSop/$1');
