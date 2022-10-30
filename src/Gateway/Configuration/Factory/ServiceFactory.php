<?php

namespace App\Gateway\Configuration\Factory;

use App\Gateway\Configuration\Model\Service;
use Exception;

class ServiceFactory
{
    const PROPERTIES = [
        'address' => true
    ];

    public static function create(array $data): Service
    {
        self::validateData($data);

        $name = array_key_first($data);

        return (new Service())
            ->setAddress($data[$name]['address'])
        ;
    }

    private static function validateData(array $data): void
    {
        foreach ($data as $name => $props) {
            foreach ($props as $prop => $value) {
                // Validate the props of the route
                if (!array_key_exists($prop, self::PROPERTIES)) {
                    throw new Exception(
                        "Erro no arquivo de configuração de Serviços da API Gateway: $prop não é uma propriedade válida"
                    );
                }
            }
        }

        // Validate the required props
        foreach (self::PROPERTIES as $prop => $required) {
            if ($required && !array_key_exists($prop, $data[array_key_first($data)])) {
                throw new Exception(
                    "Erro no arquivo de configuração de Serviços da API Gateway: $prop não encontrada"
                );
            }
        }
    }
}