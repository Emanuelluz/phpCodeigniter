<?php

namespace Modules\Sso\Controllers;

use App\Controllers\BaseController;
use Modules\Sso\Models\ProviderModel;

/**
 * Provider Controller
 * 
 * CRUD de providers de autenticação SSO
 */
class ProviderController extends BaseController
{
    protected ProviderModel $model;

    public function __construct()
    {
        $this->model = new ProviderModel();
    }

    /**
     * Lista providers
     */
    public function index()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('login');
        }

        $data['providers'] = $this->model->orderBy('priority', 'ASC')->findAll();
        
        return view('Modules\Sso\Views\providers\index', $data);
    }

    /**
     * Form criar provider
     */
    public function create()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('login');
        }

        $data['types'] = ['local', 'ldap', 'oauth', 'saml'];
        
        return view('Modules\Sso\Views\providers\create', $data);
    }

    /**
     * Salvar provider
     */
    public function store()
    {
        if (!auth()->loggedIn()) {
            return $this->response->setJSON(['error' => 'Não autorizado'])->setStatusCode(401);
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[100]|is_unique[sso_providers.name]',
            'type' => 'required|in_list[local,ldap,oauth,saml]',
            'title' => 'required|min_length[2]|max_length[255]',
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

        $data = [
            'name' => $this->request->getPost('name'),
            'type' => $this->request->getPost('type'),
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'config' => $this->getConfigByType($this->request->getPost('type')),
            'is_enabled' => (bool) $this->request->getPost('is_enabled'),
            'is_default' => (bool) $this->request->getPost('is_default'),
            'priority' => (int) $this->request->getPost('priority', FILTER_SANITIZE_NUMBER_INT) ?: 0,
        ];

        if ($this->model->insert($data)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Provider criado com sucesso!'
                ]);
            }
            
            return redirect()->to('sso/admin/providers')
                ->with('success', 'Provider criado com sucesso!');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'error' => 'Erro ao criar provider'
            ])->setStatusCode(500);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Erro ao criar provider');
    }

    /**
     * Form editar provider
     */
    public function edit(int $id)
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('login');
        }

        $provider = $this->model->find($id);
        if (!$provider) {
            return redirect()->to('sso/admin/providers')
                ->with('error', 'Provider não encontrado');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($provider);
        }

        $data['provider'] = $provider;
        $data['types'] = ['local', 'ldap', 'oauth', 'saml'];
        
        return view('Modules\Sso\Views\providers\edit', $data);
    }

    /**
     * Atualizar provider
     */
    public function update(int $id)
    {
        if (!auth()->loggedIn()) {
            return $this->response->setJSON(['error' => 'Não autorizado'])->setStatusCode(401);
        }

        $provider = $this->model->find($id);
        if (!$provider) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Provider não encontrado'])->setStatusCode(404);
            }
            return redirect()->to('sso/admin/providers')
                ->with('error', 'Provider não encontrado');
        }

        $rules = [
            'title' => 'required|min_length[2]|max_length[255]',
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

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'config' => $this->getConfigByType($provider['type']),
            'priority' => (int) $this->request->getPost('priority', FILTER_SANITIZE_NUMBER_INT) ?: 0,
        ];

        if ($this->model->update($id, $data)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Provider atualizado com sucesso!'
                ]);
            }
            
            return redirect()->to('sso/admin/providers')
                ->with('success', 'Provider atualizado com sucesso!');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'error' => 'Erro ao atualizar provider'
            ])->setStatusCode(500);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Erro ao atualizar provider');
    }

    /**
     * Excluir provider
     */
    public function delete(int $id)
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('login');
        }

        $provider = $this->model->find($id);
        if (!$provider) {
            return redirect()->to('sso/admin/providers')
                ->with('error', 'Provider não encontrado');
        }

        // Não permitir excluir se for o único ativo
        $activeCount = $this->model->where('is_enabled', true)->countAllResults();
        if ($provider['is_enabled'] && $activeCount <= 1) {
            return redirect()->to('sso/admin/providers')
                ->with('error', 'Não é possível excluir o único provider ativo');
        }

        if ($this->model->delete($id)) {
            return redirect()->to('sso/admin/providers')
                ->with('success', 'Provider excluído com sucesso!');
        }

        return redirect()->to('sso/admin/providers')
            ->with('error', 'Erro ao excluir provider');
    }

    /**
     * Alternar status do provider
     */
    public function toggle(int $id)
    {
        if (!auth()->loggedIn()) {
            return $this->response->setJSON(['error' => 'Não autorizado'])->setStatusCode(401);
        }

        $provider = $this->model->find($id);
        if (!$provider) {
            return $this->response->setJSON(['error' => 'Provider não encontrado'])->setStatusCode(404);
        }

        // Se está ativo e é o único, não permitir desativar
        if ($provider['is_enabled']) {
            $activeCount = $this->model->where('is_enabled', true)->countAllResults();
            if ($activeCount <= 1) {
                return $this->response->setJSON([
                    'error' => 'Não é possível desativar o único provider ativo'
                ])->setStatusCode(400);
            }
        }

        if ($this->model->toggleStatus($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status alterado com sucesso!',
                'is_enabled' => !$provider['is_enabled']
            ]);
        }

        return $this->response->setJSON([
            'error' => 'Erro ao alterar status'
        ])->setStatusCode(500);
    }

    /**
     * Obter configuração baseada no tipo
     */
    private function getConfigByType(string $type): array
    {
        $config = [];

        switch ($type) {
            case 'ldap':
                $config = [
                    'host' => $this->request->getPost('ldap_host'),
                    'port' => (int) $this->request->getPost('ldap_port') ?: 389,
                    'base_dn' => $this->request->getPost('ldap_base_dn'),
                    'bind_dn' => $this->request->getPost('ldap_bind_dn'),
                    'bind_password' => $this->request->getPost('ldap_bind_password'),
                    'username_attr' => $this->request->getPost('ldap_username_attr') ?: 'sAMAccountName',
                    'email_attr' => $this->request->getPost('ldap_email_attr') ?: 'mail',
                    'name_attr' => $this->request->getPost('ldap_name_attr') ?: 'cn',
                    'use_tls' => (bool) $this->request->getPost('ldap_use_tls'),
                    'use_ssl' => (bool) $this->request->getPost('ldap_use_ssl'),
                ];
                break;

            case 'oauth':
                $config = [
                    'client_id' => $this->request->getPost('oauth_client_id'),
                    'client_secret' => $this->request->getPost('oauth_client_secret'),
                    'redirect_uri' => $this->request->getPost('oauth_redirect_uri'),
                    'scopes' => explode(',', $this->request->getPost('oauth_scopes') ?? ''),
                ];
                break;

            case 'saml':
                $config = [
                    'idp_entity_id' => $this->request->getPost('saml_idp_entity_id'),
                    'idp_sso_url' => $this->request->getPost('saml_idp_sso_url'),
                    'idp_certificate' => $this->request->getPost('saml_idp_certificate'),
                ];
                break;
        }

        return $config;
    }
}
