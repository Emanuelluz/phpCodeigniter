<?php

/**
 * SSO Module Routes
 * 
 * Define rotas para autenticação SSO e gerenciamento de providers
 */

// Rotas públicas de autenticação SSO
$routes->group('sso', ['namespace' => 'Modules\Sso\Controllers'], static function ($routes) {
    // Login SSO
    $routes->get('login', 'SsoController::login', ['as' => 'sso_login']);
    $routes->post('authenticate', 'SsoController::authenticate', ['as' => 'sso_authenticate']);
    
    // Callback para providers externos (OAuth, SAML, etc)
    $routes->get('callback/(:segment)', 'SsoController::callback/$1', ['as' => 'sso_callback']);
    
    // Logout SSO
    $routes->get('logout', 'SsoController::logout', ['as' => 'sso_logout']);
    
    // LDAP Authentication
    $routes->post('ldap/authenticate', 'LdapController::authenticate', ['as' => 'sso_ldap_auth']);
});

// Rotas administrativas (requer autenticação)
$routes->group('sso/admin', ['namespace' => 'Modules\Sso\Controllers', 'filter' => 'session'], static function ($routes) {
    // Dashboard de administração SSO
    $routes->get('/', 'AdminController::index', ['as' => 'sso_admin']);
    
    // Gerenciamento de Providers
    $routes->get('providers', 'ProviderController::index', ['as' => 'sso_providers']);
    $routes->get('providers/create', 'ProviderController::create', ['as' => 'sso_providers_create']);
    $routes->post('providers/store', 'ProviderController::store', ['as' => 'sso_providers_store']);
    $routes->get('providers/edit/(:num)', 'ProviderController::edit/$1', ['as' => 'sso_providers_edit']);
    $routes->post('providers/update/(:num)', 'ProviderController::update/$1', ['as' => 'sso_providers_update']);
    $routes->delete('providers/delete/(:num)', 'ProviderController::delete/$1', ['as' => 'sso_providers_delete']);
    $routes->post('providers/delete/(:num)', 'ProviderController::delete/$1'); // Fallback POST
    $routes->post('providers/toggle/(:num)', 'ProviderController::toggle/$1', ['as' => 'sso_providers_toggle']);
    
    // Gerenciamento de Usuários SSO
    $routes->get('users', 'UserController::index', ['as' => 'sso_users']);
    $routes->get('users/sync', 'UserController::syncLdap', ['as' => 'sso_users_sync']);
    $routes->post('users/import', 'UserController::import', ['as' => 'sso_users_import']);
    
    // Configurações SSO
    $routes->get('settings', 'SettingsController::index', ['as' => 'sso_settings']);
    $routes->post('settings/update', 'SettingsController::update', ['as' => 'sso_settings_update']);
    
    // Logs de Autenticação
    $routes->get('logs', 'LogController::index', ['as' => 'sso_logs']);
    $routes->get('logs/view/(:num)', 'LogController::view/$1', ['as' => 'sso_logs_view']);
    $routes->post('logs/clear', 'LogController::clear', ['as' => 'sso_logs_clear']);
    
    // Testes de Conexão
    $routes->post('test/ldap', 'TestController::testLdap', ['as' => 'sso_test_ldap']);
    $routes->post('test/oauth', 'TestController::testOAuth', ['as' => 'sso_test_oauth']);
    $routes->post('test/saml', 'TestController::testSaml', ['as' => 'sso_test_saml']);
});
