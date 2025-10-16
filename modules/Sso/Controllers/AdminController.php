<?php

namespace Modules\Sso\Controllers;

use App\Controllers\BaseController;
use Modules\Sso\Models\ProviderModel;
use Modules\Sso\Models\AuthLogModel;
use Modules\Sso\Models\SettingsModel;

/**
 * Admin Controller
 * 
 * Dashboard administrativo do SSO
 */
class AdminController extends BaseController
{
    protected $helpers = ['auth'];
    
    public function index()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/sso/login');
        }

        $providerModel = new ProviderModel();
        $logModel = new AuthLogModel();

        $data['providers_count'] = $providerModel->countAll();
        $data['active_providers'] = $providerModel->where('is_enabled', true)->countAllResults();
        $data['stats_today'] = $logModel->getStats('today');
        $data['stats_week'] = $logModel->getStats('week');
        $data['recent_logs'] = $logModel->orderBy('created_at', 'DESC')->limit(10)->findAll();

        return view('Modules\Sso\Views\admin\dashboard', $data);
    }

    /**
     * Exibe formulário de configurações
     */
    public function settings()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/sso/login');
        }

        $settingsModel = new SettingsModel();
        $data['settings'] = $settingsModel->getAllSettings();

        return view('Modules\Sso\Views\settings\index', $data);
    }

    /**
     * Atualiza configurações
     */
    public function updateSettings()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/sso/login');
        }

        $settingsModel = new SettingsModel();
        $postData = $this->request->getPost();

        // Remove CSRF token
        unset($postData['csrf_token_name']);

        // Converte checkboxes não marcados
        $checkboxFields = [
            'require_2fa', 'enable_ip_whitelist', 'require_uppercase', 'require_numbers',
            'require_special_chars', 'prevent_password_reuse', 'enable_auto_cleanup',
            'log_ip_addresses', 'notify_failed_logins', 'notify_new_devices',
            'send_welcome_email', 'password_reset_emails', 'enable_single_session',
            'enable_captcha', 'maintenance_mode', 'debug_mode'
        ];

        foreach ($checkboxFields as $field) {
            if (!isset($postData[$field])) {
                $postData[$field] = false;
            }
        }

        // Valida configurações de segurança
        $errors = $settingsModel->validateSecuritySettings($postData);
        
        if (!empty($errors)) {
            return redirect()->back()->with('error', implode(', ', $errors))->withInput();
        }

        // Atualiza configurações
        if ($settingsModel->updateSettings($postData)) {
            return redirect()->to('/sso/admin/settings')->with('success', 'Configurações atualizadas com sucesso!');
        }

        return redirect()->back()->with('error', 'Erro ao atualizar configurações.')->withInput();
    }

    /**
     * Reseta configurações para valores padrão
     */
    public function resetSettings()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/sso/login');
        }

        $settingsModel = new SettingsModel();

        if ($settingsModel->resetToDefaults()) {
            return redirect()->to('/sso/admin/settings')->with('success', 'Configurações restauradas para valores padrão!');
        }

        return redirect()->back()->with('error', 'Erro ao restaurar configurações.');
    }

    /**
     * Exporta configurações como JSON
     */
    public function exportSettings()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/sso/login');
        }

        $settingsModel = new SettingsModel();
        $json = $settingsModel->exportSettings();

        return $this->response
            ->setContentType('application/json')
            ->setHeader('Content-Disposition', 'attachment; filename="sso_settings_' . date('Y-m-d_His') . '.json"')
            ->setBody($json);
    }

    /**
     * Importa configurações de JSON
     */
    public function importSettings()
    {
        if (!auth()->loggedIn()) {
            return redirect()->to('/sso/login');
        }

        $file = $this->request->getFile('settings_file');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Arquivo inválido.');
        }

        $json = file_get_contents($file->getTempName());
        $settingsModel = new SettingsModel();

        if ($settingsModel->importSettings($json)) {
            return redirect()->to('/sso/admin/settings')->with('success', 'Configurações importadas com sucesso!');
        }

        return redirect()->back()->with('error', 'Erro ao importar configurações.');
    }
}
