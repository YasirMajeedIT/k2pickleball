<?php

declare(strict_types=1);

namespace App\Core\Services;

/**
 * Simple configuration accessor.
 * Loads config files from /config directory and provides dot-notation access.
 */
final class Config
{
    private static array $config = [];
    private static bool $loaded = false;

    public static function load(string $configPath): void
    {
        if (self::$loaded) {
            return;
        }

        $files = glob($configPath . '/*.php');
        foreach ($files as $file) {
            $key = basename($file, '.php');
            self::$config[$key] = require $file;
        }

        self::$loaded = true;
    }

    /**
     * Get a configuration value using dot notation.
     * Example: Config::get('database.host')
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Set a configuration value at runtime.
     */
    public static function set(string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $config = &self::$config;

        foreach ($keys as $i => $segment) {
            if ($i === count($keys) - 1) {
                $config[$segment] = $value;
            } else {
                if (!isset($config[$segment]) || !is_array($config[$segment])) {
                    $config[$segment] = [];
                }
                $config = &$config[$segment];
            }
        }
    }

    /**
     * Get all configuration for a top-level key.
     */
    public static function all(string $key = ''): array
    {
        if ($key === '') {
            return self::$config;
        }

        return self::$config[$key] ?? [];
    }

    /**
     * Reset loaded config (useful for testing).
     */
    public static function reset(): void
    {
        self::$config = [];
        self::$loaded = false;
    }
}
