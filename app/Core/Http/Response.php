<?php

declare(strict_types=1);

namespace App\Core\Http;

/**
 * HTTP Response builder.
 * Provides a fluent interface for building and sending JSON responses.
 */
final class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private mixed $body = null;
    private bool $sent = false;

    public function __construct(mixed $body = null, int $statusCode = 200, array $headers = [])
    {
        $this->body = $body;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public static function json(mixed $data, int $status = 200): self
    {
        return new self($data, $status, ['Content-Type' => 'application/json']);
    }

    public static function success(mixed $data = null, string $message = 'Success', int $status = 200): self
    {
        $body = [
            'status' => 'success',
            'message' => $message,
        ];

        if ($data !== null) {
            $body['data'] = $data;
        }

        return self::json($body, $status);
    }

    public static function error(string $message, int $status = 400, array $errors = []): self
    {
        $body = [
            'status' => 'error',
            'message' => $message,
        ];

        if (!empty($errors)) {
            $body['errors'] = $errors;
        }

        return self::json($body, $status);
    }

    public static function created(mixed $data = null, string $message = 'Created successfully'): self
    {
        return self::success($data, $message, 201);
    }

    public static function noContent(): self
    {
        return new self(null, 204);
    }

    public static function notFound(string $message = 'Resource not found'): self
    {
        return self::error($message, 404);
    }

    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return self::error($message, 401);
    }

    public static function forbidden(string $message = 'Forbidden'): self
    {
        return self::error($message, 403);
    }

    public static function validationError(array $errors, string $message = 'Validation failed'): self
    {
        return self::error($message, 422, $errors);
    }

    public static function paginated(array $data, int $total, int $page, int $perPage): self
    {
        $lastPage = (int) ceil($total / $perPage);

        return self::json([
            'status' => 'success',
            'data' => $data,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'last_page' => $lastPage,
                'has_more' => $page < $lastPage,
            ],
        ]);
    }

    public static function html(string $content, int $status = 200): self
    {
        return new self($content, $status, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    // -- Fluent setters --

    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function header(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function withHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    public function body(mixed $body): self
    {
        $this->body = $body;
        return $this;
    }

    // -- Getters --

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    // -- Send --

    public function send(): void
    {
        if ($this->sent) {
            return;
        }

        http_response_code($this->statusCode);

        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }

        if ($this->body !== null) {
            if (!isset($this->headers['Content-Type'])) {
                header('Content-Type: application/json');
            }

            if (is_array($this->body) || is_object($this->body)) {
                echo json_encode($this->body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                echo $this->body;
            }
        }

        $this->sent = true;
    }
}
