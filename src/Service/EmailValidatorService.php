<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class EmailValidatorService
{
    private HttpClientInterface $client;
    private string $apiKey = '256fee1631553445b5d72cb901dff074'; 

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function isEmailValid(string $email): bool
    {
        $response = $this->client->request('GET', 'http://apilayer.net/api/check', [
            'query' => [
                'access_key' => $this->apiKey,
                'email' => $email,
                'smtp' => 1,
                'format' => 1,
            ]
        ]);

        $data = $response->toArray();

        return $data['format_valid'] && $data['smtp_check'];
    }
}
