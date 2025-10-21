<?php

namespace Toxicity\AlteredApi\tests\fixtures;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Proxy\ProxyFactory;
use Doctrine\ORM\UnitOfWork;

/**
 * Fake EntityManager for tests
 * Implements EntityManagerInterface to satisfy type hints
 */
class FakeEntityManager implements EntityManagerInterface
{
    private array $repositories = [];
    private array $persisted = [];
    private int $flushCount = 0;
    private int $persistCount = 0;

    public function __construct(
        ?FakeCardRepository $cardRepository = null,
        ?FakeFactionRepository $factionRepository = null,
        ?FakeSetRepository $setRepository = null
    ) {
        $this->repositories = [
            'Card' => $cardRepository ?? new FakeCardRepository(),
            'Faction' => $factionRepository ?? new FakeFactionRepository(),
            'Set' => $setRepository ?? new FakeSetRepository(),
        ];
    }

    public function getRepository(string $className): \Doctrine\Persistence\ObjectRepository
    {
        // Extract class name without namespace
        $parts = explode('\\', $className);
        $shortName = end($parts);
        
        // Return corresponding repository
        return $this->repositories[$shortName] ?? new FakeCardRepository();
    }

    public function persist(object $entity): void
    {
        $this->persisted[] = $entity;
        $this->persistCount++;
    }

    public function flush(): void
    {
        $this->flushCount++;
    }

    public function remove(object $entity): void
    {
        $this->persisted = array_filter(
            $this->persisted,
            fn($e) => $e !== $entity
        );
    }

    // Méthodes pour les assertions dans les tests
    public function getPersistedEntities(): array
    {
        return $this->persisted;
    }

    public function getFlushCount(): int
    {
        return $this->flushCount;
    }

    public function getPersistCount(): int
    {
        return $this->persistCount;
    }

    public function wasPersisted(object $entity): bool
    {
        return in_array($entity, $this->persisted, true);
    }

    public function reset(): void
    {
        $this->persisted = [];
        $this->flushCount = 0;
        $this->persistCount = 0;
    }
}

