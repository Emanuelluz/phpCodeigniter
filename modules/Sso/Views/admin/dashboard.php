<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard SSO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">
    
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-purple-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-1">Dashboard SSO</h1>
                    <p class="text-blue-100">Visão geral do sistema de autenticação</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/sso/admin/providers" class="px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg transition backdrop-blur-sm">
                        <i class="fas fa-key mr-2"></i>Providers
                    </a>
                    <a href="/sso/admin/logs" class="px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg transition backdrop-blur-sm">
                        <i class="fas fa-list mr-2"></i>Logs
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- Total Logins Today -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-sign-in-alt text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Hoje</span>
                </div>
                <h3 class="text-gray-500 text-sm font-medium mb-1">Logins</h3>
                <p class="text-3xl font-bold text-gray-900"><?= number_format($stats['logins_today'] ?? 0) ?></p>
                <p class="text-xs text-gray-500 mt-2">
                    <span class="text-green-600 font-semibold">
                        <i class="fas fa-arrow-up"></i> <?= $stats['logins_growth'] ?? '+12%' ?>
                    </span>
                    vs ontem
                </p>
            </div>

            <!-- Success Rate -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-1 rounded-full">Taxa</span>
                </div>
                <h3 class="text-gray-500 text-sm font-medium mb-1">Taxa de Sucesso</h3>
                <p class="text-3xl font-bold text-gray-900"><?= number_format($stats['success_rate'] ?? 95, 1) ?>%</p>
                <div class="mt-2 bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 h-full" style="width: <?= $stats['success_rate'] ?? 95 ?>%"></div>
                </div>
            </div>

            <!-- Active Providers -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-plug text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-2 py-1 rounded-full">Ativos</span>
                </div>
                <h3 class="text-gray-500 text-sm font-medium mb-1">Providers Ativos</h3>
                <p class="text-3xl font-bold text-gray-900"><?= $stats['active_providers'] ?? 0 ?></p>
                <p class="text-xs text-gray-500 mt-2">
                    de <?= $stats['total_providers'] ?? 0 ?> configurados
                </p>
            </div>

            <!-- Failed Logins -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="h-12 w-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded-full">Falhas</span>
                </div>
                <h3 class="text-gray-500 text-sm font-medium mb-1">Falhas (24h)</h3>
                <p class="text-3xl font-bold text-gray-900"><?= number_format($stats['failed_logins'] ?? 0) ?></p>
                <p class="text-xs text-gray-500 mt-2">
                    <?= $stats['failed_percent'] ?? '5%' ?> do total
                </p>
            </div>

        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            
            <!-- Login Trend Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Tendência de Logins</h2>
                        <p class="text-sm text-gray-500">Últimos 7 dias</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="px-3 py-1 text-xs bg-blue-50 text-blue-600 rounded-lg">7D</button>
                        <button class="px-3 py-1 text-xs text-gray-600 hover:bg-gray-50 rounded-lg">30D</button>
                    </div>
                </div>
                <canvas id="loginTrendChart" height="250"></canvas>
            </div>

            <!-- Provider Usage Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Uso por Provider</h2>
                        <p class="text-sm text-gray-500">Distribuição de autenticações</p>
                    </div>
                </div>
                <canvas id="providerUsageChart" height="250"></canvas>
            </div>

        </div>

        <!-- Recent Activity & Top Providers -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Atividade Recente</h2>
                            <p class="text-sm text-gray-500">Últimas autenticações</p>
                        </div>
                        <a href="/sso/admin/logs" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            Ver todos <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php foreach ($recentLogs ?? [] as $log): ?>
                    <div class="p-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 <?= $log['status'] === 'success' ? 'bg-green-100' : 'bg-red-100' ?> rounded-full flex items-center justify-center">
                                    <i class="fas <?= $log['status'] === 'success' ? 'fa-check text-green-600' : 'fa-times text-red-600' ?>"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?= esc($log['username']) ?></p>
                                    <p class="text-xs text-gray-500">
                                        <span class="font-semibold"><?= esc($log['provider']) ?></span> • 
                                        <?= esc($log['ip_address']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $log['status'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= ucfirst($log['status']) ?>
                                </span>
                                <p class="text-xs text-gray-500 mt-1"><?= time_ago($log['created_at']) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach ?>
                </div>
            </div>

            <!-- Provider Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Providers</h2>
                            <p class="text-sm text-gray-500">Performance e status</p>
                        </div>
                        <a href="/sso/admin/providers" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            Gerenciar <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="divide-y divide-gray-200">
                    <?php foreach ($providers ?? [] as $provider): ?>
                    <div class="p-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-3">
                                <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                    <?= strtoupper(substr($provider['name'], 0, 2)) ?>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?= esc($provider['title']) ?></p>
                                    <p class="text-xs text-gray-500"><?= ucfirst($provider['type']) ?></p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php if ($provider['is_default']): ?>
                                <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full font-medium">
                                    Padrão
                                </span>
                                <?php endif ?>
                                <span class="h-3 w-3 <?= $provider['is_enabled'] ? 'bg-green-500' : 'bg-gray-300' ?> rounded-full"></span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">
                                <i class="fas fa-sign-in-alt mr-1"></i>
                                <?= number_format($provider['login_count'] ?? 0) ?> logins
                            </span>
                            <span class="text-gray-500">
                                Taxa: <span class="font-semibold text-green-600"><?= $provider['success_rate'] ?? '100' ?>%</span>
                            </span>
                        </div>
                    </div>
                    <?php endforeach ?>
                </div>
            </div>

        </div>

    </main>

    <!-- Charts Configuration -->
    <script>
        // Login Trend Chart
        const loginTrendCtx = document.getElementById('loginTrendChart').getContext('2d');
        new Chart(loginTrendCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($chartData['labels'] ?? ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb']) ?>,
                datasets: [{
                    label: 'Logins Bem-sucedidos',
                    data: <?= json_encode($chartData['success'] ?? [45, 52, 38, 65, 59, 70, 62]) ?>,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Logins Falhados',
                    data: <?= json_encode($chartData['failed'] ?? [3, 5, 2, 4, 3, 2, 3]) ?>,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Provider Usage Chart
        const providerUsageCtx = document.getElementById('providerUsageChart').getContext('2d');
        new Chart(providerUsageCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($chartData['provider_labels'] ?? ['Local', 'LDAP', 'OAuth', 'SAML']) ?>,
                datasets: [{
                    data: <?= json_encode($chartData['provider_data'] ?? [65, 20, 10, 5]) ?>,
                    backgroundColor: [
                        'rgb(59, 130, 246)',
                        'rgb(147, 51, 234)',
                        'rgb(34, 197, 94)',
                        'rgb(251, 146, 60)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    </script>

    <?php
    // Helper function for time ago
    if (!function_exists('time_ago')) {
        function time_ago($datetime) {
            $timestamp = strtotime($datetime);
            $diff = time() - $timestamp;
            
            if ($diff < 60) return $diff . 's atrás';
            if ($diff < 3600) return floor($diff / 60) . 'min atrás';
            if ($diff < 86400) return floor($diff / 3600) . 'h atrás';
            return floor($diff / 86400) . 'd atrás';
        }
    }
    ?>

</body>
</html>
