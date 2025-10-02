# 🔧 Script de Verificação das Configurações Shield

Este arquivo pode ser usado temporariamente para debugar as configurações do Shield.

## Para testar, adicione este código no controller Admin::index():

```php
public function index()
{
    // Verificar se o usuário está logado
    if (!auth()->loggedIn()) {
        return redirect()->to('/login');
    }

    // DEBUG: Verificar configurações Shield
    echo "<h3>DEBUG: Configurações Shield</h3>";
    
    $authGroups = setting('AuthGroups.groups');
    echo "<h4>AuthGroups.groups:</h4>";
    var_dump($authGroups);
    
    $authMatrix = setting('AuthGroups.matrix');
    echo "<h4>AuthGroups.matrix:</h4>";
    var_dump($authMatrix);
    
    $authPermissions = setting('AuthGroups.permissions');
    echo "<h4>AuthGroups.permissions:</h4>";
    var_dump($authPermissions);
    
    echo "<hr>";
    
    // Estatísticas normais...
    $userProvider = auth()->getProvider();
    
    // Contar todos os usuários
    $totalUsers = $userProvider->countAllResults();
    
    // Contar usuários ativos (ban_until IS NULL)
    $activeUsers = $userProvider->where('active', 1)->countAllResults();
    
    // Contar usuários inativos
    $inactiveUsers = $totalUsers - $activeUsers;
    
    // Obter usuários recentes (últimos 10)
    $recentUsers = $userProvider->select('users.*, 
        (SELECT GROUP_CONCAT(auth_groups_users.group) FROM auth_groups_users WHERE auth_groups_users.user_id = users.id) as groups')
        ->orderBy('created_at', 'DESC')
        ->findAll(10);

    $stats = [
        'total_users' => $totalUsers,
        'active_users' => $activeUsers,
        'inactive_users' => $inactiveUsers,
        'recent_users' => $recentUsers,
        'current_user' => auth()->user()
    ];

    return view('Modules\\Admin\\Views\\dashboard', ['stats' => $stats]);
}
```

## Depois de testar, REMOVA o código de debug!