<?php

namespace Toxicity\AlteredApi\tests\units\Builder;

use atoum\atoum\test;
use Toxicity\AlteredApi\Builder\FactionBuilder;
use Toxicity\AlteredApi\Entity\Faction;

class FactionBuilderTest extends test
{
    public function getTestedClassName()
    {
        return 'Toxicity\AlteredApi\Builder\FactionBuilder';
    }

    public function testBuildNewFaction()
    {
        $builder = new FactionBuilder();
        $faction = new Faction();
        
        $data = [
            'id' => 'faction-123',
            'name' => 'Test Faction',
            'reference' => 'TEST_FACTION'
        ];

        $result = $builder->build($faction, $data);

        $this
            ->object($result)
                ->isInstanceOf(Faction::class)
            ->string($result->getAlteredId())
                ->isEqualTo('faction-123')
            ->string($result->getName())
                ->isEqualTo('Test Faction')
            ->string($result->getReference())
                ->isEqualTo('TEST_FACTION')
            ->variable($result->getUpdatedDate())
                ->isNull()
        ;
    }

    public function testBuildExistingFactionUpdatesTimestamp()
    {
        $builder = new FactionBuilder();
        $faction = $this->newMockInstance(Faction::class);
        
        $this->calling($faction)->getId = 42;

        $data = [
            'id' => 'faction-456',
            'name' => 'Updated Faction',
            'reference' => 'UPDATED_FACTION'
        ];

        $result = $builder->build($faction, $data);

        $this
            ->mock($result)
                ->call('setUpdatedDate')
                    ->once()
        ;
    }

    public function testBuildFluentInterface()
    {
        $builder = new FactionBuilder();
        $faction = new Faction();
        
        $data = [
            'id' => 'faction-789',
            'name' => 'Fluent Faction',
            'reference' => 'FLUENT_FACTION'
        ];

        $result = $builder->build($faction, $data);

        $this
            ->object($result)
                ->isIdenticalTo($faction)
        ;
    }
}

