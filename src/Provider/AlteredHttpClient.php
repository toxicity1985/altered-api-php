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

class AlteredHttpClient
{
    private HttpClientInterface $client;

    public function __construct()
    {
        $this->client = HttpClient::create();
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
        
        try {
            $response = $this->client->request($method, 'https://api.altered.gg' . $url, $options);
            
            // Vérifier le status code pour détecter l'erreur 429
            $statusCode = $response->getStatusCode();
            
            if ($statusCode === 429) {
                // Récupérer le message du serveur
                $serverMessage = '';
                $retryAfter = 0;
                
                try {
                    $content = $response->toArray(false); // false pour ne pas lever d'exception sur les codes d'erreur
                    $serverMessage = $content['message'] ?? $content['error'] ?? $content['detail'] ?? 'Rate limit exceeded';
                } catch (DecodingExceptionInterface $e) {
                    // Si on ne peut pas décoder le JSON, utiliser le contenu brut
                    $serverMessage = $response->getContent(false);
                }
                
                // Récupérer l'en-tête Retry-After si disponible
                $retryAfterHeader = $response->getHeaders()['retry-after'] ?? null;
                if ($retryAfterHeader && is_array($retryAfterHeader) && count($retryAfterHeader) > 0) {
                    $retryAfter = (int) $retryAfterHeader[0];
                }
                
                throw new RateLimitExceededException($serverMessage, $retryAfter);
            }
            
            return $response;
            
        } catch (ClientExceptionInterface $e) {
            // Vérifier si c'est une erreur 429 dans les exceptions client
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
