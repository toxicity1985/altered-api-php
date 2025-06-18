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
    public static function byReference(string $reference, ?string $locale = 'fr-fr'): array
    {
        return self::build()->getCardByReference($reference, $locale);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public static function alternateCardsByReference(string $reference, ?string $locale = 'fr-fr'): array
    {
        return self::build()->getAlternateCardsByReference($reference, $locale);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws InvalidSearchCardRequestException
     */
    public static function search(SearchCardRequest $searchCardRequest, ?string $locale = 'fr-fr', ?string $token = null): array
    {
        $errors = ValidatorService::validateSearchRequest($searchCardRequest);
        if(sizeof($errors) > 0) {
            throw new InvalidSearchCardRequestException($errors);
        }

        return self::build()->getCardsBySearch($searchCardRequest, $locale, $token);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws InvalidSearchCardRequestException
     */
    public static function stats(SearchCardRequest $searchCardRequest, string $token, ?string $locale = 'fr-fr'): array
    {
        $errors = ValidatorService::validateSearchRequest($searchCardRequest);
        if(sizeof($errors) > 0) {
            throw new InvalidSearchCardRequestException($errors);
        }

        return self::build()->getCardStatsBySearch($searchCardRequest, $token, $locale);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public static function offers(string $reference, string $token, ?string $locale = 'fr-fr'): array
    {
        return self::build()->getCardOffersByReference($reference, $token, $locale);
    }
}
