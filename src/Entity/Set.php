<?php

namespace Toxicity\AlteredApi\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Toxicity\AlteredApi\Contract\ObjectInterface;
use Toxicity\AlteredApi\Contract\TimestampInterface;
use Toxicity\AlteredApi\Repository\SetRepository;
use Toxicity\AlteredApi\Trait\TimestampTrait;

#[ORM\Entity(repositoryClass: SetRepository::class)]
#[ORM\Table(name: 'CardSet')]
class Set implements TimestampInterface, ObjectInterface
{
    use TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: false)]
    private ?string $alteredId = null;

    #[ORM\Column(length: 50, nullable: false)]
    private string $name;

    #[ORM\Column(length: 50, nullable: true)]
    private string $name_en;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $code;

    #[ORM\Column(length: 10, nullable: false)]
    private string $isActive;

    #[ORM\Column(length: 25, nullable: false)]
    private string $reference;

    #[ORM\Column(nullable: true)]
    private ?string $illustration;

    #[ORM\Column(nullable: true)]
    private ?string $illustrationPath;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $date;

    #[ORM\Column(type: "json", nullable: true)]
    private array $cardGoogleSheets = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getAlteredId(): ?string
    {
        return $this->alteredId;
    }

    public function setAlteredId(?string $alteredId): self
    {
        $this->alteredId = $alteredId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNameEn(): string
    {
        return $this->name_en;
    }

    public function setNameEn(string $name_en): self
    {
        $this->name_en = $name_en;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getIsActive(): string
    {
        return $this->isActive;
    }

    public function setIsActive(string $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getIllustration(): ?string
    {
        return $this->illustration;
    }

    public function setIllustration(?string $illustration): self
    {
        $this->illustration = $illustration;

        return $this;
    }

    public function getIllustrationPath(): ?string
    {
        return $this->illustrationPath;
    }

    public function setIllustrationPath(?string $illustrationPath): self
    {
        $this->illustrationPath = $illustrationPath;

        return $this;
    }

    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(?DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCardGoogleSheets(): array
    {
        return $this->cardGoogleSheets;
    }

    public function setCardGoogleSheets(array $cardGoogleSheets): self
    {
        $this->cardGoogleSheets = $cardGoogleSheets;

        return $this;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }
}
