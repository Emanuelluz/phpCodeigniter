<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;

class Admin extends BaseController
{
    public function index()
    {
        // Verifica se o usuário está autenticado (já protegido pelo filtro 'session')
        $user = auth()->user();
        
        // Obtém estatísticas para o dashboard
        $userProvider = auth()->getProvider();
        
        $stats = [
            'total_users' => $userProvider->countAll(),
            'active_users' => $userProvider->where('active', 1)->countAllResults(),
            'inactive_users' => $userProvider->where('active', 0)->countAllResults(),
            'current_user' => $user,
        ];
        
        // Usuários recentes (últimos 5)
        $recentUsers = $userProvider
            ->withIdentities()
            ->withGroups()
            ->orderBy('created_at', 'DESC')
            ->findAll(5);
            
        return view('Modules\\Admin\\Views\\dashboard', [
            'stats' => $stats,
            'recentUsers' => $recentUsers
        ]);
    }
}