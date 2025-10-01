<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('admin', ['namespace' => 'Modules\Admin\Controllers'], static function ($routes) {
    $routes->get('/', '::index');
});
