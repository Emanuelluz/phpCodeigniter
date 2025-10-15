<?php

namespace Modules\Sso\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration para tabela de logs de autenticação SSO
 */
class CreateSsoAuthLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'ID do usuário (null se falhou)',
            ],
            'provider_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'ID do provider usado',
            ],
            'provider_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'comment'    => 'Tipo de provider',
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'comment'    => 'Username tentado',
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
                'comment'    => 'IP do cliente',
            ],
            'user_agent' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
                'null'       => true,
                'comment'    => 'User agent do navegador',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['success', 'failed', 'blocked'],
                'comment'    => 'Status da tentativa',
            ],
            'failure_reason' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Razão da falha',
            ],
            'extra_data' => [
                'type'    => 'JSON',
                'null'    => true,
                'comment' => 'Dados adicionais',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('provider_id');
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        $this->forge->addKey(['username', 'created_at']);
        
        $this->forge->createTable('sso_auth_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('sso_auth_logs', true);
    }
}
