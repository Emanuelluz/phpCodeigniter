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
$routes->get('/test/login-admin', 'Test::loginAdmin');
$routes->get('/test/debug-user', 'DebugUser::index');
$routes->get('/test/cookie-test', 'CookieTest::index');
$routes->get('/test-permissions', 'TestPermissions::index');

// Grupo de rotas para autenticação (Shield)
$routes->group('/', static function ($routes) {
    // Carrega as rotas do módulo Auth (login/logout personalizado) ANTES das rotas do Shield
    if (file_exists(ROOTPATH . 'modules/Auth/Config/Routes.php')) {
        require_once ROOTPATH . 'modules/Auth/Config/Routes.php';
    }
    
    // Rotas padrão do Shield (registro, reset etc.) - login já foi sobrescrito
    service('auth')->routes($routes);
});

// Grupo de rotas administrativas protegidas
$routes->group('admin', ['namespace' => 'Modules\Admin\Controllers', 'filter' => 'session'], static function ($routes) {
    // Dashboard administrativo - requer acesso admin
    $routes->get('/', 'Admin::index', ['filter' => 'permission:admin.access']);
    
    // Rotas de gerenciamento de usuários - requer permissões específicas
    $routes->group('users', ['filter' => 'permission:admin.access'], static function ($routes) {
        $routes->get('/', 'Users::index', ['filter' => 'permission:users.view']);
        $routes->get('create', 'Users::create', ['filter' => 'permission:users.create']);
        $routes->post('store', 'Users::store', ['filter' => 'permission:users.create']);
        $routes->get('edit/(:num)', 'Users::edit/$1', ['filter' => 'permission:users.edit']);
        $routes->post('update/(:num)', 'Users::update/$1', ['filter' => 'permission:users.edit']);
        $routes->post('delete/(:num)', 'Users::delete/$1', ['filter' => 'permission:users.delete']);
        $routes->post('toggle-status/(:num)', 'Users::toggleStatus/$1', ['filter' => 'permission:users.edit']);
    });
    
    // Rotas de gerenciamento de grupos - requer permissões específicas
    $routes->group('groups', ['filter' => 'permission:admin.access'], static function ($routes) {
        $routes->get('/', 'Groups::index', ['filter' => 'permission:groups.view']);
        $routes->get('create', 'Groups::create', ['filter' => 'permission:groups.create']);
        $routes->post('store', 'Groups::store', ['filter' => 'permission:groups.create']);
        $routes->get('edit/(:segment)', 'Groups::edit/$1', ['filter' => 'permission:groups.edit']);
        $routes->post('update/(:segment)', 'Groups::update/$1', ['filter' => 'permission:groups.edit']);
        $routes->post('delete/(:segment)', 'Groups::delete/$1', ['filter' => 'permission:groups.delete']);
        $routes->get('users/(:segment)', 'Groups::users/$1', ['filter' => 'permission:groups.view']);
    });
    
    // Rotas de gerenciamento de permissões - requer permissões específicas
    $routes->group('permissions', ['filter' => 'permission:admin.access'], static function ($routes) {
        $routes->get('/', 'Permissions::index', ['filter' => 'permission:permissions.view']);
        $routes->get('create', 'Permissions::create', ['filter' => 'permission:permissions.create']);
        $routes->post('store', 'Permissions::store', ['filter' => 'permission:permissions.create']);
        $routes->get('edit/(:segment)', 'Permissions::edit/$1', ['filter' => 'permission:permissions.edit']);
        $routes->post('update/(:segment)', 'Permissions::update/$1', ['filter' => 'permission:permissions.edit']);
        $routes->post('delete/(:segment)', 'Permissions::delete/$1', ['filter' => 'permission:permissions.delete']);
        $routes->get('matrix', 'Permissions::matrix', ['filter' => 'permission:permissions.view']);
        $routes->post('update-matrix', 'Permissions::updateMatrix');
    });
});
