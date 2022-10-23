<?php

namespace App\Gateway;

use App\Gateway\Configuration\APIGatewayConfiguration;
use App\Gateway\Log\RequestLogger;
use App\Gateway\Request\JsonServiceRequest;
use App\Gateway\Requester\Requester;
use App\Gateway\Response\JsonServiceResponse;
use App\Gateway\Response\ServiceResponseStatus;
use App\Validator\AbstractRequestValidator;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
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

    public function handle(Request $request): Response
    {
        $jsonServiceRequest = new JsonServiceRequest($request, $this->configuration);
        
        $violations = $this->validator->validate($jsonServiceRequest);
        if ($violations->hasViolations()) {
            return JsonServiceResponse::encode(
                new JsonServiceResponse(
                    status: ServiceResponseStatus::FAIL,
                    data: $violations->getAll(),
                    message: "Requisição Inválida."
                )
            );
        }

        if ($jsonServiceRequest->getRoute()->isSecure()) {
            if (!$this->isAuthenticated($jsonServiceRequest)) {
                // return JsonServiceResponse::encode(new JsonServiceResponse(
                //     status: ServiceResponseStatus::FAIL,
                //     message: 'Você não está autenticado ou sua autenticação expirou'
                // ));
            }
        }

        return $this->getResponse($jsonServiceRequest);
    }

    public function login(Request $request): Response
    {
        $jsonServiceRequest = new JsonServiceRequest($request, $this->configuration);
        
        $response = $this->getResponse($jsonServiceRequest);

        $jsonServiceReponse = JsonServiceResponse::decode($response);

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

        return $response;
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

        if ($jsonServiceResponse instanceof Response) {
            return false;
        }

        return $jsonServiceResponse->getStatus() === ServiceResponseStatus::SUCCESS;
    }

    protected function getResponse(JsonServiceRequest $request): Response
    {
        $response = $this->requester->request($request);

        if ($response instanceof Response) {
            return $response;
        }
        
        return JsonServiceResponse::encode($response);
    }
}