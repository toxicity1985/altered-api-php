<?php

namespace Toxicity\AlteredApi\tests\units\Entity;

use Toxicity\AlteredApi\Entity\Set;
use atoum\atoum;

class SetTest extends atoum\test
{
    public function getTestedClassName()
    {
        return 'Toxicity\AlteredApi\Entity\Set';
    }

    public function testConstruct()
    {
        $set = new Set();

        $this
            ->object($set)
                ->isInstanceOf(Set::class)
            ->variable($set->getId())
                ->isNull()
        ;
    }

    public function testSettersAndGetters()
    {
        $set = new Set();

        $this
            ->given($set->setName('Beyond the Gates'))
            ->then
                ->string($set->getName())
                    ->isEqualTo('Beyond the Gates')
        ;

        $this
            ->given($set->setNameEn('Beyond the Gates'))
            ->then
                ->string($set->getNameEn())
                    ->isEqualTo('Beyond the Gates')
        ;

        $this
            ->given($set->setReference('BTG'))
            ->then
                ->string($set->getReference())
                    ->isEqualTo('BTG')
        ;

        $this
            ->given($set->setCode('BTG'))
            ->then
                ->string($set->getCode())
                    ->isEqualTo('BTG')
        ;

        $this
            ->given($set->setAlteredId('ALT789'))
            ->then
                ->string($set->getAlteredId())
                    ->isEqualTo('ALT789')
        ;

        $this
            ->given($set->setIsActive('1'))
            ->then
                ->string($set->getIsActive())
                    ->isEqualTo('1')
        ;

        $this
            ->given($set->setIllustration('illustration.png'))
            ->then
                ->string($set->getIllustration())
                    ->isEqualTo('illustration.png')
        ;

        $this
            ->given($set->setIllustrationPath('/path/to/illustration.png'))
            ->then
                ->string($set->getIllustrationPath())
                    ->isEqualTo('/path/to/illustration.png')
        ;
    }

    public function testDateProperty()
    {
        $set = new Set();
        $date = new \DateTimeImmutable('2024-01-01');

        $this
            ->given($set->setDate($date))
            ->then
                ->object($set->getDate())
                    ->isInstanceOf(\DateTimeImmutable::class)
                ->dateTime($set->getDate())
                    ->isEqualTo($date)
        ;
    }

    public function testCardGoogleSheets()
    {
        $set = new Set();
        $sheets = ['sheet1', 'sheet2', 'sheet3'];

        $this
            ->given($set->setCardGoogleSheets($sheets))
            ->then
                ->array($set->getCardGoogleSheets())
                    ->isEqualTo($sheets)
                    ->hasSize(3)
        ;
    }

    public function testFluentInterface()
    {
        $set = new Set();

        $this
            ->object($set->setName('Test Set'))
                ->isIdenticalTo($set)
            ->object($set->setReference('TS'))
                ->isIdenticalTo($set)
            ->object($set->setIsActive('1'))
                ->isIdenticalTo($set)
        ;
    }

    public function testSetId()
    {
        $set = new Set();
        
        $this
            ->variable($set->getId())
                ->isNull()
        ;

        $set->setId(42);

        $this
            ->integer($set->getId())
                ->isEqualTo(42)
        ;
    }
}


