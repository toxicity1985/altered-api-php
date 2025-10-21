<?php

namespace Toxicity\AlteredApi\Service;

use Doctrine\ORM\EntityManagerInterface;
use Toxicity\AlteredApi\Builder\SetBuilder;
use Toxicity\AlteredApi\Entity\Set;

class SetService extends AbstractObjectService
{
    protected string $entityClassName = Set::class;

    public function __construct(private readonly SetBuilder $setBuilder, EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->entityClassName = Set::class;
    }

    public function buildFromData(array $data): Set
    {
        $set = $this->getRepository()->findOneBy(['reference' => $data['reference']]);
        if ($set === null) {
            $set = new Set();

        }
        return $this->setBuilder->build($set, $data);
    }
}
