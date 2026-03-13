<?php

declare(strict_types=1);

namespace App\Core\Http;

/**
 * Middleware pipeline executor.
 * Runs middleware in FIFO order, each calling $next to pass to the next one.
 */
final class MiddlewarePipeline
{
    /** @var MiddlewareInterface[] */
    private array $middlewares = [];

    public function pipe(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Execute the pipeline and call the final handler.
     */
    public function run(Request $request, callable $finalHandler): Response
    {
        $pipeline = $this->createPipeline($finalHandler);
        return $pipeline($request);
    }

    private function createPipeline(callable $finalHandler): callable
    {
        $middlewares = array_reverse($this->middlewares);

        $next = $finalHandler;

        foreach ($middlewares as $middleware) {
            $next = function (Request $request) use ($middleware, $next): Response {
                return $middleware->handle($request, $next);
            };
        }

        return $next;
    }
}
