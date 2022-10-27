<?php

namespace App\Gateway\CircuitBreaker\Types;

use App\Gateway\CircuitBreaker\CircuitBreakerInterface;
use App\Gateway\Request\JsonServiceRequest;
use App\Gateway\Response\JsonServiceResponse;
use App\Gateway\Response\ServiceResponseStatus;
use Symfony\Component\HttpFoundation\Response;

class DefaultCircuitBreaker implements CircuitBreakerInterface
{
    public function handleBreak(JsonServiceRequest $request, string $errorMessage = ""): Response
    {
        return JsonServiceResponse::encode(
            new JsonServiceResponse(
                status: ServiceResponseStatus::ERROR,
                data: [$errorMessage],
                message: "Erro ao realizar a solicitação"
            )
        );
    }
}
