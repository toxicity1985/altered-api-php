<?php

namespace Toxicity\AlteredApi\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Toxicity\AlteredApi\Contract\ObjectInterface;
use Toxicity\AlteredApi\Contract\TimestampInterface;
use Toxicity\AlteredApi\Repository\FactionRepository;
use Toxicity\AlteredApi\Trait\TimestampTrait;

#[ORM\Entity(repositoryClass: FactionRepository::class)]
class Faction implements TimestampInterface, ObjectInterface
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

    #[ORM\Column(length: 5, nullable: false)]
    private string $reference;

    #[ORM\OneToMany(targetEntity: Card::class, mappedBy: 'faction')]
    private Collection $cards;

    public function __construct()
    {
        $this->cards = new ArrayCollection();
        $this->creationDate = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    /**
     * @return Collection<int, Card>
     */
    public function getCards(): Collection
    {
        return $this->cards;
    }

    public function addCard(Card $card): self
    {
        if (!$this->cards->contains($card)) {
            $this->cards->add($card);
        }

        return $this;
    }

    public function removeCard(Card $card): self
    {
        $this->cards->remove($card);

        return $this;
    }
}
