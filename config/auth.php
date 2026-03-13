<?php

declare(strict_types=1);

return [
    'jwt' => [
        'secret' => $_ENV['JWT_SECRET'] ?? '',
        'algo' => $_ENV['JWT_ALGO'] ?? 'HS256',
        'access_ttl' => (int) ($_ENV['JWT_ACCESS_TTL'] ?? 1800),     // 30 minutes
        'refresh_ttl' => (int) ($_ENV['JWT_REFRESH_TTL'] ?? 2592000), // 30 days
        'issuer' => $_ENV['APP_URL'] ?? 'http://k2pickleball.local',
    ],

    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_number' => true,
        'require_special' => true,
        'bcrypt_cost' => 12,
    ],

    'session' => [
        'lifetime' => 3600,
        'cookie_name' => 'K2PB_SESSION',
        'cookie_secure' => false,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
    ],
];
