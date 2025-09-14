<?php

namespace Toxicity\AlteredApi\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Toxicity\AlteredApi\Exception\RateLimitExceededException;
use Toxicity\AlteredApi\Provider\AlteredHttpClient;
use Toxicity\AlteredApi\Request\SearchCardRequest;
use Toxicity\AlteredApi\Request\SearchEventRequest;

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
     * @throws RateLimitExceededException
     */
    public function getSets(?string $locale = 'fr-fr'): array
    {
        $url = $this->buildUrl('/card_sets', $locale);

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
    public function getCardByReference(string $reference, ?string $locale = null): array
    {
        $url = $this->buildUrl('/cards/' . $reference, $locale);

        $response = $this->alteredHttpClient->request('GET', $url);
        return $response->toArray();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface|RateLimitExceededException
     */
    public function getCardsBySearch(SearchCardRequest $searchCardRequest, ?string $locale = 'fr-fr', ?string $token = null): array
    {
        $page = 1;
        $empty = false;
        $cards = [];
        if($token) {
            $options = ['headers' => ['Authorization' => 'Bearer ' . $token]];
        }
        else {
            $options = [];            
        }

        $url = $this->buildUrl('/cards?' . $searchCardRequest->getUrlParameters(), $locale);

        while (!$empty) {
            usleep(600000);
            $response = $this->alteredHttpClient->request('GET', $url . '&itemsPerPage=36&order[reference]=ASC&page=' . $page, $options);

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
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getCardStatsBySearch(SearchCardRequest $searchCardRequest, string $token, ?string $locale = 'fr-fr'): array
    {
        $page = 1;
        $empty = false;
        $stats = [];

        sleep(5);

        $limiter = RateLimiterService::create(20);

        $options = ['headers' => ['Authorization' => 'Bearer ' . $token]];

        $url = $this->buildUrl('/cards/stats?itemsPerPage=36&' . $searchCardRequest->getUrlParameters(), $locale);

        while (!$empty) {
            $response = $this->alteredHttpClient->request('GET', $url . '&page=' . $page, $options, $limiter);

            $content = $response->toArray();

            if (count($content['hydra:member']) > 0) {
                $stats = array_merge($stats, $content['hydra:member']);
                $page++;
            } else {
                $empty = true;
            }
        }

        return $stats;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getAlternateCardsByReference(string $reference, ?string $locale = null): array
    {
        $url = $this->buildUrl('/cards/' . $reference . '/variants', $locale);

        $cards = [];

        $response = $this->alteredHttpClient->request('GET', $url);

        $content = $response->toArray();

        if (count($content['hydra:member']) > 0) {
            $cards = array_merge($cards, $content['hydra:member']);
        }

        return $cards;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getCardOffersByReference(string $reference, string $token, ?string $locale = null): array
    {
        $page = 1;
        $empty = false;
        $offers = [];

        while (!$empty) {
            $response = $this->alteredHttpClient->request('GET', '/cards/'.$reference.'/offers/?itemsPerPage=100&page=' . $page, ['headers' => ['Authorization' => 'Bearer ' . $token]]);

            $content = $response->toArray();

            if (count($content['hydra:member']) > 0) {
                $offers = array_merge($offers, $content['hydra:member']);
                $page++;
            } else {
                $empty = true;
            }
        }

        return $offers;
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
            $response = $this->alteredHttpClient->request('GET', '/user_friendships?itemsPerPage=30&page=' . $page, ['headers' => ['Authorization' => 'Bearer ' . $token]]);

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
    public function getFriendsListForId(string $uniqueId, string $token): array
    {
        $page = 1;
        $empty = false;
        $friends = [];

        while (!$empty) {
            $response = $this->alteredHttpClient->request('GET', '/user_friendships/friends/' . $uniqueId . '?itemsPerPage=30&page=' . $page, ['headers' => ['Authorization' => 'Bearer ' . $token]]);

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
    public function getFriendProfile(string $uniqueId, string $token): array
    {
        $response = $this->alteredHttpClient->request('GET', '/users/profile/' . $uniqueId, ['headers' => ['Authorization' => 'Bearer ' . $token]]);
        return $response->toArray();
    }

    private function buildUrl(string $url, ?string $locale): string
    {
        if ($locale) {
            if (strpos($url, '?') > 0) {
                $url .= '&locale=' . $locale;
            } else {
                $url .= '?locale=' . $locale;
            }
        }

        return $url;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function me(string $token): array
    {
        $response = $this->alteredHttpClient->request('GET', '/me', ['headers' => ['Authorization' => 'Bearer ' . $token]]);
        return $response->toArray();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getFactions(?string $locale = 'fr-fr'): array
    {
        $url = $this->buildUrl('/factions', $locale);

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
    public function getCollection(string $token): array
    {
        $page = 1;
        $empty = false;
        $cardStats = [];

        while (!$empty) {
            $response = $this->alteredHttpClient->request('GET', '/cards/stats?collection=true&itemsPerPage=36&locale=en-us&page=' . $page, ['headers' => ['Authorization' => 'Bearer ' . $token]]);

            $content = $response->toArray();

            if (count($content['hydra:member']) > 0) {
                $cardStats = array_merge($cardStats, $content['hydra:member']);
                $page++;
            } else {
                $empty = true;
            }

        }

        return $cardStats;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getFriendStats(string $uniqueId, string $token): array
    {
        $response = $this->alteredHttpClient->request('GET', '/users/Kit3tsu_7919/stats/' . $uniqueId, ['headers' => ['Authorization' => 'Bearer ' . $token]]);
        return $response->toArray();
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getFriendTradeListForId(string $id, string $token, ?string $locale = 'fr-fr'): array
    {
        $page = 1;
        $empty = false;
        $cardToTrade = [];

        $url = $this->buildUrl('/cards/stats?collection=true&itemsPerPage=36' . $id . '/trades', $locale);

        while (!$empty) {
            $response = $this->alteredHttpClient->request('GET', $url . '&page=' . $page, ['headers' => ['Authorization' => 'Bearer ' . $token]]);

            $content = $response->toArray();

            if (count($content['hydra:member']) > 0) {
                $cardToTrade = array_merge($cardToTrade, $content['hydra:member']);
                $page++;
            } else {
                $empty = true;
            }

        }

        return $cardToTrade;

    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getEventsBySearch(SearchEventRequest $searchEventRequest, ?string $locale = null): array
    {
        $events = [];

        $url = $this->buildUrl('/events/locator?' . $searchEventRequest->getUrlParameters(), $locale);

        $response = $this->alteredHttpClient->request('GET', $url);

        $content = $response->toArray();

        if (count($content['hydra:member']) > 0) {
            $events = array_merge($events, $content['hydra:member']);
        }

        return $events;
    }
}
