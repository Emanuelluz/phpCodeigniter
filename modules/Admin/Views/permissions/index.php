<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Permissões - Admin</title>
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
        .permission-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .permission-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .matrix-cell {
            text-align: center;
            vertical-align: middle;
        }
        .table-matrix {
            font-size: 0.9rem;
        }
        .table-matrix th {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            min-width: 50px;
            max-width: 80px;
            word-wrap: break-word;
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
                        <span class="navbar-brand mb-0 h1">Gerenciar Permissões</span>
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
                    
                    <?php if (session('permission_config')): ?>
                    <hr>
                    <details>
                        <summary><strong>Configuração para adicionar ao Config/AuthGroups.php:</strong></summary>
                        <pre class="mt-2 bg-light p-2 rounded"><code><?php
                        $config = session('permission_config');
                        echo "// Em AuthGroups.php \$permissions:\n";
                        echo "'{$config['name']}' => '{$config['description']}',\n";
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
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs mb-4">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#permissions-tab">
                                <i class="bi bi-key me-2"></i>
                                Permissões
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#matrix-tab">
                                <i class="bi bi-grid-3x3 me-2"></i>
                                Matriz de Permissões
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Permissions Tab -->
                        <div class="tab-pane fade show active" id="permissions-tab">
                            <!-- Header Actions -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h4 class="mb-0">
                                        <i class="bi bi-key me-2"></i>
                                        Permissões do Sistema
                                    </h4>
                                    <p class="text-muted mb-0">Gerencie permissões individuais do sistema</p>
                                </div>
                                <div>
                                    <a href="/admin/permissions/create" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        Nova Permissão
                                    </a>
                                </div>
                            </div>

                            <!-- Permissions Grid -->
                            <?php if (empty($permissions)): ?>
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="bi bi-key display-1 text-muted mb-3"></i>
                                    <h5 class="text-muted">Nenhuma permissão encontrada</h5>
                                    <p class="text-muted">Comece criando sua primeira permissão do sistema</p>
                                    <a href="/admin/permissions/create" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        Criar Primeira Permissão
                                    </a>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="row">
                                <?php foreach ($permissions as $permissionName => $description): ?>
                                <div class="col-xl-4 col-lg-6 mb-4">
                                    <div class="card permission-card h-100">
                                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 fw-bold text-primary">
                                                <i class="bi bi-key-fill me-2"></i>
                                                <?= esc($permissionName) ?>
                                            </h6>
                                            <span class="badge bg-secondary">
                                                <?php
                                                // Contar quantos grupos usam esta permissão
                                                $count = 0;
                                                foreach ($matrix as $groupName => $groupData) {
                                                    if (in_array($permissionName, $groupData['permissions'])) {
                                                        $count++;
                                                    }
                                                }
                                                echo $count;
                                                ?> grupos
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <small class="text-muted">Descrição:</small>
                                                <div><?= esc($description) ?></div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <small class="text-muted">Grupos que possuem:</small>
                                                <div class="mt-1">
                                                    <?php
                                                    $groupsWithPermission = [];
                                                    foreach ($matrix as $groupName => $groupData) {
                                                        if (in_array($permissionName, $groupData['permissions'])) {
                                                            $groupsWithPermission[] = $groupData['title'];
                                                        }
                                                    }
                                                    ?>
                                                    <?php if (empty($groupsWithPermission)): ?>
                                                        <span class="badge bg-light text-dark">Nenhum grupo</span>
                                                    <?php else: ?>
                                                        <?php foreach ($groupsWithPermission as $groupTitle): ?>
                                                            <span class="badge bg-info me-1"><?= esc($groupTitle) ?></span>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <div class="btn-group w-100" role="group">
                                                <a href="/admin/permissions/edit/<?= urlencode($permissionName) ?>" 
                                                   class="btn btn-outline-primary btn-sm" 
                                                   title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-sm delete-permission" 
                                                        data-permission-name="<?= esc($permissionName) ?>"
                                                        data-groups-count="<?= count($groupsWithPermission) ?>"
                                                        title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Matrix Tab -->
                        <div class="tab-pane fade" id="matrix-tab">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h4 class="mb-0">
                                        <i class="bi bi-grid-3x3 me-2"></i>
                                        Matriz de Permissões
                                    </h4>
                                    <p class="text-muted mb-0">Visualize e edite permissões por grupo</p>
                                </div>
                                <div>
                                    <a href="/admin/permissions/matrix" class="btn btn-outline-primary">
                                        <i class="bi bi-pencil me-1"></i>
                                        Editar Matriz
                                    </a>
                                </div>
                            </div>

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
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-matrix">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-start">Grupos \ Permissões</th>
                                                    <?php foreach ($permissions as $permissionName => $description): ?>
                                                    <th title="<?= esc($description) ?>">
                                                        <?= esc($permissionName) ?>
                                                    </th>
                                                    <?php endforeach; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($matrix as $groupName => $groupData): ?>
                                                <tr>
                                                    <td class="fw-bold">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-people-fill me-2 text-primary"></i>
                                                            <?= esc($groupData['title']) ?>
                                                        </div>
                                                        <?php if (!empty($groupData['description'])): ?>
                                                        <small class="text-muted"><?= esc($groupData['description']) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <?php foreach ($permissions as $permissionName => $description): ?>
                                                    <td class="matrix-cell">
                                                        <?php if (in_array($permissionName, $groupData['permissions'])): ?>
                                                            <i class="bi bi-check-circle-fill text-success fs-5" title="Tem permissão"></i>
                                                        <?php else: ?>
                                                            <i class="bi bi-x-circle text-muted" title="Sem permissão"></i>
                                                        <?php endif; ?>
                                                    </td>
                                                    <?php endforeach; ?>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="deleteContent">
                        <p>Tem certeza que deseja excluir a permissão <strong id="deletePermissionName"></strong>?</p>
                        <div id="deleteWarning" class="text-danger mt-2">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Esta ação não pode ser desfeita.
                        </div>
                        <div id="deleteError" class="text-danger mt-2" style="display: none;">
                            <i class="bi bi-x-circle me-1"></i>
                            Não é possível excluir esta permissão pois há grupos que a utilizam.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteForm" method="post" style="display: inline;">
                        <button type="submit" class="btn btn-danger" id="deleteConfirm">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Delete permission
        document.querySelectorAll('.delete-permission').forEach(btn => {
            btn.addEventListener('click', function() {
                const permissionName = this.dataset.permissionName;
                const groupsCount = parseInt(this.dataset.groupsCount);
                
                document.getElementById('deletePermissionName').textContent = permissionName;
                document.getElementById('deleteForm').action = `/admin/permissions/delete/${encodeURIComponent(permissionName)}`;
                
                const deleteWarning = document.getElementById('deleteWarning');
                const deleteError = document.getElementById('deleteError');
                const deleteConfirm = document.getElementById('deleteConfirm');
                
                if (groupsCount > 0) {
                    deleteWarning.style.display = 'none';
                    deleteError.style.display = 'block';
                    deleteConfirm.disabled = true;
                } else {
                    deleteWarning.style.display = 'block';
                    deleteError.style.display = 'none';
                    deleteConfirm.disabled = false;
                }
                
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            });
        });

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                if (alert.querySelector('.btn-close')) {
                    alert.querySelector('.btn-close').click();
                }
            });
        }, 8000);
    </script>
</body>
</html>