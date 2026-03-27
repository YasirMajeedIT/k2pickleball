<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Database\Connection;
use App\Core\Exceptions\RateLimitException;
use App\Core\Http\MiddlewareInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Services\Config;

/**
 * Rate limiting middleware.
 * Uses database-backed rate limiting with token bucket algorithm.
 */
final class RateLimitMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $key = $this->resolveKey($request);
        $limit = $this->resolveLimit($request);
        $window = (int) ($_ENV['RATE_LIMIT_WINDOW'] ?? 60);

        $remaining = $this->checkRateLimit($key, $limit, $window);

        if ($remaining < 0) {
            throw new RateLimitException('Too many requests. Please try again later.');
        }

        /** @var Response $response */
        $response = $next($request);

        return $response
            ->header('X-RateLimit-Limit', (string) $limit)
            ->header('X-RateLimit-Remaining', (string) max(0, $remaining))
            ->header('X-RateLimit-Reset', (string) (time() + $window));
    }

    private function resolveKey(Request $request): string
    {
        $ip = $request->ip();
        $path = $request->path();
        return "rate_limit:{$ip}:{$path}";
    }

    private function resolveLimit(Request $request): int
    {
        $path = $request->path();
        $method = strtoupper($request->method());

        // Public form submissions — very strict (3 per window)
        if ($method === 'POST' && in_array($path, ['/api/contact', '/api/consultations'], true)) {
            return (int) ($_ENV['RATE_LIMIT_FORMS'] ?? 3);
        }

        // Read-only auth endpoints — use default (generous) limit
        if ($path === '/api/auth/me' || $path === '/api/auth/refresh') {
            return (int) ($_ENV['RATE_LIMIT_DEFAULT'] ?? 60);
        }

        // Sensitive write auth endpoints — stricter limit
        if (str_starts_with($path, '/api/auth/')) {
            return (int) ($_ENV['RATE_LIMIT_AUTH'] ?? 10);
        }

        return (int) ($_ENV['RATE_LIMIT_DEFAULT'] ?? 60);
    }

    private function checkRateLimit(string $key, int $limit, int $window): int
    {
        $db = Connection::getInstance();

        if ($db === null) {
            // No database available, skip rate limiting
            return $limit;
        }

        try {
            $now = date('Y-m-d H:i:s');
            $resetAt = date('Y-m-d H:i:s', time() + $window);

            // Clean expired entries
            $db->query(
                "DELETE FROM `rate_limits` WHERE `reset_at` < ?",
                [$now]
            );

            // Atomic upsert — avoids race condition with concurrent requests
            $db->query(
                "INSERT INTO `rate_limits` (`key_name`, `hits`, `reset_at`)
                 VALUES (?, 1, ?)
                 ON DUPLICATE KEY UPDATE
                    `hits` = IF(`reset_at` < ?, 1, `hits` + 1),
                    `reset_at` = IF(`reset_at` < ?, ?, `reset_at`)",
                [$key, $resetAt, $now, $now, $resetAt]
            );

            // Read back current hits
            $record = $db->fetch(
                "SELECT `hits` FROM `rate_limits` WHERE `key_name` = ?",
                [$key]
            );

            $hits = (int) ($record['hits'] ?? 1);
            return $limit - $hits;
        } catch (\Throwable $e) {
            // Rate limiting should never crash the request — log and allow through
            error_log('[RateLimit] ' . $e->getMessage());
            return $limit;
        }
    }
}
