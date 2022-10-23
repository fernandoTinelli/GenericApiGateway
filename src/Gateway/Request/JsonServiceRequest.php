<?php

namespace App\Gateway\Request;

use App\Gateway\Configuration\APIGatewayConfiguration;
use App\Gateway\Configuration\Model\Route;
use App\Gateway\Configuration\Model\Service;
use Symfony\Component\HttpFoundation\Request;

class JsonServiceRequest
{
    private APIGatewayConfiguration $configuration;

    private Route $route;

    private Service $service;

    private string $method;

    private Options $options; 

    public function __construct(
        Request $request,
        APIGatewayConfiguration $configuration
    )
    {
        $this->configuration = $configuration;
        $this->route = $configuration->getRoute($request->getPathInfo());
        $this->service = $configuration->getService($this->route->getServiceName());
        $this->method = $request->getMethod();
        $this->options = (new Options())
            ->setQuery($request->query->all() ?? [])
            ->setJson(json_decode($request->getContent(), true) ?? [])
            ->setCookies($request->cookies->all() ?? [], $request->getHost())
        ;
    }

    public function getRoute(): Route
    {
        return $this->route;
    }

    public function getService(): Service
    {
        return $this->service;
    }

    public function changeRoute(Route $route): self
    {
        $this->route = $route;
        $this->service = $this->configuration->getService($this->route->getServiceName());

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    public function setOptions(Options $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'url' => $this->service->getAddress() . $this->route->getName(),
            'method' => $this->method,
            'circuit_breaker' => $this->circuitBreaker::class
        ];
    }
}