<?php

namespace Toxicity\AlteredApi\tests\fixtures;

use Toxicity\AlteredApi\Entity\Faction;
use Toxicity\AlteredApi\Contract\RepositoryInterface;

/**
 * Fake FactionRepository for tests
 */
class FakeFactionRepository implements RepositoryInterface
{
    private array $factions = [];

    public function findOneBy(array $criteria): ?Faction
    {
        foreach ($this->factions as $faction) {
            foreach ($criteria as $key => $value) {
                $getter = 'get' . ucfirst($key);
                if (method_exists($faction, $getter) && $faction->$getter() !== $value) {
                    continue 2;
                }
            }
            return $faction;
        }
        return null;
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $results = [];
        foreach ($this->factions as $faction) {
            $match = true;
            foreach ($criteria as $key => $value) {
                $getter = 'get' . ucfirst($key);
                if (method_exists($faction, $getter) && $faction->$getter() !== $value) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $results[] = $faction;
            }
        }
        
        // Gestion basique de limit/offset
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
        return $this->factions;
    }

    public function find(mixed $id): ?Faction
    {
        foreach ($this->factions as $faction) {
            if ($faction->getId() === $id) {
                return $faction;
            }
        }
        return null;
    }

    public function add(Faction $faction): void
    {
        $this->factions[] = $faction;
    }

    public function clear(): void
    {
        $this->factions = [];
    }

    public function count(): int
    {
        return count($this->factions);
    }
}

