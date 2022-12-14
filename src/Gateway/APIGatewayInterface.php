<?php

namespace App\Gateway;

use App\Gateway\Configuration\APIGatewayConfiguration;
use App\Gateway\Request\JsonServiceRequest;
use Symfony\Component\HttpFoundation\Response;

interface APIGatewayInterface
{
    public function getConfiguration(): APIGatewayConfiguration;

    public function handle(JsonServiceRequest $request): Response;

    public function login(JsonServiceRequest $request): Response;

    public function logout(JsonServiceRequest $request): Response;
}