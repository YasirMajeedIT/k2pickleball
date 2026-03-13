<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

class AuthorizationException extends AppException
{
    public function __construct(string $message = 'Forbidden')
    {
        parent::__construct($message, 403);
    }
}
