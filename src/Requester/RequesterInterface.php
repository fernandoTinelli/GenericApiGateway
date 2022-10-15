<?php

namespace App\Requester;

use App\Response\JsonServiceRequest;
use App\Response\JsonServiceResponse;
use Symfony\Component\HttpFoundation\Response;

interface RequesterInterface
{
    public function request(JsonServiceRequest $request): JsonServiceResponse | Response;
}