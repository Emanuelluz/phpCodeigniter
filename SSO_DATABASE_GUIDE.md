# 📚 Guia de Uso - SQL das Migrations SSO

## 🎯 Arquivos Disponíveis

### 1. **SSO_DATABASE_SCHEMA.sql** (SQLite)
- ✅ Para desenvolvimento local
- ✅ Compatível com CodeIgniter padrão
- ✅ Usa `AUTOINCREMENT`

### 2. **SSO_DATABASE_MYSQL.sql** (MySQL/MariaDB) ⭐ NOVO
- ✅ Para produção
- ✅ Otimizado para MySQL 5.7+ / MariaDB 10.2+
- ✅ Usa `AUTO_INCREMENT`
- ✅ Inclui VIEWS, PROCEDURES e TRIGGERS

---

## 🚀 Como Usar

### **Opção 1: Via phpMyAdmin (Recomendado para Iniciantes)**

1. Acesse seu phpMyAdmin
2. Selecione o banco de dados
3. Clique na aba **SQL**
4. Copie o conteúdo de `SSO_DATABASE_MYSQL.sql`
5. Cole na caixa de texto
6. Clique em **Executar**

### **Opção 2: Via Linha de Comando MySQL**

```bash
# Fazer login no MySQL
mysql -u root -p

# Criar banco de dados (se não existir)
CREATE DATABASE sso_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Usar o banco
USE sso_database;

# Importar SQL
source /caminho/para/SSO_DATABASE_MYSQL.sql;

# Ou diretamente:
mysql -u root -p sso_database < /caminho/para/SSO_DATABASE_MYSQL.sql
```

### **Opção 3: Via CodeIgniter Migrations (Atual)**

```bash
# Executar migrations automaticamente
php spark migrate --all

# Executar seeder
php spark db:seed DefaultProvidersSeeder
```

---

## 📊 Estrutura Criada

### **Tabelas SSO (3)**
1. ✅ `sso_providers` - Providers de autenticação
2. ✅ `sso_auth_logs` - Logs de autenticação
3. ✅ `sso_settings` - Configurações globais

### **Tabelas Shield (7)**
1. ✅ `users` - Usuários do sistema
2. ✅ `auth_identities` - Identidades de autenticação
3. ✅ `auth_logins` - Histórico de logins
4. ✅ `auth_token_logins` - Logins via token
5. ✅ `auth_remember_tokens` - Tokens "lembrar-me"
6. ✅ `auth_groups_users` - Grupos de usuários
7. ✅ `auth_permissions_users` - Permissões de usuários

### **Tabela Settings (1)**
1. ✅ `settings` - Configurações do CodeIgniter Settings

### **Views (3)**
1. ✅ `vw_sso_provider_stats` - Estatísticas de providers
2. ✅ `vw_sso_recent_logs` - Logs recentes
3. ✅ `vw_sso_settings_grouped` - Configurações agrupadas

### **Stored Procedures (3)**
1. ✅ `sp_cleanup_old_logs` - Limpar logs antigos
2. ✅ `sp_get_auth_stats` - Estatísticas de autenticação
3. ✅ `sp_toggle_provider` - Ativar/desativar provider

### **Triggers (2)**
1. ✅ `tr_sso_providers_update` - Auto-atualizar timestamp
2. ✅ `tr_prevent_system_settings_delete` - Proteger configurações de sistema

---

## 🔧 Configuração do .env

Após importar o SQL, configure seu `.env`:

```env
# Database
database.default.hostname = localhost
database.default.database = sso_database
database.default.username = root
database.default.password = sua_senha
database.default.DBDriver = MySQLi
database.default.DBPrefix = 
database.default.port = 3306
```

---

## 🧪 Testar a Instalação

### **1. Verificar Tabelas**

