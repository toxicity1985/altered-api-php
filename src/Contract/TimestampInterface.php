<?php

namespace Toxicity\AlteredApi\Contract;

interface TimestampInterface
{
    public function getCreationDate(): ?\DateTimeImmutable;

    public function setCreationDate($dateCreation): TimestampInterface;

    public function getUpdatedDate(): ?\DateTimeImmutable;

    public function setUpdatedDate($dateUpdate): TimestampInterface;
}
