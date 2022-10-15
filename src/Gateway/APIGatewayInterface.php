<?php

namespace App\Gateway;

use App\Gateway\Configuration\APIGatewayConfiguration;
use App\Requester\Requester;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface APIGatewayInterface
{
    public function init(APIGatewayConfiguration $configuration, Requester $requester): void;

    public function handle(Request $request): Response;

    public function authenticate(Request $request): Response;
}