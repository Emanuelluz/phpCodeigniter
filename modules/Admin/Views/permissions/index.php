<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Permissões - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg border-r border-gray-200">
            <div class="flex h-16 items-center px-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-xl font-semibold text-gray-900">Admin Panel</h1>
                    </div>
                </div>
            </div>
            
            <nav class="mt-6">
                <div class="px-3">
                    <a href="<?= base_url('admin') ?>" class="text-gray-700 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <svg class="text-gray-400 group-hover:text-gray-500 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        Dashboard
                    </a>
                    
                    <a href="<?= base_url('admin/users') ?>" class="text-gray-700 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-3 py-2 text-sm font-medium mt-1 rounded-md">
                        <svg class="text-gray-400 group-hover:text-gray-500 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        Usuários
                    </a>
                    
                    <a href="<?= base_url('admin/groups') ?>" class="text-gray-700 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-3 py-2 text-sm font-medium mt-1 rounded-md">
                        <svg class="text-gray-400 group-hover:text-gray-500 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                        Grupos
                    </a>
                    
                    <a href="<?= base_url('admin/permissions') ?>" class="bg-primary-50 text-primary-700 border-r-2 border-primary-700 group flex items-center px-3 py-2 text-sm font-medium mt-1">
                        <svg class="text-primary-500 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1721.75 8.25z" />
                        </svg>
                        Permissões
                    </a>
                    
                    <div class="border-t border-gray-200 mt-6 pt-6">
                        <a href="<?= base_url('/') ?>" class="text-gray-700 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <svg class="text-gray-400 group-hover:text-gray-500 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                            Ir para o Site
                        </a>
                        
                        <a href="<?= base_url('logout') ?>" class="text-gray-700 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-3 py-2 text-sm font-medium rounded-md mt-1">
                            <svg class="text-gray-400 group-hover:text-gray-500 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                            </svg>
                            Sair
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 pl-64">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Gerenciar Permissões</h2>
                            <p class="mt-1 text-sm text-gray-500">Gerencie permissões do sistema e matriz de acesso</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <?= esc(auth()->user()->username ?? 'Admin') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="p-6">
                <!-- Flash Messages -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="mb-6 rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    <?= esc(session()->getFlashdata('success')) ?>
                                </p>
                                <?php if (session()->getFlashdata('permission_config')): ?>
                                    <div class="mt-3 p-3 bg-white border border-green-200 rounded-md">
                                        <details>
                                            <summary class="font-medium text-green-700 cursor-pointer">Configuração para adicionar ao Config/AuthGroups.php:</summary>
                                            <pre class="mt-2 text-xs bg-gray-50 p-2 rounded text-gray-800 overflow-x-auto"><?php
                                            $config = session()->getFlashdata('permission_config');
                                            echo "// Em AuthGroups.php \$permissions:\n";
                                            echo "'{$config['name']}' => '{$config['description']}',\n";
                                            ?></pre>
                                        </details>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="mb-6 rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">
                                    <?= esc(session()->getFlashdata('error')) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tabs -->
                <div class="bg-white shadow-sm border border-gray-200 rounded-lg">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                            <button 
                                onclick="showTab('permissions')" 
                                id="tab-permissions"
                                class="py-4 px-1 border-b-2 border-primary-500 font-medium text-sm text-primary-600"
                            >
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1721.75 8.25z"></path>
                                </svg>
                                Permissões
                            </button>
                            <button 
                                onclick="showTab('matrix')" 
                                id="tab-matrix"
                                class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300"
                            >
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v2m0 0V7a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                Matriz de Permissões
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content: Permissions -->
                    <div id="content-permissions" class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Permissões do Sistema</h3>
                                <p class="mt-1 text-sm text-gray-500">Gerencie permissões individuais do sistema</p>
                            </div>
                            <button 
                                onclick="openCreatePermissionModal()"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Nova Permissão
                            </button>
                        </div>

                        <!-- Permissions Grid -->
                        <?php if (empty($permissions)): ?>
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1721.75 8.25z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhuma permissão</h3>
                                <p class="mt-1 text-sm text-gray-500">Comece criando uma nova permissão do sistema.</p>
                                <div class="mt-6">
                                    <button 
                                        onclick="openCreatePermissionModal()"
                                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Criar Primeira Permissão
                                    </button>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php foreach ($permissions as $permissionName => $description): ?>
                                    <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1721.75 8.25z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <h4 class="text-lg font-medium text-gray-900"><?= esc($permissionName) ?></h4>
                                                    <p class="text-sm text-gray-500">Permissão do sistema</p>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button 
                                                    onclick="editPermission('<?= esc($permissionName) ?>')"
                                                    class="inline-flex items-center p-2 border border-gray-300 rounded-md text-gray-400 hover:text-gray-500 hover:border-gray-400"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button 
                                                    onclick="deletePermission('<?= esc($permissionName) ?>')"
                                                    class="inline-flex items-center p-2 border border-gray-300 rounded-md text-red-400 hover:text-red-500 hover:border-red-400"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <p class="text-sm text-gray-600 mb-2">Descrição:</p>
                                            <p class="text-sm text-gray-800"><?= esc($description) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab Content: Matrix -->
                    <div id="content-matrix" class="p-6 hidden">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900">Matriz de Permissões</h3>
                            <p class="mt-1 text-sm text-gray-500">Visualize as permissões por grupo em formato de matriz</p>
                        </div>

                        <?php if (empty($groups) || empty($permissions)): ?>
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v2m0 0V7a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Matriz indisponível</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    <?php if (empty($groups)): ?>
                                        Nenhum grupo encontrado. <a href="<?= base_url('admin/groups') ?>" class="text-primary-600 hover:text-primary-500">Crie grupos primeiro</a>.
                                    <?php elseif (empty($permissions)): ?>
                                        Nenhuma permissão encontrada. Crie permissões primeiro.
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Grupo
                                            </th>
                                            <?php foreach ($permissions as $permissionName => $description): ?>
                                                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[80px]">
                                                    <div class="transform -rotate-45 origin-bottom-left whitespace-nowrap">
                                                        <?= esc($permissionName) ?>
                                                    </div>
                                                </th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($groups as $groupKey => $groupData): ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    <div class="flex items-center">
                                                        <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                                            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900"><?= esc($groupKey) ?></div>
                                                            <div class="text-xs text-gray-500"><?= esc($groupData['title']) ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <?php foreach ($permissions as $permissionName => $description): ?>
                                                    <td class="px-3 py-4 whitespace-nowrap text-center">
                                                        <?php 
                                                        $hasPermission = in_array($permissionName, $groupData['permissions'] ?? []);
                                                        ?>
                                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full <?= $hasPermission ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-400' ?>">
                                                            <?php if ($hasPermission): ?>
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                            <?php else: ?>
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                            <?php endif; ?>
                                                        </span>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Create Permission Modal -->
    <div id="createPermissionModal" class="fixed inset-0 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="createPermissionForm" onsubmit="createPermission(event)">
                    <?= csrf_field() ?>
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Criar Nova Permissão
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="permissionName" class="block text-sm font-medium text-gray-700">Nome da Permissão</label>
                                        <input 
                                            type="text" 
                                            id="permissionName" 
                                            name="name" 
                                            required 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                            placeholder="ex: admin.users.edit"
                                        >
                                        <p class="mt-1 text-xs text-gray-500">Use pontos para separar módulos (ex: admin.users.edit)</p>
                                    </div>
                                    
                                    <div>
                                        <label for="permissionDescription" class="block text-sm font-medium text-gray-700">Descrição</label>
                                        <textarea 
                                            id="permissionDescription" 
                                            name="description" 
                                            rows="3"
                                            required
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                            placeholder="Descrição da permissão"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button 
                            type="submit" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Criar Permissão
                        </button>
                        <button 
                            type="button" 
                            onclick="closeCreatePermissionModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Tab functionality
        function showTab(tabName) {
            // Hide all content
            document.getElementById('content-permissions').classList.add('hidden');
            document.getElementById('content-matrix').classList.add('hidden');
            
            // Reset all tabs
            document.getElementById('tab-permissions').className = 'py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300';
            document.getElementById('tab-matrix').className = 'py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300';
            
            // Show selected content
            document.getElementById('content-' + tabName).classList.remove('hidden');
            
            // Activate selected tab
            document.getElementById('tab-' + tabName).className = 'py-4 px-1 border-b-2 border-primary-500 font-medium text-sm text-primary-600';
        }

        // Modal functions
        function openCreatePermissionModal() {
            document.getElementById('createPermissionModal').classList.remove('hidden');
        }

        function closeCreatePermissionModal() {
            document.getElementById('createPermissionModal').classList.add('hidden');
            document.getElementById('createPermissionForm').reset();
        }

        // CRUD functions
        async function createPermission(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);

            try {
                const response = await fetch('<?= base_url('admin/permissions/store') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const result = await response.text();
                
                if (response.ok) {
                    // Sucesso - recarregar página
                    location.reload();
                } else {
                    // Erro - mostrar mensagem
                    console.error('Erro na resposta:', result);
                    alert('Erro ao criar permissão. Verifique os dados e tente novamente.');
                }
            } catch (error) {
                console.error('Erro de conexão:', error);
                alert('Erro de conexão. Tente novamente.');
            }
        }
                alert('Erro de conexão');
            }
        }

        async function editPermission(permissionName) {
            // Implementar edição de permissão
            alert(`Editar permissão: ${permissionName}`);
        }

        async function deletePermission(permissionName) {
            if (!confirm(`Tem certeza que deseja excluir a permissão "${permissionName}"?\n\nEsta ação não pode ser desfeita.`)) {
                return;
            }

            try {
                const response = await fetch(`<?= base_url('admin/permissions/delete') ?>/${permissionName}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    location.reload();
                } else {
                    alert('Erro ao excluir permissão');
                }
            } catch (error) {
                alert('Erro de conexão');
            }
        }

        // Close modal when clicking outside
        document.getElementById('createPermissionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCreatePermissionModal();
            }
        });
    </script>
</body>
</html>