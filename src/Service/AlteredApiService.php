<?php

namespace Toxicity\AlteredApi\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Toxicity\AlteredApi\Provider\AlteredHttpClient;
use Toxicity\AlteredApi\Request\SearchCardRequest;

readonly class AlteredApiService
{
    public function __construct(private AlteredHttpClient $alteredHttpClient)
    {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getSets(?string $locale = 'fr-fr'): array
    {
        $url = $this->buildUrl('https://api.altered.gg/card_sets', $locale);

        $sets = [];

        $response = $this->alteredHttpClient->request('GET', $url);

        $content = $response->toArray();

        if (count($content['hydra:member']) > 0) {
            $sets = array_merge($sets, $content['hydra:member']);
        }

        return $sets;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getCardById(string $id, ?string $locale = null): array
    {
        $url = $this->buildUrl('https://api.altered.gg/cards/' . $id, $locale);

        $response = $this->alteredHttpClient->request('GET', $url);
        return $response->toArray();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getCardsBySearch(SearchCardRequest $searchCardRequest, ?string $locale = 'fr-fr'): array
    {
        $page = 1;
        $empty = false;
        $cards = [];
        $options = [];

        $url = $this->buildUrl('https://api.altered.gg/cards' . $searchCardRequest->getUrlParameters(), $locale);


        while (!$empty) {
            $response = $this->alteredHttpClient->request('GET', $url . '&itemsPerPage=36&order[reference]=ASC&page=' . $page . '&locale=' . $locale, $options);

            $content = $response->toArray();

            if (count($content['hydra:member']) > 0) {
                $cards = array_merge($cards, $content['hydra:member']);
                $page++;
            } else {
                $empty = true;
            }

        }

        return $cards;
    }

    /**
     * @param string $id
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getAlternateCardsById(string $id, ?string $locale = null): array
    {
        $url = $this->buildUrl('https://api.altered.gg/cards/' . $id . '/variants', $locale);

        $cards = [];

        $response = $this->alteredHttpClient->request('GET', $url);

        $content = $response->toArray();

        if (count($content['hydra:member']) > 0) {
            $cards = array_merge($cards, $content['hydra:member']);
        }

        return $cards;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getFriendsList(string $token): array
    {
        $page = 1;
        $empty = false;
        $friends = [];

        while (!$empty) {
            $response = $this->alteredHttpClient->request('GET', 'https://api.altered.gg/user_friendships?itemsPerPage=30&page=' . $page, ['headers' => ['Authorization' => 'Bearer ' . $token]]);

            $content = $response->toArray();

            if (count($content['hydra:member']) > 0) {
                $friends = array_merge($friends, $content['hydra:member']);
                $page++;
            } else {
                $empty = true;
            }

        }

        return $friends;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getFriendsListForId(string $id, string $token): array
    {
        $page = 1;
        $empty = false;
        $friends = [];

        while (!$empty) {
            $response = $this->alteredHttpClient->request('GET', 'https://api.altered.gg/user_friendships/friends/' . $id . '?itemsPerPage=30&page=' . $page, ['headers' => ['Authorization' => 'Bearer ' . $token]]);

            $content = $response->toArray();

            if (count($content['hydra:member']) > 0) {
                $friends = array_merge($friends, $content['hydra:member']);
                $page++;
            } else {
                $empty = true;
            }

        }

        return $friends;
    }

    private function buildUrl(string $url, ?string $locale): string
    {
        if ($locale) {
            $url .= '?locale=' . $locale;
        }

        return $url;
    }
}
