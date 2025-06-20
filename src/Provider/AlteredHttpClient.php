<?php

namespace Toxicity\AlteredApi\Provider;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AlteredHttpClient
{
    private HttpClientInterface $client;

    public function __construct()
    {
        $this->client = HttpClient::create();
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function request(string $method, string $url, array $options = [], ?LimiterInterface $limiter = null): ResponseInterface
    {
        $limiter?->reserve(1)->wait();
        return $this->client->request($method, 'https://api.altered.gg' . $url, $options);
    }
}
