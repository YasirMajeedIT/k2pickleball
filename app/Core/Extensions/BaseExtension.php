<?php

declare(strict_types=1);

namespace App\Core\Extensions;

use App\Core\Http\Router;

/**
 * Base class for extensions providing sensible defaults.
 * Extend this instead of implementing ExtensionInterface directly.
 */
abstract class BaseExtension implements ExtensionInterface
{
    public function boot(): void
    {
        // No-op by default
    }

    public function onInstall(int $organizationId): void
    {
        // No-op by default
    }

    public function onUninstall(int $organizationId): void
    {
        // No-op by default
    }

    public function registerRoutes(Router $router): void
    {
        // No routes by default
    }

    public function settingsSchema(): array
    {
        return [];
    }

    public function validateSettings(array $settings): array
    {
        return $settings;
    }
}
