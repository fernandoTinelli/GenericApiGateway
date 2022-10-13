<?php

namespace App\Gateway\Configuration;

use App\Gateway\Configuration\Factory\GatewayFactory;
use App\Gateway\Configuration\Factory\RouteFactory;
use App\Gateway\Configuration\Model\Gateway;
use App\Gateway\Configuration\Model\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Yaml\Yaml;

class APIGatewayConfiguration
{
    private array $gateways;

    private array $routes;

    public function __construct(ContainerBagInterface $paramsBag)
    {
        $this->routes = $this->getArrayRoutes(
            Yaml::parseFile(
                $paramsBag->get('kernel.project_dir')
                . '/config/gateway/routes.yaml'
            )
        );

        $this->gateways = $this->getArrayGateways(
            Yaml::parseFile(
                $paramsBag->get('kernel.project_dir')
                . '/config/gateway/gateways.yaml'
            )
        );
    }

    public function getRoute(string $route): ?Route
    {
        return $this->routes[$route] ?? null;
    }

    public function getService(string $service): ?Gateway
    {
        return $this->gateways[$service] ?? null;
    }

    private function getArrayGateways(array $routesData): array
    {
        $routes = [];
        
        if (array_key_first($routesData) != 'gateways') {
            throw new HttpException(
                Response::HTTP_BAD_GATEWAY,
                "Erro no arquivo de configuração de Gateways da API Gateway: raiz gateways não encontrada"
            );
        }

        foreach ($routesData as $route) {
            $routes[array_key_first($route)] = GatewayFactory::create($route);
        }

        return $routes;
    }

    private function getArrayRoutes(array $routesData): array
    {
        $gateways = [];
        
        if (array_key_first($routesData) != 'routes') {
            throw new HttpException(
                Response::HTTP_BAD_GATEWAY,
                "Erro no arquivo de configuração de Rotas da API Gateway: raiz routes não encontrada"
            );
        }

        foreach ($routesData as $route) {
            $gateways[array_key_first($route)] = RouteFactory::create($route);
        }

        return $gateways;
    }
}