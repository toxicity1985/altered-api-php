<?php

namespace Toxicity\AlteredApi\Builder;

use Doctrine\Persistence\ObjectRepository;
use Toxicity\AlteredApi\Entity\Card;
use Toxicity\AlteredApi\Entity\CardTranslation;
use Toxicity\AlteredApi\Model\CardRarityConstant;
use Toxicity\AlteredApi\Model\CardSubTypeConstant;
use Toxicity\AlteredApi\Model\CardTypeConstant;
use Toxicity\AlteredApi\Repository\FactionRepository;
use Toxicity\AlteredApi\Repository\SetRepository;
use DateTimeImmutable;

readonly class CardBuilder
{
    public function __construct(
        private FactionRepository|ObjectRepository $factionRepository,
        private SetRepository|ObjectRepository     $setRepository,
    )
    {

    }

    public function build(Card $card, array $data, string $locale): Card
    {
        if ($card->getId()) {
            $card->setUpdatedDate(new DateTimeImmutable());
        }

        $card->setAlteredId($data['id']);
        $card->setReference($data['reference']);

        $dbSet = $this->setRepository->findOneBy(['reference' =>  $data['cardSet']['reference']]);
        if ($dbSet) {
            $card->setSet($dbSet);
        }

        $card->setName($data['name']);

        $card->setImgPath($data['imagePath']);

        if (array_key_exists('allImagePath', $data)) {
            $card->setAllImagePath($data['allImagePath']);
        }

        if (array_key_exists('isSuspended', $data)) {
            $card->setIsSuspended($data['isSuspended']);
        }

        if (array_key_exists('elements', $data)) {
            $card->setElements($data['elements']);

            if (array_key_exists('MAIN_COST', $data['elements'])) {
                $card->setMainCost((int)$data['elements']['MAIN_COST']);
            }

            if (array_key_exists('RECALL_COST', $data['elements'])) {
                $card->setRecallCost((int)$data['elements']['RECALL_COST']);
            }

            if (array_key_exists('OCEAN_POWER', $data['elements'])) {
                $card->setOceanPower((int)$data['elements']['OCEAN_POWER']);
            }

            if (array_key_exists('MOUNTAIN_POWER', $data['elements'])) {
                $card->setMountainPower((int)$data['elements']['MOUNTAIN_POWER']);
            }

            if (array_key_exists('FOREST_POWER', $data['elements'])) {
                $card->setForestPower((int)$data['elements']['FOREST_POWER']);
            }

            if (array_key_exists('PERMANENT', $data['elements'])) {
                $card->setPermanent($data['elements']['PERMANENT']);
            }
        }

        $card = $this->buildTranslation($card, $data, $locale);


        if (array_key_exists('rarity', $data)) {
            $card->setRarity($data['rarity']);
            $card->setRarityString(constant(CardRarityConstant::class . '::' . $data['rarity']['reference']));
        }

        if (array_key_exists('cardType', $data)) {
            $card->setCardType($data['cardType']);
            $card->setTypeString(constant(CardTypeConstant::class . '::' . $data['cardType']['reference']));
        }

        if (array_key_exists('cardSubTypes', $data)) {
            $card->setCardSubType($data['cardSubTypes']);
            $cardSubTypes = [];
            foreach ($data['cardSubTypes'] as $subType) {
                $cardSubTypes[] = (constant(CardSubTypeConstant::class . '::' . $subType['reference']));
            }
            $card->setSubTypeArray($cardSubTypes);

        }

        if (array_key_exists('mainFaction', $data) && array_key_exists('reference', $data['mainFaction'])) {
            $dbFaction = $this->factionRepository->findOneBy(['reference' => $data['mainFaction']['reference']]);
            if ($dbFaction) {
                $card->setFaction($dbFaction);
            }
        }

        return $card;
    }

    private function buildTranslation(Card $card, array $data, string $locale): Card
    {
        $language = explode('-', $locale)[0];
        $cardTranslation = $card->getTranslation($language);
        if (!$cardTranslation) {
            $cardTranslation = new CardTranslation();
            $cardTranslation->setLocale($language);
            $cardTranslation->setCard($card);
            $card->addTranslation($cardTranslation);
        } else {
            $cardTranslation->setUpdatedDate(new DateTimeImmutable());
            $card->setUpdatedDate(new DateTimeImmutable());
        }

        $cardTranslation->setImgPath($data['imagePath']);
        $cardTranslation->setName($data['name']);

        if (array_key_exists('elements', $data)) {
            $cardTranslation->setElements($data['elements']);

            if (array_key_exists('ECHO_EFFECT', $data['elements'])) {
                $cardTranslation->setEchoEffect($data['elements']['ECHO_EFFECT']);
            }

            if (array_key_exists('MAIN_EFFECT', $data['elements'])) {
                $cardTranslation->setMainEffect($data['elements']['MAIN_EFFECT']);
                if ($cardTranslation->getMainEffect()) {
                    $explodedEffect = explode('  ', $cardTranslation->getMainEffect());
                    if (array_key_exists(0, $explodedEffect)) {
                        $cardTranslation->setMainEffect1($explodedEffect[0]);
                    }
                    if (array_key_exists(1, $explodedEffect)) {
                        $cardTranslation->setMainEffect2($explodedEffect[1]);
                    }
                    if (array_key_exists(2, $explodedEffect)) {
                        $cardTranslation->setMainEffect3($explodedEffect[2]);
                    }
                }
            }
        }

        return $card;
    }
}
