# K2 Pickleball

A production-ready **multi-tenant SaaS platform** for sports facility and club management, built with pure **PHP 8.3** and **MySQL 8.0+** — no frameworks.

## Features

### Core Platform
- **Multi-Tenant Architecture** — Subdomain-based organization isolation with shared database
- **API-First Design** — RESTful JSON API with JWT authentication
- **Role-Based Access Control** — 7 default roles, 34 permissions, custom role creation
- **Admin Dashboard** — TailAdmin-inspired responsive UI with Tailwind CSS + Alpine.js

### Business Modules
- **Organization Management** — Multi-org support with owner assignment and settings
- **Facility Management** — Addresses, operating hours, amenities, timezone support
- **Court Management** — Indoor/outdoor/covered types, surface types, hourly rates
- **User Management** — Registration, profile management, role assignment
- **Subscription Plans** — Free/Professional/Enterprise tiers with billing cycles
- **Payment Processing** — Square SDK integration (charges, refunds, webhooks)
- **Notifications** — In-app notification system with read/unread tracking
- **File Manager** — Upload with MIME validation, organized collections
- **API Tokens** — Generate tokens with scoped abilities for integrations
- **Audit Logs** — Full activity trail with old/new value tracking
- **Settings** — Key-value settings with type casting and grouping

### Admin Panels
- **Organization Admin** — Full CRUD for all modules, charts, data tables
- **Platform Super Admin** — Cross-organization management, plans, revenue, extensions, announcements, invoices, user impersonation

## Tech Stack

| Component | Technology |
|-----------|-----------|
| Backend | PHP 8.3 (pure, no frameworks) |
| Database | MySQL 8.0+ |
| Auth | JWT (firebase/php-jwt) |
| Payments | Square SDK |
| Frontend | Tailwind CSS CDN + Alpine.js + Chart.js |
| Server | Apache + mod_rewrite |

## Quick Start

### Prerequisites
- PHP 8.3+ with extensions: `pdo_mysql`, `mbstring`, `json`, `openssl`, `fileinfo`, `curl`
- MySQL 8.0+
- Composer 2.x
- Apache with `mod_rewrite`

### Installation

```bash
# 1. Clone the repository
cd C:\xampp_new\htdocs
git clone <repo-url> k2pickleball
cd k2pickleball

# 2. Install dependencies
composer install

# 3. Configure environment
copy .env.example .env
# Edit .env with your database credentials and JWT secret

# 4. Create database and tables
php database/migrate.php

# 5. Seed with demo data
php database/seed.php
```

### Access the Application

| Panel | URL | Credentials |
|-------|-----|-------------|
| Admin Login | http://localhost/k2pickleball/admin/login | admin@k2pickleball.com / K2Admin!2024 |
| Platform Admin | http://localhost/k2pickleball/platform | Same credentials |
| API Health | http://localhost/k2pickleball/api/health | — |

## Project Structure

```
k2pickleball/
├── app/
│   ├── Core/                  # Framework (Router, DB, Auth, Security, Middleware)
│   ├── Modules/               # Business modules (15 modules)
│   │   ├── Auth/              # Login, register, password management
│   │   ├── Organizations/     # Multi-tenant orgs
│   │   ├── Facilities/        # Sports facilities
│   │   ├── Courts/            # Court management
│   │   ├── Users/             # User management
│   │   ├── Roles/             # Roles & permissions
│   │   ├── Subscriptions/     # Plans & billing
│   │   ├── Payments/          # Square integration
│   │   ├── Notifications/     # In-app notifications
│   │   ├── Files/             # File uploads
│   │   ├── ApiTokens/         # API token management
│   │   ├── AuditLogs/         # Audit trail
│   │   ├── Settings/          # Key-value settings
│   │   ├── Admin/             # Admin panel controller
│   │   └── Platform/          # Super admin (extensions, announcements, impersonation)
│   └── Views/                 # Admin/Platform UI templates
├── config/                    # App, DB, Auth, CORS, Payments, Permissions
├── database/
│   ├── schema.sql             # 28 tables
│   ├── migrate.php            # Migration runner
│   └── seed.php               # Demo data seeder
├── docs/                      # API Reference, Architecture, Deployment, Security
├── public/                    # Web root (index.php + .htaccess)
└── storage/                   # Uploads, logs, cache
```

## API Usage

```bash
# Login
curl -X POST http://localhost/k2pickleball/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@k2pickleball.com","password":"K2Admin!2024"}'

# List facilities (with token)
curl http://localhost/k2pickleball/api/facilities \
  -H "Authorization: Bearer eyJ..."

# Create a court
curl -X POST http://localhost/k2pickleball/api/courts \
  -H "Authorization: Bearer eyJ..." \
  -H "Content-Type: application/json" \
  -d '{"facility_id":1,"name":"Court 7","court_type":"outdoor","surface_type":"concrete","hourly_rate":20,"status":"available"}'
```

See [docs/API_REFERENCE.md](docs/API_REFERENCE.md) for complete API documentation.

## Documentation

- [API Reference](docs/API_REFERENCE.md) — Complete endpoint documentation
- [Architecture Guide](docs/ARCHITECTURE.md) — System design and patterns
- [Deployment Guide](docs/DEPLOYMENT.md) — Local and production setup
- [Security Guide](docs/SECURITY.md) — Security measures and policies

## Database

28 tables covering:
- `organizations`, `organization_domains` — Multi-tenant orgs
- `facilities`, `courts` — Facility management
- `users`, `roles`, `permissions`, `role_permissions`, `user_roles` — RBAC
- `players`, `staff` — User profiles
- `plans`, `subscriptions`, `invoices` — Billing
- `payment_methods`, `payments`, `transactions` — Payment processing
- `notifications`, `files`, `activity_logs` — System services
- `api_tokens`, `settings` — Configuration
- `extensions`, `organization_extensions` — Extension marketplace & org installs
- `announcements` — Platform-wide announcements
- `refresh_tokens`, `password_resets`, `rate_limits` — Auth infrastructure

## Security

- JWT with token rotation
- Bcrypt password hashing (cost 12)
- PDO prepared statements (no SQL injection)
- CORS, CSP, HSTS security headers
- Rate limiting (token bucket)
- Account lockout after 5 failed attempts
- MIME-validated file uploads
- Square webhook signature verification
- Full audit trail

## License

Proprietary - All rights reserved.
