<?php

namespace App\Gateway\CircuitBreaker;

use App\Gateway\Request\JsonServiceRequest;
use Symfony\Component\HttpFoundation\Response;

interface CircuitBreakerInterface
{
    public function handleBreak(JsonServiceRequest $request, string $errorMessage = ""): Response;
}