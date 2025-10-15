-- ============================================
-- SQL para MySQL/MariaDB - Módulo SSO
-- Data: 15/10/2025
-- CodeIgniter 4.6.3 + Shield 1.2.0
-- ============================================

-- Configurações do MySQL
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- 1. TABELA: sso_providers
-- ============================================

DROP TABLE IF EXISTS `sso_providers`;
CREATE TABLE `sso_providers` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `type` ENUM('local', 'ldap', 'oauth', 'saml') NOT NULL,
    `config` JSON NULL,
    `is_enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `priority` INT(11) NOT NULL DEFAULT 0,
    `description` TEXT NULL,
    `icon` VARCHAR(255) NULL,
    `button_label` VARCHAR(100) NULL,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_type` (`type`),
    KEY `idx_enabled` (`is_enabled`),
    KEY `idx_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 2. TABELA: sso_auth_logs
-- ============================================

DROP TABLE IF EXISTS `sso_auth_logs`;
CREATE TABLE `sso_auth_logs` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `provider_id` INT(11) UNSIGNED NULL,
    `username` VARCHAR(100) NULL,
    `principal` VARCHAR(255) NULL,
    `status` ENUM('pending', 'success', 'failed', 'blocked') NOT NULL DEFAULT 'pending',
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `metadata` JSON NULL,
    `error_message` TEXT NULL,
    `duration_ms` INT(11) NULL,
    `created_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_provider` (`provider_id`),
    KEY `idx_username` (`username`),
    KEY `idx_status` (`status`),
    KEY `idx_ip` (`ip_address`),
    KEY `idx_created` (`created_at`),
    CONSTRAINT `fk_auth_logs_provider` 
        FOREIGN KEY (`provider_id`) 
        REFERENCES `sso_providers` (`id`) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 3. TABELA: sso_settings
-- ============================================

DROP TABLE IF EXISTS `sso_settings`;
CREATE TABLE `sso_settings` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `setting_key` VARCHAR(100) NOT NULL,
    `setting_value` TEXT NOT NULL,
    `setting_group` VARCHAR(50) DEFAULT 'general',
    `description` TEXT NULL,
    `is_system` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_setting_key` (`setting_key`),
    KEY `idx_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 4. TABELAS DO CODEIGNITER SHIELD
-- ============================================

-- 4.1. users
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
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
    UNIQUE KEY `uk_username` (`username`),
    KEY `idx_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.2. auth_identities
