<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matriz de Permissões - Admin</title>
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
        .matrix-cell {
            text-align: center;
            vertical-align: middle;
            padding: 15px 10px;
        }
        .table-matrix {
            font-size: 0.9rem;
        }
        .table-matrix th {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            min-width: 80px;
            max-width: 120px;
            word-wrap: break-word;
            background-color: #f8f9fa;
            border: 2px solid #dee2e6;
            font-weight: 600;
        }
        .table-matrix td {
            border: 1px solid #dee2e6;
        }
        .table-matrix .group-header {
            background-color: #e3f2fd;
            font-weight: 600;
            border: 2px solid #2196f3;
        }
        .permission-checkbox {
            transform: scale(1.5);
            margin: 0;
        }
        .permission-checkbox:checked {
            background-color: #28a745;
            border-color: #28a745;
        }
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f8f9fa;
        }
        .sticky-column {
            position: sticky;
            left: 0;
            z-index: 5;
            background-color: #ffffff;
            border-right: 2px solid #dee2e6;
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
                                <li class="breadcrumb-item active">Matriz de Permissões</li>
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

                <!-- Alerts -->
                <?php if (session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    <?= session('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    
                    <?php if (session('matrix_update')): ?>
                    <hr>
                    <details>
                        <summary><strong>Configuração para adicionar ao Config/AuthGroups.php:</strong></summary>
                        <pre class="mt-2 bg-light p-2 rounded"><code><?php
                        $matrixData = session('matrix_update');
                        echo "// Em AuthGroups.php \$matrix:\n";
                        foreach ($matrixData as $groupName => $permissions) {
                            echo "'{$groupName}' => ['" . implode("', '", $permissions) . "'],\n";
                        }
                        ?></code></pre>
                    </details>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if (session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Content -->
                <div class="container-fluid px-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="mb-0">
                                <i class="bi bi-grid-3x3 me-2"></i>
                                Matriz de Permissões
                            </h4>
                            <p class="text-muted mb-0">
                                Configure quais permissões cada grupo possui
                            </p>
                        </div>
                        <div>
                            <a href="/admin/permissions" class="btn btn-secondary me-2">
                                <i class="bi bi-arrow-left me-1"></i>
                                Voltar
                            </a>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h6 class="mb-2">Ações Rápidas:</h6>
                                    <button type="button" class="btn btn-sm btn-outline-success me-2" id="checkAll">
                                        <i class="bi bi-check-all me-1"></i>
                                        Marcar Todas
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger me-2" id="uncheckAll">
                                        <i class="bi bi-x me-1"></i>
                                        Desmarcar Todas
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info" id="resetMatrix">
                                        <i class="bi bi-arrow-clockwise me-1"></i>
                                        Resetar
                                    </button>
                                </div>
                                <div class="col-md-6 text-end">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="me-3">
                                            <i class="bi bi-info-circle text-info me-1"></i>
                                            Clique nos checkboxes para alterar permissões
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Matrix Form -->
                    <?php if (empty($permissions) || empty($matrix)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-grid-3x3 display-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Matriz não disponível</h5>
                            <p class="text-muted">
                                Você precisa ter pelo menos um grupo e uma permissão configurados
                            </p>
                            <div>
                                <a href="/admin/groups" class="btn btn-outline-primary me-2">
                                    <i class="bi bi-collection me-1"></i>
                                    Gerenciar Grupos
                                </a>
                                <a href="/admin/permissions/create" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    Criar Permissão
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    
                    <?= form_open('/admin/permissions/update-matrix', ['id' => 'matrixForm']) ?>
                    <div class="card">
                        <div class="card-header bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-grid-3x3 me-2"></i>
                                    Matriz Grupos × Permissões
                                </h5>
                                <div>
                                    <span class="badge bg-info me-2">
                                        <?= count($groups) ?> Grupos
                                    </span>
                                    <span class="badge bg-secondary">
                                        <?= count($permissions) ?> Permissões
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                                <table class="table table-bordered table-matrix mb-0">
                                    <thead class="sticky-header">
                                        <tr>
                                            <th class="sticky-column sticky-header text-start" style="min-width: 200px;">
                                                Grupos \ Permissões
                                            </th>
                                            <?php foreach ($permissions as $permissionName => $description): ?>
                                            <th title="<?= esc($description) ?>">
                                                <div class="text-break">
                                                    <?= esc($permissionName) ?>
                                                </div>
                                            </th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($matrix as $groupName => $groupData): ?>
                                        <tr>
                                            <td class="sticky-column group-header">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-people-fill me-2 text-primary"></i>
                                                    <div>
                                                        <div class="fw-bold"><?= esc($groupData['title']) ?></div>
                                                        <?php if (!empty($groupData['description'])): ?>
                                                        <small class="text-muted"><?= esc($groupData['description']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <?php foreach ($permissions as $permissionName => $description): ?>
                                            <td class="matrix-cell">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input class="form-check-input permission-checkbox" 
                                                           type="checkbox" 
                                                           name="matrix[<?= esc($groupName) ?>][<?= esc($permissionName) ?>]" 
                                                           value="1"
                                                           data-group="<?= esc($groupName) ?>"
                                                           data-permission="<?= esc($permissionName) ?>"
                                                           <?= $groupData['permissions'][$permissionName] ? 'checked' : '' ?>
                                                           title="<?= $groupData['permissions'][$permissionName] ? 'Remover' : 'Conceder' ?> permissão '<?= esc($permissionName) ?>' para grupo '<?= esc($groupData['title']) ?>'">
                                                </div>
                                            </td>
                                            <?php endforeach; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Alterações são salvas quando você clica em "Salvar Matriz"
                                </div>
                                <div>
                                    <button type="button" class="btn btn-secondary me-2" onclick="location.reload()">
                                        <i class="bi bi-arrow-clockwise me-1"></i>
                                        Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>
                                        Salvar Matriz
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?= form_close() ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Store original state for reset
        const originalState = new Map();
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            originalState.set(checkbox.name, checkbox.checked);
        });

        // Check all permissions
        document.getElementById('checkAll')?.addEventListener('click', function() {
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = true;
                updateCheckboxTitle(checkbox);
            });
        });

        // Uncheck all permissions
        document.getElementById('uncheckAll')?.addEventListener('click', function() {
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = false;
                updateCheckboxTitle(checkbox);
            });
        });

        // Reset to original state
        document.getElementById('resetMatrix')?.addEventListener('click', function() {
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = originalState.get(checkbox.name) || false;
                updateCheckboxTitle(checkbox);
            });
        });

        // Update checkbox titles dynamically
        function updateCheckboxTitle(checkbox) {
            const group = checkbox.dataset.group;
            const permission = checkbox.dataset.permission;
            const action = checkbox.checked ? 'Remover' : 'Conceder';
            checkbox.title = `${action} permissão '${permission}' para grupo '${group}'`;
        }

        // Add change listeners to all checkboxes
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateCheckboxTitle(this);
            });
        });

        // Form submission confirmation
        document.getElementById('matrixForm')?.addEventListener('submit', function(e) {
            const changedCount = Array.from(document.querySelectorAll('.permission-checkbox')).filter(checkbox => {
                return checkbox.checked !== (originalState.get(checkbox.name) || false);
            }).length;

            if (changedCount > 0) {
                if (!confirm(`Você fez ${changedCount} alteração(ões) na matriz. Deseja salvar?`)) {
                    e.preventDefault();
                }
            } else {
                alert('Nenhuma alteração foi feita na matriz.');
                e.preventDefault();
            }
        });

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                if (alert.querySelector('.btn-close')) {
                    alert.querySelector('.btn-close').click();
                }
            });
        }, 10000);
    </script>
</body>
</html>