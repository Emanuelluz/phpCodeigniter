<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Grupo - Admin</title>
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
        .permission-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 10px;
            margin: 5px 0;
            transition: all 0.3s;
        }
        .permission-item:hover {
            background-color: #f8f9fa;
        }
        .permission-item.selected {
            background-color: #e3f2fd;
            border-color: #2196f3;
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
                        <a class="nav-link active" href="/admin/groups">
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
                                    <a href="/admin/groups" class="text-decoration-none">Grupos</a>
                                </li>
                                <li class="breadcrumb-item active">Criar Grupo</li>
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
                                        Criar Novo Grupo
                                    </h5>
                                </div>
                                
                                <?= form_open('/admin/groups/store', ['class' => 'needs-validation', 'novalidate' => true]) ?>
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
                                    
                                    <div class="row">
                                        <!-- Name -->
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">
                                                Nome do Grupo <span class="required">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>" 
                                                   id="name" 
                                                   name="name" 
                                                   value="<?= old('name') ?>"
                                                   pattern="[a-zA-Z0-9_-]+"
                                                   required>
                                            <div class="form-text">Apenas letras, números, _ e - (sem espaços)</div>
                                            <div class="invalid-feedback">
                                                <?= session('errors.name') ?? 'Nome é obrigatório' ?>
                                            </div>
                                        </div>

                                        <!-- Title -->
                                        <div class="col-md-6 mb-3">
                                            <label for="title" class="form-label">
                                                Título de Exibição <span class="required">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control <?= session('errors.title') ? 'is-invalid' : '' ?>" 
                                                   id="title" 
                                                   name="title" 
                                                   value="<?= old('title') ?>"
                                                   required>
                                            <div class="form-text">Nome amigável para exibição</div>
                                            <div class="invalid-feedback">
                                                <?= session('errors.title') ?? 'Título é obrigatório' ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Descrição</label>
                                        <textarea class="form-control <?= session('errors.description') ? 'is-invalid' : '' ?>" 
                                                  id="description" 
                                                  name="description" 
                                                  rows="3"
                                                  placeholder="Descreva o propósito deste grupo..."><?= old('description') ?></textarea>
                                        <div class="invalid-feedback">
                                            <?= session('errors.description') ?>
                                        </div>
                                    </div>

                                    <!-- Permissions -->
                                    <div class="mb-3">
                                        <label class="form-label">Permissões</label>
                                        <div class="card border-light">
                                            <div class="card-body">
                                                <?php if (empty($permissions)): ?>
                                                    <div class="text-muted">
                                                        <i class="bi bi-info-circle me-2"></i>
                                                        Nenhuma permissão configurada no sistema
                                                    </div>
                                                <?php else: ?>
                                                    <div class="row">
                                                        <?php foreach ($permissions as $permission => $description): ?>
                                                        <div class="col-md-6 mb-2">
                                                            <div class="permission-item">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" 
                                                                           type="checkbox" 
                                                                           name="permissions[]" 
                                                                           value="<?= esc($permission) ?>" 
                                                                           id="perm_<?= esc($permission) ?>"
                                                                           <?= in_array($permission, old('permissions', [])) ? 'checked' : '' ?>>
                                                                    <label class="form-check-label" for="perm_<?= esc($permission) ?>">
                                                                        <strong><?= esc($permission) ?></strong>
                                                                        <?php if ($description): ?>
                                                                            <br><small class="text-muted"><?= esc($description) ?></small>
                                                                        <?php endif; ?>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    
                                                    <div class="mt-3">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">
                                                            <i class="bi bi-check-all me-1"></i>
                                                            Selecionar Todas
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="selectNone">
                                                            <i class="bi bi-x me-1"></i>
                                                            Limpar Seleção
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer bg-light">
                                    <div class="d-flex justify-content-between">
                                        <a href="/admin/groups" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left me-1"></i>
                                            Voltar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            Criar Grupo
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

        // Auto-generate name from title
        document.getElementById('title').addEventListener('input', function() {
            const nameField = document.getElementById('name');
            if (!nameField.value) {
                const name = this.value
                    .toLowerCase()
                    .replace(/[^a-z0-9\s]/g, '')
                    .replace(/\s+/g, '_')
                    .substring(0, 30);
                nameField.value = name;
            }
        });

        // Select/Deselect all permissions
        document.getElementById('selectAll')?.addEventListener('click', function() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                cb.checked = true;
                cb.closest('.permission-item').classList.add('selected');
            });
        });

        document.getElementById('selectNone')?.addEventListener('click', function() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                cb.checked = false;
                cb.closest('.permission-item').classList.remove('selected');
            });
        });

        // Visual feedback for selected permissions
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
            cb.addEventListener('change', function() {
                const item = this.closest('.permission-item');
                if (this.checked) {
                    item.classList.add('selected');
                } else {
                    item.classList.remove('selected');
                }
            });
            
            // Initial state
            if (cb.checked) {
                cb.closest('.permission-item').classList.add('selected');
            }
        });
    </script>
</body>
</html>