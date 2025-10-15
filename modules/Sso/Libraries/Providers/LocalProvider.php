<?php

namespace Modules\Sso\Libraries\Providers;

/**
 * Local Authentication Provider
 * 
 * Provider para autenticação local usando banco de dados (padrão do Shield)
 */
class LocalProvider extends AbstractProvider
{
    protected string $name = 'local';
    protected string $type = 'local';

    /**
     * {@inheritdoc}
     */
    public function authenticate(array $credentials)
    {
        if (!$this->validateCredentials($credentials)) {
            $this->logAuthAttempt([
                'username'       => $credentials['username'] ?? '',
                'status'         => 'failed',
                'failure_reason' => 'Credenciais inválidas',
            ]);
            return false;
        }

        // Usar authenticador Session do Shield
        $authenticator = auth()->getAuthenticator();
        
        try {
            $result = $authenticator->attempt([
                'username' => $credentials['username'],
                'password' => $credentials['password'],
            ]);

            if (!$result->isOK()) {
                $this->logAuthAttempt([
                    'username'       => $credentials['username'],
                    'status'         => 'failed',
                    'failure_reason' => $result->reason(),
                ]);
                return false;
            }

            $user = $result->extraInfo();
            
            $this->logAuthAttempt([
                'user_id'  => $user->id,
                'username' => $credentials['username'],
                'status'   => 'success',
            ]);

            return $this->normalizeUserData([
                'username' => $user->username,
                'email'    => $user->email,
                'name'     => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                'active'   => $user->active,
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro na autenticação local: ' . $e->getMessage());
            
            $this->logAuthAttempt([
                'username'       => $credentials['username'],
                'status'         => 'failed',
                'failure_reason' => 'Erro interno: ' . $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(): bool
    {
        return $this->config['enabled'] ?? true;
    }

    /**
     * {@inheritdoc}
     */
    public function validateCredentials(array $credentials): bool
    {
        return !empty($credentials['username']) && !empty($credentials['password']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig(): array
    {
        return [
            'enabled'              => true,
            'allow_email_login'    => true,
            'allow_username_login' => true,
            'auto_create_users'    => false,
            'update_user_on_login' => false,
            'log_attempts'         => true,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function testConnection(): array
    {
        try {
            // Verificar se as tabelas do Shield existem
            $db = \Config\Database::connect();
            
            if (!$db->tableExists('users')) {
                return [
                    'success' => false,
                    'message' => 'Tabela de usuários não encontrada',
                ];
            }

            return [
                'success' => true,
                'message' => 'Provider local configurado corretamente',
                'details' => [
                    'database' => $db->database,
                    'users_count' => $db->table('users')->countAll(),
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao testar provider local: ' . $e->getMessage(),
            ];
        }
    }
}
