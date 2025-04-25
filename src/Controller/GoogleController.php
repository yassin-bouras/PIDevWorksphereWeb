<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class GoogleController extends AbstractController
{
    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheck(Request $request): RedirectResponse
    {
        // This is handled by Symfony Security
        return $this->redirectToRoute('app_dashboard');
    }
    #[Route('/connect/google', name: 'connect_google')]
    public function connect(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect([
                'profile',
                'email',
            ], []);
    }
}
