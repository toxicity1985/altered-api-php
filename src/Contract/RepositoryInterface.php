<?php

namespace Toxicity\AlteredApi\Contract;

/**
 * Interface générique pour tous les repositories
 * Remplace CardRepositoryInterface, FactionRepositoryInterface, SetRepositoryInterface
 */
interface RepositoryInterface
{
    /**
     * Trouve une entité par critères
     * 
     * @param array<string, mixed> $criteria
     * @return object|null
     */
    public function findOneBy(array $criteria): ?object;

    /**
     * Trouve plusieurs entités par critères
     * 
     * @param array<string, mixed> $criteria
     * @param array<string, string>|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array<object>
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array;

    /**
     * Trouve toutes les entités
     * 
     * @return array<object>
     */
    public function findAll(): array;

    /**
     * Trouve une entité par ID
     * 
     * @param int $id
     * @return object|null
     */
    public function find(int $id): ?object;
}


