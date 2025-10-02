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
            
            // Usar o GroupModel do Shield diretamente
            $groupModel = model(\CodeIgniter\Shield\Models\GroupModel::class);
            
            // Verificar estrutura da tabela
            $db = \Config\Database::connect();
            $fields = $db->getFieldData('auth_groups');
            
            $created = [];
            $skipped = [];
            $tableStructure = [];
            
            foreach ($fields as $field) {
                $tableStructure[] = $field->name . ' (' . $field->type . ')';
            }
            
            foreach ($groups as $name => $info) {
                // Verificar se o grupo já existe usando a estrutura correta
                $exists = $db->table('auth_groups')
                    ->where('group', $name)
                    ->countAllResults() > 0;
                    
                if ($exists) {
                    $skipped[] = $name;
                    continue;
                }
                
                // Criar o grupo usando SQL direto
                $data = [
                    'group' => $name,
                    'title' => $info['title'] ?? ucfirst($name),
                    'description' => $info['description'] ?? ''
                ];
                
                if ($db->table('auth_groups')->insert($data)) {
                    $created[] = $name;
                }
            }
            
            return json_encode([
                'status' => 'success',
                'message' => 'Grupos processados',
                'created' => $created,
                'skipped' => $skipped,
                'total_groups' => count($groups),
                'table_structure' => $tableStructure
            ], JSON_PRETTY_PRINT);
            
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => 'Erro ao criar grupos',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], JSON_PRETTY_PRINT);
        }
    }
    
    public function debugAuth()
    {
        try {
            $auth = service('auth');
            $session = service('session');
            
            // Verificar se usuário está logado
            $user = $auth->user();
            $isLoggedIn = $auth->loggedIn();
            
            // Verificar configurações
            $config = config('Auth');
            
            return json_encode([
                'status' => 'success',
                'session_id' => session_id(),
                'is_logged_in' => $isLoggedIn,
                'user_id' => $user ? $user->id : null,
                'user_username' => $user ? $user->username : null,
                'session_data' => $session->get(),
                'auth_config' => [
                    'recordLoginAttempts' => $config->recordLoginAttempts ?? null,
                    'allowRegistration' => $config->allowRegistration ?? null,
                    'sessionConfig' => [
                        'field' => $config->sessionConfig['field'] ?? null,
                    ]
                ],
                'redirect_config' => [
                    'loginRedirect' => site_url('admin'),
                    'current_url' => current_url(),
                    'base_url' => base_url()
                ]
            ], JSON_PRETTY_PRINT);
            
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => 'Erro no debug auth',
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function activateAdmin()
    {
        try {
            $db = \Config\Database::connect();
            
            // Ativar usuário admin (ID 1)
            $result = $db->table('users')
                ->where('id', 1)
                ->update(['active' => 1]);
            
            if ($result) {
                return json_encode([
                    'status' => 'success',
                    'message' => 'Usuário admin ativado com sucesso!',
                    'affected_rows' => $result
                ]);
            } else {
                return json_encode([
                    'status' => 'error',
                    'message' => 'Não foi possível ativar o usuário admin'
                ]);
            }
            
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => 'Erro ao ativar admin',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function loginAdmin()
    {
        try {
            $auth = service('auth');
            
            // Tentar fazer login programaticamente
            $result = $auth->attempt([
                'email' => 'admin@example.com',
                'password' => 'admin123'
            ]);
            
            $debug_info = [
                'login_attempt' => $result->isOK(),
                'reason' => $result->reason(),
                'is_logged_in_after' => $auth->loggedIn(),
                'user_after' => $auth->user() ? [
                    'id' => $auth->user()->id,
                    'username' => $auth->user()->username,
                    'active' => $auth->user()->active
                ] : null,
                'session_id' => session_id(),
                'session_data' => session()->get()
            ];
            
            return json_encode($debug_info, JSON_PRETTY_PRINT);
            
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => 'Erro no login admin teste',
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        }
    }
}