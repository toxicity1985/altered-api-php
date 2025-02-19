<?php

namespace Toxicity\AlteredApi\Service;

use Exception;
use Symfony\Component\Panther\Client;

class AlteredPantherService
{
    private ?Client $client;

    public function __construct(private readonly string $alteredLogin, private readonly string $alteredPassword)
    {
        $this->client = Client::createChromeClient();
        $this->client->manage()->window()->maximize();
    }

    /**
     * @throws Exception
     */
    public function login(): string
    {
        $this->client->request('GET', 'https://www.altered.gg/fr-fr/decks'); // Yes, this website is 100% written in JavaScript

        $this->client->wait('30', '30');

        try {
            $this->client->executeScript("Array.from(document.querySelectorAll('button')).filter(button => button.textContent.includes('Accepter & Fermer'))[0].click()");
        }
        catch (Exception $e) {
        }

        $this->client->executeScript("Array.from(document.querySelectorAll('button')).filter(button => button.textContent.includes('Se connecter'))[0].click()");

        $this->client->wait('30', '30');

        $crawler = $this->client->refreshCrawler();
        $form = $crawler->filter('form')->form();
        $form['username'] = $this->alteredLogin;
        $form['password'] = $this->alteredPassword;

        $crawler = $this->client->submit($form);

        $this->client->request('GET', 'https://www.altered.gg');


        $this->client->takeScreenshot('check.png');

        $crawler = $this->client->refreshCrawler();

        $tokenString = substr($crawler->html(), strpos($crawler->html(), '"accessToken":"') + 15, 2000);
        $tokenString = substr($tokenString, 0, strpos($tokenString, '"'));

        return $tokenString;
    }
}
