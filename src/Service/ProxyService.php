<?php

namespace Toxicity\AlteredApi\Service;

use Symfony\Component\HttpClient\HttpClient;

class ProxyService
{
    private const PROXY_LIST_URL = 'https://free-proxy-list.net/';
    private const TEST_URL = 'https://api.altered.gg/cards?locale=en-en&itemsPerPage=1';
    private const TIMEOUT = 10;

    /** @var string[] */
    private array $proxies = [];
    private int $currentIndex = 0;

    public function findWorkingProxy(): ?string
    {
        $this->proxies = $this->fetchProxies();
        $this->currentIndex = 0;

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
     * @return string[] list of "http://ip:port"
     */
    private function fetchProxies(): array
    {
        $client = HttpClient::create(['timeout' => self::TIMEOUT]);
        $response = $client->request('GET', self::PROXY_LIST_URL, [
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome/120.0 Safari/537.36',
            ],
        ]);

        $html = $response->getContent();

        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        $rows = $xpath->query('//table//tbody/tr');

        $proxies = [];
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

            $scheme = $isHttps === 'yes' ? 'https' : 'http';
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
            $status = $response->getStatusCode();
            if ($status === 403 || $status === 407) {
                return false;
            }
            $response->getContent(false); // force body read to catch CONNECT tunnel failures
            return true;
        } catch (\Throwable) {
            return false;
        }
    }
}
