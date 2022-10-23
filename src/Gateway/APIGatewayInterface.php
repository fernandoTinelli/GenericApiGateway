<?php

namespace App\Gateway;

use App\Gateway\Configuration\APIGatewayConfiguration;
use App\Gateway\Log\RequestLogger;
use App\Gateway\Requester\Requester;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface APIGatewayInterface
{
    public function init(
        APIGatewayConfiguration $configuration,
        Requester $requester
    ): void;

    public function handle(Request $request): Response;

    public function login(Request $request): Response;
}