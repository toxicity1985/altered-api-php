<?php

namespace Toxicity\AlteredApi\Repository;

use Toxicity\AlteredApi\Entity\Faction;
use Toxicity\AlteredApi\Contract\RepositoryInterface;

class FactionRepository extends AbstractRepository implements RepositoryInterface
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
