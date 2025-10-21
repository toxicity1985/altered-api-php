<?php

namespace Toxicity\AlteredApi\tests\fixtures;

use Toxicity\AlteredApi\Entity\Card;
use Toxicity\AlteredApi\Contract\RepositoryInterface;

/**
 * Fake CardRepository for tests
 * Implements RepositoryInterface - no Doctrine constraints!
 */
class FakeCardRepository implements RepositoryInterface
{
    private array $cards = [];

    public function findOneBy(array $criteria): ?Card
    {
        foreach ($this->cards as $card) {
            // Check each criterion
            foreach ($criteria as $key => $value) {
                $getter = 'get' . ucfirst($key);
                if (method_exists($card, $getter) && $card->$getter() !== $value) {
                    continue 2; // Skip to next card
                }
            }
            return $card; // All criteria match
        }
        return null;
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $results = [];
        
        foreach ($this->cards as $card) {
            $match = true;
            foreach ($criteria as $key => $value) {
                $getter = 'get' . ucfirst($key);
                if (method_exists($card, $getter) && $card->$getter() !== $value) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $results[] = $card;
            }
        }

        // Appliquer limit et offset
        if ($offset !== null) {
            $results = array_slice($results, $offset);
        }
        if ($limit !== null) {
            $results = array_slice($results, 0, $limit);
        }

        return $results;
    }

    public function findAll(): array
    {
        return $this->cards;
    }

    public function find(mixed $id): ?Card
    {
        foreach ($this->cards as $card) {
            if ($card->getId() === $id) {
                return $card;
            }
        }
        return null;
    }

    public function add(Card $card): void
    {
        $this->cards[] = $card;
    }

    public function remove(Card $card): void
    {
        $this->cards = array_filter($this->cards, fn($c) => $c !== $card);
    }

    public function clear(): void
    {
        $this->cards = [];
    }

    public function count(): int
    {
        return count($this->cards);
    }
}

