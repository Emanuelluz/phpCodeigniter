<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AddUsernameIdentity extends BaseCommand
{
    protected $group       = 'Auth';
    protected $name        = 'auth:add-username';
    protected $description = 'Adiciona identidade username_password ao usuário admin';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('Verificando identidades do usuário admin (user_id=1)...', 'yellow');
        CLI::newLine();

        // Buscar usuário admin
        $user = $db->table('users')->where('id', 1)->get()->getRow();
        if (!$user) {
            CLI::error('❌ Usuário admin não encontrado!');
            return;
        }

        CLI::write("✅ Usuário encontrado: {$user->username}", 'green');
        CLI::newLine();

        // Buscar identidades existentes
        $identities = $db->table('auth_identities')
            ->where('user_id', 1)
            ->get()
            ->getResult();

        CLI::write('Identidades atuais:', 'yellow');
        foreach ($identities as $identity) {
            CLI::write("  - {$identity->type}: {$identity->secret}");
        }
        CLI::newLine();

        // Verificar se já existe username_password
        $usernameIdentity = $db->table('auth_identities')
            ->where('user_id', 1)
            ->where('type', 'username_password')
            ->get()
            ->getRow();

        if ($usernameIdentity) {
            CLI::write('⚠️  Identidade username_password já existe!', 'yellow');
            return;
        }

        // Buscar a senha hasheada da identidade email
        $emailIdentity = $db->table('auth_identities')
            ->where('user_id', 1)
            ->where('type', 'email_password')
            ->get()
            ->getRow();

        if (!$emailIdentity) {
            CLI::error('❌ Identidade email_password não encontrada!');
            return;
        }

        // Inserir nova identidade username_password
        $data = [
            'user_id' => 1,
            'type' => 'username_password',
            'secret' => 'admin',  // username
            'secret2' => $emailIdentity->secret2,  // mesma senha hasheada
            'force_reset' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $db->table('auth_identities')->insert($data);

        CLI::write('✅ Identidade username_password adicionada com sucesso!', 'green');
        CLI::newLine();
        CLI::write('Agora você pode fazer login com:', 'yellow');
        CLI::write('  - Username: admin', 'white');
        CLI::write('  - Email: admin@example.com', 'white');
        CLI::write('  - Senha: DtiFB@2025', 'white');
    }
}
