<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\FavoriRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reservation')]
final class ReservationController extends AbstractController{
    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository , FormationRepository $formationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id_r}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id_r}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id_r}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId_r(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }




     // Nouvelle méthode pour ajouter une réservation à une formation spécifique
     #[Route('/formation/{id_f}/ajouter-reservation', name: 'app_formation_ajouter_reservation', methods: ['GET', 'POST'])]
     public function ajouterReservation(
         Request $request,
         EntityManagerInterface $entityManager,
         FormationRepository $formationRepository,
         int $id
     ): Response {
         // Récupérer la formation par ID
         $formation = $formationRepository->find($id);
 
         // Vérifier si la formation existe
         if (!$formation) {
             throw $this->createNotFoundException('La formation demandée n\'existe pas.');
         }
 
         // Créer une nouvelle réservation
         $reservation = new Reservation();
         $reservation->setFormation($formation); // Associer la réservation à la formation
 
         // Créer le formulaire de réservation
         $form = $this->createForm(ReservationType::class, $reservation);
         $form->handleRequest($request);
 
         // Si le formulaire est soumis et valide
         if ($form->isSubmitted() && $form->isValid()) {
             $entityManager->persist($reservation);
             $entityManager->flush();
 
             // Rediriger vers la page d'affichage des réservations
             return $this->redirectToRoute('app_reservation_index');
         }
 
         // Retourner le formulaire à l'utilisateur
         return $this->render('reservation/ajouter.html.twig', [
             'form' => $form->createView(),
             'formation' => $formation, // Passer la formation à la vue
         ]);
     }
}
