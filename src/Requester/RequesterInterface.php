<?php

namespace App\Requester;

use App\Response\JsonServiceResponse;

interface RequesterInterface
{
    public function request(string $url, string $method = 'GET', array $options = []): JsonServiceResponse;
}