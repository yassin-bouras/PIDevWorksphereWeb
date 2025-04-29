<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecommendationController extends AbstractController
{
    #[Route('/recommendation', name: 'app_recommendation', methods: ['GET', 'POST'])]
    public function recommend(Request $request): Response
    {
        $recommendations = [];
        $formationName = null;

        if ($request->isMethod('POST')) {
            $formationName = $request->request->get('formation');

            if ($formationName) {
                $recommendations = $this->getRecommendations($formationName);
            } else {
                $this->addFlash('warning', 'Veuillez entrer une formation.');
            }
        }

        return $this->render('recommendation/index.html.twig', [
            'recommendations' => $recommendations,
            'formationName'   => $formationName,
        ]);
    }

    private function getRecommendations(string $formation): array
    {
        return [
            'Développement web' => [
                'HTML & CSS avancé',
                'JavaScript moderne',
                'Symfony pour les débutants'
            ],
            'Machine Learning' => [
                'Introduction à Scikit-learn',
                'Apprentissage supervisé avec Python',
                'Réseaux de neurones avec TensorFlow'
            ],
            'Développement mobile' => [
                'Flutter pour les débutants',
                'React Native avancé',
                'Kotlin pour Android'
            ],
        ][$formation] ?? [];
    }
}
