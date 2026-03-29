<?php

namespace Toxicity\AlteredApi\Service;

class CloudflareR2Service extends AbstractS3Service
{
    public function __construct()
    {
        $env       = fn(string $k): string => $_ENV[$k] ?? getenv($k) ?: '';
        $accountId = $env('R2_ACCOUNT_ID');
        $accessKey = $env('R2_ACCESS_KEY');
        $secretKey = $env('R2_SECRET_KEY');
        $bucket    = $env('R2_BUCKET') ?: 'altered-images';
        $publicUrl = rtrim($env('R2_PUBLIC_URL'), '/');

        $this->init(
            endpoint:      "https://{$accountId}.r2.cloudflarestorage.com",
            accessKey:     $accessKey,
            secretKey:     $secretKey,
            bucket:        $bucket,
            publicBaseUrl: $publicUrl,
            region:        'auto',
            pathStyle:     true,
        );
    }
}
