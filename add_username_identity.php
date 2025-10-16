<?php

// Script para adicionar identidade username_password ao usuário admin
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap CodeIgniter
$pathsPath = __DIR__ . '/app/Config/Paths.php';
$paths = new Config\Paths();
$bootstrap = require rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';
$app = Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();

echo "Verificando identidades do usuário admin (user_id=1)...\n\n";

// Buscar usuário admin
$user = $db->table('users')->where('id', 1)->get()->getRow();
if (!$user) {
    echo "❌ Usuário admin não encontrado!\n";
    exit(1);
}

echo "✅ Usuário encontrado: {$user->username} ({$user->email})\n\n";

// Buscar identidades existentes
$identities = $db->table('auth_identities')
    ->where('user_id', 1)
    ->get()
    ->getResult();

echo "Identidades atuais:\n";
foreach ($identities as $identity) {
    echo "  - {$identity->type}: {$identity->secret}\n";
}
echo "\n";

// Verificar se já existe username_password
$usernameIdentity = $db->table('auth_identities')
    ->where('user_id', 1)
    ->where('type', 'username_password')
    ->get()
    ->getRow();

if ($usernameIdentity) {
    echo "⚠️  Identidade username_password já existe!\n";
    exit(0);
}

// Buscar a senha hasheada da identidade email
$emailIdentity = $db->table('auth_identities')
    ->where('user_id', 1)
    ->where('type', 'email_password')
    ->get()
    ->getRow();

if (!$emailIdentity) {
    echo "❌ Identidade email_password não encontrada!\n";
    exit(1);
}

// Inserir nova identidade username_password
$data = [
    'user_id' => 1,
    'type' => 'username_password',
    'secret' => 'admin',  // username
    'secret2' => $emailIdentity->secret2,  // mesma senha hasheada
    'force_reset' => 0,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
];

$db->table('auth_identities')->insert($data);

echo "✅ Identidade username_password adicionada com sucesso!\n";
echo "\nAgora você pode fazer login com:\n";
echo "  - Username: admin\n";
echo "  - Email: admin@example.com\n";
echo "  - Senha: DtiFB@2025\n";
