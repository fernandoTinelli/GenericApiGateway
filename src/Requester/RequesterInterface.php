<?php

namespace App\Requester;

use App\Response\JsonServiceRequest;
use App\Response\JsonServiceResponse;

interface RequesterInterface
{
    public function request(JsonServiceRequest $request): JsonServiceResponse;
}