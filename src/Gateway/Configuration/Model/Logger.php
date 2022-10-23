<?php

namespace App\Gateway\Configuration\Model;

enum LoggerType: int
{
    case REQUEST = 1;
    case RESPONSE = 2;
    case BOTH = 3;
}

class Logger
{
    private bool $enabled;

    private LoggerType $type;

    private string $path;

    private string $fileName;

    private int $maxKbSize;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled ?? false;

        return $this;
    }

    public function getType(): LoggerType
    {
        return $this->type;
    }

    public function setType(?LoggerType $type): self
    {
        $this->type = $type ?? LoggerType::BOTH;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path ?? "";

        return $this;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName ?? "req_resp_log_.log";

        return $this;
    }

    public function getMaxKbSize(): int
    {
        return $this->maxKbSize;
    }

    public function setMaxKbSize(int $maxKbSize): self
    {
        $this->maxKbSize = $maxKbSize ?? 10000;

        return $this;
    }
}