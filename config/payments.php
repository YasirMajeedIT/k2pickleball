<?php

declare(strict_types=1);

return [
    'environment' => $_ENV['SQUARE_ENVIRONMENT'] ?? 'sandbox',
    'access_token' => $_ENV['SQUARE_ACCESS_TOKEN'] ?? '',
    'application_id' => $_ENV['SQUARE_APPLICATION_ID'] ?? '',
    'location_id' => $_ENV['SQUARE_LOCATION_ID'] ?? '',
    'webhook_signature_key' => $_ENV['SQUARE_WEBHOOK_SIGNATURE_KEY'] ?? '',
    'currency' => 'USD',

    'sandbox' => [
        'base_url' => 'https://connect.squareupsandbox.com',
        'web_payments_url' => 'https://sandbox.web.squarecdn.com/v1/square.js',
    ],

    'production' => [
        'base_url' => 'https://connect.squareup.com',
        'web_payments_url' => 'https://web.squarecdn.com/v1/square.js',
    ],
];
