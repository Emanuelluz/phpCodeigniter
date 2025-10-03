# Configuração de Ambientes

Este projeto utiliza diferentes arquivos de configuração para diferentes ambientes:

## 📁 Arquivos de Ambiente

### `.env.local` - Desenvolvimento Local
- **Uso**: Desenvolvimento local sem Docker
- **Banco**: SQLite3 (`writable/database.db`)
- **URL Base**: `http://localhost:8080`
- **Sessões**: Arquivo (`WRITEPATH . 'session'`)

### `.env.production` - Produção/Homologação
- **Uso**: Deploy via GitHub Actions com Docker
- **Banco**: MariaDB (container `mariadb`)
- **URL Base**: `https://pppr.ecl.dev.br`
- **Sessões**: Arquivo (`WRITEPATH . 'session'`)

### `.env` - Arquivo Ativo
- **Uso**: Arquivo atual sendo usado pela aplicação
- **Criação**: Copiado automaticamente pelo script `setup-env-docker.sh` durante o build Docker

## 🚀 Como Usar

### Desenvolvimento Local (SQLite)
```bash
# Copiar configuração local
cp .env.local .env

# Instalar dependências
composer install

# Executar migrações
php spark migrate

# Criar usuário admin
php spark db:seed AdminUserSeeder

# Iniciar servidor local
php spark serve
```

### Produção (Docker)
```bash
# O deploy é automático via GitHub Actions
# O script setup-env-docker.sh copia .env.production para .env
git push origin main
```

## 🔧 Scripts de Configuração

### `scripts/setup-env-docker.sh`
- Detecta ambiente Docker automaticamente
- Copia `.env.production` para `.env`
- Cria diretório `writable/session` com permissões corretas
- Usado automaticamente durante build Docker

### `scripts/db-diagnostic.sh`
- Diagnóstica problemas de conexão de banco
- Mostra configurações atuais
- Útil para debug em produção

## 📋 Migração de Configuração

Se você estava usando `.env.docker.backup`, agora use `.env.production`:
- ✅ `.env.production` - Nova configuração para produção
- ❌ `.env.docker.backup` - Arquivo removido (obsoleto)

## 🔒 Segurança

- **Nunca commitar** arquivo `.env` com credenciais reais
- `.env.local` e `.env.production` são templates seguros
- Credenciais de produção devem ser configuradas via variáveis de ambiente ou secrets