<?php

namespace App\Controller;

use App\Entity\HistoriqueEntretien;
use App\Form\HistoriqueEntretienType;
use App\Repository\HistoriqueEntretienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/historique/entretien')]
final class HistoriqueEntretienController extends AbstractController{
    #[Route(name: 'app_historique_entretien_index', methods: ['GET'])]
    public function index(HistoriqueEntretienRepository $historiqueEntretienRepository): Response
    {
        return $this->render('historique_entretien/index.html.twig', [
            'historique_entretiens' => $historiqueEntretienRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_historique_entretien_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $historiqueEntretien = new HistoriqueEntretien();
        $form = $this->createForm(HistoriqueEntretienType::class, $historiqueEntretien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($historiqueEntretien);
            $entityManager->flush();

            return $this->redirectToRoute('app_historique_entretien_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('historique_entretien/new.html.twig', [
            'historique_entretien' => $historiqueEntretien,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_historique_entretien_show', methods: ['GET'])]
    public function show(HistoriqueEntretien $historiqueEntretien): Response
    {
        return $this->render('historique_entretien/show.html.twig', [
            'historique_entretien' => $historiqueEntretien,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_historique_entretien_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HistoriqueEntretien $historiqueEntretien, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HistoriqueEntretienType::class, $historiqueEntretien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_historique_entretien_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('historique_entretien/edit.html.twig', [
            'historique_entretien' => $historiqueEntretien,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_historique_entretien_delete', methods: ['POST'])]
    public function delete(Request $request, HistoriqueEntretien $historiqueEntretien, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$historiqueEntretien->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($historiqueEntretien);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_historique_entretien_index', [], Response::HTTP_SEE_OTHER);
    }
}
