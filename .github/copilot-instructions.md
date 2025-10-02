# CodeIgniter 4 + Shield Project - AI Co### Development Workflows

### Docker Environment
- **Multi-stage deployment**: Uses Docker Swarm with Traefik reverse proxy
- **Development setup**: 
  ```bash
  docker-compose up -d
  docker exec -it [container] composer install
  ```
- **Database**: MariaDB 11 with persistent volumes
- **SSL**: Automatic Let's Encrypt certificates via Traefik

### Local Development (Without Docker)
- **SQLite fallback**: Configured to use SQLite3 for local development when MariaDB isn't available
- **Session storage**: Uses file-based sessions for SQLite compatibility (Shield doesn't support SQLite sessions)
- **Local server**: Use `php spark serve` to run development server
- **Environment config**: Copy `.env.example` to `.env` and set `CI_DATABASE_GROUP=local` for SQLite

### Testing & Debuggingstructions

## Project Overview
This is a CodeIgniter 4 application with **modular architecture** using Shield authentication. It features a complete administrative interface with user/group management and Docker Swarm deployment configuration.

## Key Architecture Patterns

### Module Structure
- **Modules directory**: `modules/` contains self-contained feature modules
- **Namespace pattern**: `Modules\[ModuleName]\Controllers\[Controller]`
- **Route isolation**: Each module has its own `Config/Routes.php`
- **Auto-discovery**: Modules are automatically discovered via `app/Config/Modules.php`

**Example module creation**:
```
modules/NewModule/
├── Config/Routes.php          # Module routes with namespace
├── Controllers/[Controller].php # Controllers in module namespace
└── Views/[views]/             # Module-specific views
```

### Authentication & Authorization
- **Shield integration**: Uses CodeIgniter Shield (`codeigniter4/shield`) for authentication
- **Groups system**: Admin roles defined in `app/Config/AuthGroups.php`
- **Session protection**: Admin routes use `filter => 'session'` in route groups
- **Custom auth**: Module `Auth` provides custom login interface overlaying Shield
- **Admin seeder**: `app/Database/Seeds/AdminUserSeeder.php` creates admin users

### Routing Architecture
- **Main routes**: `app/Config/Routes.php` contains all route definitions
- **Nested route groups**: Admin routes use nested grouping pattern:
  ```php
  $routes->group('admin', ['namespace' => 'Modules\Admin\Controllers', 'filter' => 'session'], function($routes) {
      $routes->group('users', function($routes) {
          // Nested user management routes
      });
  });
  ```
- **Module route inclusion**: Routes include module route files conditionally

## Development Workflows

### Docker Environment
- **Multi-stage deployment**: Uses Docker Swarm with Traefik reverse proxy
- **Development setup**: 
  ```bash
  docker-compose up -d
  docker exec -it [container] composer install
  ```
- **Database**: MariaDB 11 with persistent volumes
- **SSL**: Automatic Let's Encrypt certificates via Traefik

### Testing & Debugging
- **Test routes**: Temporary test routes in `/test/*` for debugging
- **PHPUnit**: Configured with `phpunit.xml.dist`
- **Debug tools**: Test controller (`app/Controllers/Test.php`) with database and auth debugging

### Administrative Interface Patterns
- **Bootstrap 5.3**: UI framework for all admin views
- **AJAX toggles**: Status changes use AJAX for better UX
- **Protected operations**: Users cannot edit/delete their own accounts
- **Session regeneration**: Security pattern after login in `AuthController::doLogin()`

## Critical File Patterns

### Controllers
- **Base inheritance**: All controllers extend `App\Controllers\BaseController`
- **Authentication checks**: Manual auth verification in admin controllers:
  ```php
  if (!auth()->loggedIn()) {
      return redirect()->to('/login');
  }
  ```
- **Shield provider usage**: Access user data via `auth()->getProvider()`

### Views
- **Module namespacing**: Views use full namespace paths:
  ```php
  return view('Modules\\Admin\\Views\\dashboard', $data);
  ```
- **Shared layouts**: Admin interface uses consistent Bootstrap layouts

### Configuration
- **Environment-driven**: Admin credentials configurable via `.env` variables
- **Shield customization**: `AuthGroups.php` extends Shield's base configuration
- **Module discovery**: Auto-discovery enabled for both local and Composer modules

## Deployment Specifics
- **Docker Swarm**: Production uses replicated services with rolling updates
- **Traefik labels**: Service discovery and SSL termination via labels
- **Document root**: Apache configured for `public/` directory as web root
- **PHP 8.3**: Uses latest PHP with required extensions (gd, mysqli, pdo_mysql, zip, intl)

## Testing Commands
```bash
# Database operations (SQLite local)
php spark migrate
php spark db:seed AdminUserSeeder

# Database operations (Docker MariaDB)
docker exec -it [container] php spark migrate
docker exec -it [container] php spark db:seed AdminUserSeeder

# Run tests
./vendor/bin/phpunit

# Development server
php spark serve  # Usually runs on localhost:8081

# Check database tables
php spark db:table
```

## Common Issues & Solutions
- **MySQLi connection errors**: When running outside Docker, switch to SQLite by setting `CI_DATABASE_GROUP=local` in `.env`
- **Session database handler errors**: Use `session.driver = 'CodeIgniter\Session\Handlers\FileHandler'` for SQLite compatibility
- **Port conflicts**: Development server may run on port 8081 instead of 8080 if port is occupied
- **Missing tables**: Run migrations first with `php spark migrate` before accessing admin interface

## When Working on This Project
1. **New features**: Create in appropriate module or create new module following namespace pattern
2. **Authentication**: Always verify login state in admin controllers, use Shield's auth helpers
3. **Routes**: Add routes to appropriate group in `app/Config/Routes.php` with proper filters
4. **Database**: Use Shield's user provider for user operations, not direct model access
5. **Views**: Follow module namespace pattern for view paths, use Bootstrap 5.3 components
6. **Docker**: Test changes in Docker environment to match production deployment