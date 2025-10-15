<?php

namespace Modules\Sso\Libraries\Providers;

use Modules\Sso\Models\AuthLogModel;
use CodeIgniter\I18n\Time;

/**
 * SAML Provider
 * 
 * Autenticação via SAML 2.0 para enterprise SSO
 * 
 * Requer: composer require onelogin/php-saml
 */
class SamlProvider extends AbstractProvider
{
    protected ?object $samlAuth = null;

    /**
     * Iniciar autenticação SAML
     * 
     * @param array $options
     * @return void Redireciona para IdP
     */
    public function login(array $options = []): void
    {
        try {
            $auth = $this->getSamlAuth();
            
            if (!$auth) {
                throw new \Exception('Failed to initialize SAML');
            }

            // Salvar URL de retorno
            $returnTo = $options['return_to'] ?? base_url();
            session()->set('saml_return_to', $returnTo);
            session()->set('saml_provider_id', $this->provider['id']);

            // Iniciar login SSO
            $auth->login($returnTo);

        } catch (\Exception $e) {
            log_message('error', 'SAML login error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Processar resposta SAML (ACS - Assertion Consumer Service)
     * 
     * @param array $post Dados POST do IdP ($_POST)
     * @return array|false
     */
    public function authenticate(array $post = [])
    {
        try {
            $auth = $this->getSamlAuth();
            
            if (!$auth) {
                $this->logFailure('unknown', 'Failed to initialize SAML');
                return false;
            }

            // Processar resposta SAML
            $auth->processResponse();

            // Verificar erros
            $errors = $auth->getErrors();
            
            if (!empty($errors)) {
                $errorMsg = implode(', ', $errors);
                $this->logFailure('unknown', "SAML errors: $errorMsg");
                log_message('error', 'SAML authentication errors: ' . $errorMsg);
                return false;
            }

            // Verificar se está autenticado
            if (!$auth->isAuthenticated()) {
                $this->logFailure('unknown', 'SAML authentication failed');
                return false;
            }

            // Obter atributos do usuário
            $attributes = $auth->getAttributes();
            $nameId = $auth->getNameId();
            $nameIdFormat = $auth->getNameIdFormat();
            $sessionIndex = $auth->getSessionIndex();

            // Mapear atributos para dados do usuário
            $userData = $this->mapSamlAttributes($attributes, $nameId);

            // Salvar informações de sessão SAML para logout
            session()->set('saml_name_id', $nameId);
            session()->set('saml_name_id_format', $nameIdFormat);
            session()->set('saml_session_index', $sessionIndex);

            $this->logSuccess($userData['email'] ?? $userData['username'] ?? 'unknown', $userData);

            return $userData;

        } catch (\Exception $e) {
            log_message('error', 'SAML authentication error: ' . $e->getMessage());
            $this->logFailure('unknown', $e->getMessage());
            return false;
        }
    }

    /**
     * Iniciar logout SAML (Single Logout)
     * 
     * @param array $options
     * @return void Redireciona para IdP
     */
    public function logout(array $options = []): void
    {
        try {
            $auth = $this->getSamlAuth();
            
            if (!$auth) {
                throw new \Exception('Failed to initialize SAML');
            }

            $returnTo = $options['return_to'] ?? base_url();
            $nameId = session()->get('saml_name_id');
            $sessionIndex = session()->get('saml_session_index');
            $nameIdFormat = session()->get('saml_name_id_format');

            // Limpar sessão SAML
            session()->remove('saml_name_id');
            session()->remove('saml_name_id_format');
            session()->remove('saml_session_index');

            // Iniciar logout SSO
            $auth->logout($returnTo, [], $nameId, $sessionIndex, false, $nameIdFormat);

        } catch (\Exception $e) {
            log_message('error', 'SAML logout error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Processar resposta de logout SAML (SLS - Single Logout Service)
     * 
     * @return bool
     */
    public function processLogoutResponse(): bool
    {
        try {
            $auth = $this->getSamlAuth();
            
            if (!$auth) {
                return false;
            }

            // Processar resposta de logout
            $auth->processSLO();

            $errors = $auth->getErrors();
            
            if (!empty($errors)) {
                log_message('error', 'SAML logout errors: ' . implode(', ', $errors));
                return false;
            }

            return true;

        } catch (\Exception $e) {
            log_message('error', 'SAML logout processing error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obter instância SAML Auth
     * 
     * @return object|null OneLogin\Saml2\Auth
     */
    protected function getSamlAuth(): ?object
    {
        if ($this->samlAuth) {
            return $this->samlAuth;
        }

        if (!class_exists('\OneLogin\Saml2\Auth')) {
            log_message('error', 'OneLogin SAML library not installed. Run: composer require onelogin/php-saml');
            return null;
        }

        try {
            $settings = $this->buildSamlSettings();
            $this->samlAuth = new \OneLogin\Saml2\Auth($settings);
            
            return $this->samlAuth;

        } catch (\Exception $e) {
            log_message('error', 'Failed to create SAML Auth: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Construir configurações SAML
     * 
     * @return array
     */
    protected function buildSamlSettings(): array
    {
        $baseUrl = base_url();
        $acsUrl = $this->config['acs_url'] ?? $baseUrl . '/sso/saml/acs';
        $slsUrl = $this->config['sls_url'] ?? $baseUrl . '/sso/saml/sls';
        $entityId = $this->config['sp_entity_id'] ?? $baseUrl;

        return [
            // SP (Service Provider) settings
            'sp' => [
                'entityId' => $entityId,
                'assertionConsumerService' => [
                    'url' => $acsUrl,
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                ],
                'singleLogoutService' => [
                    'url' => $slsUrl,
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ],
                'NameIDFormat' => $this->config['nameid_format'] ?? 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
                'x509cert' => $this->config['sp_certificate'] ?? '',
                'privateKey' => $this->config['sp_private_key'] ?? '',
            ],
            
            // IdP (Identity Provider) settings
            'idp' => [
                'entityId' => $this->config['idp_entity_id'],
                'singleSignOnService' => [
                    'url' => $this->config['idp_sso_url'],
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ],
                'singleLogoutService' => [
                    'url' => $this->config['idp_slo_url'] ?? '',
                    'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                ],
                'x509cert' => $this->config['idp_certificate'],
            ],

            // Security settings
            'security' => [
                'nameIdEncrypted' => $this->config['encrypt_nameid'] ?? false,
                'authnRequestsSigned' => $this->config['sign_authn_request'] ?? false,
                'logoutRequestSigned' => $this->config['sign_logout_request'] ?? false,
                'logoutResponseSigned' => $this->config['sign_logout_response'] ?? false,
                'signMetadata' => $this->config['sign_metadata'] ?? false,
                'wantMessagesSigned' => $this->config['want_messages_signed'] ?? false,
                'wantAssertionsSigned' => $this->config['want_assertions_signed'] ?? false,
                'wantAssertionsEncrypted' => $this->config['want_assertions_encrypted'] ?? false,
                'wantNameIdEncrypted' => $this->config['want_nameid_encrypted'] ?? false,
                'requestedAuthnContext' => $this->config['requested_authn_context'] ?? true,
                'signatureAlgorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
                'digestAlgorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256',
            ],

            // Compression
            'compress' => [
                'requests' => true,
                'responses' => true,
            ],
        ];
    }

    /**
     * Mapear atributos SAML para dados do usuário
     * 
     * @param array $attributes
     * @param string $nameId
     * @return array
     */
    protected function mapSamlAttributes(array $attributes, string $nameId): array
    {
        $mapping = $this->config['attribute_mapping'] ?? [
            'username' => 'uid',
            'email' => 'mail',
            'name' => 'displayName',
            'groups' => 'memberOf'
        ];

        $userData = [
            'provider' => $this->provider['name'],
            'provider_id' => $this->provider['id'],
            'principal' => $nameId,
        ];

        foreach ($mapping as $userKey => $samlKey) {
            if (isset($attributes[$samlKey])) {
                $value = $attributes[$samlKey];
                
                // SAML attributes são sempre arrays
                if (is_array($value)) {
                    // Para grupos, manter como array
                    if ($userKey === 'groups') {
                        $userData[$userKey] = $value;
                    } else {
                        // Para outros, pegar primeiro valor
                        $userData[$userKey] = $value[0] ?? '';
                    }
                } else {
                    $userData[$userKey] = $value;
                }
            }
        }

        // Fallback: usar nameId como email se não houver email
        if (empty($userData['email'])) {
            $userData['email'] = $nameId;
        }

        return $userData;
    }

    /**
     * Obter metadata SAML SP
     * 
     * @return string XML metadata
     */
    public function getMetadata(): string
    {
        try {
            $auth = $this->getSamlAuth();
            
            if (!$auth) {
                return '';
            }

            $settings = $auth->getSettings();
            $metadata = $settings->getSPMetadata();
            $errors = $settings->validateMetadata($metadata);

            if (!empty($errors)) {
                log_message('error', 'Invalid SAML metadata: ' . implode(', ', $errors));
                return '';
            }

            return $metadata;

        } catch (\Exception $e) {
            log_message('error', 'Failed to generate SAML metadata: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Validar configuração SAML
     * 
     * @return bool
     */
    protected function validateConfig(): bool
    {
        $required = [
            'idp_entity_id',
            'idp_sso_url',
            'idp_certificate'
        ];

        foreach ($required as $field) {
            if (empty($this->config[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Testar configuração SAML
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
                $result['message'] = 'Configuração SAML incompleta';
                return $result;
            }

            $auth = $this->getSamlAuth();

            if (!$auth) {
                $result['message'] = 'Falha ao criar SAML Auth';
                return $result;
            }

            // Validar metadata
            $metadata = $this->getMetadata();
            
            if (empty($metadata)) {
                $result['message'] = 'Falha ao gerar metadata SAML';
                return $result;
            }

            $result['success'] = true;
            $result['message'] = 'Configuração SAML válida';
            $result['details']['idp_entity_id'] = $this->config['idp_entity_id'];
            $result['details']['idp_sso_url'] = $this->config['idp_sso_url'];
            $result['details']['metadata_size'] = strlen($metadata) . ' bytes';

        } catch (\Exception $e) {
            $result['message'] = 'Erro: ' . $e->getMessage();
        }

        return $result;
    }
}
