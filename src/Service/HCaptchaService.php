<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class HCaptchaService
{
    private HttpClientInterface $httpClient;
    private string $secret;
    private string $verifyUrl;

    public function __construct(HttpClientInterface $httpClient, string $hcaptchaSecret)
    {
        $this->httpClient = $httpClient;
        $this->secret = $hcaptchaSecret;
        $this->verifyUrl = 'https://hcaptcha.com/siteverify';
    }

    public function verify(string $response): array
    {
        try {
            $result = $this->httpClient->request('POST', $this->verifyUrl, [
                'body' => [
                    'secret' => $this->secret,
                    'response' => $response
                ]
            ]);

            return $result->toArray();
        } catch (\Exception $e) {
            return ['success' => false, 'error-codes' => ['request-error']];
        }
    }
}
