<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Grupos - Admin Panel</title>
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
                    
                    <a href="<?= base_url('admin/groups') ?>" class="bg-primary-50 text-primary-700 border-r-2 border-primary-700 group flex items-center px-3 py-2 text-sm font-medium mt-1">
                        <svg class="text-primary-500 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                        Grupos
                    </a>
                    
                    <a href="<?= base_url('admin/permissions') ?>" class="text-gray-700 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-3 py-2 text-sm font-medium mt-1 rounded-md">
                        <svg class="text-gray-400 group-hover:text-gray-500 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
                            <h2 class="text-2xl font-bold text-gray-900">Gerenciar Grupos</h2>
                            <p class="mt-1 text-sm text-gray-500">Gerencie grupos de usuários e suas permissões</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <button 
                                onclick="openCreateGroupModal()"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Novo Grupo
                            </button>
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

                <!-- Groups Grid -->
                <div class="bg-white shadow-sm border border-gray-200 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Lista de Grupos</h3>
                        <p class="mt-1 text-sm text-gray-500">Total: <?= count($groups) ?> grupo(s)</p>
                    </div>
                    
                    <?php if (empty($groups)): ?>
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum grupo</h3>
                            <p class="mt-1 text-sm text-gray-500">Comece criando um novo grupo.</p>
                            <div class="mt-6">
                                <button 
                                    onclick="openCreateGroupModal()"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Novo Grupo
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php foreach ($groups as $groupKey => $groupData): ?>
                                    <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <h4 class="text-lg font-medium text-gray-900"><?= esc($groupKey) ?></h4>
                                                    <p class="text-sm text-gray-500"><?= esc($groupData['title']) ?></p>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button 
                                                    onclick="editGroup('<?= esc($groupKey) ?>')"
                                                    class="inline-flex items-center p-2 border border-gray-300 rounded-md text-gray-400 hover:text-gray-500 hover:border-gray-400"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button 
                                                    onclick="deleteGroup('<?= esc($groupKey) ?>')"
                                                    class="inline-flex items-center p-2 border border-gray-300 rounded-md text-red-400 hover:text-red-500 hover:border-red-400"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600 mb-2">Descrição:</p>
                                            <p class="text-sm text-gray-800"><?= esc($groupData['description']) ?></p>
                                        </div>
                                        
                                        <div>
                                            <p class="text-sm text-gray-600 mb-2">Permissões:</p>
                                            <div class="flex flex-wrap gap-1">
                                                <?php if (empty($groupData['permissions'])): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        Nenhuma permissão
                                                    </span>
                                                <?php else: ?>
                                                    <?php foreach ($groupData['permissions'] as $permission): ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            <?= esc($permission) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Edit Group Modal -->
    <div id="editGroupModal" class="fixed inset-0 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="editGroupForm" onsubmit="updateGroup(event)">
                    <input type="hidden" id="editGroupOriginalName" name="originalName">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Editar Grupo
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="editGroupName" class="block text-sm font-medium text-gray-700">Nome do Grupo</label>
                                        <input 
                                            type="text" 
                                            id="editGroupName" 
                                            name="name" 
                                            required 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                            placeholder="ex: moderators"
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="editGroupTitle" class="block text-sm font-medium text-gray-700">Título</label>
                                        <input 
                                            type="text" 
                                            id="editGroupTitle" 
                                            name="title" 
                                            required 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                            placeholder="Moderadores"
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="editGroupDescription" class="block text-sm font-medium text-gray-700">Descrição</label>
                                        <textarea 
                                            id="editGroupDescription" 
                                            name="description" 
                                            rows="3"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                            placeholder="Descrição do grupo"
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
                            Atualizar Grupo
                        </button>
                        <button 
                            type="button" 
                            onclick="closeEditGroupModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Group Modal -->
    <div id="createGroupModal" class="fixed inset-0 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="createGroupForm" onsubmit="createGroup(event)">
                    <?= csrf_field() ?>
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    Criar Novo Grupo
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="groupName" class="block text-sm font-medium text-gray-700">Nome do Grupo</label>
                                        <input 
                                            type="text" 
                                            id="groupName" 
                                            name="name" 
                                            required 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                            placeholder="ex: moderators"
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="groupTitle" class="block text-sm font-medium text-gray-700">Título</label>
                                        <input 
                                            type="text" 
                                            id="groupTitle" 
                                            name="title" 
                                            required 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                            placeholder="Moderadores"
                                        >
                                    </div>
                                    
                                    <div>
                                        <label for="groupDescription" class="block text-sm font-medium text-gray-700">Descrição</label>
                                        <textarea 
                                            id="groupDescription" 
                                            name="description" 
                                            rows="3"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                            placeholder="Descrição do grupo"
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
                            Criar Grupo
                        </button>
                        <button 
                            type="button" 
                            onclick="closeCreateGroupModal()"
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
        function openCreateGroupModal() {
            document.getElementById('createGroupModal').classList.remove('hidden');
        }

        function closeCreateGroupModal() {
            document.getElementById('createGroupModal').classList.add('hidden');
            document.getElementById('createGroupForm').reset();
        }

        function closeEditGroupModal() {
            document.getElementById('editGroupModal').classList.add('hidden');
            document.getElementById('editGroupForm').reset();
        }

        async function updateGroup(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const data = {
                originalName: formData.get('originalName'),
                name: formData.get('name'),
                title: formData.get('title'),
                description: formData.get('description')
            };

            try {
                const response = await fetch(`<?= base_url('admin/groups/update') ?>/${data.originalName}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    location.reload();
                } else {
                    alert('Erro ao atualizar grupo');
                }
            } catch (error) {
                alert('Erro de conexão');
            }
        }

        async function createGroup(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            
            // Obter token CSRF
            const csrfName = '<?= csrf_token() ?>';
            const csrfHash = '<?= csrf_hash() ?>';

            try {
                const response = await fetch('<?= base_url('admin/groups/store') ?>', {
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
                    alert('Erro ao criar grupo. Verifique os dados e tente novamente.');
                }
            } catch (error) {
                console.error('Erro de conexão:', error);
                alert('Erro de conexão. Tente novamente.');
            }
        }

        async function editGroup(groupName) {
            try {
                // Buscar dados do grupo
                const response = await fetch(`<?= base_url('admin/groups/edit') ?>/${groupName}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const groupData = await response.json();
                    
                    // Preencher o modal de edição
                    document.getElementById('editGroupName').value = groupData.name;
                    document.getElementById('editGroupTitle').value = groupData.title;
                    document.getElementById('editGroupDescription').value = groupData.description;
                    document.getElementById('editGroupOriginalName').value = groupData.name;
                    
                    // Abrir modal de edição
                    document.getElementById('editGroupModal').classList.remove('hidden');
                } else {
                    alert('Erro ao carregar dados do grupo');
                }
            } catch (error) {
                alert('Erro de conexão');
            }
        }

        async function deleteGroup(groupName) {
            if (!confirm(`Tem certeza que deseja excluir o grupo "${groupName}"?\n\nEsta ação não pode ser desfeita.`)) {
                return;
            }

            try {
                const response = await fetch(`<?= base_url('admin/groups/delete') ?>/${groupName}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    location.reload();
                } else {
                    alert('Erro ao excluir grupo');
                }
            } catch (error) {
                alert('Erro de conexão');
            }
        }

        // Close modal when clicking outside
        document.getElementById('createGroupModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCreateGroupModal();
            }
        });
        
        document.getElementById('editGroupModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditGroupModal();
            }
        });
    </script>
</body>
</html>