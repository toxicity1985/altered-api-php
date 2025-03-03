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
