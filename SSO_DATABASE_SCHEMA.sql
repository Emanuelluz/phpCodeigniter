-- ============================================
-- SQL das Migrations - Módulo SSO
-- Data: 15/10/2025
-- CodeIgniter 4.6.3 + Shield 1.2.0
-- ============================================

-- ============================================
-- 1. TABELA: sso_providers
-- Migration: 2025-01-15-100000_CreateSsoProvidersTable
-- Descrição: Armazena providers de autenticação (Local, LDAP, OAuth, SAML)
-- ============================================

CREATE TABLE IF NOT EXISTS `sso_providers` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `type` VARCHAR(20) NOT NULL,
    `config` TEXT NULL,
    `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `priority` INTEGER NOT NULL DEFAULT 0,
    `description` TEXT NULL,
    `icon` VARCHAR(255) NULL,
    `button_label` VARCHAR(100) NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL
);

-- Índices para performance
CREATE INDEX `idx_sso_providers_type` ON `sso_providers` (`type`);
CREATE INDEX `idx_sso_providers_enabled` ON `sso_providers` (`is_enabled`);
CREATE INDEX `idx_sso_providers_priority` ON `sso_providers` (`priority`);

-- ============================================
-- 2. TABELA: sso_auth_logs
-- Migration: 2025-01-15-100001_CreateSsoAuthLogsTable
-- Descrição: Logs de tentativas de autenticação
-- ============================================

CREATE TABLE IF NOT EXISTS `sso_auth_logs` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `provider_id` INTEGER NULL,
    `username` VARCHAR(100) NULL,
    `principal` VARCHAR(255) NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `metadata` TEXT NULL,
    `error_message` TEXT NULL,
    `duration_ms` INTEGER NULL,
    `created_at` DATETIME NULL
);

-- Índices para performance
CREATE INDEX `idx_sso_auth_logs_provider` ON `sso_auth_logs` (`provider_id`);
CREATE INDEX `idx_sso_auth_logs_username` ON `sso_auth_logs` (`username`);
CREATE INDEX `idx_sso_auth_logs_status` ON `sso_auth_logs` (`status`);
CREATE INDEX `idx_sso_auth_logs_ip` ON `sso_auth_logs` (`ip_address`);
CREATE INDEX `idx_sso_auth_logs_created` ON `sso_auth_logs` (`created_at`);

-- ============================================
-- 3. TABELA: sso_settings
-- Migration: 2025-01-15-100002_CreateSsoSettingsTable
-- Descrição: Configurações globais do SSO
-- ============================================

CREATE TABLE IF NOT EXISTS `sso_settings` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `setting_key` VARCHAR(100) UNIQUE NOT NULL,
    `setting_value` TEXT NOT NULL,
    `setting_group` VARCHAR(50) DEFAULT 'general',
    `description` TEXT NULL,
    `is_system` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL
);

-- Índice para performance
CREATE INDEX `idx_sso_settings_group` ON `sso_settings` (`setting_group`);

-- ============================================
-- 4. TABELAS DO CODEIGNITER SHIELD
-- Migration: 2020-12-28-223112 (Shield)
-- Descrição: Tabelas de autenticação do Shield
-- ============================================

-- 4.1. auth_identities
CREATE TABLE IF NOT EXISTS `auth_identities` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `user_id` INTEGER NOT NULL,
    `type` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NULL,
    `secret` VARCHAR(255) NOT NULL,
    `secret2` VARCHAR(255) NULL,
    `expires` DATETIME NULL,
    `extra` TEXT NULL,
    `force_reset` TINYINT(1) NOT NULL DEFAULT 0,
    `last_used_at` DATETIME NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL
);

CREATE UNIQUE INDEX `auth_identities_type_secret` ON `auth_identities` (`type`, `secret`);
CREATE INDEX `auth_identities_user_id` ON `auth_identities` (`user_id`);

-- 4.2. auth_logins
CREATE TABLE IF NOT EXISTS `auth_logins` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `ip_address` VARCHAR(255) NOT NULL,
    `user_agent` VARCHAR(255) NULL,
    `id_type` VARCHAR(255) NOT NULL,
    `identifier` VARCHAR(255) NOT NULL,
    `user_id` INTEGER NULL,
    `date` DATETIME NOT NULL,
    `success` TINYINT(1) NOT NULL
);

CREATE INDEX `auth_logins_id_type_identifier` ON `auth_logins` (`id_type`, `identifier`);
CREATE INDEX `auth_logins_user_id` ON `auth_logins` (`user_id`);

-- 4.3. auth_token_logins
CREATE TABLE IF NOT EXISTS `auth_token_logins` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `ip_address` VARCHAR(255) NOT NULL,
    `user_agent` VARCHAR(255) NULL,
    `id_type` VARCHAR(255) NOT NULL,
    `identifier` VARCHAR(255) NOT NULL,
    `user_id` INTEGER NULL,
    `date` DATETIME NOT NULL,
    `success` TINYINT(1) NOT NULL
);

CREATE INDEX `auth_token_logins_id_type_identifier` ON `auth_token_logins` (`id_type`, `identifier`);
CREATE INDEX `auth_token_logins_user_id` ON `auth_token_logins` (`user_id`);

-- 4.4. auth_remember_tokens
CREATE TABLE IF NOT EXISTS `auth_remember_tokens` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `selector` VARCHAR(255) NOT NULL,
    `hashedValidator` VARCHAR(255) NOT NULL,
    `user_id` INTEGER NOT NULL,
    `expires` DATETIME NOT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL
);

CREATE UNIQUE INDEX `auth_remember_tokens_selector` ON `auth_remember_tokens` (`selector`);
CREATE INDEX `auth_remember_tokens_user_id` ON `auth_remember_tokens` (`user_id`);

-- 4.5. auth_permissions_users
CREATE TABLE IF NOT EXISTS `auth_permissions_users` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `user_id` INTEGER NOT NULL,
    `permission` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL
);

CREATE INDEX `auth_permissions_users_user_id` ON `auth_permissions_users` (`user_id`);

-- 4.6. auth_groups_users
CREATE TABLE IF NOT EXISTS `auth_groups_users` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `user_id` INTEGER NOT NULL,
    `group` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL
);

CREATE INDEX `auth_groups_users_user_id` ON `auth_groups_users` (`user_id`);

-- 4.7. users (se não existir)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `username` VARCHAR(30) NULL UNIQUE,
    `status` VARCHAR(255) NULL,
    `status_message` VARCHAR(255) NULL,
    `active` TINYINT(1) NOT NULL DEFAULT 0,
    `last_active` DATETIME NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    `deleted_at` DATETIME NULL
);

-- ============================================
-- 5. TABELAS DO CODEIGNITER SETTINGS
-- Migration: 2021-07-04-041948 + 2021-11-14-143905
-- Descrição: Sistema de configurações do CI4
-- ============================================

CREATE TABLE IF NOT EXISTS `settings` (
    `id` INTEGER PRIMARY KEY AUTOINCREMENT,
    `class` VARCHAR(255) NOT NULL,
    `key` VARCHAR(255) NOT NULL,
    `value` TEXT NULL,
    `type` VARCHAR(31) NOT NULL DEFAULT 'string',
    `context` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL
);

CREATE INDEX `settings_class_key` ON `settings` (`class`, `key`);

-- ============================================
-- DADOS INICIAIS
-- ============================================

-- Provider Local Padrão
INSERT INTO `sso_providers` (
    `name`, 
    `type`, 
    `config`, 
    `is_enabled`, 
    `priority`, 
    `description`, 
    `icon`, 
    `button_label`, 
    `created_at`, 
    `updated_at`
) VALUES (
    'Local Authentication',
    'local',
    '{"hash_algorithm":"bcrypt","allow_registration":false}',
    1,
    100,
    'Autenticação local com username e senha',
    'fas fa-user',
    'Login com Usuário',
    datetime('now'),
    datetime('now')
);

-- Configurações Padrão do SSO
INSERT INTO `sso_settings` (`setting_key`, `setting_value`, `setting_group`, `description`, `created_at`, `updated_at`) VALUES
('session_timeout', '30', 'session', 'Timeout de sessão em minutos', datetime('now'), datetime('now')),
('remember_me_duration', '30', 'session', 'Duração do "lembrar-me" em dias', datetime('now'), datetime('now')),
('max_login_attempts', '5', 'security', 'Tentativas máximas de login', datetime('now'), datetime('now')),
('lockout_duration', '15', 'security', 'Tempo de bloqueio em minutos', datetime('now'), datetime('now')),
('rate_limit_window', '60', 'security', 'Janela de rate limit em segundos', datetime('now'), datetime('now')),
('require_2fa', '0', 'security', 'Exigir autenticação de dois fatores', datetime('now'), datetime('now')),
('enable_ip_whitelist', '0', 'security', 'Habilitar whitelist de IPs', datetime('now'), datetime('now')),
('min_password_length', '8', 'password', 'Comprimento mínimo da senha', datetime('now'), datetime('now')),
('password_expiry_days', '90', 'password', 'Expiração de senha em dias', datetime('now'), datetime('now')),
('require_uppercase', '1', 'password', 'Exigir letras maiúsculas', datetime('now'), datetime('now')),
('require_numbers', '1', 'password', 'Exigir números', datetime('now'), datetime('now')),
('require_special_chars', '0', 'password', 'Exigir caracteres especiais', datetime('now'), datetime('now')),
('prevent_password_reuse', '0', 'password', 'Prevenir reutilização de senhas', datetime('now'), datetime('now')),
('log_retention_days', '90', 'logs', 'Retenção de logs em dias', datetime('now'), datetime('now')),
('log_level', 'all', 'logs', 'Nível de log (all/auth/failed/critical)', datetime('now'), datetime('now')),
('enable_auto_cleanup', '1', 'logs', 'Habilitar limpeza automática de logs', datetime('now'), datetime('now')),
('log_ip_addresses', '1', 'logs', 'Registrar endereços IP nos logs', datetime('now'), datetime('now')),
('notify_failed_logins', '1', 'email', 'Notificar falhas de login', datetime('now'), datetime('now')),
('notify_new_devices', '0', 'email', 'Notificar novos dispositivos', datetime('now'), datetime('now')),
('send_welcome_email', '1', 'email', 'Enviar email de boas-vindas', datetime('now'), datetime('now')),
('password_reset_emails', '1', 'email', 'Emails de redefinição de senha', datetime('now'), datetime('now')),
('enable_single_session', '0', 'advanced', 'Apenas uma sessão ativa por usuário', datetime('now'), datetime('now')),
('enable_captcha', '0', 'advanced', 'Habilitar CAPTCHA', datetime('now'), datetime('now')),
('maintenance_mode', '0', 'advanced', 'Modo de manutenção', datetime('now'), datetime('now')),
('debug_mode', '0', 'advanced', 'Modo debug', datetime('now'), datetime('now'));

-- ============================================
-- VIEWS ÚTEIS PARA CONSULTAS
-- ============================================

-- View: Últimos 100 logs de autenticação
CREATE VIEW IF NOT EXISTS `vw_recent_auth_logs` AS
SELECT 
    l.id,
    l.username,
    p.name as provider_name,
    p.type as provider_type,
    l.status,
    l.ip_address,
    l.created_at,
    l.error_message
FROM sso_auth_logs l
LEFT JOIN sso_providers p ON l.provider_id = p.id
ORDER BY l.created_at DESC
LIMIT 100;

-- View: Estatísticas de autenticação por provider
CREATE VIEW IF NOT EXISTS `vw_auth_stats_by_provider` AS
SELECT 
    p.name as provider_name,
    p.type as provider_type,
    COUNT(*) as total_attempts,
    SUM(CASE WHEN l.status = 'success' THEN 1 ELSE 0 END) as successful,
    SUM(CASE WHEN l.status = 'failed' THEN 1 ELSE 0 END) as failed,
    ROUND(AVG(l.duration_ms), 2) as avg_duration_ms
FROM sso_auth_logs l
INNER JOIN sso_providers p ON l.provider_id = p.id
GROUP BY p.id, p.name, p.type;

-- View: Providers ativos
CREATE VIEW IF NOT EXISTS `vw_active_providers` AS
SELECT 
    id,
    name,
    type,
    description,
    icon,
    button_label,
    priority
FROM sso_providers
WHERE is_enabled = 1
ORDER BY priority DESC;

-- ============================================
-- TRIGGERS PARA AUDITORIA
-- ============================================

-- Trigger: Limpar logs antigos automaticamente
CREATE TRIGGER IF NOT EXISTS `cleanup_old_auth_logs`
AFTER INSERT ON `sso_auth_logs`
BEGIN
    DELETE FROM sso_auth_logs 
    WHERE created_at < datetime('now', '-90 days')
    AND id NOT IN (
        SELECT id FROM sso_auth_logs 
        ORDER BY created_at DESC 
        LIMIT 10000
    );
END;

-- ============================================
-- COMENTÁRIOS E METADATA
-- ============================================

/*
RESUMO DAS TABELAS:

1. sso_providers (11 colunas)
   - Armazena configurações de providers de autenticação
   - Suporta: Local, LDAP, OAuth 2.0, SAML 2.0

2. sso_auth_logs (11 colunas)
   - Log completo de tentativas de autenticação
   - Inclui IP, user agent, tempo de resposta

3. sso_settings (7 colunas)
   - Configurações globais do módulo SSO
   - 26 configurações agrupadas em 6 categorias

4. Shield Tables (7 tabelas)
   - Sistema de autenticação do CodeIgniter
   - Gerenciamento de usuários, grupos, permissões

5. Settings Table (1 tabela)
   - Sistema de configurações do CodeIgniter
   - Configurações por contexto

TOTAL: 11 tabelas + 3 views + 1 trigger
*/

-- ============================================
-- FIM DO SCRIPT SQL
-- ============================================
