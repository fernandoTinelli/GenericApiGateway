<?php

namespace App\Gateway\CircuitBreaker;

use App\Gateway\Request\JsonServiceRequest;
use App\Gateway\Response\JsonServiceResponse;

interface CircuitBreakerInterface
{
    public function handleBreak(JsonServiceRequest $request, string $errorMessage = ""): JsonServiceResponse;
}