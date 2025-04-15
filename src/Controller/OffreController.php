<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use App\Repository\CandidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/offre')]
final class OffreController extends AbstractController
{
    #[Route(name: 'app_offre_index', methods: ['GET'])]
    public function index(OffreRepository $offreRepository): Response
    {
        return $this->render('offre/index.html.twig', [
            'offres' => $offreRepository->findAll(),
        ]);
    }
    #[Route('/front', name: 'app_offre_front_index', methods: ['GET'])]
    public function frontIndex(Request $request, OffreRepository $offreRepository): Response
    {
        $search = $request->query->get('search', ''); // Get the search term from the query string
        $offres = $offreRepository->findByTitre($search); // Use a custom repository method to filter by title

        return $this->render('offre/front_index.html.twig', [
            // 'offres' => $offreRepository->findAll(),
            'offres' => $offres,
            'search' => $search,
            'context' => 'front'
        ]);
    }

    #[Route('/new', name: 'app_offre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, string $context = 'back'): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offre);
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre/new.html.twig', [
            'offre' => $offre,
            'form' => $form,
            'context' => $context,
        ]);
    }

    #[Route('/{id_offre}', name: 'app_offre_show', methods: ['GET'])]
    public function show(Offre $offre, string $context = 'back'): Response
    {
        return $this->render('offre/show.html.twig', [
            'offre' => $offre,
            'context' => $context,
        ]);
    }

    #[Route('/{id_offre}/edit', name: 'app_offre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offre $offre, EntityManagerInterface $entityManager, string $context = 'back'): Response
    {
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute($context === 'front' ? 'app_offre_front_index' : 'app_offre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre/edit.html.twig', [
            'offre' => $offre,
            'form' => $form,
            'context' => $context,
        ]);
    }

    #[Route('/{id_offre}', name: 'app_offre_delete', methods: ['POST'])]
    public function delete(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $offre->getId_offre(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id_offre}/candidatures', name: 'app_offre_candidatures', methods: ['GET'])]
    public function candidatures(Offre $offre, CandidatureRepository $candidatureRepository): Response
    {
        $candidatures = $candidatureRepository->findBy(['offre' => $offre]);

        return $this->render('offre/candidatures.html.twig', [
            'offre' => $offre,
            'candidatures' => $candidatures,
        ]);
    }
}
