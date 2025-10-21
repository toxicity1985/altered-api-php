<?php

namespace Toxicity\AlteredApi\Exception;

use Exception;

class RateLimitExceededException extends Exception
{
    private string $serverMessage;
    private int $retryAfter;

    public function __construct(string $serverMessage, int $retryAfter = 0, string $message = "", int $code = 429, ?Exception $previous = null)
    {
        $this->serverMessage = $serverMessage;
        $this->retryAfter = $retryAfter;
        
        if (empty($message)) {
            $message = "Rate limit exceeded. Server message: " . $serverMessage;
            if ($retryAfter > 0) {
                $message .= " Retry after: " . $retryAfter . " seconds";
            }
        }
        
        parent::__construct($message, $code, $previous);
    }

    public function getServerMessage(): string
    {
        return $this->serverMessage;
    }

    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }
}
