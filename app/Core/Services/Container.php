<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\Exceptions\AppException;

/**
 * Simple dependency injection container.
 * Supports singleton and factory bindings with auto-resolution.
 */
final class Container
{
    private static ?self $instance = null;

    /** @var array<string, callable> */
    private array $bindings = [];

    /** @var array<string, object> */
    private array $singletons = [];

    /** @var array<string, bool> */
    private array $singletonKeys = [];

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register a factory binding.
     */
    public function bind(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    /**
     * Register a singleton binding.
     */
    public function singleton(string $abstract, callable $factory): void
    {
        $this->bindings[$abstract] = $factory;
        $this->singletonKeys[$abstract] = true;
    }

    /**
     * Register an existing instance as a singleton.
     */
    public function instance(string $abstract, object $instance): void
    {
        $this->singletons[$abstract] = $instance;
        $this->singletonKeys[$abstract] = true;
    }

    /**
     * Resolve a binding from the container.
     */
    public function make(string $abstract): mixed
    {
        // Return cached singleton
        if (isset($this->singletons[$abstract])) {
            return $this->singletons[$abstract];
        }

        // Resolve from binding
        if (isset($this->bindings[$abstract])) {
            $instance = ($this->bindings[$abstract])($this);

            // Cache if singleton
            if (isset($this->singletonKeys[$abstract])) {
                $this->singletons[$abstract] = $instance;
            }

            return $instance;
        }

        // Auto-resolve if class exists
        if (class_exists($abstract)) {
            return $this->autoResolve($abstract);
        }

        throw new AppException("No binding found for: {$abstract}");
    }

    /**
     * Check if a binding exists.
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->singletons[$abstract]);
    }

    /**
     * Auto-resolve a class by inspecting its constructor.
     */
    private function autoResolve(string $class): object
    {
        $reflection = new \ReflectionClass($class);

        if (!$reflection->isInstantiable()) {
            throw new AppException("Class {$class} is not instantiable");
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $param) {
            $type = $param->getType();

            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                $dependencies[] = $this->make($type->getName());
            } elseif ($param->isDefaultValueAvailable()) {
                $dependencies[] = $param->getDefaultValue();
            } else {
                throw new AppException(
                    "Cannot resolve parameter \${$param->getName()} in {$class}"
                );
            }
        }

        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * Reset the container (useful for testing).
     */
    public function reset(): void
    {
        $this->bindings = [];
        $this->singletons = [];
        $this->singletonKeys = [];
    }

    private function __construct() {}
    private function __clone() {}
}
