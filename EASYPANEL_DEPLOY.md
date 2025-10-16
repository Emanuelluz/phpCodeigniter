# 🚀 Guia de Deploy - Sistema SSO no Easypanel

## 📋 Pré-requisitos

1. **Banco de Dados MySQL/MariaDB** configurado no Easypanel
2. **Domínio configurado** apontando para sua aplicação
3. **Repositório Git** conectado ao Easypanel

---

## 🔧 Configuração de Variáveis de Ambiente

Configure as seguintes variáveis de ambiente no Easypanel:

### Ambiente
```env
CI_ENVIRONMENT=production
```

### Banco de Dados
```env
DB_HOST=seu-mysql-host.easypanel.host
DB_NAME=nome_do_banco
DB_USER=usuario_mysql
DB_PASS=senha_mysql
DB_DRIVER=MySQLi
```

⚠️ **IMPORTANTE**: 
- `DB_HOST` NÃO deve ser `deppen_codeigniterdb` (isso é para Docker local)
- Use o hostname fornecido pelo Easypanel para o MySQL
- Exemplo: `mysql-instance-123.easypanel.host`

### Aplicação (Opcional)
```env
app.baseURL=https://seu-dominio.com/
```

⚠️ **NOTA**: 
- Esta variável é **OPCIONAL**
- Se não configurada, a aplicação detecta automaticamente baseado no domínio acessado
- Configure apenas se precisar forçar uma URL específica
- A URL deve terminar com `/` (barra final)
- Use `https://` em produção
- Exemplo: `app.baseURL=https://pppr.ecl.dev.br/`

### Segurança (Opcional mas Recomendado)
```env
ENCRYPTION_KEY=sua_chave_hex_32_chars_minimo
```

---

## 📦 Comandos de Deploy

### 1. Executar Migrations

Execute após o primeiro deploy ou quando houver novas migrations:

```bash
php spark migrate --all
```

### 2. Popular Banco de Dados (Primeira vez)

Execute o **MasterSeeder** para configurar tudo de uma vez:

```bash
php spark db:seed MasterSeeder
```

Ou execute os seeders individualmente:

```bash
# 1. Criar usuário admin
php spark db:seed AdminUserSeeder

# 2. Configurar providers SSO
php spark db:seed Modules\\Sso\\Database\\Seeds\\DefaultProvidersSeeder

# 3. Configurar settings
php spark db:seed SsoSettingsSeeder
```

### 3. Gerar Chave de Criptografia (Se não configurou)

```bash
php spark key:generate
```

---

## 📁 Estrutura do Banco de Dados

Após executar as migrations e seeders, você terá **13 tabelas**:

### Shield (7 tabelas)
- `users` - Usuários do sistema
- `auth_identities` - Credenciais de login
- `auth_groups_users` - Grupos de usuários
- `auth_logins` - Histórico de logins
- `auth_permissions_users` - Permissões
- `auth_remember_tokens` - Tokens "lembrar-me"
- `auth_token_logins` - Login via API token

### SSO (3 tabelas)
- `sso_providers` - Provedores de autenticação (Local, LDAP, OAuth, SAML)
- `sso_auth_logs` - Logs de autenticação SSO
- `sso_settings` - Configurações do módulo SSO

### Sistema (3 tabelas)
- `ci_sessions` - Sessões ativas
- `settings` - Settings do Shield
- `migrations` - Controle de migrations

---

## 🔐 Credenciais Padrão

Após executar o MasterSeeder:

- **Usuário:** `admin`
- **Senha:** `DtiFB@2025`
- **Email:** `admin@example.com`
- **Grupo:** `superadmin`

⚠️ **IMPORTANTE**: Altere a senha após o primeiro login em produção!

---

## 🌐 URLs do Sistema

Após o deploy, acesse:

- **Login:** `https://seu-dominio.com/sso/login`
- **Dashboard:** `https://seu-dominio.com/sso/admin`
- **Providers:** `https://seu-dominio.com/sso/admin/providers`
- **Logs:** `https://seu-dominio.com/sso/admin/logs`
- **Settings:** `https://seu-dominio.com/sso/admin/settings`

---

## 🐛 Troubleshooting

### 1. Erro de Conexão com Banco de Dados

**Sintoma:** `Connection refused` ou `Unknown host`

**Solução:**
- Verifique se o `DB_HOST` está correto
- Use o hostname interno do Easypanel (ex: `mysql-123.easypanel.host`)
- NÃO use `localhost`, `127.0.0.1` ou `deppen_codeigniterdb`

### 2. Tabela não encontrada

**Sintoma:** `Table 'database.users' doesn't exist`

**Solução:**
```bash
php spark migrate --all
```

### 3. Settings não carregam

**Sintoma:** `Table 'database.settings' doesn't exist` ou `Unknown column 'context'`

**Solução:**
```bash
php spark migrate --all
php spark db:seed MasterSeeder
```

### 4. Provider não aparece na tela de login

**Sintoma:** Página de login vazia, sem botões

**Solução:**
```bash
php spark db:seed Modules\\Sso\\Database\\Seeds\\DefaultProvidersSeeder
```

### 5. Erro 500 após login

**Sintoma:** Página branca ou erro 500 após autenticação bem-sucedida

**Possíveis causas:**
- Falta executar migrations do Shield
- Sessões não configuradas corretamente

**Solução:**
```bash
# 1. Verificar migrations
php spark migrate:status

# 2. Executar migrations faltantes
php spark migrate --all

# 3. Verificar se tabela ci_sessions existe
php spark db:table ci_sessions --show
```

---

## 📝 Checklist de Deploy

- [ ] Criar banco de dados MySQL no Easypanel
- [ ] Configurar variáveis de ambiente (DB_HOST, DB_NAME, etc)
- [ ] Fazer push do código para o repositório Git
- [ ] Aguardar deploy automático do Easypanel
- [ ] Conectar ao terminal do container
- [ ] Executar: `php spark migrate --all`
- [ ] Executar: `php spark db:seed MasterSeeder`
- [ ] Acessar: `https://seu-dominio.com/sso/login`
- [ ] Fazer login com: `admin` / `DtiFB@2025`
- [ ] Alterar senha do admin
- [ ] Configurar providers adicionais (LDAP, OAuth, SAML) se necessário

---

## 🔄 Comandos Úteis

### Ver status das migrations
```bash
php spark migrate:status
```

### Reverter última migration (CUIDADO!)
```bash
php spark migrate:rollback
```

### Ver tabelas do banco
```bash
php spark db:table --show
```

### Limpar cache
```bash
php spark cache:clear
```

### Ver rotas disponíveis
```bash
php spark routes
```

---

## 📚 Documentação Adicional

- **CodeIgniter 4:** https://codeigniter.com/user_guide/
- **Shield (Auth):** https://shield.codeigniter.com/
- **SSO Module:** Ver documentação em `/docs` do repositório

---

## 🆘 Suporte

Se encontrar problemas durante o deploy:

1. Verifique os logs do Easypanel
2. Execute `php spark db:table --show` para verificar tabelas criadas
3. Execute `php spark migrate:status` para ver status das migrations
4. Revise as variáveis de ambiente configuradas

---

**Última atualização:** 16 de Outubro de 2025
