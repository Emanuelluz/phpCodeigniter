<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rota padrão (homepage)
$routes->get('/', 'Home::index');

// Rotas de teste (temporárias)
$routes->get('/test', 'Test::index');
$routes->get('/test/db', 'Test::db');
$routes->get('/test/migrate', 'Test::migrate');
$routes->get('/test/tables', 'Test::tables');
$routes->get('/test/seed-groups', 'Test::seedGroups');
$routes->get('/test/debug-auth', 'Test::debugAuth');
$routes->get('/test/activate-admin', 'Test::activateAdmin');

// Grupo de rotas para autenticação (Shield)
$routes->group('/', static function ($routes) {
    // Rotas padrão do Shield (login, registro, reset etc.)
    service('auth')->routes($routes);

    // Carrega as rotas do módulo Auth (login/logout personalizado)
    if (file_exists(ROOTPATH . 'modules/Auth/Config/Routes.php')) {
        require_once ROOTPATH . 'modules/Auth/Config/Routes.php';
    }
});

// Grupo de rotas administrativas protegidas
$routes->group('admin', ['namespace' => 'Modules\Admin\Controllers', 'filter' => 'session'], static function ($routes) {
    // Dashboard administrativo
    $routes->get('/', 'Admin::index');
    
    // Rotas de gerenciamento de usuários
    $routes->group('users', static function ($routes) {
        $routes->get('/', 'Users::index');
        $routes->get('create', 'Users::create');
        $routes->post('store', 'Users::store');
        $routes->get('edit/(:num)', 'Users::edit/$1');
        $routes->post('update/(:num)', 'Users::update/$1');
        $routes->post('delete/(:num)', 'Users::delete/$1');
        $routes->post('toggle-status/(:num)', 'Users::toggleStatus/$1');
    });
    
    // Rotas de gerenciamento de grupos
    $routes->group('groups', static function ($routes) {
        $routes->get('/', 'Groups::index');
        $routes->get('create', 'Groups::create');
        $routes->post('store', 'Groups::store');
        $routes->get('edit/(:segment)', 'Groups::edit/$1');
        $routes->post('update/(:segment)', 'Groups::update/$1');
        $routes->post('delete/(:segment)', 'Groups::delete/$1');
        $routes->get('users/(:segment)', 'Groups::users/$1');
    });
    
    // Rotas de gerenciamento de permissões
    $routes->group('permissions', static function ($routes) {
        $routes->get('/', 'Permissions::index');
        $routes->get('create', 'Permissions::create');
        $routes->post('store', 'Permissions::store');
        $routes->get('edit/(:segment)', 'Permissions::edit/$1');
        $routes->post('update/(:segment)', 'Permissions::update/$1');
        $routes->post('delete/(:segment)', 'Permissions::delete/$1');
        $routes->get('matrix', 'Permissions::matrix');
        $routes->post('update-matrix', 'Permissions::updateMatrix');
    });
});
