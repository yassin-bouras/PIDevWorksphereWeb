<?php

namespace App\Controller;

use App\Entity\Evennement;
use App\Form\EvennementType;
use App\Repository\EvennementRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/evennement')]
final class EvennementController extends AbstractController
{
    private $jwtEncoder;
    private $userRepository;

    public function __construct(JWTEncoderInterface $jwtEncoder, UserRepository $userRepository)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
    }

    #[Route(name: 'app_evennement_index', methods: ['GET'])]
    public function index(EvennementRepository $evennementRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('search');

        if ($searchTerm) {
            $evennements = $evennementRepository->findByNomEvent($searchTerm);
        } else {
            $evennements = $evennementRepository->findAll();
        }

        return $this->render('evennement/index.html.twig', [
            'evennements' => $evennements,
        ]);
    }

    #[Route('/new', name: 'app_evennement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $token = $request->cookies->get('BEARER');
        
        if (!$token) {
            return $this->redirectToRoute('app_login');
        }

        try {
            $decodedData = $this->jwtEncoder->decode($token);
            $email = $decodedData['username'] ?? null;

            if (!$email) {
                $this->addFlash('error', 'Email non trouvé dans le token.');
                return $this->redirectToRoute('app_login');
            }

            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'Utilisateur non trouvé.');
                return $this->redirectToRoute('app_login');
            }

            $evennement = new Evennement();
            $form = $this->createForm(EvennementType::class, $evennement);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Associer l'utilisateur connecté à l'événement
                $evennement->setUser($user);
                
                $entityManager->persist($evennement);
                $entityManager->flush();

                $this->addFlash('success', 'Événement créé avec succès!');
                return $this->redirectToRoute('app_evennement_index');
            }

            return $this->render('evennement/new.html.twig', [
                'form' => $form->createView(),
            ]);

        } catch (\Exception $e) {
            $this->addFlash('error', 'Token invalide ou expiré.');
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/{idEvent}', name: 'app_evennement_show', methods: ['GET'])]
    public function show(Evennement $evennement): Response
    {
        return $this->render('evennement/show.html.twig', [
            'evennement' => $evennement,
        ]);
    }

    #[Route('/{idEvent}/edit', name: 'app_evennement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evennement $evennement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvennementType::class, $evennement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evennement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evennement/edit.html.twig', [
            'evennement' => $evennement,
            'form' => $form,
        ]);
    }

    #[Route('/{idEvent}', name: 'app_evennement_delete', methods: ['POST'])]
    public function delete(Request $request, Evennement $evennement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $evennement->getIdEvent(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($evennement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evennement_index', [], Response::HTTP_SEE_OTHER);
    }
}