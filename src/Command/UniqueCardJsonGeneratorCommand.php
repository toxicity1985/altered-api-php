<?php

namespace Toxicity\AlteredApi\Command;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Toxicity\AlteredApi\Lib\Cards;
use Toxicity\AlteredApi\Repository\CardRepository;
use Toxicity\AlteredApi\Repository\FactionRepository;
use Toxicity\AlteredApi\Repository\SetRepository;
use Toxicity\AlteredApi\Request\SearchCardRequest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Toxicity\AlteredApi\Model\CardFactionConstant;
use Toxicity\AlteredApi\Model\CardRarityConstant;

#[AsCommand(
    name: 'app:unique:json',
    description: 'Synchronize unique card.',
)]
class UniqueCardJsonGeneratorCommand extends Command
{
    private LimiterInterface $limiter;

    public function __construct(
        private readonly FactionRepository $factionRepository,
        private readonly SetRepository     $setRepository,
        private readonly CardRepository    $cardRepository
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

    public function configure(): void
    {
        $this->addArgument('set', InputArgument::REQUIRED, 'set');
        $this->addArgument('faction', InputArgument::REQUIRED, 'faction');
        $this->addArgument('locale', InputArgument::OPTIONAL, 'Locale', 'en-en');

    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputLocale = $input->getArgument('locale');
        $inputSet = $input->getArgument('set');
        $inputFaction = $input->getArgument('faction');

        $sets = [
            'ALIZE' => $this->setRepository->findOneByReference('ALIZE'),
            'CORE' => $this->setRepository->findOneByReference('CORE'),
            'COREKS' => $this->setRepository->findOneByReference('COREKS'),
        ];

        $directory = 'community_database/' . $inputSet . '/B/' . $inputFaction;
        $filesystem = new Filesystem();
        $filesystem->mkdir($directory);

        $start = false;
        foreach (CardFactionConstant::ALL as $key => $value) {
            if ($inputFaction === $value || $start) {
                $dbFaction = $this->factionRepository->findOneByReference($value);
                $cards = $this->cardRepository->findBy(['faction' => $dbFaction, 'set' => $sets[$inputSet], 'rarityString' => CardRarityConstant::RARE], ['name' => 'ASC']);

                $start = true;


            }

            if ($start === false) {
                continue;
            }


            foreach ($cards as $card) {
                $explode = explode('_', $card->getReference());

                $translatedCard = Cards::byReference($card->getReference(), $inputLocale);

                $searchCardRequest = new SearchCardRequest();
                $searchCardRequest->cardSets = [$card->getSet()->getReference()];
                $searchCardRequest->factions = [$card->getFaction()->getReference()];
                $searchCardRequest->rarities = ['UNIQUE'];
                $searchCardRequest->name = $translatedCard['name'];

                $output->writeln(sprintf('<info>%s</info>', $translatedCard['name'] . ' - ' . $card->getRarityString() . ' - ' . $card->getFaction()->getName()));
                foreach (Cards::search($searchCardRequest, $inputLocale) as $data) {
                    $directory = 'community_database/' . $inputSet . '/B/' . $data['mainFaction']['reference'] . '/' . $explode[4] . '/UNIQUE';
                    $filesystem->mkdir($directory);

                    if ($filesystem->exists($directory . '/' . $data['reference'] . '.json')) {
                        $output->writeln(sprintf('<info>%s exist</info>', $data['reference'] . '.json'));
                        continue;
                    }

                    $dataCard = $this->getByReference($data['reference'], $inputLocale);
                    $dataCard['translations'] = [];
                    $t = Cards::byReference($data['reference']);
                    ksort($t);
                    $dataCard['translations']['fr-fr'] = $t;
                    $fp = fopen($directory . '/' . $data['reference'] . '.json', 'w');
                    ksort($dataCard);
                    fwrite($fp, json_encode($dataCard, JSON_PRETTY_PRINT));
                    fclose($fp);

                    $output->writeln(sprintf('<info>Process %s</info>', $data['reference']));
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
