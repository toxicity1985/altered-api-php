<?php

namespace Toxicity\AlteredApi\tests\fixtures;

use Toxicity\AlteredApi\Model\CardFactionConstant;
use Toxicity\AlteredApi\Model\CardRarityConstant;
use Toxicity\AlteredApi\Model\CardSetConstant;
use Toxicity\AlteredApi\Model\CardTypeConstant;

/**
 * Helper to create standardized test data
 * Uses project constants to avoid hard-coded values
 */
class CardDataBuilder
{
    /**
     * Create default card data
     */
    public static function createDefaultCardData(array $overrides = []): array
    {
        $defaults = [
            'reference' => 'ALT_CORE_AX_01_R1',
            'name' => 'Test Card',
            'id' => 123,
            'cardType' => ['reference' => CardTypeConstant::HERO],
            'mainFaction' => ['reference' => CardFactionConstant::AXIOM],
            'imagePath' => '/path/to/image.png',
            'qrUrlDetail' => 'https://example.com',
            'cardSet' => ['reference' => CardSetConstant::COREKS],
            'rarity' => ['reference' => CardRarityConstant::COMMON],
            'collectorNumberFormatted' => '001',
        ];

        return array_merge($defaults, $overrides);
    }

    /**
     * Create a CHARACTER card
     */
    public static function createCharacterCardData(array $overrides = []): array
    {
        return self::createDefaultCardData(array_merge([
            'cardType' => ['reference' => CardTypeConstant::CHARACTER],
            'elements' => [
                'MAIN_COST' => 3,
                'RECALL_COST' => 3,
                'OCEAN_POWER' => 2,
                'MOUNTAIN_POWER' => 2,
                'FOREST_POWER' => 2,
            ],
        ], $overrides));
    }

    /**
     * Create a SPELL card
     */
    public static function createSpellCardData(array $overrides = []): array
    {
        return self::createDefaultCardData(array_merge([
            'cardType' => ['reference' => CardTypeConstant::SPELL],
            'elements' => [
                'MAIN_COST' => 2,
            ],
        ], $overrides));
    }

    /**
     * Create a RARE card
     */
    public static function createRareCardData(array $overrides = []): array
    {
        return self::createDefaultCardData(array_merge([
            'rarity' => ['reference' => CardRarityConstant::RARE],
        ], $overrides));
    }

    /**
     * Create a UNIQUE card
     */
    public static function createUniqueCardData(array $overrides = []): array
    {
        return self::createDefaultCardData(array_merge([
            'rarity' => ['reference' => CardRarityConstant::UNIQUE],
        ], $overrides));
    }

    /**
     * Create a card for a specific faction
     */
    public static function createCardForFaction(string $factionConstant, array $overrides = []): array
    {
        $factionNames = [
            CardFactionConstant::AXIOM => 'Axiom',
            CardFactionConstant::BRAVOS => 'Bravos',
            CardFactionConstant::MUNA => 'Muna',
            CardFactionConstant::LYRA => 'Lyra',
            CardFactionConstant::ORDIS => 'Ordis',
            CardFactionConstant::YZMIR => 'Yzmir',
        ];

        $factionName = $factionNames[$factionConstant] ?? 'Unknown';

        return self::createDefaultCardData(array_merge([
            'mainFaction' => ['reference' => $factionConstant],
            'reference' => "ALT_CORE_{$factionConstant}_01_R1",
            'name' => "{$factionName} Test Card",
        ], $overrides));
    }

    /**
     * Create a card for a specific set
     */
    public static function createCardForSet(string $setConstant, array $overrides = []): array
    {
        return self::createDefaultCardData(array_merge([
            'cardSet' => ['reference' => $setConstant],
        ], $overrides));
    }

    /**
     * Create a card with all elements
     */
    public static function createFullCardData(array $overrides = []): array
    {
        return self::createDefaultCardData(array_merge([
            'elements' => [
                'MAIN_COST' => 4,
                'RECALL_COST' => 4,
                'OCEAN_POWER' => 3,
                'MOUNTAIN_POWER' => 3,
                'FOREST_POWER' => 3,
                'MAIN_EFFECT' => 'Test main effect',
                'ECHO_EFFECT' => 'Test echo effect',
                'PERMANENT' => true,
            ],
            'allImagePath' => [
                'en' => '/path/to/en/image.png',
                'fr' => '/path/to/fr/image.png',
            ],
            'isSuspended' => false,
        ], $overrides));
    }
}

