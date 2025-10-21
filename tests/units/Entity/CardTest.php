<?php

namespace Toxicity\AlteredApi\tests\units\Entity;

use Toxicity\AlteredApi\Entity\Card;
use Toxicity\AlteredApi\Entity\Faction;
use Toxicity\AlteredApi\Entity\Set;
use Toxicity\AlteredApi\Entity\CardTranslation;
use atoum\atoum;

class CardTest extends atoum\test
{
    public function getTestedClassName()
    {
        return 'Toxicity\AlteredApi\Entity\Card';
    }

    public function testConstruct()
    {
        $card = new Card();

        $this
            ->object($card)
                ->isInstanceOf(Card::class)
            ->variable($card->getId())
                ->isNull()
            ->object($card->getCreationDate())
                ->isInstanceOf(\DateTimeImmutable::class)
            ->object($card->getTranslations())
                ->isInstanceOf(\Doctrine\Common\Collections\Collection::class)
            ->boolean($card->isSuspended())
                ->isFalse()
        ;
    }

    public function testSettersAndGetters()
    {
        $card = new Card();

        $this
            ->given($card->setName('Test Card'))
            ->then
                ->string($card->getName())
                    ->isEqualTo('Test Card')
        ;

        $this
            ->given($card->setReference('REF123'))
            ->then
                ->string($card->getReference())
                    ->isEqualTo('REF123')
        ;

        $this
            ->given($card->setAlteredId('ALT456'))
            ->then
                ->string($card->getAlteredId())
                    ->isEqualTo('ALT456')
        ;

        $this
            ->given($card->setRarityString('RARE'))
            ->then
                ->string($card->getRarityString())
                    ->isEqualTo('RARE')
        ;

        $this
            ->given($card->setTypeString('CHARACTER'))
            ->then
                ->string($card->getTypeString())
                    ->isEqualTo('CHARACTER')
        ;

        $this
            ->given($card->setImgPath('/path/to/image.png'))
            ->then
                ->string($card->getImgPath())
                    ->isEqualTo('/path/to/image.png')
        ;
    }

    public function testNumericProperties()
    {
        $card = new Card();

        $this
            ->given($card->setMainCost(5))
            ->then
                ->integer($card->getMainCost())
                    ->isEqualTo(5)
        ;

        $this
            ->given($card->setRecallCost(3))
            ->then
                ->integer($card->getRecallCost())
                    ->isEqualTo(3)
        ;

        $this
            ->given($card->setOceanPower(7))
            ->then
                ->integer($card->getOceanPower())
                    ->isEqualTo(7)
        ;

        $this
            ->given($card->setMountainPower(4))
            ->then
                ->integer($card->getMountainPower())
                    ->isEqualTo(4)
        ;

        $this
            ->given($card->setForestPower(6))
            ->then
                ->integer($card->getForestPower())
                    ->isEqualTo(6)
        ;

        $this
            ->given($card->setPermanent(2))
            ->then
                ->integer($card->getPermanent())
                    ->isEqualTo(2)
        ;
    }

    public function testArrayProperties()
    {
        $card = new Card();

        $elements = ['fire', 'water'];
        $this
            ->given($card->setElements($elements))
            ->then
                ->array($card->getElements())
                    ->isEqualTo($elements)
        ;

        $cardType = ['CHARACTER'];
        $this
            ->given($card->setCardType($cardType))
            ->then
                ->array($card->getCardType())
                    ->isEqualTo($cardType)
        ;

        $cardSubType = ['WARRIOR', 'HUMAN'];
        $this
            ->given($card->setCardSubType($cardSubType))
            ->then
                ->array($card->getCardSubType())
                    ->isEqualTo($cardSubType)
        ;

        $rarity = ['RARE'];
        $this
            ->given($card->setRarity($rarity))
            ->then
                ->array($card->getRarity())
                    ->isEqualTo($rarity)
        ;

        $cardProduct = ['BOOSTER'];
        $this
            ->given($card->setCardProduct($cardProduct))
            ->then
                ->array($card->getCardProduct())
                    ->isEqualTo($cardProduct)
        ;

        $imagePaths = ['/image1.png', '/image2.png'];
        $this
            ->given($card->setAllImagePath($imagePaths))
            ->then
                ->array($card->getAllImagePath())
                    ->isEqualTo($imagePaths)
        ;

        $subTypeArray = ['WARRIOR'];
        $this
            ->given($card->setSubTypeArray($subTypeArray))
            ->then
                ->array($card->getSubTypeArray())
                    ->isEqualTo($subTypeArray)
        ;
    }

    public function testRelationships()
    {
        $card = new Card();
        $faction = $this->newMockInstance(Faction::class);
        $set = $this->newMockInstance(Set::class);

        $this
            ->given($card->setFaction($faction))
            ->then
                ->object($card->getFaction())
                    ->isIdenticalTo($faction)
        ;

        $this
            ->given($card->setSet($set))
            ->then
                ->object($card->getSet())
                    ->isIdenticalTo($set)
        ;
    }

    public function testIsSuspended()
    {
        $card = new Card();

        $this
            ->given($card->setIsSuspended(true))
            ->then
                ->boolean($card->isSuspended())
                    ->isTrue()
        ;

        $this
            ->given($card->setIsSuspended(false))
            ->then
                ->boolean($card->isSuspended())
                    ->isFalse()
        ;
    }

    public function testFluentInterface()
    {
        $card = new Card();

        $this
            ->object($card->setName('Test'))
                ->isIdenticalTo($card)
            ->object($card->setReference('REF'))
                ->isIdenticalTo($card)
            ->object($card->setMainCost(5))
                ->isIdenticalTo($card)
        ;
    }

    public function testTranslations()
    {
        $card = new Card();
        $translation = $this->newMockInstance(CardTranslation::class);
        
        $this->calling($translation)->getLocale = 'en-us';

        $this
            ->given($card->addTranslation($translation))
            ->then
                ->integer($card->getTranslations()->count())
                    ->isGreaterThan(0)
                ->object($card->getTranslation('en-us'))
                    ->isIdenticalTo($translation)
                ->variable($card->getTranslation('fr-fr'))
                    ->isNull()
        ;
    }
}

