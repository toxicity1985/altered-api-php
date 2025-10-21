<?php

namespace Toxicity\AlteredApi\tests\units\Builder;

use atoum\atoum\test;
use Doctrine\ORM\EntityRepository;
use Toxicity\AlteredApi\Builder\CardBuilder;
use Toxicity\AlteredApi\Entity\Card;
use Toxicity\AlteredApi\Entity\Faction;
use Toxicity\AlteredApi\Entity\Set;
use Toxicity\AlteredApi\Model\CardRarityConstant;
use Toxicity\AlteredApi\Model\CardSubTypeConstant;
use Toxicity\AlteredApi\Model\CardTypeConstant;

class CardBuilderTest extends test
{
    public function getTestedClassName()
    {
        return 'Toxicity\AlteredApi\Builder\CardBuilder';
    }

    public function testBuildNewCardWithMinimalData()
    {
        $this->mockGenerator->orphanize('__construct');
        
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);
        
        $this->calling($mockSetRepo)->findOneBy = null;
        $this->calling($mockFactionRepo)->findOneBy = null;

        $builder = new CardBuilder($mockFactionRepo, $mockSetRepo);
        $card = new Card();
        
        $data = [
            'id' => 'card-123',
            'reference' => 'CARD_REF_123',
            'name' => 'Test Card',
            'imagePath' => '/path/to/image.jpg',
            'cardSet' => ['reference' => 'SET_REF']
        ];

        $result = $builder->build($card, $data, 'en-US');

