<?php

namespace Toxicity\AlteredApi\Service;

use Symfony\Component\HttpClient\HttpClient;

class ProxyService
{
    private const TEST_URL = 'https://api.altered.gg/cards?locale=en-en&itemsPerPage=1';
    private const TIMEOUT = 10;

    private const SOURCES = [
        'proxyscrape'  => 'https://api.proxyscrape.com/v2/?request=displayproxies&protocol=https&timeout=5000&country=all&ssl=all&anonymity=all',
        'geonode'      => 'https://proxylist.geonode.com/api/proxy-list?limit=100&page=1&sort_by=lastChecked&sort_type=desc&protocols=https',
        'github_speed' => 'https://raw.githubusercontent.com/TheSpeedX/PROXY-List/master/http.txt',
        'github_mono'  => 'https://raw.githubusercontent.com/monosans/proxy-list/main/proxies/http.txt',
        'free_proxy'   => 'https://free-proxy-list.net/',
    ];

    /** @var string[] */
    private array $proxies = [];
    private int $currentIndex = 0;

    public function findWorkingProxy(): ?string
    {
        $this->proxies = $this->fetchAllProxies();
        $this->currentIndex = 0;

        echo sprintf("Found %d proxies from all sources.\n", count($this->proxies));

        return $this->getNextWorkingProxy();
    }

    public function getNextWorkingProxy(): ?string
    {
        while ($this->currentIndex < count($this->proxies)) {
            $proxy = $this->proxies[$this->currentIndex];
            $this->currentIndex++;

            echo "Testing proxy: $proxy\n";
            if ($this->testProxy($proxy)) {
                echo "Working proxy found: $proxy\n";
                return $proxy;
            }
        }

        echo "No more proxies available.\n";
        return null;
    }

    /**
     * @return string[]
     */
    private function fetchAllProxies(): array
    {
        $all = [];

        foreach (self::SOURCES as $name => $url) {
            try {
                echo "Fetching proxies from $name...\n";
                $proxies = $this->fetchFromSource($name, $url);
                echo sprintf("  -> %d proxies found.\n", count($proxies));
                $all = array_merge($all, $proxies);
            } catch (\Throwable $e) {
                echo "  -> Failed: {$e->getMessage()}\n";
            }
        }

        return array_unique($all);
    }

    /**
     * @return string[]
     */
    private function fetchFromSource(string $name, string $url): array
    {
        $client = HttpClient::create(['timeout' => self::TIMEOUT]);
        $response = $client->request('GET', $url, [
            'headers' => ['User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) Chrome/120.0 Safari/537.36'],
        ]);

        $content = $response->getContent();

        return match ($name) {
            'geonode'     => $this->parseGeonode($content),
            'free_proxy'  => $this->parseFreeProxyList($content),
            default       => $this->parsePlainText($content),
        };
    }

    /** @return string[] */
    private function parsePlainText(string $content): array
    {
        $proxies = [];
        foreach (explode("\n", trim($content)) as $line) {
            $line = trim($line);
            if (preg_match('/^\d{1,3}(\.\d{1,3}){3}:\d+$/', $line)) {
                $proxies[] = 'http://' . $line;
            }
        }
        return $proxies;
    }

    /** @return string[] */
    private function parseGeonode(string $content): array
    {
        $proxies = [];
        $data = json_decode($content, true);
        foreach ($data['data'] ?? [] as $item) {
            $ip   = $item['ip'] ?? null;
            $port = $item['port'] ?? null;
            if ($ip && $port) {
                $proxies[] = 'https://' . $ip . ':' . $port;
            }
        }
        return $proxies;
    }

    /** @return string[] */
    private function parseFreeProxyList(string $content): array
    {
        $proxies = [];
        $dom = new \DOMDocument();
        @$dom->loadHTML($content);
        $xpath = new \DOMXPath($dom);
        $rows  = $xpath->query('//table//tbody/tr');

        foreach ($rows as $row) {
            $cols = $xpath->query('td', $row);
            if ($cols->length < 8) {
                continue;
            }
            $ip      = trim($cols->item(0)->textContent);
            $port    = trim($cols->item(1)->textContent);
            $isHttps = strtolower(trim($cols->item(6)->textContent));

            if (!filter_var($ip, FILTER_VALIDATE_IP) || !is_numeric($port)) {
                continue;
            }

            $scheme    = $isHttps === 'yes' ? 'https' : 'http';
            $proxies[] = $scheme . '://' . $ip . ':' . $port;
        }

        return $proxies;
    }

    private function testProxy(string $proxy): bool
    {
        $client = HttpClient::create([
            'proxy'       => $proxy,
            'timeout'     => self::TIMEOUT,
            'verify_peer' => false,
        ]);

        try {
            $response = $client->request('GET', self::TEST_URL);
            $status   = $response->getStatusCode();
            if ($status === 403 || $status === 407) {
                return false;
            }
            $response->getContent(false);
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
