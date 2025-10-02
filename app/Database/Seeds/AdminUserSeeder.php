<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $email = getenv('ADMIN_EMAIL') ?: 'admin@example.com';
        $password = getenv('ADMIN_PASSWORD') ?: 'admin123';
        $username = getenv('ADMIN_USERNAME') ?: 'admin';

        $users = auth()->getProvider();

        // Busca usuário existente ou cria um novo
        $existing = $users->findByCredentials(['email' => $email]);
        if ($existing) {
            $user = $existing;
        } else {
            $user = new User([
                'username' => $username,
                'email'    => $email,
                'password' => $password,
                'active'   => true,
            ]);
            $users->save($user);
            $user = $users->findByCredentials(['email' => $email]);
        }

        // Garante associação do usuário admin ao grupo superadmin
        try {
            // Força reload do usuário para ter os dados atualizados
            $user = $users->findByCredentials(['email' => $email]);
            
            // Remove do grupo padrão se existir
            if (method_exists($user, 'removeGroup')) {
                $user->removeGroup('user');
            }
            
            // Adiciona ao grupo superadmin
            if (method_exists($user, 'addGroup')) {
                $user->addGroup('superadmin');
            }
            
            // Força ativo=true
            $user->active = true;
            $users->save($user);
            
        } catch (\Throwable $e) {
            log_message('warning', 'Falha ao configurar grupo superadmin ao admin: ' . $e->getMessage());
        }
    }
}