        $this
            ->object($result)
                ->isInstanceOf(Card::class)
            ->string($result->getAlteredId())
                ->isEqualTo('card-123')
            ->string($result->getReference())
                ->isEqualTo('CARD_REF_123')
            ->string($result->getName())
                ->isEqualTo('Test Card')
            ->string($result->getImgPath())
                ->isEqualTo('/path/to/image.jpg')
        ;
    }

    public function testBuildCardWithSet()
    {
        $mockSet = $this->newMockInstance(Set::class);
        $this->calling($mockSet)->getId = 1;
        $this->calling($mockSet)->getReference = 'TEST_SET';
        
        $this->mockGenerator->orphanize('__construct');
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);
        $this->mockGenerator->orphanize(false);
        
        $this->calling($mockSetRepo)->findOneBy = $mockSet;
        $this->calling($mockFactionRepo)->findOneBy = null;

        $builder = new CardBuilder($mockFactionRepo, $mockSetRepo);
        $card = new Card();
        
        $data = [
            'id' => 'card-456',
            'reference' => 'CARD_REF_456',
            'name' => 'Card with Set',
            'imagePath' => '/path/to/image.jpg',
            'cardSet' => ['reference' => 'TEST_SET']
        ];

        $result = $builder->build($card, $data, 'en-US');

        $this
            ->object($result->getSet())
                ->isIdenticalTo($mockSet)
        ;
    }

    public function testBuildCardWithFaction()
    {
        $mockFaction = $this->newMockInstance(Faction::class);
        $this->calling($mockFaction)->getId = 1;
        $this->calling($mockFaction)->getReference = 'AXIOM';
        
        $this->mockGenerator->orphanize('__construct');
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);
        $this->mockGenerator->orphanize(false);
        
        $this->calling($mockSetRepo)->findOneBy = null;
        $this->calling($mockFactionRepo)->findOneBy = $mockFaction;

        $builder = new CardBuilder($mockFactionRepo, $mockSetRepo);
        $card = new Card();
        
        $data = [
            'id' => 'card-789',
            'reference' => 'CARD_REF_789',
            'name' => 'Card with Faction',
            'imagePath' => '/path/to/image.jpg',
            'cardSet' => ['reference' => 'SET_REF'],
            'mainFaction' => ['reference' => 'AXIOM']
        ];

        $result = $builder->build($card, $data, 'en-US');

        $this
            ->object($result->getFaction())
                ->isIdenticalTo($mockFaction)
        ;
    }

    public function testBuildCardWithElements()
    {
        $this->mockGenerator->orphanize('__construct');
        
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);
        
        $this->calling($mockSetRepo)->findOneBy = null;
        $this->calling($mockFactionRepo)->findOneBy = null;

        $builder = new CardBuilder($mockFactionRepo, $mockSetRepo);
        $card = new Card();
        
        $data = [
            'id' => 'card-elem',
            'reference' => 'CARD_ELEM',
            'name' => 'Card with Elements',
            'imagePath' => '/path/to/image.jpg',
            'cardSet' => ['reference' => 'SET_REF'],
            'elements' => [
                'MAIN_COST' => 3,
                'RECALL_COST' => 5,
                'OCEAN_POWER' => 2,
                'MOUNTAIN_POWER' => 4,
                'FOREST_POWER' => 1,
                'PERMANENT' => true,
                'ECHO_EFFECT' => 'Echo effect text',
                'MAIN_EFFECT' => 'Main effect text'
            ]
        ];

        $result = $builder->build($card, $data, 'en-US');

        $this
            ->integer($result->getMainCost())
                ->isEqualTo(3)
            ->integer($result->getRecallCost())
                ->isEqualTo(5)
            ->integer($result->getOceanPower())
                ->isEqualTo(2)
            ->integer($result->getMountainPower())
                ->isEqualTo(4)
            ->integer($result->getForestPower())
                ->isEqualTo(1)
            ->integer($result->getPermanent())
                ->isEqualTo(1)
        ;
    }

    public function testBuildCardWithRarity()
    {
        $this->mockGenerator->orphanize('__construct');
        
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);
        
        $this->calling($mockSetRepo)->findOneBy = null;
        $this->calling($mockFactionRepo)->findOneBy = null;

        $builder = new CardBuilder($mockFactionRepo, $mockSetRepo);
        $card = new Card();
        
        $data = [
            'id' => 'card-rare',
            'reference' => 'CARD_RARE',
            'name' => 'Rare Card',
            'imagePath' => '/path/to/image.jpg',
            'cardSet' => ['reference' => 'SET_REF'],
            'rarity' => ['reference' => 'RARE']
        ];

        $result = $builder->build($card, $data, 'en-US');

        $this
            ->string($result->getRarityString())
                ->isEqualTo(CardRarityConstant::RARE)
        ;
    }

    public function testBuildCardWithCardType()
    {
        $this->mockGenerator->orphanize('__construct');
        
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);
        
        $this->calling($mockSetRepo)->findOneBy = null;
        $this->calling($mockFactionRepo)->findOneBy = null;

        $builder = new CardBuilder($mockFactionRepo, $mockSetRepo);
        $card = new Card();
        
        $data = [
            'id' => 'card-type',
            'reference' => 'CARD_TYPE',
            'name' => 'Card with Type',
            'imagePath' => '/path/to/image.jpg',
            'cardSet' => ['reference' => 'SET_REF'],
            'cardType' => ['reference' => 'CHARACTER']
        ];

        $result = $builder->build($card, $data, 'en-US');

        $this
            ->string($result->getTypeString())
                ->isEqualTo(CardTypeConstant::CHARACTER)
        ;
    }

    public function testBuildCardWithSubTypes()
    {
        $this->mockGenerator->orphanize('__construct');
        
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);
        
        $this->calling($mockSetRepo)->findOneBy = null;
        $this->calling($mockFactionRepo)->findOneBy = null;

        $builder = new CardBuilder($mockFactionRepo, $mockSetRepo);
        $card = new Card();
        
        $data = [
            'id' => 'card-sub',
            'reference' => 'CARD_SUB',
            'name' => 'Card with SubTypes',
            'imagePath' => '/path/to/image.jpg',
            'cardSet' => ['reference' => 'SET_REF'],
            'cardSubTypes' => [
                ['reference' => 'SOLDIER'],
                ['reference' => 'MAGE']
            ]
        ];

        $result = $builder->build($card, $data, 'en-US');

        $this
            ->array($result->getSubTypeArray())
                ->contains(CardSubTypeConstant::SOLDIER)
                ->contains(CardSubTypeConstant::MAGE)
                ->size->isEqualTo(2)
        ;
    }

    public function testBuildCardWithTranslation()
    {
        $this->mockGenerator->orphanize('__construct');
        
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);
        
        $this->calling($mockSetRepo)->findOneBy = null;
        $this->calling($mockFactionRepo)->findOneBy = null;

        $builder = new CardBuilder($mockFactionRepo, $mockSetRepo);
        $card = new Card();
        
        $data = [
            'id' => 'card-trans',
            'reference' => 'CARD_TRANS',
            'name' => 'Carte avec Traduction',
            'imagePath' => '/path/to/image.jpg',
            'cardSet' => ['reference' => 'SET_REF'],
            'elements' => [
                'ECHO_EFFECT' => 'Effet écho',
                'MAIN_EFFECT' => 'Effet principal 1  Effet principal 2  Effet principal 3'
            ]
        ];

        $result = $builder->build($card, $data, 'fr-FR');

        $translations = $result->getTranslations();
        
        $this
            ->integer($translations->count())
                ->isEqualTo(1)
        ;
        
        $translation = $translations->first();
        
        $this
            ->string($translation->getLocale())
                ->isEqualTo('fr')
            ->string($translation->getName())
                ->isEqualTo('Carte avec Traduction')
            ->string($translation->getEchoEffect())
                ->isEqualTo('Effet écho')
            ->string($translation->getMainEffect1())
                ->isEqualTo('Effet principal 1')
            ->string($translation->getMainEffect2())
                ->isEqualTo('Effet principal 2')
            ->string($translation->getMainEffect3())
                ->isEqualTo('Effet principal 3')
        ;
    }

    public function testBuildExistingCardUpdatesTimestamp()
    {
        $this->mockGenerator->orphanize('__construct');
        
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);
        
        $this->calling($mockSetRepo)->findOneBy = null;
        $this->calling($mockFactionRepo)->findOneBy = null;

        $builder = new CardBuilder($mockFactionRepo, $mockSetRepo);
        
        $card = $this->newMockInstance(Card::class);
        $this->calling($card)->getId = 42;

        $data = [
            'id' => 'card-update',
            'reference' => 'CARD_UPDATE',
            'name' => 'Updated Card',
            'imagePath' => '/path/to/image.jpg',
            'cardSet' => ['reference' => 'SET_REF']
        ];

        $result = $builder->build($card, $data, 'en-US');

        $this
            ->mock($result)
                ->call('setUpdatedDate')
                    ->atLeastOnce()
        ;
    }

    public function testBuildCardWithAllImagePath()
    {
        $this->mockGenerator->orphanize('__construct');
        
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);
        
        $this->calling($mockSetRepo)->findOneBy = null;
        $this->calling($mockFactionRepo)->findOneBy = null;

        $builder = new CardBuilder($mockFactionRepo, $mockSetRepo);
        $card = new Card();
        
        $data = [
            'id' => 'card-all-img',
            'reference' => 'CARD_ALL_IMG',
            'name' => 'Card with All Images',
            'imagePath' => '/path/to/image.jpg',
            'allImagePath' => ['/path/1.jpg', '/path/2.jpg'],
            'cardSet' => ['reference' => 'SET_REF']
        ];

        $result = $builder->build($card, $data, 'en-US');

        $this
            ->array($result->getAllImagePath())
                ->contains('/path/1.jpg')
                ->contains('/path/2.jpg')
        ;
    }

    public function testBuildCardWithSuspendedFlag()
    {
        $this->mockGenerator->orphanize('__construct');
        
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);
        
        $this->calling($mockSetRepo)->findOneBy = null;
        $this->calling($mockFactionRepo)->findOneBy = null;

        $builder = new CardBuilder($mockFactionRepo, $mockSetRepo);
        $card = new Card();
        
        $data = [
            'id' => 'card-suspended',
            'reference' => 'CARD_SUSPENDED',
            'name' => 'Suspended Card',
            'imagePath' => '/path/to/image.jpg',
            'isSuspended' => true,
            'cardSet' => ['reference' => 'SET_REF']
        ];

        $result = $builder->build($card, $data, 'en-US');

        $this
            ->boolean($result->isSuspended())
                ->isTrue()
        ;
    }

    public function testBuildFluentInterface()
    {
        $this->mockGenerator->orphanize('__construct');
        
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);
        
        $this->calling($mockSetRepo)->findOneBy = null;
        $this->calling($mockFactionRepo)->findOneBy = null;

        $builder = new CardBuilder($mockFactionRepo, $mockSetRepo);
        $card = new Card();
        
        $data = [
            'id' => 'card-fluent',
            'reference' => 'CARD_FLUENT',
            'name' => 'Fluent Card',
            'imagePath' => '/path/to/image.jpg',
            'cardSet' => ['reference' => 'SET_REF']
        ];

        $result = $builder->build($card, $data, 'en-US');

        $this
            ->object($result)
                ->isIdenticalTo($card)
        ;
    }
}

