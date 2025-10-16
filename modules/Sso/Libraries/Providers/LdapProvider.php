<?php

namespace Modules\Sso\Libraries\Providers;

use Modules\Sso\Models\AuthLogModel;
use CodeIgniter\I18n\Time;

/**
 * LDAP Provider
 * 
 * Autenticação via LDAP/Active Directory usando Adldap2
 * 
 * Requer: composer require adldap2/adldap2
 */
class LdapProvider extends AbstractProvider
{
    protected ?object $connection = null;

    /**
     * Autenticar usuário via LDAP
     * 
     * @param array $credentials ['username' => 'user', 'password' => 'pass']
     * @return array|false
     */
    public function authenticate(array $credentials)
    {
        $startTime = microtime(true);
        
        try {
            // Validar configuração
            if (!$this->validateConfig()) {
                $this->logFailure($credentials['username'] ?? 'unknown', 'Invalid LDAP configuration');
                return false;
            }

            // Conectar ao LDAP
            if (!$this->connect()) {
                $this->logFailure($credentials['username'] ?? 'unknown', 'LDAP connection failed');
                return false;
            }

            // Autenticar
            $username = $credentials['username'] ?? '';
            $password = $credentials['password'] ?? '';

            if (empty($username) || empty($password)) {
                $this->logFailure($username, 'Empty credentials');
                return false;
            }

            // Buscar usuário no LDAP
            $ldapUser = $this->findUser($username);
            
            if (!$ldapUser) {
                $this->logFailure($username, 'User not found in LDAP');
                return false;
            }

            // Tentar bind com credenciais do usuário
            if (!$this->bindUser($ldapUser['dn'], $password)) {
                $this->logFailure($username, 'Invalid password');
                return false;
            }

            // Sucesso - mapear atributos
            $userData = $this->mapUserAttributes($ldapUser);
            
            $this->logSuccess($username, $userData);
            
            return $userData;

        } catch (\Exception $e) {
            log_message('error', 'LDAP authentication error: ' . $e->getMessage());
            $this->logFailure($credentials['username'] ?? 'unknown', $e->getMessage());
            return false;
        } finally {
            $this->disconnect();
        }
    }

