<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Master Seeder
 * 
 * Executa todos os seeders necessários para configurar o sistema
 * Use: php spark db:seed MasterSeeder
 */
class MasterSeeder extends Seeder
{
    public function run()
    {
        echo "\n";
        echo "╔═══════════════════════════════════════════════════════════════╗\n";
        echo "║                    MASTER SEEDER - SSO                        ║\n";
        echo "║           Executando todos os seeders do sistema              ║\n";
        echo "╚═══════════════════════════════════════════════════════════════╝\n";
        echo "\n";

        // 1. Criar usuário administrador
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📌 [1/3] Criando usuário administrador...\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        try {
            $this->call('AdminUserSeeder');
            echo "✅ Usuário administrador criado com sucesso!\n\n";
        } catch (\Exception $e) {
            echo "⚠️  Aviso: " . $e->getMessage() . "\n\n";
        }

        // 2. Popular providers SSO
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📌 [2/3] Configurando providers SSO (Local, LDAP, OAuth, SAML)...\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        try {
            $this->call('Modules\\Sso\\Database\\Seeds\\DefaultProvidersSeeder');
            echo "✅ Providers SSO configurados com sucesso!\n\n";
        } catch (\Exception $e) {
            echo "⚠️  Aviso: " . $e->getMessage() . "\n\n";
        }

        // 3. Configurar settings SSO
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📌 [3/3] Configurando settings SSO (30 configurações)...\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        try {
            $this->call('SsoSettingsSeeder');
            echo "✅ Settings SSO configuradas com sucesso!\n\n";
        } catch (\Exception $e) {
            echo "⚠️  Aviso: " . $e->getMessage() . "\n\n";
        }

        // Resumo final
        echo "\n";
        echo "╔═══════════════════════════════════════════════════════════════╗\n";
        echo "║                    ✅ SETUP CONCLUÍDO!                        ║\n";
        echo "╚═══════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "📊 Resumo do Sistema:\n";
        echo "   • Usuário Admin: admin / DtiFB@2025\n";
        echo "   • Providers SSO: 1 configurado (Local)\n";
        echo "   • Configurações: 30 registros\n";
        echo "\n";
        echo "🚀 Próximos Passos:\n";
        echo "   1. Acesse: http://localhost:8080/sso/login\n";
        echo "   2. Faça login com: admin / DtiFB@2025\n";
        echo "   3. Acesse o dashboard: http://localhost:8080/sso/admin\n";
        echo "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "\n";
    }
}
