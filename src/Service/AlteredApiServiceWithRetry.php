<?php

namespace Toxicity\AlteredApi\Service;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Toxicity\AlteredApi\Exception\RateLimitExceededException;
use Toxicity\AlteredApi\Provider\AlteredHttpClient;
use Toxicity\AlteredApi\Request\SearchCardRequest;

/**
 * Version étendue du service API avec gestion automatique des erreurs 429
 */
readonly class AlteredApiServiceWithRetry extends AlteredApiService
{
    private const MAX_RETRY_ATTEMPTS = 3;
    private const DEFAULT_RETRY_DELAY = 60; // 60 secondes par défaut

    /**
     * Version améliorée de getCardsBySearch avec gestion automatique des erreurs 429
     * 
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RateLimitExceededException
     */
    public function getCardsBySearchWithRetry(
        SearchCardRequest $searchCardRequest, 
        ?string $locale = 'fr-fr', 
        ?string $token = null,
        bool $autoRetry = true,
        int $maxRetries = self::MAX_RETRY_ATTEMPTS
    ): array {
        $attempt = 0;
        
        while ($attempt <= $maxRetries) {
            try {
                return $this->getCardsBySearch($searchCardRequest, $locale, $token);
                
            } catch (RateLimitExceededException $e) {
                $attempt++;
                
                echo "⚠️  Erreur 429 détectée (tentative $attempt/" . ($maxRetries + 1) . ")\n";
                echo "📝 Message du serveur : " . $e->getServerMessage() . "\n";
                
                if (!$autoRetry || $attempt > $maxRetries) {
                    echo "❌ Nombre maximum de tentatives atteint ou retry automatique désactivé\n";
                    throw $e;
                }
                
                $retryAfter = $e->getRetryAfter() ?: self::DEFAULT_RETRY_DELAY;
                echo "⏳ Attente de $retryAfter secondes avant nouvelle tentative...\n";
                echo "🕒 Reprise prévue à : " . date('H:i:s', time() + $retryAfter) . "\n";
                
                sleep($retryAfter);
            }
        }
        
        throw new RateLimitExceededException("Échec après $maxRetries tentatives");
    }

    /**
     * Méthode utilitaire pour afficher les détails d'une erreur 429
     */
    public static function displayRateLimitError(RateLimitExceededException $e): void
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "🚫 ERREUR DE LIMITATION DE DÉBIT (HTTP 429)\n";
        echo str_repeat("=", 60) . "\n";
        echo "📝 Message du serveur : " . $e->getServerMessage() . "\n";
        echo "⏰ Code d'erreur : " . $e->getCode() . "\n";
        
        if ($e->getRetryAfter() > 0) {
            echo "⏳ Délai avant retry : " . $e->getRetryAfter() . " secondes\n";
            echo "🕒 Heure de reprise : " . date('H:i:s', time() + $e->getRetryAfter()) . "\n";
        }
        
        echo "\n💡 SUGGESTIONS :\n";
        echo "   • Réduisez la fréquence de vos requêtes\n";
        echo "   • Utilisez un rate limiter\n";
        echo "   • Implémentez un système de retry avec backoff\n";
        echo "   • Vérifiez les limites de l'API dans la documentation\n";
        echo str_repeat("=", 60) . "\n\n";
    }
}
