<?php

namespace Toxicity\AlteredApi\Service;

use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class RateLimiterService
{
    public static function create(int $limit, int $interval = 10): LimiterInterface
    {
        $rateLimiterFactory = new RateLimiterFactory([
            'id' => 'login',
            'policy' => 'fixed_window',
            'limit' => $limit,
            'interval' => $interval . ' seconds',
        ], new InMemoryStorage());

        return $rateLimiterFactory->create();
    }
}