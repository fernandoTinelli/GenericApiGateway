<?php

namespace App\Gateway\Types;

use App\Requester\Requester;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class MainAPIGateway extends AbstractTypeAPIGateway
{
    public function __construct(Requester $requester, ContainerBagInterface $paramsBag)
    {
        $this->requester = $requester;
        $this->paramsBag = $paramsBag;
        $this->folder = "main";
    }
}