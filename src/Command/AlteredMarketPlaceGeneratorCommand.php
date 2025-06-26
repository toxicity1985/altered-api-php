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
use Toxicity\AlteredApi\Service\RateLimiterService;

#[AsCommand(
    name: 'app:market-place:json',
    description: 'Synchronize market-place.',
)]
class AlteredMarketPlaceGeneratorCommand extends Command
{
    private LimiterInterface $limiter;
    private ?\DateTime $start = null;

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
        $this->addArgument('set', InputArgument::OPTIONAL, 'set', null);
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
        $this->start = new \DateTime();
        $inputLocale = $input->getArgument('locale');
        $inputSet = $input->getArgument('set');

        $sets = [
            'ALIZE' => $this->setRepository->findOneByReference('ALIZE'),
            'CORE' => $this->setRepository->findOneByReference('CORE'),
            'COREKS' => $this->setRepository->findOneByReference('COREKS'),
            'BISE' => $this->setRepository->findOneByReference('BISE'),
        ];

        if(array_key_exists($inputSet, $sets)) {
            $sets = [$inputSet => $sets[$inputSet]];
        }

        $this->processCardStats($sets, $inputLocale, $output);
        //$this->processOffers($sets, $inputLocale, $output);

        return Command::SUCCESS;
    }

    private function processCardStats(array $sets, string $inputLocale, OutputInterface $output): void
    {
        foreach ($sets as $keySet => $set) {
            foreach (CardFactionConstant::ALL as $value) {
                $directory = 'altered_marketplace/' . $keySet . '/' . $value;
                $filesystem = new Filesystem();
                $filesystem->mkdir($directory);

                $searchCardRequest = new SearchCardRequest();
                $searchCardRequest->cardSets = [$keySet];
                $searchCardRequest->factions = [$value];
                $searchCardRequest->rarities = [CardRarityConstant::UNIQUE];
                $searchCardRequest->inSale = true;

                for ($i = 0; $i <= 10; $i++) {
                    $searchCardRequest->mainCost = $i;

                    $remoteStats = Cards::stats($searchCardRequest, $this->refreshAccessToken(), $inputLocale);
                    $counter = count($remoteStats);

                    $output->writeln('<info>Main cost: '.$i.' ('.$counter.')</info>');

                    if ($counter === 0) {
                        continue;
                    } else if ($counter < 1000) {
                        $this->processStats($remoteStats, $inputLocale, $keySet, $output, $filesystem);
                        continue;
                    }

                    for ($j = 0; $j <= 10; $j++) {
                        $searchCardRequest->recallCost = $j;

                        $remoteStats = Cards::stats($searchCardRequest, $this->refreshAccessToken(), $inputLocale);;
                        $counter = count($remoteStats);
                        if ($counter === 0) {
                            $searchCardRequest->recallCost =  null;
                            continue;
                        } else if ($counter < 1000) {
                            $searchCardRequest->recallCost =  null;
                            $this->processStats($remoteStats, $inputLocale, $keySet, $output, $filesystem);
                            continue;
                        }

                        for ($k = 0; $k <= 10; $k++) {
                            $searchCardRequest->mountainPower = $k;

                            $remoteStats = Cards::stats($searchCardRequest, $this->refreshAccessToken(), $inputLocale);
                            $counter = count($remoteStats);
                            if ($counter === 0) {
                                $searchCardRequest->mountainPower =  null;
                                continue;
                            } else if ($counter < 1000) {
                                $searchCardRequest->mountainPower =  null;
                                $this->processStats($remoteStats, $inputLocale, $keySet, $output, $filesystem);
                                continue;
                            }

                            for ($l = 0; $l <= 10; $l++) {
                                $searchCardRequest->forestPower = $l;

                                $remoteStats = Cards::stats($searchCardRequest, $this->refreshAccessToken(), $inputLocale);
                                $counter = count($remoteStats);
                                if ($counter === 0) {
                                    $searchCardRequest->forestPower =  null;
                                    continue;
                                } else if ($counter < 1000) {
                                    $searchCardRequest->forestPower =  null;
                                    $this->processStats($remoteStats, $inputLocale, $keySet, $output, $filesystem);
                                    continue;
                                }

                                for ($m = 0; $m <= 10; $m++) {
                                    $searchCardRequest->oceanPower = $m;

                                    $remoteStats = Cards::stats($searchCardRequest, $this->refreshAccessToken(), $inputLocale);
                                    $counter = count($remoteStats);
                                    if ($counter === 0) {
                                        $searchCardRequest->oceanPower =  null;
                                        continue;
                                    } else if ($counter < 1000) {
                                        $searchCardRequest->oceanPower =  null;
                                        $this->processStats($remoteStats, $inputLocale, $keySet, $output, $filesystem);
                                        continue;
                                    }


                                    $output->writeln('more than 1000 results');
                                    die();


                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function processOffers(array $sets, string $inputLocale, OutputInterface $output): void
    {
        foreach ($sets as $keySet => $set) {
            foreach (CardFactionConstant::ALL as $value) {
                $directory = 'altered_marketplace/' . $keySet . '/' . $value;
                $filesystem = new Filesystem();
                $filesystem->mkdir($directory);

                $searchCardRequest = new SearchCardRequest();
                $searchCardRequest->cardSets = [$keySet];
                $searchCardRequest->factions = [$value];
                $searchCardRequest->rarities = [CardRarityConstant::UNIQUE];
                $searchCardRequest->inSale = true;

                for ($i = 0; $i <= 10; $i++) {
                    $searchCardRequest->mainCost = $i;

                    $remoteCards = Cards::search($searchCardRequest, $inputLocale, $this->refreshAccessToken());
                    $counter = count($remoteCards);

                    if($counter === 100) {
                        die();
                    }

                    if ($counter === 0) {
                        continue;
                    } else if ($counter < 1000) {
                        $this->process($remoteCards, $inputLocale, $keySet, $output, $filesystem);
                        continue;
                    }

                    for ($j = 0; $j <= 10; $j++) {
                        $searchCardRequest->recallCost = $j;

                        $remoteCards = Cards::search($searchCardRequest, $inputLocale, $this->refreshAccessToken());
                        $counter = count($remoteCards);
                        if ($counter === 0) {
                            continue;
                        } else if ($counter < 1000) {
                            $this->process($remoteCards, $inputLocale, $keySet, $output, $filesystem);
                            continue;
                        }

                        for ($k = 0; $k <= 10; $k++) {
                            $searchCardRequest->mountainPower = $k;

                            $remoteCards = Cards::search($searchCardRequest, $inputLocale, $this->refreshAccessToken());
                            $counter = count($remoteCards);
                            if ($counter === 0) {
                                continue;
                            } else if ($counter < 1000) {
                                $this->process($remoteCards, $inputLocale, $keySet, $output, $filesystem);
                                continue;
                            }

                            for ($l = 0; $l <= 10; $l++) {
                                $searchCardRequest->forestPower = $l;

                                $remoteCards = Cards::search($searchCardRequest, $inputLocale, $this->refreshAccessToken());
                                $counter = count($remoteCards);
                                if ($counter === 0) {
                                    continue;
                                } else if ($counter < 1000) {
                                    $this->process($remoteCards, $inputLocale, $keySet, $output, $filesystem);
                                    continue;
                                }

                                for ($m = 0; $m <= 10; $m++) {
                                    $searchCardRequest->oceanPower = $m;

                                    $remoteCards = Cards::search($searchCardRequest, $inputLocale, $this->refreshAccessToken());
                                    $counter = count($remoteCards);
                                    if ($counter === 0) {
                                        continue;
                                    } else if ($counter < 1000) {
                                        $this->process($remoteCards, $inputLocale, $keySet, $output, $filesystem);
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

    private function process(array $cards, string $locale, string $set, OutputInterface $output, Filesystem $filesystem): void
    {
        $token = $this->refreshAccessToken();

        foreach ($cards as $data) {
            $interval = date_diff($this->start, (new \DateTime()));
            (int) $gap = $interval->format('%i');

            if ($gap > 15) {
                $this->start = new \DateTime();
                $token = $this->refreshAccessToken();
            }

            $explode = explode('_', $data['reference']);
            $databaseDirectory = 'community_database/' . $set . '/' . $explode[3] . '/' . $explode[4];
            $directory = 'altered_marketplace/' . $set . '/' . $explode[3] . '/' . $explode[4];
            $filesystem->mkdir($directory);
            $filesystem->mkdir($databaseDirectory);

            if ($filesystem->exists($directory . '/' . $data['reference'] . '.json')) {
                //$output->writeln(sprintf('<info>%s offer exist</info>', $data['reference'] . '.json'));
                continue;
            }

            if ($filesystem->exists($databaseDirectory . '/' . $data['reference'] . '.json')) {
                //$output->writeln(sprintf('<info>%s exist</info>', $data['reference'] . '.json'));
            } else {
                $dataCard = $this->getByReference($data['reference'], $locale);
                $dataCard['translations'] = [];
                $t = Cards::byReference($data['reference']);
                $dataCard['translations']['fr-fr'] = $t;
                $fp = fopen($databaseDirectory . '/' . $data['reference'] . '.json', 'w');
                $dataCard = self::orderRecursivelyByKey($dataCard);
                fwrite($fp, mb_convert_encoding(json_encode($dataCard, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'utf8'));
                fclose($fp);

                //$output->writeln(sprintf('<info>Process %s</info>', $data['reference']));
            }

            $offers = Cards::offers($data['reference'], $token);

            if(count($offers) > 0) {
                $fp = fopen($directory . '/' . $data['reference'] . '.json', 'w');
                $offers = self::orderRecursivelyByKey($offers);
                fwrite($fp, mb_convert_encoding(json_encode($offers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'utf8'));
                fclose($fp);

                //$output->writeln(sprintf('<info>Process %s</info>', $data['reference']));
            }
            else {
                //$output->writeln(sprintf('<info>No offers for %s</info>', $data['reference']));
            }
        }
    }

    private function processStats(array $stats, string $locale, string $set, OutputInterface $output, Filesystem $filesystem): void
    {
        $token = $this->refreshAccessToken();

        $limiter = RateLimiterService::create(2,2);

        $counter = 0;

        foreach ($stats as $data) {

            if($counter === 100) {
                $counter = 0;
                $token = $this->refreshAccessToken();
            }

            $array = explode('/', $data['@id']);
            $reference = array_pop($array);

            $explode = explode('_', $reference);
            $databaseDirectory = 'community_database/' . $set . '/' . $explode[3] . '/' . $explode[4];
            $directory = 'altered_marketplace/' . $set . '/' . $explode[3] . '/' . $explode[4];
            $filesystem->mkdir($directory);
            $filesystem->mkdir($databaseDirectory);

            if ($filesystem->exists($directory . '/' . $reference . '.json')) {
                //$output->writeln(sprintf('<info>%s offer exist, erase and replace value</info>', $reference . '.json'));

                $json = file_get_contents($directory . '/' . $reference . '.json');
                $json = json_decode($json, true);

                if($json[0]['price'] === $json[0]['convertedPrice']) {
                    $json[0]['price'] = $data['lowerPrice'];
                    $json[0]['convertedPrice'] = $data['lowerPrice'];
                    $json[0]['updated'] = (new \DateTime())->format('Y-m-d H:i:s');

                    $fp = fopen($directory . '/' . $reference . '.json', 'w');
                    $dataCard = self::orderRecursivelyByKey($json);
                    fwrite($fp, mb_convert_encoding(json_encode($dataCard, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'utf8'));
                    fclose($fp);
                }
            }
            else {

                if ($filesystem->exists($databaseDirectory . '/' . $reference . '.json')) {
                    //$output->writeln(sprintf('<info>%s exist</info>', $reference . '.json'));
                } else {
                    $dataCard = $this->getByReference($reference, $locale);
                    $dataCard['created'] = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
                    $dataCard['updated'] = null;
                    $dataCard['translations'] = [];
                    $t = Cards::byReference($reference);
                    $dataCard['translations']['fr-fr'] = $t;
                    $fp = fopen($databaseDirectory . '/' . $reference . '.json', 'w');
                    $dataCard = self::orderRecursivelyByKey($dataCard);
                    fwrite($fp, mb_convert_encoding(json_encode($dataCard, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'utf8'));
                    fclose($fp);

                    //$output->writeln(sprintf('<info>Process %s</info>', $reference));
                }

                $limiter->reserve(1)->wait();
                $offers = Cards::offers($reference, $token);

                if(count($offers) > 0) {
                    $offers[0]['updated'] = null;
                    $offers[0]['created'] = (new \DateTime())->format('Y-m-d H:i:s');
                    $fp = fopen($directory . '/' . $reference . '.json', 'w');
                    $offers = self::orderRecursivelyByKey($offers);
                    fwrite($fp, mb_convert_encoding(json_encode($offers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'utf8'));
                    fclose($fp);

                    //$output->writeln(sprintf('<info>Process %s</info>', $reference));
                }
                else {
                    //$output->writeln(sprintf('<info>No offers for %s</info>', $reference));
                }
            }
            $counter++;
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


    public function refreshAccessToken(?OutputInterface $output = null): ?string
    {
        $cookiesFile = 'tmp/cookies.json';
        $accessTokenFile = 'tmp/access_token.txt';

        if (!file_exists($cookiesFile)) {
            throw new Exception("Le fichier cookies.json est introuvable.");
        }

        $cookiesJson = file_get_contents($cookiesFile);
        $storedCookies = json_decode($cookiesJson, true); // tableau associatif

        $headerCookies = [];

        foreach ($storedCookies as $cookie) {
            $expires = isset($cookie['expires']) ? strtotime($cookie['expires']) : null;
            if ($expires === null || $expires > time()) {
                $headerCookies[] = "{$cookie['name']}={$cookie['value']}";
            }
        }

        // Ajouter le cookie statique
        $headerCookies[] = '__Secure-next-auth.callback-url=https%3A%2F%2Fwww.altered.gg';
        $cookieHeader = implode('; ', $headerCookies);

        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "Cookie: $cookieHeader\r\n"
            ]
        ];

        $context = stream_context_create($opts);
        $response = file_get_contents('https://www.altered.gg/api/auth/session', false, $context);

        if ($response === false) {
            throw new Exception("Erreur lors de la récupération de la session.");
        }

        $sessionResponse = json_decode($response, true);
        $output?->writeln("Session Response:");
        $output?->writeln(json_encode($sessionResponse));

        if (!empty($http_response_header)) {
            $output?->writeln("Cookies:");
            $output?->writeln(json_encode($http_response_header));
        }

        if (!empty($sessionResponse['accessToken']) && !empty($sessionResponse['expires'])) {
            $newCookies = [];
            foreach ($http_response_header as $header) {
                if (stripos($header, 'Set-Cookie:') === 0) {
                    $cookieStr = substr($header, strlen('Set-Cookie: '));
                    $parts = explode(';', $cookieStr);
                    list($name, $value) = explode('=', trim($parts[0]), 2);
                    $expires = null;
                    foreach ($parts as $part) {
                        if (stripos($part, 'Expires=') === 0) {
                            $expires = trim(substr($part, 8));
                        }
                    }

                    if (strpos($name, '__Secure-next-auth.session-token') === 0) {
                        $newCookies[] = [
                            'name' => $name,
                            'value' => $value,
                            'expires' => $expires
                        ];
                    }
                }
            }

            file_put_contents($cookiesFile, json_encode($newCookies, JSON_PRETTY_PRINT));
            file_put_contents($accessTokenFile, $sessionResponse['accessToken']);

            return $sessionResponse['accessToken'];
        }

        return null;
    }


}

