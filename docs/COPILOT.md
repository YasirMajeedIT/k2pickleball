# K2 Pickleball — Copilot Reference

> This file is the authoritative quick-reference for the GitHub Copilot agent.
> Read this before making any change. Keep it up-to-date as the project evolves.

---

## 1. Project Identity

| Key | Value |
|-----|-------|
| Name | K2 Pickleball |
| Stack | PHP 8.3 (no framework), MySQL 8.0+, Alpine.js 3, Tailwind CSS CDN, Chart.js |
| Auth | JWT (firebase/php-jwt) — HS256, 30-min access token, 30-day refresh token |
| Payments | Square SDK (sandbox environment locally) |
| Server | Apache + mod_rewrite on XAMPP (`C:\xampp_new`) |
| Root | `C:\xampp_new\htdocs\k2pickleball` |

---

## 2. Environment

### `.env` Keys (never hardcode these values)

```env
APP_NAME="K2 Pickleball"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/k2pickleball
APP_TIMEZONE=America/New_York

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=k2pickleball      ← correct key (NOT DB_DATABASE)
DB_USER=root              ← correct key (NOT DB_USERNAME)
DB_PASS=                  ← correct key (NOT DB_PASSWORD)
DB_CHARSET=utf8mb4

JWT_SECRET=<secret>
JWT_ALGO=HS256
JWT_ACCESS_TTL=1800
JWT_REFRESH_TTL=2592000

SQUARE_ENVIRONMENT=sandbox
SQUARE_ACCESS_TOKEN=...
SQUARE_APPLICATION_ID=...
SQUARE_LOCATION_ID=...

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=oldsmar@tbpickleball.com
MAIL_ENCRYPTION=tls

PLATFORM_DOMAIN=platform.k2pickleball.local
ADMIN_DOMAIN=admin.k2pickleball.local
API_DOMAIN=api.k2pickleball.local
BASE_DOMAIN=k2pickleball.local
```

### Standard DB Connection Pattern (all scripts must use this)

```php
require_once __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/database.php';
$pdo = new PDO(
    "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset={$config['charset']}",
    $config['user'], $config['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
```

Never use hardcoded `new PDO('mysql:host=localhost;dbname=k2pickleball', 'root', '')`.  
Never use `$_ENV['DB_DATABASE']`, `$_ENV['DB_USERNAME']`, or `$_ENV['DB_PASSWORD']` — those keys don't exist.

---

## 3. Directory Structure

```
k2pickleball/
├── app/
│   ├── Core/                    # Framework kernel (no edits unless core bug)
│   │   ├── Auth/                # JwtService, AuthService
│   │   ├── Database/            # Connection (PDO singleton), QueryBuilder, Repository
│   │   ├── Http/                # Request, Response, Router, Controller base
│   │   ├── Middleware/          # Auth, CORS, RateLimit, SecurityHeaders, TenantResolver
│   │   ├── Security/            # Validator, Sanitizer
│   │   ├── Services/            # Config, Container (DI)
│   │   └── Application.php      # Bootstrap
│   ├── Modules/                 # Business logic — one folder per domain
│   │   ├── Admin/               # Org-admin panel controller
│   │   ├── ApiTokens/
│   │   ├── AuditLogs/
│   │   ├── Auth/
│   │   ├── Calendar/
│   │   ├── Categories/          # Session/activity categories (has system-category logic)
│   │   ├── Client/              # Marketing site + customer portal
│   │   ├── Courts/              # Court CRUD + availability + booking
│   │   ├── CreditCodes/
│   │   ├── Discounts/
│   │   ├── Extensions/          # Org-level feature flags
│   │   ├── Facilities/          # Facility CRUD + hours + amenities
│   │   ├── Files/
│   │   ├── GiftCertificates/
│   │   ├── Labels/
│   │   ├── Notifications/
│   │   ├── Organizations/       # Multi-tenant org management
│   │   ├── Payments/            # Square integration
│   │   ├── Platform/            # Super-admin panel
│   │   ├── Players/
│   │   ├── Resources/
│   │   ├── Roles/               # RBAC — 7 default roles, 34 permissions
│   │   ├── SessionDetails/
│   │   ├── SessionTypes/
│   │   ├── Settings/
│   │   ├── Subscriptions/
│   │   ├── Tenant/              # Tenant public-facing pages controller
│   │   ├── Users/
│   │   └── Waivers/
│   ├── Views/
│   │   ├── layouts/
│   │   │   ├── admin.php        # Org-admin layout
│   │   │   ├── platform.php     # Super-admin layout
│   │   │   ├── client.php       # Marketing site layout
│   │   │   ├── portal.php       # Customer portal layout
│   │   │   ├── tenant.php       # Tenant public pages layout  ← most active
│   │   │   └── tenant-dashboard.php  # Tenant logged-in dashboard
│   │   ├── admin/               # Org-admin pages
│   │   ├── platform/            # Super-admin pages
│   │   ├── client/              # Marketing + portal pages
│   │   ├── tenant/              # Tenant public pages (home, schedule, book-court…)
│   │   ├── emails/              # Email templates
│   │   └── components/          # Shared UI components
│   └── routes.php               # Master route loader — ALL routes registered here
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── cors.php
│   ├── database.php             # Reads from $_ENV, returns array
│   ├── payments.php
│   └── permissions.php
├── database/
│   ├── schema.sql               # Base schema (all tables)
│   ├── migrate.php              # Migration runner (applies all .sql files)
│   ├── seed.php                 # Dev seed data
│   └── migrations/              # Incremental schema changes (001–025+)
├── docs/
│   ├── COPILOT.md               # ← YOU ARE HERE
│   ├── ARCHITECTURE.md
│   ├── API_REFERENCE.md
│   ├── DEPLOYMENT.md
│   └── SECURITY.md
├── public/
│   ├── index.php                # Front controller
│   └── assets/                  # CSS, JS, images
├── scripts/
│   └── run_migration_*.php      # One-off migration runner scripts
├── storage/
│   ├── logs/
│   ├── cache/
│   └── uploads/
├── tests/
├── vendor/                      # Composer dependencies
├── composer.json
└── .env                         # Local environment — never commit secrets
```

