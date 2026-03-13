<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

class TenantException extends AppException
{
    public function __construct(string $message = 'Tenant not found')
    {
        parent::__construct($message, 404);
    }
}
