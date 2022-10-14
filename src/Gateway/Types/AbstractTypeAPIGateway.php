<?php

namespace App\Gateway\Types;

use App\Gateway\AbstractAPIGateway;
use App\Gateway\Configuration\APIGatewayConfiguration;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractTypeAPIGateway extends AbstractAPIGateway
{
    private ContainerBagInterface $paramsBag;

    protected string $folderConfig;

    #[Required]
    public function init(APIGatewayConfiguration $configuration): void
    {
        AbstractAPIGateway::$routesFilePath = $this->paramsBag->get('kernel.project_dir')
            . "/config/gateways/{$this->folderConfig}/routes.yaml";
        AbstractAPIGateway::$servicesFilePath =  $this->paramsBag->get('kernel.project_dir')
            . "/config/gateways/{$this->folderConfig}/services.yaml";

        $this->configuration = $configuration;   
    }
}