<?php

namespace App\Service;

use Smalot\PdfParser\Parser;

class CvAnalyzerService
{
    private $pdfParser;
    private $apiEndpoint;
    private $apiToken;

    public function __construct(string $apiEndpoint = null, string $apiToken = null)
    {
        $this->pdfParser = new Parser();
        $this->apiEndpoint = $apiEndpoint ?? 'https://models.inference.ai.azure.com/chat/completions';
        $this->apiToken = $apiToken ?? $_ENV['AZURE_API_TOKEN'] ?? '';
    }

    /**
     * Extract text content from a PDF file
     */
    public function extractTextFromPdf(string $pdfPath): string
    {
        try {
            $pdf = $this->pdfParser->parseFile($pdfPath);
            return $pdf->getText();
        } catch (\Exception $e) {
            return 'Failed to parse PDF: ' . $e->getMessage();
        }
    }

    /**
     * Compare CV content with job requirements using Azure AI
     */
    public function compareCvWithJobRequirements(string $cvContent, string $jobDescription, string $jobRequirements): array
    {
        $prompt = "I need to compare this CV with job requirements. 
        
CV content: 
$cvContent 

Job description: 
$jobDescription 

Experience requirements: 
$jobRequirements 

Analyze the CV and determine: 
1. What percentage match exists between the CV and the combined job description and requirements? 
2. Which specific skills and qualifications mentioned in both the job description and experience requirements are found in the CV? 
3. Which important skills or qualifications mentioned in the job are missing from the CV? 
4. Give a brief assessment of the candidate's suitability for this position based on both technical skills and experience level. 

Format the response as a JSON with these keys: matchPercentage (number), matchedSkills (array of strings), missingSkills (array of strings), and assessment (string).";

        try {
            $payload = json_encode([
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an HR assistant specialized in analyzing CVs and matching them with job requirements. Extract skills and qualifications from both job descriptions and required experience fields.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'model' => 'gpt-4o',
                'temperature' => 0.7,
                'max_tokens' => 4000, // Increased from 2000 to ensure complete responses
                'top_p' => 1
            ]);

            $ch = curl_init($this->apiEndpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $this->apiToken,
                    'Content-Length: ' . strlen($payload)
                ],
                CURLOPT_TIMEOUT => 60, // Increased from 30 to 60 seconds
                CURLOPT_SSL_VERIFYPEER => true
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError || $httpCode !== 200) {
                throw new \Exception($curlError ?: 'API returned status code ' . $httpCode);
            }

            $data = json_decode($response, true);

            $aiResponse = $data['choices'][0]['message']['content'] ?? '';

            // Check if the aiResponse is valid JSON
            $result = $this->parseJsonResponse($aiResponse);
            // dd($result);
            if (!$result) {
                return [
                    'matchPercentage' => $this->calculateSimpleMatchPercentage($cvContent, $jobDescription . ' ' . $jobRequirements),
                    'matchedSkills' => $this->extractMatchedSkills($cvContent, $jobDescription . ' ' . $jobRequirements),
                    'missingSkills' => $this->extractMissingSkills($cvContent, $jobDescription . ' ' . $jobRequirements),
                    'assessment' => 'Unable to generate detailed assessment.',
                    'error' => 'Invalid or incomplete JSON response from AI service'
                ];
            }

            // Ensure all expected keys exist in the result
            $result = array_merge([
                'matchPercentage' => 0,
                'matchedSkills' => [],
                'missingSkills' => [],
                'assessment' => ''
            ], $result);

            return $result;
        } catch (\Exception $e) {
            return [
                'matchPercentage' => $this->calculateSimpleMatchPercentage($cvContent, $jobDescription . ' ' . $jobRequirements),
                'matchedSkills' => $this->extractMatchedSkills($cvContent, $jobDescription . ' ' . $jobRequirements),
                'missingSkills' => $this->extractMissingSkills($cvContent, $jobDescription . ' ' . $jobRequirements),
                'assessment' => 'Analysis performed using backup method due to API limitations.',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Parse potentially incomplete JSON response
     * 
     * @param string $jsonString The JSON string to parse
     * @return array|false Parsed JSON array or false on failure
     */
    private function parseJsonResponse(string $jsonString): array|false
    {
        // First try standard JSON decode
        $result = json_decode($jsonString, true);
        if ($result && json_last_error() === JSON_ERROR_NONE) {
            return $result;
        }

        // Try to repair incomplete JSON by adding missing closing braces
        $cleanString = trim($jsonString);

        // Handle potentially truncated JSON with missing closing braces
        $openBraces = substr_count($cleanString, '{');
        $closeBraces = substr_count($cleanString, '}');
        $openBrackets = substr_count($cleanString, '[');
        $closeBrackets = substr_count($cleanString, ']');

        // Add missing closing braces/brackets
        $missingCloseBraces = $openBraces - $closeBraces;
        $missingCloseBrackets = $openBrackets - $closeBrackets;

        if ($missingCloseBraces > 0 || $missingCloseBrackets > 0) {
            for ($i = 0; $i < $missingCloseBrackets; $i++) {
                $cleanString .= ']';
            }
            for ($i = 0; $i < $missingCloseBraces; $i++) {
                $cleanString .= '}';
            }

            $result = json_decode($cleanString, true);
            if ($result && json_last_error() === JSON_ERROR_NONE) {
                return $result;
            }
        }

        // Try to extract JSON using regex in case it's embedded in text
        if (preg_match('/\{.*\}/s', $cleanString, $matches)) {
            $potentialJson = $matches[0];
            $result = json_decode($potentialJson, true);
            if ($result && json_last_error() === JSON_ERROR_NONE) {
                return $result;
            }
        }

        return false;
    }

    /**
     * Simple fallback method to calculate match percentage
     */
    private function calculateSimpleMatchPercentage(string $cvContent, string $jobRequirements): int
    {
        $cvContent = strtolower($cvContent);
        $reqWords = array_filter(
            explode(' ', strtolower($jobRequirements)),
            function ($word) {
                return strlen($word) > 3;
            }
        );

        $matchCount = 0;
        foreach ($reqWords as $word) {
            if (strpos($cvContent, $word) !== false) {
                $matchCount++;
            }
        }

        return count($reqWords) > 0 ? round(($matchCount / count($reqWords)) * 100) : 0;
    }

    /**
     * Extract skills that match between CV and job requirements
     */
    private function extractMatchedSkills(string $cvContent, string $jobRequirements): array
    {
        $cvContent = strtolower($cvContent);

        $skillKeywords = array_map('trim', explode(',', $jobRequirements));

        $matchedSkills = [];
        foreach ($skillKeywords as $skill) {
            if (strpos($cvContent, strtolower($skill)) !== false) {
                $matchedSkills[] = $skill;
            }
        }

        return $matchedSkills;
    }

    /**
     * Extract skills missing from CV but mentioned in job requirements
     */
    private function extractMissingSkills(string $cvContent, string $jobRequirements): array
    {
        $cvContent = strtolower($cvContent);

        $skillKeywords = array_map('trim', explode(',', $jobRequirements));

        $missingSkills = [];
        foreach ($skillKeywords as $skill) {
            if (strpos($cvContent, strtolower($skill)) === false) {
                $missingSkills[] = $skill;
            }
        }

        return $missingSkills;
    }

    /**
     * Translate CV content from one language to another
     * 
     * @param string $cvContent The CV content to translate
     * @param string $targetLanguage The target language (e.g., 'English', 'French', 'Spanish')
     * @return array Translated CV content with sections and success status
     */
    public function translateCv(string $cvContent, string $targetLanguage): array
    {
        $prompt = "I need to translate this CV/resume to $targetLanguage while preserving its structure and professional terminology.

CV content:
$cvContent

Please translate the entire CV to $targetLanguage, maintaining the original organization and formatting as much as possible. 
Make sure to:
1. Preserve section titles, but translate them
2. Maintain all professional skills, experience, and qualifications
3. Keep dates and numbers in their original format
4. Adapt terms and expressions to be culturally appropriate in the target language
5. Ensure that job titles and education credentials are correctly translated with appropriate terms in the target language

Format the response as plain text with clear section breaks.";

        try {
            $payload = json_encode([
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional translator specializing in CVs and resumes. You provide accurate translations that maintain professional terminology and document structure.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'model' => 'gpt-4o',
                'temperature' => 0.3,
                'max_tokens' => 4000,
                'top_p' => 1
            ]);

            $ch = curl_init($this->apiEndpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $this->apiToken,
                    'Content-Length: ' . strlen($payload)
                ],
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => true
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError || $httpCode !== 200) {
                throw new \Exception($curlError ?: 'API returned status code ' . $httpCode);
            }

            $data = json_decode($response, true);

            $translatedContent = $data['choices'][0]['message']['content'] ?? '';

            if (empty($translatedContent)) {
                return [
                    'success' => false,
                    'message' => 'Failed to get translation from AI service',
                    'translatedContent' => ''
                ];
            }

            return [
                'success' => true,
                'message' => 'CV successfully translated to ' . $targetLanguage,
                'translatedContent' => $translatedContent
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Translation error: ' . $e->getMessage(),
                'translatedContent' => ''
            ];
        }
    }
}
