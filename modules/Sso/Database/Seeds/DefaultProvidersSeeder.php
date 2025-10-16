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
        $table = $this->db->table('sso_providers');
        
        // Verificar se o provider local já existe
        $existing = $table->where('name', 'local')->get()->getRow();
        
        if ($existing) {
            echo "- Provider 'local' já existe. Pulando criação.\n";
            return;
        }

        $data = [
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
        ];

        // Inserir provider
        $table->insert($data);

        echo "✓ Provider local padrão criado com sucesso!\n";
    }
}
