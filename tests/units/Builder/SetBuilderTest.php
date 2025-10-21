<?php

namespace Toxicity\AlteredApi\tests\units\Builder;

use atoum\atoum\test;
use DateTimeImmutable;
use Toxicity\AlteredApi\Builder\SetBuilder;
use Toxicity\AlteredApi\Entity\Set;

class SetBuilderTest extends test
{
    public function getTestedClassName()
    {
        return 'Toxicity\AlteredApi\Builder\SetBuilder';
    }

    public function testBuildNewSet()
    {
        $builder = new SetBuilder();
        $set = new Set();
        
        $data = [
            'id' => 'set-123',
            'code' => 'TST',
            'isActive' => true,
            'name' => 'Test Set',
            'reference' => 'TEST_SET',
            'illustration' => 'illustration.jpg',
            'illustrationPath' => '/path/to/illustration.jpg',
            'cardGoogleSheets' => ['https://sheets.google.com/test'],
            'date' => '2025-01-15'
        ];

        $result = $builder->build($set, $data);

        $this
            ->object($result)
                ->isInstanceOf(Set::class)
            ->string($result->getAlteredId())
                ->isEqualTo('set-123')
            ->string($result->getCode())
                ->isEqualTo('TST')
            ->string($result->getIsActive())
                ->isEqualTo('1')
            ->string($result->getName())
                ->isEqualTo('Test Set')
            ->string($result->getReference())
                ->isEqualTo('TEST_SET')
            ->string($result->getIllustration())
                ->isEqualTo('illustration.jpg')
            ->string($result->getIllustrationPath())
                ->isEqualTo('/path/to/illustration.jpg')
            ->array($result->getCardGoogleSheets())
                ->contains('https://sheets.google.com/test')
            ->object($result->getDate())
                ->isInstanceOf(DateTimeImmutable::class)
            ->variable($result->getUpdatedDate())
                ->isNull()
        ;
    }

    public function testBuildExistingSetUpdatesTimestamp()
    {
        $builder = new SetBuilder();
        $set = $this->newMockInstance(Set::class);
        
        $this->calling($set)->getId = 42;

        $data = [
            'id' => 'set-456',
            'name' => 'Updated Set',
            'reference' => 'UPDATED_SET'
        ];

        $result = $builder->build($set, $data);

        $this
            ->mock($result)
                ->call('setUpdatedDate')
                    ->once()
        ;
    }

    public function testBuildWithPartialData()
    {
        $builder = new SetBuilder();
        $set = new Set();
        
        $data = [
            'id' => 'set-789',
            'name' => 'Partial Set'
        ];

        $result = $builder->build($set, $data);

        $this
            ->object($result)
                ->isInstanceOf(Set::class)
            ->string($result->getAlteredId())
                ->isEqualTo('set-789')
            ->string($result->getName())
                ->isEqualTo('Partial Set')
        ;
    }

    public function testBuildFluentInterface()
    {
        $builder = new SetBuilder();
        $set = new Set();
        
        $data = [
            'id' => 'set-xyz',
            'name' => 'Fluent Set',
            'reference' => 'FLUENT_SET'
        ];

        $result = $builder->build($set, $data);

        $this
            ->object($result)
                ->isIdenticalTo($set)
        ;
    }

    public function testBuildWithDateParsing()
    {
        $builder = new SetBuilder();
        $set = new Set();
        
        $data = [
            'id' => 'set-date',
            'name' => 'Date Set',
            'date' => '2025-10-19T12:00:00+00:00'
        ];

        $result = $builder->build($set, $data);

        $this
            ->object($result->getDate())
                ->isInstanceOf(DateTimeImmutable::class)
            ->string($result->getDate()->format('Y-m-d'))
                ->isEqualTo('2025-10-19')
        ;
    }
}

