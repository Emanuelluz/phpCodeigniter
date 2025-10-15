<?php

namespace Modules\Sso\Libraries\Providers;

use CodeIgniter\Shield\Entities\User;
use Modules\Sso\Models\AuthLogModel;

/**
 * Abstract Authentication Provider
 * 
 * Classe base para todos os providers de autenticação
 */
abstract class AbstractProvider
{
    /**
     * Configuração do provider
     */
    protected array $config = [];

    /**
     * Nome do provider
     */
    protected string $name = '';

    /**
     * Tipo do provider
     */
    protected string $type = '';

    /**
     * Model de logs
     */
    protected ?AuthLogModel $logModel = null;

    /**
     * Constructor
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->getDefaultConfig(), $config);
        $this->logModel = new AuthLogModel();
    }

    /**
     * Autenticar usuário
     * 
     * @param array $credentials Credenciais de autenticação
     * @return array|false Dados do usuário ou false
     */
    abstract public function authenticate(array $credentials);

    /**
     * Verificar se o provider está habilitado
     */
    abstract public function isEnabled(): bool;

    /**
     * Validar credenciais
     */
    abstract public function validateCredentials(array $credentials): bool;

    /**
     * Obter configuração padrão
     */
    abstract protected function getDefaultConfig(): array;

    /**
     * Normalizar dados do usuário
     * 
     * Converte dados do provider para formato padrão
     */
    protected function normalizeUserData(array $data): array
    {
        return [
            'username' => $data['username'] ?? '',
            'email'    => $data['email'] ?? '',
            'name'     => $data['name'] ?? '',
            'active'   => $data['active'] ?? true,
            'provider' => $this->type,
            'extra'    => $data['extra'] ?? [],
        ];
    }

    /**
     * Log de tentativa de autenticação
     */
    protected function logAuthAttempt(array $data): void
    {
        if (!isset($this->config['log_attempts']) || $this->config['log_attempts']) {
            $this->logModel->logAttempt(array_merge([
                'provider_type' => $this->type,
            ], $data));
        }
    }

    /**
     * Obter ou criar usuário
     * 
     * Busca usuário existente ou cria novo com base nos dados do provider
     */
    protected function getOrCreateUser(array $userData): ?User
    {
        $userProvider = auth()->getProvider();
        
        // Tentar encontrar por email
        $user = $userProvider->findByCredentials(['email' => $userData['email']]);
        
        if (!$user) {
            // Tentar encontrar por username
            $user = $userProvider->findByCredentials(['username' => $userData['username']]);
        }

        // Se não encontrou e pode criar automaticamente
        if (!$user && ($this->config['auto_create_users'] ?? true)) {
            $user = $this->createUser($userData);
        }

        // Atualizar dados do usuário se configurado
        if ($user && ($this->config['update_user_on_login'] ?? true)) {
            $this->updateUser($user, $userData);
        }

        return $user;
    }

    /**
     * Criar novo usuário
     */
    protected function createUser(array $userData): ?User
    {
        $userProvider = auth()->getProvider();
        
        $newUserData = [
            'username' => $userData['username'],
            'email'    => $userData['email'],
            'active'   => $userData['active'] ?? true,
        ];

        // Adicionar nome se disponível
        if (!empty($userData['name'])) {
            // Dividir nome completo em first_name e last_name
            $nameParts = explode(' ', $userData['name'], 2);
            $newUserData['first_name'] = $nameParts[0];
            $newUserData['last_name'] = $nameParts[1] ?? '';
        }

        // Senha aleatória para usuários SSO
        $newUserData['password'] = bin2hex(random_bytes(32));

        try {
            $user = $userProvider->create($newUserData);
            
            // Adicionar ao grupo padrão
            if ($user && isset($this->config['default_group'])) {
                $user->addGroup($this->config['default_group']);
            }

            return $user;
        } catch (\Exception $e) {
            log_message('error', 'Erro ao criar usuário SSO: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Atualizar dados do usuário
     */
    protected function updateUser(User $user, array $userData): bool
    {
        $updates = [];

        // Atualizar email se mudou
        if (!empty($userData['email']) && $user->email !== $userData['email']) {
            $updates['email'] = $userData['email'];
        }

        // Atualizar nome se mudou
        if (!empty($userData['name'])) {
            $nameParts = explode(' ', $userData['name'], 2);
            if ($user->first_name !== $nameParts[0]) {
                $updates['first_name'] = $nameParts[0];
            }
            if (isset($nameParts[1]) && $user->last_name !== $nameParts[1]) {
                $updates['last_name'] = $nameParts[1];
            }
        }

        // Atualizar status ativo
        if (isset($userData['active']) && $user->active !== $userData['active']) {
            $updates['active'] = $userData['active'];
        }

        if (empty($updates)) {
            return true;
        }

        try {
            $userProvider = auth()->getProvider();
            return $userProvider->update($user->id, $updates);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar usuário SSO: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Testar conexão com o provider
     */
    public function testConnection(): array
    {
        return [
            'success' => false,
            'message' => 'Teste não implementado para este provider',
        ];
    }

    /**
     * Obter nome do provider
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Obter tipo do provider
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Obter configuração
     */
    public function getConfig(string $key = null)
    {
        if ($key === null) {
            return $this->config;
        }

        return $this->config[$key] ?? null;
    }

    /**
     * Definir configuração
     */
    public function setConfig(string $key, $value): void
    {
        $this->config[$key] = $value;
    }
}
