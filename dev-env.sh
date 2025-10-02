#!/bin/bash

# Script para trocar entre configurações de ambiente
# Uso: ./dev-env.sh [local|docker]

if [ "$1" = "local" ]; then
    echo "🔄 Configurando ambiente LOCAL (SQLite, sem Docker)..."
    
    # Backup da configuração atual se necessário
    if [ -f .env ] && [ ! -f .env.docker.backup ]; then
        cp .env .env.docker.backup
        echo "✅ Backup da configuração Docker salvo em .env.docker.backup"
    fi
    
    # Usar configuração local
    cp .env.local .env
    echo "✅ Configuração local ativada (.env.local → .env)"
    
    # Criar diretório writable se não existir
    mkdir -p writable
    
    echo "🚀 Agora você pode executar:"
    echo "   php spark serve"
    echo "   php spark migrate"
    echo "   php spark db:seed AdminUserSeeder"
    
elif [ "$1" = "docker" ]; then
    echo "🔄 Configurando ambiente DOCKER..."
    
    if [ -f .env.docker.backup ]; then
        cp .env.docker.backup .env
        echo "✅ Configuração Docker restaurada"
    else
        echo "⚠️  Backup do Docker não encontrado. Verifique manualmente o .env"
    fi
    
    echo "🚀 Agora você pode executar:"
    echo "   docker compose up -d"
    echo "   docker exec -it [container] php spark migrate"
    
else
    echo "📖 Uso: $0 [local|docker]"
    echo ""
    echo "Ambientes disponíveis:"
    echo "  local  - SQLite, desenvolvimento sem Docker"
    echo "  docker - MariaDB, ambiente Docker completo"
    echo ""
    echo "Arquivos de configuração:"
    echo "  .env.local - Configuração para desenvolvimento local"
    echo "  .env.docker.backup - Backup da configuração Docker"
fi