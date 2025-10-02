<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;

class Users extends BaseController
{
    public function index()
    {
        $userProvider = auth()->getProvider();
        
        // Parâmetros de busca e paginação
        $search = $this->request->getGet('search');
        $group = $this->request->getGet('group');
        $status = $this->request->getGet('status');
        $perPage = 10;
        
        // Query base
        $query = $userProvider->withIdentities()->withGroups();
        
        // Aplicar filtros
        if ($search) {
            $query->groupStart()
                  ->like('username', $search)
                  ->orLike('email', $search)
                  ->groupEnd();
        }
        
        if ($status !== null && $status !== '') {
            $query->where('active', (int)$status);
        }
        
        // Paginação
        $users = $query->paginate($perPage);
        $pager = $userProvider->pager;
        
        return view('Modules\\Admin\\Views\\users\\index', [
            'users' => $users,
            'pager' => $pager,
            'search' => $search,
            'group' => $group,
            'status' => $status
        ]);
    }
    
    public function create()
    {
        // Obter grupos disponíveis
        $groups = setting('AuthGroups.groups', []);
        
        return view('Modules\\Admin\\Views\\users\\create', [
            'groups' => array_keys($groups)
        ]);
    }
    
    public function store()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[auth_identities.secret]',
            'password' => 'required|min_length[8]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $userProvider = auth()->getProvider();
        
        $user = new \CodeIgniter\Shield\Entities\User([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
        ]);
        
        if ($userProvider->save($user)) {
            // Adicionar grupos se especificados
            $groups = $this->request->getPost('groups');
            if ($groups) {
                $user = $userProvider->findById($userProvider->getInsertID());
                $user->syncGroups(...$groups);
            }
            
            return redirect()->to('/admin/users')->with('success', 'Usuário criado com sucesso!');
        }
        
        return redirect()->back()->withInput()->with('error', 'Erro ao criar usuário.');
    }
    
    public function edit($id)
    {
        $userProvider = auth()->getProvider();
        $user = $userProvider->withGroups()->findById($id);
        
        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        // Obter grupos disponíveis
        $groups = setting('AuthGroups.groups', []);
        
        return view('Modules\\Admin\\Views\\users\\edit', [
            'user' => $user,
            'groups' => array_keys($groups)
        ]);
    }
    
    public function update($id)
    {
        $userProvider = auth()->getProvider();
        $user = $userProvider->findById($id);
        
        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        
        $rules = [
            'username' => "required|min_length[3]|max_length[30]|is_unique[users.username,id,{$id}]",
            'email'    => "required|valid_email|is_unique[auth_identities.secret,user_id,{$id}]",
        ];
        
        // Se senha foi fornecida, validar
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[8]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $user->fill([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'active'   => (bool)$this->request->getPost('active'),
        ]);
        
        // Atualizar senha se fornecida
        if ($this->request->getPost('password')) {
            $user->password = $this->request->getPost('password');
        }
        
        if ($userProvider->save($user)) {
            // Atualizar grupos
            $groups = $this->request->getPost('groups') ?? [];
            $user->syncGroups(...$groups);
            
            return redirect()->to('/admin/users')->with('success', 'Usuário atualizado com sucesso!');
        }
        
        return redirect()->back()->withInput()->with('error', 'Erro ao atualizar usuário.');
    }
    
    public function delete($id)
    {
        $userProvider = auth()->getProvider();
        $user = $userProvider->findById($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Usuário não encontrado.');
        }
        
        // Não permitir deletar o próprio usuário
        if ($user->id === auth()->id()) {
            return redirect()->to('/admin/users')->with('error', 'Você não pode deletar sua própria conta.');
        }
        
        if ($userProvider->delete($id, true)) {
            return redirect()->to('/admin/users')->with('success', 'Usuário excluído com sucesso!');
        }
        
        return redirect()->to('/admin/users')->with('error', 'Erro ao excluir usuário.');
    }
    
    public function toggleStatus($id)
    {
        $userProvider = auth()->getProvider();
        $user = $userProvider->findById($id);
        
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'Usuário não encontrado']);
        }
        
        // Não permitir desativar o próprio usuário
        if ($user->id === auth()->id()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Você não pode desativar sua própria conta']);
        }
        
        $user->active = !$user->active;
        
        if ($userProvider->save($user)) {
            $status = $user->active ? 'ativado' : 'desativado';
            return $this->response->setJSON(['success' => true, 'message' => "Usuário {$status} com sucesso", 'active' => $user->active]);
        }
        
        return $this->response->setJSON(['success' => false, 'message' => 'Erro ao alterar status do usuário']);
    }
}