<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações SSO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans antialiased">
    
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-purple-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-1">Configurações SSO</h1>
                    <p class="text-blue-100">Configurações globais do sistema de autenticação</p>
                </div>
                <a href="/sso/admin" class="px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg transition backdrop-blur-sm">
                    <i class="fas fa-home mr-2"></i>Dashboard
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Success/Error Messages -->
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

        <!-- Settings Form -->
        <form action="/sso/admin/settings" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <!-- Session Settings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-purple-50 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-clock text-blue-600 text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Sessão e Timeout</h2>
                            <p class="text-sm text-gray-500">Controle de tempo de sessão dos usuários</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="session_timeout" class="block text-sm font-medium text-gray-700 mb-2">
                                Timeout de Sessão (minutos)
                            </label>
                            <input 
                                type="number" 
                                name="session_timeout" 
                                id="session_timeout" 
                                value="<?= old('session_timeout', $settings['session_timeout'] ?? 30) ?>"
                                min="5"
                                max="1440"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <p class="mt-1 text-xs text-gray-500">Tempo de inatividade antes do logout automático</p>
                        </div>
                        <div>
                            <label for="remember_me_duration" class="block text-sm font-medium text-gray-700 mb-2">
                                "Lembrar-me" (dias)
                            </label>
                            <input 
                                type="number" 
                                name="remember_me_duration" 
                                id="remember_me_duration" 
                                value="<?= old('remember_me_duration', $settings['remember_me_duration'] ?? 30) ?>"
                                min="1"
                                max="365"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            <p class="mt-1 text-xs text-gray-500">Duração do login persistente</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security & Rate Limiting -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-orange-50 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-shield-alt text-red-600 text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Segurança e Rate Limiting</h2>
                            <p class="text-sm text-gray-500">Proteção contra ataques e tentativas de força bruta</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="max_login_attempts" class="block text-sm font-medium text-gray-700 mb-2">
                                Tentativas Máximas
                            </label>
                            <input 
                                type="number" 
                                name="max_login_attempts" 
                                id="max_login_attempts" 
                                value="<?= old('max_login_attempts', $settings['max_login_attempts'] ?? 5) ?>"
                                min="3"
                                max="20"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            >
                            <p class="mt-1 text-xs text-gray-500">Tentativas antes de bloquear</p>
                        </div>
                        <div>
                            <label for="lockout_duration" class="block text-sm font-medium text-gray-700 mb-2">
                                Tempo de Bloqueio (minutos)
                            </label>
                            <input 
                                type="number" 
                                name="lockout_duration" 
                                id="lockout_duration" 
                                value="<?= old('lockout_duration', $settings['lockout_duration'] ?? 15) ?>"
                                min="5"
                                max="1440"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            >
                            <p class="mt-1 text-xs text-gray-500">Duração do bloqueio temporário</p>
                        </div>
                        <div>
                            <label for="rate_limit_window" class="block text-sm font-medium text-gray-700 mb-2">
                                Janela de Rate Limit (seg)
                            </label>
                            <input 
                                type="number" 
                                name="rate_limit_window" 
                                id="rate_limit_window" 
                                value="<?= old('rate_limit_window', $settings['rate_limit_window'] ?? 60) ?>"
                                min="10"
                                max="600"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            >
                            <p class="mt-1 text-xs text-gray-500">Período de avaliação</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="require_2fa" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Exigir Autenticação de Dois Fatores (2FA)
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Obrigatório para todos os usuários</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="require_2fa" id="require_2fa" value="1" 
                                    <?= ($settings['require_2fa'] ?? false) ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="enable_ip_whitelist" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Habilitar Whitelist de IPs
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Restringir acesso por endereço IP</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="enable_ip_whitelist" id="enable_ip_whitelist" value="1" 
                                    <?= ($settings['enable_ip_whitelist'] ?? false) ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Password Policy -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-teal-50 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-key text-green-600 text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Política de Senhas</h2>
                            <p class="text-sm text-gray-500">Requisitos de segurança para senhas</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="min_password_length" class="block text-sm font-medium text-gray-700 mb-2">
                                Comprimento Mínimo
                            </label>
                            <input 
                                type="number" 
                                name="min_password_length" 
                                id="min_password_length" 
                                value="<?= old('min_password_length', $settings['min_password_length'] ?? 8) ?>"
                                min="6"
                                max="32"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            >
                        </div>
                        <div>
                            <label for="password_expiry_days" class="block text-sm font-medium text-gray-700 mb-2">
                                Expiração de Senha (dias)
                            </label>
                            <input 
                                type="number" 
                                name="password_expiry_days" 
                                id="password_expiry_days" 
                                value="<?= old('password_expiry_days', $settings['password_expiry_days'] ?? 90) ?>"
                                min="0"
                                max="365"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            >
                            <p class="mt-1 text-xs text-gray-500">0 = nunca expira</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="require_uppercase" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Exigir Letras Maiúsculas
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Pelo menos uma letra maiúscula</p>
                            </div>
                            <input type="checkbox" name="require_uppercase" id="require_uppercase" value="1" 
                                <?= ($settings['require_uppercase'] ?? true) ? 'checked' : '' ?>
                                class="h-5 w-5 text-green-600 rounded border-gray-300 focus:ring-green-500">
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="require_numbers" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Exigir Números
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Pelo menos um dígito</p>
                            </div>
                            <input type="checkbox" name="require_numbers" id="require_numbers" value="1" 
                                <?= ($settings['require_numbers'] ?? true) ? 'checked' : '' ?>
                                class="h-5 w-5 text-green-600 rounded border-gray-300 focus:ring-green-500">
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="require_special_chars" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Exigir Caracteres Especiais
                                </label>
                                <p class="text-xs text-gray-500 mt-1">@, #, $, %, etc</p>
                            </div>
                            <input type="checkbox" name="require_special_chars" id="require_special_chars" value="1" 
                                <?= ($settings['require_special_chars'] ?? false) ? 'checked' : '' ?>
                                class="h-5 w-5 text-green-600 rounded border-gray-300 focus:ring-green-500">
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="prevent_password_reuse" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Prevenir Reutilização de Senhas
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Não permitir últimas 5 senhas</p>
                            </div>
                            <input type="checkbox" name="prevent_password_reuse" id="prevent_password_reuse" value="1" 
                                <?= ($settings['prevent_password_reuse'] ?? false) ? 'checked' : '' ?>
                                class="h-5 w-5 text-green-600 rounded border-gray-300 focus:ring-green-500">
                        </div>
                    </div>

                </div>
            </div>

            <!-- Logs & Audit -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-file-alt text-purple-600 text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Logs e Auditoria</h2>
                            <p class="text-sm text-gray-500">Retenção e gerenciamento de logs</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="log_retention_days" class="block text-sm font-medium text-gray-700 mb-2">
                                Retenção de Logs (dias)
                            </label>
                            <input 
                                type="number" 
                                name="log_retention_days" 
                                id="log_retention_days" 
                                value="<?= old('log_retention_days', $settings['log_retention_days'] ?? 90) ?>"
                                min="7"
                                max="365"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            >
                            <p class="mt-1 text-xs text-gray-500">Logs mais antigos serão deletados automaticamente</p>
                        </div>
                        <div>
                            <label for="log_level" class="block text-sm font-medium text-gray-700 mb-2">
                                Nível de Log
                            </label>
                            <select 
                                name="log_level" 
                                id="log_level"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            >
                                <option value="all" <?= ($settings['log_level'] ?? 'all') === 'all' ? 'selected' : '' ?>>Todos os eventos</option>
                                <option value="auth" <?= ($settings['log_level'] ?? '') === 'auth' ? 'selected' : '' ?>>Apenas autenticações</option>
                                <option value="failed" <?= ($settings['log_level'] ?? '') === 'failed' ? 'selected' : '' ?>>Apenas falhas</option>
                                <option value="critical" <?= ($settings['log_level'] ?? '') === 'critical' ? 'selected' : '' ?>>Apenas críticos</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="enable_auto_cleanup" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Auto-Limpeza de Logs
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Executar limpeza diariamente</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="enable_auto_cleanup" id="enable_auto_cleanup" value="1" 
                                    <?= ($settings['enable_auto_cleanup'] ?? true) ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="log_ip_addresses" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Registrar Endereços IP
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Armazenar IP em logs</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="log_ip_addresses" id="log_ip_addresses" value="1" 
                                    <?= ($settings['log_ip_addresses'] ?? true) ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Email Notifications -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-orange-50 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-envelope text-yellow-600 text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Notificações por Email</h2>
                            <p class="text-sm text-gray-500">Alertas e comunicações automáticas</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="notify_failed_logins" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Notificar Falhas de Login
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Email ao detectar tentativas suspeitas</p>
                            </div>
                            <input type="checkbox" name="notify_failed_logins" id="notify_failed_logins" value="1" 
                                <?= ($settings['notify_failed_logins'] ?? true) ? 'checked' : '' ?>
                                class="h-5 w-5 text-yellow-600 rounded border-gray-300 focus:ring-yellow-500">
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="notify_new_devices" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Notificar Novos Dispositivos
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Email ao login de novo dispositivo</p>
                            </div>
                            <input type="checkbox" name="notify_new_devices" id="notify_new_devices" value="1" 
                                <?= ($settings['notify_new_devices'] ?? false) ? 'checked' : '' ?>
                                class="h-5 w-5 text-yellow-600 rounded border-gray-300 focus:ring-yellow-500">
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="send_welcome_email" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Enviar Email de Boas-vindas
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Ao criar nova conta</p>
                            </div>
                            <input type="checkbox" name="send_welcome_email" id="send_welcome_email" value="1" 
                                <?= ($settings['send_welcome_email'] ?? true) ? 'checked' : '' ?>
                                class="h-5 w-5 text-yellow-600 rounded border-gray-300 focus:ring-yellow-500">
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="password_reset_emails" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Emails de Redefinição de Senha
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Links para resetar senha</p>
                            </div>
                            <input type="checkbox" name="password_reset_emails" id="password_reset_emails" value="1" 
                                <?= ($settings['password_reset_emails'] ?? true) ? 'checked' : '' ?>
                                class="h-5 w-5 text-yellow-600 rounded border-gray-300 focus:ring-yellow-500">
                        </div>
                    </div>

                </div>
            </div>

            <!-- Advanced Settings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-cogs text-gray-600 text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Configurações Avançadas</h2>
                            <p class="text-sm text-gray-500">Opções adicionais e experimentais</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="enable_single_session" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Sessão Única
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Apenas uma sessão ativa por usuário</p>
                            </div>
                            <input type="checkbox" name="enable_single_session" id="enable_single_session" value="1" 
                                <?= ($settings['enable_single_session'] ?? false) ? 'checked' : '' ?>
                                class="h-5 w-5 text-gray-600 rounded border-gray-300 focus:ring-gray-500">
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="enable_captcha" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Habilitar CAPTCHA
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Após 3 tentativas falhadas</p>
                            </div>
                            <input type="checkbox" name="enable_captcha" id="enable_captcha" value="1" 
                                <?= ($settings['enable_captcha'] ?? false) ? 'checked' : '' ?>
                                class="h-5 w-5 text-gray-600 rounded border-gray-300 focus:ring-gray-500">
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="maintenance_mode" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Modo de Manutenção
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Bloquear todos os logins</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1" 
                                    <?= ($settings['maintenance_mode'] ?? false) ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                            </label>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex-1">
                                <label for="debug_mode" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Modo Debug
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Logs detalhados (development)</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="debug_mode" id="debug_mode" value="1" 
                                    <?= ($settings['debug_mode'] ?? false) ? 'checked' : '' ?> class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between p-6 bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="flex items-center space-x-4">
                    <button 
                        type="button" 
                        onclick="resetToDefaults()"
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition"
                    >
                        <i class="fas fa-undo mr-2"></i>Restaurar Padrões
                    </button>
                    <button 
                        type="button" 
                        onclick="exportSettings()"
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition"
                    >
                        <i class="fas fa-download mr-2"></i>Exportar
                    </button>
                </div>
                <button 
                    type="submit" 
                    class="px-8 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 shadow-md hover:shadow-lg transition transform hover:scale-105"
                >
                    <i class="fas fa-save mr-2"></i>Salvar Todas as Configurações
                </button>
            </div>

        </form>

    </main>

    <script>
        function resetToDefaults() {
            if (confirm('Tem certeza que deseja restaurar todas as configurações para os valores padrão?')) {
                window.location.href = '/sso/admin/settings/reset';
            }
        }

        function exportSettings() {
            window.location.href = '/sso/admin/settings/export';
        }

        // Show warning for critical settings
        document.getElementById('maintenance_mode')?.addEventListener('change', function(e) {
            if (e.target.checked) {
                if (!confirm('⚠️ ATENÇÃO: Modo de manutenção bloqueará TODOS os logins. Confirma?')) {
                    e.target.checked = false;
                }
            }
        });

        document.getElementById('require_2fa')?.addEventListener('change', function(e) {
            if (e.target.checked) {
                if (!confirm('Ao ativar 2FA obrigatório, todos os usuários serão forçados a configurar autenticação de dois fatores no próximo login. Continuar?')) {
                    e.target.checked = false;
                }
            }
        });
    </script>

</body>
</html>
