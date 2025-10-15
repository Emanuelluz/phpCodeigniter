<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SSO - <?= esc(config('App')->siteName ?? 'Sistema') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-md">
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                <?= esc(config('App')->siteName ?? 'Sistema') ?>
            </h1>
            <p class="text-gray-600">Faça login para continuar</p>
        </div>

        <!-- Card de Login -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            
            <!-- Mensagens -->
            <?php if (session()->has('error')): ?>
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <?= esc(session('error')) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->has('success')): ?>
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <?= esc(session('success')) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($show_provider_selection) && $show_provider_selection): ?>
                <!-- Seleção de Provider -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Escolha o método de autenticação
                    </label>
                    <div class="space-y-3">
                        <?php foreach ($providers as $provider): ?>
                            <button 
                                type="button"
                                onclick="selectProvider(<?= $provider['id'] ?>, '<?= esc($provider['type']) ?>')"
                                class="w-full p-4 text-left border-2 border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition-all duration-200 group"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <?php if ($provider['type'] === 'local'): ?>
                                            <svg class="w-6 h-6 text-gray-600 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                            </svg>
                                        <?php elseif ($provider['type'] === 'ldap'): ?>
                                            <svg class="w-6 h-6 text-gray-600 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                                            </svg>
                                        <?php else: ?>
                                            <svg class="w-6 h-6 text-gray-600 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                            </svg>
                                        <?php endif; ?>
                                        <div>
                                            <div class="font-semibold text-gray-800 group-hover:text-indigo-600">
                                                <?= esc($provider['title']) ?>
                                            </div>
                                            <?php if (!empty($provider['description'])): ?>
                                                <div class="text-sm text-gray-500">
                                                    <?= esc($provider['description']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Form de Login Direto -->
                <form action="<?= base_url('sso/authenticate') ?>" method="post" class="space-y-6">
                    <?= csrf_field() ?>
                    <input type="hidden" name="provider_id" value="<?= $provider['id'] ?? '' ?>">

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Usuário
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            value="<?= old('username') ?>"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="Digite seu usuário"
                        >
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Senha
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="Digite sua senha"
                        >
                    </div>

                    <!-- Remember Me -->
                    <?php if ($config->allowRememberMe): ?>
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="remember" 
                                name="remember" 
                                value="1"
                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                            >
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Lembrar-me por <?= $config->rememberMeDuration / 86400 ?> dias
                            </label>
                        </div>
                    <?php endif; ?>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 shadow-lg hover:shadow-xl"
                    >
                        Entrar
                    </button>
                </form>
            <?php endif; ?>

            <!-- Footer Links -->
            <div class="mt-6 text-center text-sm text-gray-600">
                <a href="<?= base_url() ?>" class="hover:text-indigo-600 transition-colors">
                    ← Voltar para o início
                </a>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="text-center mt-6 text-sm text-gray-600">
            <p>© <?= date('Y') ?> <?= esc(config('App')->siteName ?? 'Sistema') ?>. Todos os direitos reservados.</p>
        </div>
    </div>

    <script>
        function selectProvider(providerId, providerType) {
            // Criar form dinamicamente
            const form = document.createElement('form');
            form.method = 'post';
            form.action = '<?= base_url('sso/authenticate') ?>';
            
            // Se for OAuth ou SAML, redirecionar para o provider
            if (providerType === 'oauth' || providerType === 'saml') {
                window.location.href = `<?= base_url('sso/provider/') ?>${providerType}/${providerId}`;
                return;
            }
            
            // Para local e LDAP, mostrar form de login
            window.location.href = `<?= base_url('sso/login') ?>?provider=${providerId}`;
        }
    </script>
</body>
</html>
