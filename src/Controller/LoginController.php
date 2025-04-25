<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use App\Service\MailService;

final class LoginController extends AbstractController
{
    private JWTTokenManagerInterface $jwtManager;
    private JWTEncoderInterface $jwtEncoder;
    private UserRepository $userRepository;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        JWTEncoderInterface $jwtEncoder,
        UserRepository $userRepository
    ) {
        $this->jwtManager = $jwtManager;
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
    }

    #[Route('/login', name: 'app_login', methods: ['POST', 'GET'])]
    public function loginForm(): Response
    {
        return $this->render('login/index.html.twig');
    }

    #[Route('/forgot', name: 'app_forgot_password', methods: ['GET'])]
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

    #[Route('/jwt', name: 'api_decode_token', methods: ['POST'])]
    public function decodeToken(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? null;

        if (!$token) {
            return new JsonResponse(['error' => 'Token is required'], 400);
        }

        try {
            $decodedData = $this->jwtEncoder->decode($token);

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
    #[Route('/api/send-email', name: 'api_send_email', methods: ['POST'])]
    public function sendEmail(Request $request, MailService $mailService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['recipient'], $data['subject'], $data['content'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        $mailService->sendMail(
            $data['recipient'],
            $data['subject'],
            $data['content']
        );

        return new JsonResponse(['message' => 'Email sent successfully']);
    }
    #[Route('/jwt/user', name: 'api_decode_token_user', methods: ['POST'])]
    public function decodeTokenReturnUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? null;

        if (!$token) {
            return new JsonResponse(['error' => 'Token is required'], 400);
        }

        try {
            $decodedData = $this->jwtEncoder->decode($token);
            $email = $decodedData['username'] ?? null;

            if (!$email) {
                return new JsonResponse(['error' => 'Email not found in token'], 400);
            }

            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                return new JsonResponse(['error' => 'User not found'], 404);
            }

            return new JsonResponse([
                'success' => true,
                'user' => [
                    'iduser' => $user->getIduser(),
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'email' => $user->getEmail(),
                    'mdp' => $user->getMdp(),
                    'imageprofil' => $user->getImageprofil(),
                    'tel' => $user->getNumtel(),
                    'role' => $user->getRole(),
                    'poste' => $user->getPoste(),
                    'adresse' => $user->getAdresse(),
                    'banned' => $user->isBanned(),
                    'reclamation' => $user->getMessagereclamation(),

                ],
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Invalid or expired token',
                'message' => $e->getMessage(),
            ], 401);
        }
    }
}
