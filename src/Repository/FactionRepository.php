<?php

namespace Toxicity\AlteredApi\Repository;

use Toxicity\AlteredApi\Entity\Faction;

class FactionRepository extends AbstractRepository
{
    public function findOneByReference(string $reference): ?Faction
    {
        return $this->createQueryBuilder('f')
            ->where('f.reference = :reference')
            ->setParameter('reference', $reference)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
