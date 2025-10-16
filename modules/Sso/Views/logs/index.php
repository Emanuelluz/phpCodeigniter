<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Autenticação SSO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilização da paginação do CodeIgniter */
        .pagination {
            display: flex;
            gap: 0.5rem;
        }
        .pagination a, .pagination strong {
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
            text-decoration: none;
            color: #374151;
        }
        .pagination a:hover {
            background-color: #f3f4f6;
        }
        .pagination strong {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
    
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Logs de Autenticação</h1>
                    <p class="text-sm text-gray-500 mt-1">Histórico completo de tentativas de login</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="/sso/admin" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                    <button onclick="confirmCleanup()" class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition border border-red-200">
                        <i class="fas fa-broom mr-2"></i>Limpar Antigos
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Stats Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase">Total</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1"><?= number_format($stats['total'] ?? 0) ?></p>
                    </div>
                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-list text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase">Sucesso</p>
                        <p class="text-2xl font-bold text-green-600 mt-1"><?= number_format($stats['success'] ?? 0) ?></p>
                    </div>
                    <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase">Falhas</p>
                        <p class="text-2xl font-bold text-red-600 mt-1"><?= number_format($stats['failed'] ?? 0) ?></p>
                    </div>
                    <div class="h-10 w-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase">Hoje</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1"><?= number_format($stats['today'] ?? 0) ?></p>
                    </div>
                    <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-day text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="/sso/admin/logs" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                
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

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="success" <?= ($_GET['status'] ?? '') === 'success' ? 'selected' : '' ?>>✅ Sucesso</option>
                        <option value="failed" <?= ($_GET['status'] ?? '') === 'failed' ? 'selected' : '' ?>>❌ Falha</option>
                        <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>⏳ Pendente</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">Usuário</label>
                    <input 
                        type="text" 
                        name="username" 
                        value="<?= esc($_GET['username'] ?? '') ?>"
                        placeholder="Buscar usuário..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-2">Data Inicial</label>
                    <input 
                        type="date" 
                        name="date_from" 
                        value="<?= esc($_GET['date_from'] ?? '') ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        <i class="fas fa-search mr-2"></i>Filtrar
                    </button>
                    <a href="/sso/admin/logs" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>

            </form>
        </div>

        <!-- Logs Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            
            <!-- Table Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Registros 
                        <?php if (isset($_GET['status']) || isset($_GET['provider']) || isset($_GET['username'])): ?>
                            <span class="text-sm font-normal text-gray-500">(filtrados)</span>
                        <?php endif ?>
                    </h2>
                    <div class="text-sm text-gray-500">
                        Exibindo <?= count($logs ?? []) ?> registros
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usuário
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Provider
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                IP / User Agent
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data/Hora
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                                    <p class="text-gray-500 font-medium">Nenhum log encontrado</p>
                                    <p class="text-sm text-gray-400 mt-1">Tente ajustar os filtros acima</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($logs as $log): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">
                                            <?= strtoupper(substr($log['username'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?= esc($log['username']) ?></div>
                                            <?php if ($log['user_id']): ?>
                                                <div class="text-xs text-gray-500">ID: <?= $log['user_id'] ?></div>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </td>
                                </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <?= esc($log['provider_type'] ?? 'N/A') ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <?php
                        $statusConfig = [
                                        'success' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-circle', 'label' => 'Sucesso'],
                                        'failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-times-circle', 'label' => 'Falha'],
                                        'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-clock', 'label' => 'Pendente']
                                    ];
                                    $status = $statusConfig[$log['status']] ?? $statusConfig['pending'];
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $status['bg'] ?> <?= $status['text'] ?>">
                                        <i class="fas <?= $status['icon'] ?> mr-1"></i>
                                        <?= $status['label'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900"><?= esc($log['ip_address']) ?></div>
                                    <div class="text-xs text-gray-500" title="<?= esc($log['user_agent']) ?>">
                                        <?php
                                        // Extrair navegador do User Agent
                                        $ua = $log['user_agent'];
                                        $browser = 'Desconhecido';
                                        
                                        if (stripos($ua, 'Edg/') !== false) {
                                            preg_match('/Edg\/([0-9.]+)/', $ua, $matches);
                                            $browser = '🔷 Edge ' . ($matches[1] ?? '');
                                        } elseif (stripos($ua, 'Chrome/') !== false) {
                                            preg_match('/Chrome\/([0-9.]+)/', $ua, $matches);
                                            $browser = '🌐 Chrome ' . ($matches[1] ?? '');
                                        } elseif (stripos($ua, 'Firefox/') !== false) {
                                            preg_match('/Firefox\/([0-9.]+)/', $ua, $matches);
                                            $browser = '🦊 Firefox ' . ($matches[1] ?? '');
                                        } elseif (stripos($ua, 'Safari/') !== false && stripos($ua, 'Chrome/') === false) {
                                            preg_match('/Version\/([0-9.]+)/', $ua, $matches);
                                            $browser = '🧭 Safari ' . ($matches[1] ?? '');
                                        } elseif (stripos($ua, 'OPR/') !== false || stripos($ua, 'Opera/') !== false) {
                                            preg_match('/(?:OPR|Opera)\/([0-9.]+)/', $ua, $matches);
                                            $browser = '🎭 Opera ' . ($matches[1] ?? '');
                                        }
                                        
                                        // Extrair OS
                                        $os = '';
                                        if (stripos($ua, 'Windows NT 10.0') !== false) {
                                            $os = 'Windows 10/11';
                                        } elseif (stripos($ua, 'Windows NT') !== false) {
                                            $os = 'Windows';
                                        } elseif (stripos($ua, 'Mac OS X') !== false) {
                                            $os = 'macOS';
                                        } elseif (stripos($ua, 'Linux') !== false) {
                                            $os = 'Linux';
                                        } elseif (stripos($ua, 'Android') !== false) {
                                            $os = 'Android';
                                        } elseif (stripos($ua, 'iOS') !== false || stripos($ua, 'iPhone') !== false) {
                                            $os = 'iOS';
                                        }
                                        
                                        echo esc($browser . ($os ? ' • ' . $os : ''));
                                        ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= date('d/m/Y', strtotime($log['created_at'])) ?></div>
                                    <div class="text-xs text-gray-500"><?= date('H:i:s', strtotime($log['created_at'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="/sso/admin/logs/<?= $log['id'] ?>" class="text-blue-600 hover:text-blue-800 transition" title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button onclick="confirmDelete(<?= $log['id'] ?>)" class="text-red-600 hover:text-red-800 transition" title="Excluir">
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

            <!-- Pagination -->
            <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Mostrando página <span class="font-medium"><?= $pager->getCurrentPage() ?></span> de 
                        <span class="font-medium"><?= $pager->getPageCount() ?></span>
                    </div>
                    <div class="flex space-x-2">
                        <?= $pager->links() ?>
                    </div>
                </div>
            </div>
            <?php endif ?>

        </div>

    </main>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mx-auto mb-4">
                    <i class="fas fa-trash text-red-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Excluir Log</h3>
                <p class="text-gray-600 text-center mb-6">Tem certeza que deseja excluir este registro?</p>
                <div class="flex space-x-3">
                    <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition">
                        Cancelar
                    </button>
                    <form id="deleteForm" method="POST" class="flex-1">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Excluir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cleanup Confirmation Modal -->
    <div id="cleanupModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 bg-orange-100 rounded-full mx-auto mb-4">
                    <i class="fas fa-broom text-orange-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Limpar Logs Antigos</h3>
                <p class="text-gray-600 text-center mb-6">Excluir logs com mais de 90 dias?</p>
                <div class="flex space-x-3">
                    <button onclick="closeCleanupModal()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition">
                        Cancelar
                    </button>
                    <form action="/sso/admin/logs/cleanup" method="POST" class="flex-1">
                        <?= csrf_field() ?>
                        <button type="submit" class="w-full px-4 py-2.5 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                            Limpar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            document.getElementById('deleteForm').action = `/sso/admin/logs/${id}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        function confirmCleanup() {
            document.getElementById('cleanupModal').classList.remove('hidden');
        }

        function closeCleanupModal() {
            document.getElementById('cleanupModal').classList.add('hidden');
        }
    </script>

</body>
</html>
