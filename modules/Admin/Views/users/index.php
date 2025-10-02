<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - Admin</title>
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
                        <a class="nav-link" href="<?= base_url('admin') ?>">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard
                        </a>
                        <a class="nav-link active" href="<?= base_url('admin/users') ?>">
                            <i class="bi bi-people me-2"></i>
                            Usuários
                        </a>
                        <a class="nav-link" href="<?= base_url('admin/groups') ?>">
                            <i class="bi bi-collection me-2"></i>
                            Grupos
                        </a>
                        <a class="nav-link" href="<?= base_url('admin/permissions') ?>">
                            <i class="bi bi-key me-2"></i>
                            Permissões
                        </a>
                        <hr class="text-white-50">
                        <a class="nav-link" href="<?= base_url('/') ?>">
                            <i class="bi bi-house me-2"></i>
                            Ir para o Site
                        </a>
                        <a class="nav-link" href="<?= base_url('logout') ?>">
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
                        <span class="navbar-brand mb-0 h1">Gerenciar Usuários</span>
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
                    <!-- Filters and Actions -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <form method="get" class="d-flex">
                                        <input type="text" class="form-control me-2" 
                                               name="search" 
                                               placeholder="Buscar por username ou email..." 
                                               value="<?= esc($search) ?>">
                                        <select name="status" class="form-select me-2" style="width: auto;">
                                            <option value="">Todos os status</option>
                                            <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Ativo</option>
                                            <option value="0" <?= $status === '0' ? 'selected' : '' ?>>Inativo</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="col-md-6 text-end">
                                    <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        Novo Usuário
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-people me-2"></i>
                                Lista de Usuários
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Grupos</th>
                                            <th>Status</th>
                                            <th>Criado em</th>
                                            <th width="120">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($users)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                                Nenhum usuário encontrado
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?= esc($user->id) ?></td>
                                            <td>
                                                <strong><?= esc($user->username) ?></strong>
                                            </td>
                                            <td><?= esc($user->email) ?></td>
                                            <td>
                                                <?php 
                                                $groups = $user->getGroups();
                                                if (empty($groups)): ?>
                                                    <span class="badge bg-light text-dark">Nenhum</span>
                                                <?php else: ?>
                                                    <?php foreach ($groups as $group): ?>
                                                        <span class="badge bg-primary me-1"><?= esc($group) ?></span>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input status-toggle" 
                                                           type="checkbox" 
                                                           data-user-id="<?= $user->id ?>"
                                                           <?= $user->active ? 'checked' : '' ?>
                                                           <?= $user->id === auth()->id() ? 'disabled' : '' ?>>
                                                    <label class="form-check-label">
                                                        <?= $user->active ? 'Ativo' : 'Inativo' ?>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y H:i', strtotime($user->created_at)) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('admin/users/edit/' . $user->id) ?>" 
                                                       class="btn btn-outline-primary" 
                                                       title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <?php if ($user->id !== auth()->id()): ?>
                                                    <button type="button" 
                                                            class="btn btn-outline-danger delete-user" 
                                                            data-user-id="<?= $user->id ?>"
                                                            data-username="<?= esc($user->username) ?>"
                                                            title="Excluir">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
                        <div class="card-footer bg-white">
                            <?= $pager->links() ?>
                        </div>
                        <?php endif; ?>
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
                    Tem certeza que deseja excluir o usuário <strong id="deleteUsername"></strong>?
                    <div class="text-danger mt-2">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Esta ação não pode ser desfeita.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteForm" method="post" style="display: inline;">
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle status
        document.querySelectorAll('.status-toggle').forEach(toggle => {
            toggle.addEventListener('change', async function() {
                const userId = this.dataset.userId;
                const isActive = this.checked;
                
                try {
                    const response = await fetch(`<?= base_url('admin/users/toggle-status') ?>/${userId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        this.checked = result.active;
                        const label = this.nextElementSibling;
                        label.textContent = result.active ? 'Ativo' : 'Inativo';
                        
                        // Show success message
                        showAlert('success', result.message);
                    } else {
                        this.checked = !isActive; // Revert
                        showAlert('danger', result.message);
                    }
                } catch (error) {
                    this.checked = !isActive; // Revert
                    showAlert('danger', 'Erro ao alterar status do usuário');
                }
            });
        });
        
        // Delete user
        document.querySelectorAll('.delete-user').forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.dataset.userId;
                const username = this.dataset.username;
                
                document.getElementById('deleteUsername').textContent = username;
                document.getElementById('deleteForm').action = `<?= base_url('admin/users/delete') ?>/${userId}`;
                
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            });
        });
        
        // Show alert function
        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.querySelector('.main-content .container-fluid').insertBefore(
                alertDiv, 
                document.querySelector('.main-content .container-fluid').firstChild
            );
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    </script>
</body>
</html>