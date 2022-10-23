<?php

namespace App\Gateway\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

enum ServiceResponseStatus: int
{
    case SUCCESS      = 200;
    case FAIL         = 400;
    case ERROR        = 500;
    case UNAUTHORIZED = 404;
}

class JsonServiceResponse
{
    private ServiceResponseStatus $status;

    private ?array $data;

    private ?string $message;
    
    public function __construct(
        ServiceResponseStatus $status = ServiceResponseStatus::SUCCESS,
        array $data = [],
        string $message = null
    ) {
        $this->status = $status;
        $this->data = $data;
        $this->message = $message;
    }

    public function getStatus(): ServiceResponseStatus
    {
        return $this->status;
    }

    public static function encode(self $response): JsonResponse
    {
        return new JsonResponse([
            'status'  => $response->status->value,
            'data'    => $response->data,
            'message' => $response->message
        ]);
    }

    public static function decode(JsonResponse $response): self
    {
        $json = json_decode($response->getContent(), true);

        return new self(
            $json['status'],
            $json['data'],
            $json['message']
        );
    }
}