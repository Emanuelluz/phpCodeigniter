<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Permissão - Admin</title>
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
                        <a class="nav-link" href="/admin/users">
                            <i class="bi bi-people me-2"></i>
                            Usuários
                        </a>
                        <a class="nav-link" href="/admin/groups">
                            <i class="bi bi-collection me-2"></i>
                            Grupos
                        </a>
                        <a class="nav-link active" href="/admin/permissions">
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
                                    <a href="/admin/permissions" class="text-decoration-none">Permissões</a>
                                </li>
                                <li class="breadcrumb-item active">Criar Permissão</li>
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
                                        <i class="bi bi-plus-circle me-2"></i>
                                        Criar Nova Permissão
                                    </h5>
                                </div>
                                
                                <?= form_open('/admin/permissions/store', ['class' => 'needs-validation', 'novalidate' => true]) ?>
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
                                    
                                    <!-- Info Panel -->
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>Sobre permissões:</strong> As permissões definem o que usuários podem fazer no sistema. 
                                        Use nomes descritivos e únicos para facilitar o gerenciamento.
                                    </div>
                                    
                                    <div class="row">
                                        <!-- Name -->
                                        <div class="col-md-12 mb-3">
                                            <label for="name" class="form-label">
                                                Nome da Permissão <span class="required">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>" 
                                                   id="name" 
                                                   name="name" 
                                                   value="<?= old('name') ?>"
                                                   pattern="[a-zA-Z0-9_.-]+"
                                                   placeholder="ex: users.create, posts.edit, admin.access"
                                                   required>
                                            <div class="form-text">
                                                Use formato hierárquico como: módulo.ação (ex: users.create, posts.edit)
                                                <br>Apenas letras, números, pontos, underscore e hífen
                                            </div>
                                            <div class="invalid-feedback">
                                                <?= session('errors.name') ?? 'Nome é obrigatório' ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-3">
                                        <label for="description" class="form-label">
                                            Descrição <span class="required">*</span>
                                        </label>
                                        <textarea class="form-control <?= session('errors.description') ? 'is-invalid' : '' ?>" 
                                                  id="description" 
                                                  name="description" 
                                                  rows="3"
                                                  placeholder="Descreva o que esta permissão permite fazer..."
                                                  required><?= old('description') ?></textarea>
                                        <div class="form-text">
                                            Explique claramente o que esta permissão permite ao usuário fazer
                                        </div>
                                        <div class="invalid-feedback">
                                            <?= session('errors.description') ?? 'Descrição é obrigatória' ?>
                                        </div>
                                    </div>

                                    <!-- Examples Section -->
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h6 class="mb-0">
                                                <i class="bi bi-lightbulb me-2"></i>
                                                Exemplos de Permissões
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Gerenciamento de Usuários:</strong>
                                                    <ul class="list-unstyled ms-3">
                                                        <li><code>users.view</code> - Ver usuários</li>
                                                        <li><code>users.create</code> - Criar usuários</li>
                                                        <li><code>users.edit</code> - Editar usuários</li>
                                                        <li><code>users.delete</code> - Excluir usuários</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Administração:</strong>
                                                    <ul class="list-unstyled ms-3">
                                                        <li><code>admin.access</code> - Acessar painel admin</li>
                                                        <li><code>settings.manage</code> - Gerenciar configurações</li>
                                                        <li><code>reports.view</code> - Ver relatórios</li>
                                                        <li><code>logs.access</code> - Acessar logs</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer bg-light">
                                    <div class="d-flex justify-content-between">
                                        <a href="/admin/permissions" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left me-1"></i>
                                            Voltar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            Criar Permissão
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

        // Permission name suggestions
        const suggestions = [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'admin.access', 'settings.manage', 'reports.view', 'logs.access',
            'posts.create', 'posts.edit', 'posts.delete', 'comments.moderate'
        ];

        // Auto-suggest for permission names
        document.getElementById('name').addEventListener('input', function() {
            const value = this.value.toLowerCase();
            const matches = suggestions.filter(s => s.includes(value));
            
            // Remove existing datalist
            const existingDatalist = document.getElementById('permission-suggestions');
            if (existingDatalist) {
                existingDatalist.remove();
            }
            
            if (matches.length > 0 && value.length > 1) {
                const datalist = document.createElement('datalist');
                datalist.id = 'permission-suggestions';
                
                matches.forEach(match => {
                    const option = document.createElement('option');
                    option.value = match;
                    datalist.appendChild(option);
                });
                
                this.setAttribute('list', 'permission-suggestions');
                this.parentNode.appendChild(datalist);
            }
        });
    </script>
</body>
</html>