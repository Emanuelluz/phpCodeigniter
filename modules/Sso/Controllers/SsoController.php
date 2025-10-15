<?php

namespace Modules\Sso\Controllers;

use App\Controllers\BaseController;
use Modules\Sso\Models\ProviderModel;
use Modules\Sso\Models\AuthLogModel;
use Modules\Sso\Config\SsoConfig;

/**
 * SSO Controller
 * 
 * Controla autenticação SSO e login de usuários
 */
class SsoController extends BaseController
{
    protected ProviderModel $providerModel;
    protected AuthLogModel $logModel;
    protected SsoConfig $config;
    protected $helpers = ['auth'];

    public function __construct()
    {
        $this->providerModel = new ProviderModel();
        $this->logModel = new AuthLogModel();
        $this->config = new SsoConfig();
    }

    /**
     * Página de login SSO
     */
    public function login()
    {
        // Carregar helper auth
        helper('auth');
        
        // Se já está logado, redireciona
        if (auth()->loggedIn()) {
            return redirect()->to($this->config->loginRedirect);
        }

        // Buscar providers ativos
        $providers = $this->providerModel->getActiveProviders();

        // Se há apenas um provider ativo, mostrar form direto
        if (count($providers) === 1) {
            $data['provider'] = $providers[0];
            $data['show_provider_selection'] = false;
        } else {
            $data['providers'] = $providers;
            $data['show_provider_selection'] = true;
        }

        $data['config'] = $this->config;

        return view('Modules\Sso\Views\login', $data);
    }

    /**
     * Processa autenticação
     */
    public function authenticate()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
            'provider_id' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Preencha todos os campos obrigatórios');
        }

        $providerId = $this->request->getPost('provider_id');
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        // Verificar rate limiting
        if ($this->isRateLimited($username)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Muitas tentativas de login. Tente novamente em alguns minutos.');
        }

        // Buscar provider
        $providerData = $this->providerModel->find($providerId);
        if (!$providerData || !$providerData['is_enabled']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Provider de autenticação inválido ou desabilitado');
        }

        // Instanciar provider
        try {
            $providerClass = $providerData['config']['class'] ?? $this->config->providers[$providerData['type']]['class'];
            $provider = new $providerClass($providerData['config']);

            // Autenticar
            $userData = $provider->authenticate([
                'username' => $username,
                'password' => $password,
            ]);

            if (!$userData) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Credenciais inválidas');
            }

            // Buscar ou criar usuário
            $user = $this->getOrCreateUser($userData);
            if (!$user) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Erro ao processar usuário');
            }

            // Login via Shield
            auth()->login($user, $remember);

            // Log de sucesso
            $this->logModel->logAttempt([
                'user_id' => $user->id,
                'provider_id' => $providerId,
                'provider_type' => $providerData['type'],
                'username' => $username,
                'status' => 'success',
            ]);

            // Redirecionar
            return redirect()->to($this->config->loginRedirect)
                ->with('success', 'Login realizado com sucesso!');

        } catch (\Exception $e) {
            log_message('error', 'Erro na autenticação SSO: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao processar autenticação: ' . $e->getMessage());
        }
    }

    /**
     * Callback para providers externos (OAuth, SAML)
     */
    public function callback(string $providerName)
    {
        // Buscar provider por nome
        $providerData = $this->providerModel->getByName($providerName);
        
        if (!$providerData || !$providerData['is_enabled']) {
            return redirect()->to('sso/login')
                ->with('error', 'Provider não encontrado ou desabilitado');
        }

        try {
            $providerClass = $providerData['config']['class'] ?? $this->config->providers[$providerData['type']]['class'];
            $provider = new $providerClass($providerData['config']);

            // Processar callback
            $userData = $provider->handleCallback($this->request);

            if (!$userData) {
                return redirect()->to('sso/login')
                    ->with('error', 'Erro ao processar callback do provider');
            }

            // Buscar ou criar usuário
            $user = $this->getOrCreateUser($userData);
            if (!$user) {
                return redirect()->to('sso/login')
                    ->with('error', 'Erro ao processar usuário');
            }

            // Login
            auth()->login($user);

            // Log
            $this->logModel->logAttempt([
                'user_id' => $user->id,
                'provider_id' => $providerData['id'],
                'provider_type' => $providerData['type'],
                'username' => $userData['username'] ?? $userData['email'],
                'status' => 'success',
            ]);

            return redirect()->to($this->config->loginRedirect)
                ->with('success', 'Login realizado com sucesso!');

        } catch (\Exception $e) {
            log_message('error', 'Erro no callback SSO: ' . $e->getMessage());
            
            return redirect()->to('sso/login')
                ->with('error', 'Erro ao processar autenticação: ' . $e->getMessage());
        }
    }

    /**
     * Logout SSO
     */
    public function logout()
    {
        if (auth()->loggedIn()) {
            auth()->logout();
        }

        return redirect()->to($this->config->logoutRedirect)
            ->with('success', 'Logout realizado com sucesso!');
    }

    /**
     * Buscar ou criar usuário
     */
    private function getOrCreateUser(array $userData)
    {
        $userProvider = auth()->getProvider();
        
        // Tentar encontrar por email
        $user = $userProvider->findByCredentials(['email' => $userData['email']]);
        
        if (!$user && !empty($userData['username'])) {
            // Tentar por username
            $user = $userProvider->findByCredentials(['username' => $userData['username']]);
        }

        // Criar se não existe e está configurado
        if (!$user && $this->config->autoCreateUsers) {
            $newUserData = [
                'username' => $userData['username'] ?? explode('@', $userData['email'])[0],
                'email' => $userData['email'],
                'password' => bin2hex(random_bytes(32)), // Senha aleatória
                'active' => 1,
            ];

            if (!empty($userData['name'])) {
                $nameParts = explode(' ', $userData['name'], 2);
                $newUserData['first_name'] = $nameParts[0];
                $newUserData['last_name'] = $nameParts[1] ?? '';
            }

            $user = $userProvider->create($newUserData);
            
            if ($user) {
                $user->addGroup($this->config->defaultGroup);
            }
        }

        // Atualizar dados se configurado
        if ($user && $this->config->updateUserOnLogin) {
            $updates = [];
            
            if (!empty($userData['email']) && $user->email !== $userData['email']) {
                $updates['email'] = $userData['email'];
            }
            
            if (!empty($userData['name'])) {
                $nameParts = explode(' ', $userData['name'], 2);
                if ($user->first_name !== $nameParts[0]) {
                    $updates['first_name'] = $nameParts[0];
                }
                if (isset($nameParts[1]) && $user->last_name !== $nameParts[1]) {
                    $updates['last_name'] = $nameParts[1];
                }
            }

            if (!empty($updates)) {
                $userProvider->update($user->id, $updates);
            }
        }

        return $user;
    }

    /**
     * Verifica rate limiting
     */
    private function isRateLimited(string $username): bool
    {
        if (!$this->config->enableRateLimiting) {
            return false;
        }

        $failureCount = $this->logModel->countRecentFailures(
            $username,
            $this->config->lockoutDuration / 60
        );

        return $failureCount >= $this->config->maxLoginAttempts;
    }
}
