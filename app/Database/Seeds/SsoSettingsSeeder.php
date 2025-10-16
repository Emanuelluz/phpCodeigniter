<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SsoSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // Segurança
            [
                'setting_key'   => 'session_timeout',
                'setting_value' => '3600',
                'setting_group' => 'security',
                'description'   => 'Tempo de sessão em segundos (3600 = 1 hora)',
                'is_system'     => 1,
            ],
            [
                'setting_key'   => 'max_login_attempts',
                'setting_value' => '5',
                'setting_group' => 'security',
                'description'   => 'Número máximo de tentativas de login antes de bloquear',
                'is_system'     => 1,
            ],
            [
                'setting_key'   => 'lockout_duration',
                'setting_value' => '900',
                'setting_group' => 'security',
                'description'   => 'Duração do bloqueio em segundos (900 = 15 minutos)',
                'is_system'     => 1,
            ],
            [
                'setting_key'   => 'enable_2fa',
                'setting_value' => '0',
                'setting_group' => 'security',
                'description'   => 'Habilitar autenticação de dois fatores',
                'is_system'     => 1,
            ],

            // Rate Limiting
            [
                'setting_key'   => 'rate_limit_enabled',
                'setting_value' => '1',
                'setting_group' => 'rate_limiting',
                'description'   => 'Habilitar rate limiting para login',
                'is_system'     => 1,
            ],
            [
                'setting_key'   => 'rate_limit_requests',
                'setting_value' => '10',
                'setting_group' => 'rate_limiting',
                'description'   => 'Número máximo de requisições permitidas',
                'is_system'     => 1,
            ],
            [
                'setting_key'   => 'rate_limit_window',
                'setting_value' => '60',
                'setting_group' => 'rate_limiting',
                'description'   => 'Janela de tempo em segundos para rate limiting',
                'is_system'     => 1,
            ],

            // Logs
            [
                'setting_key'   => 'log_retention_days',
                'setting_value' => '90',
                'setting_group' => 'logs',
                'description'   => 'Dias para retenção de logs de autenticação',
                'is_system'     => 1,
            ],
            [
                'setting_key'   => 'log_failed_attempts',
                'setting_value' => '1',
                'setting_group' => 'logs',
                'description'   => 'Registrar tentativas de login falhadas',
                'is_system'     => 1,
            ],

            // LDAP
            [
                'setting_key'   => 'ldap_enabled',
                'setting_value' => '0',
                'setting_group' => 'ldap',
                'description'   => 'Habilitar autenticação LDAP',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'ldap_host',
                'setting_value' => '',
                'setting_group' => 'ldap',
                'description'   => 'Servidor LDAP (ex: ldap.example.com)',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'ldap_port',
                'setting_value' => '389',
                'setting_group' => 'ldap',
                'description'   => 'Porta do servidor LDAP',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'ldap_base_dn',
                'setting_value' => '',
                'setting_group' => 'ldap',
                'description'   => 'Base DN (ex: dc=example,dc=com)',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'ldap_bind_dn',
                'setting_value' => '',
                'setting_group' => 'ldap',
                'description'   => 'Bind DN para autenticação no LDAP',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'ldap_bind_password',
                'setting_value' => '',
                'setting_group' => 'ldap',
                'description'   => 'Senha para bind DN',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'ldap_user_filter',
                'setting_value' => '(uid={username})',
                'setting_group' => 'ldap',
                'description'   => 'Filtro de busca de usuários',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'ldap_use_tls',
                'setting_value' => '0',
                'setting_group' => 'ldap',
                'description'   => 'Usar TLS para conexão LDAP',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'ldap_timeout',
                'setting_value' => '30',
                'setting_group' => 'ldap',
                'description'   => 'Timeout de conexão LDAP em segundos',
                'is_system'     => 0,
            ],

            // OAuth - Google
            [
                'setting_key'   => 'oauth_google_enabled',
                'setting_value' => '0',
                'setting_group' => 'oauth',
                'description'   => 'Habilitar login com Google',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'oauth_google_client_id',
                'setting_value' => '',
                'setting_group' => 'oauth',
                'description'   => 'Google OAuth Client ID',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'oauth_google_client_secret',
                'setting_value' => '',
                'setting_group' => 'oauth',
                'description'   => 'Google OAuth Client Secret',
                'is_system'     => 0,
            ],

            // OAuth - Microsoft
            [
                'setting_key'   => 'oauth_microsoft_enabled',
                'setting_value' => '0',
                'setting_group' => 'oauth',
                'description'   => 'Habilitar login com Microsoft',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'oauth_microsoft_client_id',
                'setting_value' => '',
                'setting_group' => 'oauth',
                'description'   => 'Microsoft OAuth Client ID',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'oauth_microsoft_client_secret',
                'setting_value' => '',
                'setting_group' => 'oauth',
                'description'   => 'Microsoft OAuth Client Secret',
                'is_system'     => 0,
            ],

            // SAML
            [
                'setting_key'   => 'saml_enabled',
                'setting_value' => '0',
                'setting_group' => 'saml',
                'description'   => 'Habilitar autenticação SAML',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'saml_strict_mode',
                'setting_value' => '1',
                'setting_group' => 'saml',
                'description'   => 'Modo estrito SAML (validação completa)',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'saml_entity_id',
                'setting_value' => '',
                'setting_group' => 'saml',
                'description'   => 'Entity ID do Service Provider',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'saml_idp_entity_id',
                'setting_value' => '',
                'setting_group' => 'saml',
                'description'   => 'Entity ID do Identity Provider',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'saml_idp_sso_url',
                'setting_value' => '',
                'setting_group' => 'saml',
                'description'   => 'URL SSO do Identity Provider',
                'is_system'     => 0,
            ],
            [
                'setting_key'   => 'saml_idp_x509_cert',
                'setting_value' => '',
                'setting_group' => 'saml',
                'description'   => 'Certificado X.509 do Identity Provider',
                'is_system'     => 0,
            ],
        ];

        // Inserir configurações
        $table = $this->db->table('sso_settings');
        
        foreach ($settings as $setting) {
            // Verificar se já existe
            $existing = $table->where('setting_key', $setting['setting_key'])->get()->getRow();

            if (!$existing) {
                $setting['created_at'] = date('Y-m-d H:i:s');
                $setting['updated_at'] = date('Y-m-d H:i:s');
                $table->insert($setting);
                echo "✓ Configuração criada: {$setting['setting_key']}\n";
            } else {
                echo "- Configuração já existe: {$setting['setting_key']}\n";
            }
        }

        echo "\n✅ Configurações SSO criadas com sucesso!\n";
    }
}
