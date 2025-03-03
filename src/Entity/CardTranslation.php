<?php

namespace Toxicity\AlteredApi\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Toxicity\AlteredApi\Contract\ObjectInterface;
use Toxicity\AlteredApi\Contract\TimestampInterface;
use Toxicity\AlteredApi\Trait\TimestampTrait;

#[ORM\Entity]
class CardTranslation implements TimestampInterface, ObjectInterface
{
    use TimestampTrait;

    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id;

    #[ORM\Column(type: Types::STRING, length: 8)]
    private string $locale;

    #[ORM\Column(length: 50, nullable: false)]
    private string $name;

    #[ORM\Column(type: "json", nullable: false)]
    private array $elements = [];

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $echoEffect = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $mainEffect = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $mainEffect1 = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $mainEffect2 = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $mainEffect3 = null;

    #[ORM\ManyToOne(targetEntity: Card::class, inversedBy: 'translations')]
    protected Card $card;

    #[ORM\Column(nullable: false)]
    private string $imgPath;

    public function __construct()
    {
        $this->creationDate = new DateTimeImmutable();
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEchoEffect(): ?string
    {
        return $this->echoEffect;
    }

    public function setEchoEffect(?string $echoEffect): self
    {
        $this->echoEffect = $echoEffect;

        return $this;
    }

    public function getMainEffect(): ?string
    {
        return $this->mainEffect;
    }

    public function setMainEffect(?string $mainEffect): self
    {
        $this->mainEffect = $mainEffect;

        return $this;
    }

    public function getMainEffect1(): ?string
    {
        return $this->mainEffect1;
    }

    public function setMainEffect1(?string $mainEffect1): self
    {
        $this->mainEffect1 = $mainEffect1;

        return $this;
    }

    public function getMainEffect2(): ?string
    {
        return $this->mainEffect2;
    }

    public function setMainEffect2(?string $mainEffect2): self
    {
        $this->mainEffect2 = $mainEffect2;

        return $this;
    }

    public function getMainEffect3(): ?string
    {
        return $this->mainEffect3;
    }

    public function setMainEffect3(?string $mainEffect3): self
    {
        $this->mainEffect3 = $mainEffect3;

        return $this;
    }

    public function getCard(): Card
    {
        return $this->card;
    }

    public function setCard(Card $card): self
    {
        $this->card = $card;

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
}