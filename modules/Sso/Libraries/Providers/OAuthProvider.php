<?php

namespace Modules\Sso\Libraries\Providers;

use Modules\Sso\Models\AuthLogModel;
use CodeIgniter\I18n\Time;

/**
 * OAuth Provider
 * 
 * Autenticação via OAuth 2.0 (Google, Microsoft, GitHub, etc)
 * 
 * Requer: 
 * composer require league/oauth2-client
 * composer require league/oauth2-google (para Google)
 */
class OAuthProvider extends AbstractProvider
{
    protected ?object $oauthClient = null;
    protected string $state = '';

    /**
     * Iniciar fluxo OAuth
     * 
     * @param array $options
     * @return string URL de autorização
     */
    public function getAuthorizationUrl(array $options = []): string
    {
        try {
            $client = $this->getClient();
            
            if (!$client) {
                throw new \Exception('Failed to create OAuth client');
            }

            // Gerar state para segurança CSRF
            $this->state = bin2hex(random_bytes(16));
            session()->set('oauth_state', $this->state);
            session()->set('oauth_provider_id', $this->provider['id']);

            // Obter URL de autorização
            $authUrl = $client->getAuthorizationUrl(array_merge([
                'state' => $this->state,
                'scope' => $this->config['scope'] ?? $this->getDefaultScope()
            ], $options));

            return $authUrl;

        } catch (\Exception $e) {
            log_message('error', 'OAuth authorization error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Processar callback OAuth
     * 
     * @param array $params Parâmetros retornados ($_GET)
     * @return array|false
     */
    public function authenticate(array $params)
    {
        try {
            // Validar state (CSRF protection)
            $sessionState = session()->get('oauth_state');
            $returnedState = $params['state'] ?? '';

            if (empty($returnedState) || $returnedState !== $sessionState) {
                $this->logFailure('unknown', 'Invalid OAuth state (CSRF)');
                return false;
            }

            // Verificar erro
            if (isset($params['error'])) {
                $error = $params['error'];
                $errorDesc = $params['error_description'] ?? 'Unknown error';
                $this->logFailure('unknown', "OAuth error: $error - $errorDesc");
                return false;
            }

            // Obter código de autorização
            $code = $params['code'] ?? '';
            
            if (empty($code)) {
                $this->logFailure('unknown', 'Missing authorization code');
                return false;
            }

            // Trocar código por access token
            $client = $this->getClient();
            $accessToken = $client->getAccessToken('authorization_code', [
                'code' => $code
            ]);

            if (!$accessToken) {
                $this->logFailure('unknown', 'Failed to get access token');
                return false;
            }

            // Obter dados do usuário
            $resourceOwner = $client->getResourceOwner($accessToken);
            $userData = $this->mapUserData($resourceOwner->toArray());

            // Armazenar token para refresh futuro
            session()->set('oauth_access_token', $accessToken->getToken());
            session()->set('oauth_refresh_token', $accessToken->getRefreshToken());
            session()->set('oauth_expires', $accessToken->getExpires());

            $this->logSuccess($userData['email'] ?? $userData['username'] ?? 'unknown', $userData);

            return $userData;

        } catch (\Exception $e) {
            log_message('error', 'OAuth callback error: ' . $e->getMessage());
            $this->logFailure('unknown', $e->getMessage());
            return false;
        } finally {
            // Limpar state da sessão
            session()->remove('oauth_state');
        }
    }

    /**
     * Renovar access token usando refresh token
     * 
     * @param string $refreshToken
     * @return array|false
     */
    public function refreshToken(string $refreshToken)
    {
        try {
            $client = $this->getClient();
            
            $newAccessToken = $client->getAccessToken('refresh_token', [
                'refresh_token' => $refreshToken
            ]);

            if (!$newAccessToken) {
                return false;
            }

            return [
                'access_token' => $newAccessToken->getToken(),
                'refresh_token' => $newAccessToken->getRefreshToken() ?? $refreshToken,
                'expires' => $newAccessToken->getExpires()
            ];

        } catch (\Exception $e) {
            log_message('error', 'OAuth refresh token error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obter cliente OAuth configurado
     * 
     * @return object|null
     */
    protected function getClient(): ?object
    {
        if ($this->oauthClient) {
            return $this->oauthClient;
        }

        $providerType = strtolower($this->config['oauth_provider'] ?? 'generic');

        try {
            switch ($providerType) {
                case 'google':
                    $this->oauthClient = $this->createGoogleClient();
                    break;
                
                case 'microsoft':
                case 'azure':
                    $this->oauthClient = $this->createMicrosoftClient();
                    break;
                
                case 'github':
                    $this->oauthClient = $this->createGithubClient();
                    break;
                
                default:
                    $this->oauthClient = $this->createGenericClient();
                    break;
            }

            return $this->oauthClient;

        } catch (\Exception $e) {
            log_message('error', 'Failed to create OAuth client: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Criar cliente Google OAuth
     * 
     * @return \League\OAuth2\Client\Provider\Google
     */
    protected function createGoogleClient(): object
    {
        if (!class_exists('\League\OAuth2\Client\Provider\Google')) {
            throw new \Exception('Google OAuth provider not installed. Run: composer require league/oauth2-google');
        }

        return new \League\OAuth2\Client\Provider\Google([
            'clientId' => $this->config['client_id'],
            'clientSecret' => $this->config['client_secret'],
            'redirectUri' => $this->config['redirect_uri'],
        ]);
    }

    /**
     * Criar cliente Microsoft/Azure OAuth
     * 
     * @return object
     */
    protected function createMicrosoftClient(): object
    {
        if (!class_exists('\TheNetworg\OAuth2\Client\Provider\Azure')) {
            throw new \Exception('Microsoft OAuth provider not installed. Run: composer require thenetworg/oauth2-azure');
        }

        return new \TheNetworg\OAuth2\Client\Provider\Azure([
            'clientId' => $this->config['client_id'],
            'clientSecret' => $this->config['client_secret'],
            'redirectUri' => $this->config['redirect_uri'],
            'tenant' => $this->config['tenant'] ?? 'common',
        ]);
    }

    /**
     * Criar cliente GitHub OAuth
     * 
     * @return object
     */
    protected function createGithubClient(): object
    {
        if (!class_exists('\League\OAuth2\Client\Provider\Github')) {
            throw new \Exception('GitHub OAuth provider not installed. Run: composer require league/oauth2-github');
        }

        return new \League\OAuth2\Client\Provider\Github([
            'clientId' => $this->config['client_id'],
            'clientSecret' => $this->config['client_secret'],
            'redirectUri' => $this->config['redirect_uri'],
        ]);
    }

    /**
     * Criar cliente OAuth genérico
     * 
     * @return \League\OAuth2\Client\Provider\GenericProvider
     */
    protected function createGenericClient(): object
    {
        return new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => $this->config['client_id'],
            'clientSecret' => $this->config['client_secret'],
            'redirectUri' => $this->config['redirect_uri'],
            'urlAuthorize' => $this->config['authorize_url'],
            'urlAccessToken' => $this->config['token_url'],
            'urlResourceOwnerDetails' => $this->config['userinfo_url'],
        ]);
    }

    /**
     * Mapear dados do resource owner para formato padrão
     * 
     * @param array $ownerData
     * @return array
     */
    protected function mapUserData(array $ownerData): array
    {
        $providerType = strtolower($this->config['oauth_provider'] ?? 'generic');

        $userData = [
            'provider' => $this->provider['name'],
            'provider_id' => $this->provider['id'],
        ];

        switch ($providerType) {
            case 'google':
                $userData['username'] = $ownerData['email'];
                $userData['email'] = $ownerData['email'];
                $userData['name'] = $ownerData['name'] ?? '';
                $userData['principal'] = $ownerData['sub'] ?? $ownerData['id'];
                $userData['avatar'] = $ownerData['picture'] ?? null;
                $userData['email_verified'] = $ownerData['email_verified'] ?? false;
                break;

            case 'microsoft':
            case 'azure':
                $userData['username'] = $ownerData['userPrincipalName'] ?? $ownerData['mail'];
                $userData['email'] = $ownerData['mail'] ?? $ownerData['userPrincipalName'];
                $userData['name'] = $ownerData['displayName'] ?? '';
                $userData['principal'] = $ownerData['id'];
                break;

            case 'github':
                $userData['username'] = $ownerData['login'];
                $userData['email'] = $ownerData['email'] ?? $ownerData['login'] . '@github.com';
                $userData['name'] = $ownerData['name'] ?? $ownerData['login'];
                $userData['principal'] = $ownerData['id'];
                $userData['avatar'] = $ownerData['avatar_url'] ?? null;
                break;

            default:
                // Mapping genérico
                $mapping = $this->config['user_mapping'] ?? [
                    'username' => 'email',
                    'email' => 'email',
                    'name' => 'name',
                    'principal' => 'id'
                ];

                foreach ($mapping as $key => $ownerKey) {
                    $userData[$key] = $ownerData[$ownerKey] ?? '';
                }
                break;
        }

        return $userData;
    }

    /**
     * Obter scope padrão baseado no provider
     * 
     * @return string
     */
    protected function getDefaultScope(): string
    {
        $providerType = strtolower($this->config['oauth_provider'] ?? 'generic');

        $defaultScopes = [
            'google' => 'openid email profile',
            'microsoft' => 'openid email profile User.Read',
            'azure' => 'openid email profile User.Read',
            'github' => 'user:email',
        ];

        return $defaultScopes[$providerType] ?? 'openid email profile';
    }

    /**
     * Validar configuração OAuth
     * 
     * @return bool
     */
    protected function validateConfig(): bool
    {
        $required = ['client_id', 'client_secret', 'redirect_uri'];

        foreach ($required as $field) {
            if (empty($this->config[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Testar configuração OAuth
     * 
     * @return array
     */
    public function testConnection(): array
    {
        $result = [
            'success' => false,
            'message' => '',
            'details' => []
        ];

        try {
            if (!$this->validateConfig()) {
                $result['message'] = 'Configuração OAuth incompleta';
                return $result;
            }

            $client = $this->getClient();

            if (!$client) {
                $result['message'] = 'Falha ao criar cliente OAuth';
                return $result;
            }

            $result['success'] = true;
            $result['message'] = 'Cliente OAuth criado com sucesso';
            $result['details']['provider'] = $this->config['oauth_provider'];
            $result['details']['client_id'] = substr($this->config['client_id'], 0, 10) . '...';
            $result['details']['redirect_uri'] = $this->config['redirect_uri'];
            $result['details']['authorization_url'] = 'Ready';

        } catch (\Exception $e) {
            $result['message'] = 'Erro: ' . $e->getMessage();
        }

        return $result;
    }
}
