<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google')]
    public function connect(ClientRegistry $clientRegistry): RedirectResponse
    {
        error_log('DEBUG: Initiating Google OAuth redirect');
        return $clientRegistry
            ->getClient('google')
            ->redirect([
                'profile',
                'email',
            ], []);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheck(Request $request): JsonResponse
    {
        // The response is handled by GoogleAuthenticator
        error_log('DEBUG: Google check route reached');
        return new JsonResponse(['message' => 'Authentication handled by security system'], 200);
    }
}
