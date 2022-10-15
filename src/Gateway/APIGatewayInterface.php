<?php

namespace App\Gateway;

use App\Gateway\Configuration\APIGatewayConfiguration;
use App\Requester\Requester;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface APIGatewayInterface
{
    public function init(APIGatewayConfiguration $configuration, Requester $requester): void;

    public function handle(Request $request): JsonResponse;

    public function authenticate(Request $request): JsonResponse;
}