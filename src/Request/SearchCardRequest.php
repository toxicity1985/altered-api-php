<?php

namespace Toxicity\AlteredApi\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Toxicity\AlteredApi\Contract\SearchRequestInterface;
use Toxicity\AlteredApi\Model\CardFactionConstant;
use Toxicity\AlteredApi\Model\CardRarityConstant;
use Toxicity\AlteredApi\Model\CardSetConstant;
use Toxicity\AlteredApi\Model\CardSubTypeConstant;
use Toxicity\AlteredApi\Model\CardTypeConstant;

class SearchCardRequest implements SearchRequestInterface
{
    #[Assert\Choice(choices: CardSetConstant::ALL)]
    public array $cardSets = [];
    #[Assert\Choice(choices: CardFactionConstant::ALL)]
    public array $factions = [];
    #[Assert\Choice(choices: CardRarityConstant::ALL)]
    public array $rarities = [];
    #[Assert\Choice(choices: CardTypeConstant::ALL)]
    public array $types = [];
    #[Assert\Choice(choices: CardSubTypeConstant::ALL)]
    public array $subTypes = [];
    #[Assert\Type('boolean')]
    public ?bool $altArt = null;
    #[Assert\Type('integer')]
    public ?int $mountainPower = null;
    #[Assert\Type('integer')]
    public ?int $mainCost = null;
    #[Assert\Type('integer')]
    public ?int $recallCost = null;
    #[Assert\Type('integer')]
    public ?int $forestPower = null;
    #[Assert\Type('integer')]
    public ?int $oceanPower = null;

    public ?string $name = null;

    public function getUrlParameters(): string
    {
        $urlParameters = '';
        if (sizeof($this->cardSets) > 0) {
            foreach ($this->cardSets as $cardSet) {
                $urlParameters .= '&cardSet[]=' . $cardSet;
            }
        }
        if ($this->name !== null) {
            $urlParameters .= '&translations.name=' . $this->name;
        }
        if ($this->mountainPower !== null) {
            $urlParameters .= '&mountainPower[]=' . $this->mountainPower;
        }
        if ($this->forestPower !== null) {
            $urlParameters .= '&forestPower[]=' . $this->forestPower;
        }
        if ($this->recallCost !== null) {
            $urlParameters .= '&recallCost[]=' . $this->recallCost;
        }
        if ($this->mainCost !== null) {
            $urlParameters .= '&mainCost[]=' . $this->mainCost;
        }
        if ($this->oceanPower !== null) {
            $urlParameters .= '&oceanPower[]=' . $this->oceanPower;
        }
        if (sizeof($this->factions) > 0) {
            foreach ($this->factions as $faction) {
                $urlParameters .= '&factions[]=' . $faction;
            }
        }
        if (sizeof($this->rarities) > 0) {
            foreach ($this->rarities as $rarity) {
                $urlParameters .= '&rarity[]=' . $rarity;
            }
        }
        if (sizeof($this->types) > 0) {
            foreach ($this->types as $type) {
                $urlParameters .= '&cardType[]=' . $type;
            }
        }
        if (sizeof($this->subTypes) > 0) {
            foreach ($this->subTypes as $subType) {
                $urlParameters .= '&cardSubTypes[]=' . $subType;
            }
        }

        if ($this->altArt !== null) {
            $urlParameters .= '&altArt=true';
        }

        return str_starts_with($urlParameters, '&') ? substr($urlParameters, 1) : $urlParameters;
    }
}
