<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Formation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Repository\FavoriRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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

    #[Route('/formation/employe/{id_f}/reservation/new', name: 'app_formation_reservation_new', methods: ['GET', 'POST'])]
    public function ajouterReservationPourFormation(
        int $id_f,
        Request $request,
        EntityManagerInterface $entityManager,
        HttpClientInterface $httpClient
    ): Response {
        $formation = $entityManager->getRepository(Formation::class)->find($id_f);
        if (!$formation) {
            throw $this->createNotFoundException('Formation non trouvée');
        }
    
        $reservation = new Reservation();
        $reservation->setFormation($formation);
        $reservation->setIduser(39);
    
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();
    
            // Envoi du SMS directement ici
            $numero = '+21653462002'; // Numéro statique pour le test
            $message = "Votre réservation pour la formation '{$formation->getTitre()}' est confirmée.";
    
            $body = [
                'messages' => [
                    [
                        'destinations' => [['to' => $numero]],
                        'from' => '447491163443',
                      'text' =>  $message ,

                    ],
                ],
            ];
    
            try {
                $response = $httpClient->request('POST', 'https://nmvl3e.api.infobip.com/sms/2/text/advanced', [
                    'headers' => [
                        'Authorization' => 'App 6e9114132980f5449e41c015aaba2ab9-c66a9121-fa77-4264-8dc2-b1b3557d0e69',
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'json' => $body,
                ]);
    
                if ($response->getStatusCode() === 200) {
                    // SMS envoyé
                } else {
                    // Gestion d'erreur
                }
            } catch (\Exception $e) {
                // Log ou message d’erreur ici
            }
    
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
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

}


