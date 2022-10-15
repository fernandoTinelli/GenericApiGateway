<?php

namespace App\Response;

use App\Gateway\CircuitBreaker\CircuitBreakerInterface;
use App\Request\ServiceRequestOptions;
use Symfony\Component\HttpFoundation\Request;

class JsonServiceRequest
{
    private string $url;

    private string $method;

    private ServiceRequestOptions $options;

    private CircuitBreakerInterface $circuitBreaker;

    public function __construct(string $url, Request $request, CircuitBreakerInterface $circuitBreaker)
    {
        $this->url = $url;
        $this->method = $request->getMethod();
        $this->options = new ServiceRequestOptions(
            query: $request->query->all(),
            formParams: $request->request->all(),
            json: json_decode($request->getContent(), true),
            cookies: $request->cookies->all()
        );
        $this->circuitBreaker = $circuitBreaker;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getServiceRequestOptions(): ServiceRequestOptions
    {
        return $this->options;
    }

    public function getCircuitBreaker(): CircuitBreakerInterface
    {
        return $this->circuitBreaker;
    }
}