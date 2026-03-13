# K2 Pickleball - Architecture Guide

## Overview

K2 Pickleball is a multi-tenant SaaS platform built with pure PHP 8.3 (no frameworks) and MySQL 8.0+. It follows an API-first architecture with a TailAdmin-inspired admin dashboard.

## Directory Structure

```
k2pickleball/
├── app/
│   ├── Core/                    # Framework core
│   │   ├── Auth/                # JWT & auth services
│   │   │   ├── JwtService.php   # JWT generation/validation
│   │   │   └── AuthService.php  # Login, register, password management
│   │   ├── Database/            # Database layer
│   │   │   ├── Connection.php   # PDO wrapper (singleton)
│   │   │   ├── QueryBuilder.php # Fluent query builder
│   │   │   ├── Repository.php   # Abstract base repository
│   │   │   └── Migration.php    # Migration runner
│   │   ├── Exceptions/          # Exception hierarchy
│   │   │   ├── Handler.php      # Global exception handler
│   │   │   ├── HttpException.php
│   │   │   ├── ValidationException.php
│   │   │   └── ...
│   │   ├── Http/                # HTTP foundation
│   │   │   ├── Request.php      # HTTP request wrapper
│   │   │   ├── Response.php     # JSON/HTML response builder
│   │   │   ├── Router.php       # Regex-based router
│   │   │   ├── Controller.php   # Base controller
│   │   │   ├── MiddlewarePipeline.php
│   │   │   └── MiddlewareInterface.php
│   │   ├── Middleware/          # HTTP middleware
│   │   │   ├── AuthMiddleware.php
│   │   │   ├── CorsMiddleware.php
│   │   │   ├── RateLimitMiddleware.php
│   │   │   ├── SecurityHeadersMiddleware.php
│   │   │   └── TenantResolver.php
│   │   ├── Security/            # Validation & sanitization
│   │   │   ├── Validator.php
│   │   │   └── Sanitizer.php
│   │   ├── Services/            # Core services
│   │   │   ├── Config.php       # Configuration accessor
│   │   │   └── Container.php    # DI container
│   │   └── Application.php      # Application bootstrap
│   ├── Modules/                 # Business modules
│   │   ├── Auth/                # Authentication
│   │   ├── Organizations/       # Multi-tenant organizations
│   │   ├── Facilities/          # Sports facilities
│   │   ├── Courts/              # Court management
│   │   ├── Users/               # User management
│   │   ├── Roles/               # Roles & permissions
│   │   ├── Subscriptions/       # Plans & subscriptions
│   │   ├── Payments/            # Square payment integration
│   │   ├── Notifications/       # In-app notifications
│   │   ├── Files/               # File uploads
│   │   ├── ApiTokens/           # API token management
│   │   ├── AuditLogs/           # Audit trail
│   │   ├── Settings/            # Key-value settings
│   │   ├── Admin/               # Admin panel controller
│   │   └── Platform/            # Super admin panel
│   ├── Views/                   # PHP templates
│   │   ├── layouts/             # Layout templates
│   │   ├── components/          # Reusable UI components
│   │   ├── admin/               # Org admin pages
│   │   └── platform/            # Super admin pages
│   └── routes.php               # Master route loader
├── config/                      # Configuration files
├── database/                    # Schema, migrations, seeds
├── docs/                        # Documentation
├── public/                      # Web root
│   ├── index.php                # Front controller
│   └── .htaccess                # URL rewriting
├── storage/                     # File uploads, logs, cache
├── composer.json
├── .env
└── .htaccess
```

## Architecture Patterns

### Request Lifecycle

```
Client → Apache (.htaccess rewrite)
  → public/index.php (front controller)
    → Application::boot() (load config, container, routes)
      → MiddlewarePipeline (CORS → SecurityHeaders → RateLimit → TenantResolver → Auth)
        → Router::match() (regex pattern matching)
          → Controller::method() (business logic)
            → Repository (database queries)
              → QueryBuilder (fluent SQL with tenant scoping)
                → Connection (PDO with prepared statements)
    → Response::send() (JSON or HTML output)
```

### Multi-Tenancy

The platform uses **shared database with row-level isolation**:

1. `TenantResolver` middleware reads the subdomain to determine the organization
2. Sets `organization_id` on the request context
3. `Repository.forTenant()` auto-adds `WHERE organization_id = ?` to all queries
4. `QueryBuilder` supports explicit `forTenant($orgId)` method

**Subdomain routing:**
- `platform.*` → Super admin panel
- `admin.*` → Org admin login
- `api.*` → API endpoints
- `{slug}.*` → Tenant-specific routes

### Authentication

- **JWT Access Tokens**: HS256, 30-minute expiry, contains `user_id`, `org_id`, `roles`
- **Refresh Tokens**: SHA256 hashed, 30-day expiry, rotation on use
- **API Tokens**: Random 64-byte hex, SHA256 hashed storage, abilities-based

### Module Structure

Each module contains:
```
ModuleName/
├── ModuleNameRepository.php    # Data access (extends Repository)
├── ModuleNameController.php    # HTTP handlers (extends Controller)
└── routes.php                  # Route definitions (returns callable)
```

### Query Builder

Fluent interface with automatic tenant scoping:
```php
$results = $this->query()
    ->select(['id', 'name', 'status'])
    ->where('status', '=', 'active')
    ->orderBy('created_at', 'DESC')
    ->limit(20)
    ->offset(0)
    ->get();
```

### Dependency Injection

The Container supports:
- **Singletons**: `Container::singleton('db', fn() => new Connection())`
- **Factories**: `Container::bind('request', fn() => new Request())`
- **Auto-resolution**: `Container::make(UserController::class)` resolves constructor deps

### Error Handling

All exceptions extend `HttpException` and are caught by the global `Handler`:
- Development: Full stack traces with file/line info
- Production: Clean JSON error responses with appropriate HTTP codes

## Security Measures

1. **SQL Injection**: PDO prepared statements exclusively
2. **XSS**: `htmlspecialchars()` in all view output, CSP headers
3. **CSRF**: API-first with JWT tokens (stateless)
4. **Rate Limiting**: Token bucket algorithm, DB-backed
5. **Account Locking**: 5 failed login attempts → 15 min lockout
6. **Password Security**: bcrypt cost 12, min 8 chars
7. **Security Headers**: CSP, X-Frame-Options, X-Content-Type-Options, HSTS
8. **Input Validation**: 25+ validation rules, separate sanitizer

## Database Design

- **25 tables** with proper foreign keys and indexes
- **Soft deletes** via `deleted_at` column
- **UUID** columns for external references
- **JSON** columns for flexible data (operating_hours, amenities, features, settings)
- **Timestamps** on all tables (created_at, updated_at)
