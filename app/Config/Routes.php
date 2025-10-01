<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Modules\Admin\Controllers\Admin::index');

$routes->group('admin', ['namespace' => 'Modules\Admin\Controllers'], static function ($routes) {
    $routes->get('/', 'Admin::index');
});
