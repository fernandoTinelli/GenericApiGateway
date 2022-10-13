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
    protected Requester $requester;

    protected APIGatewayConfiguration $configuration;

    #[Required]
    public function init(APIGatewayConfiguration $configuration): void
    {
        $this->configuration = $configuration;   
    }

    public function handle(Request $request): JsonResponse
    {
        $uri = $request->getPathInfo();
        $ret = $this->validateRequest($uri);

        if ($ret instanceof JsonResponse) {
            return $ret;
        }

        if ($ret->isSecure()) {
            if (!$this->userHasValidAuthentication()) {
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
        $options = [];
        $query = $request->query->all();
        $form = $request->request->all();
        $content = JsonServiceRequest::decode($request->getContent());

        if (!empty($query)) {
            $options['query'] = $query;
        }

        if (!empty($form)) {
            $options['request'] = $form;
        }

        if (!empty($content->getData())) {
            $options['body'] = $content->getData();
        }

        $url = $this->configuration->getService($route->getGatewayName())->getAddress()
            . $route->getName();

        return JsonServiceResponse::encode($this->requester->request(
            $url,
            $request->getMethod(),
            $options
        ));
    }

    protected function userHasAuthentication(): bool
    {
        $jar = new CookieJar();
        return is_null($jar->getCookieByName('BEARER'));
    }

    protected function userHasValidAuthentication(): bool
    {
        if (!$this->userHasAuthentication()) {
            return false;
        }

        $jar = new CookieJar();
        $cookieTokenJWT = $jar->getCookieByName('BEARER');

        $routeAuthentication = $this->configuration->getRoute('/authenticate');
        $jsonResponse = $this->getResponse($routeAuthentication, new Request([
            CookieJar::fromArray($cookieTokenJWT->toArray(), 'localhost')
        ]));
        $jsonServiceReponse = JsonServiceResponse::decode($jsonResponse);

        return $jsonServiceReponse->getStatus() === ServiceResponseStatus::SUCCESS;
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

        $service = $this->configuration->getService($route->getGatewayName());
        if (is_null($service)) {
            return JsonServiceResponse::encode(new JsonServiceResponse(
                status: ServiceResponseStatus::FAIL,
                message: 'Nenhum serviço encontrado para a recurso solicitado'
            ));
        }

        return $route;
    }
}