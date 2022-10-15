<?php

namespace App\Gateway\CircuitBreaker\Types;

use App\Gateway\CircuitBreaker\CircuitBreakerInterface;
use App\Response\JsonServiceRequest;
use App\Response\JsonServiceResponse;
use App\Response\ServiceResponseStatus;
use Symfony\Component\HttpFoundation\Response;

class DefaultCircuitBreaker implements CircuitBreakerInterface
{
    public function doDummy(JsonServiceRequest $request, string $errorMessage = ""): Response
    {
        $errorMessage = 
            $errorMessage != "" 
                ? " __CIRCUIT_BREAKER_START__ $errorMessage __CIRCUIT_BREAKER_END__" 
                : "";

        return JsonServiceResponse::encode(
            new JsonServiceResponse(
                status: ServiceResponseStatus::ERROR,
                message: "Erro ao realizar a solicitação" . $errorMessage
            )
        );
    }
}
