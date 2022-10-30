<?php

namespace App\Gateway\Requester;

use App\Gateway\Request\JsonServiceRequest;
use App\Gateway\Response\JsonServiceResponse;
use App\Gateway\Response\ServiceResponseStatus;
use GuzzleHttp\Client;

class Requester implements RequesterInterface
{
    public function request(JsonServiceRequest $request): JsonServiceResponse
    {
        try {
            $client = new Client(['cookies' => true]);

            $response = $client->request(
                uri: $request->getService()->getAddress() . $request->getRoute()->getName(),
                method: $request->getMethod(),
                options: $request->getOptions()->toArray()
            );

            $response = json_decode((string) $response->getBody(), true);

            return new JsonServiceResponse(
                status: ServiceResponseStatus::from($response['status']),
                data: $response['data'],
                message: $response['message']
            );
        } catch (\Throwable $th) {
            return $request->getRoute()->getCircuitBreaker()->handleBreak($request, $th->getMessage());
        }
    }
}