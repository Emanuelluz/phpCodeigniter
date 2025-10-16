<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FixMigrationSeeder extends Seeder
{
    public function run()
    {
        // Verificar se a migration já está registrada
        $existing = $this->db->table('migrations')
            ->where('version', '2021-11-14-143905')
            ->get()
            ->getRow();

        if (!$existing) {
            $this->db->table('migrations')->insert([
                'version' => '2021-11-14-143905',
                'class' => 'CodeIgniter\\Settings\\Database\\Migrations\\AddContextColumn',
                'group' => 'default',
                'namespace' => 'CodeIgniter\\Settings',
                'time' => time(),
                'batch' => 3
            ]);
            
            echo "✓ Migration 'AddContextColumn' marcada como executada!\n";
        } else {
            echo "- Migration 'AddContextColumn' já está marcada como executada.\n";
        }
    }
}
