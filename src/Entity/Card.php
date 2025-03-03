<?php

namespace Toxicity\AlteredApi\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Toxicity\AlteredApi\Contract\ObjectInterface;
use Toxicity\AlteredApi\Contract\TimestampInterface;
use Toxicity\AlteredApi\Repository\CardRepository;
use Toxicity\AlteredApi\Trait\TimestampTrait;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card implements TimestampInterface, ObjectInterface
{
    use TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: false)]
    private string $name;

    #[ORM\Column(length: 50, nullable: false)]
    private string $reference;

    #[ORM\Column(length: 50, nullable: false)]
    private ?string $alteredId = null;

    #[ORM\Column(type: "string", nullable: false)]
    private string $rarityString;

    #[ORM\Column(type: "string", nullable: false)]
    private string $typeString;

    #[ORM\Column(type: "json", nullable: true)]
    private array|null $subTypeArray = [];

    #[ORM\Column(type: "json", nullable: false)]
    private array $elements = [];

    #[ORM\Column(type: "json", nullable: false)]
    private array $allImagePath = [];

    #[ORM\Column(type: "json", nullable: false)]
    private array $cardType = [];

    #[ORM\Column(type: "json", nullable: false)]
    private array $cardSubType = [];

    #[ORM\Column(type: "json", nullable: false)]
    private array $rarity = [];

    #[ORM\Column(type: "json", nullable: false)]
    private array $cardProduct = [];

    #[ORM\Column(nullable: false)]
    private string $imgPath;

    #[ORM\ManyToOne(targetEntity: Faction::class, inversedBy: 'cards')]
    private ?Faction $faction;

    #[ORM\ManyToOne(targetEntity: Set::class)]
    private ?Set $set;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $mainCost = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $recallCost = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $oceanPower = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $mountainPower = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $forestPower = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $permanent = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private bool $isSuspended = false;

    #[ORM\OneToMany(targetEntity: CardTranslation::class, mappedBy: 'card')]
    private Collection $translations;

    public function __construct()
    {
        $this->creationDate = new \DateTimeImmutable();
        $this->translations = new ArrayCollection();
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

    public function getFaction(): ?Faction
    {
        return $this->faction;
    }

    public function setFaction(?Faction $faction): Card
    {
        $this->faction = $faction;

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

    public function getElements(): array
    {
        return $this->elements;
    }

    public function setElements(array $elements): self
    {
        $this->elements = $elements;

        return $this;
    }

    public function getCardType(): array
    {
        return $this->cardType;
    }

    public function setCardType(array $cardType): self
    {
        $this->cardType = $cardType;

        return $this;
    }

    public function getCardSubType(): array
    {
        return $this->cardSubType;
    }

    public function setCardSubType(array $cardSubType): self
    {
        $this->cardSubType = $cardSubType;

        return $this;
    }

    public function getRarity(): array
    {
        return $this->rarity;
    }

    public function setRarity(array $rarity): self
    {
        $this->rarity = $rarity;

        return $this;
    }

    public function getCardProduct(): array
    {
        return $this->cardProduct;
    }

    public function setCardProduct(array $cardProduct): self
    {
        $this->cardProduct = $cardProduct;

        return $this;
    }

    public function getRarityString(): string
    {
        return $this->rarityString;
    }

    public function setRarityString(string $rarityString): self
    {
        $this->rarityString = $rarityString;

        return $this;
    }

    public function getTypeString(): string
    {
        return $this->typeString;
    }

    public function setTypeString(string $typeString): self
    {
        $this->typeString = $typeString;

        return $this;
    }

    public function getSubTypeArray(): ?array
    {
        return $this->subTypeArray;
    }

    public function setSubTypeArray(?array $subTypeArray): self
    {
        $this->subTypeArray = $subTypeArray;

        return $this;
    }

    public function getImgPath(): string
    {
        return $this->imgPath;
    }

    public function setImgPath(string $imgPath): self
    {
        $this->imgPath = $imgPath;

        return $this;
    }

    public function getMainCost(): ?int
    {
        return $this->mainCost;
    }

    public function setMainCost(?int $mainCost): self
    {
        $this->mainCost = $mainCost;

        return $this;
    }

    public function getRecallCost(): ?int
    {
        return $this->recallCost;
    }

    public function setRecallCost(?int $recallCost): self
    {
        $this->recallCost = $recallCost;

        return $this;
    }

    public function getOceanPower(): ?int
    {
        return $this->oceanPower;
    }

    public function setOceanPower(?int $oceanPower): self
    {
        $this->oceanPower = $oceanPower;

        return $this;
    }

    public function getMountainPower(): ?int
    {
        return $this->mountainPower;
    }

    public function setMountainPower(?int $mountainPower): self
    {
        $this->mountainPower = $mountainPower;

        return $this;
    }

    public function getForestPower(): ?int
    {
        return $this->forestPower;
    }

    public function setForestPower(?int $forestPower): self
    {
        $this->forestPower = $forestPower;

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

    public function getPermanent(): ?int
    {
        return $this->permanent;
    }

    public function setPermanent(?int $permanent): self
    {
        $this->permanent = $permanent;

        return $this;
    }

    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function getTranslation(string $locale): ?CardTranslation
    {
        foreach ($this->translations as $translation) {
            if ($translation->getLocale() === $locale) {
                return $translation;
            }
        }

        return null;
    }

    public function addTranslation(CardTranslation $t): void
    {
        if (!$this->translations->contains($t)) {
            $this->translations->add($t);
            $t->setCard($this);
        }
    }

    public function getAllImagePath(): array
    {
        return $this->allImagePath;
    }

    public function setAllImagePath(array $allImagePath): self
    {
        $this->allImagePath = $allImagePath;

        return $this;
    }

    public function getSet(): ?Set
    {
        return $this->set;
    }

    public function setSet(?Set $set): self
    {
        $this->set = $set;

        return $this;
    }

    public function isSuspended(): bool
    {
        return $this->isSuspended;
    }

    public function setIsSuspended(bool $isSuspended): self
    {
        $this->isSuspended = $isSuspended;

        return $this;
    }


}
