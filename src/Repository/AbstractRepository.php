<?php

namespace Toxicity\AlteredApi\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;

abstract class AbstractRepository extends EntityRepository
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $metadata = new ClassMetadata($this->entity());
        parent::__construct($entityManager, $metadata);
    }

    public function entity(): string
    {
        $className = get_class($this);
        $split = explode('\\', $className);
        $split[count($split) - 2] = "Entity";
        $split[count($split) - 1] = str_replace("Repository", "", $split[count($split) - 1]);

        return join('\\', $split);
    }
}
