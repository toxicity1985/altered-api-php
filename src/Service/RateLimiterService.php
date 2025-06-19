<?php

namespace Toxicity\AlteredApi\Service;

use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class RateLimiterService
{
    public static function create(int $interval): LimiterInterface
    {
        $rateLimiterFactory = new RateLimiterFactory([
            'id' => 'login',
            'policy' => 'fixed_window',
            'limit' => 15,
            'interval' => '10 seconds',
        ], new InMemoryStorage());

        return $rateLimiterFactory->create();
    }
}