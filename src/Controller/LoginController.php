<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

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

        $response = new RedirectResponse('/');


        $response->headers->clearCookie('BEARER');

        return $response;
    }
    private $tokenStorage;

    private $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }
    #[Route('/jwt', name: 'api_decode_token', methods: ['POST'])]
    public function decodeToken(Request $request): JsonResponse
    {
        // Get the token from the request (e.g., JSON body, header, or query parameter)
        $token = $request->request->get('token'); // Adjust based on how you send the token

        if (!$token) {
            return new JsonResponse(['error' => 'Token is required'], 400);
        }

        try {
            // Decode the token
            $decodedData = $this->jwtManager->decode($this->tokenStorage->getToken());

            // If you provided a raw token string, you might need a custom approach (see note below)
            // For Lexik, decode expects a TokenInterface, so we use tokenStorage for authenticated requests

            return new JsonResponse([
                'success' => true,
                'data' => $decodedData,
            ], 200);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Invalid or expired token',
                'message' => $e->getMessage(),
            ], 401);
        }
    }
}
