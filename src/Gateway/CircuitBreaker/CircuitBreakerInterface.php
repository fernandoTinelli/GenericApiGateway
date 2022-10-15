<?php

namespace App\Gateway\CircuitBreaker;

use App\Response\JsonServiceRequest;
use App\Response\JsonServiceResponse;

interface CircuitBreakerInterface
{
    public function doDummy(JsonServiceRequest $request, string $errorMessage = ""): JsonServiceResponse;
}