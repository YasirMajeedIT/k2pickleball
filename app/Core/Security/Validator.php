<?php

declare(strict_types=1);

namespace App\Core\Security;

use App\Core\Exceptions\ValidationException;

/**
 * Input validation service.
 * Validate data against rule sets.
 */
final class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Validate data against rules and throw on failure.
     *
     * Rules format: ['field' => 'required|string|max:255', ...]
     */
    public static function validate(array $data, array $rules, array $messages = []): array
    {
        $validator = new self($data);
        $validator->applyRules($rules, $messages);

        if (!$validator->passes()) {
            throw new ValidationException($validator->errors());
        }

        return $validator->validated($rules);
    }

    /**
     * Check if validation passes without throwing.
     */
    public static function check(array $data, array $rules): bool
    {
        $validator = new self($data);
        $validator->applyRules($rules);
        return $validator->passes();
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Return only validated fields.
     */
    public function validated(array $rules): array
    {
        $result = [];
        foreach ($rules as $field => $_) {
            if (array_key_exists($field, $this->data)) {
                $result[$field] = $this->data[$field];
            }
        }
        return $result;
    }

    private function applyRules(array $rules, array $messages = []): void
    {
        foreach ($rules as $field => $ruleString) {
            $fieldRules = is_array($ruleString) ? $ruleString : explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            // Check 'nullable' rule first
            if (in_array('nullable', $fieldRules, true) && ($value === null || $value === '')) {
                continue;
            }

            foreach ($fieldRules as $rule) {
                if ($rule === 'nullable') {
                    continue;
                }

                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }

                $method = 'rule' . ucfirst($rule);
                if (!method_exists($this, $method)) {
                    continue;
                }

                $error = $this->$method($field, $value, $params);
                if ($error !== null) {
                    // Allow custom message override
                    $key = "{$field}.{$rule}";
                    $this->errors[$field][] = $messages[$key] ?? $error;
                    // Stop on first error per field for 'required' / 'type' errors
                    if (in_array($rule, ['required', 'string', 'integer', 'numeric', 'array', 'boolean'])) {
                        break;
                    }
                }
            }
        }
    }

    // -- Validation Rules --

    private function ruleRequired(string $field, mixed $value, array $params): ?string
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            return "The {$field} field is required.";
        }
        return null;
    }

    private function ruleString(string $field, mixed $value, array $params): ?string
    {
        if ($value !== null && !is_string($value)) {
            return "The {$field} field must be a string.";
        }
        return null;
    }

    private function ruleInteger(string $field, mixed $value, array $params): ?string
    {
        if ($value !== null && filter_var($value, FILTER_VALIDATE_INT) === false) {
            return "The {$field} field must be an integer.";
        }
        return null;
    }

    private function ruleNumeric(string $field, mixed $value, array $params): ?string
    {
        if ($value !== null && !is_numeric($value)) {
            return "The {$field} field must be numeric.";
        }
        return null;
    }

    private function ruleBoolean(string $field, mixed $value, array $params): ?string
    {
        $accepted = [true, false, 0, 1, '0', '1', 'true', 'false'];
        if ($value !== null && !in_array($value, $accepted, true)) {
            return "The {$field} field must be a boolean.";
        }
        return null;
    }

    private function ruleArray(string $field, mixed $value, array $params): ?string
    {
        if ($value !== null && !is_array($value)) {
            return "The {$field} field must be an array.";
        }
        return null;
    }

    private function ruleEmail(string $field, mixed $value, array $params): ?string
    {
        if ($value !== null && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            return "The {$field} field must be a valid email address.";
        }
        return null;
    }

    private function ruleUrl(string $field, mixed $value, array $params): ?string
    {
        if ($value !== null && filter_var($value, FILTER_VALIDATE_URL) === false) {
            return "The {$field} field must be a valid URL.";
        }
        return null;
    }

    private function ruleMin(string $field, mixed $value, array $params): ?string
    {
        $min = (int) ($params[0] ?? 0);

        if (is_string($value)) {
            if (mb_strlen($value) < $min) {
                return "The {$field} field must be at least {$min} characters.";
            }
        } elseif (is_numeric($value)) {
            if ((float) $value < $min) {
                return "The {$field} field must be at least {$min}.";
            }
        } elseif (is_array($value) && count($value) < $min) {
            return "The {$field} field must have at least {$min} items.";
        }
        return null;
    }

    private function ruleMax(string $field, mixed $value, array $params): ?string
    {
        $max = (int) ($params[0] ?? 0);

        if (is_string($value)) {
            if (mb_strlen($value) > $max) {
                return "The {$field} field must not exceed {$max} characters.";
            }
        } elseif (is_numeric($value)) {
            if ((float) $value > $max) {
                return "The {$field} field must not exceed {$max}.";
            }
        } elseif (is_array($value) && count($value) > $max) {
            return "The {$field} field must not have more than {$max} items.";
        }
        return null;
    }

    private function ruleBetween(string $field, mixed $value, array $params): ?string
    {
        $min = (int) ($params[0] ?? 0);
        $max = (int) ($params[1] ?? 0);

        if (is_string($value)) {
            $len = mb_strlen($value);
            if ($len < $min || $len > $max) {
                return "The {$field} field must be between {$min} and {$max} characters.";
            }
        }
        if (is_numeric($value)) {
            $val = (float) $value;
            if ($val < $min || $val > $max) {
                return "The {$field} field must be between {$min} and {$max}.";
            }
        }
        return null;
    }

    private function ruleIn(string $field, mixed $value, array $params): ?string
    {
        if ($value !== null && !in_array((string) $value, $params, true)) {
            $allowed = implode(', ', $params);
            return "The {$field} field must be one of: {$allowed}.";
        }
        return null;
    }

    private function ruleNotIn(string $field, mixed $value, array $params): ?string
    {
        if ($value !== null && in_array((string) $value, $params, true)) {
            return "The {$field} field has an invalid value.";
        }
        return null;
    }

    private function ruleUuid(string $field, mixed $value, array $params): ?string
    {
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
        if ($value !== null && !preg_match($pattern, (string) $value)) {
            return "The {$field} field must be a valid UUID.";
        }
        return null;
    }

    private function ruleDate(string $field, mixed $value, array $params): ?string
    {
        $format = $params[0] ?? 'Y-m-d';
        if ($value !== null) {
            $d = \DateTime::createFromFormat($format, (string) $value);
            if (!$d || $d->format($format) !== (string) $value) {
                return "The {$field} field must be a valid date ({$format}).";
            }
        }
        return null;
    }

    private function ruleDatetime(string $field, mixed $value, array $params): ?string
    {
        if ($value !== null) {
            $d = \DateTime::createFromFormat('Y-m-d H:i:s', (string) $value);
            if (!$d || $d->format('Y-m-d H:i:s') !== (string) $value) {
                return "The {$field} field must be a valid datetime (Y-m-d H:i:s).";
            }
        }
        return null;
    }

    private function rulePhone(string $field, mixed $value, array $params): ?string
    {
        if ($value !== null && !preg_match('/^\+?[\d\s\-\(\)]{7,20}$/', (string) $value)) {
            return "The {$field} field must be a valid phone number.";
        }
        return null;
    }

    private function rulePassword(string $field, mixed $value, array $params): ?string
    {
        if ($value !== null) {
            $errors = [];
            if (mb_strlen($value) < 8) {
                $errors[] = 'at least 8 characters';
            }
            if (!preg_match('/[a-z]/', $value)) {
                $errors[] = 'a lowercase letter';
            }
            if (!preg_match('/[A-Z]/', $value)) {
                $errors[] = 'an uppercase letter';
            }
            if (!preg_match('/[0-9]/', $value)) {
                $errors[] = 'a number';
            }
            if (!empty($errors)) {
                return "The {$field} field must contain " . implode(', ', $errors) . '.';
            }
        }
        return null;
    }

    private function ruleSlug(string $field, mixed $value, array $params): ?string
    {
        if ($value !== null && !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', (string) $value)) {
            return "The {$field} field must be a valid slug (lowercase letters, numbers, and hyphens).";
        }
        return null;
    }

    private function ruleRegex(string $field, mixed $value, array $params): ?string
    {
        $pattern = $params[0] ?? '';
        if ($value !== null && $pattern && !preg_match($pattern, (string) $value)) {
            return "The {$field} field format is invalid.";
        }
        return null;
    }

    private function ruleConfirmed(string $field, mixed $value, array $params): ?string
    {
        $confirmField = $field . '_confirmation';
        $confirmValue = $this->data[$confirmField] ?? null;
        if ($value !== null && $value !== $confirmValue) {
            return "The {$field} confirmation does not match.";
        }
        return null;
    }

    private function ruleJson(string $field, mixed $value, array $params): ?string
    {
        if ($value !== null && is_string($value)) {
            json_decode($value);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return "The {$field} field must be valid JSON.";
            }
        }
        return null;
    }

    private function ruleIp(string $field, mixed $value, array $params): ?string
    {
        if ($value !== null && filter_var($value, FILTER_VALIDATE_IP) === false) {
            return "The {$field} field must be a valid IP address.";
        }
        return null;
    }

    private function ruleTimezone(string $field, mixed $value, array $params): ?string
    {
        if ($value !== null && !in_array($value, timezone_identifiers_list(), true)) {
            return "The {$field} field must be a valid timezone.";
        }
        return null;
    }

    private function ruleFile(string $field, mixed $value, array $params): ?string
    {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
            return "The {$field} field must be a valid uploaded file.";
        }
        return null;
    }

    private function ruleMimes(string $field, mixed $value, array $params): ?string
    {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($_FILES[$field]['tmp_name']);
            $mimeMap = [
                'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
                'gif' => 'image/gif', 'svg' => 'image/svg+xml', 'pdf' => 'application/pdf',
                'csv' => 'text/csv', 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];
            $allowedMimes = [];
            foreach ($params as $ext) {
                if (isset($mimeMap[$ext])) {
                    $allowedMimes[] = $mimeMap[$ext];
                }
            }
            if (!in_array($mime, $allowedMimes, true)) {
                return "The {$field} must be a file of type: " . implode(', ', $params) . '.';
            }
        }
        return null;
    }

    private function ruleMaxFilesize(string $field, mixed $value, array $params): ?string
    {
        $maxKb = (int) ($params[0] ?? 0);
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            if ($_FILES[$field]['size'] > $maxKb * 1024) {
                return "The {$field} must not exceed {$maxKb}KB.";
            }
        }
        return null;
    }
}
