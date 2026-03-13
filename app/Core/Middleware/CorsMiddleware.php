<?php

declare(strict_types=1);

namespace App\Core\Middleware;

use App\Core\Http\MiddlewareInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Services\Config;

/**
 * CORS middleware.
 * Handles preflight OPTIONS requests and sets CORS headers.
 */
final class CorsMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        // Handle preflight OPTIONS request
        if ($request->method() === 'OPTIONS') {
            return $this->buildPreflightResponse();
        }

        /** @var Response $response */
        $response = $next($request);

        return $this->addCorsHeaders($request, $response);
    }

    private function buildPreflightResponse(): Response
    {
        $response = new Response(null, 204);

        $cors = Config::all('cors');

        return $response
            ->header('Access-Control-Allow-Origin', $this->getAllowedOrigin())
            ->header('Access-Control-Allow-Methods', implode(', ', $cors['allowed_methods'] ?? ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS']))
            ->header('Access-Control-Allow-Headers', implode(', ', $cors['allowed_headers'] ?? ['Content-Type', 'Authorization']))
            ->header('Access-Control-Max-Age', (string) ($cors['max_age'] ?? 86400))
            ->header('Access-Control-Allow-Credentials', ($cors['allow_credentials'] ?? true) ? 'true' : 'false');
    }

    private function addCorsHeaders(Request $request, Response $response): Response
    {
        $cors = Config::all('cors');

        return $response
            ->header('Access-Control-Allow-Origin', $this->getAllowedOrigin())
            ->header('Access-Control-Allow-Credentials', ($cors['allow_credentials'] ?? true) ? 'true' : 'false')
            ->header('Access-Control-Expose-Headers', implode(', ', $cors['exposed_headers'] ?? []));
    }

    private function getAllowedOrigin(): string
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        $allowedOrigins = Config::get('cors.allowed_origins', []);

        foreach ($allowedOrigins as $allowed) {
            if ($allowed === '*') {
                return '*';
            }

            // Support wildcard subdomain patterns
            if (str_contains($allowed, '*')) {
                $pattern = str_replace('\*', '[a-zA-Z0-9\-]+', preg_quote($allowed, '#'));
                if (preg_match("#^{$pattern}$#", $origin)) {
                    return $origin;
                }
            } elseif ($origin === $allowed) {
                return $origin;
            }
        }

        // Default: return the first allowed origin
        return $allowedOrigins[0] ?? '';
    }
}
