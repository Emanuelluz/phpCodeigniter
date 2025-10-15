<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSsoSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INTEGER',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'setting_key' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'setting_value' => [
                'type' => 'TEXT',
            ],
            'setting_group' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'general',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_system' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'System settings cannot be deleted',
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
        $this->forge->addKey('setting_group');
        $this->forge->createTable('sso_settings');
    }

    public function down()
    {
        $this->forge->dropTable('sso_settings');
    }
}
