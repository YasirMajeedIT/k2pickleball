<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

class PaymentException extends AppException
{
    public function __construct(string $message = 'Payment required', array $errors = [])
    {
        parent::__construct($message, 402, $errors);
    }
}
