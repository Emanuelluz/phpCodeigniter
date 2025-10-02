<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários do Grupo - Admin</title>
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
                                <li class="breadcrumb-item active">
                                    Usuários: <?= esc($group['title']) ?>
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
                    <!-- Group Info Header -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h4 class="mb-1">
                                        <i class="bi bi-people-fill me-2 text-primary"></i>
                                        <?= esc($group['title']) ?>
                                    </h4>
                                    <p class="text-muted mb-0">
                                        <strong>Nome:</strong> <?= esc($group['name']) ?>
                                        <?php if (!empty($group['description'])): ?>
                                            <br><?= esc($group['description']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <span class="badge bg-primary fs-6 me-2">
                                        <?= count($users) ?> usuário(s)
                                    </span>
                                    <a href="/admin/groups/edit/<?= urlencode($group['name']) ?>" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-pencil me-1"></i>
                                        Editar Grupo
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
                                Usuários do Grupo
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($users)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-people display-1 text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum usuário neste grupo</h5>
                                <p class="text-muted">Atribua usuários a este grupo através do gerenciamento de usuários</p>
                                <a href="/admin/users" class="btn btn-primary">
                                    <i class="bi bi-people me-1"></i>
                                    Gerenciar Usuários
                                </a>
                            </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Todos os Grupos</th>
                                            <th>Status</th>
                                            <th>Criado em</th>
                                            <th width="100">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
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
                                                    <?php foreach ($groups as $groupName): ?>
                                                        <span class="badge <?= $groupName === $group['name'] ? 'bg-primary' : 'bg-secondary' ?> me-1">
                                                            <?= esc($groupName) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($user->active): ?>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        Ativo
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle me-1"></i>
                                                        Inativo
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y H:i', strtotime($user->created_at)) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <a href="/admin/users/edit/<?= $user->id ?>" 
                                                   class="btn btn-outline-primary btn-sm" 
                                                   title="Editar Usuário">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Total: <?= count($users) ?> usuário(s)
                                </div>
                                <div>
                                    <a href="/admin/groups" class="btn btn-secondary btn-sm">
                                        <i class="bi bi-arrow-left me-1"></i>
                                        Voltar aos Grupos
                                    </a>
                                    <a href="/admin/users" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        Adicionar Usuários
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>