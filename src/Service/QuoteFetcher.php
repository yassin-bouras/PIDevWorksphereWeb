<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class QuoteFetcher
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function fetchRandomQuote(): string
    {
        $response = $this->httpClient->request(
            'GET',
            'https://dummyjson.com/quotes/random'
        );

        $data = $response->toArray();
        
        return sprintf('"%s" - %s', $data['quote'], $data['author']);
    }
}