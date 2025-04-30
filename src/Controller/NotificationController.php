<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Form\NotificationType;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;


#[Route('/notification')]
final class NotificationController extends AbstractController
{
    private JWTEncoderInterface $jwtEncoder;
    private UserRepository $userRepository;

    public function __construct(JWTEncoderInterface $jwtEncoder, UserRepository $userRepository)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
    }

    #[Route(name: 'app_notification_index', methods: ['GET'])]
    public function index(NotificationRepository $notificationRepository): Response
    {
        return $this->render('notification/index.html.twig', [
            'notifications' => $notificationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_notification_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $notification = new Notification();
        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($notification);
            $entityManager->flush();

            return $this->redirectToRoute('app_notification_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('notification/new.html.twig', [
            'notification' => $notification,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_notification_show', methods: ['GET'])]
    public function show(Notification $notification): Response
    {
        return $this->render('notification/show.html.twig', [
            'notification' => $notification,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_notification_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Notification $notification, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NotificationType::class, $notification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_notification_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('notification/edit.html.twig', [
            'notification' => $notification,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_notification_delete', methods: ['POST'])]
    public function delete(Request $request, Notification $notification, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $notification->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($notification);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_notification_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/api/user/{iduser}', name: 'app_notification_api_user', methods: ['GET'])]
    public function getUserNotifications(int $iduser, NotificationRepository $notificationRepository): JsonResponse
    {
        // Find the user
        $user = $this->userRepository->find($iduser);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        // Get user notifications
        $notifications = $notificationRepository->findBy(
            ['user' => $user],
            ['created_at' => 'DESC'],
            10 // Limit to the 10 most recent notifications
        );

        // Transform the notifications into an array of data
        $notificationsData = [];
        foreach ($notifications as $notification) {
            $notificationsData[] = [
                'id' => $notification->getId(),
                'message' => $notification->getMessage(),
                'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s'),
                'isRead' => $notification->isRead(),
                'type' => $notification->getNotificationType()
            ];
        }

        return new JsonResponse([
            'notifications' => $notificationsData
        ]);
    }

    #[Route('/{id}/mark-read-ajax', name: 'app_notification_mark_read_ajax', methods: ['POST'])]
    public function markAsReadAjax(Request $request, Notification $notification, EntityManagerInterface $entityManager): JsonResponse
    {
        // Security check to ensure users can only mark their own notifications as read
        $token = $request->cookies->get('BEARER');

        if (!$token) {
            return new JsonResponse(['error' => 'Not authenticated'], 401);
        }

        try {
            $decodedData = $this->jwtEncoder->decode($token);
            $email = $decodedData['username'] ?? null;
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user || $notification->getUser() !== $user) {
                return new JsonResponse(['error' => 'Not authorized'], 403);
            }

            $notification->setIsRead(true);
            $entityManager->flush();

            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Token error'], 401);
        }
    }

    #[Route('/mark-all-read', name: 'app_notification_mark_all_read', methods: ['GET'])]
    public function markAllAsRead(Request $request, EntityManagerInterface $entityManager, NotificationRepository $notificationRepository): Response
    {
        $token = $request->cookies->get('BEARER');

        if (!$token) {
            throw $this->createAccessDeniedException('Not authenticated');
        }

        try {
            $decodedData = $this->jwtEncoder->decode($token);
            $email = $decodedData['username'] ?? null;
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                throw $this->createAccessDeniedException('User not found');
            }

            $notifications = $notificationRepository->findBy(['user' => $user, 'is_read' => false]);

            foreach ($notifications as $notification) {
                $notification->setIs_read(true);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Toutes les notifications ont été marquées comme lues.');

            return $this->redirectToRoute('app_notification_index');
        } catch (\Exception $e) {
            throw $this->createAccessDeniedException('Authentication error');
        }
    }


    #[Route('/{id}/mark-read', name: 'app_notification_mark_read', methods: ['POST'])]
    public function markAsRead(Request $request, Notification $notification, EntityManagerInterface $entityManager): Response
    {
        // Security check to ensure users can only mark their own notifications as read
        $token = $request->cookies->get('BEARER');

        if (!$token) {
            throw $this->createAccessDeniedException('Not authenticated');
        }

        try {
            $decodedData = $this->jwtEncoder->decode($token);
            $email = $decodedData['username'] ?? null;
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user || $notification->getUser() !== $user) {
                throw $this->createAccessDeniedException('Not authorized');
            }

            $notification->setIs_read(true);
            $entityManager->flush();

            $this->addFlash('success', 'Notification marquée comme lue.');

            // Redirect back to the referrer or to the notification index
            $referer = $request->headers->get('referer');
            return $referer ? $this->redirect($referer) : $this->redirectToRoute('app_notification_index');
        } catch (\Exception $e) {
            throw $this->createAccessDeniedException('Authentication error');
        }
    }

    #[Route('/api/clear-all', name: 'app_notification_clear_all', methods: ['POST'])]
    public function clearAllNotifications(Request $request, NotificationRepository $notificationRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        // Get current user from JWT token
        $token = $request->cookies->get('BEARER');
        if (!$token) {
            return new JsonResponse(['success' => false, 'message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $data = $this->jwtEncoder->decode($token);
            // Check multiple possible fields for user identification
            $userId = $data['id'] ?? $data['user_id'] ?? $data['iduser'] ?? null;

            // If no userId from fields, try to get user by username/email
            if (!$userId && isset($data['username'])) {
                $user = $this->userRepository->findOneBy(['email' => $data['username']]);
                if ($user) {
                    $userId = $user->getId(); // Or getIdUser() depending on your entity
                }
            }

            if (!$userId) {
                return new JsonResponse(['success' => false, 'message' => 'Invalid token - could not identify user'], Response::HTTP_UNAUTHORIZED);
            }

            $user = $this->userRepository->find($userId);
            if (!$user) {
                return new JsonResponse(['success' => false, 'message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            // Delete all notifications for this user
            $notifications = $notificationRepository->findBy(['user' => $user]);

            if (empty($notifications)) {
                return new JsonResponse(['success' => true, 'message' => 'No notifications to delete']);
            }

            foreach ($notifications as $notification) {
                $entityManager->remove($notification);
            }

            $entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => 'All notifications deleted']);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error processing request: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
