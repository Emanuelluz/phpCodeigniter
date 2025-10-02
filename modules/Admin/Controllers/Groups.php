<?php

namespace Modules\Admin\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Shield\Authorization\AuthorizationException;

class Groups extends Controller
{
    protected $helpers = ['form', 'url'];

    public function index()
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to(url_to('login'));
        }

        // Obter todos os grupos configurados - com fallback para array vazio
        $authGroups = setting('AuthGroups.groups');
        $authMatrix = setting('AuthGroups.matrix');
        
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
                
                $groups[] = [
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
        $permissions = setting('AuthGroups.permissions', []);

        return view('Modules\\Admin\\Views\\groups\\create', [
            'permissions' => $permissions
        ]);
    }

    public function store()
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to(url_to('login'));
        }

        // Validação
        $rules = [
            'name' => 'required|min_length[2]|max_length[50]|alpha_dash',
            'title' => 'required|min_length[2]|max_length[100]',
            'description' => 'max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $name = $this->request->getPost('name');
        $title = $this->request->getPost('title');
        $description = $this->request->getPost('description');
        $permissions = $this->request->getPost('permissions', []);

        // Verificar se o grupo já existe
        $currentGroups = setting('AuthGroups.groups', []);
        if (isset($currentGroups[$name])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Grupo já existe com este nome.');
        }

        try {
            // Adicionar novo grupo
            $currentGroups[$name] = [
                'title' => $title,
                'description' => $description
            ];

            // Atualizar matriz de permissões
            $currentMatrix = setting('AuthGroups.matrix', []);
            $currentMatrix[$name] = $permissions;

            // Salvar configurações (simulado - em produção usaria um sistema de configuração dinâmica)
            // Por enquanto, mostramos instruções para adicionar manualmente
            
            session()->setFlashdata('success', 'Grupo criado com sucesso! Adicione a configuração ao arquivo Config/AuthGroups.php para tornar permanente.');
            session()->setFlashdata('group_config', [
                'name' => $name,
                'config' => $currentGroups[$name],
                'permissions' => $permissions
            ]);

            return redirect()->to(base_url('admin/groups'));

        } catch (\Exception $e) {
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

        // Verificar se o grupo existe
        $authGroups = setting('AuthGroups.groups', []);
        if (!isset($authGroups[$groupName])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $group = $authGroups[$groupName];
        $group['name'] = $groupName;

        // Obter permissões do grupo
        $authMatrix = setting('AuthGroups.matrix', []);
        $groupPermissions = $authMatrix[$groupName] ?? [];

        // Obter todas as permissões disponíveis
        $allPermissions = setting('AuthGroups.permissions', []);

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
            return redirect()->to(url_to('login'));
        }

        // Verificar se o grupo existe
        $authGroups = setting('AuthGroups.groups', []);
        if (!isset($authGroups[$groupName])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Validação
        $rules = [
            'title' => 'required|min_length[2]|max_length[100]',
            'description' => 'max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $title = $this->request->getPost('title');
        $description = $this->request->getPost('description');
        $permissions = $this->request->getPost('permissions', []);

        try {
            // Atualizar grupo (simulado)
            session()->setFlashdata('success', 'Grupo atualizado com sucesso! Atualize o arquivo Config/AuthGroups.php para tornar permanente.');
            session()->setFlashdata('group_update', [
                'name' => $groupName,
                'config' => [
                    'title' => $title,
                    'description' => $description
                ],
                'permissions' => $permissions
            ]);

            return redirect()->to(base_url('admin/groups'));

        } catch (\Exception $e) {
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
        $authGroups = setting('AuthGroups.groups', []);
        if (!isset($authGroups[$groupName])) {
            return redirect()->back()->with('error', 'Grupo não encontrado.');
        }

        // Verificar se há usuários neste grupo
        $userCount = $this->countUsersInGroup($groupName);

        if ($userCount > 0) {
            return redirect()->back()->with('error', "Não é possível excluir o grupo '{$groupName}' pois há {$userCount} usuário(s) associado(s).");
        }

        try {
            // Remover grupo (simulado)
            session()->setFlashdata('success', 'Grupo removido com sucesso! Remova a configuração do arquivo Config/AuthGroups.php para tornar permanente.');
            session()->setFlashdata('group_delete', $groupName);

            return redirect()->to(base_url('admin/groups'));

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

        // Verificar se o grupo existe
        $authGroups = setting('AuthGroups.groups', []);
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
}