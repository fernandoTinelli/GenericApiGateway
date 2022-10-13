<?php

namespace App\Gateway;

use App\Gateway\Configuration\APIGatewayConfiguration;
use App\Requester\Requester;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBag;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\Service\Attribute\Required;

class AdministrativoGeralAPIGateway extends AbstractAPIGateway
{
    public function __construct(Requester $requester)
    {
        $this->requester = $requester;
    }
}