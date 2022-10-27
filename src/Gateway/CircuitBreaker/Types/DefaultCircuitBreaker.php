<?php

namespace App\Gateway\CircuitBreaker\Types;

use App\Gateway\CircuitBreaker\CircuitBreakerInterface;
use App\Gateway\Request\JsonServiceRequest;
use App\Gateway\Response\JsonServiceResponse;
use App\Gateway\Response\ServiceResponseStatus;

class DefaultCircuitBreaker implements CircuitBreakerInterface
{
    public function handleBreak(JsonServiceRequest $request, string $errorMessage = ""): JsonServiceResponse
    {
        return new JsonServiceResponse(
            status: ServiceResponseStatus::ERROR,
            data: [$errorMessage],
            message: "Erro ao realizar a solicitação"
        );
    }
}
