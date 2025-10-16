<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Providers SSO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans antialiased">
    
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-purple-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-1">Providers de Autenticação</h1>
                    <p class="text-blue-100">Gerencie os métodos de autenticação disponíveis</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/sso/admin" class="px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg transition backdrop-blur-sm">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                    <a href="/sso/admin/logs" class="px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg transition backdrop-blur-sm">
                        <i class="fas fa-list mr-2"></i>Logs
                    </a>
                </div>
            </div>
        </div>
    </header>

<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Botão Novo Provider -->
    <div class="mb-8">
        <a href="<?= base_url('sso/admin/providers/create') ?>" 
           class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg transition-colors duration-200 inline-flex items-center shadow-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Novo Provider
        </a>
    </div>

    <!-- Mensagens -->
    <?php if (session()->has('success')): ?>
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg shadow">
            <?= esc(session('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg shadow">
            <?= esc(session('error')) ?>
        </div>
    <?php endif; ?>

    <!-- Tabela de Providers -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Provider
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tipo
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Padrão
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Prioridade
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($providers)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <p class="text-lg font-medium">Nenhum provider cadastrado</p>
                            <p class="text-sm mt-1">Comece criando um novo provider de autenticação</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($providers as $provider): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-indigo-100">
                                        <?php
                                        $icons = [
                                            'local' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>',
                                            'ldap' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>',
                                            'oauth' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>',
                                            'saml' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'
                                        ];
                                        ?>
                                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <?= $icons[$provider['type']] ?? $icons['local'] ?>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= esc($provider['title']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?= esc($provider['name']) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php
                                    $colors = [
                                        'local' => 'bg-blue-100 text-blue-800',
                                        'ldap' => 'bg-green-100 text-green-800',
                                        'oauth' => 'bg-purple-100 text-purple-800',
                                        'saml' => 'bg-orange-100 text-orange-800'
                                    ];
                                    echo $colors[$provider['type']] ?? 'bg-gray-100 text-gray-800';
                                    ?>">
                                    <?= strtoupper(esc($provider['type'])) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button 
                                    onclick="toggleProvider(<?= $provider['id'] ?>)"
                                    class="relative inline-flex items-center h-6 rounded-full w-11 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                                        <?= $provider['is_enabled'] ? 'bg-indigo-600' : 'bg-gray-200' ?>"
                                    id="toggle-<?= $provider['id'] ?>"
                                >
                                    <span class="sr-only">Toggle provider</span>
                                    <span class="inline-block w-4 h-4 transform bg-white rounded-full transition-transform
                                        <?= $provider['is_enabled'] ? 'translate-x-6' : 'translate-x-1' ?>"
                                        id="toggle-span-<?= $provider['id'] ?>">
                                    </span>
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($provider['is_default']): ?>
                                    <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">
                                        Padrão
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= $provider['priority'] ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?= base_url('sso/admin/providers/edit/' . $provider['id']) ?>" 
                                   class="text-indigo-600 hover:text-indigo-900 mr-4">
                                    Editar
                                </a>
                                <button 
                                    onclick="deleteProvider(<?= $provider['id'] ?>, '<?= esc($provider['title']) ?>')"
                                    class="text-red-600 hover:text-red-900">
                                    Excluir
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleProvider(id) {
    fetch(`<?= base_url('sso/admin/providers/toggle/') ?>${id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const toggle = document.getElementById(`toggle-${id}`);
            const span = document.getElementById(`toggle-span-${id}`);
            
            if (data.is_enabled) {
                toggle.classList.remove('bg-gray-200');
                toggle.classList.add('bg-indigo-600');
                span.classList.remove('translate-x-1');
                span.classList.add('translate-x-6');
            } else {
                toggle.classList.remove('bg-indigo-600');
                toggle.classList.add('bg-gray-200');
                span.classList.remove('translate-x-6');
                span.classList.add('translate-x-1');
            }
        } else {
            alert(data.error || 'Erro ao alterar status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao alterar status do provider');
    });
}

function deleteProvider(id, title) {
    if (!confirm(`Tem certeza que deseja excluir o provider "${title}"?`)) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `<?= base_url('sso/admin/providers/delete/') ?>${id}`;
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '<?= csrf_token() ?>';
    csrf.value = '<?= csrf_hash() ?>';
    form.appendChild(csrf);
    
    document.body.appendChild(form);
    form.submit();
}
</script>

</body>
</html>
