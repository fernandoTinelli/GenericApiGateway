<?php

namespace App\Gateway;

use App\Gateway\Configuration\APIGatewayConfiguration;
use App\Gateway\Request\JsonServiceRequest;
use App\Gateway\Requester\Requester;
use App\Gateway\Response\JsonServiceResponse;
use App\Gateway\Response\ServiceResponseStatus;
use App\Validator\AbstractRequestValidator;
use GuzzleHttp\Cookie\CookieJar;
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
    public function init(
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
        // $jsonServiceRequest = new JsonServiceRequest($request, $this->configuration);
        
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

        if ($request->getRoute()->isSecure()) {
            if (!$this->isAuthenticated($request)) {
                return JsonServiceResponse::encode(new JsonServiceResponse(
                    status: ServiceResponseStatus::FAIL,
                    message: 'Você não está autenticado ou sua autenticação expirou'
                ));
            }
        }

        return JsonServiceResponse::encode($this->requester->request($request));
    }

    public function login(JsonServiceRequest $request): Response
    {        
        $response = $this->requester->request($request);

        if ($response->getStatus() === ServiceResponseStatus::SUCCESS) {
            $jar = new CookieJar();
            $cookieTokenJWT = $jar->getCookieByName('BEARER');
            if (!is_null($cookieTokenJWT)) {
                $cookieResponse = new Response();
                $cookieResponse->headers->setCookie(new Cookie(
                    name: 'BEARER',
                    value: $cookieTokenJWT->getValue(),
                    expire: time()*3600,
                    domain: 'localhost',
                    httpOnly: true
                ));
                $cookieResponse->send();
            }
        }

        return JsonServiceResponse::encode($response);
    }

    protected function isAuthenticated(JsonServiceRequest $request): bool
    {
        if (is_null($request->getOptions()->getCookieByName('BEARER'))) {
            return false;
        }

        $routeAuthentication = $this->configuration->getRoute('/authenticate');

        $clonedRequest = clone($request);
        $clonedRequest->changeRoute($routeAuthentication);
        $jsonServiceResponse = $this->requester->request($clonedRequest);

        return $jsonServiceResponse->getStatus() === ServiceResponseStatus::SUCCESS;
    }
}