```sql
-- Listar tabelas SSO
SHOW TABLES LIKE 'sso_%';

-- Listar tabelas Auth
SHOW TABLES LIKE 'auth_%';

-- Resultado esperado:
-- sso_auth_logs
-- sso_providers
-- sso_settings
-- auth_identities
-- auth_logins
-- auth_token_logins
-- auth_remember_tokens
-- auth_groups_users
-- auth_permissions_users
```

### **2. Verificar Dados Iniciais**

```sql
-- Provider padrão
SELECT * FROM sso_providers;
-- Deve retornar: Local Authentication

-- Configurações padrão
SELECT COUNT(*) as total FROM sso_settings;
-- Deve retornar: 24 configurações

-- Por grupo
SELECT setting_group, COUNT(*) as total 
FROM sso_settings 
GROUP BY setting_group;
```

### **3. Testar Views**

```sql
-- Estatísticas de providers
SELECT * FROM vw_sso_provider_stats;

-- Configurações agrupadas
SELECT * FROM vw_sso_settings_grouped;
```

### **4. Testar Procedures**

```sql
-- Estatísticas de hoje
CALL sp_get_auth_stats('today');

-- Estatísticas da semana
CALL sp_get_auth_stats('week');

-- Limpar logs com mais de 90 dias
CALL sp_cleanup_old_logs(90);
```

---

## 📝 Queries Úteis

### **Estatísticas de Login**

```sql
-- Total de tentativas por status
SELECT 
    status, 
    COUNT(*) as total,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM sso_auth_logs), 2) as percentage
FROM sso_auth_logs
GROUP BY status;

-- Últimos 10 logins
SELECT 
    username,
    status,
    ip_address,
    created_at,
    (SELECT name FROM sso_providers WHERE id = l.provider_id) as provider
FROM sso_auth_logs l
ORDER BY created_at DESC
LIMIT 10;
```

### **Configurações**

```sql
-- Listar todas as configurações de segurança
SELECT * FROM sso_settings WHERE setting_group = 'security';

-- Atualizar timeout de sessão
UPDATE sso_settings 
SET setting_value = '60', updated_at = NOW() 
WHERE setting_key = 'session_timeout';

-- Habilitar 2FA obrigatório
UPDATE sso_settings 
SET setting_value = '1', updated_at = NOW() 
WHERE setting_key = 'require_2fa';
```

### **Providers**

```sql
-- Listar providers ativos
SELECT * FROM sso_providers WHERE is_enabled = 1 ORDER BY priority;

-- Desabilitar provider LDAP
UPDATE sso_providers 
SET is_enabled = 0, updated_at = NOW() 
WHERE type = 'ldap';

-- Estatísticas de uso por provider
SELECT 
    p.name,
    p.type,
    COUNT(l.id) as total_attempts,
    SUM(CASE WHEN l.status = 'success' THEN 1 ELSE 0 END) as successful
FROM sso_providers p
LEFT JOIN sso_auth_logs l ON p.id = l.provider_id
GROUP BY p.id, p.name, p.type;
```

### **Limpeza e Manutenção**

```sql
-- Limpar logs com mais de 90 dias
DELETE FROM sso_auth_logs 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);

-- Resetar configurações para padrão (cuidado!)
-- DELETE FROM sso_settings WHERE is_system = 0;

-- Listar IPs bloqueados (via rate limiting)
SELECT 
    ip_address, 
    COUNT(*) as failed_attempts,
    MAX(created_at) as last_attempt
FROM sso_auth_logs
WHERE status = 'failed'
  AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
GROUP BY ip_address
HAVING failed_attempts >= 5;
```

---

## 🔒 Segurança

### **1. Criar Usuário Específico**

```sql
-- Criar usuário para a aplicação
CREATE USER 'sso_app'@'localhost' IDENTIFIED BY 'senha_muito_segura';

-- Conceder permissões específicas
GRANT SELECT, INSERT, UPDATE, DELETE ON sso_database.sso_* TO 'sso_app'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON sso_database.auth_* TO 'sso_app'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON sso_database.users TO 'sso_app'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON sso_database.settings TO 'sso_app'@'localhost';

-- Permissões para procedures
GRANT EXECUTE ON PROCEDURE sso_database.sp_cleanup_old_logs TO 'sso_app'@'localhost';
GRANT EXECUTE ON PROCEDURE sso_database.sp_get_auth_stats TO 'sso_app'@'localhost';

FLUSH PRIVILEGES;
```

