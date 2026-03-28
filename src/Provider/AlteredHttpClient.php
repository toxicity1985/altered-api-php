<?php

namespace Toxicity\AlteredApi\Provider;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Toxicity\AlteredApi\Exception\RateLimitExceededException;
use Toxicity\AlteredApi\Service\ProxyService;

class AlteredHttpClient
{
    private const MAX_PROXY_RETRIES = 3;

    private HttpClientInterface $client;
    private ?string $currentProxy;

    public function __construct(?string $proxy = null, private readonly ?ProxyService $proxyService = null)
    {
        $this->currentProxy = $proxy ?? getenv('ALTERED_PROXY') ?: null;
        $this->client = $this->buildClient($this->currentProxy);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws RateLimitExceededException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function request(string $method, string $url, array $options = [], ?LimiterInterface $limiter = null): ResponseInterface
    {
        $limiter?->reserve(1)->wait();

        $attempts = 0;

        while (true) {
            try {
                $response = $this->client->request($method, 'https://api.altered.gg' . $url, $options);

                $statusCode = $response->getStatusCode();

                if ($statusCode === 429) {
                    $serverMessage = '';
                    $retryAfter = 0;

                    try {
                        $content = $response->toArray(false);
                        $serverMessage = $content['message'] ?? $content['error'] ?? $content['detail'] ?? 'Rate limit exceeded';
                    } catch (DecodingExceptionInterface $e) {
                        $serverMessage = $response->getContent(false);
                    }

                    $retryAfterHeader = $response->getHeaders()['retry-after'] ?? null;
                    if ($retryAfterHeader && is_array($retryAfterHeader) && count($retryAfterHeader) > 0) {
                        $retryAfter = (int) $retryAfterHeader[0];
                    }

                    throw new RateLimitExceededException($serverMessage, $retryAfter);
                }

                if ($statusCode === 403 && $this->proxyService !== null && $attempts < self::MAX_PROXY_RETRIES) {
                    echo "Proxy returned 403, switching proxy...\n";
                    $this->switchProxy();
                    $attempts++;
                    continue;
                }

                return $response;

            } catch (TransportExceptionInterface $e) {
                if ($this->proxyService !== null && $attempts < self::MAX_PROXY_RETRIES) {
                    echo "Proxy connection failed ({$e->getMessage()}), switching proxy...\n";
                    $this->switchProxy();
                    $attempts++;
                    continue;
                }
                throw $e;
            } catch (ClientExceptionInterface $e) {
                if (str_contains($e->getMessage(), '429')) {
                    throw new RateLimitExceededException(
                        'Rate limit exceeded: ' . $e->getMessage(),
                        0,
                        '',
                        429,
                        $e
                    );
                }
                throw $e;
            }
        }
    }

    private function switchProxy(): void
    {
        $newProxy = $this->proxyService->getNextWorkingProxy();
        if ($newProxy === null) {
            echo "No more proxies available, continuing without proxy.\n";
            $this->currentProxy = null;
        } else {
            $this->currentProxy = $newProxy;
        }
        $this->client = $this->buildClient($this->currentProxy);
    }

    private function buildClient(?string $proxy): HttpClientInterface
    {
        $options = [];
        if ($proxy) {
            $options['proxy'] = $proxy;
            $options['verify_peer'] = false;
        }
        return HttpClient::create($options);
    }
}
