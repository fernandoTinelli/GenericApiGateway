<?php

namespace App\Gateway\Configuration\Factory;

use App\Gateway\Configuration\Model\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RouteFactory
{
    const PROPERTIES = [
        'service',
        'secure'
    ];

    public static function create(array $data): Route
    {
        self::validateData($data);

        $name = array_key_first($data);

        return (new Route())
            ->setName($name)
            ->setServiceName($data[$name]['service'])
            ->setSecure($data[$name]['secure'])
        ;
    }

    private static function validateData(array $data): void
    {
        $name = array_key_first($data);

        foreach (self::PROPERTIES as $props) {
            if (!array_key_exists($props, $data[$name])) {
                throw new HttpException(
                    Response::HTTP_BAD_GATEWAY,
                    "Erro no arquivo de configuração de Rotas da API Gateway: $props não encontrada"
                );
            }
        }
    }
}