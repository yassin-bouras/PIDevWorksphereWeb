<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;
use RuntimeException;

class GeminiAIService
{
    private HttpClientInterface $client;
    private string $apiKey = 'AIzaSyB5HQtWxwdLaAXwsfE9RpRM0uAVgtKCS4M'; // Clé API en dur ici
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function generateContent(string $prompt, string $model = 'gemini-2.0-flash'): array
    {
        try {
            $response = $this->client->request(
                'POST',
                "{$this->baseUrl}/{$model}:generateContent",
                [
                    'query' => ['key' => $this->apiKey],
                    'json' => $this->buildPayload($prompt),
                    'timeout' => 30
                ]
            );

            return $this->handleResponse($response);

        } catch (\Exception $e) {
            throw new RuntimeException('API request failed: '.$e->getMessage(), 0, $e);
        }
    }

    private function buildPayload(string $prompt): array
    {
        return [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topP' => 1,
                'maxOutputTokens' => 2048
            ]
        ];
    }

    private function handleResponse($response): array
    {
        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);

        if ($statusCode !== Response::HTTP_OK) {
            throw new RuntimeException('API request failed with status '.$statusCode.': '.$content);
        }

        $data = json_decode($content, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new RuntimeException('Invalid JSON response: '.json_last_error_msg());
        }

        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            throw new RuntimeException('Unexpected API response format');
        }

        return [
            'text' => $data['candidates'][0]['content']['parts'][0]['text'],
            'metadata' => [
                'modelVersion' => $data['modelVersion'] ?? 'unknown',
                'usage' => $data['usageMetadata'] ?? null,
                'finishReason' => $data['candidates'][0]['finishReason'] ?? null
            ]
        ];
    }
}