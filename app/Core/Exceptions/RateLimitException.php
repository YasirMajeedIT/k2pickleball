<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

class RateLimitException extends AppException
{
    public function __construct(string $message = 'Too many requests')
    {
        parent::__construct($message, 429);
    }
}
