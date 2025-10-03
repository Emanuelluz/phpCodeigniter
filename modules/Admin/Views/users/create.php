<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Usuário - Admin Panel</title>
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
                    
                    <a href="<?= base_url('admin/users') ?>" class="bg-primary-50 text-primary-700 border-r-2 border-primary-700 group flex items-center px-3 py-2 text-sm font-medium mt-1">
                        <svg class="text-primary-500 mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
                            <h2 class="text-2xl font-bold text-gray-900">Criar Usuário</h2>
                            <p class="mt-1 text-sm text-gray-500">Adicione um novo usuário ao sistema</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="<?= base_url('admin/users') ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Voltar
                            </a>
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

                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="mb-6 rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Erros de validação:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                            <li><?= esc($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <div class="bg-white shadow-sm border border-gray-200 rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informações do Usuário</h3>
                        <p class="mt-1 text-sm text-gray-500">Preencha os dados do novo usuário</p>
                    </div>
                    
                    <form action="<?= base_url('admin/users/store') ?>" method="post" class="p-6">
                        <?= csrf_field() ?>
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Nome de Usuário -->
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700">
                                    Nome de Usuário <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="username" 
                                    name="username" 
                                    required 
                                    value="<?= old('username') ?>"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                    placeholder="ex: joao.silva"
                                >
                                <p class="mt-1 text-xs text-gray-500">Único identificador do usuário no sistema</p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    required 
                                    value="<?= old('email') ?>"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                    placeholder="ex: joao@exemplo.com"
                                >
                            </div>

                            <!-- Senha -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Senha <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    required 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                    placeholder="Mínimo 8 caracteres"
                                >
                                <p class="mt-1 text-xs text-gray-500">Mínimo de 8 caracteres</p>
                            </div>

                            <!-- Confirmar Senha -->
                            <div>
                                <label for="password_confirm" class="block text-sm font-medium text-gray-700">
                                    Confirmar Senha <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="password" 
                                    id="password_confirm" 
                                    name="password_confirm" 
                                    required 
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                    placeholder="Digite a senha novamente"
                                >
                            </div>
                        </div>

                        <!-- Grupos -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Grupos do Usuário
                            </label>
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                <?php if (isset($groups) && !empty($groups)): ?>
                                    <?php foreach ($groups as $groupKey => $groupData): ?>
                                        <div class="relative flex items-start">
                                            <div class="flex items-center h-5">
                                                <input 
                                                    id="group_<?= esc($groupKey) ?>" 
                                                    name="groups[]" 
                                                    type="checkbox" 
                                                    value="<?= esc($groupKey) ?>"
                                                    <?= in_array($groupKey, old('groups') ?? []) ? 'checked' : '' ?>
                                                    class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded"
                                                >
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="group_<?= esc($groupKey) ?>" class="font-medium text-gray-700">
                                                    <?= esc($groupData['title']) ?>
                                                </label>
                                                <p class="text-gray-500 text-xs"><?= esc($groupData['description']) ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-sm text-gray-500 col-span-full">Nenhum grupo disponível</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mt-6">
                            <div class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input 
                                        id="active" 
                                        name="active" 
                                        type="checkbox" 
                                        value="1"
                                        <?= old('active', '1') ? 'checked' : '' ?>
                                        class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded"
                                    >
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="active" class="font-medium text-gray-700">
                                        Usuário Ativo
                                    </label>
                                    <p class="text-gray-500">O usuário poderá fazer login no sistema</p>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="mt-8 flex justify-end space-x-3">
                            <a 
                                href="<?= base_url('admin/users') ?>" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            >
                                Cancelar
                            </a>
                            <button 
                                type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Criar Usuário
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Validação de confirmação de senha
        document.getElementById('password_confirm').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('As senhas não coincidem');
            } else {
                this.setCustomValidity('');
            }
        });

        // Validação em tempo real
        document.getElementById('password').addEventListener('input', function() {
            const confirmPassword = document.getElementById('password_confirm');
            if (confirmPassword.value) {
                confirmPassword.dispatchEvent(new Event('input'));
            }
        });
    </script>
</body>
</html>