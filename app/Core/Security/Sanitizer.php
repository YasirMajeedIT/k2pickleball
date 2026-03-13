<?php

declare(strict_types=1);

namespace App\Core\Security;

/**
 * Input sanitization service.
 * Cleans user input to prevent XSS and injection attacks.
 */
final class Sanitizer
{
    /**
     * Sanitize all values in an array.
     */
    public static function sanitizeAll(array $data, array $rules = []): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (isset($rules[$key])) {
                $result[$key] = self::applySanitizer($value, $rules[$key]);
            } elseif (is_string($value)) {
                $result[$key] = self::string($value);
            } elseif (is_array($value)) {
                $result[$key] = self::sanitizeAll($value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Apply a named sanitizer.
     */
    public static function applySanitizer(mixed $value, string $type): mixed
    {
        return match ($type) {
            'string' => is_string($value) ? self::string($value) : $value,
            'email' => is_string($value) ? self::email($value) : $value,
            'html' => is_string($value) ? self::html($value) : $value,
            'url' => is_string($value) ? self::url($value) : $value,
            'filename' => is_string($value) ? self::filename($value) : $value,
            'integer' => self::integer($value),
            'float' => self::float($value),
            'boolean' => self::boolean($value),
            'slug' => is_string($value) ? self::slug($value) : $value,
            'phone' => is_string($value) ? self::phone($value) : $value,
            'none' => $value,
            default => is_string($value) ? self::string($value) : $value,
        };
    }

    /**
     * Sanitize a plain string: trim, strip tags, encode special chars.
     */
    public static function string(string $value): string
    {
        $value = trim($value);
        $value = strip_tags($value);
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Sanitize email address.
     */
    public static function email(string $value): string
    {
        $value = trim($value);
        $value = mb_strtolower($value);
        return filter_var($value, FILTER_SANITIZE_EMAIL) ?: '';
    }

    /**
     * Sanitize HTML - allow safe tags only.
     */
    public static function html(string $value): string
    {
        $allowed = '<p><br><strong><em><ul><ol><li><a><h1><h2><h3><h4><h5><h6><blockquote><code><pre><span><div><table><thead><tbody><tr><th><td>';
        $value = trim($value);
        $value = strip_tags($value, $allowed);
        return $value;
    }

    /**
     * Sanitize URL.
     */
    public static function url(string $value): string
    {
        $value = trim($value);
        $url = filter_var($value, FILTER_SANITIZE_URL) ?: '';
        // Only allow http/https schemes
        if ($url && !preg_match('#^https?://#i', $url)) {
            return '';
        }
        return $url;
    }

    /**
     * Sanitize filename - remove path components and dangerous characters.
     */
    public static function filename(string $value): string
    {
        // Remove directory traversal
        $value = basename($value);
        // Replace dangerous characters
        $value = preg_replace('/[^\w\-.]/', '_', $value);
        // Remove multiple dots (prevent double extensions)
        $value = preg_replace('/\.{2,}/', '.', $value);
        // Trim dots and underscores from edges
        return trim($value, '._');
    }

    /**
     * Sanitize to integer.
     */
    public static function integer(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        $result = filter_var($value, FILTER_VALIDATE_INT);
        return $result !== false ? $result : null;
    }

    /**
     * Sanitize to float.
     */
    public static function float(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        $result = filter_var($value, FILTER_VALIDATE_FLOAT);
        return $result !== false ? $result : null;
    }

    /**
     * Sanitize to boolean.
     */
    public static function boolean(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Sanitize slug.
     */
    public static function slug(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9\-]/', '-', $value);
        $value = preg_replace('/-+/', '-', $value);
        return trim($value, '-');
    }

    /**
     * Sanitize phone number.
     */
    public static function phone(string $value): string
    {
        // Keep only digits, +, -, (, ), and spaces
        return preg_replace('/[^\d\+\-\(\)\s]/', '', trim($value));
    }
}
