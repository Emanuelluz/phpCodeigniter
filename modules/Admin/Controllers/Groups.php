<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Shield\Authorization\AuthorizationException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Groups extends BaseController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);
        
        // Carregar helpers necessários
        helper(['form', 'url']);
    }

    public function index()
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to(url_to('login'));
        }

        // Limpar cache da configuração para garantir dados atualizados
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        // Obter todos os grupos configurados - com fallback para array vazio
        $authGroupsConfig = config('AuthGroups');
        $authGroups = $authGroupsConfig->groups ?? [];
        $authMatrix = $authGroupsConfig->matrix ?? [];
        
        // Verificar se as configurações existem
        if ($authGroups === null) {
            $authGroups = [];
        }
        if ($authMatrix === null) {
            $authMatrix = [];
        }
        
        // Preparar dados dos grupos com estatísticas
        $groups = [];
        $userProvider = auth()->getProvider();
        
        // Verificar se $authGroups é um array antes do foreach
        if (is_array($authGroups)) {
            foreach ($authGroups as $groupName => $groupConfig) {
                // Contar usuários neste grupo - método personalizado
                $userCount = $this->countUsersInGroup($groupName);
                
                // Obter permissões do grupo
                $permissions = is_array($authMatrix) ? ($authMatrix[$groupName] ?? []) : [];
                
                $groups[$groupName] = [
                    'name' => $groupName,
                    'title' => is_array($groupConfig) ? ($groupConfig['title'] ?? $groupName) : $groupName,
                    'description' => is_array($groupConfig) ? ($groupConfig['description'] ?? '') : '',
                    'permissions' => $permissions,
                    'user_count' => $userCount
                ];
            }
        }

        return view('Modules\\Admin\\Views\\groups\\index', [
            'groups' => $groups
        ]);
    }

    public function create()
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to(url_to('login'));
        }

        // Obter todas as permissões disponíveis
        $authGroupsConfig = config('AuthGroups');
        $permissions = $authGroupsConfig->permissions ?? [];

        return view('Modules\\Admin\\Views\\groups\\create', [
            'permissions' => $permissions
        ]);
    }

    public function store()
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Não autorizado'])->setStatusCode(401);
            }
            return redirect()->to(url_to('login'));
        }

        // Validação
        $rules = [
            'name' => 'required|min_length[2]|max_length[50]|alpha_dash',
            'title' => 'required|min_length[2]|max_length[100]',
            'description' => 'max_length[255]'
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'error' => 'Dados inválidos',
                    'validation' => $this->validator->getErrors()
                ])->setStatusCode(400);
            }
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $name = (string) $this->request->getPost('name');
        $title = (string) $this->request->getPost('title');
        $description = (string) $this->request->getPost('description');
        $permissions = (array) $this->request->getPost('permissions');

        // Verificar se o grupo já existe
        $authGroupsConfig = config('AuthGroups');
        $currentGroups = $authGroupsConfig->groups ?? [];
        if (isset($currentGroups[$name])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'error' => 'Grupo já existe com este nome.'
                ])->setStatusCode(400);
            }
            return redirect()->back()
                ->withInput()
                ->with('error', 'Grupo já existe com este nome.');
        }

        try {
            // Log para debug
            log_message('info', 'Tentando criar grupo: ' . $name . ' - ' . $title);
            
            // Adicionar grupo ao arquivo AuthGroups.php
            $this->addGroupToConfig($name, $title, $description, $permissions);
            
            log_message('info', 'Grupo criado com sucesso: ' . $name);
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Grupo criado com sucesso!'
                ]);
            }
            
            session()->setFlashdata('success', 'Grupo criado com sucesso!');
            return redirect()->to(base_url('admin/groups'));

        } catch (\Exception $e) {
            log_message('error', 'Erro ao criar grupo: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'error' => 'Erro ao criar grupo: ' . $e->getMessage()
                ])->setStatusCode(500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao criar grupo: ' . $e->getMessage());
        }
    }

    public function edit($groupName)
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to(url_to('login'));
        }

        // Obter grupos diretamente da configuração
        $authGroupsConfig = config('AuthGroups');
        $authGroups = $authGroupsConfig->groups ?? [];
        
        if (!isset($authGroups[$groupName])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Grupo não encontrado']);
            }
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $group = $authGroups[$groupName];
        $group['name'] = $groupName;

        // Se for requisição AJAX, retornar JSON
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($group);
        }

        // Obter permissões do grupo diretamente da configuração
        $authMatrix = $authGroupsConfig->matrix ?? [];
        $groupPermissions = $authMatrix[$groupName] ?? [];

        // Obter todas as permissões disponíveis diretamente da configuração
        $allPermissions = $authGroupsConfig->permissions ?? [];

        return view('Modules\\Admin\\Views\\groups\\edit', [
            'group' => $group,
            'group_permissions' => $groupPermissions,
            'all_permissions' => $allPermissions
        ]);
    }

    public function update($groupName)
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Não autorizado'])->setStatusCode(401);
            }
            return redirect()->to(url_to('login'));
        }

        // Verificar se o grupo existe
        $authGroupsConfig = config('AuthGroups');
        $authGroups = $authGroupsConfig->groups ?? [];
        if (!isset($authGroups[$groupName])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Grupo não encontrado'])->setStatusCode(404);
            }
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Obter dados da requisição
        if ($this->request->isAJAX()) {
            $input = $this->request->getJSON(true);
            $title = $input['title'] ?? '';
            $description = $input['description'] ?? '';
            $permissions = $input['permissions'] ?? [];
        } else {
            $title = (string) $this->request->getPost('title');
            $description = (string) $this->request->getPost('description');
            $permissions = (array) $this->request->getPost('permissions');
        }

        // Validação
        $rules = [
            'title' => 'required|min_length[2]|max_length[100]',
            'description' => 'max_length[255]'
        ];

        $validation = \Config\Services::validation();
        $validation->setRules($rules);
        
        if (!$validation->run(['title' => $title, 'description' => $description])) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Dados inválidos', 'validation' => $validation->getErrors()])->setStatusCode(400);
            }
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        try {
            // Atualizar arquivo de configuração permanentemente
            $this->updateAuthGroupsConfig($groupName, $title, $description, $permissions);
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => true, 'message' => 'Grupo atualizado com sucesso!']);
            }
            
            return redirect()->to(base_url('admin/groups'))
                ->with('success', 'Grupo atualizado com sucesso!');

        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Erro ao atualizar grupo: ' . $e->getMessage()])->setStatusCode(500);
            }
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar grupo: ' . $e->getMessage());
        }
    }

    public function delete($groupName)
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to(url_to('login'));
        }

        // Verificar se o grupo existe
        $authGroupsConfig = config('AuthGroups');
        $authGroups = $authGroupsConfig->groups ?? [];
        if (!isset($authGroups[$groupName])) {
            return redirect()->back()->with('error', 'Grupo não encontrado.');
        }

        // Verificar se há usuários neste grupo
        $userCount = $this->countUsersInGroup($groupName);

        if ($userCount > 0) {
            return redirect()->back()->with('error', "Não é possível excluir o grupo '{$groupName}' pois há {$userCount} usuário(s) associado(s).");
        }

        try {
            // Remover grupo permanentemente do arquivo de configuração
            $this->deleteGroupFromConfig($groupName);
            
            return redirect()->to(base_url('admin/groups'))
                ->with('success', "Grupo '{$groupName}' excluído com sucesso!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao excluir grupo: ' . $e->getMessage());
        }
    }

    public function users($groupName)
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to(url_to('login'));
        }

        // Obter grupos diretamente da configuração
        $authGroupsConfig = config('AuthGroups');
        $authGroups = $authGroupsConfig->groups ?? [];
        
        if (!isset($authGroups[$groupName])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Obter usuários do grupo
        $users = $this->getUsersInGroup($groupName);

        $group = $authGroups[$groupName];
        $group['name'] = $groupName;

        return view('Modules\\Admin\\Views\\groups\\users', [
            'group' => $group,
            'users' => $users
        ]);
    }

    /**
     * Contar usuários em um grupo específico
     */
    private function countUsersInGroup($groupName)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('auth_groups_users');
        return $builder->where('group', $groupName)->countAllResults();
    }

    /**
     * Obter usuários de um grupo específico
     */
    private function getUsersInGroup($groupName)
    {
        $userProvider = auth()->getProvider();
        
        // Obter IDs dos usuários no grupo
        $db = \Config\Database::connect();
        $builder = $db->table('auth_groups_users');
        $groupUsers = $builder->where('group', $groupName)->get()->getResultArray();
        
        if (empty($groupUsers)) {
            return [];
        }
        
        $userIds = array_column($groupUsers, 'user_id');
        
        // Buscar os usuários completos
        $users = $userProvider->withIdentities()->withGroups()->whereIn('id', $userIds)->findAll();
        
        return $users;
    }

    /**
     * Adicionar novo grupo ao arquivo Config/AuthGroups.php permanentemente
     */
    private function addGroupToConfig($groupName, $title, $description, $permissions)
    {
        $configPath = APPPATH . 'Config/AuthGroups.php';

        if (!is_writable($configPath)) {
            throw new \Exception('Arquivo Config/AuthGroups.php não tem permissão de escrita');
        }

        // Ler o arquivo atual
        $content = file_get_contents($configPath);
        if ($content === false) {
            throw new \Exception('Não foi possível ler o arquivo Config/AuthGroups.php');
        }

        // Verificar se o grupo já existe
        if (strpos($content, "'{$groupName}' =>") !== false) {
            throw new \Exception('Grupo já existe no arquivo de configuração');
        }

        // Escapar caracteres especiais
        $escapedTitle = addslashes($title);
        $escapedDescription = addslashes($description);

        // Dividir o conteúdo em linhas para manipulação mais precisa
        $lines = explode("\n", $content);
        $groupsStart = -1;
        $groupsEnd = -1;

        // Encontrar os limites da array $groups
        foreach ($lines as $i => $line) {
            if (strpos($line, 'public array $groups = [') !== false) {
                $groupsStart = $i;
            }
            if ($groupsStart !== -1 && strpos($line, '];') !== false && $groupsEnd === -1) {
                $groupsEnd = $i;
                break;
            }
        }

        if ($groupsStart === -1 || $groupsEnd === -1) {
            throw new \Exception('Não foi possível encontrar a seção $groups no arquivo');
        }

        // Preparar o novo grupo
        $newGroupLines = [
            "        '{$groupName}' => [",
            "            'title'       => '{$escapedTitle}',",
            "            'description' => '{$escapedDescription}',",
            "        ],"
        ];

        // Inserir o novo grupo antes do fechamento da array
        array_splice($lines, $groupsEnd, 0, $newGroupLines);

        // Reconstruir o conteúdo
        $content = implode("\n", $lines);

        // Adicionar permissões à matriz se especificadas
        if (!empty($permissions)) {
            $this->addPermissionsToMatrix($groupName, $permissions);
        }

        // Salvar o arquivo
        if (file_put_contents($configPath, $content) === false) {
            throw new \Exception('Não foi possível salvar o arquivo Config/AuthGroups.php');
        }

        // Limpar cache do OPcache se estiver ativo
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($configPath);
        }
    }

    /**
     * Atualizar o arquivo Config/AuthGroups.php permanentemente
     */
    private function updateAuthGroupsConfig($groupName, $title, $description, $permissions)
    {
        $configPath = APPPATH . 'Config/AuthGroups.php';
        
        if (!is_writable($configPath)) {
            throw new \Exception('Arquivo Config/AuthGroups.php não tem permissão de escrita');
        }

        // Ler o arquivo atual
        $content = file_get_contents($configPath);
        if ($content === false) {
            throw new \Exception('Não foi possível ler o arquivo Config/AuthGroups.php');
        }

        // Escapar caracteres especiais
        $escapedTitle = addslashes($title);
        $escapedDescription = addslashes($description);

        // Padrão mais flexível para capturar o grupo completo
        $pattern = "/'{$groupName}'\s*=>\s*\[\s*'title'\s*=>\s*'[^']*',\s*'description'\s*=>\s*'[^']*',?\s*\]/s";
        $replacement = "'{$groupName}' => [\n            'title'       => '{$escapedTitle}',\n            'description' => '{$escapedDescription}',\n        ]";
        
        // Tentar diferentes padrões se o primeiro não funcionar
        $patterns = [
            "/'{$groupName}'\s*=>\s*\[\s*'title'\s*=>\s*'[^']*',\s*'description'\s*=>\s*'[^']*',?\s*\]/s",
            "/'{$groupName}'\s*=>\s*\[[^\]]*\]/s",
            "/'{$groupName}'\s*=>\s*\[\s*'title'\s*=>[^,]+,\s*'description'\s*=>[^,\]]+[,\s]*\]/s"
        ];
        
        $updated = null;
        foreach ($patterns as $pattern) {
            $updated = preg_replace($pattern, $replacement, $content);
            if ($updated !== null && $updated !== $content) {
                break; // Sucesso com este padrão
            }
        }
        
        if ($updated === null || $updated === $content) {
            // Log do conteúdo para debug
            log_message('error', "Grupo {$groupName} não encontrado. Conteúdo do arquivo em torno do grupo:");
            $lines = explode("\n", $content);
            foreach ($lines as $i => $line) {
                if (strpos($line, $groupName) !== false) {
                    $start = max(0, $i - 2);
                    $end = min(count($lines) - 1, $i + 2);
                    for ($j = $start; $j <= $end; $j++) {
                        log_message('error', "Linha " . ($j + 1) . ": " . $lines[$j]);
                    }
                    break;
                }
            }
            throw new \Exception("Grupo '{$groupName}' não encontrado ou formato não reconhecido no arquivo Config/AuthGroups.php");
        }

        // Salvar o arquivo
        if (file_put_contents($configPath, $updated) === false) {
            throw new \Exception('Não foi possível salvar o arquivo Config/AuthGroups.php');
        }
        
        // Limpar cache de configuração se existir
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($configPath);
        }
    }

    /**
     * Excluir grupo permanentemente do arquivo Config/AuthGroups.php
     */
    private function deleteGroupFromConfig($groupName)
    {
        $configPath = APPPATH . 'Config/AuthGroups.php';
        
        if (!is_writable($configPath)) {
            throw new \Exception('Arquivo Config/AuthGroups.php não tem permissão de escrita');
        }

        // Ler o arquivo atual
        $content = file_get_contents($configPath);
        if ($content === false) {
            throw new \Exception('Não foi possível ler o arquivo Config/AuthGroups.php');
        }

        // Padrão mais preciso para remover o grupo e manter formatação
        $pattern = "/,?\s*'{$groupName}'\s*=>\s*\[\s*'title'\s*=>\s*'[^']*',\s*'description'\s*=>\s*'[^']*',?\s*\],?/s";
        
        $updated = preg_replace($pattern, '', $content);
        
        if ($updated === null || $updated === $content) {
            // Tentar padrão alternativo
            $pattern = "/'{$groupName}'\s*=>\s*\[[^\]]*\],?\s*/s";
            $updated = preg_replace($pattern, '', $content);
        }
        
        if ($updated === null || $updated === $content) {
            throw new \Exception("Grupo '{$groupName}' não encontrado no arquivo Config/AuthGroups.php");
        }

        // Remover também da matriz de permissões se existir
        $matrixPattern = "/'{$groupName}'\s*=>\s*\[[^\]]*\],?\s*/s";
        $updated = preg_replace($matrixPattern, '', $updated);

        // Limpar formatação: vírgulas duplas, espaços extras
        $updated = preg_replace('/,\s*,/', ',', $updated);
        $updated = preg_replace('/\],\s*\n\s*\'/', "],\n        '", $updated);
        $updated = preg_replace('/\]\s*,\s*\'/', "],\n        '", $updated);
        
        // Salvar o arquivo
        if (file_put_contents($configPath, $updated) === false) {
            throw new \Exception('Não foi possível salvar o arquivo Config/AuthGroups.php');
        }
        
        // Limpar cache de configuração se existir
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($configPath);
        }
    }

    /**
     * Adicionar permissões à matriz de um grupo
     */
    private function addPermissionsToMatrix($groupName, $permissions)
    {
        $configPath = APPPATH . 'Config/AuthGroups.php';

        if (!is_writable($configPath)) {
            throw new \Exception('Arquivo Config/AuthGroups.php não tem permissão de escrita');
        }

        // Ler o arquivo atual
        $content = file_get_contents($configPath);
        if ($content === false) {
            throw new \Exception('Não foi possível ler o arquivo Config/AuthGroups.php');
        }

        // Dividir o conteúdo em linhas para manipulação mais precisa
        $lines = explode("\n", $content);
        $matrixStart = -1;
        $matrixEnd = -1;

        // Encontrar os limites da array $matrix
        foreach ($lines as $i => $line) {
            if (strpos($line, 'public array $matrix = [') !== false) {
                $matrixStart = $i;
            }
            if ($matrixStart !== -1 && strpos($line, '];') !== false && $matrixEnd === -1) {
                $matrixEnd = $i;
                break;
            }
        }

        if ($matrixStart === -1 || $matrixEnd === -1) {
            throw new \Exception('Não foi possível encontrar a seção $matrix no arquivo');
        }

        // Preparar as permissões do grupo
        $permissionsList = "'" . implode("',\n            '", $permissions) . "'";
        $newMatrixLines = [
            "        '{$groupName}' => [",
            "            {$permissionsList},",
            "        ],"
        ];

        // Inserir o novo grupo na matriz antes do fechamento da array
        array_splice($lines, $matrixEnd, 0, $newMatrixLines);

        // Reconstruir o conteúdo
        $content = implode("\n", $lines);

        // Salvar o arquivo
        if (file_put_contents($configPath, $content) === false) {
            throw new \Exception('Não foi possível salvar o arquivo Config/AuthGroups.php');
        }

        // Limpar cache do OPcache se estiver ativo
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($configPath);
        }
    }
}