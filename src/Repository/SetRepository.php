<?php

namespace Toxicity\AlteredApi\Repository;

use Toxicity\AlteredApi\Entity\Set;
use Toxicity\AlteredApi\Contract\RepositoryInterface;

class SetRepository extends AbstractRepository implements RepositoryInterface
{
    public function findOneByReference(string $string): ?Set
    {
        return $this->createQueryBuilder('e')
            ->where('e.reference = :string')
            ->setParameter('string', $string)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
