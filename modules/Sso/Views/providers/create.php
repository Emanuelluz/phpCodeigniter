<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Provider SSO</title>
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
                    <h1 class="text-2xl font-bold text-gray-900">Novo Provider de Autenticação</h1>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-500">SSO Admin</span>
                    <div class="h-8 w-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                        A
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Alert Messages -->
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
            
            <form action="/sso/admin/providers" method="POST" id="providerForm" class="divide-y divide-gray-200">
                <?= csrf_field() ?>

                <!-- Basic Information Section -->
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-info-circle text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Informações Básicas</h2>
                            <p class="text-sm text-gray-500">Configure os dados principais do provider</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Name -->
                        <div class="col-span-1">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nome Único <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                id="name" 
                                value="<?= old('name') ?>"
                                placeholder="ex: google, ldap_empresa, saml_idp"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                required
                            >
                            <p class="mt-1 text-xs text-gray-500">Identificador único (sem espaços, apenas letras, números e _)</p>
                        </div>

                        <!-- Type -->
                        <div class="col-span-1">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Provider <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="type" 
                                id="type" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                required
                            >
                                <option value="">Selecione o tipo</option>
                                <option value="local" <?= old('type') === 'local' ? 'selected' : '' ?>>
                                    🔐 Local (Banco de Dados)
                                </option>
                                <option value="ldap" <?= old('type') === 'ldap' ? 'selected' : '' ?>>
                                    🏢 LDAP / Active Directory
                                </option>
                                <option value="oauth" <?= old('type') === 'oauth' ? 'selected' : '' ?>>
                                    🔗 OAuth 2.0 (Google, GitHub, etc)
                                </option>
                                <option value="saml" <?= old('type') === 'saml' ? 'selected' : '' ?>>
                                    🛡️ SAML 2.0
                                </option>
                            </select>
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
                                value="<?= old('title') ?>"
                                placeholder="ex: Login com Google, LDAP Corporativo"
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
                                placeholder="Descreva para que serve este provider..."
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                            ><?= old('description') ?></textarea>
                        </div>

                    </div>
                </div>

                <!-- Configuration Section -->
                <div class="p-6 bg-gray-50">
                    <div class="flex items-center mb-6">
                        <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-cog text-purple-600 text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Configuração</h2>
                            <p class="text-sm text-gray-500">Parâmetros específicos do tipo de provider</p>
                        </div>
                    </div>

                    <div id="configSection">
                        <div class="bg-white rounded-lg border-2 border-dashed border-gray-300 p-8 text-center">
                            <i class="fas fa-hand-pointer text-gray-400 text-3xl mb-3"></i>
                            <p class="text-gray-500">Selecione um tipo de provider acima para exibir as configurações</p>
                        </div>
                    </div>
                </div>

                <!-- Settings Section -->
                <div class="p-6">
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
                                <label for="is_enabled" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Ativo
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Provider disponível para uso</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_enabled" id="is_enabled" value="1" <?= old('is_enabled', '1') ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <!-- Is Default -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="is_default" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Padrão
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Provider principal no login</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_default" id="is_default" value="1" <?= old('is_default') ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                            </label>
                        </div>

                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                                Prioridade
                            </label>
                            <input 
                                type="number" 
                                name="priority" 
                                id="priority" 
                                value="<?= old('priority', '10') ?>"
                                min="1"
                                max="100"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            >
                            <p class="mt-1 text-xs text-gray-500">Ordem de exibição (1 = primeiro)</p>
                        </div>

                    </div>
                </div>

                <!-- Actions -->
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-between">
                    <a 
                        href="/sso/admin/providers" 
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition"
                    >
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button 
                        type="submit" 
                        class="px-8 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 shadow-md hover:shadow-lg transition transform hover:scale-105"
                    >
                        <i class="fas fa-save mr-2"></i>Salvar Provider
                    </button>
                </div>

            </form>

        </div>

        <!-- Help Card -->
        <div class="mt-6 bg-blue-50 rounded-xl border border-blue-200 p-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-0.5"></i>
                <div>
                    <h3 class="font-semibold text-blue-900 mb-2">Dicas de Configuração</h3>
                    <ul class="space-y-2 text-sm text-blue-800">
                        <li><strong>Local:</strong> Usa o banco de dados local com Shield</li>
                        <li><strong>LDAP:</strong> Conecta a Active Directory ou servidores LDAP</li>
                        <li><strong>OAuth:</strong> Integra com Google, Microsoft, GitHub, etc</li>
                        <li><strong>SAML:</strong> Para Single Sign-On corporativo</li>
                    </ul>
                </div>
            </div>
        </div>

    </main>

    <!-- Dynamic Config Forms Script -->
    <script>
        const typeSelect = document.getElementById('type');
        const configSection = document.getElementById('configSection');

        // Config templates for each provider type
        const configTemplates = {
            local: `
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-900">Permitir Registro</label>
                            <p class="text-xs text-gray-500 mt-1">Usuários podem criar conta</p>
                        </div>
                        <input type="checkbox" name="config[allow_registration]" value="1" checked 
                            class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200">
                        <div class="flex-1">
                            <label class="text-sm font-medium text-gray-900">Requer Email Verificado</label>
                            <p class="text-xs text-gray-500 mt-1">Validar email antes do login</p>
                        </div>
                        <input type="checkbox" name="config[require_email_verification]" value="1" 
                            class="h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                    </div>
                </div>
            `,
            ldap: `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Servidor LDAP <span class="text-red-500">*</span></label>
                        <input type="text" name="config[host]" placeholder="ldap://servidor.empresa.com" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Porta</label>
                            <input type="number" name="config[port]" value="389" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Base DN <span class="text-red-500">*</span></label>
                            <input type="text" name="config[base_dn]" placeholder="dc=empresa,dc=com" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filtro de Busca</label>
                        <input type="text" name="config[filter]" value="(&(objectClass=user)(sAMAccountName={username}))" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center p-4 bg-white rounded-lg border border-gray-200">
                        <input type="checkbox" name="config[use_ssl]" value="1" class="h-5 w-5 text-blue-600 rounded mr-3">
                        <label class="text-sm font-medium text-gray-900">Usar SSL/TLS</label>
                    </div>
                </div>
            `,
            oauth: `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Provider OAuth <span class="text-red-500">*</span></label>
                        <select name="config[provider]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                            <option value="google">Google</option>
                            <option value="microsoft">Microsoft</option>
                            <option value="github">GitHub</option>
                            <option value="facebook">Facebook</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Client ID <span class="text-red-500">*</span></label>
                        <input type="text" name="config[client_id]" placeholder="Seu Client ID" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Client Secret <span class="text-red-500">*</span></label>
                        <input type="password" name="config[client_secret]" placeholder="Seu Client Secret" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Redirect URI</label>
                        <input type="url" name="config[redirect_uri]" value="<?= base_url('sso/callback') ?>" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-gray-50" readonly>
                    </div>
                </div>
            `,
            saml: `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Entity ID <span class="text-red-500">*</span></label>
                        <input type="text" name="config[entity_id]" placeholder="https://idp.empresa.com" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SSO URL <span class="text-red-500">*</span></label>
                        <input type="url" name="config[sso_url]" placeholder="https://idp.empresa.com/sso" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Certificado X.509 <span class="text-red-500">*</span></label>
                        <textarea name="config[certificate]" rows="4" placeholder="Certificado do IdP (formato PEM)" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-mono text-xs" required></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attribute Mapping (Email)</label>
                        <input type="text" name="config[attr_email]" value="email" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            `
        };

        // Update config section when type changes
        typeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            if (selectedType && configTemplates[selectedType]) {
                configSection.innerHTML = configTemplates[selectedType];
            } else {
                configSection.innerHTML = `
                    <div class="bg-white rounded-lg border-2 border-dashed border-gray-300 p-8 text-center">
                        <i class="fas fa-hand-pointer text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">Selecione um tipo de provider acima</p>
                    </div>
                `;
            }
        });

        // Trigger on page load if type is pre-selected
        if (typeSelect.value) {
            typeSelect.dispatchEvent(new Event('change'));
        }
    </script>

</body>
</html>
