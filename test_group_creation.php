<?php

// Teste simples do método addGroupToConfig
define('APPPATH', __DIR__ . '/app/');

// Simular a classe Groups com apenas o método necessário
class TestGroups {

    private function addGroupToConfig($groupName, $title, $description, $permissions)
    {
        $configPath = APPPATH . 'Config/AuthGroups.php';

        if (!is_writable($configPath)) {
            throw new \Exception('Arquivo Config/AuthGroups.php não tem permissão de escrita');
        }

        // Ler o arquivo atual
        $content = file_get_contents($configPath);
        if ($content === false) {
            throw new \Exception('Não foi possível ler o arquivo Config/AuthGroups.php');
        }

        // Verificar se o grupo já existe
        if (strpos($content, "'{$groupName}' =>") !== false) {
            throw new \Exception('Grupo já existe no arquivo de configuração');
        }

        // Escapar caracteres especiais
        $escapedTitle = addslashes($title);
        $escapedDescription = addslashes($description);

        // Dividir o conteúdo em linhas para manipulação mais precisa
        $lines = explode("\n", $content);
        $groupsStart = -1;
        $groupsEnd = -1;

        // Encontrar os limites da array $groups
        foreach ($lines as $i => $line) {
            if (strpos($line, 'public $groups = [') !== false) {
                $groupsStart = $i;
            }
            if ($groupsStart !== -1 && strpos($line, '];') !== false && $groupsEnd === -1) {
                $groupsEnd = $i;
                break;
            }
        }

        if ($groupsStart === -1 || $groupsEnd === -1) {
            throw new \Exception('Não foi possível encontrar a seção $groups no arquivo');
        }

        // Preparar o novo grupo
        $newGroupLines = [
            "        '{$groupName}' => [",
            "            'title'       => '{$escapedTitle}',",
            "            'description' => '{$escapedDescription}',",
            "        ],"
        ];

        // Inserir o novo grupo antes do fechamento da array
        array_splice($lines, $groupsEnd, 0, $newGroupLines);

        // Reconstruir o conteúdo
        $content = implode("\n", $lines);

        // Adicionar permissões à matriz se especificadas
        if (!empty($permissions)) {
            $this->addPermissionsToMatrix($groupName, $permissions);
        }

        // Salvar o arquivo
        if (file_put_contents($configPath, $content) === false) {
            throw new \Exception('Não foi possível salvar o arquivo Config/AuthGroups.php');
        }

        // Limpar cache do OPcache se estiver ativo
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($configPath);
        }
    }

    private function addPermissionsToMatrix($groupName, $permissions)
    {
        $configPath = APPPATH . 'Config/AuthGroups.php';

        if (!is_writable($configPath)) {
            throw new \Exception('Arquivo Config/AuthGroups.php não tem permissão de escrita');
        }

        // Ler o arquivo atual
        $content = file_get_contents($configPath);
        if ($content === false) {
            throw new \Exception('Não foi possível ler o arquivo Config/AuthGroups.php');
        }

        // Dividir o conteúdo em linhas para manipulação mais precisa
        $lines = explode("\n", $content);
        $matrixStart = -1;
        $matrixEnd = -1;

        // Encontrar os limites da array $matrix
        foreach ($lines as $i => $line) {
            if (strpos($line, 'public array $matrix = [') !== false) {
                $matrixStart = $i;
            }
            if ($matrixStart !== -1 && strpos($line, '];') !== false && $matrixEnd === -1) {
                $matrixEnd = $i;
                break;
            }
        }

        if ($matrixStart === -1 || $matrixEnd === -1) {
            throw new \Exception('Não foi possível encontrar a seção $matrix no arquivo');
        }

        // Preparar as permissões do grupo
        $permissionsList = "'" . implode("',\n            '", $permissions) . "'";
        $newMatrixLines = [
            "        '{$groupName}' => [",
            "            {$permissionsList},",
            "        ],"
        ];

        // Inserir o novo grupo na matriz antes do fechamento da array
        array_splice($lines, $matrixEnd, 0, $newMatrixLines);

        // Reconstruir o conteúdo
        $content = implode("\n", $lines);

        // Salvar o arquivo
        if (file_put_contents($configPath, $content) === false) {
            throw new \Exception('Não foi possível salvar o arquivo Config/AuthGroups.php');
        }

        // Limpar cache do OPcache se estiver ativo
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($configPath);
        }
    }

    public function test()
    {
        try {
            echo "Testando criação de grupo 'teste_group'...\n";

            $this->addGroupToConfig('teste_group', 'Grupo de Teste', 'Descrição do grupo de teste', ['admin.access', 'users.view']);

            echo "Grupo criado com sucesso!\n";

            // Verificar se o arquivo foi modificado corretamente
            $content = file_get_contents(APPPATH . 'Config/AuthGroups.php');
            if (strpos($content, "'teste_group' =>") !== false) {
                echo "✓ Grupo encontrado no arquivo de configuração\n";
            } else {
                echo "✗ Grupo não encontrado no arquivo de configuração\n";
            }

            // Verificar sintaxe PHP
            $syntaxCheck = shell_exec("php -l " . APPPATH . "Config/AuthGroups.php");
            if (strpos($syntaxCheck, 'No syntax errors') !== false) {
                echo "✓ Sintaxe PHP válida\n";
            } else {
                echo "✗ Erro de sintaxe: $syntaxCheck\n";
            }

        } catch (Exception $e) {
            echo "Erro: " . $e->getMessage() . "\n";
        }
    }
}

// Executar o teste
$test = new TestGroups();
$test->test();