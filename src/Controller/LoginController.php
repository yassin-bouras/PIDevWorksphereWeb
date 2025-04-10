<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;

final class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['POST', 'GET'])]
    public function loginForm(): Response
    {
        return $this->render('login/index.html.twig');
    }

    #[Route('/forget', name: 'app_forgot_password', methods: ['GET'])]
    public function forgotPassword(): Response
    {
        return $this->render('login/forgot_password.html.twig');
    }


    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        // Create a redirect response to "/"
        $response = new RedirectResponse('/');

        // Clear the JWT cookie
        $response->headers->clearCookie('BEARER');

        return $response;
    }
}
