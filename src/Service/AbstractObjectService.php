<?php

namespace Toxicity\AlteredApi\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Toxicity\AlteredApi\Contract\ObjectInterface;
use Toxicity\AlteredApi\Contract\ObjectServiceInterface;

abstract class AbstractObjectService implements ObjectServiceInterface
{
    protected string $entityClassName;

    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
    }

    public function persist(ObjectInterface $object): void
    {
        $this->entityManager->persist($object);
    }

    public function save(ObjectInterface $object): void
    {
        $this->entityManager->persist($object);
        $this->entityManager->flush();
    }

    public function delete(ObjectInterface $object): void
    {
        $this->entityManager->remove($object);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }

    public function getRepository(?string $className = null): ObjectRepository
    {
        if ($className !== null) {
            return $this->entityManager->getRepository($className);
        }

        return $this->entityManager->getRepository($this->entityClassName);
    }
}
