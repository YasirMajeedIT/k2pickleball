# K2 Pickleball - Security Documentation

## Authentication & Authorization

### JWT Tokens
- **Algorithm**: HS256
- **Access Token TTL**: 30 minutes
- **Refresh Token TTL**: 30 days
- **Token Rotation**: Refresh tokens are single-use; a new pair is issued on refresh
- **Storage**: Refresh tokens are SHA256 hashed before database storage

### Password Policy
- **Hashing**: bcrypt with cost factor 12
- **Minimum Length**: 8 characters
- **Account Locking**: 5 failed attempts triggers 15-minute lockout
- **Reset Tokens**: Random 64-byte hex, SHA256 hashed, 1-hour expiry

### API Token Authentication
- Tokens are random 64-byte hex strings
- Only SHA256 hash is stored in database
- Plain token shown only once at creation
- Supports abilities-based access control
- Tracks last usage timestamp

## Input Security

### SQL Injection Prevention
All database queries use PDO prepared statements with parameterized queries. The QueryBuilder enforces this:
```php
// All values are bound as parameters, never interpolated
$builder->where('email', '=', $email)->get();
```

### XSS Prevention
- All view output uses `htmlspecialchars($value, ENT_QUOTES)`
- Content-Security-Policy header restricts inline scripts
- API responses are JSON with `Content-Type: application/json`

### Input Validation
The `Validator` class provides 25+ rules:
- Type checking: `string`, `integer`, `float`, `boolean`, `array`
- Format validation: `email`, `url`, `uuid`, `date`, `phone`, `slug`
- Constraints: `min`, `max`, `between`, `in`, `regex`
- File validation: `file`, `mimes`, `max_filesize`
- Security: `password` (min length + complexity)

### Input Sanitization
The `Sanitizer` class cleans input:
- `string`: Strip tags, trim whitespace
- `email`: Sanitize email format
- `html`: Strip dangerous tags, allow safe subset
- `url`: Validate URL format
- `filename`: Remove path traversal characters
- `slug`: Lowercase alphanumeric with hyphens
- `integer/float`: Cast to numeric types

## HTTP Security Headers

Applied via `SecurityHeadersMiddleware`:

| Header | Value |
|--------|-------|
| Content-Security-Policy | `default-src 'self'; script-src 'self' 'unsafe-inline' cdn.tailwindcss.com cdn.jsdelivr.net cdn.jsdelivr.net/npm/chart.js; style-src 'self' 'unsafe-inline'` |
| X-Content-Type-Options | `nosniff` |
| X-Frame-Options | `DENY` |
| X-XSS-Protection | `1; mode=block` |
| Strict-Transport-Security | `max-age=31536000; includeSubDomains` |
| Referrer-Policy | `strict-origin-when-cross-origin` |
| Permissions-Policy | `camera=(), microphone=(), geolocation=()` |

## Rate Limiting

- **Token Bucket Algorithm**: Database-backed
- **API Limit**: 60 requests/minute per IP
- **Auth Limit**: 10 requests/minute per IP  
- **Headers**: `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `X-RateLimit-Reset`
- **Exceeded**: Returns `429 Too Many Requests`

## CORS Configuration

Configured in `config/cors.php`:
- Allowed origins: Configurable per environment
- Allowed methods: GET, POST, PUT, PATCH, DELETE, OPTIONS
- Allowed headers: Content-Type, Authorization, X-Requested-With, Accept
- Credentials: Supported
- Max age: 86400 seconds (24 hours)
- Preflight: OPTIONS requests handled automatically

## Multi-Tenant Isolation

- Organization data isolated via `organization_id` foreign key
- `TenantResolver` middleware sets org context from subdomain
- `Repository` base class auto-scopes queries to current tenant
- Cross-tenant data access prevented at the query layer

## File Upload Security

- **MIME Validation**: `finfo_file()` for true MIME type detection
- **Allowed Types**: Configurable whitelist (images, documents, PDFs)
- **Size Limit**: 10MB default (configurable)
- **Storage**: Files stored outside web root in `storage/uploads/`
- **Naming**: Files renamed with random hash to prevent path traversal

## Payment Security (Square)

- **PCI Compliance**: Card data never touches our server (Square handles)
- **Webhook Verification**: HMAC-SHA256 signature validation
- **Idempotency**: Unique idempotency keys for payment requests
- **Sandbox Mode**: Development uses Square sandbox environment

## Audit Trail

All sensitive operations are logged to `activity_logs` table:
- User ID, action, entity type/ID
- Old and new values (JSON)
- IP address, user agent
- Timestamp

## Error Handling

- **Development**: Full stack traces, file paths, query details
- **Production**: Generic error messages, no internal details exposed
- **Logging**: Errors logged to `storage/logs/` (not exposed)
