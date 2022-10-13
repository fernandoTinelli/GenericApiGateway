<?php

namespace App\Gateway\Configuration;

use App\Gateway\Configuration\Factory\RouteFactory;
use App\Gateway\Configuration\Factory\ServiceFactory;
use App\Gateway\Configuration\Model\Route;
use App\Gateway\Configuration\Model\Service;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Yaml\Yaml;

class APIGatewayConfiguration
{
    private array $services;

    private array $routes;

    public function __construct(ContainerBagInterface $paramsBag)
    {
        $this->routes = $this->getArrayRoutes(
            Yaml::parseFile(
                $paramsBag->get('kernel.project_dir')
                . '/config/gateway/routes.yaml'
            )
        );

        $this->services = $this->getArrayServices(
            Yaml::parseFile(
                $paramsBag->get('kernel.project_dir')
                . '/config/gateway/services.yaml'
            )
        );
    }

    public function getRoute(string $route): ?Route
    {
        return $this->routes[$route] ?? null;
    }

    public function getService(string $service): ?Service
    {
        return $this->services[$service] ?? null;
    }

    private function getArrayServices(array $routesData): array
    {
        $routes = [];
        
        if (array_key_first($routesData) != 'services') {
            throw new HttpException(
                Response::HTTP_BAD_GATEWAY,
                "Erro no arquivo de configuração de Serviços da API Gateway: raiz 'services' não encontrada"
            );
        }

        foreach ($routesData as $route) {
            $routes[array_key_first($route)] = ServiceFactory::create($route);
        }

        return $routes;
    }

    private function getArrayRoutes(array $routesData): array
    {
        $services = [];
        
        if (array_key_first($routesData) != 'routes') {
            throw new HttpException(
                Response::HTTP_BAD_GATEWAY,
                "Erro no arquivo de configuração de Rotas da API Gateway: raiz 'routes' não encontrada"
            );
        }

        foreach ($routesData as $route) {
            $services[array_key_first($route)] = RouteFactory::create($route);
        }

        return $services;
    }
}