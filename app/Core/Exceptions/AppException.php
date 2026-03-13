<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

class AppException extends \RuntimeException
{
    protected int $statusCode;
    protected array $errors;

    public function __construct(
        string $message = 'An error occurred',
        int $statusCode = 500,
        array $errors = [],
        ?\Throwable $previous = null
    ) {
        $this->statusCode = $statusCode;
        $this->errors = $errors;
        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
