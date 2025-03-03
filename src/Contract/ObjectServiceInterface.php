<?php

namespace Toxicity\AlteredApi\Contract;

interface ObjectServiceInterface
{
    public function delete(ObjectInterface $object): void;
    public function persist(ObjectInterface $object): void;
    public function save(ObjectInterface $object): void;
    public function flush(): void;
}
