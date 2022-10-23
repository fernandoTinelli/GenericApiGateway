<?php

namespace App\Gateway\Log;

use App\Gateway\Request\JsonServiceRequest;
use App\Gateway\Response\JsonServiceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestLogger
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function logRequest(JsonServiceRequest $request): void
    {
    }

    public function logResponse(JsonServiceResponse $response): void
    {
    }

    public static function getDataLog(Request $request = null, JsonServiceRequest $jsonRequest = null): array
    {
        return array_merge(
            self::getRequestDataLog($request),
            self::getJsonServiceRequestDataLog($jsonRequest)
        );
    }

    private static function getRequestDataLog(?Request $request): array
    {
        if (is_null($request)) {
            return [];
        }

        return [
            'http-request' => [
                'clientIp' => $request->getClientIp(),
                'host' => $request->getHost(),
                'method' => $request->getMethod(),
                'pathInfo' => $request->getPathInfo(),
                'headers' => $request->headers->all(),
                'query' => $request->query->all(),
                'form' => $request->request->all(),
                'content' => json_decode($request->getContent(), true) ?? [],
                'cookies' => $request->cookies->all(),
                'files' => $request->files->all(),
                'attributes' => $request->attributes->all()
            ]
        ];
    }

    private static function getJsonServiceRequestDataLog(?JsonServiceRequest $request): array
    {
        if (is_null($request)) {
            return [];
        }

        return [
            'gateway-request' => $request->toArray()
        ];
    }
}