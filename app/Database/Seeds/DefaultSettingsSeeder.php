<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DefaultSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // Configurações de Segurança SSO
            [
                'key'          => 'sso.session_timeout',
                'value'        => '3600',
                'type'         => 'int',
                'group'        => 'sso',
                'description'  => 'Tempo de sessão em segundos (3600 = 1 hora)',
                'is_encrypted' => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'key'          => 'sso.max_login_attempts',
                'value'        => '5',
                'type'         => 'int',
                'group'        => 'sso',
                'description'  => 'Número máximo de tentativas de login antes de bloquear',
                'is_encrypted' => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'key'          => 'sso.lockout_duration',
                'value'        => '900',
                'type'         => 'int',
                'group'        => 'sso',
                'description'  => 'Duração do bloqueio após exceder tentativas (900 = 15 minutos)',
                'is_encrypted' => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'key'          => 'sso.enable_2fa',
                'value'        => '0',
                'type'         => 'bool',
                'group'        => 'sso',
                'description'  => 'Habilitar autenticação de dois fatores',
                'is_encrypted' => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            
            // Configurações de Rate Limiting
            [
                'key'          => 'sso.rate_limit_enabled',
                'value'        => '1',
                'type'         => 'bool',
                'group'        => 'sso',
                'description'  => 'Habilitar rate limiting para login',
                'is_encrypted' => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'key'          => 'sso.rate_limit_requests',
                'value'        => '10',
                'type'         => 'int',
                'group'        => 'sso',
                'description'  => 'Número máximo de requisições permitidas',
                'is_encrypted' => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'key'          => 'sso.rate_limit_window',
                'value'        => '60',
                'type'         => 'int',
                'group'        => 'sso',
                'description'  => 'Janela de tempo em segundos para rate limiting',
                'is_encrypted' => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            
            // Configurações de Logs
            [
                'key'          => 'sso.log_retention_days',
                'value'        => '90',
                'type'         => 'int',
                'group'        => 'sso',
                'description'  => 'Dias para retenção de logs de autenticação',
                'is_encrypted' => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'key'          => 'sso.log_failed_attempts',
                'value'        => '1',
                'type'         => 'bool',
                'group'        => 'sso',
                'description'  => 'Registrar tentativas de login falhadas',
                'is_encrypted' => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            
            // Configurações LDAP
            [
                'key'          => 'ldap.enabled',
                'value'        => '0',
                'type'         => 'bool',
                'group'        => 'ldap',
                'description'  => 'Habilitar autenticação LDAP',
                'is_encrypted' => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'key'          => 'ldap.timeout',
                'value'        => '30',
                'type'         => 'int',
                'group'        => 'ldap',
                'description'  => 'Timeout de conexão LDAP em segundos',
                'is_encrypted' => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            
            // Configurações OAuth
            [
                'key'          => 'oauth.google_enabled',
                'value'        => '0',
                'type'         => 'bool',
                'group'        => 'oauth',
                'description'  => 'Habilitar login com Google',
                'is_encrypted' => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'key'          => 'oauth.microsoft_enabled',
                'value'        => '0',
                'type'         => 'bool',
                'group'        => 'oauth',
                'description'  => 'Habilitar login com Microsoft',
                'is_encrypted' => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            
            // Configurações SAML
            [
                'key'          => 'saml.enabled',
                'value'        => '0',
                'type'         => 'bool',
                'group'        => 'saml',
                'description'  => 'Habilitar autenticação SAML',
                'is_encrypted' => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'key'          => 'saml.strict_mode',
                'value'        => '1',
                'type'         => 'bool',
                'group'        => 'saml',
                'description'  => 'Modo estrito SAML (validação completa)',
                'is_encrypted' => 0,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        // Inserir configurações
        foreach ($settings as $setting) {
            // Verificar se já existe
            $existing = $this->db->table('settings')
                ->where('key', $setting['key'])
                ->get()
                ->getRow();

            if (!$existing) {
                $this->db->table('settings')->insert($setting);
                echo "Configuração criada: {$setting['key']}\n";
            } else {
                echo "Configuração já existe: {$setting['key']}\n";
            }
        }

        echo "\n✅ Configurações padrão do SSO criadas com sucesso!\n";
    }
}
