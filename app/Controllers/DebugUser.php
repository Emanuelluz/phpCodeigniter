<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DebugUser extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Verificar se as tabelas existem
        $tables = $db->listTables();
        
        $data = [
            'tables' => $tables,
            'users' => [],
            'auth_identities' => []
        ];
        
        // Se a tabela users existe, buscar dados
        if (in_array('users', $tables)) {
            // Primeiro verificar a estrutura da tabela
            $structure = $db->query("PRAGMA table_info(users)")->getResultArray();
            $data['users_structure'] = $structure;
            
            // Buscar apenas colunas que existem
            $usersQuery = $db->query("SELECT * FROM users");
            $data['users'] = $usersQuery->getResultArray();
        }
        
        // Se a tabela auth_identities existe, buscar dados
        if (in_array('auth_identities', $tables)) {
            $authQuery = $db->query("SELECT id, user_id, type, name, secret FROM auth_identities");
            $data['auth_identities'] = $authQuery->getResultArray();
        }
        
        return $this->response->setJSON($data, JSON_PRETTY_PRINT);
    }
}