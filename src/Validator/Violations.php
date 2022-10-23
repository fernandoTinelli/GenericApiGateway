<?php

namespace App\Validator;

class Violations
{
    private array $violations;

    public function __construct()
    {
        $this->violations = [];
    }

    public function add(string $name, string $message): self
    {
        $this->violations[$name] = $message;

        return $this;
    }

    public function getAll(): array
    {
        return $this->violations;
    }

    public function getMessage(string $name): bool | string
    {
        return $this->violations[$name] ?? false;
    }

    public function hasViolations(): bool
    {
        return count($this->violations) > 0;
    }
}