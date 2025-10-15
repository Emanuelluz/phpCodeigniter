<?php

namespace Modules\Sso\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Modules\Sso\Models\SettingsModel;

/**
 * SSO Auth Filter
 * 
 * Protege rotas exigindo autenticação SSO
 */
class SsoAuthFilter implements FilterInterface
{
    /**
     * Executar antes do controller
     * 
     * @param RequestInterface $request
     * @param mixed $arguments
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verificar modo de manutenção
        if ($this->isMaintenanceMode()) {
            return $this->maintenanceResponse();
        }

        // Verificar se está autenticado
        if (!auth()->loggedIn()) {
            // Salvar URL de retorno
            session()->set('redirect_url', current_url());
            
            // Redirecionar para login SSO
            return redirect()->to('/sso/login')->with('error', 'Você precisa fazer login para acessar esta página.');
        }

        // Verificar whitelist de IPs (se habilitado)
        if ($this->isIpWhitelistEnabled()) {
            if (!$this->isIpWhitelisted($request->getIPAddress())) {
                return $this->accessDeniedResponse('IP não autorizado');
            }
        }

        // Verificar expiração de sessão
        if ($this->isSessionExpired()) {
            auth()->logout();
            session()->set('redirect_url', current_url());
            
            return redirect()->to('/sso/login')->with('error', 'Sua sessão expirou. Faça login novamente.');
        }

        // Atualizar último acesso
        $this->updateLastActivity();

        // Verificar 2FA obrigatório
        if ($this->require2FA() && !$this->has2FAEnabled()) {
            return redirect()->to('/user/2fa/setup')->with('warning', 'Configure autenticação de dois fatores para continuar.');
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
     * Verificar se está em modo de manutenção
     * 
     * @return bool
     */
    protected function isMaintenanceMode(): bool
    {
        $settingsModel = new SettingsModel();
        return (bool) $settingsModel->getSetting('maintenance_mode', false);
    }

    /**
     * Verificar se IP whitelist está habilitado
     * 
     * @return bool
     */
    protected function isIpWhitelistEnabled(): bool
    {
        $settingsModel = new SettingsModel();
        return (bool) $settingsModel->getSetting('enable_ip_whitelist', false);
    }

    /**
     * Verificar se IP está na whitelist
     * 
     * @param string $ip
     * @return bool
     */
    protected function isIpWhitelisted(string $ip): bool
    {
        $settingsModel = new SettingsModel();
        $whitelist = $settingsModel->getSetting('ip_whitelist', []);

        if (!is_array($whitelist)) {
            $whitelist = json_decode($whitelist, true) ?? [];
        }

        return in_array($ip, $whitelist) || $this->matchCIDR($ip, $whitelist);
    }

    /**
     * Verificar se IP corresponde a algum range CIDR
     * 
     * @param string $ip
     * @param array $ranges
     * @return bool
     */
    protected function matchCIDR(string $ip, array $ranges): bool
    {
        $ipLong = ip2long($ip);

        foreach ($ranges as $range) {
            if (strpos($range, '/') === false) {
                continue;
            }

            [$subnet, $mask] = explode('/', $range);
            $subnetLong = ip2long($subnet);
            $maskLong = -1 << (32 - (int)$mask);

            if (($ipLong & $maskLong) === ($subnetLong & $maskLong)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar se sessão expirou
     * 
     * @return bool
     */
    protected function isSessionExpired(): bool
    {
        $lastActivity = session()->get('last_activity');

        if (!$lastActivity) {
            return false;
        }

        $settingsModel = new SettingsModel();
        $timeout = $settingsModel->getSetting('session_timeout', 30); // minutos

        $elapsed = (time() - $lastActivity) / 60; // converter para minutos

        return $elapsed > $timeout;
    }

    /**
     * Atualizar última atividade
     */
    protected function updateLastActivity(): void
    {
        session()->set('last_activity', time());
    }

    /**
     * Verificar se 2FA é obrigatório
     * 
     * @return bool
     */
    protected function require2FA(): bool
    {
        $settingsModel = new SettingsModel();
        return (bool) $settingsModel->getSetting('require_2fa', false);
    }

    /**
     * Verificar se usuário tem 2FA habilitado
     * 
     * @return bool
     */
    protected function has2FAEnabled(): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        // Verificar se Shield tem 2FA habilitado
        return isset($user->requires_2fa) ? (bool) $user->requires_2fa : false;
    }

    /**
     * Resposta de manutenção
     * 
     * @return ResponseInterface
     */
    protected function maintenanceResponse(): ResponseInterface
    {
        return service('response')
            ->setStatusCode(503)
            ->setBody(view('Modules\Sso\Views\errors\maintenance'));
    }

    /**
     * Resposta de acesso negado
     * 
     * @param string $reason
     * @return ResponseInterface
     */
    protected function accessDeniedResponse(string $reason = 'Acesso negado'): ResponseInterface
    {
        return service('response')
            ->setStatusCode(403)
            ->setBody(view('Modules\Sso\Views\errors\access_denied', ['reason' => $reason]));
    }
}
