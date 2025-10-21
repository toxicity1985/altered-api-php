<?php

namespace Toxicity\AlteredApi\tests\units\Service;

use Toxicity\AlteredApi\Service\CardService;
use Toxicity\AlteredApi\Entity\Card;
use Toxicity\AlteredApi\Entity\Faction;
use Toxicity\AlteredApi\Entity\Set;
use Toxicity\AlteredApi\Model\CardFactionConstant;
use Toxicity\AlteredApi\Model\CardRarityConstant;
use Toxicity\AlteredApi\Model\CardSetConstant;
use Toxicity\AlteredApi\Model\CardTypeConstant;
use Toxicity\AlteredApi\tests\fixtures\CardDataBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use atoum\atoum;

/**
 * Test CardService with mocked EntityRepository
 */
class CardServiceTest extends atoum\test
{
    public function getTestedClassName()
    {
        return 'Toxicity\AlteredApi\Service\CardService';
    }

    public function testBuildFromDataWithMockedRepositories()
    {
        // 1. Mock EntityManager
        $mockEM = $this->newMockInstance(EntityManagerInterface::class);
        
        // 2. Mock repositories (Card, Faction, Set)
        $this->mockGenerator->orphanize('__construct');
        $mockCardRepo = $this->newMockInstance(EntityRepository::class);
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);
        
        // 3. Prepare test data - Faction
        $faction = new Faction();
        $faction->setReference(CardFactionConstant::AXIOM);
        $faction->setName('Axiom');
        
        // 4. Prepare test data - Set
        $set = new Set();
        $set->setReference(CardSetConstant::COREKS);
        $set->setName('Core');
        
        // 5. Configure mocks to return test data
        $this->calling($mockCardRepo)->findOneBy = null; // New card
        $this->calling($mockFactionRepo)->findOneBy = $faction;
        $this->calling($mockSetRepo)->findOneBy = $set;
        
        // 6. Configure EntityManager to return appropriate repository
        $this->calling($mockEM)->getRepository = function($className) use ($mockCardRepo, $mockFactionRepo, $mockSetRepo) {
            if ($className === Card::class) {
                return $mockCardRepo;
            } elseif ($className === Faction::class) {
                return $mockFactionRepo;
            } elseif ($className === Set::class) {
                return $mockSetRepo;
            }
            return $mockCardRepo;
        };
        
        // 7. Configure persist and flush
        $this->calling($mockEM)->persist = null;
        $this->calling($mockEM)->flush = null;
        
        // 8. Create service
        $service = new CardService($mockEM);
        
        // 9. Test buildFromData with CardDataBuilder
        $cardData = CardDataBuilder::createDefaultCardData();
        $card = $service->buildFromData($cardData);
        
