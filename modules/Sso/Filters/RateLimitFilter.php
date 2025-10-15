<?php

namespace Modules\Sso\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Modules\Sso\Models\SettingsModel;
use Modules\Sso\Models\AuthLogModel;

/**
 * Rate Limit Filter
 * 
 * Limita tentativas de login por IP/usuário
 */
class RateLimitFilter implements FilterInterface
{
    protected SettingsModel $settingsModel;
    protected AuthLogModel $logModel;
    protected array $settings;

    public function __construct()
    {
        $this->settingsModel = new SettingsModel();
        $this->logModel = new AuthLogModel();
        $this->settings = $this->settingsModel->getAllSettings();
    }

    /**
     * Executar antes do controller
     * 
     * @param RequestInterface $request
     * @param mixed $arguments
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $ip = $request->getIPAddress();
        $username = $request->getPost('username') ?? $request->getPost('email') ?? '';

        // Verificar rate limit por IP
        if ($this->isRateLimitedByIp($ip)) {
            return $this->rateLimitResponse('IP bloqueado temporariamente. Muitas tentativas.');
        }

        // Verificar rate limit por usuário (se fornecido)
        if (!empty($username) && $this->isRateLimitedByUser($username)) {
            return $this->rateLimitResponse('Usuário bloqueado temporariamente. Muitas tentativas.');
        }

        // Verificar lockout por tentativas falhadas
        if (!empty($username) && $this->isLockedOut($username, $ip)) {
            $lockoutDuration = $this->settings['lockout_duration'] ?? 15;
            return $this->rateLimitResponse("Conta bloqueada por {$lockoutDuration} minutos devido a múltiplas tentativas falhadas.");
        }
    }

    /**
     * Executar após o controller
     * 
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param mixed $arguments
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada a fazer após
    }

    /**
     * Verificar rate limit por IP
     * 
     * @param string $ip
     * @return bool
     */
    protected function isRateLimitedByIp(string $ip): bool
    {
        $window = $this->settings['rate_limit_window'] ?? 60; // segundos
        $maxAttempts = $this->settings['max_login_attempts'] ?? 5;

        $cacheKey = "rate_limit_ip_{$ip}";
        $cache = \Config\Services::cache();

        $attempts = (int) $cache->get($cacheKey);

        if ($attempts >= $maxAttempts) {
            return true;
        }

        // Incrementar tentativas
        $cache->save($cacheKey, $attempts + 1, $window);

        return false;
    }

    /**
     * Verificar rate limit por usuário
     * 
     * @param string $username
     * @return bool
     */
    protected function isRateLimitedByUser(string $username): bool
    {
        $window = $this->settings['rate_limit_window'] ?? 60; // segundos
        $maxAttempts = $this->settings['max_login_attempts'] ?? 5;

        $cacheKey = "rate_limit_user_" . md5($username);
        $cache = \Config\Services::cache();

        $attempts = (int) $cache->get($cacheKey);

        if ($attempts >= $maxAttempts) {
            return true;
        }

        // Incrementar tentativas
        $cache->save($cacheKey, $attempts + 1, $window);

        return false;
    }

    /**
     * Verificar se está em lockout por tentativas falhadas
     * 
     * @param string $username
     * @param string $ip
     * @return bool
     */
    protected function isLockedOut(string $username, string $ip): bool
    {
        $maxAttempts = $this->settings['max_login_attempts'] ?? 5;
        $lockoutDuration = $this->settings['lockout_duration'] ?? 15; // minutos

        // Verificar lockout em cache primeiro (performance)
        $lockoutKey = "lockout_" . md5($username . $ip);
        $cache = \Config\Services::cache();

        if ($cache->get($lockoutKey)) {
            return true;
        }

        // Contar falhas recentes do banco de dados
        $recentFailures = $this->logModel->countRecentFailures($username, $ip, $lockoutDuration);

        if ($recentFailures >= $maxAttempts) {
            // Ativar lockout
            $cache->save($lockoutKey, true, $lockoutDuration * 60); // converter para segundos
            return true;
        }

        return false;
    }

