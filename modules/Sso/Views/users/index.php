<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Usuários SSO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans antialiased">
    
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Gerenciamento de Usuários</h1>
                    <p class="text-sm text-gray-500 mt-1">Administração de contas e permissões SSO</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="/sso/admin" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                    <button onclick="showExportModal()" class="px-4 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition border border-green-200">
                        <i class="fas fa-download mr-2"></i>Exportar
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Stats Summary -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase">Total</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1"><?= number_format($stats['total'] ?? 0) ?></p>
                    </div>
                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase">Ativos</p>
                        <p class="text-2xl font-bold text-green-600 mt-1"><?= number_format($stats['active'] ?? 0) ?></p>
                    </div>
                    <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-check text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase">Bloqueados</p>
                        <p class="text-2xl font-bold text-red-600 mt-1"><?= number_format($stats['blocked'] ?? 0) ?></p>
                    </div>
                    <div class="h-10 w-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-lock text-red-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase">Online</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1"><?= number_format($stats['online'] ?? 0) ?></p>
                    </div>
                    <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-circle text-purple-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase">Novos (7d)</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1"><?= number_format($stats['new_week'] ?? 0) ?></p>
                    </div>
                    <div class="h-10 w-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-plus text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="/sso/admin/users" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700 mb-2">Buscar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input 
                            type="text" 
                            name="search" 
                            value="<?= esc($_GET['search'] ?? '') ?>"
                            placeholder="Nome, email ou username..."
                            class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>✅ Ativos</option>
                        <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>⏸️ Inativos</option>
                        <option value="blocked" <?= ($_GET['status'] ?? '') === 'blocked' ? 'selected' : '' ?>>🔒 Bloqueados</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">Grupo</label>
                    <select name="group" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <?php foreach ($groups ?? [] as $group): ?>
                            <option value="<?= $group['id'] ?>" <?= ($_GET['group'] ?? '') == $group['id'] ? 'selected' : '' ?>>
                                <?= esc($group['title']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">Provider</label>
                    <select name="provider" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <?php foreach ($providers ?? [] as $provider): ?>
                            <option value="<?= esc($provider['name']) ?>" <?= ($_GET['provider'] ?? '') === $provider['name'] ? 'selected' : '' ?>>
                                <?= esc($provider['title']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        <i class="fas fa-search mr-2"></i>Filtrar
                    </button>
                    <a href="/sso/admin/users" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>

            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Usuários Cadastrados
                        <?php if (isset($_GET['search']) || isset($_GET['status']) || isset($_GET['group'])): ?>
                            <span class="text-sm font-normal text-gray-500">(filtrados)</span>
                        <?php endif ?>
                    </h2>
                    <div class="flex items-center space-x-3">
                        <div class="text-sm text-gray-500">
                            <?= count($users ?? []) ?> usuários
                        </div>
                        <div class="flex space-x-1">
                            <button onclick="toggleView('grid')" id="gridViewBtn" class="px-3 py-1.5 text-gray-600 hover:bg-gray-100 rounded transition">
                                <i class="fas fa-th"></i>
                            </button>
                            <button onclick="toggleView('list')" id="listViewBtn" class="px-3 py-1.5 bg-gray-100 text-blue-600 rounded transition">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- List View (Default) -->
            <div id="listView" class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usuário
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Grupos
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Provider Principal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Último Login
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                                    <p class="text-gray-500 font-medium">Nenhum usuário encontrado</p>
                                    <p class="text-sm text-gray-400 mt-1">Ajuste os filtros ou adicione novos usuários</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="relative">
                                            <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                                <?= strtoupper(substr($user['username'] ?? $user['email'], 0, 2)) ?>
                                            </div>
                                            <?php if ($user['is_online'] ?? false): ?>
                                                <span class="absolute bottom-0 right-0 h-3 w-3 bg-green-500 border-2 border-white rounded-full"></span>
                                            <?php endif ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= esc($user['username'] ?? 'N/A') ?>
                                                <?php if ($user['is_admin'] ?? false): ?>
                                                    <i class="fas fa-crown text-yellow-500 ml-1" title="Administrador"></i>
                                                <?php endif ?>
                                            </div>
                                            <div class="text-sm text-gray-500"><?= esc($user['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        <?php foreach ($user['groups'] ?? [] as $group): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                <?= esc($group) ?>
                                            </span>
                                        <?php endforeach ?>
                                        <?php if (empty($user['groups'])): ?>
                                            <span class="text-xs text-gray-400 italic">Sem grupo</span>
                                        <?php endif ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= esc($user['provider'] ?? 'local') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($user['last_login']): ?>
                                        <div class="text-sm text-gray-900"><?= date('d/m/Y', strtotime($user['last_login'])) ?></div>
                                        <div class="text-xs text-gray-500"><?= date('H:i', strtotime($user['last_login'])) ?></div>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400 italic">Nunca</span>
                                    <?php endif ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusConfig = [
                                        'active' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-circle', 'label' => 'Ativo'],
                                        'inactive' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-pause-circle', 'label' => 'Inativo'],
                                        'blocked' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-lock', 'label' => 'Bloqueado']
                                    ];
                                    $status = $statusConfig[$user['status'] ?? 'active'] ?? $statusConfig['active'];
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $status['bg'] ?> <?= $status['text'] ?>">
                                        <i class="fas <?= $status['icon'] ?> mr-1"></i>
                                        <?= $status['label'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button onclick="showUserDetails(<?= $user['id'] ?>)" class="text-blue-600 hover:text-blue-800 transition" title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="/admin/users/<?= $user['id'] ?>/edit" class="text-purple-600 hover:text-purple-800 transition" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="showUserLogs(<?= $user['id'] ?>)" class="text-green-600 hover:text-green-800 transition" title="Histórico">
                                            <i class="fas fa-history"></i>
                                        </button>
                                        <?php if ($user['status'] === 'active'): ?>
                                            <button onclick="toggleUserStatus(<?= $user['id'] ?>, 'block')" class="text-orange-600 hover:text-orange-800 transition" title="Bloquear">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        <?php else: ?>
                                            <button onclick="toggleUserStatus(<?= $user['id'] ?>, 'activate')" class="text-green-600 hover:text-green-800 transition" title="Ativar">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        <?php endif ?>
                                        <button onclick="confirmDeleteUser(<?= $user['id'] ?>)" class="text-red-600 hover:text-red-800 transition" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>

            <!-- Grid View (Hidden by default) -->
            <div id="gridView" class="hidden p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <?php foreach ($users ?? [] as $user): ?>
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-start justify-between mb-3">
                            <div class="relative">
                                <div class="h-14 w-14 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                    <?= strtoupper(substr($user['username'] ?? $user['email'], 0, 2)) ?>
                                </div>
                                <?php if ($user['is_online'] ?? false): ?>
                                    <span class="absolute bottom-0 right-0 h-4 w-4 bg-green-500 border-2 border-white rounded-full"></span>
                                <?php endif ?>
                            </div>
                            <?php
                            $statusConfig = [
                                'active' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-circle'],
                                'inactive' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-pause-circle'],
                                'blocked' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-lock']
                            ];
                            $status = $statusConfig[$user['status'] ?? 'active'] ?? $statusConfig['active'];
                            ?>
                            <span class="px-2 py-1 rounded-full text-xs font-medium <?= $status['bg'] ?> <?= $status['text'] ?>">
                                <i class="fas <?= $status['icon'] ?>"></i>
                            </span>
                        </div>
                        <h3 class="font-semibold text-gray-900 truncate mb-1">
                            <?= esc($user['username'] ?? 'N/A') ?>
                            <?php if ($user['is_admin'] ?? false): ?>
                                <i class="fas fa-crown text-yellow-500 text-xs ml-1"></i>
                            <?php endif ?>
                        </h3>
                        <p class="text-sm text-gray-500 truncate mb-3"><?= esc($user['email']) ?></p>
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                            <span><i class="fas fa-sign-in-alt mr-1"></i><?= $user['login_count'] ?? 0 ?></span>
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-600 rounded"><?= esc($user['provider'] ?? 'local') ?></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="showUserDetails(<?= $user['id'] ?>)" class="flex-1 px-3 py-1.5 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 transition text-xs">
                                <i class="fas fa-eye mr-1"></i>Detalhes
                            </button>
                            <button class="px-3 py-1.5 border border-gray-300 text-gray-600 rounded hover:bg-gray-50 transition text-xs">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach ?>
                </div>
            </div>

            <!-- Pagination -->
            <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Página <span class="font-medium"><?= $pager->getCurrentPage() ?></span> de 
                        <span class="font-medium"><?= $pager->getPageCount() ?></span>
                    </div>
                    <div class="flex space-x-2">
                        <?php if ($pager->hasPrevious()): ?>
                            <a href="<?= $pager->getPrevious() ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-100 transition">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif ?>
                        
                        <?php foreach ($pager->links() as $link): ?>
                            <a href="<?= $link['uri'] ?>" class="px-3 py-2 <?= $link['active'] ? 'bg-blue-600 text-white' : 'border border-gray-300 hover:bg-gray-100' ?> rounded-lg text-sm transition">
                                <?= $link['title'] ?>
                            </a>
                        <?php endforeach ?>

                        <?php if ($pager->hasNext()): ?>
                            <a href="<?= $pager->getNext() ?>" class="px-3 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-100 transition">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <?php endif ?>

        </div>

    </main>

    <!-- User Details Modal -->
    <div id="userDetailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Detalhes do Usuário</h3>
                <button onclick="closeUserDetails()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="userDetailsContent" class="p-6">
                <div class="flex items-center justify-center py-12">
                    <i class="fas fa-spinner fa-spin text-blue-600 text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteUserModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Excluir Usuário</h3>
                <p class="text-gray-600 text-center mb-6">Esta ação é irreversível. Todos os dados do usuário serão permanentemente excluídos.</p>
                <div class="flex space-x-3">
                    <button onclick="closeDeleteUser()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition">
                        Cancelar
                    </button>
                    <form id="deleteUserForm" method="POST" class="flex-1">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Excluir Permanentemente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle between list and grid view
        function toggleView(view) {
            const listView = document.getElementById('listView');
            const gridView = document.getElementById('gridView');
            const listBtn = document.getElementById('listViewBtn');
            const gridBtn = document.getElementById('gridViewBtn');

            if (view === 'grid') {
                listView.classList.add('hidden');
                gridView.classList.remove('hidden');
                listBtn.classList.remove('bg-gray-100', 'text-blue-600');
                gridBtn.classList.add('bg-gray-100', 'text-blue-600');
            } else {
                listView.classList.remove('hidden');
                gridView.classList.add('hidden');
                gridBtn.classList.remove('bg-gray-100', 'text-blue-600');
                listBtn.classList.add('bg-gray-100', 'text-blue-600');
            }
        }

        // Show user details modal
        function showUserDetails(userId) {
            document.getElementById('userDetailsModal').classList.remove('hidden');
            // AJAX call to load user details
            fetch(`/api/users/${userId}`)
                .then(response => response.json())
                .then(data => {
                    // Render user details
                    document.getElementById('userDetailsContent').innerHTML = `
                        <div class="space-y-6">
                            <div class="flex items-center space-x-4">
                                <div class="h-20 w-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-2xl">
                                    ${data.username.substring(0, 2).toUpperCase()}
                                </div>
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900">${data.username}</h4>
                                    <p class="text-gray-500">${data.email}</p>
                                </div>
                            </div>
                            <!-- More details here -->
                        </div>
                    `;
                })
                .catch(error => {
                    document.getElementById('userDetailsContent').innerHTML = `
                        <div class="text-center text-red-600">
                            <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                            <p>Erro ao carregar detalhes</p>
                        </div>
                    `;
                });
        }

        function closeUserDetails() {
            document.getElementById('userDetailsModal').classList.add('hidden');
        }

        function showUserLogs(userId) {
            window.location.href = `/sso/admin/logs?user_id=${userId}`;
        }

        function toggleUserStatus(userId, action) {
            if (confirm(`Tem certeza que deseja ${action === 'block' ? 'bloquear' : 'ativar'} este usuário?`)) {
                fetch(`/api/users/${userId}/${action}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                    }
                })
                .then(() => location.reload())
                .catch(error => alert('Erro ao atualizar status'));
            }
        }

        function confirmDeleteUser(userId) {
            document.getElementById('deleteUserForm').action = `/admin/users/${userId}`;
            document.getElementById('deleteUserModal').classList.remove('hidden');
        }

        function closeDeleteUser() {
            document.getElementById('deleteUserModal').classList.add('hidden');
        }

        function showExportModal() {
            alert('Funcionalidade de exportação em desenvolvimento');
        }
    </script>

</body>
</html>
