<?php

namespace App\Requester;

use App\Response\JsonServiceRequest;
use App\Response\JsonServiceResponse;
use App\Response\ServiceResponseStatus;
use GuzzleHttp\Client;

class Requester implements RequesterInterface
{
    public function request(JsonServiceRequest $request): JsonServiceResponse
    {
        try {
            $client = new Client(['cookies' => true]);

            $response = $client->request(
                uri: $request->getUrl(),
                method: $request->getMethod(),
                options: $request->getServiceRequestOptions()->getOptions()
            );

            $response = json_decode((string) $response->getBody(), true);

            return new JsonServiceResponse(
                status: ServiceResponseStatus::from($response['status']),
                data: $response['data'],
                message: $response['message']
            );

        } catch (\Throwable $th) {
            if (!is_null($request->getCircuitBreaker())) {
                return $request->getCircuitBreaker()->doDummy($request);
            }

            return new JsonServiceResponse(
                status: ServiceResponseStatus::ERROR,
                message: "Erro ao realizar a solicitação __MESSAGE__EXCEPTION: " . $th->getMessage()
            );
        }
    }
}