<?php

namespace App\Gateway\Types;

use App\Gateway\AbstractAPIGateway;
use App\Gateway\Log\RequestLogger;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class MainAPIGateway extends AbstractAPIGateway
{
    public function __construct(ContainerBagInterface $paramsBag, RequestLogger $logger)
    {
        $this->paramsBag = $paramsBag;
        $this->logger = $logger;

        AbstractAPIGateway::$configPath = "main";
    }
}