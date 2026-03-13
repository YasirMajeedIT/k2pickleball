<?php

declare(strict_types=1);

namespace App\Core\Http;

/**
 * Middleware interface for the request pipeline.
 */
interface MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response;
}
