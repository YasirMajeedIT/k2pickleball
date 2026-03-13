<?php

declare(strict_types=1);

return [
    'name' => $_ENV['APP_NAME'] ?? 'K2 Pickleball',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
    'url' => $_ENV['APP_URL'] ?? 'http://k2pickleball.local',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'America/New_York',
    'key' => $_ENV['APP_KEY'] ?? '',

    'domains' => [
        'base' => $_ENV['BASE_DOMAIN'] ?? 'k2pickleball.local',
        'platform' => $_ENV['PLATFORM_DOMAIN'] ?? 'platform.k2pickleball.local',
        'admin' => $_ENV['ADMIN_DOMAIN'] ?? 'admin.k2pickleball.local',
        'api' => $_ENV['API_DOMAIN'] ?? 'api.k2pickleball.local',
    ],

    'version' => '1.0.0',
];
