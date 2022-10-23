<?php

namespace App\Gateway\Configuration;

use App\Gateway\AbstractAPIGateway;
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
        $this->services = $this->getArrayServices(
            Yaml::parseFile(
                $paramsBag->get('kernel.project_dir')
                . "/config/gateways/" . AbstractAPIGateway::$configPath . "/services.yaml"
            )
        );

        $this->routes = $this->getArrayRoutes(
            Yaml::parseFile(
                $paramsBag->get('kernel.project_dir')
                . "/config/gateways/" . AbstractAPIGateway::$configPath . "/routes.yaml"
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

    private function getArrayServices(array $servicesData): array
    {
        $services = [];
        
        if (array_key_first($servicesData) != 'services') {
            throw new HttpException(
                Response::HTTP_BAD_GATEWAY,
                "Erro no arquivo de configuração de Serviços da API Gateway: raiz 'services' não encontrada"
            );
        }

        foreach ($servicesData['services'] as $name => $service) {
            $services[$name] = ServiceFactory::create([$name => $service]);
        }

        return $services;
    }

    private function getArrayRoutes(array $routesData): array
    {
        $routes = [];
        
        if (array_key_first($routesData) != 'routes') {
            throw new HttpException(
                Response::HTTP_BAD_GATEWAY,
                "Erro no arquivo de configuração de Rotas da API Gateway: raiz 'routes' não encontrada"
            );
        }

        $routesOverload = [];
        foreach ($routesData['routes'] as $name => $route) {
            if ($route == null) {
                $routesOverload[] = $name;
                continue;
            }

            $routes[$name] = RouteFactory::create([$name => $route]);

            if (count(($routesOverload)) > 0) {
                foreach ($routesOverload as $key) {
                    $routeClone = clone($routes[$name]);
                    $routeClone->setName($key);
                    $routes[$key] = $routeClone;
                }
                $routesOverload = [];
            }
        }

        if (count($routesOverload) > 0) {
            throw new HttpException(
                Response::HTTP_BAD_GATEWAY,
                "Erro no arquivo de configuração de Rotas da API Gateway: há 'routes' sem dados"
            );
        }

        return $routes;
    }
}