        // 10. Assertions
        $this
            ->object($card)
                ->isInstanceOf(Card::class)
            ->string($card->getReference())
                ->isEqualTo('ALT_CORE_AX_01_R1')
        ;
    }

    public function testBuildFromDataUpdatesExistingCard()
    {
        // Setup
        $this->mockGenerator->orphanize('__construct');
        $mockCardRepo = $this->newMockInstance(EntityRepository::class);
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);

        $mockEM = $this->newMockInstance(EntityManagerInterface::class);
        $this->calling($mockEM)->persist = null;
        $this->calling($mockEM)->flush = null;

        // Add existing card
        $existingCard = new Card();
        $existingCard->setReference('ALT_CORE_AX_01_R1');
        $existingCard->setName('Old Name');

        // Add faction and set
        $faction = new Faction();
        $faction->setReference(CardFactionConstant::AXIOM);
        $faction->setName('Axiom');

        $set = new Set();
        $set->setReference(CardSetConstant::COREKS);
        $set->setName('Core');

        // Configure repositories
        $this->calling($mockCardRepo)->findOneBy = $existingCard;
        $this->calling($mockFactionRepo)->findOneBy = $faction;
        $this->calling($mockSetRepo)->findOneBy = $set;

        $this->calling($mockEM)->getRepository = function($className) use ($mockCardRepo, $mockFactionRepo, $mockSetRepo) {
            if ($className === Card::class) return $mockCardRepo;
            if ($className === Faction::class) return $mockFactionRepo;
            if ($className === Set::class) return $mockSetRepo;
            return $mockCardRepo;
        };

        $service = new CardService($mockEM);

        // Test: update existing card
        $cardData = CardDataBuilder::createDefaultCardData([
            'name' => 'Updated Card Name',
        ]);

        $updatedCard = $service->buildFromData($cardData);

        // Verify it's the same instance that was updated
        $this
            ->object($updatedCard)
                ->isIdenticalTo($existingCard)
            ->string($updatedCard->getName())
                ->isEqualTo('Updated Card Name')
        ;
    }

    public function testSaveCallsPersistAndFlush()
    {
        $this->mockGenerator->orphanize('__construct');
        $mockCardRepo = $this->newMockInstance(EntityRepository::class);
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);

        $mockEM = $this->newMockInstance(EntityManagerInterface::class);
        $this->calling($mockEM)->persist = null;
        $this->calling($mockEM)->flush = null;
        
        $this->calling($mockEM)->getRepository = function($className) use ($mockCardRepo, $mockFactionRepo, $mockSetRepo) {
            if ($className === Card::class) return $mockCardRepo;
            if ($className === Faction::class) return $mockFactionRepo;
            if ($className === Set::class) return $mockSetRepo;
            return $mockCardRepo;
        };

        $service = new CardService($mockEM);

        $card = new Card();
        $card->setReference('TEST_CARD');

        $service->save($card);

        // Verify that persist and flush were called
        $this->mock($mockEM)
            ->call('persist')
                ->atLeastOnce()
        ;

        $this->mock($mockEM)
            ->call('flush')
                ->once()
        ;
    }

    public function testBuildCharacterCard()
    {
        $this->mockGenerator->orphanize('__construct');
        $mockCardRepo = $this->newMockInstance(EntityRepository::class);
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);

        $mockEM = $this->newMockInstance(EntityManagerInterface::class);
        $this->calling($mockEM)->persist = null;
        $this->calling($mockEM)->flush = null;

        // Prepare faction and set
        $faction = new Faction();
        $faction->setReference(CardFactionConstant::AXIOM);
        $faction->setName('Axiom');

        $set = new Set();
        $set->setReference(CardSetConstant::COREKS);
        $set->setName('Core');

        $this->calling($mockCardRepo)->findOneBy = null;
        $this->calling($mockFactionRepo)->findOneBy = $faction;
        $this->calling($mockSetRepo)->findOneBy = $set;
        
        $this->calling($mockEM)->getRepository = function($className) use ($mockCardRepo, $mockFactionRepo, $mockSetRepo) {
            if ($className === Card::class) return $mockCardRepo;
            if ($className === Faction::class) return $mockFactionRepo;
            if ($className === Set::class) return $mockSetRepo;
            return $mockCardRepo;
        };

        $service = new CardService($mockEM);

        // Use builder for a CHARACTER card
        $cardData = CardDataBuilder::createCharacterCardData([
            'reference' => 'ALT_CORE_AX_02_C',
            'name' => 'Test Character',
        ]);

        $card = $service->buildFromData($cardData);

        $this
            ->object($card)
                ->isInstanceOf(Card::class)
            ->integer($card->getMainCost())
                ->isEqualTo(3)
            ->integer($card->getOceanPower())
                ->isEqualTo(2)
        ;
    }

    public function testBuildRareCard()
    {
        $this->mockGenerator->orphanize('__construct');
        $mockCardRepo = $this->newMockInstance(EntityRepository::class);
        $mockFactionRepo = $this->newMockInstance(EntityRepository::class);
        $mockSetRepo = $this->newMockInstance(EntityRepository::class);

        $mockEM = $this->newMockInstance(EntityManagerInterface::class);
        $this->calling($mockEM)->persist = null;
        $this->calling($mockEM)->flush = null;

        // Prepare faction and set
        $faction = new Faction();
        $faction->setReference(CardFactionConstant::BRAVOS);
        $faction->setName('Bravos');

        $set = new Set();
        $set->setReference(CardSetConstant::COREKS);
        $set->setName('Core');

        $this->calling($mockCardRepo)->findOneBy = null;
        $this->calling($mockFactionRepo)->findOneBy = $faction;
        $this->calling($mockSetRepo)->findOneBy = $set;
        
        $this->calling($mockEM)->getRepository = function($className) use ($mockCardRepo, $mockFactionRepo, $mockSetRepo) {
            if ($className === Card::class) return $mockCardRepo;
            if ($className === Faction::class) return $mockFactionRepo;
            if ($className === Set::class) return $mockSetRepo;
            return $mockCardRepo;
        };

        $service = new CardService($mockEM);

        // Use builder for a RARE Bravos card
        $cardData = CardDataBuilder::createCardForFaction(
            CardFactionConstant::BRAVOS,
            [
                'rarity' => ['reference' => CardRarityConstant::RARE],
                'name' => 'Rare Bravos Card',
            ]
        );

        $card = $service->buildFromData($cardData);

        $this
            ->object($card)
                ->isInstanceOf(Card::class)
            ->string($card->getName())
                ->isEqualTo('Rare Bravos Card')
            ->array($card->getRarity())
                ->hasKey('reference')
            ->string($card->getRarity()['reference'])
                ->isEqualTo(CardRarityConstant::RARE)
        ;
    }
}
