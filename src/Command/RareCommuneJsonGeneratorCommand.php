<?php

namespace Toxicity\AlteredApi\Command;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Toxicity\AlteredApi\Lib\AlteredApiResource;
use Toxicity\AlteredApi\Lib\Cards;
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
use Toxicity\AlteredApi\Model\CardSetConstant;
use Toxicity\AlteredApi\Service\ProxyService;

#[AsCommand(
    name: 'app:rare-commune:json',
    description: 'Generate JSON files for COMMON, RARE and EXALTED cards fetched directly from Altered API.',
)]
class RareCommuneJsonGeneratorCommand extends Command
{
    private LimiterInterface $limiter;

    public function __construct()
    {
        parent::__construct();

        $rateLimiterFactory = new RateLimiterFactory([
            'id' => 'login',
            'policy' => 'fixed_window',
            'limit' => 8,
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

        $proxyService = new ProxyService();
        $proxy = $proxyService->findWorkingProxy();
        if ($proxy) {
            AlteredApiResource::setProxy($proxy, $proxyService);
        } else {
            $output->writeln('<comment>No working proxy found, proceeding without proxy.</comment>');
        }

        $sets = CardSetConstant::ALL;
        if ($inputSet && in_array($inputSet, $sets)) {
            $sets = [$inputSet];
        }

        $factions = CardFactionConstant::ALL;
        if ($inputFaction && in_array($inputFaction, $factions)) {
            $factions = [$inputFaction];
        }

        $filesystem = new Filesystem();

        foreach ($sets as $set) {
            foreach ($factions as $faction) {
                $output->writeln(sprintf('<info>Fetching COMMON/RARE/EXALTED cards for set=%s faction=%s</info>', $set, $faction));

                $cards = $this->fetchCards($set, $faction, $inputLocale);
                $output->writeln(sprintf('<info>Found %d cards</info>', count($cards)));

                $this->process($cards, $inputLocale, $set, $output, $filesystem, $inputForceRefresh);
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function fetchCards(string $set, string $faction, string $locale): array
    {
        $searchCardRequest = new SearchCardRequest();
        $searchCardRequest->cardSets = [$set];
        $searchCardRequest->factions = [$faction];
        $searchCardRequest->rarities = [CardRarityConstant::COMMON, CardRarityConstant::RARE, CardRarityConstant::EXALTED];

        return Cards::search($searchCardRequest, $locale);
    }

    private function process(array $cards, string $locale, string $set, OutputInterface $output, Filesystem $filesystem, bool $forceRefresh): void
    {
        foreach ($cards as $data) {
            $explode = explode('_', $data['reference']);
            $directory = 'community_database/' . $set . '/' . $explode[3] . '/' . $explode[4];
            $filesystem->mkdir($directory);

            $filePath = $directory . '/' . $data['reference'] . '.json';

            if ($filesystem->exists($filePath) && !$forceRefresh) {
                $existing = json_decode(file_get_contents($filePath), true);
                if (count($existing['translations'] ?? []) >= 4) {
                    $output->writeln(sprintf('<info>%s exist (4 translations)</info>', $data['reference'] . '.json'));
                    continue;
                }
                $output->writeln(sprintf('<comment>%s exist but missing translations, updating...</comment>', $data['reference'] . '.json'));
            }

            $dataCard = $this->getByReference($data['reference'], $locale);
            $dataCard['translations'] = [];
            $dataCard['translations']['fr-fr'] = Cards::byReference($data['reference']);
            $dataCard['translations']['it-it'] = Cards::byReference($data['reference'], 'it-it');
            $dataCard['translations']['de-de'] = Cards::byReference($data['reference'], 'de-de');
            $dataCard['translations']['es-es'] = Cards::byReference($data['reference'], 'es-es');

            if ($filesystem->exists($filePath) && !$forceRefresh) {
                $dataCard['updated'] = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
            }

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

    private static function orderRecursivelyByKey(array $data): array
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
