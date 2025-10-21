<?php

namespace Toxicity\AlteredApi\tests\units\Exception;

use Toxicity\AlteredApi\Exception\RateLimitExceededException;
use atoum\atoum;

class RateLimitExceededExceptionTest extends atoum\test
{
    public function getTestedClassName()
    {
        return 'Toxicity\AlteredApi\Exception\RateLimitExceededException';
    }

    public function testConstructWithDefaultValues()
    {
        $exception = new RateLimitExceededException('Rate limit exceeded');

        $this
            ->exception($exception)
                ->isInstanceOf(\Exception::class)
                ->message
                    ->contains('Rate limit exceeded')
            ->integer($exception->getCode())
                ->isEqualTo(429)
            ->string($exception->getServerMessage())
                ->isEqualTo('Rate limit exceeded')
            ->integer($exception->getRetryAfter())
                ->isEqualTo(0)
        ;
    }

    public function testConstructWithRetryAfter()
    {
        $exception = new RateLimitExceededException('Too many requests', 120);

        $this
            ->exception($exception)
                ->message
                    ->contains('Too many requests')
                    ->contains('Retry after: 120 seconds')
            ->string($exception->getServerMessage())
                ->isEqualTo('Too many requests')
            ->integer($exception->getRetryAfter())
                ->isEqualTo(120)
        ;
    }

    public function testConstructWithAllParameters()
    {
        $previousException = new \Exception('Previous error');
        $exception = new RateLimitExceededException(
            'API rate limit exceeded',
            60,
            'Custom message',
            500,
            $previousException
        );

        $this
            ->exception($exception)
                ->hasMessage('Custom message')
            ->string($exception->getServerMessage())
                ->isEqualTo('API rate limit exceeded')
            ->integer($exception->getRetryAfter())
                ->isEqualTo(60)
            ->integer($exception->getCode())
                ->isEqualTo(500)
            ->object($exception->getPrevious())
                ->isIdenticalTo($previousException)
        ;
    }

    public function testGetServerMessage()
    {
        $serverMsg = 'Too many requests from your IP';
        $exception = new RateLimitExceededException($serverMsg);

        $this
            ->string($exception->getServerMessage())
                ->isEqualTo($serverMsg)
        ;
    }

    public function testGetRetryAfter()
    {
        $exception = new RateLimitExceededException('Test', 300);

        $this
            ->integer($exception->getRetryAfter())
                ->isEqualTo(300)
        ;
    }

    public function testDefaultRetryAfter()
    {
        $exception = new RateLimitExceededException('Test');

        $this
            ->integer($exception->getRetryAfter())
                ->isEqualTo(0)
        ;
    }

    public function testMessageFormatWithRetryAfter()
    {
        $exception = new RateLimitExceededException('Server busy', 60);

        $this
            ->string($exception->getMessage())
                ->contains('Rate limit exceeded')
                ->contains('Server busy')
                ->contains('Retry after: 60 seconds')
        ;
    }

    public function testMessageFormatWithoutRetryAfter()
    {
        $exception = new RateLimitExceededException('Server busy');

        $this
            ->string($exception->getMessage())
                ->contains('Rate limit exceeded')
                ->contains('Server busy')
                ->notContains('Retry after')
        ;
    }
}

