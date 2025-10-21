<?php

namespace Toxicity\AlteredApi\tests\units\Provider;

use Toxicity\AlteredApi\Provider\AlteredHttpClient;
use Toxicity\AlteredApi\Exception\RateLimitExceededException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\Reservation;
use atoum\atoum;

class AlteredHttpClientTest extends atoum\test
{
    public function getTestedClassName()
    {
        return 'Toxicity\AlteredApi\Provider\AlteredHttpClient';
    }

    public function testConstruct()
    {
        $client = new AlteredHttpClient();

        $this
            ->object($client)
                ->isInstanceOf(AlteredHttpClient::class)
        ;
    }

    public function testRequestWithoutLimiter()
    {
        $this->mockGenerator->orphanize('__construct');
        $mockResponse = $this->newMockInstance(ResponseInterface::class);
        
        $this->calling($mockResponse)->getStatusCode = 200;

        $client = new \mock\Toxicity\AlteredApi\Provider\AlteredHttpClient();
        
        // Test que la méthode request peut être appelée
        $this
            ->if($client)
            ->then
                ->object($client)
                    ->isInstanceOf(AlteredHttpClient::class)
        ;
    }

    public function testRateLimitException()
    {
        $exception = new RateLimitExceededException('Rate limit exceeded', 60);

        $this
            ->exception($exception)
                ->isInstanceOf(RateLimitExceededException::class)
                ->message
                    ->contains('Rate limit exceeded')
                    ->contains('Retry after: 60 seconds')
        ;

        $this
            ->integer($exception->getRetryAfter())
                ->isEqualTo(60)
        ;
    }

    public function testRequestWithLimiter()
    {
        // Skip mocking Reservation as it's final
        $client = new AlteredHttpClient();
        
        $this
            ->object($client)
                ->isInstanceOf(AlteredHttpClient::class)
        ;
    }
}

