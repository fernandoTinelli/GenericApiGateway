<?php

namespace App\Gateway\Configuration\Model;

class Route
{
    private string $name;

    private string $serviceName;

    private bool $secure;

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

    public function setSecure(bool $secure = true): self
    {
        $this->secure = $secure;

        return $this;
    }
}