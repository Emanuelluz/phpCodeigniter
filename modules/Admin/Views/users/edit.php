<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário - Admin Panel</title>
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
                        Admin Panel
                    </h5>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="/admin">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                        <a class="nav-link active" href="/admin/users">
                            <i class="bi bi-people me-2"></i>
                            Usuários
                        </a>
                        <a class="nav-link" href="/admin/groups">
                            <i class="bi bi-collection me-2"></i>
                            Grupos
                        </a>
                        <a class="nav-link" href="/admin/permissions">
                            <i class="bi bi-key me-2"></i>
                            Permissões
                        </a>
                        <hr class="text-white-50">
                        <a class="nav-link" href="/">
                            <i class="bi bi-house me-2"></i>
                            Ir para o Site
                        </a>
                        <a class="nav-link" href="/logout">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Sair
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Header -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
                    <div class="container-fluid">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item">
                                    <a href="/admin" class="text-decoration-none">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="/admin/users" class="text-decoration-none">Usuários</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    Editar: <?= esc($user->username) ?>
                                </li>
                            </ol>
                        </nav>
                        <div class="navbar-nav ms-auto">
                            <span class="nav-link">
                                <i class="bi bi-person-circle me-1"></i>
                                <?= esc(auth()->user()->username ?? 'Admin') ?>
                            </span>
                        </div>
                    </div>
                </nav>

                <!-- Content -->
                <div class="container-fluid px-4">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <!-- Form Card -->
                            <div class="card">
                                <div class="card-header bg-white">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-pencil me-2"></i>
                                        Editar Usuário: <?= esc($user->username) ?>
                                    </h5>
                                </div>
                                
                                <?= form_open('/admin/users/update/' . $user->id, ['class' => 'needs-validation', 'novalidate' => true]) ?>
                                <div class="card-body">
                                    <!-- Alerts -->
                                    <?php if (session('error')): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <i class="bi bi-exclamation-circle me-2"></i>
                                        <?= session('error') ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Validation Errors -->
                                    <?php if (session('errors')): ?>
                                    <div class="alert alert-danger">
                                        <h6><i class="bi bi-exclamation-triangle me-2"></i>Erro de Validação:</h6>
                                        <ul class="mb-0">
                                            <?php foreach (session('errors') as $error): ?>
                                                <li><?= esc($error) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <?php endif; ?>

                                    <!-- User Info -->
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <small class="text-muted">ID do Usuário:</small>
                                                            <div class="fw-bold"><?= esc($user->id) ?></div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <small class="text-muted">Criado em:</small>
                                                            <div class="fw-bold">
                                                                <?= date('d/m/Y H:i', strtotime($user->created_at)) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <!-- Username -->
                                        <div class="col-md-6 mb-3">
                                            <label for="username" class="form-label">
                                                Username <span class="required">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>" 
                                                   id="username" 
                                                   name="username" 
                                                   value="<?= old('username', $user->username) ?>"
                                                   required>
                                            <div class="invalid-feedback">
                                                <?= session('errors.username') ?? 'Campo obrigatório' ?>
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">
                                                Email <span class="required">*</span>
                                            </label>
                                            <input type="email" 
                                                   class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                                                   id="email" 
                                                   name="email" 
                                                   value="<?= old('email', $user->email) ?>"
                                                   required>
                                            <div class="invalid-feedback">
                                                <?= session('errors.email') ?? 'Email válido é obrigatório' ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Password Section -->
                                    <div class="card border-warning mb-3">
                                        <div class="card-header bg-warning bg-opacity-10">
                                            <h6 class="mb-0">
                                                <i class="bi bi-key me-2"></i>
                                                Alterar Senha
                                            </h6>
                                            <small class="text-muted">Deixe em branco para manter a senha atual</small>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- New Password -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="password" class="form-label">Nova Senha</label>
                                                    <div class="input-group">
                                                        <input type="password" 
                                                               class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" 
                                                               id="password" 
                                                               name="password" 
                                                               minlength="8">
                                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <div class="invalid-feedback">
                                                            <?= session('errors.password') ?? 'Mínimo 8 caracteres' ?>
                                                        </div>
                                                    </div>
                                                    <div class="form-text">Mínimo 8 caracteres (opcional)</div>
                                                </div>

                                                <!-- Password Confirmation -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="password_confirm" class="form-label">Confirmar Nova Senha</label>
                                                    <input type="password" 
                                                           class="form-control <?= session('errors.password_confirm') ? 'is-invalid' : '' ?>" 
                                                           id="password_confirm" 
                                                           name="password_confirm" 
                                                           minlength="8">
                                                    <div class="invalid-feedback">
                                                        <?= session('errors.password_confirm') ?? 'Confirme a nova senha' ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Groups -->
                                    <div class="mb-3">
                                        <label class="form-label">Grupos</label>
                                        <div class="card border-light">
                                            <div class="card-body">
                                                <?php if (empty($groups)): ?>
                                                    <div class="text-muted">
                                                        <i class="bi bi-info-circle me-2"></i>
                                                        Nenhum grupo disponível
                                                    </div>
                                                <?php else: ?>
                                                    <div class="row">
                                                        <?php 
                                                        $userGroups = $user->getGroups();
                                                        foreach ($groups as $group): ?>
                                                        <div class="col-md-4 mb-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" 
                                                                       type="checkbox" 
                                                                       name="groups[]" 
                                                                       value="<?= esc($group) ?>" 
                                                                       id="group_<?= esc($group) ?>"
                                                                       <?= in_array($group, old('groups', $userGroups)) ? 'checked' : '' ?>>
                                                                <label class="form-check-label" for="group_<?= esc($group) ?>">
                                                                    <?= esc($group) ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="active" 
                                                   name="active" 
                                                   value="1" 
                                                   <?= old('active', $user->active) ? 'checked' : '' ?>
                                                   <?= $user->id === auth()->id() ? 'disabled' : '' ?>>
                                            <label class="form-check-label" for="active">
                                                <strong>Usuário Ativo</strong>
                                                <div class="form-text">
                                                    <?= $user->id === auth()->id() 
                                                        ? 'Você não pode desativar sua própria conta' 
                                                        : 'Usuário pode fazer login no sistema' ?>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Self-edit warning -->
                                    <?php if ($user->id === auth()->id()): ?>
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>Editando sua própria conta:</strong> 
                                        Algumas opções podem estar limitadas por segurança.
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="card-footer bg-light">
                                    <div class="d-flex justify-content-between">
                                        <a href="/admin/users" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left me-1"></i>
                                            Voltar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-lg me-1"></i>
                                            Salvar Alterações
                                        </button>
                                    </div>
                                </div>
                                <?= form_close() ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Bootstrap validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });

        // Password confirmation validation
        document.getElementById('password_confirm').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirm = this.value;
            
            if (password && password !== confirm) {
                this.setCustomValidity('As senhas não coincidem');
            } else {
                this.setCustomValidity('');
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            const confirm = document.getElementById('password_confirm');
            if (confirm.value || this.value) {
                confirm.dispatchEvent(new Event('input'));
            }
        });
    </script>
</body>
</html>