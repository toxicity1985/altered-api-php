<?php

namespace Toxicity\AlteredApi\tests\units\Service;

use atoum\atoum\test;
use Symfony\Component\RateLimiter\LimiterInterface;
use Toxicity\AlteredApi\Service\RateLimiterService;

class RateLimiterServiceTest extends test
{
    public function getTestedClassName()
    {
        return 'Toxicity\AlteredApi\Service\RateLimiterService';
    }

    public function testCreateReturnsLimiterInterface()
    {
        $limiter = RateLimiterService::create(10, 5);

        $this
            ->object($limiter)
                ->isInstanceOf(LimiterInterface::class)
        ;
    }

    public function testCreateWithDefaultInterval()
    {
        $limiter = RateLimiterService::create(20);

        $this
            ->object($limiter)
                ->isInstanceOf(LimiterInterface::class)
        ;
    }

    public function testCreateLimiterCanConsume()
    {
        $limiter = RateLimiterService::create(5, 60);

        // Should be able to consume up to limit
        for ($i = 0; $i < 5; $i++) {
            $rateLimit = $limiter->consume(1);
            $this
                ->boolean($rateLimit->isAccepted())
                    ->isTrue()
            ;
        }

        // Should be blocked after reaching limit
        $rateLimit = $limiter->consume(1);
        $this
            ->boolean($rateLimit->isAccepted())
                ->isFalse()
        ;
    }

    public function testCreateWithDifferentLimits()
    {
        $limiter1 = RateLimiterService::create(3, 60);
        $limiter2 = RateLimiterService::create(10, 60);

        // Limiter1 should block after 3
        for ($i = 0; $i < 3; $i++) {
            $limiter1->consume(1);
        }
        $rateLimit1 = $limiter1->consume(1);
        
        // Limiter2 should still accept (separate instance)
        $rateLimit2 = $limiter2->consume(1);

        $this
            ->boolean($rateLimit1->isAccepted())
                ->isFalse()
            ->boolean($rateLimit2->isAccepted())
                ->isTrue()
        ;
    }
}

