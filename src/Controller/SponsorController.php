<?php

namespace App\Controller;

use App\Entity\Sponsor;
use App\Form\SponsorType;
use App\Repository\EvenementSponsorRepository;
use App\Service\GeminiAIService;
use App\Repository\SponsorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sponsor')]
final class SponsorController extends AbstractController{
    #[Route(name: 'app_sponsor_index', methods: ['GET'])]
    public function index(SponsorRepository $sponsorRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('search');
        $budgetFilter = $request->query->get('budget');

        $sponsors = $sponsorRepository->findBySearchAndBudget($searchTerm, $budgetFilter);

        return $this->render('sponsor/index.html.twig', [
            'sponsors' => $sponsors,
        ]);
    }

    #[Route('/new', name: 'app_sponsor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sponsor = new Sponsor();
        $form = $this->createForm(SponsorType::class, $sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sponsor);
            $entityManager->flush();

            return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sponsor/new.html.twig', [
            'sponsor' => $sponsor,
            'form' => $form,
        ]);
    }

    #[Route('/{idSponsor}', name: 'app_sponsor_show', methods: ['GET'])]
    public function show(Sponsor $sponsor): Response
    {
        return $this->render('sponsor/show.html.twig', [
            'sponsor' => $sponsor,
        ]);
    }

    #[Route('/{idSponsor}/edit', name: 'app_sponsor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sponsor $sponsor, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SponsorType::class, $sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sponsor/edit.html.twig', [
            'sponsor' => $sponsor,
            'form' => $form,
        ]);
    }

    #[Route('/{idSponsor}/delete', name: 'app_sponsor_delete', methods: ['POST'])]
    public function delete(Request $request, Sponsor $sponsor, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sponsor->getIdSponsor(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($sponsor);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/{idSponsor}/events', name: 'app_sponsor_events', methods: ['GET'])]
    public function showSponsorEvents(Sponsor $sponsor, EvenementSponsorRepository $evenementSponsorRepository): Response
    {
        $evenementSponsors = $evenementSponsorRepository->findBy(['sponsor' => $sponsor]);

        return $this->render('sponsor/events.html.twig', [
            'sponsor' => $sponsor,
            'evenement_sponsors' => $evenementSponsors,
        ]);
    }

    #[Route('/suggestions', name: 'app_sponsor_suggestions', methods: ['POST'])]
public function getSuggestions(Request $request, GeminiAIService $geminiAiService): Response
{
    $sector = $request->request->get('sector');
    
    try {
        $prompt = "Génère une liste de 5 sponsors potentiels dans le secteur $sector. 
        Pour chaque sponsor, fournis:
        - Nom complet (data-name)
        - Domaine d'activité
        - Budget moyen estimé en euros (data-budget)
        
        Format de sortie EXCLUSIVEMENT:
        <div class='suggestion-item' data-name='NOM_DU_SPONSOR' data-budget='BUDGET'>
            <h5>NOM_DU_SPONSOR</h5>
            <p><strong>Domaine:</strong> DOMAINE_ACTIVITE</p>
            <p><strong>Budget moyen:</strong> BUDGET €</p>
        </div>
        
        Ne donne aucune explication, commentaire ou texte supplémentaire. Juste les 5 divs comme dans l'exemple.";
        
        $response = $geminiAiService->generateContent($prompt);
        
        // Nettoyer la réponse si nécessaire
        $cleanResponse = strip_tags($response['text'], '<div><h5><p><strong><em><br>');
        
        return new Response($cleanResponse);
    } catch (\Exception $e) {
        return new Response(
            '<div class="alert alert-danger">Erreur lors de la génération des suggestions: '.$e->getMessage().'</div>',
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
}