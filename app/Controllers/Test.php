<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Test extends Controller
{
    public function index()
    {
        return view('test_simple');
    }
    
    public function db()
    {
        try {
            $db = \Config\Database::connect();
            $result = $db->query('SELECT 1 as test');
            $row = $result->getRow();
            
            return json_encode([
                'status' => 'success',
                'message' => 'Conexão com banco de dados funcionando',
                'test_result' => $row->test
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => 'Erro de conexão com banco de dados',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function migrate()
    {
        try {
            $migrate = \Config\Services::migrations();
            
            // Executar migrações
            $migrate->latest();
            
            return json_encode([
                'status' => 'success',
                'message' => 'Migrações executadas com sucesso'
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => 'Erro ao executar migrações',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function tables()
    {
        try {
            $db = \Config\Database::connect();
            $tables = $db->listTables();
            
            return json_encode([
                'status' => 'success',
                'message' => 'Tabelas do banco de dados',
                'tables' => $tables
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => 'Erro ao listar tabelas',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function seedGroups()
    {
        try {
            // Obter configuração dos grupos
            $authGroupsConfig = config('AuthGroups');
            $groups = $authGroupsConfig->groups ?? [];
            
            if (empty($groups)) {
                return json_encode([
                    'status' => 'error',
                    'message' => 'Nenhum grupo encontrado na configuração'
                ]);
            }
            
            // Usar o GroupModel do Shield para criar os grupos
            $groupModel = model(\CodeIgniter\Shield\Models\GroupModel::class);
            
            $created = [];
            $skipped = [];
            
            foreach ($groups as $name => $info) {
                // Verificar se o grupo já existe
                if ($groupModel->where('name', $name)->first()) {
                    $skipped[] = $name;
                    continue;
                }
                
                // Criar o grupo
                $data = [
                    'name' => $name,
                    'title' => $info['title'] ?? ucfirst($name),
                    'description' => $info['description'] ?? ''
                ];
                
                if ($groupModel->insert($data)) {
                    $created[] = $name;
                }
            }
            
            return json_encode([
                'status' => 'success',
                'message' => 'Grupos processados',
                'created' => $created,
                'skipped' => $skipped,
                'total_groups' => count($groups)
            ]);
            
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => 'Erro ao criar grupos',
                'error' => $e->getMessage()
            ]);
        }
    }
}