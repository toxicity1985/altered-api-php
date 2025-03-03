<?php

namespace Toxicity\AlteredApi\Builder;

use DateTimeImmutable;
use Toxicity\AlteredApi\Entity\Faction;

class FactionBuilder
{
    public function build(Faction $faction, $data): Faction
    {
        if ($faction->getId()) {
            $faction->setUpdatedDate(new DateTimeImmutable());
        }

        return $faction->setName($data['name'])
            ->setAlteredId($data['id'])
            ->setReference($data['reference']);

    }
}
