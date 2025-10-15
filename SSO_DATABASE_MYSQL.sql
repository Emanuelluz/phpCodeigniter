-- ============================================
-- SQL das Migrations - Módulo SSO (MySQL/MariaDB)
-- Data: 15/10/2025
-- CodeIgniter 4.6.3 + Shield 1.2.0
-- Banco: MySQL 5.7+ / MariaDB 10.2+
-- ============================================

-- ============================================
-- 1. TABELA: sso_providers
-- Migration: 2025-01-15-100000_CreateSsoProvidersTable
-- Descrição: Armazena providers de autenticação (Local, LDAP, OAuth, SAML)
-- ============================================

CREATE TABLE IF NOT EXISTS `sso_providers` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `type` ENUM('local', 'ldap', 'oauth', 'saml') NOT NULL,
    `config` TEXT NULL,
    `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `priority` INT(11) NOT NULL DEFAULT 0,
    `description` TEXT NULL,
    `icon` VARCHAR(255) NULL,
    `button_label` VARCHAR(100) NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_type` (`type`),
    INDEX `idx_enabled` (`is_enabled`),
    INDEX `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dados iniciais: Provider Local
INSERT INTO `sso_providers` (`name`, `type`, `config`, `is_enabled`, `priority`, `description`, `button_label`, `created_at`, `updated_at`) 
VALUES (
    'Local Authentication',
    'local',
    '{}',
    1,
    1,
    'Autenticação local com username e senha',
    'Login Local',
    NOW(),
    NOW()
);

-- ============================================
-- 2. TABELA: sso_auth_logs
-- Migration: 2025-01-15-100001_CreateSsoAuthLogsTable
-- Descrição: Logs de tentativas de autenticação
-- ============================================

CREATE TABLE IF NOT EXISTS `sso_auth_logs` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `provider_id` INT(11) UNSIGNED NULL,
    `username` VARCHAR(255) NULL,
    `email` VARCHAR(255) NULL,
    `status` ENUM('success', 'failed', 'blocked') NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(512) NULL,
    `error_message` TEXT NULL,
    `session_id` VARCHAR(128) NULL,
    `duration_ms` INT(11) NULL,
    `created_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_provider` (`provider_id`),
    INDEX `idx_username` (`username`),
    INDEX `idx_status` (`status`),
    INDEX `idx_created` (`created_at`),
    INDEX `idx_ip` (`ip_address`),
    FOREIGN KEY (`provider_id`) REFERENCES `sso_providers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. TABELA: sso_settings
-- Migration: 2025-01-15-100002_CreateSsoSettingsTable
-- Descrição: Configurações globais do SSO
-- ============================================

CREATE TABLE IF NOT EXISTS `sso_settings` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT NOT NULL,
    `setting_group` VARCHAR(50) NOT NULL DEFAULT 'general',
    `description` TEXT NULL,
    `is_system` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'System settings cannot be deleted',
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_key` (`setting_key`),
    INDEX `idx_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configurações padrão do SSO