DROP TABLE IF EXISTS `auth_identities`;
CREATE TABLE `auth_identities` (
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
    UNIQUE KEY `uk_type_secret` (`type`, `secret`),
    KEY `idx_user_id` (`user_id`),
    CONSTRAINT `fk_identities_user` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.3. auth_logins
DROP TABLE IF EXISTS `auth_logins`;
CREATE TABLE `auth_logins` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `ip_address` VARCHAR(255) NOT NULL,
    `user_agent` VARCHAR(255) NULL,
    `id_type` VARCHAR(255) NOT NULL,
    `identifier` VARCHAR(255) NOT NULL,
    `user_id` INT(11) UNSIGNED NULL,
    `date` DATETIME NOT NULL,
    `success` TINYINT(1) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_id_type_identifier` (`id_type`, `identifier`),
    KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.4. auth_token_logins
DROP TABLE IF EXISTS `auth_token_logins`;
CREATE TABLE `auth_token_logins` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `ip_address` VARCHAR(255) NOT NULL,
    `user_agent` VARCHAR(255) NULL,
    `id_type` VARCHAR(255) NOT NULL,
    `identifier` VARCHAR(255) NOT NULL,
    `user_id` INT(11) UNSIGNED NULL,
    `date` DATETIME NOT NULL,
    `success` TINYINT(1) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_id_type_identifier` (`id_type`, `identifier`),
    KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.5. auth_remember_tokens
DROP TABLE IF EXISTS `auth_remember_tokens`;
CREATE TABLE `auth_remember_tokens` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `selector` VARCHAR(255) NOT NULL,
    `hashedValidator` VARCHAR(255) NOT NULL,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `expires` DATETIME NOT NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_selector` (`selector`),
    KEY `idx_user_id` (`user_id`),
    CONSTRAINT `fk_remember_tokens_user` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.6. auth_permissions_users
DROP TABLE IF EXISTS `auth_permissions_users`;
CREATE TABLE `auth_permissions_users` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `permission` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    CONSTRAINT `fk_permissions_users_user` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4.7. auth_groups_users
DROP TABLE IF EXISTS `auth_groups_users`;
CREATE TABLE `auth_groups_users` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `group` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    CONSTRAINT `fk_groups_users_user` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. TABELA DO CODEIGNITER SETTINGS
-- ============================================

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `class` VARCHAR(255) NOT NULL,
    `key` VARCHAR(255) NOT NULL,
    `value` TEXT NULL,
    `type` VARCHAR(31) NOT NULL DEFAULT 'string',
    `context` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL,
    `updated_at` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_class_key` (`class`, `key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DADOS INICIAIS
-- ============================================

-- Provider Local Padrão
INSERT INTO `sso_providers` (
    `name`, `type`, `config`, `is_enabled`, `priority`, 
    `description`, `icon`, `button_label`, `created_at`, `updated_at`
) VALUES (
    'Local Authentication',
    'local',
    '{"hash_algorithm":"bcrypt","allow_registration":false}',
    1,
    100,
    'Autenticação local com username e senha',
    'fas fa-user',
    'Login com Usuário',
    NOW(),
    NOW()
);

-- Configurações Padrão do SSO
INSERT INTO `sso_settings` (`setting_key`, `setting_value`, `setting_group`, `description`, `created_at`, `updated_at`) VALUES
('session_timeout', '30', 'session', 'Timeout de sessão em minutos', NOW(), NOW()),
('remember_me_duration', '30', 'session', 'Duração do "lembrar-me" em dias', NOW(), NOW()),
('max_login_attempts', '5', 'security', 'Tentativas máximas de login', NOW(), NOW()),
('lockout_duration', '15', 'security', 'Tempo de bloqueio em minutos', NOW(), NOW()),
('rate_limit_window', '60', 'security', 'Janela de rate limit em segundos', NOW(), NOW()),
('require_2fa', '0', 'security', 'Exigir autenticação de dois fatores', NOW(), NOW()),
('enable_ip_whitelist', '0', 'security', 'Habilitar whitelist de IPs', NOW(), NOW()),
('min_password_length', '8', 'password', 'Comprimento mínimo da senha', NOW(), NOW()),
('password_expiry_days', '90', 'password', 'Expiração de senha em dias', NOW(), NOW()),
('require_uppercase', '1', 'password', 'Exigir letras maiúsculas', NOW(), NOW()),
('require_numbers', '1', 'password', 'Exigir números', NOW(), NOW()),
('require_special_chars', '0', 'password', 'Exigir caracteres especiais', NOW(), NOW()),
('prevent_password_reuse', '0', 'password', 'Prevenir reutilização de senhas', NOW(), NOW()),
('log_retention_days', '90', 'logs', 'Retenção de logs em dias', NOW(), NOW()),
('log_level', 'all', 'logs', 'Nível de log', NOW(), NOW()),
('enable_auto_cleanup', '1', 'logs', 'Habilitar limpeza automática', NOW(), NOW()),
('log_ip_addresses', '1', 'logs', 'Registrar IPs', NOW(), NOW()),
('notify_failed_logins', '1', 'email', 'Notificar falhas', NOW(), NOW()),
('notify_new_devices', '0', 'email', 'Notificar novos dispositivos', NOW(), NOW()),
('send_welcome_email', '1', 'email', 'Email de boas-vindas', NOW(), NOW()),
('password_reset_emails', '1', 'email', 'Email de reset', NOW(), NOW()),
('enable_single_session', '0', 'advanced', 'Sessão única', NOW(), NOW()),
('enable_captcha', '0', 'advanced', 'CAPTCHA', NOW(), NOW()),
('maintenance_mode', '0', 'advanced', 'Manutenção', NOW(), NOW()),
('debug_mode', '0', 'advanced', 'Debug', NOW(), NOW());

-- ============================================
-- VIEWS
-- ============================================

CREATE OR REPLACE VIEW `vw_recent_auth_logs` AS
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

CREATE OR REPLACE VIEW `vw_auth_stats_by_provider` AS
SELECT 
    p.name as provider_name,
    p.type as provider_type,
    COUNT(*) as total_attempts,
    SUM(CASE WHEN l.status = 'success' THEN 1 ELSE 0 END) as successful,
    SUM(CASE WHEN l.status = 'failed' THEN 1 ELSE 0 END) as failed,
    ROUND(AVG(l.duration_ms), 2) as avg_duration_ms
FROM sso_auth_logs l
INNER JOIN sso_providers p ON l.provider_id = p.id
GROUP BY p.id;

CREATE OR REPLACE VIEW `vw_active_providers` AS
SELECT 
    id, name, type, description, icon, button_label, priority
FROM sso_providers
WHERE is_enabled = 1
ORDER BY priority DESC;

-- ============================================
-- EVENT para limpeza automática (MySQL 5.7+)
-- ============================================

DELIMITER $$

DROP EVENT IF EXISTS `cleanup_old_auth_logs`$$
CREATE EVENT `cleanup_old_auth_logs`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 1 DAY
DO
BEGIN
    DELETE FROM sso_auth_logs 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)
    AND id NOT IN (
        SELECT id FROM (
            SELECT id FROM sso_auth_logs 
            ORDER BY created_at DESC 
            LIMIT 10000
        ) AS recent_logs
    );
END$$

DELIMITER ;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- FIM DO SCRIPT
-- ============================================
