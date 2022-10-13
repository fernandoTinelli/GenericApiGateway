<?php

namespace App\Response;

class JsonServiceRequest
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public static function decode(string $requestContent): self
    {
        return (new self(json_decode($requestContent), true));
    }
}