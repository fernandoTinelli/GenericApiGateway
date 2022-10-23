<?php

namespace App\Gateway\Requester;

use App\Gateway\Request\JsonServiceRequest;
use App\Gateway\Response\JsonServiceResponse;
use Symfony\Component\HttpFoundation\Response;

interface RequesterInterface
{
    public function request(JsonServiceRequest $request): JsonServiceResponse | Response;
}