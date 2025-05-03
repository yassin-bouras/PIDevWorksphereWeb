<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpKernel\KernelInterface;

class GeminiService
{
    private const API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';
    private const API_KEY = 'AIzaSyDlJH2RyzPz9CZdF2n9zcggC0JKd0nOwGc';

    private $pdfGenerator;
    private $kernel;

    public function __construct(Pdf $pdfGenerator, KernelInterface $kernel)
    {
        $this->pdfGenerator = $pdfGenerator;
        $this->kernel = $kernel;
    }

    public function generateInterviewQuestions(string $poste): ?string
    {
        $client = HttpClient::create();
        $prompt = "Génère 5 questions chaque fois different  d'entretien pour un poste de $poste";

        try {
            $response = $client->request('POST', self::API_URL.'?key='.self::API_KEY, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                throw new \Exception('API request failed with status code: '.$response->getStatusCode());
            }

            $data = $response->toArray();


            dump($data); 

            
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        } catch (\Exception $e) {
            error_log('Gemini API Error: '.$e->getMessage());
            return null;
        }
    }

    public function generatePdfFromQuestions(string $questions, string $filename): string
    {
        $pdfPath = $this->kernel->getProjectDir().'/public/uploads/pdfs/'.$filename;
        
        $html = $this->generateHtmlFromQuestions($questions);
        
        $this->pdfGenerator->generateFromHtml(
            $html,
            $pdfPath,
            [],
            true
        );

        return '/uploads/pdfs/'.$filename;
    }

    public  function generateHtmlFromQuestions(string $questions): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Questions d'entretien</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                h1 { color: #2c3e50; text-align: center; }
                .questions { margin: 20px 0; }
                .question { margin-bottom: 15px; }
            </style>
        </head>
        <body>
            <h1>Questions d'entretien</h1>
            <div class="questions">
                {$this->formatQuestions($questions)}
            </div>
        </body>
        </html>
        HTML;
    }

    private function formatQuestions(string $questions): string
    {
        $formatted = nl2br(htmlspecialchars($questions));
        return str_replace("\n", '', $formatted); // Supprime les sauts de ligne bruts
    }
}