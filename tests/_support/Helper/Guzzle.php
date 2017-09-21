<?php

namespace Helper;

use Codeception\Module\WebDriver;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class Guzzle extends \Codeception\Module
{
    /**
     * @return WebDriver
     */
    public function getWebDriver(): WebDriver
    {
        return $this->getModule('WebDriver');
    }

    public function download(string $url): string
    {
        $baseUrl = $this->getWebDriver()->_getUrl();
        //$session = $this->getWebDriver()->grabCookie('session');

        $domain = null;
        if (preg_match('#^(?:[a-z]+)://([^/]+)/#', $baseUrl, $matches)) {
            $domain = $matches[1];
        }

        $this->assertNotEmpty($domain, 'Could not find domain from WebDriver');

        $client = new Client([
            'base_uri' => $baseUrl,
            'timeout' => 10,
        ]);

        $headers = [
            'User-Agent' => 'OpenSALT Testing/1.0',
            'Accept' => 'application/json',
        ];

        /*
        $cookies = new CookieJar([
            'session' => $session,
        ], $domain);
        */

        $savedFile = tempnam('/tmp', 'download');

        $response = $client->get($url, [
            'headers' => $headers,
            //'cookies' => $cookies,
            'sink' => $savedFile,
        ]);

        return $savedFile;
    }
}
