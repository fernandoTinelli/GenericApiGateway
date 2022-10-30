<?php

namespace App\Gateway;

use App\Gateway\Configuration\APIGatewayConfiguration;
use App\Gateway\Request\JsonServiceRequest;
use App\Gateway\Requester\Requester;
use App\Gateway\Response\JsonServiceResponse;
use App\Gateway\Response\ServiceResponseStatus;
use App\Validator\AbstractRequestValidator;
use GuzzleHttp\Cookie\SetCookie;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractAPIGateway implements APIGatewayInterface
{
    public static string $configPath;

    protected APIGatewayConfiguration $configuration;

    protected Requester $requester;

    protected AbstractRequestValidator $validator;

    #[Required]
    private function init(
        APIGatewayConfiguration $configuration,
        Requester $requester
    ): void
    {
        $this->configuration = $configuration;
        $this->requester = $requester;
    }

    public function getConfiguration(): APIGatewayConfiguration
    {
        return $this->configuration;
    }

    public function handle(JsonServiceRequest $request): Response
    {       
        $violations = $this->validator->validate($request);
        if ($violations->hasViolations()) {
            return JsonServiceResponse::encode(
                new JsonServiceResponse(
                    status: ServiceResponseStatus::FAIL,
                    data: $violations->getAll(),
                    message: "Requisição Inválida."
                )
            );
        }

        if ($request->getRoute()->isSecure() && !$this->isAuthenticated($request)) {
            return JsonServiceResponse::encode(new JsonServiceResponse(
                status: ServiceResponseStatus::FAIL,
                message: 'Você não está autenticado ou sua autenticação expirou'
            ));
        }

        return JsonServiceResponse::encode($this->requester->request($request));
    }

    public function login(JsonServiceRequest $request): Response
    {     
        $routeLogin = $this->configuration->getRoute('/login');

        $clonedRequest = clone($request);
        $clonedRequest->changeRoute($routeLogin);
        $response = $this->requester->request($request);

        if ($response->getStatus() === ServiceResponseStatus::SUCCESS) {
            $this->sendCookie($request->getOptions()->getCookieByName('BEARER'));
        }

        return JsonServiceResponse::encode($response);
    }

    public function logout(JsonServiceRequest $request): Response
    {
        $this->sendCookie($request->getOptions()->getCookieByName('BEARER'), time() - 3600);

        return JsonServiceResponse::encode(
            new JsonServiceResponse()
        );
    }

    protected function isAuthenticated(JsonServiceRequest $request): bool
    {
        if (is_null($request->getOptions()->getCookieByName('BEARER'))) {
            return false;
        }

        $routeAuthentication = $this->configuration->getRoute('/authenticate');

        $clonedRequest = clone($request);
        $clonedRequest->changeRoute($routeAuthentication);
        $response = $this->requester->request($clonedRequest);

        return $response->getStatus() === ServiceResponseStatus::SUCCESS;
    }

    protected function sendCookie(?SetCookie $cookie, int $expire = null)
    {
        if (!is_null($cookie)) {
            $cookieResponse = new Response();
            $cookieResponse->headers->setCookie(
                new Cookie(
                    name: $cookie->getName(),
                    value: $cookie->getValue(),
                    expire: $expire ?? time() * 3600,
                    domain: $cookie->getDomain(),
                    httpOnly: $cookie->getHttpOnly()
                )
            );
            $cookieResponse->send();
        }
    }
}