<?php

namespace App\Requester;

use App\Response\JsonServiceResponse;
use App\Response\ServiceResponseStatus;
use GuzzleHttp\Client;

class Requester implements RequesterInterface
{
    public function request(string $url, string $method = 'GET', array $options = []): JsonServiceResponse
    {
        try {
            $client = new Client();

            $response = $client->request(
                uri: $url,
                method: $method,
                options: $options
            );

            $response = json_decode((string) $response->getBody(), true);

            return new JsonServiceResponse(
                // status: ServiceResponseStatus::from($response['status']),
                status: ServiceResponseStatus::SUCCESS,
                // data: $response['data'],
                data: $response,
                // message: $response['message']
                message: $response['Message']
            );

        } catch (\Throwable $th) {
            return new JsonServiceResponse(
                status: ServiceResponseStatus::ERROR,
                message: "Erro ao realizar a solicitação __MESSAGE__EXCEPTION: " . $th->getMessage()
            );
        }
    }
}