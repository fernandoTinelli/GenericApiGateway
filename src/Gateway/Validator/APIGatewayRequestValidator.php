<?php

namespace App\Gateway\Validator;

use App\Gateway\Request\JsonServiceRequest;
use App\Validator\AbstractRequestValidator;
use App\Validator\Violations;

class APIGatewayRequestValidator extends AbstractRequestValidator
{
    public function validate(JsonServiceRequest $request): Violations
    {
        $violations = new Violations();

        $route = $request->getRoute();
        if (is_null($route)) {
            $violations->add("Route", "Recurso solicitado não encontrado");
        }

        $service = $this->configuration->getService($route->getServiceName());
        if (is_null($service)) {
            $violations->add("Service", "Nenhum serviço encontrado para a recurso solicitado");
        }

        return $violations;
    }
}