---

## 4. Request Lifecycle

```
Browser → Apache mod_rewrite
  → public/index.php
    → Application::boot()  (load .env, config, DI container, routes)
      → MiddlewarePipeline:
          CorsMiddleware
          → SecurityHeadersMiddleware
            → RateLimitMiddleware
              → TenantResolver  (reads subdomain → sets org_id on request)
                → AuthMiddleware (validates JWT Bearer token)
                  → Router::match()  (regex route matching)
                    → Controller method
                      → Repository (QueryBuilder + PDO)
  → Response::send()  (JSON or HTML)
```

---

## 5. Multi-Tenancy

- Architecture: **shared database, row-level isolation** via `organization_id`
- `TenantResolver` middleware reads subdomain → looks up `organizations.slug`
- `Repository::forTenant($orgId)` auto-appends `WHERE organization_id = ?`
- Layouts inject `window.ORG` (JS object) with org data available to Alpine components

**Subdomain routing:**

| Subdomain pattern | Panel |
|-------------------|-------|
| `platform.k2pickleball.local` | Super-admin (Platform) |
| `admin.k2pickleball.local` | Org-admin login |
| `api.k2pickleball.local` | REST API |
| `{slug}.k2pickleball.local` | Tenant public & logged-in pages |

---

## 6. Live Database State (as of 2026-03-19)

### Organizations

| id | name | slug | status |
|----|------|------|--------|
| 1 | Demo Sports Club | `demo-sports-club` | active |

> Note: Org #4 (`oldsmar`) was an accidentally created duplicate — it has been identified but cleanup is pending. The real "Oldsmar" location is **Facility #1** under Org #1.

### Facilities

| id | org_id | name |
|----|--------|------|
| 1 | 1 | Tampa Oldsmar |
| 10 | 1 | Savannah Pickleball Academy |

### Courts

| id | facility_id | name |
|----|-------------|------|
| 1–6 | 1 | Court 1 – Court 6 |
| 10–16 | 10 | Court 1 – Court 7 |

---

## 7. API Response Envelope

