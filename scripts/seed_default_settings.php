<?php
// Seed default settings for all organizations
// Run: php scripts/seed_default_settings.php

$env = [];
foreach (file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') continue;
    if (strpos($line, '=') === false) continue;
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, " \t\r\n\"'");
}
if (empty($env)) {
    die("Could not read .env file\n");
}

$dsn = 'mysql:host=' . $env['DB_HOST'] . ';dbname=' . $env['DB_NAME'] . ';charset=utf8mb4';
try {
    $pdo = new PDO($dsn, $env['DB_USER'], $env['DB_PASS'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage() . "\n");
}

echo "Connected to: " . $env['DB_NAME'] . "\n\n";

// Default settings grouped by category
$defaults = [
    'general' => [
        ['key' => 'site_name',                'value' => 'K2 Pickleball', 'type' => 'string',  'desc' => 'Organization display name'],
        ['key' => 'timezone',                  'value' => 'America/New_York', 'type' => 'string', 'desc' => 'Default timezone for dates/times'],
        ['key' => 'date_format',               'value' => 'm/d/Y', 'type' => 'string', 'desc' => 'Date display format (PHP date format)'],
        ['key' => 'time_format',               'value' => 'h:i A', 'type' => 'string', 'desc' => 'Time display format (12h or 24h)'],
        ['key' => 'default_currency',          'value' => 'USD', 'type' => 'string', 'desc' => 'Default currency code'],
        ['key' => 'booking_lead_time_hours',   'value' => '2', 'type' => 'integer', 'desc' => 'Minimum hours before a booking can start'],
        ['key' => 'max_booking_days_ahead',    'value' => '30', 'type' => 'integer', 'desc' => 'How many days in advance bookings are allowed'],
        ['key' => 'cancellation_window_hours', 'value' => '24', 'type' => 'integer', 'desc' => 'Hours before start that cancellation is allowed'],
    ],
    'branding' => [
        ['key' => 'primary_color',   'value' => '#4f46e5', 'type' => 'string', 'desc' => 'Brand primary color (hex)'],
        ['key' => 'logo_url',        'value' => '', 'type' => 'string', 'desc' => 'URL to organization logo image'],
        ['key' => 'favicon_url',     'value' => '', 'type' => 'string', 'desc' => 'URL to favicon image'],
        ['key' => 'footer_text',     'value' => 'Powered by K2 Pickleball', 'type' => 'string', 'desc' => 'Footer text displayed on client pages'],
    ],
    'notifications' => [
        ['key' => 'email_notifications_enabled', 'value' => '1', 'type' => 'boolean', 'desc' => 'Enable email notifications'],
        ['key' => 'sms_notifications_enabled',   'value' => '0', 'type' => 'boolean', 'desc' => 'Enable SMS notifications via Twilio'],
        ['key' => 'booking_confirmation_email',  'value' => '1', 'type' => 'boolean', 'desc' => 'Send confirmation email after booking'],
        ['key' => 'booking_reminder_hours',      'value' => '24', 'type' => 'integer', 'desc' => 'Hours before session to send reminder'],
        ['key' => 'payment_receipt_email',       'value' => '1', 'type' => 'boolean', 'desc' => 'Send receipt email after payment'],
        ['key' => 'admin_new_booking_alert',     'value' => '1', 'type' => 'boolean', 'desc' => 'Notify admin when a new booking is made'],
    ],
    'billing' => [
        ['key' => 'tax_enabled',         'value' => '0', 'type' => 'boolean', 'desc' => 'Enable tax on invoices'],
        ['key' => 'tax_rate',            'value' => '0', 'type' => 'float', 'desc' => 'Tax rate percentage (e.g. 8.25)'],
        ['key' => 'tax_label',           'value' => 'Tax', 'type' => 'string', 'desc' => 'Label shown for tax line item'],
        ['key' => 'auto_charge_enabled', 'value' => '0', 'type' => 'boolean', 'desc' => 'Automatically charge saved payment methods'],
        ['key' => 'invoice_prefix',      'value' => 'INV-', 'type' => 'string', 'desc' => 'Prefix for generated invoice numbers'],
        ['key' => 'payment_terms_days',  'value' => '30', 'type' => 'integer', 'desc' => 'Default payment terms in days'],
    ],
    'integrations' => [
        ['key' => 'square_enabled',       'value' => '0', 'type' => 'boolean', 'desc' => 'Enable Square payment processing'],
        ['key' => 'twilio_enabled',       'value' => '0', 'type' => 'boolean', 'desc' => 'Enable Twilio SMS integration'],
        ['key' => 'google_analytics_id',  'value' => '', 'type' => 'string', 'desc' => 'Google Analytics tracking ID'],
    ],
    'advanced' => [
        ['key' => 'maintenance_mode',         'value' => '0', 'type' => 'boolean', 'desc' => 'Put the site in maintenance mode'],
        ['key' => 'debug_mode',               'value' => '0', 'type' => 'boolean', 'desc' => 'Enable debug logging (disable in production)'],
        ['key' => 'session_timeout_minutes',  'value' => '60', 'type' => 'integer', 'desc' => 'User session timeout in minutes'],
        ['key' => 'max_file_upload_mb',       'value' => '10', 'type' => 'integer', 'desc' => 'Maximum file upload size in MB'],
        ['key' => 'api_rate_limit_per_minute','value' => '60', 'type' => 'integer', 'desc' => 'API rate limit (requests per minute)'],
    ],
];

// Get all organizations
$orgs = $pdo->query("SELECT id, name FROM organizations")->fetchAll(PDO::FETCH_ASSOC);
if (empty($orgs)) {
    die("No organizations found.\n");
}

$insertStmt = $pdo->prepare(
    "INSERT IGNORE INTO `settings` (`organization_id`, `group_name`, `key_name`, `value`, `type`, `description`, `created_at`, `updated_at`)
     VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())"
);

$total = 0;
foreach ($orgs as $org) {
    echo "Org #{$org['id']} — {$org['name']}:\n";
    $orgCount = 0;
    foreach ($defaults as $group => $settings) {
        foreach ($settings as $s) {
            $insertStmt->execute([$org['id'], $group, $s['key'], $s['value'], $s['type'], $s['desc']]);
            if ($insertStmt->rowCount() > 0) {
                $orgCount++;
            }
        }
    }
    echo "  Inserted $orgCount new settings (skipped existing)\n";
    $total += $orgCount;
}

echo "\nDone. Total settings inserted: $total\n";
