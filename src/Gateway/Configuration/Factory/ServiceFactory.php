<?php

namespace App\Gateway\Configuration\Factory;

use App\Gateway\Configuration\Model\Service;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ServiceFactory
{
    const PROPERTIES = [
        'address'
    ];

    public static function create(array $data): Service
    {
        self::validateData($data);

        $name = array_key_first($data);

        return (new Service())
            ->setName($name)
            ->setAddress($data[$name]['address'])
        ;
    }

    private static function validateData(array $data): void
    {
        $name = array_key_first($data);

        foreach (self::PROPERTIES as $props) {
            if (!array_key_exists($props, $data[$name])) {
                throw new HttpException(
                    Response::HTTP_BAD_GATEWAY,
                    "Erro no arquivo de configuração dos Serviços da API Gateway: $props não encontrada"
                );
            }
        }
    }
}