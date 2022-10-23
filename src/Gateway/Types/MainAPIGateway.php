<?php

namespace App\Gateway\Types;

use App\Gateway\AbstractAPIGateway;
use App\Gateway\Validator\APIGatewayRequestValidator;

class MainAPIGateway extends AbstractAPIGateway
{
    public function __construct(
        APIGatewayRequestValidator $validator
    ) {
        $this->validator = $validator;

        AbstractAPIGateway::$configPath = "main";
    }
}