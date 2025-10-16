#!/bin/bash

# ============================================================================
# Script de Setup Automático - Sistema SSO
# ============================================================================
# Execute este script após o primeiro deploy no Easypanel
# Uso: bash setup.sh
# ============================================================================

set -e  # Para execução em caso de erro

echo ""
echo "╔═══════════════════════════════════════════════════════════════╗"
echo "║           SETUP AUTOMÁTICO - SISTEMA SSO                      ║"
echo "║                  Easypanel Deploy                             ║"
echo "╚═══════════════════════════════════════════════════════════════╝"
echo ""

# Verificar se estamos no diretório correto
if [ ! -f "spark" ]; then
    echo "❌ Erro: Arquivo 'spark' não encontrado!"
    echo "   Execute este script a partir do diretório raiz do projeto."
    exit 1
fi

# 1. Verificar conexão com banco de dados
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📌 [1/3] Verificando conexão com banco de dados..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if php spark db:table --show > /dev/null 2>&1; then
    echo "✅ Conexão com banco de dados OK!"
else
    echo "❌ Erro: Não foi possível conectar ao banco de dados!"
    echo "   Verifique as variáveis de ambiente: DB_HOST, DB_NAME, DB_USER, DB_PASS"
    exit 1
fi

echo ""

# 2. Executar migrations
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📌 [2/3] Executando migrations..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

php spark migrate --all

echo "✅ Migrations executadas com sucesso!"
echo ""

# 3. Executar seeders
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📌 [3/3] Populando banco de dados..."
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

php spark db:seed MasterSeeder

echo ""
echo "╔═══════════════════════════════════════════════════════════════╗"
echo "║                    ✅ SETUP CONCLUÍDO!                        ║"
echo "╚═══════════════════════════════════════════════════════════════╝"
echo ""
echo "📊 Sistema configurado com sucesso!"
echo ""
echo "🔐 Credenciais de acesso:"
echo "   • Usuário: admin"
echo "   • Senha: DtiFB@2025"
echo ""
echo "🌐 Acesse sua aplicação:"
echo "   • Login: https://seu-dominio.com/sso/login"
echo "   • Dashboard: https://seu-dominio.com/sso/admin"
echo ""
echo "⚠️  IMPORTANTE: Altere a senha do admin após o primeiro login!"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
