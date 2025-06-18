<?php

namespace Toxicity\AlteredApi\Provider;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AlteredHttpClient
{
    private HttpClientInterface $client;
    private LimiterInterface $limiter;

    public function __construct()
    {
        $this->client = HttpClient::create();

        $rateLimiterFactory = new RateLimiterFactory([
            'id' => 'login',
            'policy' => 'fixed_window',
            'limit' => 20,
            'interval' => '10 seconds',
        ], new InMemoryStorage());

        $this->limiter = $rateLimiterFactory->create();

    }

    /**
     * @throws TransportExceptionInterface
     */
    public function request(string $method, string $url, array $options = [], bool $limiter = false): ResponseInterface
    {
        if($limiter) {
            $this->limiter->reserve(1)->wait();
        }
        return $this->client->request($method, 'https://api.altered.gg' . $url, $options);
    }
}
