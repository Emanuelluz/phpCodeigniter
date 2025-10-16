# 🔐 Sistema SSO - CodeIgniter 4 + Shield

Sistema completo de **Single Sign-On (SSO)** com múltiplos provedores de autenticação construído com CodeIgniter 4 e Shield.

## 🚀 Quick Start

### Setup Local (Desenvolvimento)

```bash
# 1. Instalar dependências
composer install

# 2. Configurar ambiente
cp .env .env.local
# Edite .env com suas configurações de banco

# 3. Subir banco de dados (Docker)
docker compose up -d

# 4. Executar migrations
php spark migrate --all

# 5. Popular banco de dados
php spark db:seed MasterSeeder

# 6. Iniciar servidor
php spark serve --port=8080
```

**Acesse:** http://localhost:8080/sso/login  
**Login:** `admin` / `DtiFB@2025`

---

## 📦 Deploy no Easypanel

### 1. Configurar Variáveis de Ambiente

```env
CI_ENVIRONMENT=production
DB_HOST=mysql-host.easypanel.host
DB_NAME=seu_banco
DB_USER=usuario
DB_PASS=senha
DB_DRIVER=MySQLi
app.baseURL=https://seu-dominio.com/  # OPCIONAL - detecta automaticamente se omitido
```

⚠️ **NOTA**: A variável `app.baseURL` é **opcional**. Se não configurada, a aplicação detecta automaticamente o domínio.

### 2. Executar Setup

Após o deploy, conecte ao terminal do container e execute:

```bash
bash setup.sh
```

Ou manualmente:

```bash
php spark migrate --all
php spark db:seed MasterSeeder
```

📖 **Guia completo:** Veja [EASYPANEL_DEPLOY.md](EASYPANEL_DEPLOY.md)

---

## 🏗️ Estrutura do Projeto

```
phpCodeigniter/
├── app/                      # Aplicação principal
│   ├── Config/              # Configurações (Auth, Database, etc)
│   └── Database/
│       ├── Migrations/      # Migrations do sistema
│       └── Seeds/           # Seeders (MasterSeeder, AdminUserSeeder)
├── modules/
│   ├── Admin/               # Módulo Admin
│   ├── Auth/                # Módulo de Autenticação
│   └── Sso/                 # Módulo SSO ⭐
│       ├── Config/          # Configurações SSO
│       ├── Controllers/     # AdminController, SsoController, etc
│       ├── Models/          # ProviderModel, AuthLogModel, SettingsModel
│       ├── Views/           # Dashboard, Login, Providers, Logs
│       ├── Libraries/
│       │   └── Providers/  # LdapProvider, OAuthProvider, SamlProvider
│       ├── Filters/         # SsoAuthFilter, RateLimitFilter
│       └── Database/
│           └── Seeds/       # DefaultProvidersSeeder, SsoSettingsSeeder
└── public/                  # Ponto de entrada web
```

---

## 🔑 Funcionalidades

### ✅ Implementado

- ✅ **Autenticação Local** (usuário/senha)
- ✅ **Dashboard Administrativo** com estatísticas
- ✅ **Gerenciamento de Providers**
- ✅ **Logs de Autenticação** com filtros e paginação
- ✅ **Configurações SSO** (30+ opções)
- ✅ **Rate Limiting** para proteção contra brute force
- ✅ **Integração com CodeIgniter Shield**
- ✅ **Suporte a múltiplos providers**

### 🔧 Providers Disponíveis

- **Local** - Autenticação com usuário/senha (✅ Ativo)
- **LDAP/Active Directory** - Integração com servidores LDAP (⚙️ Configurável)
- **OAuth 2.0** - Google, Microsoft, GitHub (⚙️ Configurável)
- **SAML 2.0** - Integração com IdP corporativo (⚙️ Configurável)

---

## 📊 Banco de Dados

**13 Tabelas:**

| Categoria | Tabelas | Descrição |
|-----------|---------|-----------|
| **Shield** | 7 tabelas | `users`, `auth_identities`, `auth_groups_users`, `auth_logins`, `auth_permissions_users`, `auth_remember_tokens`, `auth_token_logins` |
| **SSO** | 3 tabelas | `sso_providers`, `sso_auth_logs`, `sso_settings` |
| **Sistema** | 3 tabelas | `ci_sessions`, `settings`, `migrations` |

---

## 🛠️ Comandos Úteis

```bash
# Migrations
php spark migrate --all           # Executar todas
php spark migrate:status          # Ver status
php spark migrate:rollback        # Reverter última

# Seeders
php spark db:seed MasterSeeder    # Executar todos de uma vez
php spark db:seed AdminUserSeeder # Apenas usuário admin

# Banco de Dados
php spark db:table --show         # Listar todas as tabelas

# Cache
php spark cache:clear             # Limpar cache

# Rotas
php spark routes                  # Listar todas as rotas
```

---

## 🌐 Rotas Principais

| Rota | Descrição |
|------|-----------|
| `/sso/login` | Página de login |
| `/sso/authenticate` | Processar login |
| `/sso/logout` | Logout |
| `/sso/admin` | Dashboard administrativo |
| `/sso/admin/providers` | Gerenciar providers |
| `/sso/admin/logs` | Logs de autenticação |
| `/sso/admin/settings` | Configurações SSO |

---

## 🔐 Segurança

- ✅ **Rate Limiting** - Proteção contra brute force
- ✅ **Password Hashing** - Bcrypt via Shield
- ✅ **CSRF Protection** - Token em todos os formulários
- ✅ **Session Security** - Armazenamento em banco de dados
- ✅ **Input Validation** - Validação em todos os controllers
- ✅ **SQL Injection Protection** - Query Builder do CI4

---

## 📝 Credenciais Padrão

**Usuário Admin:**
- Username: `admin`
- Email: `admin@example.com`
- Senha: `DtiFB@2025`
- Grupo: `superadmin`

⚠️ **Altere a senha em produção!**

---

## 🐛 Troubleshooting

### Problema: Tabelas não existem

```bash
php spark migrate --all
php spark db:seed MasterSeeder
```

### Problema: Provider não aparece

```bash
php spark db:seed Modules\\Sso\\Database\\Seeds\\DefaultProvidersSeeder
```

### Problema: Erro de conexão no Easypanel

Verifique `DB_HOST` - NÃO use `deppen_codeigniterdb` ou `localhost`.  
Use o hostname fornecido pelo Easypanel (ex: `mysql-123.easypanel.host`)

---

## 📚 Documentação

- **Deploy no Easypanel:** [EASYPANEL_DEPLOY.md](EASYPANEL_DEPLOY.md)
- **CodeIgniter 4:** https://codeigniter.com/user_guide/
- **Shield:** https://shield.codeigniter.com/

---

## 🤝 Contribuindo

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

## ✨ Tecnologias

- **CodeIgniter 4.6.3** - Framework PHP
- **CodeIgniter Shield 1.2.0** - Sistema de autenticação
- **MariaDB 11.2** - Banco de dados
- **Tailwind CSS** - Framework CSS (via CDN)
- **Chart.js** - Gráficos e visualizações
- **Docker** - Containerização (desenvolvimento)

---

**Desenvolvido com ❤️ usando CodeIgniter 4**
