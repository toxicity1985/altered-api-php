<?php

namespace Toxicity\AlteredApi\Trait;

use Doctrine\ORM\Mapping as ORM;

trait TimestampTrait
{
    #[ORM\Column(type: 'datetime_immutable')]
    protected \DateTimeImmutable $creationDate;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    protected ?\DateTimeImmutable $updatedDate = null;

    public function __construct()
    {
        $this->creationDate = new \DateTimeImmutable();
    }

    public function getCreationDate(): ?\DateTimeImmutable
    {
        return $this->creationDate;
    }

    public function setCreationDate($creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getUpdatedDate(): ?\DateTimeImmutable
    {
        return $this->updatedDate;
    }

    public function setUpdatedDate($updatedDate): self
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    public function update(): void
    {
        if (empty($this->creation_date)) {
            $this->creationDate = new \DateTimeImmutable();
        }

        $this->updatedDate = new \DateTimeImmutable();
    }
}
