<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class NewsApiService
{
    private string $apiKey = 'pub_8382036cc41d54fd5cc9fdd2fb13c6f74bc2b'; 


    

    public function fetchHrNews(string $country = 'us', int $maxResults = 10): array
    {
        $client = HttpClient::create();
        $url = 'https://newsdata.io/api/1/latest';
        $query = [
            'apikey' => $this->apiKey,
            'country' => $country,
            'q' => '"human resources" OR employee OR workforce OR hiring OR recruitment',
            'language' => 'en',
        ];

        try {
            $response = $client->request('GET', $url, ['query' => $query]);
            $data = $response->toArray();

            if ($data['status'] !== 'success') {
                throw new \RuntimeException('API request failed: ' . ($data['message'] ?? 'Unknown error'));
            }

            $hrNews = array_filter($data['results'], function ($article) {
                $text = strtolower($article['title'] . ' ' . $article['description'] . ' ' . ($article['content'] ?? ''));
                return preg_match('/\b(business|company|corporation|hr manager|human resources|finance|money|trading|asset management|investment|stock market|economy|financial)\b/i', $text);            });

            return array_slice(array_values($hrNews), 0, $maxResults);
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Failed to connect to NewsData.io API: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \RuntimeException('Error processing news: ' . $e->getMessage());
        }
    }
}