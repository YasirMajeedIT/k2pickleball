<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Http\MiddlewareInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;

/**
 * Security headers middleware.
 * Sets standard security headers on all responses.
 */
final class SecurityHeadersMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        return $response
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('X-Frame-Options', 'DENY')
            ->header('X-XSS-Protection', '1; mode=block')
            ->header('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->header('Permissions-Policy', 'geolocation=(), microphone=(), camera=()')
            ->header('Content-Security-Policy', $this->buildCsp($request));
    }

    private function buildCsp(Request $request): string
    {
        if ($request->isApi()) {
            return "default-src 'none'; frame-ancestors 'none'";
        }

        // Allow inline styles/scripts for admin dashboard (TailAdmin + Alpine.js)
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://unpkg.com https://accounts.google.com https://*.squarecdn.com",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://accounts.google.com https://*.squarecdn.com",
            "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net https://*.squarecdn.com https://*.cloudfront.net",
            "img-src 'self' data: blob: https:",
            "connect-src 'self' https://accounts.google.com https://oauth2.googleapis.com https://*.squareup.com https://*.squareupsandbox.com",
            "frame-src https://accounts.google.com https://*.squarecdn.com https://*.squareup.com https://*.squareupsandbox.com",
            "frame-ancestors 'none'",
        ]);
    }
}
