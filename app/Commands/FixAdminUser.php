<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixAdminUser extends BaseCommand
{
    protected $group = 'admin';
    protected $name = 'admin:fix-user';
    protected $description = 'Fix the admin user to be active and in superadmin group';

    public function run(array $params)
    {
        $users = auth()->getProvider();
        $email = 'admin@example.com';
        
        CLI::write('Buscando usuário admin...', 'yellow');
        
        $user = $users->findByCredentials(['email' => $email]);
        if (!$user) {
            CLI::error('Usuário admin não encontrado! Execute primeiro: php spark db:seed AdminUserSeeder');
            return;
        }
        
        CLI::write('Usuário encontrado: ' . $user->username . ' (' . $user->email . ')', 'green');
        CLI::write('Status atual: ' . ($user->active ? 'Ativo' : 'Inativo'), $user->active ? 'green' : 'red');
        
        // Tornar ativo
        $user->active = true;
        
        // Remover do grupo user
        if (method_exists($user, 'getGroups')) {
            $currentGroups = $user->getGroups();
            CLI::write('Grupos atuais: ' . implode(', ', $currentGroups), 'cyan');
            
            if (in_array('user', $currentGroups) && method_exists($user, 'removeGroup')) {
                $user->removeGroup('user');
                CLI::write('Removido do grupo "user"', 'yellow');
            }
        }
        
        // Adicionar ao grupo superadmin
        if (method_exists($user, 'addGroup')) {
            $user->addGroup('superadmin');
            CLI::write('Adicionado ao grupo "superadmin"', 'green');
        }
        
        // Salvar alterações
        if ($users->save($user)) {
            CLI::write('Usuário admin corrigido com sucesso!', 'green');
            
            // Verificar resultado
            $updatedUser = $users->findByCredentials(['email' => $email]);
            CLI::write('Status final: ' . ($updatedUser->active ? 'Ativo' : 'Inativo'), $updatedUser->active ? 'green' : 'red');
            
            if (method_exists($updatedUser, 'getGroups')) {
                $finalGroups = $updatedUser->getGroups();
                CLI::write('Grupos finais: ' . implode(', ', $finalGroups), 'cyan');
            }
        } else {
            CLI::error('Erro ao salvar as alterações do usuário');
        }
    }
}