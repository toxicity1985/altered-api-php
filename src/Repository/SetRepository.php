<?php

namespace Toxicity\AlteredApi\Repository;

use Toxicity\AlteredApi\Entity\Set;

class SetRepository extends AbstractRepository
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
