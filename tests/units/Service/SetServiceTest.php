<?php

namespace Toxicity\AlteredApi\tests\units\Service;

use Toxicity\AlteredApi\Service\SetService;
use Toxicity\AlteredApi\Entity\Set;
use Toxicity\AlteredApi\Builder\SetBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use atoum\atoum;

/**
 * Test SetService en mockant EntityRepository via EntityManager
 */
class SetServiceTest extends atoum\test
{
    public function getTestedClassName()
    {
        return 'Toxicity\AlteredApi\Service\SetService';
    }

    public function testBuildFromDataWithMockedRepository()
    {
        // 1. Mock EntityManager
        $mockEM = $this->newMockInstance(EntityManagerInterface::class);
        
        // 2. Mock EntityRepository avec orphanize
        $this->mockGenerator->orphanize('__construct');
        $mockRepo = $this->newMockInstance(EntityRepository::class);
        
        // 3. Configurer findOneBy() pour retourner null (nouveau set)
        $this->calling($mockRepo)->findOneBy = null;
        
        // 4. Configurer EntityManager pour retourner le mock repository
        $this->calling($mockEM)->getRepository = function($className) use ($mockRepo) {
            return $mockRepo;
        };
        
        // 5. Configurer persist et flush
        $this->calling($mockEM)->persist = null;
        $this->calling($mockEM)->flush = null;
        
        // 6. Créer le service
        $setBuilder = new SetBuilder();
        $service = new SetService($setBuilder, $mockEM);
        
        // 7. Test
        $data = [
            'reference' => 'COREKS',
            'name' => 'Core Set',
            'id' => '1',
            'isActive' => true,
            'cardGoogleSheets' => [],
        ];
        
        $set = $service->buildFromData($data);
        
        // 8. Assertions
        $this
            ->object($set)
                ->isInstanceOf(Set::class)
            ->string($set->getReference())
                ->isEqualTo('COREKS')
            ->string($set->getName())
                ->isEqualTo('Core Set')
        ;
        
        // 9. Vérifier que getRepository a été appelé
        $this->mock($mockEM)
            ->call('getRepository')
                ->withArguments(Set::class)
                ->once()
        ;
    }
    
    public function testBuildFromDataUpdatesExistingSet()
    {
        // 1. Préparer un set existant
        $existingSet = new Set();
        $existingSet->setReference('CORE');
        $existingSet->setName('Old Name');
        $existingSet->setAlteredId('2');
        
        // 2. Mock repository pour retourner le set existant
        $this->mockGenerator->orphanize('__construct');
        $mockRepo = $this->newMockInstance(EntityRepository::class);
        $this->calling($mockRepo)->findOneBy = $existingSet;
        
        // 3. Mock EntityManager
        $mockEM = $this->newMockInstance(EntityManagerInterface::class);
        $this->calling($mockEM)->getRepository = $mockRepo;
        $this->calling($mockEM)->persist = null;
        $this->calling($mockEM)->flush = null;
        
        // 4. Créer le service
        $setBuilder = new SetBuilder();
        $service = new SetService($setBuilder, $mockEM);
        
        // 5. Mettre à jour
        $data = [
            'reference' => 'CORE',
            'name' => 'Core Updated',
            'id' => '2',
            'isActive' => true,
            'cardGoogleSheets' => [],
        ];
        
        $updatedSet = $service->buildFromData($data);
        
        // 6. Vérifier que c'est la même instance
        $this
            ->object($updatedSet)
                ->isIdenticalTo($existingSet)
            ->string($updatedSet->getName())
                ->isEqualTo('Core Updated')
        ;
    }
    
    public function testSaveCallsPersistAndFlush()
    {
        // Mock repository
        $this->mockGenerator->orphanize('__construct');
        $mockRepo = $this->newMockInstance(EntityRepository::class);
        
        // Mock EntityManager
        $mockEM = $this->newMockInstance(EntityManagerInterface::class);
        $this->calling($mockEM)->getRepository = $mockRepo;
        $this->calling($mockEM)->persist = null;
        $this->calling($mockEM)->flush = null;
        
        // Créer le service
        $setBuilder = new SetBuilder();
        $service = new SetService($setBuilder, $mockEM);
        
        // Sauvegarder un set
        $set = new Set();
        $set->setReference('ALIZE');
        $set->setName('Alize');
        
        $service->save($set);
        
        // Vérifier les appels
        $this->mock($mockEM)
            ->call('persist')
                ->withArguments($set)
                ->once()
            ->call('flush')
                ->once()
        ;
    }
}


