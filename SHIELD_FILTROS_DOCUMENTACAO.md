# 🛡️ Filtros de Grupos e Permissões Shield - Guia Completo

> **CodeIgniter 4 + Shield**: Como proteger rotas com grupos e permissões

---

## 📋 **ÍNDICE**

1. [Conceitos Básicos](#conceitos-básicos)
2. [Configuração de Filtros](#configuração-de-filtros)
3. [Proteção por Grupos](#proteção-por-grupos)
4. [Proteção por Permissões](#proteção-por-permissões)
5. [Filtros Combinados](#filtros-combinados)
6. [Aplicação em Controllers](#aplicação-em-controllers)
7. [Exemplos Práticos](#exemplos-práticos)
8. [Troubleshooting](#troubleshooting)

---

## 🎯 **CONCEITOS BÁSICOS**

### **O que são Filtros Shield?**

Os filtros do Shield permitem proteger rotas específicas baseado em:
- **Autenticação**: Se o usuário está logado
- **Grupos**: Se o usuário pertence a grupos específicos  
- **Permissões**: Se o usuário tem permissões específicas

### **Hierarquia de Proteção**
```
🔓 Rota Pública
    ↓
🔐 Autenticação (session)
    ↓  
👥 Grupos (group)
    ↓
🛡️ Permissões (permission)
```

---

## ⚙️ **CONFIGURAÇÃO DE FILTROS**

### **1. Arquivo de Configuração Principal**
📁 `app/Config/Filters.php`

```php
<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        
        // 🛡️ FILTROS SHIELD
        'session'       => \CodeIgniter\Shield\Filters\SessionAuth::class,
        'tokens'        => \CodeIgniter\Shield\Filters\TokenAuth::class,
        'chain'         => \CodeIgniter\Shield\Filters\ChainAuth::class,
        'auth-rates'    => \CodeIgniter\Shield\Filters\AuthRates::class,
        'group'         => \CodeIgniter\Shield\Filters\GroupFilter::class,
        'permission'    => \CodeIgniter\Shield\Filters\PermissionFilter::class,
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
            // 'invalidchars',
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     */
    public array $methods = [];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     */
    public array $filters = [];
}
```

---

## 👥 **PROTEÇÃO POR GRUPOS**

### **Filtro `group`**
Permite acesso apenas para usuários de grupos específicos.

### **1. Configuração no Routes.php**

```php
<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// 🏠 Rotas públicas
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');

// 🔐 Rotas que exigem login (qualquer usuário autenticado)
$routes->group('', ['filter' => 'session'], function($routes) {
    $routes->get('/dashboard', 'Dashboard::index');
    $routes->get('/profile', 'User::profile');
});

// 👑 Rotas para ADMINISTRADORES apenas
$routes->group('admin', ['filter' => 'session,group:superadmin,admin'], function($routes) {
    $routes->get('/', 'Admin::dashboard');
    $routes->get('/users', 'Admin::users');
    $routes->get('/settings', 'Admin::settings');
});

// 📊 Rotas para GERENTES apenas  
$routes->group('manager', ['filter' => 'session,group:manager'], function($routes) {
    $routes->get('/', 'Manager::dashboard');
    $routes->get('/reports', 'Manager::reports');
    $routes->get('/team', 'Manager::team');
});

// 📝 Rotas para EDITORES apenas
$routes->group('editor', ['filter' => 'session,group:editor'], function($routes) {
    $routes->get('/', 'Editor::dashboard');
    $routes->get('/posts', 'Editor::posts');
    $routes->post('/posts/create', 'Editor::createPost');
});

// 🏪 Rotas para USUÁRIOS PREMIUM apenas
$routes->group('premium', ['filter' => 'session,group:premium'], function($routes) {
    $routes->get('/', 'Premium::dashboard');
    $routes->get('/features', 'Premium::features');
    $routes->get('/downloads', 'Premium::downloads');
});
```

### **2. Múltiplos Grupos (OR)**
```php
// Usuário pode ser ADMIN OU MANAGER OU EDITOR
$routes->group('management', ['filter' => 'session,group:admin,manager,editor'], function($routes) {
    $routes->get('/', 'Management::dashboard');
    $routes->get('/overview', 'Management::overview');
});
```

### **3. Rotas Individuais com Grupos**
```php
// Rota específica para super admins
$routes->get('/admin/system', 'Admin::system', ['filter' => 'session,group:superadmin']);

// Rota para admins e managers
$routes->get('/reports/analytics', 'Reports::analytics', ['filter' => 'session,group:admin,manager']);

// Rota para qualquer usuário logado
$routes->get('/dashboard/home', 'Dashboard::home', ['filter' => 'session']);
```

---

## 🛡️ **PROTEÇÃO POR PERMISSÕES**

### **Filtro `permission`**
Permite acesso apenas para usuários com permissões específicas.

### **1. Configuração no Routes.php**

```php
<?php

// 👥 PERMISSÕES DE USUÁRIOS
$routes->group('users', ['filter' => 'session'], function($routes) {
    // Listar usuários (permissão: users.list)
    $routes->get('/', 'Users::index', ['filter' => 'permission:users.list']);
    
    // Criar usuários (permissão: users.create)
    $routes->get('/create', 'Users::create', ['filter' => 'permission:users.create']);
    $routes->post('/store', 'Users::store', ['filter' => 'permission:users.create']);
    
    // Editar usuários (permissão: users.edit)
    $routes->get('/edit/(:num)', 'Users::edit/$1', ['filter' => 'permission:users.edit']);
    $routes->post('/update/(:num)', 'Users::update/$1', ['filter' => 'permission:users.edit']);
    
    // Excluir usuários (permissão: users.delete)
    $routes->post('/delete/(:num)', 'Users::delete/$1', ['filter' => 'permission:users.delete']);
});

// 📝 PERMISSÕES DE POSTS/CONTEÚDO
$routes->group('posts', ['filter' => 'session'], function($routes) {
    // Ver posts (permissão: posts.view)
    $routes->get('/', 'Posts::index', ['filter' => 'permission:posts.view']);
    
    // Criar posts (permissão: posts.create)
    $routes->get('/create', 'Posts::create', ['filter' => 'permission:posts.create']);
    $routes->post('/store', 'Posts::store', ['filter' => 'permission:posts.create']);
    
    // Publicar posts (permissão: posts.publish)
    $routes->post('/publish/(:num)', 'Posts::publish/$1', ['filter' => 'permission:posts.publish']);
    
    // Moderar posts (permissão: posts.moderate)
    $routes->get('/moderate', 'Posts::moderate', ['filter' => 'permission:posts.moderate']);
});

// 📊 PERMISSÕES DE RELATÓRIOS
$routes->group('reports', ['filter' => 'session'], function($routes) {
    // Relatórios básicos (permissão: reports.view)
    $routes->get('/', 'Reports::index', ['filter' => 'permission:reports.view']);
    
    // Relatórios financeiros (permissão: reports.financial)
    $routes->get('/financial', 'Reports::financial', ['filter' => 'permission:reports.financial']);
    
    // Relatórios de sistema (permissão: reports.system)
    $routes->get('/system', 'Reports::system', ['filter' => 'permission:reports.system']);
    
    // Exportar relatórios (permissão: reports.export)
    $routes->get('/export/(:segment)', 'Reports::export/$1', ['filter' => 'permission:reports.export']);
});

// ⚙️ PERMISSÕES ADMINISTRATIVAS
$routes->group('admin', ['filter' => 'session'], function($routes) {
    // Acesso ao painel admin (permissão: admin.access)
    $routes->get('/', 'Admin::dashboard', ['filter' => 'permission:admin.access']);
    
    // Configurações do sistema (permissão: admin.settings)
    $routes->get('/settings', 'Admin::settings', ['filter' => 'permission:admin.settings']);
    $routes->post('/settings/save', 'Admin::saveSettings', ['filter' => 'permission:admin.settings']);
    
    // Logs do sistema (permissão: admin.logs)
    $routes->get('/logs', 'Admin::logs', ['filter' => 'permission:admin.logs']);
    
    // Backup do sistema (permissão: admin.backup)
    $routes->get('/backup', 'Admin::backup', ['filter' => 'permission:admin.backup']);
});
```

### **2. Múltiplas Permissões (OR)**
```php
// Usuário precisa ter QUALQUER UMA das permissões
$routes->get('/content/manage', 'Content::manage', [
    'filter' => 'session,permission:posts.edit,posts.moderate,posts.publish'
]);
```

### **3. Permissões Hierárquicas**
```php
// Estrutura hierárquica de permissões
$routes->group('api', ['filter' => 'session'], function($routes) {
    // API básica (permissão: api.access)
    $routes->get('/', 'Api::index', ['filter' => 'permission:api.access']);
    
    // API de usuários (permissão: api.users)
    $routes->group('users', ['filter' => 'permission:api.users'], function($routes) {
        $routes->get('/', 'Api\Users::index');
        $routes->get('/(:num)', 'Api\Users::show/$1');
    });
    
    // API de admin (permissão: api.admin)
    $routes->group('admin', ['filter' => 'permission:api.admin'], function($routes) {
        $routes->get('/stats', 'Api\Admin::stats');
        $routes->post('/maintenance', 'Api\Admin::maintenance');
    });
});
```

---

## 🔗 **FILTROS COMBINADOS**

### **Grupos + Permissões**
Combine filtros para maior controle de acesso.

```php
<?php

// 🎯 EXEMPLO 1: Admin COM permissão específica
$routes->group('admin/users', [
    'filter' => 'session,group:admin,permission:users.manage'
], function($routes) {
    $routes->get('/', 'Admin\Users::index');
    $routes->post('/ban/(:num)', 'Admin\Users::ban/$1');
});

// 🎯 EXEMPLO 2: Manager OU Admin COM permissão de relatórios
$routes->group('reports/advanced', [
    'filter' => 'session,group:admin,manager,permission:reports.advanced'
], function($routes) {
    $routes->get('/', 'Reports::advanced');
    $routes->get('/export', 'Reports::export');
});

// 🎯 EXEMPLO 3: Múltiplos grupos COM múltiplas permissões
$routes->group('content/moderation', [
    'filter' => 'session,group:admin,moderator,editor,permission:content.moderate,content.publish'
], function($routes) {
    $routes->get('/', 'Moderation::index');
    $routes->post('/approve/(:num)', 'Moderation::approve/$1');
    $routes->post('/reject/(:num)', 'Moderation::reject/$1');
});

// 🎯 EXEMPLO 4: Super Admin OU permissão específica para operações críticas
$routes->group('system', ['filter' => 'session'], function($routes) {
    $routes->get('/maintenance', 'System::maintenance', [
        'filter' => 'group:superadmin,permission:system.maintenance'
    ]);
    
    $routes->post('/reset', 'System::reset', [
        'filter' => 'group:superadmin,permission:system.reset'
    ]);
});
```

---

## 🎮 **APLICAÇÃO EM CONTROLLERS**

### **1. Proteção no Constructor**
```php
<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class AdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // 🛡️ Verificar se usuário está logado
        if (!auth()->loggedIn()) {
            return redirect()->to('/login');
        }
        
        // 👑 Verificar se é admin
        if (!auth()->user()->inGroup('admin')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }
        
        // 🔐 Verificar permissão específica
        if (!auth()->user()->can('admin.access')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }
    }
    
    public function dashboard()
    {
        return view('admin/dashboard');
    }
    
    public function users()
    {
        // Verificação adicional para esta ação específica
        if (!auth()->user()->can('users.manage')) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }
        
        return view('admin/users');
    }
}
```

### **2. Proteção em Métodos Específicos**
```php
<?php

namespace App\Controllers;

class PostsController extends Controller
{
    public function index()
    {
        // Qualquer usuário logado pode ver posts
        return view('posts/index');
    }
    
    public function create()
    {
        // Verificar permissão para criar posts
        if (!auth()->user()->can('posts.create')) {
            return redirect()->back()->with('error', 'Você não tem permissão para criar posts.');
        }
        
        return view('posts/create');
    }
    
    public function publish($id)
    {
        // Verificar permissão para publicar
        if (!auth()->user()->can('posts.publish')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Permissão negada para publicar posts.'
            ]);
        }
        
        // Lógica de publicação...
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Post publicado com sucesso!'
        ]);
    }
    
    public function delete($id)
    {
        // Verificar se é admin OU tem permissão específica
        if (!auth()->user()->inGroup('admin') && !auth()->user()->can('posts.delete')) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }
        
        // Lógica de exclusão...
        
        return redirect()->to('/posts')->with('success', 'Post excluído com sucesso!');
    }
}
```

### **3. Helper para Verificações**
```php
<?php

// app/Helpers/auth_helper.php

if (!function_exists('requireGroup')) {
    /**
     * Verificar se usuário está em grupo específico
     */
    function requireGroup(string $group): bool
    {
        if (!auth()->loggedIn()) {
            return false;
        }
        
        return auth()->user()->inGroup($group);
    }
}

if (!function_exists('requirePermission')) {
    /**
     * Verificar se usuário tem permissão específica
     */
    function requirePermission(string $permission): bool
    {
        if (!auth()->loggedIn()) {
            return false;
        }
        
        return auth()->user()->can($permission);
    }
}

if (!function_exists('requireGroupOrPermission')) {
    /**
     * Verificar se usuário está em grupo OU tem permissão
     */
    function requireGroupOrPermission(string $group, string $permission): bool
    {
        if (!auth()->loggedIn()) {
            return false;
        }
        
        return auth()->user()->inGroup($group) || auth()->user()->can($permission);
    }
}

// Uso nos controllers:
public function sensitiveAction()
{
    if (!requireGroupOrPermission('admin', 'system.access')) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }
    
    // Ação sensível...
}
```

---

## 📚 **EXEMPLOS PRÁTICOS**

### **1. Sistema de Blog**
```php
<?php

// 📝 BLOG COM DIFERENTES NÍVEIS DE ACESSO

// Rotas públicas (qualquer visitante)
$routes->get('/blog', 'Blog::index');
$routes->get('/blog/post/(:segment)', 'Blog::show/$1');

// Área do autor (usuários logados)
$routes->group('author', ['filter' => 'session'], function($routes) {
    $routes->get('/', 'Author::dashboard');
    
    // Criar posts (permissão: posts.create)
    $routes->get('/posts/create', 'Author::createPost', ['filter' => 'permission:posts.create']);
    $routes->post('/posts/store', 'Author::storePost', ['filter' => 'permission:posts.create']);
    
    // Editar próprios posts (permissão: posts.edit)
    $routes->get('/posts/edit/(:num)', 'Author::editPost/$1', ['filter' => 'permission:posts.edit']);
    $routes->post('/posts/update/(:num)', 'Author::updatePost/$1', ['filter' => 'permission:posts.edit']);
});

// Área de moderação (moderadores e admins)
$routes->group('moderation', [
    'filter' => 'session,group:moderator,admin'
], function($routes) {
    $routes->get('/', 'Moderation::dashboard');
    
    // Moderar posts (permissão: posts.moderate)
    $routes->get('/posts', 'Moderation::posts', ['filter' => 'permission:posts.moderate']);
    $routes->post('/posts/approve/(:num)', 'Moderation::approve/$1', ['filter' => 'permission:posts.moderate']);
    $routes->post('/posts/reject/(:num)', 'Moderation::reject/$1', ['filter' => 'permission:posts.moderate']);
});

// Área administrativa (apenas admins)
$routes->group('admin', [
    'filter' => 'session,group:admin'
], function($routes) {
    $routes->get('/', 'Admin::dashboard');
    
    // Gerenciar usuários (permissão: users.manage)
    $routes->get('/users', 'Admin::users', ['filter' => 'permission:users.manage']);
    $routes->post('/users/ban/(:num)', 'Admin::banUser/$1', ['filter' => 'permission:users.manage']);
    
    // Configurações (permissão: admin.settings)
    $routes->get('/settings', 'Admin::settings', ['filter' => 'permission:admin.settings']);
    $routes->post('/settings/save', 'Admin::saveSettings', ['filter' => 'permission:admin.settings']);
});
```

### **2. Sistema de E-commerce**
```php
<?php

// 🛒 E-COMMERCE COM PERMISSÕES GRANULARES

// Área do cliente (usuários logados)
$routes->group('customer', ['filter' => 'session'], function($routes) {
    $routes->get('/', 'Customer::dashboard');
    $routes->get('/orders', 'Customer::orders');
    $routes->get('/profile', 'Customer::profile');
});

// Área do vendedor (grupo: seller)
$routes->group('seller', [
    'filter' => 'session,group:seller'
], function($routes) {
    $routes->get('/', 'Seller::dashboard');
    
    // Gerenciar produtos (permissão: products.manage)
    $routes->get('/products', 'Seller::products', ['filter' => 'permission:products.manage']);
    $routes->get('/products/create', 'Seller::createProduct', ['filter' => 'permission:products.create']);
    $routes->post('/products/store', 'Seller::storeProduct', ['filter' => 'permission:products.create']);
    
    // Ver vendas (permissão: sales.view)
    $routes->get('/sales', 'Seller::sales', ['filter' => 'permission:sales.view']);
    
    // Relatórios de vendas (permissão: sales.reports)
    $routes->get('/reports', 'Seller::reports', ['filter' => 'permission:sales.reports']);
});

// Área administrativa (grupo: admin)
$routes->group('admin', [
    'filter' => 'session,group:admin'
], function($routes) {
    $routes->get('/', 'Admin::dashboard');
    
    // Gerenciar todos os produtos (permissão: products.admin)
    $routes->get('/products', 'Admin::products', ['filter' => 'permission:products.admin']);
    $routes->post('/products/feature/(:num)', 'Admin::featureProduct/$1', ['filter' => 'permission:products.admin']);
    
    // Gerenciar pedidos (permissão: orders.admin)
    $routes->get('/orders', 'Admin::orders', ['filter' => 'permission:orders.admin']);
    $routes->post('/orders/cancel/(:num)', 'Admin::cancelOrder/$1', ['filter' => 'permission:orders.admin']);
    
    // Relatórios financeiros (permissão: finance.reports)
    $routes->get('/finance', 'Admin::finance', ['filter' => 'permission:finance.reports']);
    
    // Configurações do sistema (permissão: system.settings)
    $routes->get('/settings', 'Admin::settings', ['filter' => 'permission:system.settings']);
});

// Super administrador (grupo: superadmin)
$routes->group('superadmin', [
    'filter' => 'session,group:superadmin'
], function($routes) {
    $routes->get('/', 'SuperAdmin::dashboard');
    
    // Backup do sistema (permissão: system.backup)
    $routes->get('/backup', 'SuperAdmin::backup', ['filter' => 'permission:system.backup']);
    
    // Manutenção (permissão: system.maintenance)
    $routes->get('/maintenance', 'SuperAdmin::maintenance', ['filter' => 'permission:system.maintenance']);
    
    // Logs do sistema (permissão: system.logs)
    $routes->get('/logs', 'SuperAdmin::logs', ['filter' => 'permission:system.logs']);
});
```

### **3. Sistema Educacional**
```php
<?php

// 🎓 PLATAFORMA EDUCACIONAL

// Área do estudante (grupo: student)
$routes->group('student', [
    'filter' => 'session,group:student'
], function($routes) {
    $routes->get('/', 'Student::dashboard');
    
    // Ver cursos matriculados (permissão: courses.view)
    $routes->get('/courses', 'Student::courses', ['filter' => 'permission:courses.view']);
    $routes->get('/courses/(:num)', 'Student::course/$1', ['filter' => 'permission:courses.view']);
    
    // Fazer exercícios (permissão: exercises.submit)
    $routes->get('/exercises/(:num)', 'Student::exercise/$1', ['filter' => 'permission:exercises.submit']);
    $routes->post('/exercises/submit/(:num)', 'Student::submitExercise/$1', ['filter' => 'permission:exercises.submit']);
    
    // Ver notas (permissão: grades.view)
    $routes->get('/grades', 'Student::grades', ['filter' => 'permission:grades.view']);
});

// Área do professor (grupo: teacher)
$routes->group('teacher', [
    'filter' => 'session,group:teacher'
], function($routes) {
    $routes->get('/', 'Teacher::dashboard');
    
    // Gerenciar cursos (permissão: courses.manage)
    $routes->get('/courses', 'Teacher::courses', ['filter' => 'permission:courses.manage']);
    $routes->get('/courses/create', 'Teacher::createCourse', ['filter' => 'permission:courses.create']);
    $routes->post('/courses/store', 'Teacher::storeCourse', ['filter' => 'permission:courses.create']);
    
    // Criar exercícios (permissão: exercises.create)
    $routes->get('/exercises/create', 'Teacher::createExercise', ['filter' => 'permission:exercises.create']);
    $routes->post('/exercises/store', 'Teacher::storeExercise', ['filter' => 'permission:exercises.create']);
    
    // Avaliar exercícios (permissão: exercises.grade)
    $routes->get('/exercises/grade', 'Teacher::gradeExercises', ['filter' => 'permission:exercises.grade']);
    $routes->post('/exercises/grade/(:num)', 'Teacher::submitGrade/$1', ['filter' => 'permission:exercises.grade']);
    
    // Gerenciar notas (permissão: grades.manage)
    $routes->get('/grades', 'Teacher::grades', ['filter' => 'permission:grades.manage']);
});

// Coordenação (grupo: coordinator)
$routes->group('coordinator', [
    'filter' => 'session,group:coordinator'
], function($routes) {
    $routes->get('/', 'Coordinator::dashboard');
    
    // Relatórios acadêmicos (permissão: reports.academic)
    $routes->get('/reports', 'Coordinator::reports', ['filter' => 'permission:reports.academic']);
    
    // Gerenciar professores (permissão: teachers.manage)
    $routes->get('/teachers', 'Coordinator::teachers', ['filter' => 'permission:teachers.manage']);
    
    // Aprovar cursos (permissão: courses.approve)
    $routes->get('/courses/pending', 'Coordinator::pendingCourses', ['filter' => 'permission:courses.approve']);
    $routes->post('/courses/approve/(:num)', 'Coordinator::approveCourse/$1', ['filter' => 'permission:courses.approve']);
});

// Administração acadêmica (grupo: admin)
$routes->group('admin', [
    'filter' => 'session,group:admin'
], function($routes) {
    $routes->get('/', 'Admin::dashboard');
    
    // Gerenciar todos os usuários (permissão: users.admin)
    $routes->get('/users', 'Admin::users', ['filter' => 'permission:users.admin']);
    
    // Configurações acadêmicas (permissão: academic.settings)
    $routes->get('/settings', 'Admin::settings', ['filter' => 'permission:academic.settings']);
    
    // Relatórios completos (permissão: reports.full)
    $routes->get('/reports/full', 'Admin::fullReports', ['filter' => 'permission:reports.full']);
});
```

---

## 🔍 **TROUBLESHOOTING**

### **Problemas Comuns e Soluções**

#### **1. Erro: "Filter não encontrado"**
```
Filtro 'group' ou 'permission' não foi carregado.
```

**Solução:**
```php
// Verificar app/Config/Filters.php
public array $aliases = [
    // Certifique-se que estes estão definidos:
    'group'      => \CodeIgniter\Shield\Filters\GroupFilter::class,
    'permission' => \CodeIgniter\Shield\Filters\PermissionFilter::class,
];
```

#### **2. Redirecionamento em Loop**
```
Usuário fica redirecionando infinitamente.
```

**Solução:**
```php
// Verificar se a rota de login não tem filtros
$routes->get('/login', 'Auth::login'); // ❌ SEM filtros!

// Excluir rotas de auth dos filtros globais
public array $filters = [
    'session' => [
        'before' => ['admin/*', 'dashboard/*'], 
        'except' => ['login', 'register', 'logout'] // ✅ Exceções
    ]
];
```

#### **3. Usuário com Grupo mas Sem Acesso**
```
Usuário está no grupo mas ainda não tem acesso.
```

**Solução:**
```php
// Verificar configuração em app/Config/AuthGroups.php
public array $matrix = [
    'superadmin' => ['admin.*', 'users.*', 'beta.*'],
    'admin'      => ['admin.access', 'users.*'],
    'manager'    => ['users.list', 'users.create'],
    'user'       => ['users.list'],
];

// Certifique-se que as permissões estão na matriz
```

#### **4. Permissões não Funcionam**
```
Filtro de permissão não está bloqueando usuários.
```

**Solução:**
```php
// Verificar se permissões estão definidas em app/Config/AuthGroups.php
public array $permissions = [
    'admin.access'   => 'Can access the sites admin area',
    'admin.settings' => 'Can change the sites settings',
    'users.manage'   => 'Can manage all users',
    'users.create'   => 'Can create new users',
    'users.edit'     => 'Can edit existing users',
    'users.delete'   => 'Can delete existing users',
];

// Verificar se estão na matriz de grupos
public array $matrix = [
    'admin' => [
        'admin.access',
        'admin.settings', 
        'users.manage',
        'users.create',
        'users.edit',
        'users.delete'
    ],
];
```

#### **5. Performance com Muitos Filtros**
```
Site ficando lento com muitas verificações.
```

**Solução:**
```php
// Cache de permissões no controller
class BaseController extends Controller
{
    protected $userPermissions;
    
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        // Cache das permissões do usuário
        if (auth()->loggedIn()) {
            $this->userPermissions = auth()->user()->getPermissions();
        }
    }
    
    protected function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->userPermissions ?? []);
    }
}
```

---

## 📝 **RESUMO DE BOAS PRÁTICAS**

### **✅ FAÇA:**
- Use filtros de grupo para proteções amplas
- Use filtros de permissão para ações específicas
- Combine grupos + permissões para controle granular
- Cache permissões de usuário para performance
- Crie helpers personalizados para verificações comuns
- Documente bem suas permissões e grupos
- Use nomenclatura hierárquica (ex: `users.create`, `admin.settings`)
- Teste todas as rotas protegidas

### **❌ NÃO FAÇA:**
- Aplicar filtros em rotas de login/logout
- Confiar apenas no frontend para segurança
- Criar muitos grupos sem necessidade
- Usar nomes de permissão genéricos demais
- Esquecer de atualizar a matriz de grupos/permissões
- Hardcodar verificações de acesso nos controllers
- Misturar lógica de autorização com lógica de negócio

### **🎯 ESTRUTURA RECOMENDADA:**
```
Grupos Hierárquicos:
├── superadmin (acesso total)
├── admin (acesso administrativo)
├── manager (gestão de equipe)
├── editor (criação de conteúdo)  
├── moderator (moderação)
└── user (usuário básico)

Permissões Modulares:
├── admin.* (administrativas)
├── users.* (gestão de usuários)
├── posts.* (gestão de posts)
├── reports.* (relatórios)
└── api.* (acesso à API)
```

---

**🎉 Com esta documentação você tem o controle total sobre grupos e permissões no CodeIgniter Shield!**

> _Documentação criada para CodeIgniter 4 + Shield - Outubro 2025_