<?php

namespace App\Gateway\Configuration\Model;

use App\Gateway\CircuitBreaker\CircuitBreakerInterface;
use App\Gateway\CircuitBreaker\Types\DefaultCircuitBreaker;
use Symfony\Component\VarExporter\Exception\ClassNotFoundException;

class Route
{
    private string $name;

    private string $serviceName;

    private bool $secure;

    private CircuitBreakerInterface $circuitBreaker;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    public function setServiceName(string $serviceName): self
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    public function isSecure(): bool
    {
        return $this->secure;
    }

    public function setSecure(bool $secure): self
    {
        $this->secure = $secure;

        return $this;
    }

    public function getCircuitBreaker(): CircuitBreakerInterface
    {
        return $this->circuitBreaker;
    }

    public function setCircuitBreaker(string $circuitBreakerClassName): self
    {
        try {
            $this->circuitBreaker = new ("\App\Gateway\CircuitBreaker\Types\$circuitBreakerClassName")();
        } catch (ClassNotFoundException $e) {
            $this->circuitBreaker = new DefaultCircuitBreaker();
        }
        
        return $this;
    }
}