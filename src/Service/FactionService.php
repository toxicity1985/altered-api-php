<?php

namespace Toxicity\AlteredApi\Service;

use Doctrine\ORM\EntityManagerInterface;
use Toxicity\AlteredApi\Builder\FactionBuilder;
use Toxicity\AlteredApi\Entity\Faction;

class FactionService extends AbstractObjectService
{
    protected string $entityClassName = Faction::class;

    public function __construct(private readonly FactionBuilder $factionBuilder, EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->entityClassName = Faction::class;
    }

    public function buildFromData(array $data): Faction
    {
        $faction = $this->getRepository()->findOneBy(['reference' => $data['reference']]);
        if ($faction === null) {
            $faction = new Faction();

        }
        return $this->factionBuilder->build($faction, $data);
    }
}
