<?php

namespace Toxicity\AlteredApi\tests\units\examples;

use Toxicity\AlteredApi\Provider\AlteredHttpClient;
use Toxicity\AlteredApi\Exception\RateLimitExceededException;
use Toxicity\AlteredApi\Entity\Faction;
use Symfony\Contracts\HttpClient\ResponseInterface;
use atoum\atoum;

/**
 * Exemples avancés de mocking pour AlteredHttpClient
 * Démontre comment tester des scénarios complexes
 */
class AdvancedMockingExamplesTest extends atoum\test
{
    public function getTestedClassName()
    {
        return 'Toxicity\AlteredApi\Provider\AlteredHttpClient';
    }

    /**
     * Exemple 1 : Mock d'une réponse HTTP avec plusieurs appels
     */
    public function testMockHttpResponseMultipleCalls()
    {
        $mockResponse = $this->newMockInstance(ResponseInterface::class);
        
        $callCount = 0;
        
        // Le premier appel retourne 200, le second 429
        $this->calling($mockResponse)->getStatusCode = function() use (&$callCount) {
            $callCount++;
            return ($callCount === 1) ? 200 : 429;
        };
        
        // Simuler des réponses JSON différentes
        $this->calling($mockResponse)->toArray = function() use (&$callCount) {
            if ($callCount > 1) {
                return ['message' => 'Rate limit exceeded'];
            }
            return ['data' => ['cards' => []]];
        };
        
        // Premier appel : 200
        $this
            ->integer($mockResponse->getStatusCode())
                ->isEqualTo(200)
        ;
        
        // Second appel : 429
        $this
            ->integer($mockResponse->getStatusCode())
                ->isEqualTo(429)
            ->array($mockResponse->toArray())
                ->hasKey('message')
        ;
    }

    /**
     * Exemple 2 : Mock avec vérification des en-têtes
     */
    public function testMockResponseHeaders()
    {
        $mockResponse = $this->newMockInstance(ResponseInterface::class);
        
        // Simuler des en-têtes HTTP
        $headers = [
            'content-type' => ['application/json'],
            'x-ratelimit-limit' => ['100'],
            'x-ratelimit-remaining' => ['95'],
            'retry-after' => ['60']
        ];
        
        $this->calling($mockResponse)->getHeaders = $headers;
        $this->calling($mockResponse)->getStatusCode = 429;
        
        // Vérifier les en-têtes
        $responseHeaders = $mockResponse->getHeaders();
        
        $this
            ->array($responseHeaders)
                ->hasKey('retry-after')
            ->array($responseHeaders['retry-after'])
                ->contains('60')
        ;
    }

    /**
     * Exemple 3 : Mock avec différents types d'erreurs
     */
    public function testMockDifferentErrorTypes()
    {
        $mockResponse = $this->newMockInstance(ResponseInterface::class);
        
        // Simuler différents codes d'erreur
        $errors = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            404 => 'Not Found',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable'
        ];
        
