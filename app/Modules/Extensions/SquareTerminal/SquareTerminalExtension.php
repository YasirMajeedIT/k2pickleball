<?php

declare(strict_types=1);

namespace App\Modules\Extensions\SquareTerminal;

use App\Core\Extensions\BaseExtension;
use App\Core\Http\Router;

/**
 * Square Terminal POS extension.
 * Enables payment processing via Square Terminal hardware devices.
 * Supports per-facility device pairing and terminal checkouts.
 */
final class SquareTerminalExtension extends BaseExtension
{
    public function slug(): string
    {
        return 'square-terminal-pos';
    }

    public function name(): string
    {
        return 'Square Terminal POS';
    }

    public function settingsSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'device_id' => [
                    'type' => 'string',
                    'title' => 'Terminal Device ID',
                    'description' => 'The paired Square Terminal device ID',
                ],
                'device_name' => [
                    'type' => 'string',
                    'title' => 'Terminal Name',
                    'description' => 'A friendly name for this terminal',
                ],
            ],
        ];
    }

    public function validateSettings(array $settings): array
    {
        return [
            'device_id' => trim((string) ($settings['device_id'] ?? '')),
            'device_name' => trim((string) ($settings['device_name'] ?? '')),
        ];
    }

    public function registerRoutes(Router $router): void
    {
        $routeFile = __DIR__ . '/routes.php';
        if (file_exists($routeFile)) {
            $loadRoutes = require $routeFile;
            if (is_callable($loadRoutes)) {
                $loadRoutes($router);
            }
        }
    }
}
