<?php

namespace App\Gateway\Configuration\Factory;

use App\Gateway\Configuration\Model\Logger;
use App\Gateway\Configuration\Model\LoggerType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LoggerFactory
{
    const PROPERTIES = [
        'enabled'     => false,
        'type'        => false,
        'path'        => false,
        'file_name'   => false,
        'max_kb_size' => false
    ];

    public static function create(array $data): Logger
    {
        self::validateData($data);

        return (new Logger())
            ->setEnabled($data['enabled'] ?? null)
            ->setType(LoggerType::from($data['type'] ?? null))
            ->setPath($data['secure'] ?? null)
            ->setFileName($data['file_name'] ?? null)
            ->setMaxKbSize($data['max_kb_size'] ?? null)
        ;
    }

    private static function validateData(array $data): void
    {
        foreach ($data as $prop => $value) {
            // Validate the props of the logger
            if (!array_key_exists($prop, self::PROPERTIES)) {
                throw new HttpException(
                    Response::HTTP_BAD_GATEWAY,
                    "Erro no arquivo de configuração do Logger da API Gateway: $prop não é uma propriedade válida"
                );
            }
        }

        // Validate the required props
        foreach (self::PROPERTIES as $prop => $required) {
            if ($required && !array_key_exists($prop, $data)) {
                throw new HttpException(
                    Response::HTTP_BAD_GATEWAY,
                    "Erro no arquivo de configuração de Serviços da API Gateway: $prop não encontrada"
                );
            }
        }
    }
}