<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// ===================================================
// SSO Module Routes
// ===================================================
require ROOTPATH . 'modules/Sso/Config/Routes.php';
