<?php

namespace App\Gateway\Configuration\Model;

class Route
{
    private string $name;

    private string $gatewayName;

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

    public function getGatewayName(): string
    {
        return $this->gatewayName;
    }

    public function setGatewayName(string $gatewayName): self
    {
        $this->gatewayName = $gatewayName;

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