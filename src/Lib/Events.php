<?php

namespace Toxicity\AlteredApi\Lib;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Toxicity\AlteredApi\Exception\InvalidSearchCardRequestException;
use Toxicity\AlteredApi\Request\SearchEventRequest;
use Toxicity\AlteredApi\Service\ValidatorService;

class Events extends AlteredApiResource
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws InvalidSearchCardRequestException
     */
    public static function search(SearchEventRequest $searchEventRequest, ?string $locale = 'fr-fr'): array
    {
        $errors = ValidatorService::validateSearchRequest($searchEventRequest);
        if(sizeof($errors) > 0) {
            throw new InvalidSearchCardRequestException($errors);
        }

        return self::build()->getEventsBySearch($searchEventRequest, $locale);
    }
}
