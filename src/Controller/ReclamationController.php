<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reclamation')]
final class ReclamationController extends AbstractController
{
    #[Route('/new/{id}', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, int $id, UserRepository $userRepository): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->find($id);

            if (!$user) {
                throw $this->createNotFoundException('User not found.');
            }

            $reclamation->setUser($user);

            if (!$reclamation->getDatedepot()) {
                $reclamation->setDatedepot(new \DateTimeImmutable());
            }

            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('reclamations_by_user', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
            'id_user' => $id,
        ]);
    }

    #[Route('/user/{id}', name: 'reclamations_by_user', methods: ['GET'])]
    public function showReclamationsByUser(
        int $id,
        ReclamationRepository $reclamationRepository,
        UserRepository $userRepository
    ): Response {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $role = $user->getRole();
        $reclamations = [];

        if ($role === 'Candidat') {
            $reclamations = $reclamationRepository->findBy(['user' => $id]);
        } elseif ($role === 'Employe') {
            $reclamations = $reclamationRepository->findBy(['receiver' => $id]);
        }

        $reponsesMap = [];
        foreach ($reclamations as $reclamation) {
            $reponseId = $reclamationRepository->findReponseIdByReclamation($reclamation->getIdReclamation());
            $reponsesMap[$reclamation->getIdReclamation()] = $reponseId ?? 0;
        }

        return $this->render('reclamation/by_user.html.twig', [
            'reclamations' => $reclamations,
            'id_user' => $id,
            'role' => $role,
            'reponsesMap' => $reponsesMap,
        ]);
    }



    #[Route('/{id_reclamation}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id_reclamation}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('reclamations_by_user', ['id' => $reclamation->getUser()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form->createView(),
            'id_user' => $reclamation->getUser()->getId(),
        ]);
    }

    #[Route('/{id_reclamation}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getIdReclamation(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reclamations_by_user', ['id' => $reclamation->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }
}
