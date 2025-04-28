<?php

namespace App\Controller;

use App\Service\HuggingFaceRecommendationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecommendationController extends AbstractController
{
    #[Route('/recommendation', name: 'app_recommendation', methods: ['GET', 'POST'])]
    public function recommend(Request $request, HuggingFaceRecommendationService $hfService): Response
    {
        $recommendations = null;
        $formationName = null;

        if ($request->isMethod('POST')) {
            $formationName = $request->request->get('formation');

            if ($formationName) {
                try {
                    $result = $hfService->getRecommendations($formationName);
                    $recommendations = $result['text'];
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors de la récupération des recommandations : '.$e->getMessage());
                }
            } else {
                $this->addFlash('warning', 'Veuillez entrer une formation.');
            }
        }

        return $this->render('recommendation/index.html.twig', [
            'recommendations' => $recommendations,
            'formationName'   => $formationName,
        ]);
    }
}
