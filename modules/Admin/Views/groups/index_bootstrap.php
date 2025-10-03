<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Grupos - Admin</title>
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
        .group-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .group-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .permission-badge {
            font-size: 0.75rem;
            margin: 2px;
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
                        <span class="navbar-brand mb-0 h1">Gerenciar Grupos</span>
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
                    
                    <?php if (session('group_config')): ?>
                    <hr>
                    <details>
                        <summary><strong>Configuração para adicionar ao Config/AuthGroups.php:</strong></summary>
                        <pre class="mt-2 bg-light p-2 rounded"><code><?php
                        $config = session('group_config');
                        echo "// Em AuthGroups.php:\n";
                        echo "'{$config['name']}' => [\n";
                        echo "    'title' => '{$config['config']['title']}',\n";
                        echo "    'description' => '{$config['config']['description']}'\n";
                        echo "],\n\n";
                        echo "// Em AuthGroups.php \$matrix:\n";
                        echo "'{$config['name']}' => ['" . implode("', '", $config['permissions']) . "'],\n";
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
                    <!-- Header Actions -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="mb-0">
                                <i class="bi bi-collection me-2"></i>
                                Grupos de Usuários
                            </h4>
                            <p class="text-muted mb-0">Gerencie grupos e suas permissões no sistema</p>
                        </div>
                        <div>
                            <a href="/admin/groups/create" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                Novo Grupo
                            </a>
                        </div>
                    </div>

                    <!-- Groups Grid -->
                    <?php if (empty($groups)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-collection display-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum grupo encontrado</h5>
                            <p class="text-muted">Comece criando seu primeiro grupo de usuários</p>
                            <a href="/admin/groups/create" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                Criar Primeiro Grupo
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="row">
                        <?php foreach ($groups as $group): ?>
                        <div class="col-xl-4 col-lg-6 mb-4">
                            <div class="card group-card h-100">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-primary">
                                        <i class="bi bi-people-fill me-2"></i>
                                        <?= esc($group['title']) ?>
                                    </h6>
                                    <span class="badge bg-primary"><?= $group['user_count'] ?> usuários</span>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <small class="text-muted">Nome:</small>
                                        <div class="fw-bold"><?= esc($group['name']) ?></div>
                                    </div>
                                    
                                    <?php if (!empty($group['description'])): ?>
                                    <div class="mb-3">
                                        <small class="text-muted">Descrição:</small>
                                        <div><?= esc($group['description']) ?></div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">Permissões:</small>
                                        <div class="mt-1">
                                            <?php if (empty($group['permissions'])): ?>
                                                <span class="badge bg-light text-dark">Nenhuma</span>
                                            <?php else: ?>
                                                <?php foreach ($group['permissions'] as $permission): ?>
                                                    <span class="badge bg-info permission-badge"><?= esc($permission) ?></span>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-light">
                                    <div class="btn-group w-100" role="group">
                                        <a href="/admin/groups/users/<?= urlencode($group['name']) ?>" 
                                           class="btn btn-outline-info btn-sm" 
                                           title="Ver Usuários">
                                            <i class="bi bi-people"></i>
                                        </a>
                                        <a href="/admin/groups/edit/<?= urlencode($group['name']) ?>" 
                                           class="btn btn-outline-primary btn-sm" 
                                           title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-sm delete-group" 
                                                data-group-name="<?= esc($group['name']) ?>"
                                                data-group-title="<?= esc($group['title']) ?>"
                                                data-user-count="<?= $group['user_count'] ?>"
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
                        <p>Tem certeza que deseja excluir o grupo <strong id="deleteGroupTitle"></strong>?</p>
                        <div id="deleteWarning" class="text-danger mt-2">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Esta ação não pode ser desfeita.
                        </div>
                        <div id="deleteError" class="text-danger mt-2" style="display: none;">
                            <i class="bi bi-x-circle me-1"></i>
                            Não é possível excluir este grupo pois há usuários associados.
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
        // Delete group
        document.querySelectorAll('.delete-group').forEach(btn => {
            btn.addEventListener('click', function() {
                const groupName = this.dataset.groupName;
                const groupTitle = this.dataset.groupTitle;
                const userCount = parseInt(this.dataset.userCount);
                
                document.getElementById('deleteGroupTitle').textContent = groupTitle;
                document.getElementById('deleteForm').action = `/admin/groups/delete/${encodeURIComponent(groupName)}`;
                
                const deleteWarning = document.getElementById('deleteWarning');
                const deleteError = document.getElementById('deleteError');
                const deleteConfirm = document.getElementById('deleteConfirm');
                
                if (userCount > 0) {
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