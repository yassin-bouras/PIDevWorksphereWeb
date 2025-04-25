<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\MailService;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/user')]
final class UserController extends AbstractController
{

    #[Route('/reclamation', name: 'api_user_reclamation', methods: ['POST'])]
    public function updateReclamation(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $message = $data['message'] ?? null;

        if (!$email || !$message) {
            return new JsonResponse(['error' => 'Missing email or message'], 400);
        }

        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        if (!$user->isBanned()) {
            return new JsonResponse(['error' => 'User is not banned'], 400);
        }

        if ($user->getMessageReclamation()) {
            return new JsonResponse(['error' => 'Reclamation already submitted'], 400);
        }

        $user->setMessageReclamation($message);

        $entityManager->flush();

        return new JsonResponse(['message' => 'Reclamation saved successfully']);
    }
    private $passwordHasher;

    // Inject the UserPasswordHasherInterface via constructor
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the plain password from the form and hash it
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
                $user->setMdp($hashedPassword);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('/login', [], Response::HTTP_SEE_OTHER);
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
    #[Route('/{iduser}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the plain password from the form and hash it if provided
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
    #[Route('/api/verify-email', name: 'api_verify_email', methods: ['POST'])]
    public function verifyEmail(Request $request, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'])) {
            return new JsonResponse(['error' => 'Email field is required'], 400);
        }

        $email = $data['email'];
        $user = $userRepository->findOneBy(['email' => $email]);

        if ($user) {
            return new JsonResponse(['exists' => true, 'message' => 'Email found']);
        } else {
            return new JsonResponse(['exists' => false, 'message' => 'Email not found'], 404);
        }
    }

    #[Route('/api/reset-password', name: 'api_reset_password', methods: ['POST'])]
    public function resetPasswordByEmail(Request $request, UserRepository $userRepository, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['newPassword'])) {
            return new JsonResponse(['error' => 'Missing email or newPassword field'], 400);
        }

        $email = $data['email'];
        $newPassword = $data['newPassword'];

        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['error' => 'Email not found'], 404);
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
        $user->setMdp($hashedPassword);

        $em->flush();

        return new JsonResponse(['message' => 'Password has been successfully reset']);
    }
}
