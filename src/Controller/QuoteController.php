<?php
namespace App\Controller;

use App\Service\QuoteFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class QuoteController extends AbstractController
{
    #[Route('/quote', name: 'app_quote')]
    public function index(QuoteFetcher $quoteFetcher): JsonResponse
    {
        $quote = $quoteFetcher->fetchRandomQuote();
        return $this->json(['quote' => $quote]);
    }
}