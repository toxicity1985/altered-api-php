<?php

namespace Toxicity\AlteredApi\Service;

class MinioService extends AbstractS3Service
{
    public function __construct()
    {
        $env       = fn(string $k): string => $_ENV[$k] ?? getenv($k) ?: '';
        $endpoint  = rtrim($env('MINIO_ENDPOINT') ?: 'http://localhost:9000', '/');
        $accessKey = $env('MINIO_USER') ?: $env('MINIO_ACCESS_KEY');
        $secretKey = $env('MINIO_PASSWORD') ?: $env('MINIO_SECRET_KEY');
        $bucket    = $env('MINIO_BUCKET') ?: 'altered-images';
        $publicUrl = $env('MINIO_PUBLIC_URL') ?: ($endpoint . '/' . $bucket);

        $this->init(
            endpoint:      $endpoint,
            accessKey:     $accessKey,
            secretKey:     $secretKey,
            bucket:        $bucket,
            publicBaseUrl: $publicUrl,
            region:        'us-east-1',
            pathStyle:     true,
        );
    }
}
