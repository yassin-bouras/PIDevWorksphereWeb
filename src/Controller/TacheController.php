<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

#[Route('/tache')]
final class TacheController extends AbstractController
{
    
    private $jwtEncoder;
    private $userRepository;

    public function __construct(JWTEncoderInterface $jwtEncoder, UserRepository $userRepository)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
     

    }
 


    #[Route(name: 'app_tache_index', methods: ['GET'])]
    public function index(TacheRepository $tacheRepository): Response
    {
        $taches = $tacheRepository->findAll();
        $groupedTaches = [
            'À faire' => array_filter($taches, fn($t) => $t->getStatut() === 'À faire'),
            'En cours' => array_filter($taches, fn($t) => $t->getStatut() === 'En cours'),
            'Terminé' => array_filter($taches, fn($t) => $t->getStatut() === 'Terminé'),
        ];
    
        return $this->render('tache/index.html.twig', [
            'groupedTaches' => $groupedTaches,
        ]);
    }

    #[Route('/new', name: 'app_tache_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tache);
            $entityManager->flush();

            return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tache/new.html.twig', [
            'tache' => $tache,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tache_show', methods: ['GET'])]
    public function show(Tache $tache): Response
    {
        return $this->render('tache/show.html.twig', [
            'tache' => $tache,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tache_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tache/edit.html.twig', [
            'tache' => $tache,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tache_delete', methods: ['POST'])]
    public function delete(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tache->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tache);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/update-status', name: 'app_tache_update_status', methods: ['POST'])]
    public function updateStatus(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
    
        if (!$this->isCsrfTokenValid('update-status', $request->request->get('_token'))) {
            return new JsonResponse(['status' => 'error', 'message' => 'Token CSRF invalide'], 400);
        }
        
        $newStatus = $request->request->get('status');
        if (!in_array($newStatus, ['À faire', 'En cours', 'Terminé'])) {
            return new JsonResponse(['status' => 'error', 'message' => 'Statut invalide'], 400);
        }
        
        $tache->setStatut($newStatus);
        $entityManager->flush();
        
        return new JsonResponse(['status' => 'success']);
    }



    #[Route('/tache/mestaches', name: 'app_mes_taches', methods: ['GET'])]
    public function mesTaches(TacheRepository $tacheRepository, Request $request): Response
    {
        $token = $request->cookies->get('BEARER');
        
        if (!$token) {
            return $this->redirectToRoute('app_login');
        }
    
        try {
            $decodedData = $this->jwtEncoder->decode($token);
            $email = $decodedData['username'] ?? null;
            $user = $this->userRepository->findOneBy(['email' => $email]);
    
            if (!$user) {
                return $this->redirectToRoute('app_login');
            }
            
            $taches = $tacheRepository->findBy(['assignee' => $user]);
            
            $groupedTaches = [
                'À faire' => array_filter($taches, fn($t) => $t->getStatut() === 'À faire'),
                'En cours' => array_filter($taches, fn($t) => $t->getStatut() === 'En cours'),
                'Terminé' => array_filter($taches, fn($t) => $t->getStatut() === 'Terminé'),
            ];
            
            $totalTaches = count($taches);
    
            return $this->render('tache/tacheemploye.html.twig', [
                'groupedTaches' => $groupedTaches,
                'totalTaches' => $totalTaches,
                'user' => $user
            ]);
    
        } catch (\Exception $e) {
            $this->addFlash('error', 'Token invalide ou expiré.');
            return $this->redirectToRoute('app_login');
        }
    }



    #[Route('/tache/export', name: 'app_tache_export', methods: ['GET'])]
    public function export(TacheRepository $tacheRepository): StreamedResponse
    {
        $taches = $tacheRepository->findAll();
    
        $response = new StreamedResponse(function () use ($taches) {
            $handle = fopen('php://output', 'w');
    
            // Entêtes du fichier
            fputcsv($handle, ['ID', 'Titre', 'Description', 'Date de creation', 'Deadline', 'Statut', 'Projet', 'Assigne a', 'Priorite']);
    
            foreach ($taches as $tache) {
                fputcsv($handle, [
                    $tache->getId(),
                    $tache->getTitre(),
                    $tache->getDescription(),
                    $tache->getDateCreation()?->format('Y-m-d'),
                    $tache->getDeadline()?->format('Y-m-d'),
                    $tache->getStatut(),
                    $tache->getProjet()?->getNom() ?? '',
                    $tache->getAssignee()?->getNom() ?? '',
                    $tache->getPriorite(),
                ]);
            }
    
            fclose($handle);
        });
    
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', (new ResponseHeaderBag())->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'taches_export.csv'
        ));
    
        return $response;
    }

}