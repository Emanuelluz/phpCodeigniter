<?php

namespace Modules\Admin\Controllers;

use CodeIgniter\Controller;

class Permissions extends Controller
{
    protected $helpers = ['form', 'url'];

    public function index()
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        // Obter todas as permissões configuradas
        $permissions = setting('AuthGroups.permissions', []);
        
        // Obter matriz de grupos e permissões
        $authGroups = setting('AuthGroups.groups', []);
        $authMatrix = setting('AuthGroups.matrix', []);

        // Preparar matriz para exibição
        $matrix = [];
        foreach ($authGroups as $groupName => $groupConfig) {
            $matrix[$groupName] = [
                'title' => $groupConfig['title'] ?? $groupName,
                'permissions' => $authMatrix[$groupName] ?? []
            ];
        }

        return view('Modules\\Admin\\Views\\permissions\\index', [
            'permissions' => $permissions,
            'groups' => $authGroups,
            'matrix' => $matrix
        ]);
    }

    public function create()
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        return view('Modules\\Admin\\Views\\permissions\\create');
    }

    public function store()
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        // Validação
        $rules = [
            'name' => 'required|min_length[2]|max_length[50]|alpha_dash',
            'description' => 'required|min_length[2]|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');

        // Verificar se a permissão já existe
        $currentPermissions = setting('AuthGroups.permissions', []);
        if (isset($currentPermissions[$name])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Permissão já existe com este nome.');
        }

        try {
            // Adicionar nova permissão (simulado)
            session()->setFlashdata('success', 'Permissão criada com sucesso! Adicione a configuração ao arquivo Config/AuthGroups.php para tornar permanente.');
            session()->setFlashdata('permission_config', [
                'name' => $name,
                'description' => $description
            ]);

            return redirect()->to('/admin/permissions');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao criar permissão: ' . $e->getMessage());
        }
    }

    public function edit($permissionName)
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        // Verificar se a permissão existe
        $permissions = setting('AuthGroups.permissions', []);
        if (!isset($permissions[$permissionName])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $permission = [
            'name' => $permissionName,
            'description' => $permissions[$permissionName]
        ];

        return view('Modules\\Admin\\Views\\permissions\\edit', [
            'permission' => $permission
        ]);
    }

    public function update($permissionName)
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        // Verificar se a permissão existe
        $permissions = setting('AuthGroups.permissions', []);
        if (!isset($permissions[$permissionName])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Validação
        $rules = [
            'description' => 'required|min_length[2]|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $description = $this->request->getPost('description');

        try {
            // Atualizar permissão (simulado)
            session()->setFlashdata('success', 'Permissão atualizada com sucesso! Atualize o arquivo Config/AuthGroups.php para tornar permanente.');
            session()->setFlashdata('permission_update', [
                'name' => $permissionName,
                'description' => $description
            ]);

            return redirect()->to('/admin/permissions');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar permissão: ' . $e->getMessage());
        }
    }

    public function delete($permissionName)
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        // Verificar se a permissão existe
        $permissions = setting('AuthGroups.permissions', []);
        if (!isset($permissions[$permissionName])) {
            return redirect()->back()->with('error', 'Permissão não encontrada.');
        }

        // Verificar se a permissão está sendo usada por algum grupo
        $authMatrix = setting('AuthGroups.matrix', []);
        $groupsUsing = [];
        
        foreach ($authMatrix as $groupName => $groupPermissions) {
            if (in_array($permissionName, $groupPermissions)) {
                $groupsUsing[] = $groupName;
            }
        }

        if (!empty($groupsUsing)) {
            return redirect()->back()->with('error', "Não é possível excluir a permissão '{$permissionName}' pois ela está sendo usada pelos grupos: " . implode(', ', $groupsUsing));
        }

        try {
            // Remover permissão (simulado)
            session()->setFlashdata('success', 'Permissão removida com sucesso! Remova a configuração do arquivo Config/AuthGroups.php para tornar permanente.');
            session()->setFlashdata('permission_delete', $permissionName);

            return redirect()->to('/admin/permissions');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao excluir permissão: ' . $e->getMessage());
        }
    }

    public function matrix()
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        // Obter configurações
        $permissions = setting('AuthGroups.permissions', []);
        $authGroups = setting('AuthGroups.groups', []);
        $authMatrix = setting('AuthGroups.matrix', []);

        // Preparar matriz completa
        $matrix = [];
        foreach ($authGroups as $groupName => $groupConfig) {
            $matrix[$groupName] = [
                'title' => $groupConfig['title'] ?? $groupName,
                'description' => $groupConfig['description'] ?? '',
                'permissions' => []
            ];
            
            foreach ($permissions as $permissionName => $permissionDesc) {
                $matrix[$groupName]['permissions'][$permissionName] = in_array($permissionName, $authMatrix[$groupName] ?? []);
            }
        }

        return view('Modules\\Admin\\Views\\permissions\\matrix', [
            'permissions' => $permissions,
            'groups' => $authGroups,
            'matrix' => $matrix
        ]);
    }

    public function updateMatrix()
    {
        // Verificar se o usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }

        try {
            $matrixData = $this->request->getPost('matrix', []);
            
            // Processar dados da matriz
            $newMatrix = [];
            foreach ($matrixData as $groupName => $permissions) {
                $newMatrix[$groupName] = array_keys(array_filter($permissions, function($value) {
                    return $value === '1' || $value === 'on';
                }));
            }

            // Salvar matriz (simulado)
            session()->setFlashdata('success', 'Matriz de permissões atualizada com sucesso! Atualize o arquivo Config/AuthGroups.php para tornar permanente.');
            session()->setFlashdata('matrix_update', $newMatrix);

            return redirect()->to('/admin/permissions/matrix');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao atualizar matriz: ' . $e->getMessage());
        }
    }
}