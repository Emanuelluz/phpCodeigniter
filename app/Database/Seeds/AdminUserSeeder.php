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

        // Se já existir, não recria
        if ($users->findByCredentials(['email' => $email])) {
            return;
        }

        $user = new User([
            'username' => $username,
            'email'    => $email,
            'password' => $password,
        ]);

        $users->save($user);
        $user = $users->findByCredentials(['email' => $email]);

        // adiciona ao grupo padrão (se configurado) ou cria um grupo admin
        try {
            $users->addToDefaultGroup($user);
        } catch (\Throwable $e) {
            // ignore se não houver grupo default
        }
    }
}
