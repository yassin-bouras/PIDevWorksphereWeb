<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/user')]
final class UserController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('search', '');
        $role = $request->query->get('role', '');

        $queryBuilder = $userRepository->createQueryBuilder('u');

        // Search by name or email (case-insensitive)
        if ($search) {
            $queryBuilder->andWhere('UPPER(u.nom) LIKE :search OR UPPER(u.email) LIKE :search')
                ->setParameter('search', '%' . strtoupper($search) . '%');
        }

        // Filter by role (case-insensitive)
        if ($role && in_array(strtoupper($role), ['EMPLOYE', 'MANAGER', 'CANDIDAT', 'RH'], true)) {
            $queryBuilder->andWhere('UPPER(u.role) = :role')
                ->setParameter('role', strtoupper($role));
        }

        // Paginate the results
        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10 // Items per page
        );

        return $this->render('user/index.html.twig', [
            'users' => $pagination, // Pass pagination as 'users' for compatibility
            'search' => $search,
            'role' => $role,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setMdp($hashedPassword);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{iduser}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{iduser}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setMdp($hashedPassword);
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{iduser}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getIduser(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    // API ENDPOINTS
    #[Route('/api/send-email', name: 'api_send_email', methods: ['POST'])]
    public function sendEmail(Request $request, MailService $mailService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['recipient'], $data['subject'], $data['content'])) {
            return new JsonResponse(['error' => 'Missing required fields'], 400);
        }

        $mailService->sendMail($data['recipient'], $data['subject'], $data['content']);

        return new JsonResponse(['message' => 'Email sent successfully']);
    }

    #[Route('/api/verify-email', name: 'api_verify_email', methods: ['POST'])]
    public function verifyEmail(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'])) {
            return new JsonResponse(['error' => 'Email field is required'], 400);
        }

        $user = $userRepository->findOneBy(['email' => $data['email']]);

        return new JsonResponse([
            'exists' => (bool) $user,
            'message' => $user ? 'Email found' : 'Email not found'
        ], $user ? 200 : 404);
    }

    #[Route('/api/reset-password', name: 'api_reset_password', methods: ['POST'])]
    public function resetPasswordByEmail(Request $request, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['newPassword'])) {
            return new JsonResponse(['error' => 'Missing email or newPassword'], 400);
        }

        $user = $userRepository->findOneBy(['email' => $data['email']]);

        if (!$user) {
            return new JsonResponse(['error' => 'Email not found'], 404);
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['newPassword']);
        $user->setMdp($hashedPassword);

        $em->flush();

        return new JsonResponse(['message' => 'Password has been successfully reset']);
    }
    #[Route('/user/{id}/ban', name: 'app_user_ban')]
    public function ban(User $user, EntityManagerInterface $em): Response
    {
        $user->setBanned(true);
        $em->flush();
        return $this->redirectToRoute('app_user_index');
    }

    #[Route('/user/{id}/unban', name: 'app_user_unban')]
    public function unban(User $user, EntityManagerInterface $em): Response
    {
        $user->setBanned(false);
        $em->flush();
        return $this->redirectToRoute('app_user_index');
    }
    private $entityManager;
    private $userRepository;
    #[Route('/reclamation', name: 'app_user_reclamation')]
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
        if ($user->getMessageReclamation()) {
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

    #[Route('/user/{id}/promote', name: 'app_user_promote')]
    public function promote(User $user, EntityManagerInterface $em): Response
    {
        $user->setRole('ROLE_MANAGER'); // example promotion
        $em->flush();
        return $this->redirectToRoute('app_user_index');
    }
}
