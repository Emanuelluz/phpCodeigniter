<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration para tabela de providers de autenticação SSO
 */
class CreateSsoProvidersTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'comment'    => 'Nome identificador do provider',
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['local', 'ldap', 'oauth', 'saml'],
                'default'    => 'local',
                'comment'    => 'Tipo de provider de autenticação',
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'comment'    => 'Título exibido para o usuário',
            ],
            'description' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Descrição do provider',
            ],
            'config' => [
                'type'    => 'JSON',
                'comment' => 'Configurações específicas do provider',
            ],
            'is_enabled' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
                'comment'    => 'Provider está ativo',
            ],
            'is_default' => [
                'type'       => 'BOOLEAN',
                'default'    => false,
                'comment'    => 'Provider padrão para login',
            ],
            'priority' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Ordem de exibição',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('type');
        $this->forge->addKey('is_enabled');
        $this->forge->addKey('is_default');
        $this->forge->addUniqueKey('name');
        
        $this->forge->createTable('sso_providers', true);
    }

    public function down()
    {
        $this->forge->dropTable('sso_providers', true);
    }
}