    /**
     * Limpar rate limit de um IP/usuário (após login bem-sucedido)
     * 
     * @param string $ip
     * @param string $username
     */
    public static function clearRateLimit(string $ip, string $username = ''): void
    {
        $cache = \Config\Services::cache();

        // Limpar rate limit por IP
        $cache->delete("rate_limit_ip_{$ip}");

        // Limpar rate limit por usuário
        if (!empty($username)) {
            $cache->delete("rate_limit_user_" . md5($username));
            $cache->delete("lockout_" . md5($username . $ip));
        }
    }

    /**
     * Registrar tentativa falhada
     * 
     * @param string $username
     * @param string $ip
     * @param string $reason
     */
    public static function recordFailedAttempt(string $username, string $ip, string $reason = 'Invalid credentials'): void
    {
        $logModel = new AuthLogModel();
        
        $logModel->logAttempt([
            'provider_id' => null,
            'username' => $username,
            'status' => 'failed',
            'ip_address' => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'error_message' => $reason
        ]);
    }

    /**
     * Registrar tentativa bem-sucedida
     * 
     * @param string $username
     * @param string $ip
     * @param int $providerId
     */
    public static function recordSuccessfulAttempt(string $username, string $ip, int $providerId): void
    {
        $logModel = new AuthLogModel();
        
        $logModel->logAttempt([
            'provider_id' => $providerId,
            'username' => $username,
            'status' => 'success',
            'ip_address' => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);

        // Limpar rate limits
        self::clearRateLimit($ip, $username);
    }

    /**
     * Obter tempo restante de bloqueio
     * 
     * @param string $ip
     * @param string $username
     * @return int Segundos restantes, 0 se não bloqueado
     */
    public static function getRemainingLockoutTime(string $ip, string $username = ''): int
    {
        $cache = \Config\Services::cache();

        // Verificar lockout por IP
        $ipKey = "rate_limit_ip_{$ip}";
        $ipMetadata = $cache->getMetadata($ipKey);
        
        if ($ipMetadata && isset($ipMetadata['expire'])) {
            $remaining = $ipMetadata['expire'] - time();
            if ($remaining > 0) {
                return $remaining;
            }
        }

        // Verificar lockout por usuário
        if (!empty($username)) {
            $userKey = "lockout_" . md5($username . $ip);
            $userMetadata = $cache->getMetadata($userKey);
            
            if ($userMetadata && isset($userMetadata['expire'])) {
                $remaining = $userMetadata['expire'] - time();
                if ($remaining > 0) {
                    return $remaining;
                }
            }
        }

        return 0;
    }

    /**
     * Obter tentativas restantes
     * 
     * @param string $ip
     * @param string $username
     * @return int
     */
    public static function getRemainingAttempts(string $ip, string $username = ''): int
    {
        $settingsModel = new SettingsModel();
        $maxAttempts = $settingsModel->getSetting('max_login_attempts', 5);

        $cache = \Config\Services::cache();

        // Verificar tentativas por IP
        $ipKey = "rate_limit_ip_{$ip}";
        $ipAttempts = (int) $cache->get($ipKey);

        $remaining = $maxAttempts - $ipAttempts;

        // Verificar tentativas por usuário
        if (!empty($username)) {
            $userKey = "rate_limit_user_" . md5($username);
            $userAttempts = (int) $cache->get($userKey);
            
            $userRemaining = $maxAttempts - $userAttempts;
            $remaining = min($remaining, $userRemaining);
        }

        return max(0, $remaining);
    }

    /**
     * Resposta de rate limit excedido
     * 
     * @param string $message
     * @return ResponseInterface
     */
    protected function rateLimitResponse(string $message): ResponseInterface
    {
        $request = service('request');
        
        // Se for requisição AJAX, retornar JSON
        if ($request->isAJAX()) {
            return service('response')
                ->setStatusCode(429)
                ->setJSON([
                    'error' => true,
                    'message' => $message
                ]);
        }

        // Senão, redirecionar com mensagem
        return redirect()->back()->with('error', $message);
    }
}
