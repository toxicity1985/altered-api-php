<?php

namespace Toxicity\AlteredApi\Lib;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Toxicity\AlteredApi\Exception\InvalidSearchCardRequestException;
use Toxicity\AlteredApi\Request\SearchCardRequest;
use Toxicity\AlteredApi\Service\ValidatorService;

class Cards extends AlteredApiResource
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public static function byId(string $id, ?string $locale = 'fr-fr'): array
    {
        return self::build()->getCardById($id, $locale);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public static function alternateCardsById(string $id, ?string $locale = 'fr-fr'): array
    {
        return self::build()->getAlternateCardsById($id, $locale);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws InvalidSearchCardRequestException
     */
    public static function search(SearchCardRequest $searchCardRequest, ?string $locale = 'fr-fr'): array
    {
        $errors = ValidatorService::validateSearchRequest($searchCardRequest);
        if(sizeof($errors) > 0) {
            throw new InvalidSearchCardRequestException($errors);
        }

        return self::build()->getCardsBySearch($searchCardRequest, $locale);
    }
}
