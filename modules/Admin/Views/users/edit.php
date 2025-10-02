<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 2px 0 4px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .required {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar px-0">
                <div class="p-3">
                    <h5 class="text-white mb-4">
                        <i class="bi bi-shield-check me-2"></i>
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