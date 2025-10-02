<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class TestPermissions extends BaseController
{
    public function index()
    {
        if (!auth()->loggedIn()) {
            return 'Usuário não está logado';
        }

        $user = auth()->user();
        $groups = $user->getGroups();
        $permissions = $user->getPermissions();

        $output = "Usuário: " . $user->email . "\n";
        $output .= "Grupos: " . implode(', ', $groups) . "\n";
        $output .= "Permissões: " . implode(', ', $permissions) . "\n";
        
        // Testar permissão específica
        $hasAdminAccess = $user->can('admin.access');
        $output .= "Tem admin.access: " . ($hasAdminAccess ? 'SIM' : 'NÃO') . "\n";

        return '<pre>' . $output . '</pre>';
    }
}