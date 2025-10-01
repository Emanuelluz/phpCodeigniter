<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('', ['namespace' => 'Modules\\Auth\\Controllers'], static function (RouteCollection $routes) {
	$routes->get('login', 'AuthController::login');
	$routes->post('login', 'AuthController::doLogin');
	$routes->get('logout', 'AuthController::logout');
});
