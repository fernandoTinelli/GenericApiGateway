<?php

namespace App\Gateway\Requester;

use App\Gateway\Request\JsonServiceRequest;
use App\Gateway\Response\JsonServiceResponse;

interface RequesterInterface
{
    public function request(JsonServiceRequest $request): JsonServiceResponse;
}