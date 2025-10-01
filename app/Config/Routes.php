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
});
