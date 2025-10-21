<?php

namespace Toxicity\AlteredApi\tests\units\Service;

use Toxicity\AlteredApi\Service\FactionService;
use Toxicity\AlteredApi\Entity\Faction;
use Toxicity\AlteredApi\Builder\FactionBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use atoum\atoum;

/**
 * Test FactionService en mockant EntityRepository via EntityManager
 * Utilise l'entityClassName pour récupérer le bon repository
 */
class FactionServiceTest extends atoum\test
{
    public function getTestedClassName()
    {
        return 'Toxicity\AlteredApi\Service\FactionService';
    }

    public function testBuildFromDataWithMockedRepository()
    {
        echo "\n=== Test FactionService avec EntityRepository mocké ===\n\n";
        
        // 1. Mock EntityManager
        echo "1. Mock EntityManager\n";
        $mockEM = $this->newMockInstance(EntityManagerInterface::class);
        
        // 2. Mock EntityRepository avec orphanize pour éviter le constructeur
        echo "2. Mock EntityRepository avec orphanize\n";
        $this->mockGenerator->orphanize('__construct');
        $mockRepo = $this->newMockInstance(EntityRepository::class);
        
        // 3. Configurer le mock repository pour retourner null (nouvelle faction)
        echo "3. Configuration findOneBy() pour retourner null\n";
        $this->calling($mockRepo)->findOneBy = null;
        
        // 5. Configurer EntityManager pour retourner le mock repository
        //    Quand on appelle getRepository(Faction::class)
        echo "5. Configuration getRepository() pour retourner le mock\n";
        $this->calling($mockEM)->getRepository = function($className) use ($mockRepo) {
            echo "   → getRepository('$className') appelé\n";
            return $mockRepo;
        };
        
        // 6. Configurer persist et flush
        $this->calling($mockEM)->persist = null;
        $this->calling($mockEM)->flush = null;
        
        // 7. Créer le service
        echo "6. Création FactionService\n";
        $factionBuilder = new FactionBuilder();
        $service = new FactionService($factionBuilder, $mockEM);
        
        // 8. Test
        echo "7. Appel buildFromData()\n";
        $data = [
            'reference' => 'AX',
            'name' => 'Axiom',
            'id' => '1',
            'isActive' => true,
        ];
        
        $faction = $service->buildFromData($data);
        
        echo "8. Vérifications\n";
        
        // 9. Assertions
        $this
            ->object($faction)
                ->isInstanceOf(Faction::class)
            ->string($faction->getReference())
                ->isEqualTo('AX')
            ->string($faction->getName())
                ->isEqualTo('Axiom')
        ;
        
        // 10. Vérifier que getRepository a été appelé avec le bon entityClassName
        echo "9. Vérification des appels de méthodes\n";
        $this->mock($mockEM)
            ->call('getRepository')
                ->withArguments(Faction::class)
                ->once()
        ;
        
        echo "\n✅ SUCCESS! FactionService testé avec mock EntityRepository!\n";
    }
    
    public function testBuildFromDataUpdatesExistingFaction()
    {
        echo "\n=== Test mise à jour faction existante ===\n\n";
        
        // 1. Préparer une faction existante
        $existingFaction = new Faction();
        $existingFaction->setReference('BR');
        $existingFaction->setName('Old Name');
        $existingFaction->setAlteredId('2');
        
        // 2. Mock repository pour retourner la faction existante
        $this->mockGenerator->orphanize('__construct');
        $mockRepo = $this->newMockInstance(EntityRepository::class);
        $this->calling($mockRepo)->findOneBy = $existingFaction;
        
        // 3. Mock EntityManager
        $mockEM = $this->newMockInstance(EntityManagerInterface::class);
        $this->calling($mockEM)->getRepository = $mockRepo;
        $this->calling($mockEM)->persist = null;
        $this->calling($mockEM)->flush = null;
        
        // 4. Créer le service
        $factionBuilder = new FactionBuilder();
        $service = new FactionService($factionBuilder, $mockEM);
        
        // 5. Mettre à jour
        $data = [
            'reference' => 'BR',
            'name' => 'Bravos Updated',
            'id' => '2',
            'isActive' => true,
        ];
        
        $updatedFaction = $service->buildFromData($data);
        
        // 6. Vérifier que c'est la même instance
        $this
            ->object($updatedFaction)
                ->isIdenticalTo($existingFaction)
            ->string($updatedFaction->getName())
                ->isEqualTo('Bravos Updated')
        ;
        
        echo "✅ SUCCESS! Mise à jour testée!\n";
    }
    
    public function testSaveCallsPersistAndFlush()
    {
        echo "\n=== Test save() ===\n\n";
        
        // Mock repository
        $this->mockGenerator->orphanize('__construct');
        $mockRepo = $this->newMockInstance(EntityRepository::class);
        
        // Mock EntityManager
        $mockEM = $this->newMockInstance(EntityManagerInterface::class);
        $this->calling($mockEM)->getRepository = $mockRepo;
        $this->calling($mockEM)->persist = null;
        $this->calling($mockEM)->flush = null;
        
        // Créer le service
        $factionBuilder = new FactionBuilder();
        $service = new FactionService($factionBuilder, $mockEM);
        
        // Sauvegarder une faction
        $faction = new Faction();
        $faction->setReference('MU');
        $faction->setName('Muna');
        
        $service->save($faction);
        
        // Vérifier les appels
        $this->mock($mockEM)
            ->call('persist')
                ->withArguments($faction)
                ->once()
            ->call('flush')
                ->once()
        ;
        
        echo "✅ SUCCESS! save() vérifié!\n";
    }
}

