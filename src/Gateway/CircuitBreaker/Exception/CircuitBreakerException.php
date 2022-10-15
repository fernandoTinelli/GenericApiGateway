<?php

namespace App\Gateway\CircuitBreaker\Exception;

use Exception;

class CircuitBreakerException extends Exception
{
    public function __construct(string $message = "Circuit Breaker Exception", int $code = 0)
    {
        parent::__construct($message, $code);
    }
}