INSERT INTO `sso_settings` (`setting_key`, `setting_value`, `setting_group`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
-- Session
('session_timeout', '30', 'session', 'Timeout de sessão em minutos', 1, NOW(), NOW()),
('remember_me_duration', '30', 'session', 'Duração do "lembrar-me" em dias', 1, NOW(), NOW()),

-- Security
('max_login_attempts', '5', 'security', 'Tentativas máximas de login', 1, NOW(), NOW()),
('lockout_duration', '15', 'security', 'Duração do bloqueio em minutos', 1, NOW(), NOW()),
('rate_limit_window', '60', 'security', 'Janela de rate limit em segundos', 1, NOW(), NOW()),
('require_2fa', '0', 'security', 'Exigir autenticação de dois fatores', 1, NOW(), NOW()),
('enable_ip_whitelist', '0', 'security', 'Habilitar whitelist de IPs', 1, NOW(), NOW()),

-- Password Policy
('min_password_length', '8', 'password', 'Comprimento mínimo da senha', 1, NOW(), NOW()),
('password_expiry_days', '90', 'password', 'Dias para expiração de senha (0 = nunca)', 1, NOW(), NOW()),
('require_uppercase', '1', 'password', 'Exigir letras maiúsculas', 1, NOW(), NOW()),
('require_numbers', '1', 'password', 'Exigir números', 1, NOW(), NOW()),
('require_special_chars', '0', 'password', 'Exigir caracteres especiais', 1, NOW(), NOW()),
('prevent_password_reuse', '0', 'password', 'Prevenir reutilização de senhas', 1, NOW(), NOW()),

-- Logs
('log_retention_days', '90', 'logs', 'Dias de retenção de logs', 1, NOW(), NOW()),
('log_level', 'all', 'logs', 'Nível de log (all, auth, failed, critical)', 1, NOW(), NOW()),
('enable_auto_cleanup', '1', 'logs', 'Habilitar limpeza automática de logs', 1, NOW(), NOW()),
('log_ip_addresses', '1', 'logs', 'Registrar endereços IP', 1, NOW(), NOW()),

-- Email
('notify_failed_logins', '1', 'email', 'Notificar falhas de login', 1, NOW(), NOW()),
('notify_new_devices', '0', 'email', 'Notificar novos dispositivos', 1, NOW(), NOW()),
('send_welcome_email', '1', 'email', 'Enviar email de boas-vindas', 1, NOW(), NOW()),
('password_reset_emails', '1', 'email', 'Enviar emails de reset de senha', 1, NOW(), NOW()),

-- Advanced
('enable_single_session', '0', 'advanced', 'Permitir apenas uma sessão por usuário', 1, NOW(), NOW()),
('enable_captcha', '0', 'advanced', 'Habilitar CAPTCHA', 1, NOW(), NOW()),
('maintenance_mode', '0', 'advanced', 'Modo de manutenção', 1, NOW(), NOW()),
('debug_mode', '0', 'advanced', 'Modo debug', 1, NOW(), NOW());

-- ============================================
-- 4. TABELAS DO SHIELD (Auth)
-- Criadas automaticamente pelo Shield:setup
-- ============================================

-- 4.1. auth_identities
CREATE TABLE IF NOT EXISTS `auth_identities` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `type` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NULL,
    `secret` VARCHAR(255) NOT NULL,
    `secret2` VARCHAR(255) NULL,
    `expires` DATETIME NULL,
    `extra` TEXT NULL,
    `force_reset` TINYINT(1) NOT NULL DEFAULT 0,
    `last_used_at` DATETIME NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `type_secret` (`type`, `secret`),
    INDEX `user_id` (`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.2. auth_logins
CREATE TABLE IF NOT EXISTS `auth_logins` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `ip_address` VARCHAR(45) NOT NULL,
    `user_agent` VARCHAR(255) NULL,
    `id_type` VARCHAR(255) NOT NULL,
    `identifier` VARCHAR(255) NOT NULL,
    `user_id` INT(11) UNSIGNED NULL,
    `date` DATETIME NOT NULL,
    `success` TINYINT(1) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `id_type_identifier` (`id_type`, `identifier`),
    INDEX `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.3. auth_token_logins
CREATE TABLE IF NOT EXISTS `auth_token_logins` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `ip_address` VARCHAR(45) NOT NULL,
    `user_agent` VARCHAR(255) NULL,
    `id_type` VARCHAR(255) NOT NULL,
    `identifier` VARCHAR(255) NOT NULL,
    `user_id` INT(11) UNSIGNED NULL,
    `date` DATETIME NOT NULL,
    `success` TINYINT(1) NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `id_type_identifier` (`id_type`, `identifier`),
    INDEX `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.4. auth_remember_tokens
CREATE TABLE IF NOT EXISTS `auth_remember_tokens` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `selector` VARCHAR(255) NOT NULL,
    `hashedValidator` VARCHAR(255) NOT NULL,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `expires` DATETIME NOT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `selector` (`selector`),
    INDEX `user_id` (`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.5. auth_groups_users
CREATE TABLE IF NOT EXISTS `auth_groups_users` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `group` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `user_id` (`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.6. auth_permissions_users
CREATE TABLE IF NOT EXISTS `auth_permissions_users` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `permission` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `user_id` (`user_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.7. users (tabela base do Shield)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(30) NULL,
    `status` VARCHAR(255) NULL,
    `status_message` VARCHAR(255) NULL,
    `active` TINYINT(1) NOT NULL DEFAULT 0,
    `last_active` DATETIME NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    `deleted_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. TABELA: settings (CodeIgniter Settings)
-- Criada automaticamente pelo Shield:setup
-- ============================================

CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `class` VARCHAR(255) NOT NULL,
    `key` VARCHAR(255) NOT NULL,
    `value` TEXT NULL,
    `type` VARCHAR(31) NOT NULL DEFAULT 'string',
    `context` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `class_key_context` (`class`, `key`, `context`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- VIEWS ÚTEIS
-- ============================================

-- View: Estatísticas de Providers
CREATE OR REPLACE VIEW `vw_sso_provider_stats` AS
SELECT 
    p.id,
    p.name,
    p.type,
    p.is_enabled,
    COUNT(l.id) as total_attempts,
    SUM(CASE WHEN l.status = 'success' THEN 1 ELSE 0 END) as successful_logins,
    SUM(CASE WHEN l.status = 'failed' THEN 1 ELSE 0 END) as failed_attempts,
    MAX(l.created_at) as last_used
FROM sso_providers p
LEFT JOIN sso_auth_logs l ON p.id = l.provider_id
GROUP BY p.id, p.name, p.type, p.is_enabled;

-- View: Logs Recentes
CREATE OR REPLACE VIEW `vw_sso_recent_logs` AS
SELECT 
    l.id,
    l.username,
    l.email,
    l.status,
    l.ip_address,
    l.created_at,
    p.name as provider_name,
    p.type as provider_type
FROM sso_auth_logs l
LEFT JOIN sso_providers p ON l.provider_id = p.id
ORDER BY l.created_at DESC
LIMIT 100;

-- View: Configurações por Grupo
CREATE OR REPLACE VIEW `vw_sso_settings_grouped` AS
SELECT 
    setting_group,
    COUNT(*) as total_settings,
    SUM(CASE WHEN is_system = 1 THEN 1 ELSE 0 END) as system_settings,
    SUM(CASE WHEN is_system = 0 THEN 1 ELSE 0 END) as custom_settings
FROM sso_settings
GROUP BY setting_group;

-- ============================================
-- STORED PROCEDURES
-- ============================================

DELIMITER //

-- Procedure: Limpar logs antigos
CREATE PROCEDURE `sp_cleanup_old_logs`(IN days_to_keep INT)
BEGIN
    DELETE FROM sso_auth_logs 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL days_to_keep DAY);
    
    SELECT ROW_COUNT() as deleted_records;
END //

-- Procedure: Obter estatísticas de autenticação
CREATE PROCEDURE `sp_get_auth_stats`(IN period VARCHAR(10))
BEGIN
    DECLARE start_date DATETIME;
    
    CASE period
        WHEN 'today' THEN SET start_date = CURDATE();
        WHEN 'week' THEN SET start_date = DATE_SUB(NOW(), INTERVAL 7 DAY);
        WHEN 'month' THEN SET start_date = DATE_SUB(NOW(), INTERVAL 30 DAY);
        ELSE SET start_date = DATE_SUB(NOW(), INTERVAL 7 DAY);
    END CASE;
    
    SELECT 
        COUNT(*) as total_attempts,
        SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful,
        SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
        SUM(CASE WHEN status = 'blocked' THEN 1 ELSE 0 END) as blocked,
        COUNT(DISTINCT ip_address) as unique_ips,
        COUNT(DISTINCT username) as unique_users
    FROM sso_auth_logs
    WHERE created_at >= start_date;
END //

-- Procedure: Bloquear provider
CREATE PROCEDURE `sp_toggle_provider`(IN provider_id INT, IN enabled TINYINT)
BEGIN
    UPDATE sso_providers 
    SET is_enabled = enabled, updated_at = NOW()
    WHERE id = provider_id;
    
    SELECT * FROM sso_providers WHERE id = provider_id;
END //

DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================

DELIMITER //

-- Trigger: Atualizar updated_at ao modificar provider
CREATE TRIGGER `tr_sso_providers_update` 
BEFORE UPDATE ON `sso_providers`
FOR EACH ROW
BEGIN
    SET NEW.updated_at = NOW();
END //

-- Trigger: Prevenir exclusão de configurações do sistema
CREATE TRIGGER `tr_prevent_system_settings_delete` 
BEFORE DELETE ON `sso_settings`
FOR EACH ROW
BEGIN
    IF OLD.is_system = 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot delete system settings';
    END IF;
END //

DELIMITER ;

-- ============================================
-- GRANTS E PERMISSÕES (Opcional)
-- ============================================

-- Criar usuário específico para a aplicação (ajuste conforme necessário)
-- CREATE USER 'sso_app'@'localhost' IDENTIFIED BY 'senha_segura';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON `sso_providers` TO 'sso_app'@'localhost';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON `sso_auth_logs` TO 'sso_app'@'localhost';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON `sso_settings` TO 'sso_app'@'localhost';
-- GRANT EXECUTE ON PROCEDURE `sp_cleanup_old_logs` TO 'sso_app'@'localhost';
-- GRANT EXECUTE ON PROCEDURE `sp_get_auth_stats` TO 'sso_app'@'localhost';
-- FLUSH PRIVILEGES;

-- ============================================
-- FIM DO SCRIPT
-- ============================================

-- Verificar tabelas criadas
SHOW TABLES LIKE 'sso_%';
SHOW TABLES LIKE 'auth_%';

-- Verificar dados iniciais
SELECT * FROM sso_providers;
SELECT COUNT(*) as total_settings FROM sso_settings;
