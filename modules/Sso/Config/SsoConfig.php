<?php

namespace Modules\Sso\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * SSO Configuration
 * 
 * Configurações para Single Sign-On e providers de autenticação
 */
class SsoConfig extends BaseConfig
{
    /**
     * Provider de autenticação padrão
     * 
     * Opções: 'local', 'ldap', 'oauth', 'saml'
     */
    public string $defaultProvider = 'local';

    /**
     * Permitir múltiplos providers simultâneos
     */
    public bool $allowMultipleProviders = true;

    /**
     * Permitir fallback para autenticação local
     */
    public bool $allowLocalFallback = true;

    /**
     * Sincronização automática de usuários
     */
    public bool $autoSyncUsers = false;

    /**
     * Criar usuários automaticamente no primeiro login
     */
    public bool $autoCreateUsers = true;

    /**
     * Atualizar dados do usuário a cada login
     */
    public bool $updateUserOnLogin = true;

    /**
     * Configurações de providers disponíveis
     */
    public array $providers = [
        'local' => [
            'enabled' => true,
            'class'   => 'Modules\Sso\Libraries\Providers\LocalProvider',
            'config'  => [],
        ],
        'ldap' => [
            'enabled' => false,
            'class'   => 'Modules\Sso\Libraries\Providers\LdapProvider',
            'config'  => [
                'host'            => '',
                'port'            => 389,
                'base_dn'         => '',
                'bind_dn'         => '',
                'bind_password'   => '',
                'username_attr'   => 'sAMAccountName',
                'email_attr'      => 'mail',
                'name_attr'       => 'cn',
                'user_filter'     => '(objectClass=person)',
                'use_tls'         => false,
                'use_ssl'         => false,
                'timeout'         => 10,
            ],
        ],
        'oauth' => [
            'enabled' => false,
            'class'   => 'Modules\Sso\Libraries\Providers\OAuthProvider',
            'config'  => [
                'providers' => [
                    'google' => [
                        'client_id'     => '',
                        'client_secret' => '',
                        'redirect_uri'  => '',
                        'scopes'        => ['email', 'profile'],
                    ],
                    'microsoft' => [
                        'client_id'     => '',
                        'client_secret' => '',
                        'redirect_uri'  => '',
                        'tenant'        => 'common',
                        'scopes'        => ['openid', 'email', 'profile'],
                    ],
                ],
            ],
        ],
        'saml' => [
            'enabled' => false,
            'class'   => 'Modules\Sso\Libraries\Providers\SamlProvider',
            'config'  => [
                'idp_entity_id'      => '',
                'idp_sso_url'        => '',
                'idp_slo_url'        => '',
                'idp_certificate'    => '',
                'sp_entity_id'       => '',
                'sp_acs_url'         => '',
                'sp_slo_url'         => '',
                'sp_certificate'     => '',
                'sp_private_key'     => '',
                'name_id_format'     => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
                'attribute_mapping'  => [
                    'email'      => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress',
                    'name'       => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name',
                    'username'   => 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/upn',
                ],
            ],
        ],
    ];

    /**
     * Mapeamento de campos do usuário
     */
    public array $userFieldMapping = [
        'username' => 'username',
        'email'    => 'email',
        'name'     => 'name',
        'active'   => 'active',
    ];

    /**
     * Grupo padrão para novos usuários SSO
     */
    public string $defaultGroup = 'user';

    /**
     * Tempo de vida da sessão SSO (em segundos)
     */
    public int $sessionLifetime = 7200; // 2 horas

    /**
     * Lembrar credenciais (Remember Me)
     */
    public bool $allowRememberMe = true;
    public int $rememberMeDuration = 2592000; // 30 dias

    /**
     * Logs de autenticação
     */
    public bool $logAuthAttempts = true;
    public bool $logSuccessfulLogins = true;
    public bool $logFailedLogins = true;
    public int $logRetentionDays = 90;

    /**
     * Rate limiting
     */
    public bool $enableRateLimiting = true;
    public int $maxLoginAttempts = 5;
    public int $lockoutDuration = 900; // 15 minutos

    /**
     * Redirects
     */
    public string $loginRedirect = '/sso/admin';
    public string $logoutRedirect = '/sso/login';

    /**
     * Views customizadas
     */
    public array $views = [
        'login'     => 'Modules\Sso\Views\login',
        'providers' => 'Modules\Sso\Views\providers\index',
        'settings'  => 'Modules\Sso\Views\settings\index',
    ];
}
