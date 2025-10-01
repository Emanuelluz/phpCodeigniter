<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('blog', ['namespace' => 'Modules\Admin\Controllers'], static function ($routes) {
    $routes->get('/', 'Blog::index');
});
