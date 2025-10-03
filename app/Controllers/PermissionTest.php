<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class PermissionTest extends BaseController
{
    public function testAddPermission()
    {
        // Simular dados de uma nova permissão
        $permissionName = 'test.permission';
        $description = 'Teste de criação de permissão';
        
        try {
            // Verificar se arquivo é gravável
            $configPath = APPPATH . 'Config/AuthGroups.php';
            
            $diagnostics = [
                'file_path' => $configPath,
                'file_exists' => file_exists($configPath),
                'is_readable' => is_readable($configPath),
                'is_writable' => is_writable($configPath),
                'permissions' => substr(sprintf('%o', fileperms($configPath)), -4),
                'file_size' => filesize($configPath)
            ];

            if (!is_writable($configPath)) {
                return $this->response->setJSON([
                    'error' => 'Arquivo Config/AuthGroups.php não tem permissão de escrita',
                    'diagnostics' => $diagnostics
                ]);
            }

            // Testar leitura do arquivo
            $content = file_get_contents($configPath);
            if ($content === false) {
                return $this->response->setJSON([
                    'error' => 'Não foi possível ler o arquivo Config/AuthGroups.php',
                    'diagnostics' => $diagnostics
                ]);
            }

            // Verificar se já existe
            if (strpos($content, "'{$permissionName}' =>") !== false) {
                return $this->response->setJSON([
                    'error' => 'Permissão já existe no arquivo',
                    'diagnostics' => $diagnostics
                ]);
            }

            // Testar padrão de regex
            $pattern = '/(\s+)(];)(\s*\/\*\*[\s\S]*?Permissions Matrix)/';
            if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                $insertPosition = $matches[1][1];
                $indentation = $matches[1][0];
                
                // Simular inserção
                $newPermission = "{$indentation}'{$permissionName}' => '{$description}',\n";
                $beforePermissions = substr($content, 0, $insertPosition);
                $afterPermissions = substr($content, $insertPosition);
                $newContent = $beforePermissions . $newPermission . $afterPermissions;
                
                // Testar escrita
                $backupContent = $content; // Backup do conteúdo original
                if (file_put_contents($configPath, $newContent) === false) {
                    return $this->response->setJSON([
                        'error' => 'Falha ao escrever no arquivo',
                        'diagnostics' => $diagnostics
                    ]);
                }

                // Restaurar conteúdo original
                file_put_contents($configPath, $backupContent);

                return $this->response->setJSON([
                    'success' => 'Teste de criação de permissão bem-sucedido!',
                    'permission' => $permissionName,
                    'description' => $description,
                    'insert_position' => $insertPosition,
                    'indentation_length' => strlen($indentation),
                    'diagnostics' => $diagnostics
                ]);
            } else {
                // Testar padrão fallback
                $permissionsStart = strpos($content, 'public array $permissions = [');
                $searchStart = $permissionsStart;
                $permissionsEnd = strpos($content, '];', $searchStart);
                
                return $this->response->setJSON([
                    'error' => 'Padrão regex não encontrado no arquivo',
                    'permissions_start' => $permissionsStart,
                    'permissions_end' => $permissionsEnd,
                    'content_around_end' => substr($content, max(0, $permissionsEnd - 100), 200),
                    'diagnostics' => $diagnostics
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Exceção: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'diagnostics' => $diagnostics ?? []
            ]);
        }
    }

    public function testAddGroup()
    {
        // Simular dados de um novo grupo
        $groupName = 'test_group';
        $title = 'Grupo de Teste';
        $description = 'Descrição do grupo de teste';
        $permissions = ['users.view', 'posts.view'];
        
        try {
            $configPath = APPPATH . 'Config/AuthGroups.php';
            
            $diagnostics = [
                'file_path' => $configPath,
                'file_exists' => file_exists($configPath),
                'is_readable' => is_readable($configPath),
                'is_writable' => is_writable($configPath),
                'permissions' => substr(sprintf('%o', fileperms($configPath)), -4)
            ];

            if (!is_writable($configPath)) {
                return $this->response->setJSON([
                    'error' => 'Arquivo Config/AuthGroups.php não tem permissão de escrita',
                    'diagnostics' => $diagnostics
                ]);
            }

            $content = file_get_contents($configPath);
            if ($content === false) {
                return $this->response->setJSON([
                    'error' => 'Não foi possível ler o arquivo',
                    'diagnostics' => $diagnostics
                ]);
            }

            // Verificar se o grupo já existe
            if (strpos($content, "'{$groupName}' =>") !== false) {
                return $this->response->setJSON([
                    'error' => 'Grupo já existe no arquivo',
                    'diagnostics' => $diagnostics
                ]);
            }

            // Procurar o final da array $groups
            $groupsStart = strpos($content, 'public array $groups = [');
            if ($groupsStart === false) {
                return $this->response->setJSON([
                    'error' => 'Não foi possível encontrar public array $groups',
                    'diagnostics' => $diagnostics
                ]);
            }
            
            // Encontrar o próximo ]; após a declaração de $groups
            $searchStart = $groupsStart + strlen('public array $groups = [');
            $nextArrayStart = strpos($content, 'public array $', $searchStart);
            
            if ($nextArrayStart !== false) {
                // Procurar ]; antes da próxima array
                $groupsEnd = strrpos(substr($content, 0, $nextArrayStart), '];');
            } else {
                // Se não há próxima array, procurar o último ];
                $groupsEnd = strrpos($content, '];');
            }
            
            if ($groupsEnd === false) {
                return $this->response->setJSON([
                    'error' => 'Não foi possível encontrar o final da array $groups',
                    'groups_start' => $groupsStart,
                    'next_array_start' => $nextArrayStart,
                    'diagnostics' => $diagnostics
                ]);
            }

            // Preparar o novo grupo
            $newGroup = "        '{$groupName}' => [\n";
            $newGroup .= "            'title'       => '{$title}',\n";
            $newGroup .= "            'description' => '{$description}',\n";
            $newGroup .= "        ],\n";

            // Simular inserção (sem salvar)
            $beforeGroups = substr($content, 0, $groupsEnd);
            $afterGroups = substr($content, $groupsEnd);
            $newContent = $beforeGroups . $newGroup . $afterGroups;

            return $this->response->setJSON([
                'success' => 'Teste de criação de grupo bem-sucedido!',
                'group' => $groupName,
                'title' => $title,
                'description' => $description,
                'groups_start' => $groupsStart,
                'groups_end' => $groupsEnd,
                'next_array_start' => $nextArrayStart,
                'new_group_content' => $newGroup,
                'diagnostics' => $diagnostics
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Exceção: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'diagnostics' => $diagnostics ?? []
            ]);
        }
    }
}