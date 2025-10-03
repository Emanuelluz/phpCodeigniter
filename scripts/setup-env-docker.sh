#!/bin/bash

# Script para configurar .env correto para Docker/MariaDB
# Executa durante o build do Docker para garantir configuração correta

# Detecta o diretório correto (onde está o script ou /var/www/html no Docker)
if [ -f "/var/www/html/.env.docker.backup" ]; then
    BASE_DIR="/var/www/html"
else
    BASE_DIR="$(cd "$(dirname "$0")/.." && pwd)"
fi

ENV_FILE="$BASE_DIR/.env"
ENV_PRODUCTION="$BASE_DIR/.env.production"

echo "=== Configurando .env para Docker/MariaDB ==="
echo "Diretório base: $BASE_DIR"

# Verifica se existe .env.production
if [ ! -f "$ENV_PRODUCTION" ]; then
    echo "ERRO: $ENV_PRODUCTION não encontrado!"
    exit 1
fi

# Copia .env.production para .env
cp "$ENV_PRODUCTION" "$ENV_FILE"
echo "Copiado $ENV_PRODUCTION para $ENV_FILE"

# Garante que o diretório de sessões existe e tem permissões corretas
SESSION_DIR="$BASE_DIR/writable/session"
if [ ! -d "$SESSION_DIR" ]; then
    echo "Criando diretório de sessões: $SESSION_DIR"
    mkdir -p "$SESSION_DIR"
fi
chmod 755 "$SESSION_DIR"
echo "Diretório de sessões configurado: $SESSION_DIR"

# Verifica o resultado
echo ""
echo "=== Verificação do arquivo .env final ==="
grep -E "database\.|CI_|session\." "$ENV_FILE" | head -10

echo ""
echo "=== Configuração concluída ==="