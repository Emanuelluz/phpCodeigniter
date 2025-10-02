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
            ]);
            $users->save($user);
            $user = $users->findByCredentials(['email' => $email]);
        }

        // Garante associação do usuário admin ao grupo superadmin via serviço de autorização
        try {
            $authz = service('authorization');
            if (! $authz->inGroup('superadmin', $user->id)) {
                $authz->addUserToGroup($user->id, 'superadmin');
            }
        } catch (\Throwable $e) {
            log_message('warning', 'Falha ao garantir grupo superadmin ao admin: ' . $e->getMessage());
        }
    }
}
