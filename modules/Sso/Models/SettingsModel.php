<?php

namespace Modules\Sso\Models;

use CodeIgniter\Model;

/**
 * Settings Model
 * 
 * Gerencia configurações globais do SSO
 */
class SettingsModel extends Model
{
    protected $table = 'sso_settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'setting_key',
        'setting_value',
        'setting_group',
        'description',
        'is_system'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'setting_key' => 'required|max_length[100]|is_unique[sso_settings.setting_key,id,{id}]',
        'setting_value' => 'required',
        'setting_group' => 'required|max_length[50]',
    ];

    protected $validationMessages = [
        'setting_key' => [
            'required' => 'A chave da configuração é obrigatória',
            'is_unique' => 'Esta chave já existe'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Configurações padrão do sistema
     */
    protected $defaults = [
        // Session
        'session_timeout' => 30,
        'remember_me_duration' => 30,
        
        // Security & Rate Limiting
        'max_login_attempts' => 5,
        'lockout_duration' => 15,
        'rate_limit_window' => 60,
        'require_2fa' => false,
        'enable_ip_whitelist' => false,
        
        // Password Policy
        'min_password_length' => 8,
        'password_expiry_days' => 90,
        'require_uppercase' => true,
        'require_numbers' => true,
        'require_special_chars' => false,
        'prevent_password_reuse' => false,
        
        // Logs & Audit
        'log_retention_days' => 90,
        'log_level' => 'all',
        'enable_auto_cleanup' => true,
        'log_ip_addresses' => true,
        
        // Email Notifications
        'notify_failed_logins' => true,
        'notify_new_devices' => false,
        'send_welcome_email' => true,
        'password_reset_emails' => true,
        
        // Advanced
        'enable_single_session' => false,
        'enable_captcha' => false,
        'maintenance_mode' => false,
        'debug_mode' => false,
    ];

    /**
     * Obtém todas as configurações como array associativo
     * 
     * @return array
     */
    public function getAllSettings(): array
    {
        $settings = $this->findAll();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $this->castValue($setting['setting_key'], $setting['setting_value']);
        }
        
        // Preenche com defaults para chaves não existentes
        foreach ($this->defaults as $key => $value) {
            if (!isset($result[$key])) {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }

    /**
     * Obtém uma configuração específica
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getSetting(string $key, $default = null)
    {
        $setting = $this->where('setting_key', $key)->first();
        
        if ($setting) {
            return $this->castValue($key, $setting['setting_value']);
        }
        
        return $this->defaults[$key] ?? $default;
    }

    /**
     * Define uma configuração
     * 
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @param string|null $description
     * @return bool
     */
    public function setSetting(string $key, $value, string $group = 'general', ?string $description = null): bool
    {
        $existing = $this->where('setting_key', $key)->first();
        
        $data = [
            'setting_key' => $key,
            'setting_value' => $this->prepareValue($value),
            'setting_group' => $group,
            'description' => $description
        ];
        
        if ($existing) {
            return $this->update($existing['id'], $data);
        }
        
        return $this->insert($data) !== false;
    }

    /**
     * Atualiza múltiplas configurações
     * 
     * @param array $settings
     * @return bool
     */
    public function updateSettings(array $settings): bool
    {
        $this->db->transStart();
        
        foreach ($settings as $key => $value) {
            $group = $this->getGroupForKey($key);
            $this->setSetting($key, $value, $group);
        }
        
        $this->db->transComplete();
        
        return $this->db->transStatus();
    }

    /**
     * Reseta todas as configurações para os valores padrão
     * 
     * @return bool
     */
    public function resetToDefaults(): bool
    {
        $this->db->transStart();
        
        // Remove configurações não-sistema
        $this->where('is_system', false)->delete();
        
        // Insere defaults
        foreach ($this->defaults as $key => $value) {
            $group = $this->getGroupForKey($key);
            $this->setSetting($key, $value, $group);
        }
        
        $this->db->transComplete();
        
        return $this->db->transStatus();
    }

    /**
     * Exporta configurações como JSON
     * 
     * @return string
     */
    public function exportSettings(): string
    {
        $settings = $this->getAllSettings();
        return json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Importa configurações de JSON
     * 
     * @param string $json
     * @return bool
     */
    public function importSettings(string $json): bool
    {
        $settings = json_decode($json, true);
        
        if (!is_array($settings)) {
            return false;
        }
        
        return $this->updateSettings($settings);
    }

    /**
     * Prepara valor para armazenamento
     * 
     * @param mixed $value
     * @return string
     */
    protected function prepareValue($value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        
        if (is_array($value)) {
            return json_encode($value);
        }
        
        return (string) $value;
    }

    /**
     * Converte valor do banco para tipo apropriado
     * 
     * @param string $key
     * @param string $value
     * @return mixed
     */
    protected function castValue(string $key, string $value)
    {
        // Booleans
        $booleanKeys = [
            'require_2fa', 'enable_ip_whitelist', 'require_uppercase', 'require_numbers',
            'require_special_chars', 'prevent_password_reuse', 'enable_auto_cleanup',
            'log_ip_addresses', 'notify_failed_logins', 'notify_new_devices',
            'send_welcome_email', 'password_reset_emails', 'enable_single_session',
            'enable_captcha', 'maintenance_mode', 'debug_mode'
        ];
        
        if (in_array($key, $booleanKeys)) {
            return $value === '1' || $value === 'true' || $value === true;
        }
        
        // Integers
        $integerKeys = [
            'session_timeout', 'remember_me_duration', 'max_login_attempts',
            'lockout_duration', 'rate_limit_window', 'min_password_length',
            'password_expiry_days', 'log_retention_days'
        ];
        
        if (in_array($key, $integerKeys)) {
            return (int) $value;
        }
        
        // Arrays (JSON)
        if (str_starts_with($value, '{') || str_starts_with($value, '[')) {
            return json_decode($value, true);
        }
        
        return $value;
    }

    /**
     * Determina o grupo de uma chave
     * 
     * @param string $key
     * @return string
     */
    protected function getGroupForKey(string $key): string
    {
        $groups = [
            'session' => ['session_timeout', 'remember_me_duration'],
            'security' => ['max_login_attempts', 'lockout_duration', 'rate_limit_window', 'require_2fa', 'enable_ip_whitelist'],
            'password' => ['min_password_length', 'password_expiry_days', 'require_uppercase', 'require_numbers', 'require_special_chars', 'prevent_password_reuse'],
            'logs' => ['log_retention_days', 'log_level', 'enable_auto_cleanup', 'log_ip_addresses'],
            'email' => ['notify_failed_logins', 'notify_new_devices', 'send_welcome_email', 'password_reset_emails'],
            'advanced' => ['enable_single_session', 'enable_captcha', 'maintenance_mode', 'debug_mode'],
        ];
        
        foreach ($groups as $group => $keys) {
            if (in_array($key, $keys)) {
                return $group;
            }
        }
        
        return 'general';
    }

    /**
     * Valida configurações de segurança
     * 
     * @param array $settings
     * @return array Erros encontrados
     */
    public function validateSecuritySettings(array $settings): array
    {
        $errors = [];
        
        // Session timeout mínimo
        if (isset($settings['session_timeout']) && $settings['session_timeout'] < 5) {
            $errors['session_timeout'] = 'Timeout mínimo é 5 minutos';
        }
        
        // Max attempts
        if (isset($settings['max_login_attempts']) && $settings['max_login_attempts'] < 3) {
            $errors['max_login_attempts'] = 'Mínimo de 3 tentativas';
        }
        
        // Password length
        if (isset($settings['min_password_length']) && $settings['min_password_length'] < 6) {
            $errors['min_password_length'] = 'Comprimento mínimo é 6 caracteres';
        }
        
        // Log retention
        if (isset($settings['log_retention_days']) && $settings['log_retention_days'] < 7) {
            $errors['log_retention_days'] = 'Retenção mínima é 7 dias';
        }
        
        return $errors;
    }
}
