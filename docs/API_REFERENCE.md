# K2 Pickleball - API Reference

## Base URL

```
http://localhost/k2pickleball/api
```

## Authentication

All API endpoints (except auth) require a Bearer token:

```
Authorization: Bearer {jwt_token}
```

### Obtain Token

```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@k2pickleball.com",
  "password": "K2Admin!2024"
}
```

**Response:**
```json
{
  "data": {
    "access_token": "eyJ...",
    "refresh_token": "abc123...",
    "token_type": "Bearer",
    "expires_in": 1800
  }
}
```

---

## Endpoints

### Auth

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/api/auth/login` | Login | No |
| POST | `/api/auth/register` | Register | No |
| POST | `/api/auth/refresh` | Refresh token | No |
| POST | `/api/auth/logout` | Logout | Yes |
| POST | `/api/auth/forgot-password` | Request password reset | No |
| POST | `/api/auth/reset-password` | Reset password | No |
| POST | `/api/auth/change-password` | Change password | Yes |
| GET | `/api/auth/me` | Get current user | Yes |

### Organizations

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/organizations` | List organizations |
| GET | `/api/organizations/{id}` | Get organization |
| POST | `/api/organizations` | Create organization |
| PUT | `/api/organizations/{id}` | Update organization |
| DELETE | `/api/organizations/{id}` | Delete organization |

### Facilities

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/facilities` | List facilities |
| GET | `/api/facilities/{id}` | Get facility |
| POST | `/api/facilities` | Create facility |
| PUT | `/api/facilities/{id}` | Update facility |
| DELETE | `/api/facilities/{id}` | Delete facility |

**Create/Update Body:**
```json
{
  "name": "Main Center",
  "slug": "main-center",
  "address_line1": "123 Sport Lane",
  "city": "Denver",
  "state": "CO",
  "zip_code": "80202",
  "country": "US",
  "phone": "(303) 555-0100",
  "email": "info@example.com",
  "timezone": "America/Denver",
  "status": "active",
  "operating_hours": {"mon": "06:00-22:00"},
  "amenities": ["parking", "restrooms"]
}
```

### Courts

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/courts` | List courts |
| GET | `/api/courts/{id}` | Get court |
| POST | `/api/courts` | Create court |
| PUT | `/api/courts/{id}` | Update court |
| DELETE | `/api/courts/{id}` | Delete court |

**Create/Update Body:**
```json
{
  "facility_id": 1,
  "name": "Court 1",
  "court_type": "indoor",
  "surface_type": "hard",
  "hourly_rate": 25.00,
  "capacity": 4,
  "status": "available"
}
```

### Users

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/users` | List users |
| GET | `/api/users/{id}` | Get user |
| POST | `/api/users` | Create user |
| PUT | `/api/users/{id}` | Update user |
| DELETE | `/api/users/{id}` | Delete user |
| POST | `/api/users/{id}/roles` | Assign role |
| DELETE | `/api/users/{id}/roles/{roleId}` | Remove role |

### Roles & Permissions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/roles` | List roles |
| GET | `/api/roles/{id}` | Get role with permissions |
| POST | `/api/roles` | Create role |
| PUT | `/api/roles/{id}` | Update role |
| DELETE | `/api/roles/{id}` | Delete role |
| GET | `/api/permissions` | List all permissions |

### Subscriptions

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/subscriptions/plans` | List plans |
| GET | `/api/subscriptions` | Get current subscription |
| POST | `/api/subscriptions` | Subscribe to plan |
| POST | `/api/subscriptions/cancel` | Cancel subscription |
| GET | `/api/subscriptions/invoices` | List invoices |

### Payments

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/payments/charge` | Charge payment |
| POST | `/api/payments/refund` | Refund payment |
| GET | `/api/payments/methods` | List payment methods |
| POST | `/api/payments/methods` | Add payment method |
| DELETE | `/api/payments/methods/{id}` | Remove payment method |
| GET | `/api/payments/transactions` | List transactions |
| POST | `/api/webhooks/square` | Square webhook (no auth) |

**Charge Body:**
```json
{
  "amount": 25.00,
  "currency": "USD",
  "source_id": "cnon:card-nonce-ok",
  "note": "Court reservation"
}
```

### Notifications

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/notifications` | List notifications |
| GET | `/api/notifications/unread-count` | Get unread count |
| GET | `/api/notifications/{id}` | Get notification |
| POST | `/api/notifications/{id}/read` | Mark as read |
| POST | `/api/notifications/read-all` | Mark all as read |
| DELETE | `/api/notifications/{id}` | Delete notification |

### Files

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/files` | List files |
| POST | `/api/files` | Upload file (multipart) |
| DELETE | `/api/files/{id}` | Delete file |
| GET | `/api/files/usage` | Get storage usage |

### API Tokens

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/api-tokens` | List tokens |
| POST | `/api/api-tokens` | Generate token |
| DELETE | `/api/api-tokens/{id}` | Revoke token |
| DELETE | `/api/api-tokens` | Revoke all tokens |

### Audit Logs

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/audit-logs` | List logs (filterable) |
| GET | `/api/audit-logs/{entity}/{id}` | Entity-specific logs |

**Query Parameters:** `user_id`, `action`, `entity_type`, `date_from`, `date_to`, `page`, `per_page`

### Settings

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/settings` | Get all settings |
| GET | `/api/settings/{group}` | Get settings by group |
| GET | `/api/settings/{group}/{key}` | Get single setting |
| PUT | `/api/settings/{group}/{key}` | Set setting value |
| DELETE | `/api/settings/{group}/{key}` | Delete setting |

---

## Response Format

All responses follow this structure:

**Success:**
```json
{
  "data": { ... },
  "message": "Success"
}
```

**Error:**
```json
{
  "error": {
    "message": "Validation failed",
    "code": 422,
    "errors": {
      "email": ["The email field is required"]
    }
  }
}
```

## HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | OK |
| 201 | Created |
| 204 | No Content |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Rate Limited |
| 500 | Server Error |

## Rate Limiting

- API: 60 requests per minute per IP
- Auth: 10 requests per minute per IP
- Headers: `X-RateLimit-Limit`, `X-RateLimit-Remaining`
