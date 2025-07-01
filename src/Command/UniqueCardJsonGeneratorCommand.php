<?php

namespace Toxicity\AlteredApi\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Toxicity\AlteredApi\Entity\Card;
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
        $this->addArgument('set', InputArgument::OPTIONAL, 'set');
        $this->addArgument('faction', InputArgument::OPTIONAL, 'faction');
        $this->addArgument('locale', InputArgument::OPTIONAL, 'Locale', 'en-en');
        $this->addArgument('force-refresh', InputArgument::OPTIONAL, 'Refresh', false);
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
        $inputForceRefresh = (bool)$input->getArgument('force-refresh');

        $sets = [
            'ALIZE' => $this->setRepository->findOneByReference('ALIZE'),
            'CORE' => $this->setRepository->findOneByReference('CORE'),
            'COREKS' => $this->setRepository->findOneByReference('COREKS'),
            'BISE' => $this->setRepository->findOneByReference('BISE'),
        ];

        if(array_key_exists($inputSet, $sets)) {
            $sets = [$inputSet => $sets[$inputSet]];
        }
        if(!$inputFaction) {
            $inputFaction = CardFactionConstant::ALL[0];
        }

        var_dump(count($sets));

        $directory = 'community_database/' . $inputSet . '/' . $inputFaction;
        $filesystem = new Filesystem();
        $filesystem->mkdir($directory);

        $start = false;
        foreach ($sets as $inputSet => $set) {
            foreach (CardFactionConstant::ALL as $key => $value) {

                if ($inputFaction === $value || $start) {
                    $dbFaction = $this->factionRepository->findOneByReference($value);
                    $cards = $this->cardRepository->findBy(['faction' => $dbFaction, 'set' => $set, 'rarityString' => CardRarityConstant::RARE], ['name' => 'ASC']);

                    $start = true;
                }

                if ($start === false) {

                    continue;
                }

                foreach ($cards as $card) {
                    $translatedCard = Cards::byReference($card->getReference(), $inputLocale);

                    $searchCardRequest = new SearchCardRequest();
                    $searchCardRequest->cardSets = [$card->getSet()->getReference()];
                    $searchCardRequest->factions = [$card->getFaction()->getReference()];
                    $searchCardRequest->rarities = ['UNIQUE'];
                    $searchCardRequest->name = $translatedCard['name'];

                    for ($i = 0; $i <= 10; $i++) {
                        $searchCardRequest->mainCost = $i;

                        $remoteCards = Cards::search($searchCardRequest, $inputLocale);
                        $counter = count($remoteCards);

                        if ($counter === 0) {
                            continue;
                        } else if ($counter < 1000) {
                            $this->process($remoteCards, $card, $translatedCard, $inputLocale, $inputSet, $output, $filesystem, $inputForceRefresh);
                            continue;
                        }

                        for ($j = 0; $j <= 10; $j++) {
                            $searchCardRequest->recallCost = $j;

                            $remoteCards = Cards::search($searchCardRequest, $inputLocale);
                            $counter = count($remoteCards);
                            if ($counter === 0) {
                                continue;
                            } else if ($counter < 1000) {
                                $this->process($remoteCards, $card, $translatedCard, $inputLocale, $inputSet, $output, $filesystem, $inputForceRefresh);
                                continue;
                            }

                            for ($k = 0; $k <= 10; $k++) {
                                $searchCardRequest->mountainPower = $k;

                                $remoteCards = Cards::search($searchCardRequest, $inputLocale);
                                $counter = count($remoteCards);
                                if ($counter === 0) {
                                    continue;
                                } else if ($counter < 1000) {
                                    $this->process($remoteCards, $card, $translatedCard, $inputLocale, $inputSet, $output, $filesystem, $inputForceRefresh);
                                    continue;
                                }

                                for ($l = 0; $l <= 10; $l++) {
                                    $searchCardRequest->forestPower = $l;

                                    $remoteCards = Cards::search($searchCardRequest, $inputLocale);
                                    $counter = count($remoteCards);
                                    if ($counter === 0) {
                                        continue;
                                    } else if ($counter < 1000) {
                                        $this->process($remoteCards, $card, $translatedCard, $inputLocale, $inputSet, $output, $filesystem, $inputForceRefresh);
                                        continue;
                                    }

                                    for ($m = 0; $m <= 10; $m++) {
                                        $searchCardRequest->oceanPower = $m;

                                        $remoteCards = Cards::search($searchCardRequest, $inputLocale);
                                        $counter = count($remoteCards);
                                        if ($counter === 0) {
                                            continue;
                                        } else if ($counter < 1000) {
                                            $this->process($remoteCards, $card, $translatedCard, $inputLocale, $inputSet, $output, $filesystem, $inputForceRefresh);
                                            continue;
                                        }


                                        $output->writeln('more than 1000 results');


                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return Command::SUCCESS;
    }

    private function process(array $cards, Card $card, array $translatedCard, string $locale, string $set, OutputInterface $output, Filesystem $filesystem, bool $forceRefresh): void
    {
        $output->writeln(sprintf('<info>%s</info>', $translatedCard['name'] . ' - ' . $card->getRarityString() . ' - ' . $card->getFaction()->getName()));
        foreach ($cards as $data) {
            $explode = explode('_', $data['reference']);
            $directory = 'community_database/' . $set . '/' . $explode[3] . '/' . $explode[4];
            $filesystem->mkdir($directory);

            if ($filesystem->exists($directory . '/' . $data['reference'] . '.json') && !$forceRefresh) {
                $output->writeln(sprintf('<info>%s exist</info>', $data['reference'] . '.json'));
                continue;
            }

            $dataCard = $this->getByReference($data['reference'], $locale);
            $dataCard['translations'] = [];
            $t = Cards::byReference($data['reference']);
            $dataCard['translations']['fr-fr'] = $t;
            $fp = fopen($directory . '/' . $data['reference'] . '.json', 'w');
            $dataCard = self::orderRecursivelyByKey($dataCard);
            fwrite($fp, mb_convert_encoding(json_encode($dataCard, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'utf8'));
            fclose($fp);

            $output->writeln(sprintf('<info>Process %s</info>', $data['reference']));
        }
    }

    private function getByReference(string $reference, string $locale = 'fr-fr'): array
    {
        $this->limiter->reserve(1)->wait();

        return Cards::byReference($reference, $locale);
    }

    private function orderRecursivelyByKey(array $data): array
    {
        ksort($data);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::orderRecursivelyByKey($value);
            }
        }

        return $data;
    }

}

