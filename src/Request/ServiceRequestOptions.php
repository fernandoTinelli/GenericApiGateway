<?php

namespace App\Request;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;

class ServiceRequestOptions
{
    private array $query;

    private array $formParams;

    private array $json;

    private CookieJar $cookieJar;

    public function __construct(array $query = [], array $formParams = [], array $json = [], array $cookies = [])
    {
        $this->query = $query;
        $this->formParams = $formParams;
        $this->json = $json;
        
        $this->cookieJar = new CookieJar();
        foreach ($cookies as $key => $value) {
            $this->cookieJar->setCookie(
                new SetCookie([
                    'Name' => $key,
                    'Value' => $value,
                    'HttpOnly' => true
                ])
            );
        }
    }

    public function getOptions(): array
    {
        return [
            'query' => $this->query,
            'form_params' => $this->formParams,
            'json' => $this->json,
            'cookies' => $this->cookieJar
        ];
    }

    public function getCookie(string $name): ?SetCookie
    {
        return $this->cookieJar->getCookieByName($name);
    }
}