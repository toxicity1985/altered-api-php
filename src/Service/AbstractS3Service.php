<?php

namespace Toxicity\AlteredApi\Service;

use Aws\S3\S3Client;
use Symfony\Component\HttpClient\HttpClient;

abstract class AbstractS3Service
{
    private S3Client $client;
    private string $bucket;
    private string $publicBaseUrl;

    protected function init(
        string $endpoint,
        string $accessKey,
        string $secretKey,
        string $bucket,
        string $publicBaseUrl,
        string $region = 'auto',
        bool $pathStyle = false,
    ): void {
        $this->bucket        = $bucket;
        $this->publicBaseUrl = rtrim($publicBaseUrl, '/');

        $this->client = new S3Client([
            'version'                 => 'latest',
            'region'                  => $region,
            'endpoint'                => $endpoint,
            'credentials'             => [
                'key'    => $accessKey,
                'secret' => $secretKey,
            ],
            'use_path_style_endpoint' => $pathStyle,
        ]);
    }

    public function uploadFromUrl(string $url, string $key): string
    {
        $http     = HttpClient::create(['timeout' => 30]);
        $response = $http->request('GET', $url);
        $content  = $response->getContent();
        $mimeType = $response->getHeaders()['content-type'][0] ?? 'image/jpeg';

        $this->client->putObject([
            'Bucket'      => $this->bucket,
            'Key'         => $key,
            'Body'        => $content,
            'ContentType' => $mimeType,
        ]);

        return $this->publicBaseUrl . '/' . $key;
    }

    public function exists(string $key): bool
    {
        return $this->client->doesObjectExist($this->bucket, $key);
    }

    public function publicUrl(string $key): string
    {
        return $this->publicBaseUrl . '/' . $key;
    }
}
