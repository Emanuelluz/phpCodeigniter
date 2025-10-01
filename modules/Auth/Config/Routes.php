<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('login', 'Modules\\Auth\\Controllers\\AuthController::login');
$routes->post('login', 'Modules\\Auth\\Controllers\\AuthController::doLogin');
$routes->get('logout', 'Modules\\Auth\\Controllers\\AuthController::logout');
