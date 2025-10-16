<?php

namespace Modules\Sso\Config;

use CodeIgniter\Config\BaseConfig;

class SsoSettings extends BaseConfig
{
    /**
     * Tempo de sessão em segundos
     * @var int
     */
    public int $sessionTimeout = 3600; // 1 hora

    /**
     * Número máximo de tentativas de login
     * @var int
     */
    public int $maxLoginAttempts = 5;

    /**
     * Duração do bloqueio em segundos
     * @var int
     */
    public int $lockoutDuration = 900; // 15 minutos

    /**
     * Habilitar autenticação de dois fatores
     * @var bool
     */
    public bool $enable2FA = false;

    /**
     * Habilitar rate limiting
     * @var bool
     */
    public bool $rateLimitEnabled = true;

    /**
     * Número máximo de requisições permitidas
     * @var int
     */
    public int $rateLimitRequests = 10;

    /**
     * Janela de tempo para rate limiting (segundos)
     * @var int
     */
    public int $rateLimitWindow = 60;

    /**
     * Dias para retenção de logs
     * @var int
     */
    public int $logRetentionDays = 90;

    /**
     * Registrar tentativas de login falhadas
     * @var bool
     */
    public bool $logFailedAttempts = true;

    /**
     * Configurações LDAP
     */
    public bool $ldapEnabled = false;
    public int $ldapTimeout = 30;
    public string $ldapHost = '';
    public int $ldapPort = 389;
    public string $ldapBaseDN = '';
    public string $ldapBindDN = '';
    public string $ldapBindPassword = '';
    public string $ldapUserFilter = '(uid={username})';
    public bool $ldapUseTLS = false;

    /**
     * Configurações OAuth
     */
    public bool $oauthGoogleEnabled = false;
    public string $oauthGoogleClientId = '';
    public string $oauthGoogleClientSecret = '';
    public string $oauthGoogleRedirectUri = '';

    public bool $oauthMicrosoftEnabled = false;
    public string $oauthMicrosoftClientId = '';
    public string $oauthMicrosoftClientSecret = '';
    public string $oauthMicrosoftRedirectUri = '';

    /**
     * Configurações SAML
     */
    public bool $samlEnabled = false;
    public bool $samlStrictMode = true;
    public bool $samlDebug = false;
    public string $samlEntityId = '';
    public string $samlIdpEntityId = '';
    public string $samlIdpSsoUrl = '';
    public string $samlIdpX509Cert = '';
}
