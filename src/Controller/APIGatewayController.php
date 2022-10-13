<?php

namespace App\Controller;

use App\Gateway\AbstractAPIGateway;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class APIGatewayController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, AbstractAPIGateway $apiGateway): JsonResponse
    {
        return $apiGateway->authenticate($request);
    }

    #[Route('/{uri}', name: 'app_index', requirements: ['uri' => '^(?!/login).*$'])]
    public function index(Request $request, AbstractAPIGateway $apiGateway): JsonResponse
    {
        return $apiGateway->handle($request);
    }
}
