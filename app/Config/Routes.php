<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('health', function () {
    return service('response')
        ->setStatusCode(200)
        ->setHeader('Content-Type', 'application/json')
        ->setBody(json_encode(['status' => 'ok']));
}, ['as' => 'health']);

foreach (glob(ROOTPATH . 'modules/*', GLOB_ONLYDIR) as $modulePath) {
    $moduleName = basename($modulePath);
    $routeFile  = $modulePath . '/Routes.php';

    if (file_exists($routeFile)) {
        /**
         * Membuat route group dengan prefix sesuai nama module
         * (contoh: /pelayanan, /kalenderkegiatan)
         */
        $routes->group(strtolower($moduleName), [
            'namespace' => "Modules\\$moduleName\\Controllers"
        ], function ($routes) use ($routeFile) {
            require $routeFile;
        });
    }
}
