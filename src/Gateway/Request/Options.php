<?php

namespace App\Gateway\Request;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;

class Options
{
    private array $query;

    private array $json;

    private CookieJar $cookieJar;

    public function __construct()
    {       
        $this->cookieJar = new CookieJar();
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function setQuery(array $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function getJson(): array
    {
        return $this->json;
    }

    public function setJson(array $json): self
    {
        $this->json = $json;

        return $this;
    }

    public function getCookieJar(): CookieJar
    {
        return $this->cookieJar;
    }

    public function getCookieByName(string $cookie): ?SetCookie
    {
        return $this->cookieJar->getCookieByName($cookie);
    }

    public function setCookies(array $cookies, string $domain): self
    {
        foreach ($cookies as $key => $value) {
            $this->cookieJar->setCookie(
                new SetCookie([
                    'Name' => $key,
                    'Value' => $value,
                    'Domain' => $domain,
                    'HttpOnly' => true
                ])
            );
        }

        return $this;
    }

    public function toArray(): array
    {
        return [
            'query' => $this->query,
            'json' => $this->json,
            'cookies' => $this->cookieJar->toArray()
        ];
    }
}