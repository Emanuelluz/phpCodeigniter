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
ENV_DOCKER_BACKUP="$BASE_DIR/.env.docker.backup"

echo "=== Configurando .env para Docker/MariaDB ==="
echo "Diretório base: $BASE_DIR"

# Verifica se existe .env.docker.backup
if [ ! -f "$ENV_DOCKER_BACKUP" ]; then
    echo "ERRO: $ENV_DOCKER_BACKUP não encontrado!"
    exit 1
fi

# Copia .env.docker.backup para .env
cp "$ENV_DOCKER_BACKUP" "$ENV_FILE"
echo "Copiado $ENV_DOCKER_BACKUP para $ENV_FILE"

# Força configurações específicas para garantir MariaDB
cat >> "$ENV_FILE" << 'EOF'

# Configurações forçadas para Docker/MariaDB (adicionadas pelo script)
CI_DATABASE_GROUP = default
database.default.hostname = mariadb
database.default.database = codeigniter
database.default.username = ciuser
database.default.password = cipass
database.default.DBDriver = MySQLi
database.default.port = 3306

EOF

echo "Configurações adicionais adicionadas ao .env"

# Verifica o resultado
echo ""
echo "=== Verificação do arquivo .env final ==="
grep -E "database\.|CI_" "$ENV_FILE"

echo ""
echo "=== Configuração concluída ==="