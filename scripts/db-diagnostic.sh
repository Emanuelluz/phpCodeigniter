#!/bin/bash

# Script para verificar e corrigir configuração de banco de dados no Docker
# Este script deve ser executado dentro do container Docker

echo "=== Diagnóstico de Configuração de Banco de Dados ==="

echo "1. Verificando arquivo .env:"
if [ -f "/var/www/html/.env" ]; then
    echo "Arquivo .env encontrado. Conteúdo relacionado ao banco:"
    grep -E "database\.|CI_DATABASE" /var/www/html/.env || echo "Nenhuma configuração de banco encontrada"
else
    echo "ERRO: Arquivo .env não encontrado!"
fi

echo ""
echo "2. Verificando variáveis de ambiente PHP:"
php -r "
echo 'CI_ENVIRONMENT: ' . (getenv('CI_ENVIRONMENT') ?: 'não definido') . PHP_EOL;
echo 'database.default.hostname: ' . (getenv('database.default.hostname') ?: 'não definido') . PHP_EOL;
echo 'database.default.database: ' . (getenv('database.default.database') ?: 'não definido') . PHP_EOL;
echo 'database.default.username: ' . (getenv('database.default.username') ?: 'não definido') . PHP_EOL;
echo 'database.default.DBDriver: ' . (getenv('database.default.DBDriver') ?: 'não definido') . PHP_EOL;
"

echo ""
echo "3. Testando conexão MariaDB:"
php -r "
try {
    \$conn = new mysqli('mariadb', 'ciuser', 'cipass', 'codeigniter', 3306);
    if (\$conn->connect_error) {
        echo 'ERRO: ' . \$conn->connect_error . PHP_EOL;
    } else {
        echo 'Conexão MariaDB: SUCESSO' . PHP_EOL;
        \$conn->close();
    }
} catch (Exception \$e) {
    echo 'ERRO na conexão: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "4. Verificando configuração CodeIgniter:"
php -r "
require_once '/var/www/html/app/Config/Boot/development.php';
require_once '/var/www/html/system/bootstrap.php';

// Simula o processo de carregamento do CI
\$app = new \Config\App();
\$paths = new \Config\Paths();

// Carrega as variáveis de ambiente
if (file_exists('/var/www/html/.env')) {
    \$dotenv = \Dotenv\Dotenv::createImmutable('/var/www/html');
    \$dotenv->load();
}

\$db = new \Config\Database();
echo 'Database defaultGroup: ' . \$db->defaultGroup . PHP_EOL;
echo 'Database DBDriver: ' . \$db->default['DBDriver'] . PHP_EOL;
echo 'Database hostname: ' . \$db->default['hostname'] . PHP_EOL;
echo 'Database database: ' . \$db->default['database'] . PHP_EOL;
"