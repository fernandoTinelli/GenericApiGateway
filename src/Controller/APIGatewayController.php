<?php

namespace App\Controller;

use App\Gateway\AbstractAPIGateway;
use App\Gateway\Request\JsonServiceRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class APIGatewayController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(Request $request, AbstractAPIGateway $apiGateway): Response
    {
        return $apiGateway->login(new JsonServiceRequest($request, $apiGateway->getConfiguration()));
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(Request $request, AbstractAPIGateway $apiGateway): Response
    {
        return $apiGateway->logout(new JsonServiceRequest($request, $apiGateway->getConfiguration()));
    }

    #[Route('/{uri}', name: 'app_index', requirements: ['uri' => '^(?!(/login)|(/logout).*$'])]
    public function index(Request $request, AbstractAPIGateway $apiGateway): Response
    {
        return $apiGateway->handle(new JsonServiceRequest($request, $apiGateway->getConfiguration()));
    }
}
