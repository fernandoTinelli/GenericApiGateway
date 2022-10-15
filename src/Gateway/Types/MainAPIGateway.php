<?php

namespace App\Gateway\Types;

use App\Gateway\AbstractAPIGateway;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class MainAPIGateway extends AbstractAPIGateway
{
    public function __construct(ContainerBagInterface $paramsBag)
    {
        $this->paramsBag = $paramsBag;

        AbstractAPIGateway::$configPath = "main";
    }
}