    /**
     * Conectar ao servidor LDAP
     * 
     * @return bool
     */
    protected function connect(): bool
    {
        try {
            $host = $this->config['host'] ?? '10.15.62.30';
            $port = $this->config['port'] ?? 389;
            $useSsl = $this->config['use_ssl'] ?? false;
            $useTls = $this->config['use_tls'] ?? false;

            $dsn = ($useSsl ? 'ldaps://' : 'ldap://') . $host . ':' . $port;

            $this->connection = ldap_connect($dsn);

            if (!$this->connection) {
                return false;
            }

            // Opções LDAP
            ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($this->connection, LDAP_OPT_NETWORK_TIMEOUT, 10);

            if ($useTls && !$useSsl) {
                if (!ldap_start_tls($this->connection)) {
                    return false;
                }
            }

            // Bind administrativo (opcional)
            $bindDn = $this->config['bind_dn'] ?? null;
            $bindPassword = $this->config['bind_password'] ?? null;

            if ($bindDn && $bindPassword) {
                if (!@ldap_bind($this->connection, $bindDn, $bindPassword)) {
                    return false;
                }
            }

            return true;

        } catch (\Exception $e) {
            log_message('error', 'LDAP connection error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Desconectar do LDAP
     */
    protected function disconnect(): void
    {
        if ($this->connection) {
            @ldap_unbind($this->connection);
            $this->connection = null;
        }
    }

    /**
     * Buscar usuário no LDAP
     * 
     * @param string $username
     * @return array|false
     */
    protected function findUser(string $username)
    {
        if (!$this->connection) {
            return false;
        }

        $baseDn = $this->config['base_dn'] ?? '';
        $filter = $this->buildUserFilter($username);
        $attributes = $this->config['attributes'] ?? ['dn', 'cn', 'mail', 'memberof', 'displayname'];

        $search = @ldap_search($this->connection, $baseDn, $filter, $attributes);

        if (!$search) {
            return false;
        }

        $entries = ldap_get_entries($this->connection, $search);

        if (!$entries || $entries['count'] === 0) {
            return false;
        }

        return $this->normalizeEntry($entries[0]);
    }

    /**
     * Fazer bind com credenciais do usuário
     * 
     * @param string $dn
     * @param string $password
     * @return bool
     */
    protected function bindUser(string $dn, string $password): bool
    {
        if (!$this->connection) {
            return false;
        }

        return @ldap_bind($this->connection, $dn, $password);
    }

    /**
     * Construir filtro LDAP para busca de usuário
     * 
     * @param string $username
     * @return string
     */
    protected function buildUserFilter(string $username): string
    {
        $userFilter = $this->config['user_filter'] ?? '(sAMAccountName={username})';
        return str_replace('{username}', ldap_escape($username, '', LDAP_ESCAPE_FILTER), $userFilter);
    }

    /**
     * Normalizar entrada LDAP
     * 
     * @param array $entry
     * @return array
     */
    protected function normalizeEntry(array $entry): array
    {
        $normalized = [];

        foreach ($entry as $key => $value) {
            if (is_numeric($key)) {
                continue;
            }

            if (isset($value['count']) && $value['count'] === 1) {
                $normalized[$key] = $value[0];
            } elseif (is_array($value) && isset($value['count'])) {
                unset($value['count']);
                $normalized[$key] = array_values($value);
            } else {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }

    /**
     * Mapear atributos LDAP para dados do usuário
     * 
     * @param array $ldapUser
     * @return array
     */
    protected function mapUserAttributes(array $ldapUser): array
    {
        $mapping = $this->config['attribute_mapping'] ?? [
            'username' => 'samaccountname',
            'email' => 'mail',
            'name' => 'displayname',
            'groups' => 'memberof'
        ];

        $userData = [
            'provider' => $this->provider['name'],
            'provider_id' => $this->provider['id'],
            'principal' => $ldapUser['dn'] ?? '',
        ];

        foreach ($mapping as $userKey => $ldapKey) {
            $ldapKey = strtolower($ldapKey);
            
            if (isset($ldapUser[$ldapKey])) {
                $value = $ldapUser[$ldapKey];
                
                // Processar grupos
                if ($userKey === 'groups' && is_array($value)) {
                    $userData[$userKey] = $this->extractGroupNames($value);
                } else {
                    $userData[$userKey] = is_array($value) ? $value[0] : $value;
                }
            }
        }

        return $userData;
    }

    /**
     * Extrair nomes de grupos de DNs
     * 
     * @param array $groupDns
     * @return array
     */
    protected function extractGroupNames(array $groupDns): array
    {
        $groups = [];

        foreach ($groupDns as $dn) {
            if (preg_match('/^CN=([^,]+)/i', $dn, $matches)) {
                $groups[] = $matches[1];
            }
        }

        return $groups;
    }

    /**
     * Validar configuração LDAP
     * 
     * @return bool
     */
    protected function validateConfig(): bool
    {
        $required = ['host', 'base_dn'];

        foreach ($required as $field) {
            if (empty($this->config[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Testar conexão LDAP
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
            if (!$this->connect()) {
                $result['message'] = 'Falha ao conectar ao servidor LDAP';
                $result['details']['error'] = ldap_error($this->connection ?? null);
                return $result;
            }

            $result['success'] = true;
            $result['message'] = 'Conexão LDAP estabelecida com sucesso';
            $result['details']['host'] = $this->config['host'];
            $result['details']['port'] = $this->config['port'] ?? 389;
            $result['details']['base_dn'] = $this->config['base_dn'];

        } catch (\Exception $e) {
            $result['message'] = 'Erro: ' . $e->getMessage();
        } finally {
            $this->disconnect();
        }

        return $result;
    }

    /**
     * Sincronizar usuários do LDAP
     * 
     * @param array $options
     * @return array
     */
    public function syncUsers(array $options = []): array
    {
        $result = [
            'success' => false,
            'users_found' => 0,
            'users_synced' => 0,
            'errors' => []
        ];

        try {
            if (!$this->connect()) {
                $result['errors'][] = 'Falha ao conectar ao LDAP';
                return $result;
            }

            $baseDn = $this->config['base_dn'];
            $filter = $options['filter'] ?? '(objectClass=user)';
            
            $search = @ldap_search($this->connection, $baseDn, $filter);
            
            if (!$search) {
                $result['errors'][] = 'Falha na busca: ' . ldap_error($this->connection);
                return $result;
            }

            $entries = ldap_get_entries($this->connection, $search);
            $result['users_found'] = $entries['count'];

            // Processar cada usuário encontrado
            for ($i = 0; $i < $entries['count']; $i++) {
                $ldapUser = $this->normalizeEntry($entries[$i]);
                $userData = $this->mapUserAttributes($ldapUser);
                
                // Aqui você pode salvar no banco de dados
                // $userModel->syncUser($userData);
                
                $result['users_synced']++;
            }

            $result['success'] = true;

        } catch (\Exception $e) {
            $result['errors'][] = $e->getMessage();
        } finally {
            $this->disconnect();
        }

        return $result;
    }
}
