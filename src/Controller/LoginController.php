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
use App\Service\HCaptchaService;
use Doctrine\ORM\EntityManagerInterface;

final class LoginController extends AbstractController
{
    private string $hcaptchaSiteKey;
    private JWTTokenManagerInterface $jwtManager;
    private JWTEncoderInterface $jwtEncoder;
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;
    private HCaptchaService $hcaptchaService;

    public function __construct(
        string $hcaptchaSiteKey,
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $jwtManager,
        JWTEncoderInterface $jwtEncoder,
        UserRepository $userRepository,
        HCaptchaService $hcaptchaService
    ) {
        $this->hcaptchaSiteKey = $hcaptchaSiteKey;
        $this->jwtManager = $jwtManager;
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->hcaptchaService = $hcaptchaService;
    }

    #[Route('/lrreclamation', name: 'app_lr_reclamation')]
    public function submitReclamation(Request $request): JsonResponse
    {
        error_log('DEBUG: Entering /user/reclamation route');

        // Parse JSON payload
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $message = $data['message'] ?? null;

        // Validate input
        if (!$email || !$message) {
            error_log('ERROR: Missing email or message');
            return new JsonResponse(['error' => 'Email and message are required'], 400);
        }

        // Find user by email
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            error_log('ERROR: User not found for email: ' . $email);
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        // Check if user is banned
        if (!$user->isBanned()) {
            error_log('ERROR: User is not banned: ' . $email);
            return new JsonResponse(['error' => 'User is not banned'], 403);
        }

        // Check if reclamation already exists
        if ($user->getMessagereclamation()) {
            error_log('ERROR: Reclamation already submitted for user: ' . $email);
            return new JsonResponse(['error' => 'Reclamation already submitted'], 400);
        }

        // Save reclamation message
        try {
            $user->setMessageReclamation($message);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            error_log('DEBUG: Reclamation saved for user: ' . $email);
            return new JsonResponse(['message' => 'Reclamation submitted successfully'], 200);
        } catch (\Exception $e) {
            error_log('ERROR: Failed to save reclamation: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Failed to save reclamation', 'details' => $e->getMessage()], 500);
        }
    }

    #[Route('/login', name: 'app_login', methods: ['POST', 'GET'])]
    public function loginForm(Request $request): Response
    {
        // Check for ban status
        $banUntil = $request->getSession()->get('login_ban_until');
        $isBanned = $banUntil && new \DateTime() < new \DateTime($banUntil);

        if ($isBanned) {
            return $this->render('login/index.html.twig', [
                'hcaptcha_site_key' => $this->hcaptchaSiteKey,
                'is_banned' => true,
                'ban_until' => $banUntil,
                'hcaptcha_error' => $request->getSession()->get('hcaptcha_error'),
            ]);
        }

        if ($request->isMethod('POST')) {
            $data = json_decode($request->getContent(), true);
            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;
            $hcaptchaResponse = $data['h-captcha-response'] ?? null;

            // Check if user is banned
            if ($isBanned) {
                return new JsonResponse(['error' => 'Too many failed attempts. Try again later.'], 403);
            }

            // Validate hCaptcha
            if (!$hcaptchaResponse) {
                $this->handleFailedAttempt($request);
                $request->getSession()->set('hcaptcha_error', 'Please complete the CAPTCHA.');
                return new JsonResponse(['error' => 'Please complete the CAPTCHA'], 400);
            }

            $isHCaptchaValid = $this->hcaptchaService->verify($hcaptchaResponse);
            if (!$isHCaptchaValid) {
                $this->handleFailedAttempt($request);
                $request->getSession()->set('hcaptcha_error', 'Invalid CAPTCHA. Please try again.');
                return new JsonResponse(['error' => 'Invalid CAPTCHA'], 400);
            }

            // Clear hCaptcha error if successful
            $request->getSession()->remove('hcaptcha_error');

            // Proceed with authentication (your existing logic)
            $user = $this->userRepository->findOneBy(['email' => $email]);
            if (!$user || !password_verify($password, $user->getMdp())) {
                $this->handleFailedAttempt($request);
                return new JsonResponse(['error' => 'Invalid credentials'], 401);
            }

            // Generate JWT token
            $token = $this->jwtManager->create($user);

            return new JsonResponse(['token' => $token], 200);
        }

        return $this->render('login/index.html.twig', [
            'hcaptcha_site_key' => $this->hcaptchaSiteKey,
            'is_banned' => $isBanned,
            'ban_until' => $banUntil,
            'hcaptcha_error' => $request->getSession()->get('hcaptcha_error'),
        ]);
    }

    private function handleFailedAttempt(Request $request): void
    {
        // Increment failed attempts count
        $failedAttempts = $request->getSession()->get('login_failed_attempts', 0) + 1;
        $request->getSession()->set('login_failed_attempts', $failedAttempts);

        // If 3 or more failed attempts, ban for 15 seconds
        if ($failedAttempts >= 3) {
            $banUntil = (new \DateTime())->add(new \DateInterval('PT15S'));
            $request->getSession()->set('login_ban_until', $banUntil->format('Y-m-d H:i:s'));
            $request->getSession()->set('login_failed_attempts', 0);
        }
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

    #[Route('/jwt/user', name: 'api_decode_token_user', methods: ['POST', 'GET'])]
    public function decodeTokenReturnUser(Request $request): JsonResponse
    {
        // 1. Try to get the token from JSON body
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? null;

        // 2. If not found, try to get it from the BEARER cookie
        if (!$token) {
            $token = $request->cookies->get('BEARER');
        }

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
