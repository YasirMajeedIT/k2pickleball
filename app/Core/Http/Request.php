<?php

declare(strict_types=1);

namespace App\Core\Http;

/**
 * HTTP Request wrapper.
 * Encapsulates all request data with a clean interface.
 */
final class Request
{
    private string $method;
    private string $uri;
    private string $path;
    private array $query;
    private array $body;
    private array $headers;
    private array $server;
    private array $files;
    private array $cookies;
    private array $attributes;
    private ?string $rawBody;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->uri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->path = $this->parsePath();
        $this->query = $_GET;
        $this->body = $this->parseBody();
        $this->headers = $this->parseHeaders();
        $this->server = $_SERVER;
        $this->files = $_FILES;
        $this->cookies = $_COOKIE;
        $this->attributes = [];
        $this->rawBody = null;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(string $key = '', mixed $default = null): mixed
    {
        if ($key === '') {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }

    public function input(string $key = '', mixed $default = null): mixed
    {
        if ($key === '') {
            return $this->body;
        }
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function only(array $keys): array
    {
        $all = $this->all();
        return array_intersect_key($all, array_flip($keys));
    }

    public function has(string $key): bool
    {
        return isset($this->body[$key]) || isset($this->query[$key]);
    }

    public function header(string $key, ?string $default = null): ?string
    {
        $key = strtolower($key);
        return $this->headers[$key] ?? $default;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function bearerToken(): ?string
    {
        $auth = $this->header('authorization');
        if ($auth !== null && str_starts_with($auth, 'Bearer ')) {
            return substr($auth, 7);
        }
        return null;
    }

    public function ip(): string
    {
        return $this->server['HTTP_X_FORWARDED_FOR']
            ?? $this->server['HTTP_X_REAL_IP']
            ?? $this->server['REMOTE_ADDR']
            ?? '0.0.0.0';
    }

    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    public function host(): string
    {
        $host = $this->server['HTTP_HOST'] ?? 'localhost';
        // Strip port number
        return explode(':', $host)[0];
    }

    public function subdomain(): string
    {
        $host = $this->host();
        $baseDomain = $_ENV['BASE_DOMAIN'] ?? 'k2pickleball.local';

        // Remove base domain to get subdomain
        $baseParts = explode('.', $baseDomain);
        $hostParts = explode('.', $host);

        if (count($hostParts) > count($baseParts)) {
            return $hostParts[0];
        }

        return '';
    }

    public function isJson(): bool
    {
        $contentType = $this->header('content-type', '');
        return str_contains($contentType, 'application/json');
    }

    public function isApi(): bool
    {
        return str_starts_with($this->path, '/api/') || $this->isJson();
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function cookie(string $key, ?string $default = null): ?string
    {
        return $this->cookies[$key] ?? $default;
    }

    // -- Attributes (set by middleware) --

    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function setAttribute(string $key, mixed $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    // -- Convenience: tenant & user from middleware --

    public function tenantId(): ?int
    {
        return $this->attributes['tenant_id'] ?? null;
    }

    public function organizationId(): ?int
    {
        return $this->attributes['organization_id'] ?? null;
    }

    public function userId(): ?int
    {
        return $this->attributes['user_id'] ?? null;
    }

    public function user(): ?array
    {
        return $this->attributes['user'] ?? null;
    }

    public function userRoles(): array
    {
        return $this->attributes['user_roles'] ?? [];
    }

    public function isSuperAdmin(): bool
    {
        $roles = $this->userRoles();
        return in_array('super-admin', $roles, true) || in_array('super_admin', $roles, true);
    }

    public function rawBody(): string
    {
        if ($this->rawBody === null) {
            $this->rawBody = file_get_contents('php://input') ?: '';
        }
        return $this->rawBody;
    }

    // -- Private --

    private function parsePath(): string
    {
        $path = parse_url($this->uri, PHP_URL_PATH) ?: '/';

        // Strip the base path for subdirectory installations (e.g., /k2pickleball/)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = dirname(dirname($scriptName)); // e.g., /k2pickleball
        if ($basePath !== '/' && $basePath !== '\\' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }

        return rtrim($path, '/') ?: '/';
    }

    private function parseBody(): array
    {
        if (in_array($this->method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

            if (str_contains($contentType, 'application/json')) {
                $raw = file_get_contents('php://input');
                $decoded = json_decode($raw ?: '', true);
                return is_array($decoded) ? $decoded : [];
            }

            return $_POST;
        }

        return [];
    }

    private function parseHeaders(): array
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headerKey = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$headerKey] = $value;
            }
        }

        // Content-Type and Content-Length aren't prefixed with HTTP_
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] = $_SERVER['CONTENT_TYPE'];
        }
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $headers['content-length'] = $_SERVER['CONTENT_LENGTH'];
        }

        return $headers;
    }
}
