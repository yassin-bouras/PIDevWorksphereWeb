<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleAuthenticator extends OAuth2Authenticator
{
    private ClientRegistry $clientRegistry;
    private EntityManagerInterface $entityManager;
    private RouterInterface $router;
    private JWTTokenManagerInterface $jwtManager;
    private HttpClientInterface $client;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        HttpClientInterface $client,
        JWTTokenManagerInterface $jwtManager
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->jwtManager = $jwtManager;
        $this->client = $client;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        error_log('DEBUG: Entering GoogleAuthenticator::authenticate');

        $client = $this->clientRegistry->getClient('google');
        try {
            $accessToken = $client->getAccessToken();
            /** @var GoogleUser $googleUser */
            $googleUser = $client->fetchUserFromToken($accessToken);
        } catch (\Exception $e) {
            error_log('ERROR: Failed to fetch Google user: ' . $e->getMessage());
            throw new AuthenticationException('Failed to authenticate with Google');
        }

        $email = $googleUser->getEmail();
        $name = $googleUser->getName();

        return new SelfValidatingPassport(
            new UserBadge($email, function () use ($email, $name) {
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

                if (!$user) {
                    error_log('DEBUG: Creating new user for email: ' . $email);
                    $user = new User();
                    $user->setEmail($email);
                    $user->setNom($name);
                    $user->setMdp(md5(uniqid())); // Temporary password
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                }

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): RedirectResponse
    {
        error_log('DEBUG: Entering onAuthenticationSuccess');

        /** @var User $user */
        $user = $token->getUser();

        // Validate user
        if ($user->isBanned()) {
            error_log('DEBUG: User is banned: ' . $user->getEmail());
            return new RedirectResponse($this->router->generate('app_login') . '?error=banned');
        }

        // Generate JWT token
        try {
            $jwt = $this->jwtManager->create($user);
            error_log('DEBUG: JWT generated for user: ' . $user->getEmail());

            $response = new RedirectResponse($this->router->generate('app_dashboard'));
            $response->headers->setCookie(
                new \Symfony\Component\HttpFoundation\Cookie('BEARER', $jwt, strtotime('+1 day'), '/', null, false, true)
            );

            return $response;
        } catch (\Exception $e) {
            error_log('ERROR: Failed to generate JWT: ' . $e->getMessage());
            return new RedirectResponse($this->router->generate('app_login') . '?error=jwt');
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        error_log('ERROR: Authentication failed: ' . $exception->getMessage());
        return new JsonResponse(['error' => $exception->getMessage()], 401);
    }
}
