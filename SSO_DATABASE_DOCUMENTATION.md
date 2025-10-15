# 📊 SQL das Migrations - Módulo SSO

## 📁 Arquivos Gerados

1. **SSO_DATABASE_SCHEMA.sql** - SQL para SQLite3 (banco atual)
2. **SSO_DATABASE_SCHEMA_MYSQL.sql** - SQL para MySQL/MariaDB (produção)

---

## 🗄️ Estrutura do Banco de Dados

### **Tabelas do Módulo SSO (3 tabelas)**

| # | Tabela | Colunas | Descrição |
|---|--------|---------|-----------|
| 1 | `sso_providers` | 11 | Providers de autenticação (Local, LDAP, OAuth, SAML) |
| 2 | `sso_auth_logs` | 11 | Logs de tentativas de autenticação com IP, user agent, duração |
| 3 | `sso_settings` | 7 | Configurações globais do SSO (26 settings em 6 grupos) |

### **Tabelas do CodeIgniter Shield (7 tabelas)**

| # | Tabela | Descrição |
|---|--------|-----------|
| 4 | `users` | Usuários do sistema |
| 5 | `auth_identities` | Credenciais de autenticação (email/senha, tokens, etc) |
| 6 | `auth_logins` | Log de tentativas de login |
| 7 | `auth_token_logins` | Log de logins via token |
| 8 | `auth_remember_tokens` | Tokens "lembrar-me" |
| 9 | `auth_permissions_users` | Permissões individuais de usuários |
| 10 | `auth_groups_users` | Grupos de usuários |

### **Tabela do CodeIgniter Settings (1 tabela)**

| # | Tabela | Descrição |
|---|--------|-----------|
| 11 | `settings` | Sistema de configurações do CodeIgniter 4 |

---

## 📋 Detalhes das Tabelas SSO

### **1. sso_providers**
Armazena configurações dos providers de autenticação.

```sql
Campos principais:
- id: INTEGER (PK)
- name: VARCHAR(100) - Nome do provider
- type: ENUM('local', 'ldap', 'oauth', 'saml')
- config: JSON - Configurações específicas do provider
- is_enabled: BOOLEAN
- priority: INTEGER - Ordem de exibição
- icon: VARCHAR(255) - Ícone FontAwesome
- button_label: VARCHAR(100)

Índices:
- idx_type (type)
- idx_enabled (is_enabled)
- idx_priority (priority)
```

**Exemplo de Dados:**
```json
{
  "name": "Google Login",
  "type": "oauth",
  "config": {
    "oauth_provider": "google",
    "client_id": "xxx.apps.googleusercontent.com",
    "client_secret": "xxx",
    "redirect_uri": "http://localhost:8080/sso/callback/oauth"
  },
  "is_enabled": true,
  "priority": 90
}
```

---

### **2. sso_auth_logs**
Log completo de todas as tentativas de autenticação.

```sql
Campos principais:
- id: BIGINT (PK)
- provider_id: INTEGER (FK → sso_providers)
- username: VARCHAR(100)
- principal: VARCHAR(255) - DN do LDAP, email OAuth, etc
- status: ENUM('pending', 'success', 'failed', 'blocked')
- ip_address: VARCHAR(45) - Suporta IPv4 e IPv6
- user_agent: TEXT
- metadata: JSON - Dados adicionais
- error_message: TEXT
- duration_ms: INTEGER - Tempo de resposta em ms
- created_at: DATETIME

Índices:
- idx_provider (provider_id)
- idx_username (username)
- idx_status (status)
- idx_ip (ip_address)
- idx_created (created_at)
```

**Uso para Análise:**
```sql
-- Top 10 IPs com mais falhas
SELECT ip_address, COUNT(*) as failures
FROM sso_auth_logs
WHERE status = 'failed'
GROUP BY ip_address
ORDER BY failures DESC
LIMIT 10;

-- Tempo médio de autenticação por provider
SELECT p.name, AVG(l.duration_ms) as avg_ms
FROM sso_auth_logs l
JOIN sso_providers p ON l.provider_id = p.id
WHERE l.status = 'success'
GROUP BY p.id;
```

---

### **3. sso_settings**
Configurações globais do sistema SSO.

```sql
Campos principais:
- id: INTEGER (PK)
- setting_key: VARCHAR(100) UNIQUE
- setting_value: TEXT
- setting_group: VARCHAR(50)
- description: TEXT
- is_system: BOOLEAN
- created_at: DATETIME
- updated_at: DATETIME

Índice:
- idx_group (setting_group)
```

**26 Configurações Padrão:**

