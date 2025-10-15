<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

service('auth')->routes($routes);

// ===================================================
// SSO Module Routes
// ===================================================
require ROOTPATH . 'modules/Sso/Config/Routes.php';
