<?php

namespace App\Traits;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

trait ScraperTrait
{
    public function scrapePage($url)
    {
        $client = new Client();
        $response = $client->request('GET', $url);
        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);

        return $crawler;
    }
}