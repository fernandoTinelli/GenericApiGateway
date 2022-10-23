<?php

namespace App\Gateway\Types;

use App\Gateway\AbstractAPIGateway;
use App\Gateway\Log\RequestLogger;
use App\Gateway\Validator\APIGatewayRequestValidator;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class MainAPIGateway extends AbstractAPIGateway
{
    public function __construct(
        ContainerBagInterface $paramsBag,
        RequestLogger $logger,
        APIGatewayRequestValidator $validator
    ) {
        $this->paramsBag = $paramsBag;
        $this->logger = $logger;
        $this->validator = $validator;

        AbstractAPIGateway::$configPath = "main";
    }
}