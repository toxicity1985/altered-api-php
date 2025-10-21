<?php

namespace Toxicity\AlteredApi\tests\fixtures;

use Toxicity\AlteredApi\Entity\Set;
use Toxicity\AlteredApi\Contract\RepositoryInterface;

/**
 * Fake SetRepository for tests
 */
class FakeSetRepository implements RepositoryInterface
{
    private array $sets = [];

    public function findOneBy(array $criteria): ?Set
    {
        foreach ($this->sets as $set) {
            foreach ($criteria as $key => $value) {
                $getter = 'get' . ucfirst($key);
                if (method_exists($set, $getter) && $set->$getter() !== $value) {
                    continue 2;
                }
            }
            return $set;
        }
        return null;
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
    {
        $results = [];
        foreach ($this->sets as $set) {
            $match = true;
            foreach ($criteria as $key => $value) {
                $getter = 'get' . ucfirst($key);
                if (method_exists($set, $getter) && $set->$getter() !== $value) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $results[] = $set;
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
        return $this->sets;
    }

    public function find(mixed $id): ?Set
    {
        foreach ($this->sets as $set) {
            if ($set->getId() === $id) {
                return $set;
            }
        }
        return null;
    }

    public function add(Set $set): void
    {
        $this->sets[] = $set;
    }

    public function clear(): void
    {
        $this->sets = [];
    }

    public function count(): int
    {
        return count($this->sets);
    }
}

