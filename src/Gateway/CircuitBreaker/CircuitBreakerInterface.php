<?php

namespace App\Gateway\CircuitBreaker;

use App\Response\JsonServiceRequest;
use Symfony\Component\HttpFoundation\Response;

interface CircuitBreakerInterface
{
    public function doDummy(JsonServiceRequest $request, string $errorMessage = ""): Response;
}