| Grupo | Configuração | Valor Padrão | Descrição |
|-------|--------------|--------------|-----------|
| **session** | session_timeout | 30 | Timeout em minutos |
| session | remember_me_duration | 30 | Duração em dias |
| **security** | max_login_attempts | 5 | Tentativas máximas |
| security | lockout_duration | 15 | Bloqueio em minutos |
| security | rate_limit_window | 60 | Janela em segundos |
| security | require_2fa | false | 2FA obrigatório |
| security | enable_ip_whitelist | false | Whitelist de IPs |
| **password** | min_password_length | 8 | Comprimento mínimo |
| password | password_expiry_days | 90 | Expiração em dias |
| password | require_uppercase | true | Exigir maiúsculas |
| password | require_numbers | true | Exigir números |
| password | require_special_chars | false | Exigir caracteres especiais |
| password | prevent_password_reuse | false | Prevenir reuso |
| **logs** | log_retention_days | 90 | Retenção em dias |
| logs | log_level | all | Nível de log |
| logs | enable_auto_cleanup | true | Limpeza automática |
| logs | log_ip_addresses | true | Registrar IPs |
| **email** | notify_failed_logins | true | Notificar falhas |
| email | notify_new_devices | false | Notificar novos devices |
| email | send_welcome_email | true | Email boas-vindas |
| email | password_reset_emails | true | Email reset senha |
| **advanced** | enable_single_session | false | Sessão única |
| advanced | enable_captcha | false | CAPTCHA |
| advanced | maintenance_mode | false | Modo manutenção |
| advanced | debug_mode | false | Modo debug |

---

## 🔍 Views Criadas

### **1. vw_recent_auth_logs**
Últimos 100 logs de autenticação com informações do provider.

```sql
SELECT * FROM vw_recent_auth_logs;
```

### **2. vw_auth_stats_by_provider**
Estatísticas de autenticação agrupadas por provider.

```sql
SELECT * FROM vw_auth_stats_by_provider;
```

### **3. vw_active_providers**
Lista apenas providers ativos, ordenados por prioridade.

```sql
SELECT * FROM vw_active_providers;
```

---

## ⚙️ Triggers e Events

### **SQLite: Trigger de Limpeza**
```sql
CREATE TRIGGER cleanup_old_auth_logs
AFTER INSERT ON sso_auth_logs
BEGIN
    DELETE FROM sso_auth_logs 
    WHERE created_at < datetime('now', '-90 days')
    AND id NOT IN (
        SELECT id FROM sso_auth_logs 
        ORDER BY created_at DESC 
        LIMIT 10000
    );
END;
```

### **MySQL: Event de Limpeza**
```sql
CREATE EVENT cleanup_old_auth_logs
ON SCHEDULE EVERY 1 DAY
DO
    DELETE FROM sso_auth_logs 
    WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)
    AND id NOT IN (
        SELECT id FROM (
            SELECT id FROM sso_auth_logs 
            ORDER BY created_at DESC 
            LIMIT 10000
        ) AS recent_logs
    );
```

---

## 📦 Comandos de Importação

### **SQLite (Atual)**
```bash
cd /home/emanuel/phpCodeigniter
sqlite3 writable/database.db < SSO_DATABASE_SCHEMA.sql
```

### **MySQL/MariaDB (Produção)**
```bash
# Local
mysql -u root -p seu_database < SSO_DATABASE_SCHEMA_MYSQL.sql

# Remoto
mysql -h hostname -u username -p database_name < SSO_DATABASE_SCHEMA_MYSQL.sql
```

---

## 🔄 Migrar de SQLite para MySQL

### **Exportar Dados do SQLite**
```bash
# Exportar como SQL
sqlite3 writable/database.db .dump > sqlite_dump.sql

# Ou exportar tabelas específicas
sqlite3 writable/database.db << EOF
.mode insert sso_providers
.output providers.sql
SELECT * FROM sso_providers;
EOF
```

### **Importar no MySQL**
```bash
# 1. Criar estrutura
mysql -u root -p database < SSO_DATABASE_SCHEMA_MYSQL.sql

# 2. Importar dados (ajustar sintaxe SQLite → MySQL)
mysql -u root -p database < sqlite_dump_adjusted.sql
```

---

## 📊 Estatísticas do Banco

```
TOTAL DE TABELAS: 11
- SSO: 3 tabelas
- Shield: 7 tabelas  
- Settings: 1 tabela

TOTAL DE VIEWS: 3
TOTAL DE TRIGGERS/EVENTS: 1
TOTAL DE ÍNDICES: ~25

DADOS INICIAIS:
- 1 provider (Local)
- 26 settings
- 0 usuários (criar via Shield)
```

---

## 🛠️ Consultas Úteis

### **Ver todos os providers**
```sql
SELECT name, type, is_enabled, priority 
FROM sso_providers 
ORDER BY priority DESC;
```

### **Logs das últimas 24h**
```sql
SELECT username, status, ip_address, created_at
FROM sso_auth_logs
WHERE created_at >= datetime('now', '-1 day')
ORDER BY created_at DESC;
```

### **Configurações de segurança**
```sql
SELECT setting_key, setting_value 
FROM sso_settings 
WHERE setting_group = 'security';
```

### **Top 5 usuários com mais tentativas**
```sql
SELECT username, COUNT(*) as attempts
FROM sso_auth_logs
GROUP BY username
ORDER BY attempts DESC
LIMIT 5;
```

---

## ✅ Validação

### **Verificar tabelas criadas**
```sql
-- SQLite
SELECT name FROM sqlite_master 
WHERE type='table' 
AND name LIKE 'sso_%';

-- MySQL
SHOW TABLES LIKE 'sso_%';
```

### **Verificar dados iniciais**
```sql
SELECT COUNT(*) FROM sso_providers; -- Deve retornar 1
SELECT COUNT(*) FROM sso_settings;  -- Deve retornar 26
```

---

**Gerado em: 15/10/2025**
**Versão do Schema: 1.0**
**Compatível com: SQLite 3.x, MySQL 5.7+, MariaDB 10.3+**
