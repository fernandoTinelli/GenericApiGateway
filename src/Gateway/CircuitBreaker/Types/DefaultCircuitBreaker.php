<?php

namespace App\Gateway\CircuitBreaker\Types;

use App\Gateway\CircuitBreaker\CircuitBreakerInterface;
use App\Response\JsonServiceRequest;
use App\Response\JsonServiceResponse;

class DefaultCircuitBreaker implements CircuitBreakerInterface
{
    public function doDummy(JsonServiceRequest $request): JsonServiceResponse
    {
        return new JsonServiceResponse();
    }
}
