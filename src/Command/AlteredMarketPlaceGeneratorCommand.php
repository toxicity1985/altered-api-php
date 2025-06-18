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

        return Command::SUCCESS;
    }


    private function processForUnique(array $sets, string $inputLocale, OutputInterface $output, \DateTime $start): void
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

                    $remoteCards = Cards::stats($searchCardRequest, $inputLocale);
                    $counter = count($remoteCards);

                    if ($counter === 0) {
                        continue;
                    } else if ($counter < 1000) {
                        $this->process($remoteCards, $inputLocale, $keySet, $output, $filesystem);
                        continue;
                    }

                    for ($j = 0; $j <= 10; $j++) {
                        $searchCardRequest->recallCost = $j;

                        $remoteCards = Cards::stats($searchCardRequest, $inputToken, $inputLocale);;
                        $counter = count($remoteCards);
                        if ($counter === 0) {
                            continue;
                        } else if ($counter < 1000) {
                            $this->process($remoteCards, $inputLocale, $keySet, $output, $filesystem);
                            continue;
                        }

                        for ($k = 0; $k <= 10; $k++) {
                            $searchCardRequest->mountainPower = $k;

                            $remoteCards = Cards::stats($searchCardRequest, $inputToken, $inputLocale);
                            $counter = count($remoteCards);
                            if ($counter === 0) {
                                continue;
                            } else if ($counter < 1000) {
                                $this->process($remoteCards, $inputLocale, $keySet, $output, $filesystem);
                                continue;
                            }

                            for ($l = 0; $l <= 10; $l++) {
                                $searchCardRequest->forestPower = $l;

                                $remoteCards = Cards::stats($searchCardRequest, $inputToken, $inputLocale);
                                $counter = count($remoteCards);
                                if ($counter === 0) {
                                    continue;
                                } else if ($counter < 1000) {
                                    $this->process($remoteCards, $inputLocale, $keySet, $output, $filesystem);
                                    continue;
                                }

                                for ($m = 0; $m <= 10; $m++) {
                                    $searchCardRequest->oceanPower = $m;

                                    $remoteCards = Cards::stats($searchCardRequest, $inputToken, $inputLocale);
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
                $output->writeln(sprintf('<info>%s offer exist</info>', $data['reference'] . '.json'));
                continue;
            }

            if ($filesystem->exists($databaseDirectory . '/' . $data['reference'] . '.json')) {
                $output->writeln(sprintf('<info>%s exist</info>', $data['reference'] . '.json'));
            } else {
                $dataCard = $this->getByReference($data['reference'], $locale);
                $dataCard['translations'] = [];
                $t = Cards::byReference($data['reference']);
                $dataCard['translations']['fr-fr'] = $t;
                $fp = fopen($databaseDirectory . '/' . $data['reference'] . '.json', 'w');
                $dataCard = self::orderRecursivelyByKey($dataCard);
                fwrite($fp, mb_convert_encoding(json_encode($dataCard, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'utf8'));
                fclose($fp);

                $output->writeln(sprintf('<info>Process %s</info>', $data['reference']));
            }

            $offers = Cards::offers($data['reference'], $token);

            if(count($offers) > 0) {
                $fp = fopen($directory . '/' . $data['reference'] . '.json', 'w');
                $offers = self::orderRecursivelyByKey($offers);
                fwrite($fp, mb_convert_encoding(json_encode($offers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'utf8'));
                fclose($fp);

                $output->writeln(sprintf('<info>Process %s</info>', $data['reference']));
            }
            else {
                $output->writeln(sprintf('<info>No offers for %s</info>', $data['reference']));
            }
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


    public function refreshAccessToken(): ?string
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
        echo "Session Response:\n";
        print_r($sessionResponse);

        if (!empty($http_response_header)) {
            echo "Cookies:\n";
            print_r($http_response_header);
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

