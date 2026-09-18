<?php

$routes->get('/', 'KalenderKegiatanController::index', [
    'as' => 'base.kalenderkegiatan',
    'filter' => 'role:*'
]);
$routes->get('(:num)', 'KalenderKegiatanController::detail/$1');
$routes->get('(:num)/edit', 'KalenderKegiatanController::edit/$1');
$routes->post('submit', 'KalenderKegiatanController::submit', ['as' => 'submit.kalenderkegiatan']);
$routes->get('get-data', 'KalenderKegiatanController::getData', ['as' => 'get-data.kalenderkegiatan']);
