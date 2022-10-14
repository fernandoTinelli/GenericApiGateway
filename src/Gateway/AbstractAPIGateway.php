<?php

namespace App\Gateway;

use App\Gateway\Configuration\APIGatewayConfiguration;
use App\Gateway\Configuration\Model\Route;
use App\Requester\Requester;
use App\Response\JsonServiceRequest;
use App\Response\JsonServiceResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Response\ServiceResponseStatus;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractAPIGateway implements APIGatewayInterface
{
    public static string $routesFilePath;

    public static string $servicesFilePath;

    protected Requester $requester;

    protected APIGatewayConfiguration $configuration;

    abstract public function init(APIGatewayConfiguration $configuration): void;

    public function handle(Request $request): JsonResponse
    {
        $uri = $request->getPathInfo();
        $ret = $this->validateRequest($uri);

        if ($ret instanceof JsonResponse) {
            return $ret;
        }

        if ($ret->isSecure()) {
            if (!$this->userHasValidAuthentication($request)) {
                return JsonServiceResponse::encode(new JsonServiceResponse(
                    status: ServiceResponseStatus::FAIL,
                    message: 'Você não está autenticado ou sua autenticação expirou'
                ));
            }
        }

        return $this->getResponse($ret, $request);
    }

    public function authenticate(Request $request): JsonResponse
    {
        $routeLogin = $this->configuration->getRoute('/login');
        
        $jsonResponse = $this->getResponse($routeLogin, $request);
        $jsonServiceReponse = JsonServiceResponse::decode($jsonResponse);

        if ($jsonServiceReponse->getStatus() === ServiceResponseStatus::SUCCESS) {
            $jar = new CookieJar();
            $cookieTokenJWT = $jar->getCookieByName('BEARER');
            if (!is_null($cookieTokenJWT)) {
                $response = new Response();
                $response->headers->setCookie(new Cookie(
                    name: 'BEARER',
                    value: $cookieTokenJWT->getValue(),
                    expire: time()*3600,
                    domain: 'localhost',
                    httpOnly: true
                ));
                $response->send();
            }
        }

        return $jsonResponse;
    }

    protected function getResponse(Route $route, Request $request): JsonResponse
    {
        $url = $this->configuration->getService($route->getServiceName())->getAddress()
            . $route->getName();

        $jsonServiceRequest = new JsonServiceRequest($url, $request);

        return JsonServiceResponse::encode($this->requester->request($jsonServiceRequest));
    }

    protected function userHasValidAuthentication(Request $request): bool
    {
        if (!$request->cookies->has('BEARER')) {
            return false;
        }

        $routeAuthentication = $this->configuration->getRoute('/authenticate');

        $url = $this->configuration->getService($routeAuthentication->getServiceName())->getAddress()
                . $routeAuthentication->getName();

        $jsonServiceRequest = new JsonServiceRequest($url, $request);
        
        $JsonServiceResponse = $this->requester->request($jsonServiceRequest);

        return $JsonServiceResponse->getStatus() === ServiceResponseStatus::SUCCESS;
    }

    protected function validateRequest(string $uri): Route | JsonResponse
    {
        $route = $this->configuration->getRoute($uri);
        if (is_null($route)) {
            return JsonServiceResponse::encode(new JsonServiceResponse(
                status: ServiceResponseStatus::FAIL,
                message: 'Recurso solicitado não encontrado'
            ));
        }

        $service = $this->configuration->getService($route->getServiceName());
        if (is_null($service)) {
            return JsonServiceResponse::encode(new JsonServiceResponse(
                status: ServiceResponseStatus::FAIL,
                message: 'Nenhum serviço encontrado para a recurso solicitado'
            ));
        }

        return $route;
    }
}