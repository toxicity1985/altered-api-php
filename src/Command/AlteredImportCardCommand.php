<?php

namespace Toxicity\AlteredApi\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Toxicity\AlteredApi\Exception\InvalidSearchCardRequestException;
use Toxicity\AlteredApi\Lib\Cards;
use Toxicity\AlteredApi\Lib\Factions;
use Toxicity\AlteredApi\Lib\Sets;
use Toxicity\AlteredApi\Request\SearchCardRequest;
use Toxicity\AlteredApi\Service\CardService;
use Toxicity\AlteredApi\Service\FactionService;
use Toxicity\AlteredApi\Service\SetService;

#[AsCommand(name: 'altered:import:card', description: 'Get Altered Card')]
class AlteredImportCardCommand extends Command
{
    private LimiterInterface $limiter;

    public function __construct(
        private readonly FactionService $factionService,
        private readonly SetService     $setService,
        private readonly CardService    $cardService,
    )
    {
        parent::__construct();

        $rateLimiterFactory = new RateLimiterFactory([
            'id' => 'login',
            'policy' => 'fixed_window',
            'limit' => 10,
            'interval' => '10 seconds',
        ], new InMemoryStorage());

        $this->limiter = $rateLimiterFactory->create();
    }

    /**
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     * @throws InvalidSearchCardRequestException
     * @throws RedirectionExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $factions = [];
        $sets = [];
        foreach (Factions::all() as $data) {
            $faction = $this->factionService->buildFromData($data);
            $this->factionService->save($faction);
            $factions[] = $faction;
        }

        foreach (Sets::all() as $data) {
            $set = $this->setService->buildFromData($data);
            $this->setService->save($set);
            if (in_array($set->getReference(), ['CYCLONE'])) {
                $sets[] = $set;
            }
        }

        foreach ($sets as $set) {
            foreach ($factions as $faction) {
                $searchCardRequest = new SearchCardRequest();
                $searchCardRequest->cardSets = [$set->getReference()];
                $searchCardRequest->factions = [$faction->getReference()];

                foreach (Cards::search($searchCardRequest) as $data) {
                    $dataCard = $this->getByReference($data['reference']);
                    $card = $this->cardService->buildFromData($dataCard);
                    $this->cardService->save($card);
                }
            }
        }


        return Command::SUCCESS;
    }

    private function getByReference(string $reference, string $locale = 'fr-fr'): array
    {
        $this->limiter->reserve(1)->wait();

        return Cards::byReference($reference, $locale);
    }
}
