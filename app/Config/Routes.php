<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Modules\Admin\Controllers\Admin::index');

$routes->group('/', static function ($routes) {
    // Rotas padrão do Shield (login, registro, reset etc.) se desejar usar as views do Shield
    service('auth')->routes($routes); // habilita rotas padrão do Shield

    // Carrega as rotas do módulo Auth (login/logout simples)
    require_once ROOTPATH . 'modules/Auth/Config/Routes.php';
});

$routes->group('admin', ['namespace' => 'Modules\Admin\Controllers'], static function ($routes) {
    // Proteger admin com filtro 'session' do Shield
    $routes->get('/', 'Admin::index', ['filter' => 'session']);
    
    // Rotas de gerenciamento de usuários
    $routes->group('users', ['filter' => 'session'], static function ($routes) {
        $routes->get('/', 'Users::index');
        $routes->get('create', 'Users::create');
        $routes->post('store', 'Users::store');
        $routes->get('edit/(:num)', 'Users::edit/$1');
        $routes->post('update/(:num)', 'Users::update/$1');
        $routes->post('delete/(:num)', 'Users::delete/$1');
        $routes->post('toggle-status/(:num)', 'Users::toggleStatus/$1');
    });
    
    // Rotas de gerenciamento de grupos
    $routes->group('groups', ['filter' => 'session'], static function ($routes) {
        $routes->get('/', 'Groups::index');
        $routes->get('create', 'Groups::create');
        $routes->post('store', 'Groups::store');
        $routes->get('edit/(:segment)', 'Groups::edit/$1');
        $routes->post('update/(:segment)', 'Groups::update/$1');
        $routes->post('delete/(:segment)', 'Groups::delete/$1');
        $routes->get('users/(:segment)', 'Groups::users/$1');
    });
    
    // Rotas de gerenciamento de permissões
    $routes->group('permissions', ['filter' => 'session'], static function ($routes) {
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