**Always** check `json.status === 'success'` in JavaScript — NOT `json.success` (that field doesn't exist).

```json
{
  "status": "success",
  "message": "Courts retrieved",
  "data": { ... }
}
```

Error response:
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": { ... }
}
```

---

## 8. Frontend Conventions

- **Alpine.js 3** — all interactivity via `x-data`, `x-bind`, `@click`, etc.
- **Tailwind CSS** — loaded from CDN, no build step
- **`window.ORG`** — injected by `tenant.php` layout, contains org data + `facilities[]`
- **`window.baseApi`** — set in `tenant.php` layout as `window.baseApi = baseApi`. Use this for all API calls from Alpine components. Never reconstruct the base URL manually.
- **`window.APP_BASE`** — the raw PHP-injected base path (e.g. `/k2pickleball`)

```javascript
// Correct way to call the API from Alpine components:
const res = await fetch(`${window.baseApi}/api/courts/availability?...`);
const json = await res.json();
if (json.status === 'success') { ... }
```

---

## 9. Categories — System Category Rules

The `categories` table has system-managed rows:

| Column | Purpose |
|--------|---------|
| `is_system` | `1` = system-owned, cannot be deleted |
| `system_slug` | Unique system identifier (e.g. `book-a-court`) |
| `is_active` | Toggle display in tenant nav |
| `description` | Optional description |
| `image_url` | Optional image |

- **Book a Court** (`system_slug = 'book-a-court'`) — seeded for every org on creation via `OrganizationController::seedSystemCategories()`
- Deleting a system category returns HTTP 403
- System fields are stripped on update (slug, is_system, system_slug cannot be changed via API)

---

## 10. Migration Conventions

### SQL Migration Files — `database/migrations/`

- Named `NNN_description.sql` (zero-padded 3 digits)
- Pure SQL only — no PHP, no hardcoded DB names
- Applied in order by `database/migrate.php`
- Latest: **025** (add system categories columns)

### Runner Scripts — `scripts/run_migration_NNN.php`

- All scripts **must** use `config/database.php` (see Section 2 for the pattern)
- Scripts 010–025 have been verified and fixed
- Do NOT use hardcoded credentials or wrong env key names in new scripts

---

## 11. Key Files Quick Reference

| File | Purpose |
|------|---------|
| `app/routes.php` | All route definitions — add new routes here |
| `app/Views/layouts/tenant.php` | Tenant public layout — contains `window.baseApi`, `window.ORG`, nav logic |
| `app/Views/tenant/book-court.php` | 4-step court booking page (Alpine.js) |
| `config/database.php` | DB config — reads from `$_ENV` |
| `config/permissions.php` | All 34 permission definitions |
| `database/schema.sql` | Full base schema |
| `public/index.php` | Front controller |
| `app/Core/Application.php` | App bootstrap |
| `app/Core/Http/Router.php` | Regex router |
| `app/Core/Middleware/TenantResolver.php` | Subdomain → org resolution |

---

## 12. Coding Rules

1. **No framework** — no Laravel, Symfony, etc. Extend `app/Core` only when necessary.
2. **PDO prepared statements only** — no raw string interpolation into SQL queries.
3. **Tenant scope everything** — any query touching tenant data must include `organization_id`.
4. **Config over hardcode** — use `config/database.php`, `config/app.php`, `.env` keys.
5. **PHP 8.3 syntax** — use typed properties, match expressions, named arguments, enums where appropriate.
6. **Alpine.js for interactivity** — no jQuery, no custom vanilla JS build system.
7. **API responses** — always return `{status: "success"|"error", message: "...", data: ...}`.
8. **Migrations are append-only** — never modify an existing `.sql` migration file; create a new one.
9. **Runner scripts** — always use `config/database.php` pattern, never hardcode credentials.
10. **System categories** — never delete `is_system = 1` rows; protect at controller level.

---

## 13. Pending / Known Issues

| Issue | Status | Notes |
|-------|--------|-------|
| Org #4 `oldsmar` duplicate org cleanup | ⏳ Pending | Needs decision: delete or repurpose. Real Oldsmar = Facility #1, Org #1 |
| Savannah org setup | ⏳ Pending | Org #2 (`savannahpickleball`) has no facilities yet |

---

## 14. Changelog (Copilot session history)

| Date | Change |
|------|--------|
| 2026-03-20 | Added Google Sign-In button to tenant login page (`tenant/auth/login.php`) |
| 2026-03-20 | Added Google Sign-In button to tenant register page (`tenant/auth/register.php`) |
| 2026-03-20 | Fixed `GoogleAuthController` — accepts `organization_id`, assigns `player` role for tenant sign-ups |
| 2026-03-20 | Fixed `json.success` → `json.status === 'success'` in tenant register page |
| 2026-03-20 | Fixed schedule SQL: `cls.start_time` → `cls.scheduled_at`, `cls.max_participants` → `cls.slots`, etc. |
| 2026-03-19 | Fixed `window.baseApi` undefined — added `window.baseApi = baseApi` in `tenant.php` |
| 2026-03-19 | Fixed `json.success` → `json.status === 'success'` in `book-court.php` |
| 2026-03-19 | Fixed facility auto-select in `book-court.php` (was reading `localStorage` only) |
| 2026-03-19 | Added multi-facility picker + no-facilities empty state in `book-court.php` |
| 2026-03-19 | Added `courtHasSelectableSlots()` + `getAvailableDurations()` per-court duration hints |
| 2026-03-19 | Standardized all 9 migration runner scripts (010–023) to use `config/database.php` |
| 2026-03-19 | Fixed syntax errors in 010, 013, 014, 021, 022 from previous partial replacements |
| Prior | Book-a-Court system category: DB migration 025, seeding, delete protection, public API |
| Prior | Fixed PHP parse error in `tenant.php` — nested PHP tags in `$navItems` array |
