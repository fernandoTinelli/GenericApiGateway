<?php

namespace App\Gateway\Configuration\Factory;

use App\Gateway\Configuration\Model\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RouteFactory
{
    const PROPERTIES = [
        'service'        => true,
        'secure'         => false,
        'circuit_breaker' => false
    ];

    public static function create(array $data): Route
    {
        self::validateData($data);

        $name = array_key_first($data);

        return (new Route())
            ->setName($name)
            ->setServiceName($data[$name]['service'])
            ->setSecure($data[$name]['secure'] ?? null)
            ->setCircuitBreaker($data[$name]['circuitBreaker'] ?? null)
        ;
    }

    private static function validateData(array $data): void
    {
        foreach ($data as $name => $props) {
            foreach ($props as $prop => $value) {
                // Validate the props of the route
                if (!array_key_exists($prop, self::PROPERTIES)) {
                    throw new HttpException(
                        Response::HTTP_BAD_GATEWAY,
                        "Erro no arquivo de configuração de Serviços da API Gateway: $prop não é uma propriedade válida"
                    );
                }
            }
        }

        // Validate the required props
        foreach (self::PROPERTIES as $prop => $required) {
            if ($required && !array_key_exists($prop, $data[array_key_first($data)])) {
                throw new HttpException(
                    Response::HTTP_BAD_GATEWAY,
                    "Erro no arquivo de configuração de Serviços da API Gateway: $prop não encontrada"
                );
            }
        }
    }
}