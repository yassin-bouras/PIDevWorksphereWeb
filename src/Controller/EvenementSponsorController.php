<?php

namespace App\Controller;

use App\Entity\EvenementSponsor;
use App\Entity\Evennement;
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
use Symfony\Bridge\Doctrine\Form\Type\EntityType; // Ajoutez cette ligne
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
    public function new(Request $request, EntityManagerInterface $entityManager, 
                       SponsorRepository $sponsorRepository, 
                       EvenementSponsorRepository $evenementSponsorRepository,
                       EvennementRepository $evenementRepository): Response
    {
        $evenementSponsor = new EvenementSponsor();
        $sponsorId = $request->query->get('sponsor_id');
        $sponsor = $sponsorId ? $sponsorRepository->find($sponsorId) : null;
    
        if ($sponsor) {
            $evenementSponsor->setSponsor($sponsor);
        }
    
        // Créez le formulaire avec des options personnalisées
        $form = $this->createForm(EvenementSponsorType::class, $evenementSponsor, [
            'sponsor' => $sponsor,
        ]);
    
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
            'sponsorSecteur' => $sponsor ? $sponsor->getSecteurSponsor() : null,
        ]);
    }
    
    // #[Route('/new', name: 'app_evenement_sponsor_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, EntityManagerInterface $entityManager, SponsorRepository $sponsorRepository, EvenementSponsorRepository $evenementSponsorRepository,
    // EvennementRepository $evenementRepository): Response
    // {
    //     $evenementSponsor = new EvenementSponsor();
    //     $sponsorId = $request->query->get('sponsor_id');
    
        
    //     if ($sponsorId) {
    //         $sponsor = $sponsorRepository->find($sponsorId);
    //         $evenementSponsor->setSponsor($sponsor);
    
    //         $allEvents = $evenementRepository->findAll();
    //         $availableEvents = [];
    
    //         foreach ($allEvents as $event) {
    //             $existingAssociation = $evenementSponsorRepository->findOneBy(['evenement' => $event, 'sponsor' => $sponsor]);
    //             if (!$existingAssociation && $event->getTypeEvent() === $sponsor->getSecteurSponsor()) {
    //                 $availableEvents[] = $event;
    //             }
    //         }
    
    //         $form = $this->createFormBuilder($evenementSponsor)
    //             ->add('evenement', EntityType::class, [
    //                 'class' => Evennement::class,
    //                 'choices' => $availableEvents,
    //                 'choice_label' => 'nomEvent',
    //                 'placeholder' => 'Sélectionnez un événement',
    //                 'attr' => [
    //                     'class' => 'form-control',
    //                     'data-secteur' => $sponsor->getSecteurSponsor() 
    //                 ]
    //             ])
    //             ->add('datedebutContrat')
    //             ->add('duree')
    //             ->getForm();
    //     } else {
    //         $form = $this->createForm(EvenementSponsorType::class, $evenementSponsor);
    //     }
    
    //     $form->handleRequest($request);
    
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($evenementSponsor);
    //         $entityManager->flush();
    
    //         return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
    //     }
    
    //     return $this->render('evenement_sponsor/new.html.twig', [
    //         'evenement_sponsor' => $evenementSponsor,
    //         'form' => $form->createView(),
    //         'sponsorId' => $sponsorId,
    //         'sponsorSecteur' => $sponsorId ? $sponsor->getSecteurSponsor() : null,
    //     ]);
    // }
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