        foreach ($errors as $code => $message) {
            $this->calling($mockResponse)->getStatusCode = $code;
            $this->calling($mockResponse)->toArray = ['error' => $message];
            
            $this
                ->integer($mockResponse->getStatusCode())
                    ->isEqualTo($code)
                ->array($mockResponse->toArray())
                    ->string['error']
                        ->isEqualTo($message)
            ;
        }
    }

    /**
     * Exemple 4 : Mock simple sans contraintes de type
     */
    public function testMockSimpleObject()
    {
        $mockFaction = $this->newMockInstance(Faction::class);
        
        // Définir le comportement
        $this->calling($mockFaction)->getName = 'Axiom';
        $this->calling($mockFaction)->getReference = 'AX';
        
        // Appeler les méthodes
        $name = $mockFaction->getName();
        $ref = $mockFaction->getReference();
        
        // Vérifications
        $this
            ->string($name)
                ->isEqualTo('Axiom')
            ->string($ref)
                ->isEqualTo('AX')
        ;
        
        // Vérifier les appels
        $this->mock($mockFaction)
            ->call('getName')
                ->once()
        ;
        
        $this->mock($mockFaction)
            ->call('getReference')
                ->once()
        ;
    }

    /**
     * Exemple 5 : Mock avec callbacks pour simuler un comportement réaliste
     */
    public function testMockWithRealisticBehavior()
    {
        $mockResponse = $this->newMockInstance(ResponseInterface::class);
        
        $requestCount = 0;
        $maxRequests = 3;
        
        // Simuler un rate limit après 3 requêtes
        $this->calling($mockResponse)->getStatusCode = function() use (&$requestCount, $maxRequests) {
            $requestCount++;
            return ($requestCount > $maxRequests) ? 429 : 200;
        };
        
        $this->calling($mockResponse)->toArray = function() use (&$requestCount, $maxRequests) {
            if ($requestCount > $maxRequests) {
                return ['message' => 'Rate limit exceeded'];
            }
            return ['data' => ['result' => 'success']];
        };
        
        // Simuler plusieurs requêtes
        $this->integer($mockResponse->getStatusCode())->isEqualTo(200);
        $this->integer($mockResponse->getStatusCode())->isEqualTo(200);
        $this->integer($mockResponse->getStatusCode())->isEqualTo(200);
        $this->integer($mockResponse->getStatusCode())->isEqualTo(429); // Rate limit !
    }

    /**
     * Exemple 6 : Mock avec assertion sur l'ordre des appels
     */
    public function testMockVerifyCallOrder()
    {
        $mockResponse = $this->newMockInstance(ResponseInterface::class);
        
        $this->calling($mockResponse)->getStatusCode = 200;
        $this->calling($mockResponse)->getHeaders = [];
        $this->calling($mockResponse)->toArray = ['data' => []];
        
        // Appeler dans un ordre spécifique
        $mockResponse->getStatusCode();
        $mockResponse->getHeaders();
        $mockResponse->toArray();
        
        // Vérifier que chaque méthode a été appelée au moins une fois
        $this->mock($mockResponse)
            ->call('getStatusCode')
                ->once()
        ;
        
        $this->mock($mockResponse)
            ->call('getHeaders')
                ->once()
        ;
        
        $this->mock($mockResponse)
            ->call('toArray')
                ->once()
        ;
    }

    /**
     * Exemple 7 : Mock avec spy pattern (capturer les arguments)
     */
    public function testMockSpyPattern()
    {
        $capturedArguments = [];
        
        $mockResponse = $this->newMockInstance(ResponseInterface::class);
        
        // "Espionner" les appels en capturant les arguments
        $this->calling($mockResponse)->getContent = function($throw = true) use (&$capturedArguments) {
            $capturedArguments[] = ['throw' => $throw];
            return json_encode(['data' => 'test']);
        };
        
        // Faire plusieurs appels avec différents arguments
        $mockResponse->getContent(true);
        $mockResponse->getContent(false);
        $mockResponse->getContent(true);
        
        // Vérifier que les arguments ont été capturés
        $this
            ->array($capturedArguments)
                ->hasSize(3)
            ->boolean($capturedArguments[0]['throw'])
                ->isTrue()
            ->boolean($capturedArguments[1]['throw'])
                ->isFalse()
            ->boolean($capturedArguments[2]['throw'])
                ->isTrue()
        ;
    }

    /**
     * Exemple 8 : Mock qui retourne des valeurs différentes en séquence
     */
    public function testMockSequentialReturns()
    {
        $mockResponse = $this->newMockInstance(ResponseInterface::class);
        
        $callCount = 0;
        $statuses = ['loading', 'processing', 'completed'];
        
        // Retourner des valeurs différentes à chaque appel avec une closure
        $this->calling($mockResponse)->toArray = function() use (&$callCount, $statuses) {
            $status = $statuses[min($callCount, count($statuses) - 1)];
            $callCount++;
            return ['status' => $status];
        };
        
        // Premier appel : loading
        $result1 = $mockResponse->toArray();
        $this
            ->array($result1)
                ->hasKey('status')
            ->string($result1['status'])
                ->isEqualTo('loading')
        ;
        
        // Deuxième appel : processing
        $result2 = $mockResponse->toArray();
        $this
            ->array($result2)
                ->hasKey('status')
            ->string($result2['status'])
                ->isEqualTo('processing')
        ;
        
        // Troisième appel : completed
        $result3 = $mockResponse->toArray();
        $this
            ->array($result3)
                ->hasKey('status')
            ->string($result3['status'])
                ->isEqualTo('completed')
        ;
        
        // Après la séquence, reste sur la dernière valeur
        $result4 = $mockResponse->toArray();
        $this
            ->array($result4)
                ->hasKey('status')
            ->string($result4['status'])
                ->isEqualTo('completed')
        ;
    }

    /**
     * Exemple 9 : Mock avec timeout simulation
     */
    public function testMockTimeoutSimulation()
    {
        $mockResponse = $this->newMockInstance(ResponseInterface::class);
        
        // Simuler un timeout après un délai
        $startTime = microtime(true);
        
        $this->calling($mockResponse)->toArray = function() use ($startTime) {
            $elapsed = microtime(true) - $startTime;
            
            if ($elapsed > 2) { // Simuler timeout après 2 secondes
                throw new \Exception('Request timeout');
            }
            
            return ['data' => 'success'];
        };
        
        // Première requête réussit
        $this
            ->array($mockResponse->toArray())
                ->hasKey('data')
        ;
    }

    /**
     * Exemple 10 : Mock avec état interne (stateful mock) simplifié
     */
    public function testStatefulMock()
    {
        $mockResponse = $this->newMockInstance(ResponseInterface::class);
        
        $remainingTokens = 100;
        
        // Simuler un compteur qui décrémente
        $this->calling($mockResponse)->getInfo = function() use (&$remainingTokens) {
            $remainingTokens -= 10;
            return ['remaining_tokens' => $remainingTokens];
        };
        
        // Consommer des tokens
        $result1 = $mockResponse->getInfo();
        $this->integer($result1['remaining_tokens'])->isEqualTo(90);
        
        $result2 = $mockResponse->getInfo();
        $this->integer($result2['remaining_tokens'])->isEqualTo(80);
        
        $result3 = $mockResponse->getInfo();
        $this->integer($result3['remaining_tokens'])->isEqualTo(70);
    }
}

