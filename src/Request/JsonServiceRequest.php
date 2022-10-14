<?php

namespace App\Response;

use App\Request\ServiceRequestOptions;
use Symfony\Component\HttpFoundation\Request;

class JsonServiceRequest
{
    private string $url;

    private string $method;

    private ServiceRequestOptions $options;

    public function __construct(string $url, Request $request)
    {
        $this->url = $url;
        $this->method = $request->getMethod();
        $this->options = new ServiceRequestOptions(
            query: $request->query->all(),
            formParams: $request->request->all(),
            json: json_decode($request->getContent(), true),
            cookies: $request->cookies->all()
        );
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getServiceRequestOptions(): ServiceRequestOptions
    {
        return $this->options;
    }
}