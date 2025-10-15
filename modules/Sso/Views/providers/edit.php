<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Provider - <?= esc($provider['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans antialiased">
    
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="/sso/admin/providers" class="text-gray-600 hover:text-gray-900 transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Editar Provider</h1>
                        <p class="text-sm text-gray-500 mt-1"><?= esc($provider['name']) ?></p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Delete Button -->
                    <button 
                        onclick="confirmDelete(<?= $provider['id'] ?>)"
                        class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition border border-red-200"
                    >
                        <i class="fas fa-trash mr-2"></i>Excluir
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Alert Messages -->
        <?php if (session()->has('success')): ?>
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-800 font-medium"><?= session('success') ?></p>
            </div>
        </div>
        <?php endif ?>

        <?php if (session()->has('error')): ?>
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <p class="text-red-800 font-medium"><?= session('error') ?></p>
            </div>
        </div>
        <?php endif ?>

        <?php if (isset($validation)): ?>
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-500 mr-3 mt-1"></i>
                <div>
                    <p class="text-red-800 font-medium mb-2">Corrija os seguintes erros:</p>
                    <ul class="list-disc list-inside text-red-700 space-y-1">
                        <?php foreach ($validation->getErrors() as $error): ?>
                            <li class="text-sm"><?= esc($error) ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif ?>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            
            <form action="/sso/admin/providers/<?= $provider['id'] ?>" method="POST" id="providerForm">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">

                <!-- Basic Information Section -->
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-info-circle text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Informações Básicas</h2>
                            <p class="text-sm text-gray-500">Dados principais do provider</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Name (readonly) -->
                        <div class="col-span-1">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nome Único
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                id="name" 
                                value="<?= esc($provider['name']) ?>"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed"
                                readonly
                            >
                            <p class="mt-1 text-xs text-gray-500">Não é possível alterar o nome do provider</p>
                        </div>

                        <!-- Type (readonly) -->
                        <div class="col-span-1">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Provider
                            </label>
                            <div class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 flex items-center">
                                <?php 
                                $typeIcons = [
                                    'local' => '🔐 Local',
                                    'ldap' => '🏢 LDAP',
                                    'oauth' => '🔗 OAuth',
                                    'saml' => '🛡️ SAML'
                                ];
                                echo $typeIcons[$provider['type']] ?? $provider['type'];
                                ?>
                            </div>
                            <input type="hidden" name="type" value="<?= esc($provider['type']) ?>">
                        </div>

                        <!-- Title -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Título de Exibição <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="title" 
                                id="title" 
                                value="<?= old('title', $provider['title']) ?>"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                required
                            >
                        </div>

                        <!-- Description -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Descrição
                            </label>
                            <textarea 
                                name="description" 
                                id="description" 
                                rows="3"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                            ><?= old('description', $provider['description']) ?></textarea>
                        </div>

                    </div>
                </div>

                <!-- Configuration Section -->
                <div class="p-6 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-cog text-purple-600 text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Configuração</h2>
                            <p class="text-sm text-gray-500">Parâmetros do provider <?= $provider['type'] ?></p>
                        </div>
                    </div>

                    <div id="configSection">
                        <?php $config = $provider['config']; ?>
                        
                        <?php if ($provider['type'] === 'local'): ?>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200">
                                    <div class="flex-1">
                                        <label class="text-sm font-medium text-gray-900">Permitir Registro</label>
                                        <p class="text-xs text-gray-500 mt-1">Usuários podem criar conta</p>
                                    </div>
                                    <input type="checkbox" name="config[allow_registration]" value="1" 
                                        <?= ($config['allow_registration'] ?? false) ? 'checked' : '' ?>
                                        class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                </div>
                                <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200">
                                    <div class="flex-1">
                                        <label class="text-sm font-medium text-gray-900">Requer Email Verificado</label>
                                        <p class="text-xs text-gray-500 mt-1">Validar email antes do login</p>
                                    </div>
                                    <input type="checkbox" name="config[require_email_verification]" value="1" 
                                        <?= ($config['require_email_verification'] ?? false) ? 'checked' : '' ?>
                                        class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                </div>
                            </div>
                        <?php endif ?>

                        <?php if ($provider['type'] === 'ldap'): ?>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Servidor LDAP</label>
                                    <input type="text" name="config[host]" value="<?= esc($config['host'] ?? '') ?>" 
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Porta</label>
                                        <input type="number" name="config[port]" value="<?= esc($config['port'] ?? '389') ?>" 
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Base DN</label>
                                        <input type="text" name="config[base_dn]" value="<?= esc($config['base_dn'] ?? '') ?>" 
                                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    </div>
                                </div>
                                <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200">
                                    <input type="checkbox" name="config[use_ssl]" value="1" 
                                        <?= ($config['use_ssl'] ?? false) ? 'checked' : '' ?>
                                        class="h-5 w-5 text-blue-600 rounded mr-3">
                                    <label class="text-sm font-medium text-gray-900">Usar SSL/TLS</label>
                                </div>
                            </div>
                        <?php endif ?>

                        <?php if ($provider['type'] === 'oauth'): ?>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Provider OAuth</label>
                                    <select name="config[provider]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option value="google" <?= ($config['provider'] ?? '') === 'google' ? 'selected' : '' ?>>Google</option>
                                        <option value="microsoft" <?= ($config['provider'] ?? '') === 'microsoft' ? 'selected' : '' ?>>Microsoft</option>
                                        <option value="github" <?= ($config['provider'] ?? '') === 'github' ? 'selected' : '' ?>>GitHub</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Client ID</label>
                                    <input type="text" name="config[client_id]" value="<?= esc($config['client_id'] ?? '') ?>" 
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Client Secret</label>
                                    <input type="password" name="config[client_secret]" value="<?= esc($config['client_secret'] ?? '') ?>" 
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        <?php endif ?>

                        <?php if ($provider['type'] === 'saml'): ?>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Entity ID</label>
                                    <input type="text" name="config[entity_id]" value="<?= esc($config['entity_id'] ?? '') ?>" 
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">SSO URL</label>
                                    <input type="url" name="config[sso_url]" value="<?= esc($config['sso_url'] ?? '') ?>" 
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                </div>

                <!-- Settings Section -->
                <div class="p-6 border-t border-gray-200">
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-sliders-h text-green-600 text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Configurações Gerais</h2>
                            <p class="text-sm text-gray-500">Ativação e priorização</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <!-- Is Enabled -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="is_enabled" class="text-sm font-medium text-gray-900 cursor-pointer">Ativo</label>
                                <p class="text-xs text-gray-500 mt-1">Provider disponível</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_enabled" id="is_enabled" value="1" 
                                    <?= $provider['is_enabled'] ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <!-- Is Default -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="is_default" class="text-sm font-medium text-gray-900 cursor-pointer">Padrão</label>
                                <p class="text-xs text-gray-500 mt-1">Provider principal</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_default" id="is_default" value="1" 
                                    <?= $provider['is_default'] ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                            </label>
                        </div>

                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Prioridade</label>
                            <input type="number" name="priority" id="priority" value="<?= $provider['priority'] ?>" 
                                min="1" max="100"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <p class="mt-1 text-xs text-gray-500">1 = primeiro</p>
                        </div>

                    </div>
                </div>

                <!-- Actions -->
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-between border-t border-gray-200">
                    <a href="/sso/admin/providers" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" class="px-8 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 shadow-md hover:shadow-lg transition transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i>Salvar Alterações
                    </button>
                </div>

            </form>

        </div>

    </main>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Confirmar Exclusão</h3>
                <p class="text-gray-600 text-center mb-6">
                    Tem certeza que deseja excluir este provider? Esta ação não pode ser desfeita.
                </p>
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

    <script>
        function confirmDelete(id) {
            document.getElementById('deleteForm').action = `/sso/admin/providers/${id}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>

</body>
</html>
