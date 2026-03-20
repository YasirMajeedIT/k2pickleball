<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Database\Connection;
use App\Core\Exceptions\AppException;
use App\Core\Exceptions\Handler;
use App\Core\Http\MiddlewareInterface;
use App\Core\Http\MiddlewarePipeline;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Router;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\CorsMiddleware;
use App\Core\Middleware\RateLimitMiddleware;
use App\Core\Middleware\SecurityHeadersMiddleware;
use App\Core\Middleware\TenantResolver;
use App\Core\Services\Config;
use App\Core\Services\Container;

/**
 * Main Application class.
 * Bootstraps the platform: config, database, middleware, routing.
 */
final class Application
{
    private static ?self $instance = null;

    private Container $container;
    private Router $router;
    private Request $request;
    private MiddlewarePipeline $pipeline;

    private function __construct()
    {
        $this->container = Container::getInstance();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Boot the application: config, error handling, database, routes, middleware.
     */
    public function boot(): void
    {
        // 1. Load configuration
        Config::load(K2_ROOT . '/config');

        // 2. Register error handler
        Handler::register();

        // 3. Initialize request
        $this->request = new Request();

        // 4. Register core services in container
        $this->registerServices();

        // 5. Initialize database
        $this->initDatabase();

        // 6. Initialize router and load routes
        $this->router = new Router();
        $this->container->instance(Router::class, $this->router);
        $this->loadRoutes();

        // 7. Build middleware pipeline
        $this->buildMiddlewarePipeline();
    }

    /**
     * Handle the incoming request through the middleware pipeline and router.
     */
    public function handleRequest(): void
    {
        try {
            $response = $this->pipeline->run($this->request, function (Request $request): Response {
                return $this->dispatch($request);
            });
            $response->send();
        } catch (AppException $e) {
            $body = ['status' => 'error', 'message' => $e->getMessage()];
            if (!empty($e->getErrors())) {
                $body['errors'] = $e->getErrors();
            }
            Response::json($body, $e->getStatusCode())->send();
        } catch (\Throwable $e) {
            Handler::handleException($e);
        }
    }

    /**
     * Get the DI container.
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * Get the router.
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    // -- Private --

    private function registerServices(): void
    {
        // Register the application itself
        $this->container->instance(self::class, $this);
        $this->container->instance(Request::class, $this->request);

        // Database connection (singleton)
        $this->container->singleton(Connection::class, function () {
            $dbConfig = Config::all('database');
            return new Connection(
                host: $dbConfig['host'],
                port: $dbConfig['port'],
                database: $dbConfig['name'],
                username: $dbConfig['user'],
                password: $dbConfig['pass'],
                charset: $dbConfig['charset'],
                options: $dbConfig['options'] ?? []
            );
        });
    }

    private function initDatabase(): void
    {
        // Eagerly initialize database to catch connection errors early
        try {
            $this->container->make(Connection::class);
        } catch (\Throwable $e) {
            // In debug mode, let it propagate; in production, log and serve error
            if (Config::get('app.debug')) {
                throw $e;
            }
            throw new AppException('Service temporarily unavailable', 503);
        }
    }

    private function loadRoutes(): void
    {
        $routesFile = K2_ROOT . '/app/routes.php';
        if (file_exists($routesFile)) {
            $registerRoutes = require $routesFile;
            if (is_callable($registerRoutes)) {
                $registerRoutes($this->router, $this->container);
            }
        }
    }

    private function buildMiddlewarePipeline(): void
    {
        $this->pipeline = new MiddlewarePipeline();

        // Global middleware stack (order matters)
        $this->pipeline
            ->pipe(new CorsMiddleware())
            ->pipe(new SecurityHeadersMiddleware())
            ->pipe(new RateLimitMiddleware())
            ->pipe($this->container->make(TenantResolver::class))
            ->pipe($this->container->make(AuthMiddleware::class));
    }

    /**
     * Dispatch the request to the matched route handler.
     */
    private function dispatch(Request $request): Response
    {
        $method = $request->method();
        $path = $request->path();

        $match = $this->router->match($method, $path);

        if ($match === null) {
            // Check if any route matches the path with different method
            $allowedMethods = [];
            foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $m) {
                if ($m !== $method && $this->router->match($m, $path) !== null) {
                    $allowedMethods[] = $m;
                }
            }

            if (!empty($allowedMethods)) {
                return Response::error('Method Not Allowed', 405)
                    ->header('Allow', implode(', ', $allowedMethods));
            }

            return Response::notFound('Route not found');
        }

        // Check route-level permission
        if ($match['permission'] !== null) {
            $this->checkPermission($request, $match['permission']);
        }

        // Protect all /api/platform/* routes — super-admin only
        if (str_starts_with($path, '/api/platform/')) {
            if (!$request->isSuperAdmin()) {
                return Response::error('Forbidden. Super-admin access required.', 403);
            }
        }

        // Resolve and call the handler
        return $this->callHandler($match['handler'], $match['params'], $request);
    }

    /**
     * Call a route handler (controller method or callable).
     */
    private function callHandler(array|callable $handler, array $params, Request $request): Response
    {
        if (is_callable($handler) && !is_array($handler)) {
            return $handler($request, ...$params);
        }

        if (is_array($handler) && count($handler) === 2) {
            [$controllerClass, $method] = $handler;

            $controller = $this->container->make($controllerClass);

            if (method_exists($controller, 'setRequest')) {
                $controller->setRequest($request);
            }

            // Cast numeric route params (e.g. {id}) to int
            $castParams = [];
            foreach ($params as $key => $value) {
                $castParams[$key] = is_numeric($value) ? (int) $value : $value;
            }

            $result = $controller->{$method}($request, ...$castParams);

            if ($result instanceof Response) {
                return $result;
            }

            return Response::json($result);
        }

        throw new AppException('Invalid route handler');
    }

    /**
     * Check if the current user has the required permission.
     */
    private function checkPermission(Request $request, string $permission): void
    {
        // Super admins bypass permission checks
        if ($request->isSuperAdmin()) {
            return;
        }

        $userPermissions = $request->getAttribute('user_permissions', []);

        // Check for wildcard permissions
        $module = explode('.', $permission)[0] ?? '';
        $hasWildcard = in_array("{$module}.*", $userPermissions, true)
            || in_array('*', $userPermissions, true);

        if (!$hasWildcard && !in_array($permission, $userPermissions, true)) {
            throw new \App\Core\Exceptions\AuthorizationException(
                'You do not have permission to perform this action'
            );
        }
    }

    private function __clone() {}
}
