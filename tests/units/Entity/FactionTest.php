<?php

namespace Toxicity\AlteredApi\tests\units\Entity;

use Toxicity\AlteredApi\Entity\Faction;
use Toxicity\AlteredApi\Entity\Card;
use atoum\atoum;

class FactionTest extends atoum\test
{
    public function getTestedClassName()
    {
        return 'Toxicity\AlteredApi\Entity\Faction';
    }

    public function testConstruct()
    {
        $faction = new Faction();

        $this
            ->object($faction)
                ->isInstanceOf(Faction::class)
            ->variable($faction->getId())
                ->isNull()
            ->object($faction->getCreationDate())
                ->isInstanceOf(\DateTimeImmutable::class)
            ->object($faction->getCards())
                ->isInstanceOf(\Doctrine\Common\Collections\Collection::class)
            ->integer($faction->getCards()->count())
                ->isEqualTo(0)
        ;
    }

    public function testSettersAndGetters()
    {
        $faction = new Faction();

        $this
            ->given($faction->setName('Axiom'))
            ->then
                ->string($faction->getName())
                    ->isEqualTo('Axiom')
        ;

        $this
            ->given($faction->setReference('AX'))
            ->then
                ->string($faction->getReference())
                    ->isEqualTo('AX')
        ;

        $this
            ->given($faction->setAlteredId('ALT123'))
            ->then
                ->string($faction->getAlteredId())
                    ->isEqualTo('ALT123')
        ;
    }

    public function testFluentInterface()
    {
        $faction = new Faction();

        $this
            ->object($faction->setName('Axiom'))
                ->isIdenticalTo($faction)
            ->object($faction->setReference('AX'))
                ->isIdenticalTo($faction)
            ->object($faction->setAlteredId('ALT123'))
                ->isIdenticalTo($faction)
        ;
    }

    public function testAddCard()
    {
        $faction = new Faction();
        $card = $this->newMockInstance(Card::class);

        $this
            ->given($faction->addCard($card))
            ->then
                ->integer($faction->getCards()->count())
                    ->isEqualTo(1)
        ;

        // Ajouter la même carte ne doit pas la dupliquer
        $this
            ->given($faction->addCard($card))
            ->then
                ->integer($faction->getCards()->count())
                    ->isEqualTo(1)
        ;
    }

    public function testRemoveCard()
    {
        $faction = new Faction();
        $card = $this->newMockInstance(Card::class);

        $this
            ->given($faction->addCard($card))
            ->and($faction->removeCard($card))
            ->then
                ->integer($faction->getCards()->count())
                    ->isEqualTo(0)
        ;
    }

    public function testAddRemoveCardFluentInterface()
    {
        $faction = new Faction();
        $card = $this->newMockInstance(Card::class);

        $this
            ->object($faction->addCard($card))
                ->isIdenticalTo($faction)
            ->object($faction->removeCard($card))
                ->isIdenticalTo($faction)
        ;
    }
}

