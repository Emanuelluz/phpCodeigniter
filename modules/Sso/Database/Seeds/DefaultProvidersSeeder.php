<?php

namespace Modules\Sso\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Default Providers Seeder
 * 
 * Cria provider local padrão e exemplos de configuração
 */
class DefaultProvidersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'local',
                'type' => 'local',
                'title' => 'Autenticação Local',
                'description' => 'Login com usuário e senha do sistema',
                'config' => json_encode([
                    'enabled' => true,
                    'allow_email_login' => true,
                    'allow_username_login' => true,
                ]),
                'is_enabled' => true,
                'is_default' => true,
                'priority' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Inserir providers
        $this->db->table('sso_providers')->insertBatch($data);

        echo "✓ Provider local padrão criado com sucesso!\n";
    }
}
