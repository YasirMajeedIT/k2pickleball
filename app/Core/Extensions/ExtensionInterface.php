<?php

declare(strict_types=1);

namespace App\Core\Extensions;

use App\Core\Http\Router;

/**
 * Contract that all platform extensions must implement.
 * Extensions are modular add-ons that can be installed/activated per-organization.
 */
interface ExtensionInterface
{
    /** Unique slug matching the `extensions` table record. */
    public function slug(): string;

    /** Human-readable extension name. */
    public function name(): string;

    /** Called when the extension is booted (active for an org). */
    public function boot(): void;

    /** Called after installation for an organization. */
    public function onInstall(int $organizationId): void;

    /** Called before uninstallation from an organization. */
    public function onUninstall(int $organizationId): void;

    /** Register API routes specific to this extension. */
    public function registerRoutes(Router $router): void;

    /** Return JSON-serializable settings schema for the settings UI. */
    public function settingsSchema(): array;

    /** Validate and return sanitized settings from user input. */
    public function validateSettings(array $settings): array;
}