### **2. Atualizar .env**

```env
database.default.username = sso_app
database.default.password = senha_muito_segura
```

### **3. Backup Automático**

```bash
# Criar backup diário
mysqldump -u root -p sso_database > backup_$(date +%Y%m%d).sql

# Com cron (diariamente às 2h)
0 2 * * * mysqldump -u root -pSENHA sso_database > /backups/sso_$(date +\%Y\%m\%d).sql
```

---

## 🐛 Troubleshooting

### **Erro: "Table already exists"**

```sql
-- Remover tabelas (CUIDADO - perde dados!)
DROP TABLE IF EXISTS auth_permissions_users;
DROP TABLE IF EXISTS auth_groups_users;
DROP TABLE IF EXISTS auth_remember_tokens;
DROP TABLE IF EXISTS auth_token_logins;
DROP TABLE IF EXISTS auth_logins;
DROP TABLE IF EXISTS auth_identities;
DROP TABLE IF EXISTS sso_auth_logs;
DROP TABLE IF EXISTS sso_settings;
DROP TABLE IF EXISTS sso_providers;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS users;

-- Depois execute o SQL novamente
```

### **Erro: "Cannot add foreign key constraint"**

- Certifique-se de que a tabela `users` existe primeiro
- Verifique se os tipos de dados são compatíveis
- Execute as tabelas na ordem correta

### **Erro: "Unknown collation: 'utf8mb4_unicode_ci'"**

```sql
-- Para MySQL antigo, use:
ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
```

### **Performance Lenta**

```sql
-- Analisar índices
SHOW INDEX FROM sso_auth_logs;

-- Otimizar tabelas
OPTIMIZE TABLE sso_auth_logs;
OPTIMIZE TABLE sso_providers;
OPTIMIZE TABLE auth_logins;

-- Analisar queries lentas
EXPLAIN SELECT * FROM sso_auth_logs WHERE username = 'test';
```

---

## 📊 Monitoramento

### **Dashboard SQL**

```sql
-- Resumo geral
SELECT 
    'Providers' as tabela,
    COUNT(*) as total,
    SUM(CASE WHEN is_enabled = 1 THEN 1 ELSE 0 END) as ativos
FROM sso_providers
UNION ALL
SELECT 
    'Logs (últimas 24h)',
    COUNT(*),
    SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END)
FROM sso_auth_logs
WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
UNION ALL
SELECT 
    'Usuários',
    COUNT(*),
    SUM(CASE WHEN active = 1 THEN 1 ELSE 0 END)
FROM users;

-- Taxa de sucesso por hora (últimas 24h)
SELECT 
    DATE_FORMAT(created_at, '%Y-%m-%d %H:00') as hora,
    COUNT(*) as tentativas,
    SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as sucessos,
    ROUND(SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as taxa_sucesso
FROM sso_auth_logs
WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY DATE_FORMAT(created_at, '%Y-%m-%d %H:00')
ORDER BY hora;
```

---

## ✅ Checklist Pós-Instalação

- [ ] Todas as tabelas criadas (11 tabelas)
- [ ] Provider local inserido
- [ ] 24 configurações padrão inseridas
- [ ] 3 Views funcionando
- [ ] 3 Stored Procedures criadas
- [ ] 2 Triggers ativos
- [ ] `.env` configurado corretamente
- [ ] Teste de conexão bem-sucedido
- [ ] Primeiro login funcional
- [ ] Backup configurado

---

**Documentação criada em: 15/10/2025**
**Versão: 1.0**
**Compatível com: MySQL 5.7+, MariaDB 10.2+**
