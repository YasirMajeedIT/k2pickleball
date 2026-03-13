<?php

declare(strict_types=1);

namespace App\Core\Http;

/**
 * HTTP Router with regex-based route matching.
 * Supports parameterized routes, route groups, and middleware per group.
 */
final class Router
{
    private array $routes = [];
    private array $groupStack = [];
    private array $namedRoutes = [];

    /**
     * Register a GET route.
     */
    public function get(string $path, array|callable $handler, ?string $name = null): self
    {
        return $this->addRoute('GET', $path, $handler, $name);
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, array|callable $handler, ?string $name = null): self
    {
        return $this->addRoute('POST', $path, $handler, $name);
    }

    /**
     * Register a PUT route.
     */
    public function put(string $path, array|callable $handler, ?string $name = null): self
    {
        return $this->addRoute('PUT', $path, $handler, $name);
    }

    /**
     * Register a PATCH route.
     */
    public function patch(string $path, array|callable $handler, ?string $name = null): self
    {
        return $this->addRoute('PATCH', $path, $handler, $name);
    }

    /**
     * Register a DELETE route.
     */
    public function delete(string $path, array|callable $handler, ?string $name = null): self
    {
        return $this->addRoute('DELETE', $path, $handler, $name);
    }

    /**
     * Register CRUD routes for a resource.
     * Creates: GET /, GET /{id}, POST /, PUT /{id}, DELETE /{id}
     */
    public function resource(string $path, string $controller): self
    {
        $this->get($path, [$controller, 'index']);
        $this->get("{$path}/{id}", [$controller, 'show']);
        $this->post($path, [$controller, 'store']);
        $this->put("{$path}/{id}", [$controller, 'update']);
        $this->delete("{$path}/{id}", [$controller, 'destroy']);
        return $this;
    }

    /**
     * Create a route group with shared prefix and middleware.
     */
    public function group(array $options, callable $callback): self
    {
        $this->groupStack[] = $options;
        $callback($this);
        array_pop($this->groupStack);
        return $this;
    }

    /**
     * Match a request to a route.
     *
     * @return array{handler: array|callable, params: array, middleware: string[], permission: string|null}|null
     */
    public function match(string $method, string $path): ?array
    {
        $path = rtrim($path, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $path, $matches)) {
                $params = array_filter(
                    $matches,
                    fn($key) => !is_numeric($key),
                    ARRAY_FILTER_USE_KEY
                );

                return [
                    'handler' => $route['handler'],
                    'params' => $params,
                    'middleware' => $route['middleware'],
                    'permission' => $route['permission'] ?? null,
                ];
            }
        }

        return null;
    }

    /**
     * Get all registered routes (for documentation/debugging).
     */
    public function getRoutes(): array
    {
        return array_map(fn($r) => [
            'method' => $r['method'],
            'path' => $r['path'],
            'middleware' => $r['middleware'],
            'permission' => $r['permission'] ?? null,
        ], $this->routes);
    }

    // -- Private --

    private function addRoute(string $method, string $path, array|callable $handler, ?string $name = null): self
    {
        $prefix = $this->getCurrentPrefix();
        $middleware = $this->getCurrentMiddleware();
        $permission = $this->getCurrentPermission();

        $fullPath = rtrim($prefix . '/' . ltrim($path, '/'), '/') ?: '/';
        $pattern = $this->pathToPattern($fullPath);

        $route = [
            'method' => $method,
            'path' => $fullPath,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => $middleware,
            'permission' => $permission,
        ];

        $this->routes[] = $route;

        if ($name !== null) {
            $this->namedRoutes[$name] = $route;
        }

        return $this;
    }

    /**
     * Convert a path like /api/organizations/{id} to a regex pattern.
     * Supports {param} and {param:pattern} syntax.
     */
    private function pathToPattern(string $path): string
    {
        $pattern = preg_replace_callback(
            '#\{(\w+)(?::([^}]+))?\}#',
            function ($matches) {
                $name = $matches[1];
                $regex = $matches[2] ?? '[^/]+';
                return "(?P<{$name}>{$regex})";
            },
            $path
        );

        return '#^' . $pattern . '$#';
    }

    private function getCurrentPrefix(): string
    {
        $prefix = '';
        foreach ($this->groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= '/' . ltrim($group['prefix'], '/');
            }
        }
        return rtrim($prefix, '/');
    }

    private function getCurrentMiddleware(): array
    {
        $middleware = [];
        foreach ($this->groupStack as $group) {
            if (isset($group['middleware'])) {
                $middleware = array_merge($middleware, (array) $group['middleware']);
            }
        }
        return $middleware;
    }

    private function getCurrentPermission(): ?string
    {
        // Return the most specific (last) permission in the group stack
        for ($i = count($this->groupStack) - 1; $i >= 0; $i--) {
            if (isset($this->groupStack[$i]['permission'])) {
                return $this->groupStack[$i]['permission'];
            }
        }
        return null;
    }
}
