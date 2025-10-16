<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

/**
 * Admin User Seeder
 * 
 * Cria usuário administrador padrão do sistema
 */
class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $users = auth()->getProvider();

        // Verificar se usuário admin já existe
        $existingUser = $users->findByCredentials(['username' => 'admin']);
        
        if ($existingUser) {
            echo "Usuário 'admin' já existe. Pulando criação.\n";
            return;
        }

        // Criar usuário administrador
        $user = new User([
            'username' => 'admin',
            'email'    => 'admin@example.com',
            'password' => 'DtiFB@2025',
            'active'   => true,
        ]);

        $users->save($user);

        // Adicionar ao grupo de administradores
        $user = $users->findByCredentials(['username' => 'admin']);
        
        if ($user) {
            // Adicionar grupo 'superadmin' ou 'admin'
            $user->addGroup('superadmin');
            
            // Criar identidade adicional para login com username
            // Por padrão, Shield só cria email_password
            $identities = model('CodeIgniter\Shield\Models\UserIdentityModel');
            
            // Verificar se já existe identidade username_password
            $existingIdentity = $identities->where([
                'user_id' => $user->id,
                'type' => 'username_password'
            ])->first();
            
            if (!$existingIdentity) {
                $identities->insert([
                    'user_id' => $user->id,
                    'type' => 'username_password',
                    'secret' => 'admin',  // username
                    'secret2' => $user->password_hash,  // mesma senha hasheada
                    'force_reset' => 0,
                ]);
            }
            
            echo "✅ Usuário administrador criado com sucesso!\n";
            echo "   Username: admin\n";
            echo "   Email: admin@example.com\n";
            echo "   Senha: DtiFB@2025\n";
            echo "   Grupo: superadmin\n";
            echo "   Login: Pode usar username OU email\n";
        } else {
            echo "❌ Erro ao buscar usuário criado.\n";
        }
    }
}
