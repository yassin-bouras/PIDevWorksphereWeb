<?php

namespace App\Controller;

use App\Entity\EvenementSponsor;
use App\Form\EvenementSponsorType;
use App\Repository\EvennementRepository;
use App\Repository\EvenementSponsorRepository;
use App\Repository\SponsorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/evenement/sponsor')]
final class EvenementSponsorController extends AbstractController
{
    #[Route(name: 'app_evenement_sponsor_index', methods: ['GET'])]
    public function index(EvenementSponsorRepository $evenementSponsorRepository): Response
    {
        return $this->render('evenement_sponsor/index.html.twig', [
            'evenement_sponsors' => $evenementSponsorRepository->findAll(),
        ]);
    }


    #[Route('/new', name: 'app_evenement_sponsor_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SponsorRepository $sponsorRepository, EvenementSponsorRepository $evenementSponsorRepository,
    EvennementRepository $evenementRepository): Response
    {
        $evenementSponsor = new EvenementSponsor();
        $sponsorId = $request->query->get('sponsor_id');

        // Si un sponsor est sélectionné, le pré-remplir et filtrer les événements
        if ($sponsorId) {
            $sponsor = $sponsorRepository->find($sponsorId);
            $evenementSponsor->setSponsor($sponsor);

            // Récupérer tous les événements
            $allEvents = $evenementRepository->findAll();
            $availableEvents = [];

            // Filtrer les événements pour ne garder que ceux auxquels le sponsor n'est pas déjà associé
            foreach ($allEvents as $event) {
                $existingAssociation = $evenementSponsorRepository->findOneBy(['evenement' => $event, 'sponsor' => $sponsor]);
                if (!$existingAssociation) {
                    $availableEvents[] = $event;
                }
            }

            $form = $this->createFormBuilder($evenementSponsor)
                ->add('evenement', null, [
                    'choices' => $availableEvents,
                    'choice_label' => 'nomEvent', // Assurez-vous que votre entité Evenement a une propriété 'nomEvent'
                    'placeholder' => 'Sélectionnez un événement',
                ])
                ->add('datedebutContrat')
                ->add('duree')
                ->getForm();
        } else {
            // Sinon, inclure le champ sponsor dans le formulaire et afficher tous les événements
            $form = $this->createForm(EvenementSponsorType::class, $evenementSponsor);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evenementSponsor);
            $entityManager->flush();

            return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement_sponsor/new.html.twig', [
            'evenement_sponsor' => $evenementSponsor,
            'form' => $form->createView(),
            'sponsorId' => $sponsorId,
        ]);
    }

    #[Route('/{evenement_id}/{sponsor_id}', name: 'app_evenement_sponsor_show', methods: ['GET'])]
    public function show(
        #[MapEntity(mapping: ['evenement_id' => 'evenement', 'sponsor_id' => 'sponsor'])]
        EvenementSponsor $evenementSponsor
    ): Response {
        return $this->render('evenement_sponsor/show.html.twig', [
            'evenement_sponsor' => $evenementSponsor,
        ]);
    }

    #[Route('/{evenement_id}/{sponsor_id}/edit', name: 'app_evenement_sponsor_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['evenement_id' => 'evenement', 'sponsor_id' => 'sponsor'])]
        EvenementSponsor $evenementSponsor,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(EvenementSponsorType::class, $evenementSponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_sponsor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement_sponsor/edit.html.twig', [
            'evenement_sponsor' => $evenementSponsor,
            'form' => $form,
        ]);
    }

    #[Route('/{evenement_id}/{sponsor_id}', name: 'app_evenement_sponsor_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['evenement_id' => 'evenement', 'sponsor_id' => 'sponsor'])]
        EvenementSponsor $evenementSponsor,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $evenementSponsor->getEvenement()->getIdEvent() . $evenementSponsor->getSponsor()->getIdSponsor(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($evenementSponsor);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
    }
}